<?php

enum fcolors: string {
	case black = 'black';
	case dark_gray = 'dark_gray';
	case blue = 'blue';
	case light_blue = 'light_blue';
	case green = 'green';
	case light_green = 'light_green';
	case cyan = 'cyan';
	case light_cyan = 'light_cyan';
	case red = 'red';
	case light_red = 'light_red';
	case purple = 'purple';
	case light_purple = 'light_purple';
	case brown = 'brown';
	case yellow = 'yellow';
	case light_gray = 'light_gray';
	case white = 'white';
}

if (!class_exists('Colors')){
	class Colors {
		private static $foreground_colors = [];
		private static $background_colors = [];
	
		public static function SetUpColors(){
			// Set up shell colors
			static::$foreground_colors['black'] = '30';
			static::$foreground_colors['dark_gray'] = '1;30';
			static::$foreground_colors['blue'] = '34';
			static::$foreground_colors['light_blue'] = '1;34';
			static::$foreground_colors['green'] = '32';
			static::$foreground_colors['light_green'] = '1;32';
			static::$foreground_colors['cyan'] = '36';
			static::$foreground_colors['light_cyan'] = '1;36';
			static::$foreground_colors['red'] = '31';
			static::$foreground_colors['light_red'] = '1;31';
			static::$foreground_colors['purple'] = '35';
			static::$foreground_colors['light_purple'] = '1;35';
			static::$foreground_colors['brown'] = '33';
			static::$foreground_colors['yellow'] = '1;33';
			static::$foreground_colors['light_gray'] = '37';
			static::$foreground_colors['white'] = '1;37';
	
			static::$background_colors['black'] = '40';
			static::$background_colors['red'] = '41';
			static::$background_colors['green'] = '42';
			static::$background_colors['yellow'] = '43';
			static::$background_colors['blue'] = '44';
			static::$background_colors['magenta'] = '45';
			static::$background_colors['cyan'] = '46';
			static::$background_colors['light_gray'] = '47';
		}
	
		public static function colorize($str, $vars = [], $char = '%'){
			static::SetUpColors();
			if (is_array($vars) && !empty($vars)) {
				foreach ($vars as $k => $v) {
					$str = str_replace($char . $k, static::setColor($k, $v), $str);
				}
			}
			return $str;
		}
	
		public static function wrap($string, $options){
			static::SetUpColors();
			foreach ($options as $key => $value) {
				if ($key == 'vars') {
					foreach ($value as $what => $params) {
						$arr = explode($what, $string, 2);
						$string = static::setColor($arr[0], $ColorFg) . static::setColor($params['replace'], $params['colorVar']) . static::setColor($arr[1], $ColorFg);
					}
				} else {
					$$key = $value;
				}
			}
			echo $string . PHP_EOL;
		}

		// Returns colored string
		public static function setColor($string, string|fcolors $foreground_color = null, $background_color = null) {
			static::SetUpColors();
			$colored_string = "";
			if ($foreground_color instanceof fcolors){
				$foreground_color = $foreground_color->value;
			}
			// Check if given foreground color found
			if (isset(static::$foreground_colors[$foreground_color])) {
				$colored_string .= "\033[" . static::$foreground_colors[$foreground_color] . "m";
			}
			// Check if given background color found
			if (isset(static::$background_colors[$background_color])) {
				$colored_string .= "\033[" . static::$background_colors[$background_color] . "m";
			}
	
			// Add string and end coloring
			$colored_string .=  $string . "\033[0m";
	
			return $colored_string;
		}
	
		public static function showAllColors(array $colors) {
			static::SetUpColors();
			$foreground_colors = static::getForegroundColors();
			$background_colors = static::getBackgroundColors();
			foreach ($colors as $color){
				if ($colors === $foreground_colors){
					echo static::setColor('test String : '.$color, $color) . PHP_EOL;
				}
				if ($colors === $background_colors){
					echo static::setColor('test String', background_color: $color) . static::setColor(' : ' . $color, $color) . PHP_EOL;
				}
			}
		}

		// Returns all foreground color names
		public static function getForegroundColors() {
			return array_keys(static::$foreground_colors);
		}
	
		// Returns all background color names
		public static function getBackgroundColors() {
			return array_keys(static::$background_colors);
		}
	}
}