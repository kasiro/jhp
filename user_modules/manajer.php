<?php

class terminal_manager {

	public array $command_list = [];
	public array $on_list = [];
	public array $descriptions = [];
	public array $data = [];
	public array $args = [];

	function __construct($name, $args, $create_json_file = true){
		unset($args[0]);
		$this->name = $name;
		$this->args = $args;
		$this->create_json_file = $create_json_file;
	}

	public function registerMethod($name, $callback){
		$this->$name = $callback;
	}

	public function get_json(){
		$this->json_file = __DIR__."/json/{$this->name}_modules.json";
		if (file_exists($this->json_file)){
			return json_decode(file_get_contents($this->json_file), true);
		}
		if ($this->create_json_file){
			file_put_contents($this->json_file, '[]');
		}
		return [];
	}

	public function command(string|array $command_name, callable $func){
		$first = @$this->args[1] ?? '';
		$my_args = [];
		for ($i = 2; $i <= count($this->args); $i++) { 
			$my_args[] = $this->args[$i];
		}
		$command = $this->name.' '.implode(' ', $this->args);
		$this->manager_json = $this->get_json();
		$module_name = '';
		for ($i = 2; $i <= count($this->args); $i++){
			if (!str_starts_with($this->args[$i], '-')){
				$module_name = $this->args[$i];
				break;
			}
		}
		$FuncArgs = [$module_name, $this->manager_json, $this->json_file, $my_args];
		if (is_string($command_name)){
			if ($first == $command_name){
				$func(...$FuncArgs);
			}
		} elseif (is_array($command_name)) {
			if (in_array($first, $command_name)){
				$func(...$FuncArgs);
			} else {
				$command_name = @end($command_name);
			}
		}
		if (!in_array($command_name, $this->command_list))
			$this->command_list[] = $command_name;
	}

	public function __call($name, $args){
		$func = $this->data[$name.'_var'];
		return $func(...$args);
	}

	public function __get($name){
		if (isset($this->data[$name.'_var'])){
			return $this->data[$name.'_var'];
		}
		return false;
	}

	public function __set($name, $value){
		$this->data[$name.'_var'] = $value;
	}

	public function setDescription($command_name, $desc){
		$all_list = array_merge($this->command_list, $this->on_list);
		if (in_array($command_name, $all_list)){
			if (!array_key_exists($command_name, $this->descriptions)){
				$this->descriptions[$command_name] = $desc;
			}
		}
	}

	public function getDescription($command_name){
		if (array_key_exists($command_name, $this->descriptions)){
			return $this->descriptions[$command_name];
		}
	}

	public function on(string|array $command_name, callable $func){
		$first = @$this->args[1] ?? '';
		$my_args = [];
		for ($i = 2; $i < count($this->args); $i++) { 
			$my_args[] = $this->args[$i];
		}
		$command = $this->name.' '.implode(' ', $this->args);
		$this->manager_json = $this->get_json();
		$module_name = '';
		for ($i = 2; $i <= count($this->args); $i++){
			if (!str_starts_with($this->args[$i], '-')){
				$module_name = $this->args[$i];
				break;
			}
		}
		if (in_array('-g', $this->args)){
			$command = 'sudo '.$command;
		}
		$FuncArgs = [$module_name, $this->manager_json, $this->json_file, $my_args];
		if (is_string($command_name)){
			if ($first == $command_name){
				$res = $func(...$FuncArgs);
				if ($res){
					// echo $command . PHP_EOL;
					system($command);
				}
			}
		} elseif (is_array($command_name)) {
			if (in_array($first, $command_name)){
				$res = $func(...$FuncArgs);
				if ($res){
					// echo $command . PHP_EOL;
					system($command);
				} 
			}
		}
		if (!in_array($command_name, $this->on_list) && !is_array($command_name)){
			$this->on_list[] = $command_name;
		} else {
			$this->on_list = array_merge($this->on_list, $command_name);
		}
	}

	public function other(callable $func){
		$command_name = @$this->args[1] ?? '';
		$command = $this->name.' '.implode(' ', $this->args);
		if (!in_array($command_name, $this->on_list) && !in_array($command_name, $this->command_list)){
			$res = $func($this->name, $this->args);
		}
	}
}