<?php

namespace user_modules;

class Logger {
	public $path;
	public $CountLog = 0;

    public function __construct($file_path, $log = true) {
		date_default_timezone_set('Etc/GMT-7');
		$this->log = $log;
		if (!file_exists($file_path) && $this->log === true) {
            file_put_contents($file_path, '');
            $this->path = $file_path;
        } else {
            $this->path = $file_path;
		}
    }

    public function LOG($text){
		$mark = '[' . date('d.m.Y') . '|' . date('G:i:s') . ']: ';
		if ($this->log === true) file_put_contents($this->path, $mark . $text . "\r\n", FILE_APPEND);
		$this->CountLog++;
	}

	public function LOG_arr($name, $text_arr){
        $tab = '    ';
        $mark = '[' . date('d.m.Y') . '|' . date('G:i:s') . ']: ';
		if ($this->log === true) file_put_contents($this->path, $mark . $name . "[\r\n", FILE_APPEND);
		foreach ($text_arr as $name => $data) {
			if ($this->log === true) file_put_contents($this->path, $tab . $name . ' ' . $data . "\r\n", FILE_APPEND);
		}
		if ($this->log === true) file_put_contents($this->path, "]\r\n", FILE_APPEND);
	}

	public function __destruct(){
		if ($this->CountLog == 0) {
			unlink($this->path);
		}
	}
}
