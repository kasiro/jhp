<?php

$module = new jModule;
$module->setSettings([
	'use' => true,
	'Ex' => '\\Throwable'
]);
$n = explode('.', basename(__FILE__))[0];
$module->setName($n);
$settings = $module->getSettings();
$module->addreg('/catch(\s*)\((\$.*)\)/m', 'catch$1('.$settings['Ex'].' $2)');
return $module;