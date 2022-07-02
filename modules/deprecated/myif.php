<?php declare(strict_types=1);

$module = new jModule;
$module->setSettings([
	'use' => false
]);
$module->setName(__FILE__);
$module->addreg('/if(\s*)\((.*)\)/m', function ($matches){
	$if_in = $matches[2];
	echo $if_in . PHP_EOL;
	// $res = $if_in;
	$allstr = [];
	$i = 1;
	$res = preg_replace_callback('/\'(.*?[^\\\\])\'/m', function ($match) use (&$allstr, &$i){
		$to = '%string'.($i).'_quote1%';
		$allstr[$match[1]] = $to;
		$i++;
		return $to;
	}, $if_in);
	// $res = str_replace('main', 'res_main', $res);
	foreach ($allstr as $to => $what){
		$q = substr(explode('_', $what)[1], 0, -1);
		if ($q == 'quote1') {
			$res = str_replace($what, "'$to'", $res);
		}
	}
	return 'if'.$matches[1].'('.$res.')';
});
return $module;