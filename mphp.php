<?php declare(strict_types=1);

# 1 - Абсолютная проходимость - Абсолютная передача данных)

require __DIR__.'/Storage.php';
if (!class_exists('fs')) require __DIR__.'/user_modules/fs.php';
if (!class_exists('Logger')) {
	require __DIR__.'/com/Logger.php';
	if (!class_exists('Logger')) echo 'class Logger not required!'.PHP_EOL;
}
Storage::set('Logger', function (){
	return new Logger(__DIR__.'/log.txt');
});

class module_loader {
	public $path_mods;
	public $load_modules = [];

	function __construct(string $path_mods){
		$this->Logger = Storage::get('Logger');
		if (!class_exists('jModule')) {
			require __DIR__.'/jModule.php';
		}
		$this->path_mods = $path_mods;
	}

	public function load_larege_modules(){
		$files = array_merge(
			glob(__DIR__.'/larege_modules/*/index.php')
		);
		foreach ($files as $file){
			yield $file;
		}
	}

	/**
	 * update_modules_config Modules Settings
	 *
	 * @return void
	 */
	public function update_modules_config($modules){
		if ($this->load_modules != $modules)
			$this->load_modules = array_merge($this->load_modules, $modules);
		else
			$this->load_modules = $modules;
	}

	public function get_module_list(){
		foreach ($this->getModules() as $path){
			$module_name = explode('.', basename($path))[0];
			$current_module = require $path;
			if ($current_module->getSettings()['use'] === true) {
				yield $module_name;
			}
		}
	}

	public function getModules(string $path_mods = ''){
		if (strlen($path_mods)) $files = glob($path_mods);
		else $files = glob($this->path_mods);
		foreach ($files as $file){
			yield $file;
		}
	}
}

Storage::set('module_loader', function (){
	return new module_loader(__DIR__.'/modules/*.php');
});

class Config {
	public static $config_name = '';

	public static function find($file_path){
		$arr = explode('/', dirname($file_path));
		$DirPath = dirname($file_path);
		for ($i = 0; $i < count($arr); $i++){
			$cur_dir_up = $DirPath;
			$files = glob($cur_dir_up . '/*.config');
			if (!empty($files)){
				foreach ($files as $file){
					if (basename($file) == 'jhp.config'){
						return $file;
					}
				}
			}
			$DirPath = dirname($DirPath);
		}
		return false;
	}

	public static function create($conf_path, $mode = 'all'){
		$Logger = Storage::get('Logger');
		$module_loader = Storage::get('module_loader');
		$config = [
			'modules' => [],
			'aliases' => []
		];
		foreach ($module_loader->getModules() as $path){
			$current_module = require $path;
			// $module_name = explode('.', basename($path))[0];
			$module_name = $current_module->getName();
			$module_settings = $current_module->getSettings();
			if ($mode == 'all') {
				$config['modules'][] = [
					$module_name => $module_settings
				];
			} elseif ($mode == 'use'){
				if ($module_settings['use']) {
					$config['modules'][] = [
						$module_name => $module_settings
					];
				}
			}
		}
		foreach ($module_loader->load_larege_modules() as $path){
			// $module_name = basename(dirname($path));
			$current_module = require $path;
			$module_name = $current_module->getName();
			$module_settings = $current_module->getSettings();
			switch ($mode) {
				case 'all':
					$config['modules'][] = [
						$module_name => $module_settings
					];
					break;
				
				case 'use':
					if ($module_settings['use']) {
						$config['modules'][] = [
							$module_name => $module_settings
						];
					}
					break;
			}
		}
		$Logger->add("create_start_config mode is '$mode'");
		$j = json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
		$j = str_replace('    ', "\t", $j);
		file_put_contents($conf_path, $j);
	}
}

class JHP {
	public $conf_path;
	protected static $_instance;

	public function isJhp($path) {
		if (explode('.', basename($path))[1] !== 'jhp') {
			// Нереально из за \\.jhp (VS Code) (Разве что если вручную)
			Storage::get('Logger')->add('file is not jhp');
			system('notify-send "JHP" "file is not jhp"');
			throw new Exception('file is not jhp');
		}
	}
	
	# FIXME: 2 раза вызывается модуль, А если глобально выключен - Один раз, разобраться...
	function __construct(string $path){
		fs::create_if_not_exist(fs::TYPE_DIR, __DIR__.'/user_modules');
		$this->Logger = Storage::get('Logger');
		$this->isJhp($path);
		$this->setGlobalPath($path);
		$this->module_loader = Storage::get('module_loader');
		if ($conf_path = Config::find($path)) {
			// echo $conf_path . PHP_EOL;
			$GLOBALS['conf_path'] = $this->conf_path = $conf_path;
			$json = file_get_contents($this->conf_path);
			if (strlen($json) == 0) {
				echo 'Заполняем конфиг' . PHP_EOL;
				if ($conf_path != './jhp.config') $this->Logger->add("Заполняем конфиг '$conf_path'");
				Config::create($this->conf_path, 'use');
				$json = file_get_contents($this->conf_path);
				$this->elseHandler($json, $this->conf_path);
			} else {
				$this->elseHandler($json, $this->conf_path);
			}
		} else {
			$this->Logger->add('Конфиг не найден');
			echo 'Конфиг не найден' . PHP_EOL;
		}
		$this->renderCode();
	}

	public function elseHandler($json, $conf_path){
		if ($conf_path != './jhp.config') $this->Logger->add("Обрабатываем данные конфига '$conf_path'");
		echo 'Обрабатываем данные конфига' . PHP_EOL;
		$config = json_decode($json, true);
		$this->module_loader->update_modules_config($config['modules']);
	}

	public static function getInstance(...$args) {
		if (self::$_instance === null) {
			self::$_instance = new self(...$args);
		}
		return self::$_instance;
	}
 
	private function __clone() {}
	public function __wakeup() {}

	public function setGlobalPath($path){
		$GLOBALS['fileinfo']['full'] = $path;
		$GLOBALS['fileinfo']['dirname'] = dirname($path);
		$GLOBALS['fileinfo']['basename'] = basename($path);
		$newpath = preg_replace('#\.[\w\d]+$#i', '.php', basename($path));
		$GLOBALS['fileinfo']['savefull'] = dirname($path).'/'.$newpath;
		foreach ($GLOBALS['fileinfo'] as $fname => $p){
			$this->Logger->add("file $fname path is '$p'");
		}
	}

	public function transform($module, $code){
		foreach ($module->regList as $reg => $act){
			switch (gettype($act)) {
				case 'string':
					$code = preg_replace($reg, $act, $code);
					break;

				case 'object': // function
					$code = preg_replace_callback($reg, $act, $code);
					break;
				
				default:
					$this->Logger->add('$act is not NEED TYPE (mphp) type is ' . gettype($act));
					throw new Exception('$act is not NEED TYPE (mphp) type is ' . gettype($act));
					break;
			}
		}
		return $code;
	}

	public function module_list(){
		foreach ($this->module_loader->getModules() as $file){
			$current_module = require $file;
			if ($current_module->getSettings()['use'] === true) {
				yield $file;
			}
		}
		foreach ($this->module_loader->load_larege_modules() as $file){
			yield $file;
		}
	}

	public function getConfigSettings($module){
		foreach ($this->module_loader->load_modules as $moule){
			if (array_key_exists($module->getName(), $moule)) {
				return $moule[$module->getName()];
			}
		}
		return ['use' => false];
	}

	public function renderCode(){
		$code = file_get_contents($GLOBALS['fileinfo']['full']);
		$load_modules = &$this->module_loader->load_modules;
		foreach ($this->module_list() as $file){
			// echo $file . PHP_EOL;
			$module = require $file;
			$name = $module->getName();
			// if ($module_type == 'large')
			if (count($load_modules) > 0) {
				$sets = $this->getConfigSettings($module);
				if (!empty($sets)) {
					// if ($module_type == 'large') {
					// 	$this->Logger->add("load settings for large module: '$name'");
					// } else {
					// 	$this->Logger->add("load settings for module: '$name'");
					// }
					$module->setSettings($sets);
				} else {
					$name = $module->getName();
					if ($module_type == 'large') {
						$this->Logger->add("settings of large module '{$name}' not be empty!");
						throw new Exception("settings of large module '$name' not be empty!");
					} else {
						$this->Logger->add("settings of module '{$name}' not be empty!");
						throw new Exception("settings of module '$name' not be empty!");
					}	
				}
			}
			if ($module->getSettings()['use'] === true){
				$module_type = basename($file) == 'index.php' ? 'large' : 'module';
				if ($module_type == 'large') $this->Logger->add("load large module: '$name'");
				else $this->Logger->add("load module: '$name'");
				// print_r([
				// 	$module->getName() => $module->getSettings()
				// ]);
				$code = $this->transform($module, $code);
			}
		}
		if ($this->conf_path){
			$json = file_get_contents($this->conf_path);
			$config = json_decode($json, true);
			foreach ($config['aliases'] as $what => $to){
				$code = str_replace($what, $to, $code);
			}
		}
		file_put_contents($GLOBALS['fileinfo']['savefull'], $code);
	}
}

$p = @$argv[1];
$mphp = new JHP($p);
Storage::get('Logger')->ot();