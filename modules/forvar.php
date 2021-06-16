<?php declare(strict_types=1);

$module = new jModule;
$module->setSettings([
	'use' => true
]);
$module->setName(explode('.', basename(__FILE__))[0]);
$module->addreg(
	'/for(\s*)\((\$\w++)(.*)count\((.*)\);(.*)\) as (\$\w++) \{\n(\t*|\s*)((?:(?(R)\w++|[^}]+\N\n)|(?R))*)\}\;/m',
	'for ($2$3count($4);$5){'."\n".'$7$6 = $4[$2];'."\n".'$7$8}'
);
$module->addreg(
	'/for(\s*)\((\$\w++)(.*)strlen\((.*)\);(.*)\) as (\$\w++) \{\n(\t*|\s*)((?:(?(R)\w++|[^}]+\N\n)|(?R))*)\}\;/m',
	'for ($2$3strlen($4);$5){'."\n".'$7$6 = $4[$2];'."\n".'$7$8}'
);
return $module;