<?php


class general {
	public $algo;
	private $hash;
	private $scan;

	public function __construct(string $algo){
		$this->algo = $algo;
	}

	public function getHashSum($path){
		return hash_file($this->algo, $path);
	}

	public function set_scan_folder($path){
		$this->scan = $path;
		if (!file_exists($path)) mkdir($path);
	}
	public function set_hash_folder($path){
		$this->hash = $path;
		if (!file_exists($path)) mkdir($path);
	}

	public function get_scan_folder(){
		return $this->scan;
	}

	public function get_hash_folder(){
		return $this->hash;
	}

	public function scan_folder($path){
		$list = [];
		$di = new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS);
		$ri = new RecursiveIteratorIterator($di, RecursiveIteratorIterator::CHILD_FIRST);
		foreach ($ri as $full => $file){
			if ($file->isFile()) $list[] = $full;
		}
		return $list;
	}

	public function update_folder($to, $from){
		# $to = path/stie
		# $from = path/Updates/site_0-3
		# path/stie/text.txt нету
		# path/Updates/site_0-3/text.txt есть
		# site_0-3/text.txt -> path/stie/text.txt
	}

	public function gen_hashsum($echo = true){
		$list = $this->scan_folder($this->get_scan_folder());
		$hash_path = $this->get_hash_folder();
		if (count($list) > 0) {
			foreach ($list as $full_path){
				$mini_path = str_replace(__DIR__.'/', '', $full_path);
				$save_path = str_replace('/', '-', $mini_path);
				$save_path .= '.hashsum';
				$hashsum = $this->getHashSum($full_path);
				file_put_contents($hash_path.'/'.$save_path, $hashsum);
				if ($echo) echo $mini_path . ' : hashed' . PHP_EOL;
			}
		}
	}

	public function full_match($echo = true){
		$scan_list = $this->scan_folder($this->get_scan_folder());
		$hash_path = $this->get_hash_folder();
		if (count($scan_list) > 0) {
			foreach ($scan_list as $full_path){
				$mini_path = str_replace(__DIR__.'/', '', $full_path);
				$save_path = str_replace('/', '-', $mini_path);
				$save_path .= '.hashsum';
				$saved_hashsum_path = $hash_path.'/'.$save_path;
				$hashsum = $this->getHashSum($full_path);
				$saved_hashsum = file_exists($saved_hashsum_path) ? file_get_contents($saved_hashsum_path) : '';
				if (strlen($saved_hashsum) > 0) {
					if ($this->getHashSum($full_path) !== $saved_hashsum) return false;
				} else {
					if ($echo) echo $mini_path . ' : no-hashsum' . PHP_EOL;
				}
			}
		}
		return true;
	}
}

// $general = new general('sha256');
// $general->set_scan_folder(__DIR__.'/project');
// $general->set_hash_folder(__DIR__.'/hashsum');
// // $general->gen_hashsum();
// $general->full_match();