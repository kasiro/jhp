<?php
# 1 - Абсолютная проходимость - Абсолютная передача данных

class module_loader {
	private $path_mods; 

	function __construct(string $path_mods){
		require __DIR__.'/jModule.php';
		$this->path_mods = $path_mods;
	}

	public function load_larege_modules(){
		$files = glob(__DIR__.'/larege_modules/*/index.php');
		foreach ($files as $file){
			yield $file;
		}
	}

	public function get_module_list(){
		foreach ($this->getModules() as $path){
			$fname = basename($path);
			$module_name = explode('.', $fname)[0];
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
class MyPHP {
	private $conf_path;
	
	public function isJhp($path) {
		if (explode('.', basename($path))[1] !== 'jhp') {
			throw new Exception('file is not jhp');
		}
	}

	function __construct(string $path){
		$this->isJhp($path); 
		$this->setGlobalPath($path);
		$this->module_loader = new module_loader(__DIR__.'/modules/*.php');
		$this->renderCode();
	}

	public function setGlobalPath($path){
		$GLOBALS['fileinfo']['full'] = $path;
		$GLOBALS['fileinfo']['dirname'] = dirname($path);
		$GLOBALS['fileinfo']['basename'] = basename($path);
		$newpath = preg_replace("#\.[\w\d]+$#i", ".php", basename($path));
		$GLOBALS['fileinfo']['savefull'] = dirname($path).'/'.$newpath;
	}

	/**
	 * update_modules_config Modules Settings
	 *
	 * @return void
	 */
	public function update_modules_config(){
		# code...
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
			// if (basename($file) == 'index.php') {
			// 	echo 'load module: ';
			// 	echo basename(dirname($file)) . PHP_EOL;
			// } else {
			// 	echo 'load module: ';
			// 	echo explode('.', basename($file))[0] . PHP_EOL;
			// }
			$module = require $file;
			if ($module->getSettings()['use'] === false) continue;
			$code = $this->transform($module, $code);
		}
		file_put_contents($GLOBALS['fileinfo']['savefull'], $code);
	}

	public function create_start_config(){
		$config = [
			'modules' => [],
			'aliases' => [
				'__con' => '__construct'
			]
		];
		foreach  ($this->module_loader->get_module_list() as $module_name){
			$config['modules'][] = $module_name;
		}
		$j = json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
		$j = str_replace('    ', "\t", $j);
		file_put_contents($this->conf_path, $j);
	}
}

$p = '/home/kasiro/Документы/php/pyTest/test.jhp';
$mphp = new MyPHP($p);