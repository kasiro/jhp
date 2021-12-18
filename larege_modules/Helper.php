<?php

require __DIR__.'/import/func.php';

class JhpHelper {
	public static function getImportModules(array|string $path){
		$modules = [];
		$allvars = get_defined_vars();
		$getMod = function($path) use ($allvars) {
			extract($allvars);
			$text = file_get_contents($path);
			preg_replace_callback('/(.*)import: include \'(.*)\';/m', function ($matches) use (&$modules) {
				$modules[] = $matches[2];
			}, $text);
			preg_replace_callback('/(\t*|\s*)import_array: include \[((?:(?(R)\w++|[^]]*+)|(?R))*)\];/m', function ($matches) use (&$modules) {
				foreach (import_array($matches[2]) as $module){
					$modules[] = $module;
				}
			}, $text);
			return $modules;
		};
		if (is_string($path)){
			return $getMod($path);
		} elseif (is_array($path)){
			foreach ($path as $file_path){
				$modules = array_merge($modules, $getMod($path));
			}
			return $modules;
		}
		return false;
	}

	public static function getClassContent(array|string $path){
		$code = file_get_contents($path);
		if (is_string($path)){
			$ClassContent = [];
			$code = preg_replace_callback('/class\s*(\w+)\s*{(.*?\n)}/m', function ($matches) {
				$className = $matches[1];
				$classContent = $matches[2];
				$ClassContent[$className] = $classContent;
				return $matches[0];
			}, code);
			return $ClassContent;
		} elseif (is_array($path)){
			$ClassContentFiles = [];
			foreach ($path as $p){
				$code = preg_replace_callback('/class\s*(\w+)\s*{(.*?\n)}/m', function ($matches) {
					$className = $matches[1];
					$classContent = $matches[2];
					$filename = explode('.', basename($p))[0];
					$ClassContentFiles[$filename][$className] = $classContent;
					return $matches[0];
				}, code);
			}
			return $ClassContentFiles;
		}
		return false;
	}
}