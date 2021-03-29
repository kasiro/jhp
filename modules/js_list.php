<?php

$module_name = explode('.', basename(__FILE__))[0];
$settings = [
	'use' => true
];
return [
	'settings' => $settings,
	'rules' => [
		'/(var|let) \{(.*)\} = (.*)/m' => [
			'type' => 'string',
			'reg' => 'list($2) = $3',
			'do' => [],
			'then' => []
		],
		'/(var|let) \[(.*)\] = (.*)/m' => [
			'type' => 'string',
			'reg' => 'list($2) = $3',
			'do' => [],
			'then' => []
		],
	]
];