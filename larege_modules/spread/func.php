<?php

if (!function_exists('check_array')){
	/**
	 * Проверяет надо ли изменять слой массива
	 *
	 * @return void
	 */
	function check_array($text){
		$rules = [
			'/...\$\w+/m',
			'/...\[.*\]/m'
		];
		$res = [];
		foreach ($rules as $rule){
			$res[] = (preg_match($rule, $text) ? 'true' : 'false');
		}
		$res = array_unique($res);
		$or = in_array('true', $res) && in_array('false', $res) ? true : false;
		$and = in_array('true', $res) && count($res) == 1 ? true : false;
		if (in_array('true', $res)){
			return true;
		}
		return false;
	}
}
if (!function_exists('mystart')){
	function mystart($matches, $args){
		$array_in = $matches[1];
		list($key, $indient) = $args;
		if (check_array($array_in)){
			// if (preg_match('/([^\n]*|[^\n]*)(\'.*\'|\d+) => [^[]/m', $array_in)){
			// 	$array_in = preg_replace_callback('/([^\n]*|[^\n]*)(\'.*\'|\d+) => [^[]/m', function ($matches){
			// 		// print_r($matches);
			// 		$tabs = $matches[1];
			// 		if (str_contains($tabs, "\t")){
			// 			$count = count(explode("\t", $tabs)) + 2;
			// 			// echo $count . PHP_EOL;
			// 		}
			// 		return str_repeat('	', $count).'['.str_replace($matches[1], '', $matches[0]).']';
			// 		// return '['.$matches[0].']';
			// 	}, $array_in);
			// }
			if (preg_match('/^(\s*|\t*)(\'.*\'|\d+)\s*=>\s\[(.*)\]/ms', $array_in)){
				$array_in = preg_replace_callback('/^(\s*|\t*)(\'.*\'|\d+)\s*=>\s(\[(.*)\])/ms', function ($matches) use ($key){
					$code = $matches[1].implode(PHP_EOL.$matches[1], explode(PHP_EOL, $matches[0]));
					if (preg_match('/^(\s*|\t*)(\'.*\'|\d+)\s*=>\s\[(.*)\]/ms', $code)){
						$arr = $matches[3];
						// $code = myspread([1 => $arr], [
						// 	$matches[2],
						// 	$matches[1]
						// ]);
						// echo $code . PHP_EOL;
					}
					// return $matches[1].'['.$code.']';
					return $matches[1].'['.$matches[0].']';
				}, $array_in);
			}
			$array_in = preg_replace_callback('/...(\$\w+)/m', function ($matches){return $matches[1];}, $array_in);
			$array_in = preg_replace_callback('/...(\[.*\])/m', function ($matches){return $matches[1];}, $array_in);
			if ($key){
				if ($indient) {
					return $indient.$key.' => array_merge('."\n".$indient.$array_in."\n".$indient.');';
				}
				return $key.' => array_merge('."\n".$array_in."\n".$indient.');';
			}
			return 'array_merge('.$array_in.');';
		}
		return $matches[0];
	}
}