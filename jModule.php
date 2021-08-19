<?php declare(strict_types=1);

class jModule {
	protected $settings = [];
	protected $name;
	public $regList = [];

	// function __construct(string $name){
	// 	$this->name = $name;
	// }

	public function setName(string $name){
		$this->name = $name;
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

	public function addreg(string $reg, string|callable $action){
		if (!array_key_exists($reg, $this->regList)) {
			$this->regList[$reg] = $action;
		} else {
			throw new Exception('regexp is aelredy in $regList');
		}
	}
}