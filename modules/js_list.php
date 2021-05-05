<?php

$module = new jModule;
$module->setSettings([
	'use' => true
]);
$n = explode('.', basename(__FILE__))[0];
$module->setName($n);
$module->addreg('/(var|let) \{((?:(?(R).*|[^}]*+)|(?R))*)\} = (.*)/m', 'list($2) = $3');
$module->addreg('/(var|let) \[((?:(?(R).*|[^]]*+)|(?R))*)\] = (.*)/m', 'list($2) = $3');
return $module;