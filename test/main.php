<?php

$name = 'Danil';
$surname = 'Kashirskikh';
$allvars = get_defined_vars();
$main = function() use ($allvars) {
	extract($allvars);
	$allvars = get_defined_vars();
	$loop = function() use ($allvars) {
		extract($allvars);
		echo 'hello ', $name, ' ', $surname . PHP_EOL;
	};
	$loop();
};
$main();

$bot->app(function ($message) use (&$bot) {
	
});