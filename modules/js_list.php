<?php

$settings = [
	'use' => true
];
return [
	'settings' => $settings,
	'rules' => [
		'/(var|let) \{(.*)\} = (.*)/ms' => [
			'type' => 'string',
			'reg' => 'list($2) = $3',
			'do' => [],
			'then' => []
		],
		'/(var|let) \[(.*)\] = (.*)/ms' => [
			'type' => 'string',
			'reg' => 'list($2) = $3',
			'do' => [],
			'then' => []
		],
	]
];