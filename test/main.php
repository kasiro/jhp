<?php

require '/home/kasiro/Документы/projects/testphp/user_modules/fs.php';

$name = 'Imya';
$surname = 'Familya';
$allvars = get_defined_vars();
$main = function() use ($allvars) {
	extract($allvars);
	$allvars = get_defined_vars();
	$hello = function() use ($allvars) {
		extract($allvars);
		echo 'hello ', $name, ' ', $surname . PHP_EOL;
	};
	$hello();
};
$main();
$bot->app(function ($message) use (&$bot) {
	
});
echo "hello \"{$name}\", im lisa" . PHP_EOL;