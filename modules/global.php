<?php

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