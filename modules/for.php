<?php declare(strict_types=1);

$module = new jModule;
$module->setSettings([
	'use' => true
]);
$module->setName(__FILE__);
$example = 'for $arr as $el {}';
$module->addreg('/for ([^(\()].*[^(\))]*) {/m', function ($matches) {
	return 'for ('.$matches[1].') {';
});
return $module;