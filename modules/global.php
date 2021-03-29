<?php

$module_name = explode('.', basename(__FILE__))[0];
$settings = [
	'use' => true
];
return [
	'settings' => $settings,
	'rules' => [
		'/<g (\$.*)>/m' => [
			'type' => 'string',
			'reg' => 'global $1;'
		],
	]
];