<?php

if (!function_exists('myrglob')) {
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
	}
}
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
		eval('$files'." = [$path];");/*  */
		foreach ($files as $file){
			if (!is_string($file)) {
				throw new Exception('[jhp: 404] import_array el is not STRING');
			}
			yield $file;
		}
	}
}