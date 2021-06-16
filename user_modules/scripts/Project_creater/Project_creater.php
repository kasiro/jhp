<?php

if (!function_exists('is_list')) {
	function is_list($arr){
		return array_values($arr) === $arr;
	}
}

class Project_creater 
{
    public $separator = '/';
    function __construct($data, array $tree) {
		$this->separator = $data['sep'];
		$bool = $data['in_exist_folder'];
		$path = $data['folder'];
        if (!file_exists($path)) {
            @mkdir($path);
            if (file_exists($path)) {
                $this->tree_execute($path, $tree);
            } else {
                $this->r_mkdir($path);
                if (file_exists($path)) {
                    $this->tree_execute($path, $tree);
                }
            }
        } else {
            if ($bool == true) {
                if (glob($path . '/*')) {
                    $this->tree_execute($path, $tree);
                }
            } else {
                if (!glob($path . '/*')) {
                    $this->tree_execute($path, $tree);
                } else {
                    echo 'path:', "\n";
					echo '   ' . $path, "\n";
					echo 'not Cleared!', "\n";
                }
            }
        }
    }
    
    public function r_mkdir($path)
    {
        if ($this->separator == '/') {
            $arr = explode('/', $path);
            if (in_array('..', $arr)) {
                $string = '../';
                for ($i = 1; $i < count($arr); $i++) { 
                    mkdir($string . $arr[$i]);
                    $string .= $arr[$i];
                    if ($i != (count($arr) - 1)) {
                        $string .= '/';
                    }
                }
            }
            if (in_array('.', $arr)) {
                $string = './';
                for ($i = 1; $i < count($arr); $i++) { 
                    mkdir($string . $arr[$i]);
                    $string .= $arr[$i];
                    if ($i != (count($arr) - 1)) {
                        $string .= '/';
                    }
                }
            }
        } else {
            $arr = explode('\\', $path);
        }
    }

    public function tree_execute($start_path, $tree)
    {
        $file_reg = '/.+\..+/';
        if (!is_list($tree)) {
            foreach ($tree as $key => $value) {
                if (is_array($value)) {
                    mkdir($start_path . $this->separator . $key);
                    $this->tree_execute($start_path . $this->separator . $key, $value);
                } else {
                    if (is_string($value)) {
                        if (str_contains($value, '.')) {
                            if ($value == 'file') {
                                file_put_contents($start_path . $this->separator . $key, '');
                            } elseif ($value == 'folder') {
                                mkdir($start_path . $this->separator . $key);
                            } elseif (str_contains($value, 'Save ')) {
                                $text = str_replace('Save ', '', $value);
                                file_put_contents($start_path . $this->separator . $key, $text);
                            } else {
                                if (preg_match($file_reg, $value)) {
                                    file_put_contents($start_path . $this->separator . $value, '');
                                } else {
                                    mkdir($start_path . $this->separator . $value);
                                }
                            }
                        } else {
                            if ($value == 'file') {
                                file_put_contents($start_path . $this->separator . $key, '');
                            } elseif ($value == 'folder') {
                                mkdir($start_path . $this->separator . $key);
                            } elseif (str_contains($value, 'Save ')) {
                                $text = str_replace('Save ', '', $value);
                                file_put_contents($start_path . $this->separator . $key, $text);
                            } else {
                                mkdir($start_path . $this->separator . $value);
                            }
                        }
                    }
                }
            }
        } else {
            foreach ($tree as $value) {
                if (is_array($value)) {
                    mkdir($start_path . $this->separator . $value);
                    $this->tree_execute($start_path . $this->separator . $value, $tree);
                } else {
                    if (is_string($value)) {
                        if (str_contains($value, '.')) {
                            if (preg_match($file_reg, $value)) {
                                file_put_contents($start_path . $this->separator . $value, '');
                            } else {
                                mkdir($start_path . $this->separator . $value);
                            }
                        } else {
                            mkdir($start_path . $this->separator . $value);
                        }
                    }
                }
            }
        }
    }

}
