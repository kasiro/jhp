<?php

// if (!class_exists('jModule')) require $GLOBALS['jModule'];

# Сделать так что бы... можно было настроить каждый модуль из конфига папки проекта

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
				echo 'ERROR: $level > count(explode($sep, $path))' . "\n";
			}
		} else {
			return $path;
		}
	}
}

$throw_text = function ($path){
	return "throw new Exception('[jhp: 404] $path user_module is not found...');";
};

$path = leveling(__DIR__, 2);
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
	$mp = FILE_REQ;
	$paths = $matches[2];
	$s = '';
	$modules = [];
	$s = '';
	$dirname = $GLOBALS['fileinfo']['dirname'];
	if (!file_exists($dirname.'/jhp_modules')) {
		mkdir($dirname.'/jhp_modules');
	}
	// echo $dirname.'/jhp_modules' . PHP_EOL;
	$modules_path = $dirname.'/jhp_modules';
	$modules_path_local = './jhp_modules';
	if (is_string($paths) && strlen($paths)) {
		if (file_exists("$mp/$paths.php")) {
			$modules[] = "$mp/$paths.php";
			$s .= "{$tabs}require '$modules_path/$paths.php';\n";
		} else {
			if (file_exists("$mp/$paths") && is_dir("$mp/$paths")) {
				$files = myrglob("$mp/$paths", '*.php');
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
	if (str_contains($tabs, '// ')) {
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
$module->addreg('/(.*)import_array: include \[((?:(?(R)\w++|[^]]*+)|(?R))*)\];/m', function ($matches) use ($throw_text, &$module){
	$i = 0;
	restart:
	$tabs = $matches[1];
	if (str_contains($tabs, '// ')) {
		return $matches[0];
	}
	$module_name = $matches[2];
	$mp = FILE_REQ;
	$paths = import_array($module_name);
	// print_r($paths);
	$modules = [];
	$s = '';
	$dirname = $GLOBALS['fileinfo']['dirname'];
	if (!file_exists($dirname.'/jhp_modules')) {
		mkdir($dirname.'/jhp_modules');
	}
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