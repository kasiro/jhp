<?php declare(strict_types=1);

$types = require __DIR__.'/types/types.php';

$module = new jModule;
$module->setSettings([
	'use' => true
]);
$module->setName(__FILE__);
$module->addreg('/^([^\/\/\n].*|)fn((?<!\')(?<!\"))\((.*|)\) use \((\$.*)\) => {/m', '$1function ($3) use ($4) {');
$module->addreg('/^([^\/\/\n].+)(\$.*)[[:>:]] => {/m', '$1function ($2) {');
$module->addreg('/^([^\/\/\n].*|)fn\((.*|)\) => {/m', '$1function ($2) {');
$typeString = implode('|', $types);
$module->addreg('/^([^\/\/\n].*|)fn\((.*|)\): ('.$typeString.') => {/m', '$1function ($2): $3 {');
return $module;