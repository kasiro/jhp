<?php
# 1 - Абсолютная проходимость - Абсолютная передача данных
class module_loader {
	function __construct(string $path_mods){
		$this->path_mods = $path_mods;
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
		if (strlen($path_mods)) {
			$files = glob($path_mods);
		} else {
			$files = glob($this->path_mods);
		}
		foreach ($files as $file){
			yield $file;
		}
	}
}
class MyPHP {
	function __construct(string $path){
		if (explode('.', basename($path))[1] !== 'jhp') {
			throw new Exception('file is not jhp');
		}
		$GLOBALS['fileinfo']['full'] = $path;
		$GLOBALS['fileinfo']['dirname'] = dirname($path);
		$GLOBALS['fileinfo']['basename'] = basename($path);
		$arr = explode('.', basename($path));
		$arr[1] = 'php';
		$GLOBALS['fileinfo']['savefull'] = dirname($path).'/'.implode('.', $arr);
		unset($arr);
		$this->module_loader = new module_loader(__DIR__.'/modules/*.php');
		foreach ($this->module_loader->getModules() as $file){
			$module = require $file;
			foreach ($module->regList as $reg => $act){
				switch (gettype($act)) {
					case 'string':
						// $code = preg_replace($reg, $act, $code);
						break;

					case 'object': // function
						// $code = preg_replace_callback($reg, $act, $code);
						break;
					
					default:
						throw new Exception('$act is not NEED TYPE (mphp) type is ' . gettype($act));
						break;
				}
			}
		}
	}

	public function RenderCode(){
		$code = file_get_contents($GLOBALS['fileinfo']['full']);
	}

	public function create_start_config(){
		$config = [
			'modules' => [],
			'aliases' => [
				'__con' => '__construct'
			]
		];
		$this->conf_path;
		foreach ($this->module_loader->get_module_list() as $module_name){
			$config['modules'][] = $module_name;
		}
		$j = json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
		$j = str_replace('    ', "\t", $j);
		file_put_contents($this->conf_path, $j);
	}
}
$p = '/home/kasiro/Документы/php/pyTest/test.jhp';
$mphp = new MyPHP($p);