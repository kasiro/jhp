<?php declare(strict_types=1);

$module = new jModule;
$module->setSettings([
	'use' => false
]);
$module->setName(__FILE__);
$module->addreg(
	'/for \((\$\w+)(.*)count\((.*?)\); (.*)\) as (\$\w+) \{\n(\t*|\s*)/m',
	'for ($1$2count($3); $4){'.PHP_EOL.'$6$5 = $3[$1];'.PHP_EOL.'$6'
);
$module->addreg(
	'/for \((\$\w+)(.*)strlen\((.*)\); (.*)\) as (\$\w+) \{\n(\t*|\s*)/m',
	'for ($1$2strlen($3); $4){'.PHP_EOL.'$6$5 = $3[$1];'.PHP_EOL.'$6'
);
return $module;