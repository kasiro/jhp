<?php

class Logger {
	private static $count = 0;
	function __construct($file){
		if (file_exists($file)) {
			$this->file = $file;
		} else {
			throw new Exception('file '.basename($file).' is not exist');
		}
	}

	public function ot($flag = false){
		if (filesize($this->file) > 0 && $flag || static::$count > 0)
			file_put_contents($this->file, "\r\n", FILE_APPEND);
	}

	public function add($text){
		date_default_timezone_set('Etc/GMT-7');
		$date = date('d.m.Y');
		$time = date('G:i');
		$string = "[$date][$time] $text";
		$log = file_get_contents($this->file);
		if (!str_contains($log, $string)){
			file_put_contents($this->file, $string . "\r\n", FILE_APPEND);
			static::$count++;
		}
	}
}