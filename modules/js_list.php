<?php

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