<?php declare(strict_types=1);

$module = new jModule;
$module->setSettings([
	'use' => true
]);
$module->setName(explode('.', basename(__FILE__))[0]);
$module->addreg('/<g (\$.*)>/m', 'global $1;');
return $module;