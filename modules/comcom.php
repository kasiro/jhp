<?php declare(strict_types=1);

$module = new jModule;
$module->setSettings([
	'use' => true
]);
$n = explode('.', basename(__FILE__))[0];
$module->setName($n);
$module->addreg('/#alias: class (\w+) as (\w+);/m', 'class $2 extends $1 {}');
return $module;