<?php

$module_name = explode('.', basename(__FILE__))[0];
$settings = [
	'use' => true
];
return [
	'settings' => $settings,
	'rules' => [
		'/(\w*)::(\w*)\(\)/m' => [
			'type' => 'call',
			'example' => 'Class::__vnames()',
			'reg' => function ($matches) {
				switch ($matches[2]) {
					case '__vnames':
						return "array_keys(get_class_vars('$matches[1]'))";
						break;

					case '__vars':
						return "get_class_vars('$matches[1]')";
						break;
					
					default:
						return $matches[0];
						break;
				}
			}
		],
	]
];