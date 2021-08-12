<?php declare(strict_types=1);

$module = new jModule;
$module->setSettings([
	'use' => false
]);
$module->setName(explode('.', basename(__FILE__))[0]);
$module->addreg('/#alias: class (\w+) as (\w+);/m', 'class $2 extends $1 {}');
return $module;