<?php

$settings = [
	'use' => true
];
return [
	'settings' => $settings,
	'rules' => [
		'/^([^\/\/].*|)fn((?<!\')(?<!\"))\((.*|)\) use \((\$.*)\) => {/m' => [
			'type' => 'string',
			'group' => 'arf',
			'reg' => '$1function ($3) use ($4) {',
			'do' => [],
			'then' => []
		],
		'/^([^\/\/].+?)((?<!\')(?<!\"))(\$.*) => (\$.*) => {/m' => [
			'type' => 'string',
			'group' => 'arf',
			'reg' => '$1function($3, $4) {',
			'do' => [],
			'then' => []
		],
		'/^([^\/\/].+)(\$.*)[[:>:]] => {/m' => [
			'type' => 'string',
			'group' => 'arf',
			'reg' => '$1function ($2) {',
			'do' => [],
			'then' => []
		],
		'/^([^\/\/].*|)fn\((.*|)\) => {/m' => [
			'type' => 'string',
			'group' => 'arf',
			'reg' => '$1function ($2) {',
			'do' => [],
			'then' => []
		],
	]
];