<?php

$module_name = explode('.', basename(__FILE__))[0];
$settings = [
	'use' => true
];
return [
	'settings' => $settings,
	'rules' => [
		'/(.*)((?<!\ )(?!\-)\>(?!\ )(?!\())(\$\w*?(?!\d))[[:>:]]/m' => [
			'type' => 'call',
			'group' => 'arel',
			'count' => 10,
			'reg' => function ($matches) {
				// print_r($matches);
				if (
					!str_contains($matches[1], '// ')
					&& !str_contains($matches[1], 'if')
				) {
					return $matches[1] . '[' . $matches[3] . ']';
				} else return $matches[0];
			},
			'do' => [],
			'then' => []
		],
		'/(.*)((?!\ )(?<!\-)\>(?!\ )(?!\())(\w*?(?!\d))[[:>:]]/m' => [
			'type' => 'call',
			'group' => 'arel',
			'count' => 10,
			'reg' => function ($matches) {
				if (
					!str_contains($matches[1], '// ')
					&& !str_contains($matches[1], 'if')
				) {
					// print_r($matches);
					return $matches[1] . "['" . $matches[3] . "']";
				} else return $matches[0];
			},
			'do' => [],
			'then' => []
		],
	]
];
