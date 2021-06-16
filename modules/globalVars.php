<?php declare(strict_types=1);

$module = new jModule;
$module->setSettings([
	'use' => true
]);
$module->setName(explode('.', basename(__FILE__))[0]);
$module->addreg('/<\$(\w+):\s*((?:(?(R)\w++|[^<>]*+)|(?R))*)>/m', "\$GLOBALS['$1'] = $2;");
$module->addreg('/<\$\w+>/m', "\$GLOBALS['$1']");
return $module;