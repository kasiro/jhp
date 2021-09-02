<?php declare(strict_types=1);

$module = new jModule;
$module->setSettings([
	'use' => true,
	'Ex' => '\\Throwable'
]);
$module->setName(__FILE__);
$module->addreg('/catch(\s*)\((\$.*)\)/m', function ($matches) use (&$module) {
	$settings = $module->getSettings();
	return 'catch'.$matches[1].'('.$settings['Ex'].' '.$matches[2].')';
});
return $module;