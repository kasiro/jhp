<?php

class Storage {
	private static $data = [];

	public static function set($key, $value){
		if (!isset(static::$data[$key])){
			static::$data[$key] = $value;
		}
	}

	public static function get($key){
		if (isset(static::$data[$key])){
			if (is_callable(static::$data[$key])){
				return static::$data[$key]();
			} else {
				return static::$data[$key];
			}
		}
		return false;
	}
}