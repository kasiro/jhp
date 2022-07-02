<?php declare(strict_types=1);

$module = new jModule;
$module->setSettings([
	'use' => true
]);
$module->setName(__FILE__);
$module->addreg('/[\t\s]*\/\/-\s*.+$/m', function () {
	return '';
});
return $module;