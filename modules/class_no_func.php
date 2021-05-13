<?php declare(strict_types=1);

$module = new jModule;
$module->setSettings([
	'use' => true
]);
$n = explode('.', basename(__FILE__))[0];
$module->setName($n);
$module->addreg(
	'/^([^\n\/\/].*public|[^\n\/\/].*private|[^\n\/\/].*protected|)[[:>:]](.*)[[:<:]](.*)(\((.*)\)|\()/m',
	function ($matches) {
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
);
return $module;