<?php declare(strict_types=1);

// if (!class_exists('jModule')) require dirname(__DIR__).'/jModule.php';

$module = new jModule;
$module->setSettings([
	'use' => true
]);
$n = explode('.', basename(__FILE__))[0];
$module->setName($n);
$module->addreg('/nl (.*);/m', 'echo $1 . PHP_EOL;');
return $module;