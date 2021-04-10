<?php

$settings = [
	'use' => true
];
return [
	'settings' => $settings,
	'rules' => [
		'/#alias: class (\w+) as (\w+);/m' => [
			'type' => 'string',
			'reg' => 'class $2 extends $1 {}'
		],
	]
];