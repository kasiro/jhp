<?php

$module = new jModule;
$module->setSettings([
	'use' => true
]);
$n = explode('.', basename(__FILE__))[0];
$module->setName($n);
$example = 'Class::__vnames()';
$module->addreg('/(\w*)::(\w*)\(\)/m', function ($matches) {
	switch ($matches[2]) {
		case '__vnames':
			return "array_keys(get_class_vars('$matches[1]'))";
			break;

		case '__vars':
			return "get_class_vars('$matches[1]')";
			break;
		
		default:
			return $matches[0];
			break;
	}
});
return $module;