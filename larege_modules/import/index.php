<?php

// if (!class_exists('jModule')) require $GLOBALS['jModule'];

# Сделать так что бы... можно было настроить каждый модуль из конфига папки проекта

$module = new jModule;
$module->setSettings([
	'use' => true
]);
$n = explode('.', basename(__FILE__))[0];
$module->setName($n);
// $module->addreg('#regexp#m', '$1 == $2');
// $module->addreg('#newregexp#m', function ($matches) use (&$module) {
// 	// $settings = $module->getSettings();
// });
return $module;