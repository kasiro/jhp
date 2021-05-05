<?php
# 1 - Абсолютная проходимость - Абсолютная передача данных
require(__DIR__.'/com/logger.php');

class module_loader {
	public $path_mods;
	public $load_modules = [];

	function __construct(string $path_mods){
		$this->Logger = new Logger(__DIR__.'/log.txt');
		require __DIR__.'/jModule.php';
		$this->path_mods = $path_mods;
	}

	public function load_larege_modules(){
		$files = glob(__DIR__.'/larege_modules/*/index.php');
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
class Config {

	public static function find($file_path){
		$arr = explode('/', dirname($file_path));
		$cur_dir_up = '';
		for ($i = 0; $i < count($arr); $i++){
			$cur_dir_up .= $i == 0 ? $arr[$i] : '/' . $arr[$i];
			$files = glob($cur_dir_up . '/*.config');
			if (count($files) > 0) {
				foreach ($files as $file){
					if (basename($file) == 'jhp.config') {
						return $file;
					}
				}
			}
		}
		return false;
	}

	public function create_start_config($mode = 'all'){
		$this->Logger = new Logger(__DIR__.'/log.txt');
		$config = [
			'modules' => [],
			'aliases' => [
				'__con' => '__construct'
			]
		];
		foreach ($this->module_loader->getModules() as $path){
			$module_name = explode('.', basename($path))[0];
			$current_module = require $path;
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
		$this->Logger->add("create_start_config mode is '$mode'");
		$j = json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
		$j = str_replace('    ', "\t", $j);
		file_put_contents($this->conf_path, $j); 
	}
}
class MyPHP {
	public $conf_path;
	
	public function isJhp($path) {
		if (explode('.', basename($path))[1] !== 'jhp') {
			throw new Exception('file is not jhp');
		}
	}

	function __construct(string $path){
		if (!file_exists(__DIR__.'/user_modules')) mkdir(__DIR__.'/user_modules');
		$this->Logger = new Logger(__DIR__.'/log.txt');
		$this->isJhp($path);
		$this->setGlobalPath($path);
		$this->module_loader = new module_loader(__DIR__.'/modules/*.php');
		if ($conf_path = Config::find($path)) {
			// echo $conf_path . PHP_EOL;
			$this->conf_path = $conf_path;
			$json = file_get_contents($this->conf_path);
			if (strlen($json) == 0) {
				echo 'Заполняем конфиг' . PHP_EOL;
				if ($conf_path != './jhp.config') $this->Logger->add("Заполняем конфиг '$conf_path'");
				Config::create_start_config();
			} else {
				if ($conf_path != './jhp.config') $this->Logger->add("Обрабатываем данные конфига '$conf_path'");
				echo 'Обрабатываем данные конфига' . PHP_EOL;
				$config = json_decode($json, true);
				$this->module_loader->update_modules_config($config['modules']);
			}
		} else {
			$this->Logger->add('Конфиг не найден');
			echo 'Конфиг не найден' . PHP_EOL;
		}
		$this->renderCode();
	}

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
			yield $file;
		}
		foreach ($this->module_loader->load_larege_modules() as $file){
			yield $file;
		}
	}

	public function renderCode(){
		$code = file_get_contents($GLOBALS['fileinfo']['full']);
		foreach ($this->module_list() as $file){
			// echo $file . PHP_EOL;
			$module = require $file;
			if (basename($file) == 'index.php') {
				// echo 'load large module: ';
				$n = basename(dirname($file));
				$this->Logger->add("load large module: '$n'");
				// echo $n . PHP_EOL;
			} else {
				// echo 'load module: ';
				$n = explode('.', basename($file))[0];
				$this->Logger->add("load module: '$n'");
				// echo explode('.', basename($file))[0] . PHP_EOL;
				$cmn = explode('.', basename($file))[0];
				if (count($this->module_loader->load_modules)){
					foreach ($this->module_loader->load_modules as $moule){
						foreach ($moule as $name => $sets) {
							if ($cmn = $name) {
								if (!empty($sets)) {
									$module->setSettings($sets);
								} else {
									$this->Logger->add("settings of module '{$name}' not be empty!");
									throw new Exception("settings of module '$name' not be empty!");
								}
							}
						}
					}
				}
			}
			if ($module->getSettings()['use'] === true){
				$code = $this->transform($module, $code);
			}
		}
		file_put_contents($GLOBALS['fileinfo']['savefull'], $code);
	}
}

$p = @$argv[1];
$mphp = new MyPHP($p);
$loger = new Logger(__DIR__ . '/log.txt');
$loger->ot();