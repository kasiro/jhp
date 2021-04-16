<?php

$settings = [
	'use' => true
];
return [
	'settings' => $settings,
	'rules' => [
		'/<j>scandir\((.*)\)/m' => [
			'type' => 'string',
			'reg' => "array_diff(scandir($1), ['.', '..'])",
		],
	]
];