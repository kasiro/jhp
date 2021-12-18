<?php declare(strict_types=1);

class jModule {
	protected $settings = [];
	protected $name;
	public $regList = [];

	// function __construct(string $name){
	// 	$this->name = $name;
	// }

	public function setName(string $name){
		$newName = $this->PrepareName($name);
		$this->name = $newName;
	}

	public function PrepareName(string $path){
		return explode('.', basename($path))[0];
	}

	public function getName(){
		return $this->name;
	}

	public function setSettings(array $settings){
		$this->settings = $settings;
	}

	public function getSettings(){
		return $this->settings;
	}

	public function action($function){
		return $function();
	}

	public function addreg(string $reg, string|callable $action){
		if (!array_key_exists($reg, $this->regList)) {
			$this->regList[$reg] = $action;
		} else {
			throw new Exception('regexp is already in $regList');
		}
	}
}