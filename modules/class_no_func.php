<?php

$settings = [
	'use' => true
];
return [
	'settings' => $settings,
	'rules' => [
		'/^([^\n\/\/].*public|[^\n\/\/].*private|[^\n\/\/].*protected|)[[:>:]](.*)[[:<:]](.*)(\((.*)\)|\()/m' => [
			'type' => 'call',
			'reg' => function ($matches) {
				if (
					!str_contains($matches[1], '\'')
					&& !str_contains($matches[1], '"')
					&& !str_contains($matches[2], 'function')
					&& !str_contains($matches[0], '// ')
				)
					return $matches[1] . $matches[2] . 'function ' . $matches[3] . $matches[4];
				else
					return $matches[0];
			}
		],
	]
];