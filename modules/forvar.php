<?php

$settings = [
	'use' => true
];
return [
	'settings' => $settings,
	'rules' => [
		'/for(\s*)\((\$\w++)(.*)count\((.*)\);(.*)\) as (\$\w++) \{\n(\t*|\s*)((?:(?(R)\w++|[^}]+\N\n)|(?R))*)\}\;/m' => [
			'type' => 'string',
			'reg' => 'for ($2$3count($4);$5){'."\n".'$7$6 = $4[$2];'."\n".'$7$8}'
		],
	]
];