<?php declare(strict_types=1);

$module = new jModule;
$module->setSettings([
	'use' => true
]);
$module->setName(__FILE__);
$example = 'if $arr as $el {}';
$module->addreg('/if ([^(\()].*) {/m', function ($matches) {
	return 'if ('.$matches[1].') {';
});
$module->addreg('/if ([^(\()][^{]*?):\s(.*?)$/m', function ($matches) {
	$action = explode(' ', $matches[2])[0];
	$end_symbol = $matches[2][strlen($matches[2]) - 1];
	$blacklist = [
		'{',
		'[',
		'('
	];
	if (!in_array($end_symbol, $blacklist) && $end_symbol !== ';')
		return 'if ('.$matches[1].') '.$matches[2].';';
	else if (!in_array($end_symbol, $blacklist) && $end_symbol == ';')
		return 'if ('.$matches[1].') '.$matches[2];
});
return $module;