<?php

$module = new jModule;
$module->setSettings([
	'use' => true
]);
$n = explode('.', basename(__FILE__))[0];
$module->setName($n);
$module->addreg('/<g (\$.*)>/m', 'global $1;');
return $module;