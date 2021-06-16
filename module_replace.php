<?php

$what = '$n = explode(\'.\', basename(__FILE__))[0];'."\n".'$module->setName($n);';
$to = '$module->setName(explode(\'.\', basename(__FILE__))[0]);';
$modules = glob(__DIR__ . '/modules/*.php');
foreach ($modules as $module){
	$text = file_get_contents($module);
	if (str_contains($text, $what)){
		$text = str_replace($what, $to, $text);
		file_put_contents($module, $text);
	}
}
