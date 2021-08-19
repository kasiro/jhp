<?php

class arr {
	public static function is_assoc(array $array): bool {
		return !empty(preg_grep('/[^0-9]/', array_keys($array)));
	}
	
	public static function array_change(array $array): array {
		$newArr = [];
		while (count($newArr) != count($array)){
			foreach ($array as $el) {
				$newArr[] = $array[mt_rand(0, count($array) - 1)];
			}
			$newArr = array_unique($newArr);
			if (count($newArr) == count($array)) {
				return $newArr;
			}
		}
	}
	
	public static function blob_string_sort(array $array){
		for ($j = 0; $j < count($array) - 1; $j++){
			for ($i = 0; $i < count($array) - $j - 1; $i++){
				if (strlen($array[$i]) < strlen($array[$i + 1])){
					list($array[$i], $array[$i + 1]) = [$array[$i + 1], $array[$i]];
				}
			}
		}
		return $array;
	}
}