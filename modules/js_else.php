<?php declare(strict_types=1);

if (!function_exists('getHash')) {
	function getHash($len){
		$end_string = '';
		$string = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
		for ($i = 1; $i <= $len; $i++) {
			$end_string .= $string[mt_rand(0, strlen($string) - 1)];
		}
		return '$%' . $end_string;
	}
}

if (!function_exists('ternarn_else')) {
	function ternarn_else($arr, $i, $end_string = '', $hash = '', $mini = true){
		if (!$mini) {
			$end = ($i + 1 < count($arr)) ? '('."\n".str_repeat("\t", $i+1).'@' . $arr[$i + 1] . ' ? ' . $arr[$i + 1] . ' : ' . $hash . "\n" .str_repeat("\t", $i) . ')' : 'null';
		} else {
			$end = ($i + 1 < count($arr)) ? '(@' . $arr[$i + 1] . ' ? ' . $arr[$i + 1] . ' : ' . $hash . ')' : 'null';
		}
		if ($end == 'null') return str_replace($hash, 'null', $end_string);
		if ($i == 0) $string = $arr[$i] . ' ? ' . $arr[$i] . ' : ' . $hash;
		else $string = '(@' . $arr[$i] . ' ? ' . $arr[$i] . ' : ' . $hash .')';
		// echo $end . "\n";
		if ($i == 0) $end_string .= $string;
		$end_string = str_replace($hash, $end, $end_string);
		$i++;
		$end_string = ternarn_else($arr, $i, $end_string,  $hash, $mini);
		return $end_string;
	}
}

$module = new jModule;
$module->setSettings([
	'use' => true,
	'mini' => false
]);
$module->setName(__FILE__);
$module->addreg('/^(\$.*) = (.*)(\|)(.*?);/m', function ($matches) use (&$module) {
	$settings = $module->getSettings();
	$res = '';
	for ($i = 2; $i < count($matches); $i++) {
		$res .= $matches[$i];
	}
	$arr = explode(' | ', $res);
	$hash = getHash(10);
	if ($settings['mini']) {
		$s = ternarn_else($arr, 0, hash: $hash);
	} else {
		$s = ternarn_else($arr, 0, hash: $hash, mini: false);
	}
	$s .= ';';
	return $matches[1] . ' = @' . $s;
});
return $module;