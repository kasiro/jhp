<?php declare(strict_types=1);

$module = new jModule;
$module->setSettings([
	'use' => true,
	'spaces' => false
]);
$module->setName(__FILE__);
$module->addreg(
	'/( *|\t*)([^\t\n]*)[^fn]?\(([^(]*)\) => {/m',
	function ($matches) use (&$module){
		if (
			str_starts_with($matches[0], '// ')
			|| str_starts_with($matches[0], '#')
			|| str_ends_with($matches[2], 'fn')
		) {
			return $matches[0];
		} else {
			$settings = $module->getSettings();
			if (!preg_match('/fn\(.*\)/m', $matches[2]) && !preg_match('/fn\(.*\) use \(.*\)/m', $matches[2])){
				if ($settings['spaces']){
					$separator = '    ';
				} else {
					$separator = "\t";
				}
				$end_string = '';
				$end_string .= $matches[1];
				$end_string .= '$allvars = get_defined_vars();';
				$end_string .= "\n";
				$end_string .= $matches[1];
				$end_string .= $matches[2];
				$end_string .= 'function('.$matches[3].') use ($allvars) {';
				$end_string .= "\n";
				$end_string .= $separator;
				$end_string .= $matches[1];
				$end_string .= 'extract($allvars);';
				// if (strlen($end_string) == 0) return $matches[0];
				return $end_string;
			}
			// return $matches[0];
		}
	}
);
return $module;