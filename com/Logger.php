<?php

date_default_timezone_set('Etc/GMT-7');

class Logger {
	private static $count = 0;
	public static $countLines = 0;
	public $file = '';
	function __construct($file){
		$handle = fopen($file, "r");
		while(!feof($handle)){
			$line = fgets($handle);
			static::$countLines++;
		}
		fclose($handle);

		if (static::$countLines > 50000){
			file_put_contents($file, '');
		}

		if (file_exists($file)) {
			$this->file = $file;
		} else {
			file_put_contents($file, '');
			// throw new Exception('file '.basename($file).' is not exist');
		}
	}

	public function ot($flag = false){ 
		if (filesize($this->file) > 0 && $flag || static::$count > 0){
			file_put_contents($this->file, "\r\n", FILE_APPEND);
		}
	}

	public function add($text){
		$date = date('d.m.Y');
		$time = date('G:i');
		$log_text = "[$date][$time] $text";
		$log = file_get_contents($this->file);
		if (!str_contains($log, $log_text)){
			file_put_contents($this->file, $log_text . "\r\n", FILE_APPEND);
			static::$count++;
		}
	}
}