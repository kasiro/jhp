<?php

class TagReader {
	
	public function __construct($xml){
		$this->xml = $xml;
	}

	public function findTag($name, $text = null){
		if ($text == null){
			$find = false;
			preg_replace_callback('/<(\w+)>(.*)<\/\w+>/ms', function ($matches) use ($name, &$find) {
				$tagName = $matches[1];
				if ($tagName == $name){
					$find = $matches[2];
				}
				return $matches[0];
			}, $this->xml);
			if (!$find){
				preg_replace_callback('/<(\w+)>(.*)<\/\w+>/m', function ($matches) use ($name, &$find) {
					$tagName = $matches[1];
					if ($tagName == $name){
						$find = $matches[2];
					}
					return $matches[0];
				}, $this->xml);
			}
		} else {
			$find = false;
			preg_replace_callback('/<(\w+)>(.*)<\/\w+>/ms', function ($matches) use ($name, &$find) {
				$tagName = $matches[1];
				if ($tagName == $name){
					$find = $matches[2];
				}
				return $matches[0];
			}, $text);
			if (!$find){
				preg_replace_callback('/<(\w+)>(.*)<\/\w+>/m', function ($matches) use ($name, &$find) {
					$tagName = $matches[1];
					if ($tagName == $name){
						$find = $matches[2];
					}
					return $matches[0];
				}, $text);
			}
		}
		return $find;
		return false;
	}

	public function getTag($search, $text = null){
		if ($text == null){
			if (str_contains($search, '>')){
				$els = explode('>', $search);
				$lel = false;
				foreach ($els as $el){
					if (!$lel){
						$lel = $this->findTag($el);
					} else {
						$lel = $this->findTag($lel);
					}
				}
			} else {
				return $this->findTag($search);
			}
		} else {
			if (str_contains($search, '>')){
				$els = explode('>', $search);
				$lel = false;
				foreach ($els as $el){
					if (!$lel){
						$lel = $this->findTag($el, $text);
					} else {
						$lel = $this->findTag($lel, $text);
					}
				}
			} else {
				return $this->findTag($search, $text);
			}
		}
		
	}

	public function getTagAll($search, $text = null){
		if ($text == null){
			if (str_contains($search, '>')){

			} else {
				
			}
		} else {
			if (str_contains($search, '>')){

			} else {
				return $this->findTag($search);
			}
		}
	}

}