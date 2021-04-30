<?php

class jModule {
	public $settings = [];
	protected $name;
	public $regList = [];

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
		if (!isset($this->regList[$reg])) {
			$this->regList[$reg] = $action;
			return;
		}
		throw new Exception('regexp is aelredy in $regList');
	}
}