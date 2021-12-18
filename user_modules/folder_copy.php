<?php

use ProgressBar\Manager;


class folder_copy
{
	public static $i = 0;

	function __construct($from) {
		require_once '/home/kasiro/Документы/projects/testphp/user_modules/scripts/Bar/ProgressBar/Manager.php';
		require_once '/home/kasiro/Документы/projects/testphp/user_modules/scripts/Bar/ProgressBar/Registry.php';
		$count = static::getFilesRDI($from);
		$this->progress = new Manager(0, $count);
	}

	public function folder_copy($text = '', $from, $to, $black_list = []) {
		if ($text != '') {
			$this->progress->setFormat('%text%: %current%/%max% [%bar%] %percent%');
			$this->progress->addReplacementRule('%text%', 70, function ($buffer, $registry) use ($text) {return $text;});
		}
		$dir = opendir($from);
		@mkdir($to);
        while(false !== ($file = readdir($dir)) ) { 
            if (($file != '.') && ($file != '..')) { 
                if (is_dir($from . '/' . $file)) {
                    $this->folder_copy($text, $from . '/' . $file, $to . '/' . $file, $black_list);
                } else {
                    if (!in_array($file, $black_list)) {
						$this->progress->advance();
						copy($from . '/' . $file, $to . '/' . $file);
					}
                }
            } 
        } 
        closedir($dir);
	}
	
	public function clean($text = '', $path)
    {
		$count = static::getFilesRDI($path);
		$manager = new Manager(0, $count);
		if ($text != '') {
			$this->progress->setFormat('%text%: %current%/%max% [%bar%] %percent%' . "\r\n" . '%file%');
			$this->progress->addReplacementRule('%text%', 70, function ($buffer, $registry) use ($text) {return $text;});
		}
		$dir = $path;
        $di = new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS);
        $ri = new RecursiveIteratorIterator($di, RecursiveIteratorIterator::CHILD_FIRST);
        foreach ($ri as $file) {
            if ($file->isDir()) {
				rmdir($file);
			} else {
				$manager->advance();
				$this->progress->setFormat("%text%: %current%/%max% [%bar%] %percent%\r\n%file%");
				$this->progress->addReplacementRule('%text%', 70, function ($buffer, $registry) use ($text) {return $text;});
				$this->progress->addReplacementRule('%file%', 70, function ($buffer, $registry) use ($file) {return $file;});
				unlink($file);
			}
        }
        return true;
    }

	public static function getFoldersRDI($path){
		static::$i = 0;
		$dir = $path;
		$di = new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS);
		$ri = new RecursiveIteratorIterator($di, RecursiveIteratorIterator::CHILD_FIRST);
		foreach ($ri as $file) {
			if ($file->isDir())
			static::$i++;
		}
		return static::$i;
	}

	public static function getAllRDI($path){
		static::$i = 0;
		$dir = $path;
		$di = new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS);
		$ri = new RecursiveIteratorIterator($di, RecursiveIteratorIterator::CHILD_FIRST);
		foreach ($ri as $file) {
			static::$i++;
		}
		return static::$i;
	}

	public static function getFilesRDI($path){
		static::$i = 0;
		$dir = $path;
		$di = new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS);
		$ri = new RecursiveIteratorIterator($di, RecursiveIteratorIterator::CHILD_FIRST);
		foreach ($ri as $file) {
			if ($file->isFile())
				static::$i++;
		}
		return static::$i;
	}

	public static function getFoldersRec($path){
		$dir = scandir($path);
        foreach($dir as $file) { 
            if (!in_array($file, ['.', '..'])) {
				if (is_dir($path . '\\' . $file)) {
					static::$i++;
					static::getFoldersRec($path . '\\' . $file);
                }
            }
        }
		return static::$i;
	}

	public static function getFilesRec($path){
		try {
			$dir = scandir($path);
			foreach($dir as $file) {
				if (!in_array($file, ['.', '..'])) {
					if (is_dir($path . '\\' . $file)) {
						static::getFilesRec($path . '\\' . $file);
					} else {
						static::$i++;
					}
				}
			}
		} catch (InvalidArgumentException $e) {
			echo $path . ' not open...' . "\n";
		}
		return static::$i / 2;
	}
}