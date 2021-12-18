<?php


/**
 * class for generate uids
 */
class uid_generator {

	/**
	 * uses symbols for mask
	 * @var string
	 **/
	public static $abc = '0123456789abcdefghijklmnopqrstuvwxyz';
	
	/**
	 * generate uid from mask
	 **/
	public static function uid_generate(string $mask, string $el, $set = false){
		if ($set === false) {
			$masked = str_split($mask);
			foreach ($masked as &$n) {
				if ($n == $el){
					$n = static::$abc[random_int(0, strlen(static::$abc)-1)];
				}
			}
			$uid = join('', $masked);
		} else {
			$uid = str_replace($el, $set, $mask);
		}
		return $uid;
	}

	/**
	 * generate standart uid from mask xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx
	 * @return string
	 **/
	public static function generate_standart_uid($set = false){
		return static::uid_generate('xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx', 'x');
	}

}