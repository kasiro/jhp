<?php

$settings = [
	'use' => true
];
return [
	'settings' => $settings,
	'rules' => [
		'/jscandir\((.*)\)/m' => [
			'type' => 'string',
			'reg' => "array_diff(scandir($1), ['.', '..'])",
		],
	]
];