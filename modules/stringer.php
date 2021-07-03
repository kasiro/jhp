<?php declare(strict_types=1);

$module = new jModule;
$module->setSettings([
	'use' => true,
	'desc' => '\"текст без $\" -> \'текст без $\''
]);
$module->setName(explode('.', basename(__FILE__))[0]);
$module->addreg(
	'/(.*?)"(.*?[^\\\\])"/m',
	function ($matches){
		if (!str_contains($matches[2], '$') && !str_contains($matches[1], 'import')) {
			return $matches[1]."'".$matches[2]."'";
		}
		return $matches[0];
	}
);
return $module;