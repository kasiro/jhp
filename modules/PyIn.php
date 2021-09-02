<?php declare(strict_types=1);

$module = new jModule;
$module->setSettings([
	'use' => false,
	'version' => 8
]);
$module->setName(__FILE__);
$module->addreg('/if(\s*)\((.*)\)/m', function ($matches) use (&$module){
	$settings = $module->getSettings();
	$if_in = $matches[2];
	$res = preg_replace('/(\$\w*\S*|\'.*?\'|\".*?\"|%.*%) in (\$\w*\S*|\[.*\]\S*|\w*\(.*\))/m', 'in_array($1, $2)', $if_in);
	switch ($settings['version']) {
		case 8:
			$res = preg_replace('/(\$\w*\S*|\'.*?\'|\".*?\"|%.*%) of (\$\w*\S*|\w*\(.*\))/m', 'str_contains($2, $1)', $res);
			break;
		
		case 7:
			$res = preg_replace('/(\$\w*\S*|\'.*?\'|\".*?\"|%.*%) of (\$\w*\S*|\w*\(.*\))/m', 'strops($2, $1) !== false', $res);
			break;
	}
	return 'if'.$matches[1].'('.$res.')';
});
return $module;