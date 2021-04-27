<?php

$settings = [
	'use' => true
];
return [
	'settings' => $settings,
	'rules' => [
		'/(var|let) \{((?:(?(R).*|[^}]*+)|(?R))*)\} = (.*)/m' => [
			'type' => 'string',
			'reg' => 'list($2) = $3',
			'do' => [],
			'then' => []
		],
		'/(var|let) \[((?:(?(R).*|[^]]*+)|(?R))*)\] = (.*)/m' => [
			'type' => 'string',
			'reg' => 'list($2) = $3',
			'do' => [],
			'then' => []
		],
	]
];