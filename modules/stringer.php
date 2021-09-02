<?php declare(strict_types=1);

$module = new jModule;
$module->setSettings([
	'use' => false,
	'desc' => '\"текст без $\" -> \'текст без $\''
]);
$module->setName(__FILE__);
$module->addreg(
	'/(.*?)"(.*?[^\\\\])"/m',
	function ($matches){
		$black_list = [
			'\n',
			'\r',
			'\t'
		];
		if (strlen($matches[2]) == 0) return $matches[1]."'".$matches[2]."'";
		if (!str_contains($matches[2], '$') && !str_contains($matches[2], 'import') ) {
			if (str_contains($matches[2], "'")){
				$matches[2] = preg_replace("/([^\\\\])\'/m", "$1\\'", $matches[2]);
			}
			return $matches[1]."'".$matches[2]."'";
		}
		return $matches[0];
	}
);
return $module;