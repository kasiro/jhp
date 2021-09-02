<?php declare(strict_types=1);

$module = new jModule;
$module->setSettings([
	'use' => false
]);
$module->setName(__FILE__);
$module->addreg('/#alias: class (\w+) as (\w+);/m', 'class $2 extends $1 {}');
return $module;