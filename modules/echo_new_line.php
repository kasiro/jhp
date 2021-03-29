<?php

$module_name = explode('.', basename(__FILE__))[0];
$settings = [
	'use' => true
];
return [
	'settings' => $settings,
	'rules' => [
		'/nl (.*);/m' => [
			'type' => 'string',
			'reg' => 'echo $1 . "\n";'
		],
	]
];