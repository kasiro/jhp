<?php declare(strict_types=1);

$module = new jModule;
$module->setSettings([
	'use' => true
]);
$module->setName(__FILE__);
$example = 'Class::__vnames()';
$module->addreg('/(\w*)::(\w*)\(\)/m', function ($matches) {
	$className = $matches[1];
	$err = [
		'static',
		'self'
	];
	if (!in_array($className, $err)) {
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
	}
	return "throw new Exception('undefiend class name $matches[1]')";
});
return $module;