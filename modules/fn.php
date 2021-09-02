<?php declare(strict_types=1);

$module = new jModule;
$module->setSettings([
	'use' => true
]);
$module->setName(__FILE__);
$module->addreg('/^([^\/\/].*|)fn((?<!\')(?<!\"))\((.*|)\) use \((\$.*)\) => {/m', '$1function ($3) use ($4) {');
$module->addreg('/^([^\/\/].+)(\$.*)[[:>:]] => {/m', '$1function ($2) {');
$module->addreg('/^([^\/\/].*|)fn\((.*|)\) => {/m', '$1function ($2) {');
return $module;