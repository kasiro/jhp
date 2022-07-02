<?php declare(strict_types=1);

$module = new jModule;
$module->setSettings([
	'use' => true
]);
$module->setName(__FILE__);
$example = 'foreach $arr as $el {}';
$module->addreg('/foreach ([^(\()].*?[^(\))]*?) {/m', function ($matches) {
	return 'foreach ('.$matches[1].') {';
});
return $module;