<?php

class module_loader {
	private $rules = [];
	private $settings = [];
	private $path_modules = '';

	public function __construct($path_modules, $module_list = false){
		$this->path_modules = $path_modules;
		if ($module_list) {
			$this->preload_modules($path_modules, $module_list);
		} else {
			$this->preload_modules($path_modules);
		}
	}

	public function get_module_list(){
		$list = [];
		$files = glob($this->path_modules . '/*.php');
		foreach ($files as $path){
			$fname = @end(explode('/', $path));
			$module_name = explode('.', $fname)[0];
			$current_module = require $path;
			if ($current_module['settings']['use'] === true) {
				$list[] = $module_name;
			}
		}
		return $list;
	}

	private function preload_modules($path_modules, $module_list = false){
		if ($module_list) {
			$files = glob($path_modules . '/*.php');
			foreach ($files as $path){
				$module_name = explode('.', @end(explode('/', $path)))[0];
				// echo $module_name . "\n";
				$current_module = require $path;
				if ($current_module['settings']['use'] === true && in_array($module_name, $module_list)){
					foreach ($current_module['settings'] as $key => $value){
						if ($key != 'use' && str_contains($key, 'use_')){
							$rules_name = substr($key, 4);
							if (is_array($current_module[$rules_name]) && $current_module['settings'][$key] === true){
								foreach ($current_module[$rules_name] as $rule_key => $rule_value){
									$this->rules[$rule_key] = $rule_value;
								}
							}
						}
					}
					foreach ($current_module['rules'] as $key => $value){
						$this->rules[$key] = $value;
					}
					$this->settings[$module_name] = $current_module['settings'];
				}
			}
		} else {
			$files = glob($path_modules . '/*.php');
			foreach ($files as $path){
				$module_name = explode('.', @end(explode('/', $path)))[0];
				$current_module = require $path;
				if ($current_module['settings']['use'] === true){
					foreach ($current_module['settings'] as $key => $value){
						if ($key != 'use' && str_contains($key, 'use_')){
							$rules_name = substr($key, 4);
							if (is_array($current_module[$rules_name]) && $current_module['settings'][$key] === true){
								foreach ($current_module[$rules_name] as $rule_key => $rule_value){
									$this->rules[$rule_key] = $rule_value;
								}
							}
						}
					}
					foreach ($current_module['rules'] as $key => $value){
						$use_ex = array_key_exists('use', $value);
						if ($use_ex && $value['use'] === true) {
							$this->rules[$key] = $value;
						} else {
							$this->rules[$key] = $value;
						}
					}
					$this->settings[$module_name] = $current_module['settings'];
				}
			}
		}
	}

	public function getAllSettings(){
		return $this->settings;
	}

	public function getAllRules(){
		return $this->rules;
	}

}

class MyPHP {
	public $conf_path;
	public function __construct($file) {
		// echo 'Config finded: ' . ($this->findConfig($file) ? 'true' : 'false') . "\n";
		getConfig:
		if ($this->findConfig($file)) {
			$json = file_get_contents($this->conf_path);
			if (strlen($json) > 0) {
				$config = json_decode($json, true);
				$this->module_loader = new module_loader(__DIR__.'/modules', $config['modules']);
			} else {
				$this->module_loader = new module_loader(__DIR__.'/modules');
				$module_list = $this->module_loader->get_module_list();
				$this->create_start_config($module_list);
				goto getConfig;
			}
			$spath = substr($file, 0, -strlen('.jhp')) . '.php';
			$dirname = dirname($file);
			$basename = basename($file);
			$reg_list = $this->module_loader->getAllRules();
			if (file_exists($file)){
				$code = file_get_contents($file);
				$code = str_replace('    ', "\t", $code);
				file_put_contents($file, $code);
			} else $code = '';
			if ($code) {
				$code = $this->RunRender($reg_list, $code);
				$has_error = $this->hasErrorText($code);
				if ($has_error) {
					echo 'Состояние кода: ' . $has_error . "\n";
				}
				if (isset($config['aliases'])) {
					foreach ($config['aliases'] as $key => $value){
						$code = str_replace($key, $value, $code);
					}
				}
				file_put_contents($spath, $code);
			}
		} else {
			$this->module_loader = new module_loader(__DIR__.'/modules');
			$spath = substr($file, 0, -strlen('.jhp')) . '.php';
			$dirname = dirname($file);
			$basename = basename($file);
			$reg_list = $this->module_loader->getAllRules();
			if (file_exists($file))
				$code = file_get_contents($file);
			else
				$code = '';
			if ($code) {
				$code = $this->RunRender($reg_list, $code);
				$has_error = $this->hasErrorText($code);
				if ($has_error) {
					echo 'Состояние кода: ' . $has_error . "\n";
				}
				file_put_contents($spath, $code);
			}
		}
	}

	public function create_start_config($module_list){
		$config = [
			'modules' => [],
			'aliases' => [
				'__con' => '__construct'
			]
		];
		$this->conf_path;
		foreach ($module_list as $module_name){
			$config['modules'][] = $module_name;
		}
		$j = json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
		$j = str_replace('    ', "\t", $j);
		file_put_contents($this->conf_path, $j);
	}

	public function findConfig($file_path){
		$arr = explode('/', dirname($file_path));
		$cur_dir_up = '';
		for ($i = 0; $i < count($arr); $i++){
			$cur_dir_up .= $i == 0 ? $arr[$i] : '/' . $arr[$i];
			$files = glob($cur_dir_up . '/*.config');
			if (count($files) > 0) {
				foreach ($files as $file){
					if (basename($file) == 'jhp.config') {
						$this->conf_path = $file;
						return true;
					}
				}
			}
		}
		return false;
	}

	private function hasErrorText($code){
		if ($this->hasError($code))
			return 'Есть ошибки';
	}

	private function hasError($code){
		if (file_get_contents('https://yandex.ru/')) {
			$myCurl = curl_init();
			if (!str_contains($code, '<?php')) {
				$code = "<?php\n\n" . $code;
			}
			curl_setopt_array($myCurl, [
				CURLOPT_URL => 'https://api.extendsclass.com/php-checker/8.0.0',
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_POST => true,
				CURLOPT_NOBODY => false,
				CURLOPT_POSTFIELDS => $code
			]);
			$response = curl_exec($myCurl);
			curl_close($myCurl);
			$code = json_decode($response, JSON_OBJECT_AS_ARRAY)['code'];
			$has_error = $code === 0 ? false : true;
			return $has_error;
		}
		return false;
	}

	private function RunRender($array, $code){
		// Перебираем основной Массив Регулярок $array
		// $option (Массив)
		foreach ($array as $pattern => $option){
			// До
			if (isset($option['do']) && count($option['do']) > 0) {
				// #2 Перебираем Строки зависимостей ('ref', 'main' ... и т.д) $dep (string)
				foreach ($option['do'] as $dep) {
					// Выбираем все регулярные выражения которые имеют $option['group'] == $dep
					$list_reg_dep = array_filter($array, function($reg) use ($dep) {
						// Ищем по $reg['group']
						if (isset($reg['group']) && strlen($reg['group']) > 0){
							if ($reg['group'] == $dep) {
								return $reg;
							}
						}
						// Ищем по $reg['name']
						if (isset($reg['name']) && strlen($reg['name']) > 0){
							if ($reg['name'] == $dep) {
								return $reg;
							}
						}
					});
					// Если есть
					if (count($list_reg_dep) > 0) {
						// print_r($list_reg_dep);
						// Перебираем Список Регулярок которые являются зависимостью данной регулярки -> #2
						// Что бы выполнить их после Данной Регулярки (Взависимости от типа)
						$code = $this->RunRender($list_reg_dep, $code);
					}
				}
			}
			// текущая
			if ($option['type'] == 'string') {
				if (isset($option['count'])) {
					for ($i = 1; $i <= $option['count']; $i++) { 
						$code = preg_replace($pattern, $option['reg'], $code);
					}
				} else {
					$code = preg_replace($pattern, $option['reg'], $code);
				}
			}
			if ($option['type'] == 'call') {
				if (isset($option['count'])) {
					for ($i = 1; $i <= $option['count']; $i++) { 
						$code = preg_replace_callback($pattern, $option['reg'], $code);
					}
				} else {
					$code = preg_replace_callback($pattern, $option['reg'], $code);
				}
			}
			// После
			if (isset($option['then']) && count($option['then']) > 0) {
				// #2 Перебираем Строки зависимостей ('ref', 'main' ... и т.д) $dep (string)
				foreach ($option['then'] as $dep) {
					// Выбираем все регулярные выражения которые имеют $option['group'] == $dep
					$list_reg_dep = array_filter($array, function($reg) use ($dep) {
						// Ищем по $reg['group']
						if (isset($reg['group']) && strlen($reg['group']) > 0){
							if ($reg['group'] == $dep) {
								return $reg;
							}
						}
						// Ищем по $reg['name']
						if (isset($reg['name']) && strlen($reg['name']) > 0){
							if ($reg['name'] == $dep) {
								return $reg;
							}
						}
					});
					// Если есть
					if (count($list_reg_dep) > 0) {
						// print_r($list_reg_dep);
						// Перебираем Список Регулярок которые являются зависимостью данной регулярки -> #2
						// Что бы выполнить их после Данной Регулярки (Взависимости от типа)
						$code = $this->RunRender($list_reg_dep, $code);
					}
				}
			}
		}
		return str_replace('    ', "\t", $code);
	}

}
$path_to_file = $argv[1];
$myPHP = new MyPHP($path_to_file);