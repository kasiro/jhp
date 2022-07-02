<?php

if (!function_exists('is_list')) {
	function is_list($arr){
		return array_values($arr) === $arr;
	}
}

if (!function_exists('jscandir')) {
	function jscandir($s){
		return array_diff(
			scandir($s),
			['.', '..']
		);
	}
}

if (!function_exists('str_contains')) {
	function str_contains($string, $find){
		if (strpos($string, $find) !== false) {
			return true;
		}
		return false;
	}
}

if (!function_exists('newenv')) {
	function newenv(string $filepath){
		list($dir, $filename) = [dirname($filepath), basename($filepath)];
		$files = scandir($dir);
		$data = array_diff($files, ['.', '..']);
		$newData = $dir.'/'.[...array_filter($data, function ($e) use ($dir, $filename) {
			if ($e == $filename){
				return $dir.'/'.$e;
			}
		})][0];
		putenv(
			file_get_contents(
				$newData
			)
		);
	}
}