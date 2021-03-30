<?php

$settings = [
	'use' => false
];
return [
	'settings' => $settings,
	'rules' => [
		'/require \'(.*)\';/m' => [
			'type' => 'call',
			'reg' => function ($matches) {
				$content = $matches[1];
			}
		],
		'/require \"(.*)\";/m' => [
			'type' => 'call',
			'reg' => function ($matches) {
				$content = $matches[1];
			}
		],
	]
];