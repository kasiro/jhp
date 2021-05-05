<?php

$module = new jModule;
$module->setSettings([
	'use' => true
]);
$n = explode('.', basename(__FILE__))[0];
$module->setName($n);
$module->addreg('/if(\s*)\((.*)\)/m', function ($matches){
	$if_in = $matches[2];
	$res = preg_replace('/(\$\w*\S*|\'.*\'|\".*\") in (\$\w*\S*|\[.*\]\S*|\w*\(.*\))/m', 'in_array($1, $2)', $if_in);
	$res = preg_replace('/(\$\w*\S*|\'.*\'|\".*\") of (\$\w*\S*|\w*\(.*\))/m', 'str_contains($2, $1)', $res);
	return 'if'.$matches[1].'('.$res.')';
});
return $module;