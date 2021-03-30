<?php

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