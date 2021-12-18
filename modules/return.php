<?php declare(strict_types=1);

$module = new jModule;
$module->setSettings([
	'use' => true
]);
$module->setName(__FILE__);
$module->addreg('/return\s+(.*)([^;\n{]+)$/m', function ($matches) use (&$module) {
	$settings = $module->getSettings();
	return 'return '.$matches[1].$matches[2].';';
});
$module->addreg('/return$/m', function ($matches) use (&$module) {
	$settings = $module->getSettings();
	return 'return;';
});
return $module;