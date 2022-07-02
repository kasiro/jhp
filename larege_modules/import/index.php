<?php

// if (!class_exists('jModule')) require $GLOBALS['jModule'];
require dirname(__DIR__).'/Helper.php';

if (!function_exists('leveling')){
	function leveling($path, $level = 0){
		if ($path == './' || $path == '/') {
			return __DIR__;
		} else {
			if ($level > 0) {
				$npath = '';
				if (str_contains($path, '/')) {
					$sep = '/';
				}
				if (str_contains($path, '\\')) {
					$sep = '\\';
				}
				if ($level < count(explode($sep, $path))) {
					$arr = explode($sep, $path);
					for ($i = 0; $i < count($arr) - ($level); $i++) { 
						if ($i != count($arr) - ($level) - 1) {
							$npath .= $arr[$i] . $sep;
						} else {
							$npath .= $arr[$i];
						}
					}
					if ((count(explode($sep, $path)) - 1) == $level) {
						$npath .= $sep;
					}
					if (strlen($npath) > 0) {
						return $npath;
					}
				} else {
					echo 'ERROR: $level > count(explode($sep, $path))' . PHP_EOL;
				}
			} else {
				return $path;
			}
		}
	}
	$path = leveling(__DIR__, 2);
} else {
	$path = dirname(__DIR__, 2);
}

if (!function_exists('create_jhp_modules')){
	function create_jhp_modules($dirname){
		$AllModules = [];
		$ImportModules = JhpHelper::getImportModules($GLOBALS['fileinfo']['full']);
		$AllModules = array_merge($AllModules, $ImportModules);
		$dirDfiles = myrglob(dirname($dirname), '*.jhp');
		$dirDfiles = array_filter($dirDfiles, function($el){
			if ($el != $GLOBALS['fileinfo']['full']){
				return $el;
			}
		});
		if (!empty($dirDfiles)){
			foreach ($dirDfiles as $dfile){
				// echo $dfile . PHP_EOL;
				$ImportModulesFile = JhpHelper::getImportModules($dfile);
				$AllModules = array_merge($AllModules, $ImportModulesFile);
			}
		}
		$countValues = array_keys(array_filter(array_count_values($AllModules), function($v){
			return $v > 1;
		}));
		if (!empty($countValues) && count($countValues) > 0){
			$dirDir = true;
			$dirname = dirname($dirname);
			if (!file_exists($dirname.'/jhp_modules')) {
				mkdir($dirname.'/jhp_modules');
			}
		} else {
			if (!file_exists($dirname.'/jhp_modules')) {
				mkdir($dirname.'/jhp_modules');
			}
		}
		return $dirname;
	}	
}

$throw_text = function ($path){
	return "throw new Exception('[jhp: 404] $path user_module is not found...');";
};

if (!defined('FILE_REQ')) define('FILE_REQ', $path.'/user_modules');
require __DIR__.'/func.php';

$module = new jModule;
$module->setSettings([
	'use' => true,
	'fullpath' => true,
	'clean' => false
]);
$n = basename(dirname(__FILE__));
$module->setName($n);
$module->addreg('/(.*)import \'(.*)\';/m', function ($matches) use ($throw_text){
	$tabs = $matches[1];
	if (str_contains($tabs, '// ') || preg_match('/\w+/m', $tabs)){
		return $matches[0];
	}
	$mp = FILE_REQ;
	$paths = $matches[2];
	$s = '';
	if (is_string($paths) && strlen($paths)) {
		if (file_exists("$mp/$paths.php")) {
			$s .= "{$tabs}require '$mp/$paths.php';\n";
		} else {
			if (file_exists("$mp/$paths") && is_dir("$mp/$paths")) {
				$files = myrglob("$mp/$paths", '*.php');
				foreach ($files as $file){
					$s .= "{$tabs}require '$file';\n";
				}
			} else {
				$s .= "{$tabs}".$throw_text($paths)."\n";
			}
		}
	}
	$s = str_replace("\n", '', $s);
	$s = str_replace(';require', ";\nrequire", $s);
	return $s;
});
$module->addreg('/(.*)import: include \'(.*)\';/m', function ($matches) use ($module, $throw_text){
	$i = 0;
	restart2:
	$tabs = $matches[1];
	if (str_contains($tabs, '// ') || preg_match('/\w+/m', $tabs)){
		return $matches[0];
	}
	# Путь до user_modules
	$mp = FILE_REQ;
	# Название файла
	$paths = $matches[2];
	$modules = [];
	$s = '';
	# Директория файла
	$dirname = $GLOBALS['fileinfo']['dirname'];
	$dirname = create_jhp_modules($dirname);
	// echo $dirname.'/jhp_modules' . PHP_EOL;
	$modules_path = $dirname.'/jhp_modules';
	$modules_path_local = './jhp_modules';
	if (is_string($paths) && strlen($paths)) {
		if (file_exists("$mp/$paths.php")) {
			$modules[] = "$mp/$paths.php";
			$s .= "{$tabs}require '$modules_path/$paths.php';".PHP_EOL;
		} else {
			if (file_exists("$mp/$paths") && is_dir("$mp/$paths")) {
				$files = myrglob("$mp/$paths", '*.php');
				foreach ($files as $file){
					$b = basename($file);
					if (file_exists("$modules_path/$b")) {
						$s .= "{$tabs}require '$modules_path/$b';".PHP_EOL;
					} else {
						$s .= "{$tabs}require '$file';".PHP_EOL;
					}
					$modules[] = $file;
				}
			} else {
				$s .= "{$tabs}".$throw_text($path).PHP_EOL;
			}
		}
	}
	$mbs = [];
	foreach ($modules as $mod){
		if (!in_array(basename($mod), $mbs))
			$mbs[] = basename($mod);
	}
	if ($module->getSettings()['clean']) {
		$m = array_diff(
			scandir($modules_path),
			['.', '..']
		);
		foreach ($m as $new_module){
			if (!in_array($new_module, $mbs)) {
				unlink($modules_path.'/'.$new_module);
			}
		}
	}
	foreach ($modules as $mod){
		if (!file_exists($modules_path.'/'.basename($mod))) {
			copy($mod, $modules_path.'/'.basename($mod));
		} else {
			$before = file_get_contents($mod);
			$after = file_get_contents($modules_path.'/'.basename($mod));
			if ($before !== $after){
				copy($mod, $modules_path.'/'.basename($mod));
			}
		}
	}
	if ($i <= 0) {
		$i++;
		goto restart2;
	}
	$s = str_replace("\n", '', $s);
	$s = str_replace(';require', ";\nrequire", $s);
	$s = str_replace(';throw', ";\nthrow", $s);
	if (!$module->getSettings()['fullpath']) {
		$s = str_replace($dirname, '.', $s);
	}
	return $s;
});
$module->addreg('/(.*)import_array \[((?:(?(R)\w++|[^]]*+)|(?R))*)\];/m', function ($matches) use ($throw_text){
	$tabs = $matches[1];
	if (str_contains($tabs, '// ') || preg_match('/\w+/m', $tabs)){
		return $matches[0];
	}
	$module_name = $matches[2];
	$mp = FILE_REQ;
	$paths = import_array($module_name);
	// print_r($paths);
	$s = '';
	foreach ($paths as $path){
		if (file_exists("$mp/$path.php")) {
			$s .= "{$tabs}require '$mp/$path.php';\n";
		} else {
			if (file_exists("$mp/$path") && is_dir("$mp/$path")) {
				$files = myrglob("$mp/$path", '*.php');
				foreach ($files as $file){
					$s .= "{$tabs}require '$file';\n";
				}
			} else {
				$s .= "{$tabs}".$throw_text($path)."\n";
			}
		}
	}
	$s = str_replace("\n", '', $s);
	$s = str_replace(';require', ";\nrequire", $s);
	$s = str_replace(';throw', ";\nthrow", $s);
	return $s;
});
$module->addreg('/(\t*|\s*)import_array: include \[((?:(?(R)\w++|[^]]*+)|(?R))*)\];/m', function ($matches) use ($throw_text, &$module){
	$i = 0;
	restart:
	$tabs = $matches[1];
	if (str_contains($tabs, '// ') || preg_match('/\w+/m', $tabs)){
		return $matches[0];
	}
	$module_name = $matches[2];
	$mp = FILE_REQ;
	$paths = import_array($module_name);
	// print_r($paths);
	$modules = [];
	$s = '';
	$dirname = $GLOBALS['fileinfo']['dirname'];
	$dirname = create_jhp_modules($dirname);
	// echo $dirname.'/jhp_modules' . PHP_EOL;
	$modules_path = $dirname.'/jhp_modules';
	$modules_path_local = './jhp_modules';
	foreach ($paths as $path){
		if (file_exists("$mp/$path.php")) {
			$modules[] = "$mp/$path.php";
			$s .= "{$tabs}require '$modules_path/$path.php';\n";
		} else {
			// echo "$mp/$path.php" . ' not exist' . PHP_EOL;
			if (file_exists("$mp/$path") && is_dir("$mp/$path")) {
				$files = myrglob("$mp/$path", '*.php');
				foreach ($files as $file){
					$b = basename($file);
					if (file_exists("$modules_path/$b")) {
						$s .= "{$tabs}require '$modules_path/$b';\n";
					} else {
						$s .= "{$tabs}require '$file';\n";
					}
					$modules[] = $file;
				}
			} else {
				// echo "$modules_path/$b" . ' not exist' . PHP_EOL;
				$s .= "{$tabs}".$throw_text($path)."\n";
			}
		}
	}
	$mbs = [];
	foreach ($modules as $mod){
		if (!in_array(basename($mod), $mbs))
			$mbs[] = basename($mod);
	}
	if ($module->getSettings()['clean']) {
		$m = array_diff(
			scandir($modules_path),
			['.', '..']
		);
		foreach ($m as $new_module){
			if (!in_array($new_module, $mbs)) {
				unlink($modules_path.'/'.$new_module);
			}
		}
	}
	foreach ($modules as $mod){
		if (!file_exists($modules_path.'/'.basename($mod))) {
			copy($mod, $modules_path.'/'.basename($mod));
		}
	}
	if ($i <= 0) {
		$i++;
		goto restart;
	}
	$s = str_replace("\n", '', $s);
	$s = str_replace(';require', ";\nrequire", $s);
	$s = str_replace(';throw', ";\nthrow", $s);
	if (!$module->getSettings()['fullpath']) {
		$s = str_replace($dirname, '.', $s);
	}
	return $s;
});
// $module->addreg('#newregexp#m', function ($matches) use (&$module) {
// 	// $settings = $module->getSettings();
// });
return $module;