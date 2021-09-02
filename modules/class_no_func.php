<?php declare(strict_types=1);

$module = new jModule;
$module->setSettings([
	'use' => true,
	'ots' => true
]);
$module->setName(__FILE__);
if (!function_exists('findType')) {
	function findType($sym){
		$ops = [
			'int',
			'string',
			'bool',
			'array'
		];
		$find = false;
		foreach ($ops as $el){
			if (in_array($el, $sym)) {
				$find = $el;
				break;
			}
		}
		foreach ($ops as $el){
			$sym = array_filter($sym, fn($e) => $e != $el);
		}
		$str = implode(' ', $sym);
	
		if (!empty($str)) $mat2 = " {$str} ";
		else $mat2 = ' ';
		
		return [$find, $mat2];
	}
}
$module->addreg(
	'/^([^\n\/\/].*public|[^\n\/\/].*private|[^\n\/\/].*protected|)[[:>:]](.*)[[:<:]](.*)(\((.*)\)|\()/m',
	function ($matches) use (&$module) {
		$sym = explode(' ', trim($matches[2]));
		if (!str_contains($matches[1], '\'')){
			if (!str_contains($matches[1], '"')){
				if (!in_array('function', $sym)){
					if (!str_contains($matches[0], '// ')){
						list($find, $mat2) = findType($sym);
						if ($find) {
							if (array_key_exists('ots', $module->getSettings())){
								if ($module->getSettings()['ots'] === true) {
									return $matches[1] . $mat2 . 'function ' . $matches[3] . $matches[4] . ': '.$find.' ';
								} else {
									return $matches[1] . $mat2 . 'function ' . $matches[3] . $matches[4] . ': '.$find;
								}
							}
							return $matches[1] . $mat2 . 'function ' . $matches[3] . $matches[4] . ':'.$find;
						} else {
							return $matches[1] . $mat2 . 'function ' . $matches[3] . $matches[4];
						}
					}
				}
			}
		}
		return $matches[0];
	}
);
return $module;