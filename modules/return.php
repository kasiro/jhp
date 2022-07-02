<?php declare(strict_types=1);

$module = new jModule;
$module->setSettings([
	'use' => true
]);
$module->setName(__FILE__);
$module->addreg('/return\s+([^\r\n;]+[^;\r\n[({])$/m', function ($m){
	return $m[0].';';
});
$module->addreg('/return$/m', 'return;');
return $module;