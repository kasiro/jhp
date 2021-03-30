<?php

if (!function_exists('import')) {
	function import($path){
		$mp = '/home/kasiro/Документы/projects/mphp/file_req';
		if (file_exists("$mp/{$path}.php")) {
			return "$mp/{$path}.php";
		} else {
			return "module $path not found";
		}
	}
}
$throw_text = function ($path){
	return "throw new Exception('[jhp] $path');";
};
$settings = [
	'use' => true
];
return [
	'settings' => $settings,
	'rules' => [
		'/import \'(.*)\';/m' => [
			'type' => 'call',
			'reg' => function ($matches) use ($throw_text) {
				$module_name = $matches[1];
				$path = import($module_name);
				if ($path != "module $module_name not found")
					return "require '$path';";
				else
					return $throw_text($path);
			}
		],
		'/import \"(.*)\";/m' => [
			'type' => 'call',
			'reg' => function ($matches) use ($throw_text) {
				$module_name = $matches[1];
				$path = import($module_name);
				if ($path != "module $module_name not found")
					return "require \"$path\";";
				else
					return $throw_text($path);
			}
		],
		'/import\(\'(.*)\'\);/m' => [
			'type' => 'call',
			'reg' => function ($matches) use ($throw_text) {
				$module_name = $matches[1];
				$path = import($module_name);
				if ($path != "module $module_name not found")
					return "require('$path');";
				else
					return $throw_text($path);
			}
		],
		'/import\(\"(.*)\"\);/m' => [
			'type' => 'call',
			'reg' => function ($matches) use ($throw_text) {
				$module_name = $matches[1];
				$path = import($module_name);
				if ($path != "module $module_name not found")
					return "require(\"$path\");";
				else
					return $throw_text($path);
			}
		],
		'/import_once \'(.*)\';/m' => [
			'type' => 'call',
			'reg' => function ($matches) use ($throw_text) {
				$module_name = $matches[1];
				$path = import($module_name);
				if ($path != "module $module_name not found")
					return "require_once '$path';";
				else
					return $throw_text($path);
			}
		],
		'/import_once \"(.*)\";/m' => [
			'type' => 'call',
			'reg' => function ($matches) use ($throw_text) {
				$module_name = $matches[1];
				$path = import($module_name);
				if ($path != "module $module_name not found")
					return "require_once \"$path\";";
				else
					return $throw_text($path);
			}
		],
		'/import_once\(\'(.*)\'\);/m' => [
			'type' => 'call',
			'reg' => function ($matches) use ($throw_text) {
				$module_name = $matches[1];
				$path = import($module_name);
				if ($path != "module $module_name not found")
					return "require_once('$path');";
				else
					return $throw_text($path);
			}
		],
		'/import_once\(\"(.*)\"\);/m' => [
			'type' => 'call',
			'reg' => function ($matches) use ($throw_text) {
				$module_name = $matches[1];
				$path = import($module_name);
				if ($path != "module $module_name not found")
					return "require_once(\"$path\");";
				else
					return $throw_text($path);
			}
		],
	]
];