<?php

$module = new jModule;
$module->setSettings([
	'use' => false
]);
$n = explode('.', basename(__FILE__))[0];
$module->setName($n);
// $module->addreg();
return $module;