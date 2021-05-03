<?php

$module = new jModule;
$module->setSettings([
	'use' => true
]);
$n = explode('.', basename(__FILE__))[0];
$module->setName($n);
$module->addreg('/^([^\/\/].*|)fn((?<!\')(?<!\"))\((.*|)\) use \((\$.*)\) => {/m', '$1function ($3) use ($4) {');
$module->addreg('/^([^\/\/].+)(\$.*)[[:>:]] => {/m', '$1function ($2) {');
$module->addreg('/^([^\/\/].*|)fn\((.*|)\) => {/m', '$1function ($2) {');
return $module;