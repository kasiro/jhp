<?php

define('FILE_REQ', '/home/kasiro/Документы/projects/mphp/file_req');
function myrglob($base, $pattern, $flags = 0) {
	if (substr($base, -1) !== DIRECTORY_SEPARATOR) {
		$base .= DIRECTORY_SEPARATOR;
	}

	$files = glob($base.$pattern, $flags);
	
	foreach (glob($base.'*', GLOB_ONLYDIR|GLOB_NOSORT|GLOB_MARK) as $dir) {
		$dirFiles = myrglob($dir, $pattern, $flags);
		if ($dirFiles !== false) {
			$files = array_merge($files, $dirFiles);
		}
	}

	return $files;
};
if (!function_exists('import')) {
	/**
	 * my req function
	 *
	 * @param string $path
	 * @return string|array
	 */
	function import(string $path) {
		$mp = FILE_REQ;
		if (file_exists("$mp/{$path}.php")) {
			return "$mp/{$path}.php";
		} else {
			if (file_exists("$mp/$path") && is_dir("$mp/$path")) {
				$files = array_filter(
					scandir("$mp/$path"),
					fn($e) => !in_array($e, ['.', '..'])
				);
				return $files;
			} else {
				return "module $path not found";
			}
		}
	}
}
if (!function_exists('import_array')) {
	/**
	 * my array req function
	 *
	 * @param string $path
	 * @return array
	 */
	function import_array(string $path){
		eval('$files'." = [$path];");
		return $files;
	}
}
$throw_text = function ($path){
	return "throw new Exception('[jhp: 404] $path');";
};
$settings = [
	'use' => true
];
return [
	'settings' => $settings,
	'rules' => [
		'/(.*)import \'(.*)\';/m' => [
			'type' => 'call',
			'reg' => function ($matches) use ($throw_text) {
				$tabs = $matches[1];
				$mp = FILE_REQ;
				$paths = $matches[2];
				$s = '';
				if (is_string($paths)) {
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
			}
		],
		'/(.*)import: include \'(.*)\';/m' => [
			'type' => 'call',
			'reg' => function ($matches) use ($throw_text) {
				$tabs = $matches[1];
				$mp = FILE_REQ;
				$paths = $matches[2];
				$s = '';
				$modules_path = './jhp_modules';
				if (is_string($paths)) {
					if (file_exists("$modules_path/$paths.php")) {
						$s .= "{$tabs}require '$modules_path/$paths.php';\n";
					} else {
						$s .= "{$tabs}".$throw_text($paths)."\n";
					}
				}
				$s = str_replace("\n", '', $s);
				$s = str_replace(';require', ";\nrequire", $s);
				return $s;
			}
		],
		'/(.*)import_array \[((?:(?(R)\w++|[^]]*+)|(?R))*)\];/m' => [
			'type' => 'call',
			'reg' => function ($matches) use ($throw_text) {
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
			}
		],
		'/(.*)import_array: include \[((?:(?(R)\w++|[^]]*+)|(?R))*)\];/m' => [
			'type' => 'call',
			'reg' => function ($matches) use ($throw_text) {
				$i = 0;
				restart:
				$tabs = $matches[1];
				if (str_contains($tabs, '// ')) {
					return $matches[0];
				}
				$module_name = $matches[2];
				$mp = FILE_REQ;
				$paths = import_array($module_name);
				print_r($paths);
				
				$modules = [];
				$s = '';
				$dirname = $GLOBALS['fileinfo']['dirname'];
				if (!file_exists($dirname.'/jhp_modules')) {
					mkdir($dirname.'/jhp_modules');
				}
				$modules_path = $dirname.'/jhp_modules';
				$modules_path_local = './jhp_modules';
				foreach ($paths as $path){
					if (file_exists("$mp/$path.php")) {
						$modules[] = "$mp/$path.php";
						$s .= "{$tabs}require '$modules_path/$path.php';\n";
					} else {
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
							$s .= "{$tabs}".$throw_text($path)."\n";
						}
					}
				}
				$mbs = [];
				foreach ($modules as $module){
					if (!in_array(basename($module), $mbs))
						$mbs[] = basename($module);
					
					$m = array_diff(
						scandir($modules_path),
						['.', '..']
					);

					if (!file_exists($modules_path.'/'.basename($module))) {
						copy($module, $modules_path.'/'.basename($module));
					}
				}
				if ($i <= 0) {
					$i++;
					goto restart;
				}
				$s = str_replace("\n", '', $s);
				$s = str_replace(';require', ";\nrequire", $s);
				$s = str_replace(';throw', ";\nthrow", $s);
				$s = str_replace($dirname, '.', $s);
				return $s;
			}
		],
	]
];