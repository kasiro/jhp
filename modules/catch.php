<?php

$settings = [
	'use' => true,
	'Ex' => '\\Throwable'
];
return [
	'settings' => $settings,
	'rules' => [
		'/catch(\s*)\((\$.*)\)/m' => [
			'type' => 'string',
			'reg' => 'catch$1('.$settings['Ex'].' $2)'
		],
	]
];