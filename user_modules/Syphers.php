<?php

/**
 * FULL method enrypt
 * метод шифрования всех преведущих
 */
class CODER_PASSWORD
{
	/**
	 * @var int
	 */
	public static $offset = 27;

	public static function Full_encrypt($text, $kode, $debug = false) {
		if ($debug === false){
			$enc_Aes = CODER_Aes256cbc::encrypt($text, $kode);
			$enc_VCoder = CODER_VCoder::vizhener_encode($enc_Aes, $kode);
			$enc_caesar = CODER_caesar::caesarEncode($enc_VCoder, CODER_PASSWORD::$offset);
			return $enc_caesar;
		} else {
			$enc_Aes = CODER_Aes256cbc::encrypt($text, $kode);
			echo 'Aes: ' . $enc_Aes . "\n";
			$enc_VCoder = CODER_VCoder::vizhener_encode($enc_Aes, $kode);
			echo 'Viz: ' . $enc_VCoder . "\n";
			$enc_caesar = CODER_caesar::caesarEncode($enc_VCoder, CODER_PASSWORD::$offset);
			echo 'Cez: ' . $enc_caesar . "\n";
			return $enc_caesar;
		}
	}

	public static function Full_decrypt($text, $kode, $debug = false) {
		if ($debug === false) {
			$dec_caesar = CODER_caesar::caesarDecode($text, CODER_PASSWORD::$offset);
			$dec_VCoder = CODER_VCoder::vizhener_decode($dec_caesar, $kode);
			$dec_Aes = CODER_Aes256cbc::decrypt($dec_VCoder, $kode);
			return $dec_Aes;
		} else {
			$dec_caesar = CODER_caesar::caesarDecode($text, CODER_PASSWORD::$offset);
			echo 'Cez: ' . $dec_caesar . "\n";
			$dec_VCoder = CODER_VCoder::vizhener_decode($dec_caesar, $kode);
			echo 'Viz: ' . $dec_VCoder . "\n";
			$dec_Aes = CODER_Aes256cbc::decrypt($dec_VCoder, $kode);
			echo 'Aez: ' . $dec_Aes . "\n";
			return $dec_Aes;
		}
	}
}

class CODER_keyboard
{
	public static $alphs = [
		' qwertyuiop[QWERTYUIOP{]' => 7,
		'\asdfghjklASDFGHJKL' => 8,
		']zxcvbnm,ZXCVBNM<' => 3
	];

	public static function encrypt($text)
	{
		foreach (CODER_keyboard::$alphs as $alph => $offset) { 
			CODER_caesar::$abc = $alph;
			$calph = CODER_caesar::caesarEncode(CODER_caesar::$abc, $offset);
			$text = strtr($text, CODER_caesar::$abc, $calph);
		}
		return $text;
	}

	public static function decrypt($text)
	{
		foreach (CODER_keyboard::$alphs as $alph => $offset) {
			CODER_caesar::$abc = $alph;
			$calph = CODER_caesar::caesarDecode(CODER_caesar::$abc, $offset);
			$text = strtr($text, CODER_caesar::$abc, $calph);
		}
		return $text;
	}
}

class CODER_MY {

	/**
	 * @var int
	 */
	public static $offset = 4;

	public static function my_enc($text, $kode)
	{
		CODER_VCoder::$abc = ' 0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ,.:\'"()\\/';
		$enc_VCoder = CODER_VCoder::vizhener_encode($text, $kode);
		$enc_base64 = base64_encode($enc_VCoder);
		$end_enc = CODER_caesar::caesarEncode($enc_base64, CODER_MY::$offset);
		return $end_enc;
	}

	public static function my_dec($text, $kode)
	{
		CODER_VCoder::$abc = ' 0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ,.:\'"()\\/';
		$dec = CODER_caesar::caesarDecode($text, CODER_MY::$offset);
		$dec_base64 = base64_decode($dec);
		$dec_end = CODER_VCoder::vizhener_decode($dec_base64, $kode);
		return $dec_end;
	}
}

/**
 * caesar method enrypt
 * метод шифрования Цезаря
 */
class CODER_caesar
{
	/**
	 * @var string
	 */
	public static $abc = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

	public static function caesarEncode($message, $key) {
		$from = static::$abc;
		$to   = substr($from, $key) . substr($from, 0, $key);
		return strtr($message, $from, $to);
	}

	public static function caesarDecode($message, $key) {
		$key = -($key);
		$from = static::$abc;
		$to   = substr($from, $key) . substr($from, 0, $key);
		return strtr($message, $from, $to);
	}

	public static function getOffset($offset) {
		$key = $offset;
		$from = static::$abc;
		$to   = substr($from, $key) . substr($from, 0, $key);
		return $to;
	}
}

/**
 * Substitution method enrypt
 * Substitution
 */
class CODER_Subs
{
	/**
	 * @var string
	 */
	public static $abc = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	
	/**
	 * @var string
	 */
	public static $sabc = '';

	public static function SubstitutionEncode($message) {
		return strtr($message, CODER_Subs::$abc, CODER_Subs::$sabc);
	}

	public static function SubstitutionDecode($message) {
		return strtr($message, CODER_Subs::$sabc, CODER_Subs::$abc);
	}

}

/**
 * caesar method enrypt
 * метод шифрования Цезаря
 */
class CODER_caesar_FULL
{
	/**
	 * @var string
	 */
	public static $abc 		 = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	
	/**
	 * @var array
	 */
	public static $offsets   = [5, 7, -10, 18, -13, -16, -11];

	public static function caesarEnc($message) {
		$text = $message;
		for ($i = 0; $i < count(CODER_caesar_FULL::$offsets); $i++) { 
			$text = CODER_caesar_FULL::caesarEncode($text, CODER_caesar_FULL::$offsets[$i]);
		}
		return $text;
	}

	public static function caesarDec($message) {
		$text = $message;
		for ($i = 0; $i < count(CODER_caesar_FULL::$offsets); $i++) { 
			$text = CODER_caesar_FULL::caesarDecode($text, CODER_caesar_FULL::$offsets[$i]);
		}
		return $text;
	}

	public static function caesarEncode($message, $key) {
		$from = CODER_caesar_FULL::$abc;
		$to   = substr($from, $key) . substr($from, 0, $key);
		return strtr($message, $from, $to);
	}

	public static function caesarDecode($message, $key) {
		$from = CODER_caesar_FULL::$abc;
		$to   = substr($from, $key) . substr($from, 0, $key);
		return strtr($message, $from, $to);
	}
}

class CODER_Number_FULL {
	
	public static $abc = '';
	public static $abcNum = '';
	private static $offset = 5;

	public static function NumEncode($text){
		CODER_Number_Shyper::$abc = CODER_Number_FULL::$abc;
		CODER_caesar::$abc = CODER_Number_FULL::$abcNum;
		return CODER_caesar::caesarEncode(CODER_Number_Shyper::NumEncode($text), CODER_Number_FULL::$offset);
	}

	public static function NumDecode($enctext){
		CODER_Number_Shyper::$abc = CODER_Number_FULL::$abc;
		CODER_caesar::$abc = CODER_Number_FULL::$abcNum;
		return CODER_Number_Shyper::NumDecode(CODER_caesar::caesarDecode($enctext, CODER_Number_FULL::$offset));
	}
	
}

class CODER_Number_Shyper {

	public static $abc = ' 0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

	public static function NumEncode($text){
		$alf = CODER_Number_Shyper::$abc;
		$text_arr = [];
		for ($i = 0; $i < strlen($text); $i++) { 
			$text_arr[] = strpos($alf, $text[$i]);
		}
		return implode(' ', $text_arr);
	}

	public static function NumDecode($enctext){
		$alf = CODER_Number_Shyper::$abc;
		$text_arr = [];
		$arr = explode(' ', $enctext);
		for ($i = 0; $i < count($arr); $i++) { 
			$text_arr[] = $alf[$arr[$i]];
		}
		return implode('', $text_arr);
	}
}

/**
 * caesar method enrypt
 * метод шифрования Цезаря
 */
class CODER_caesar_BFULL
{
	/**
	 * @var string
	 */
	public static $abc 		 = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

	/**
	 * @var array
	 */
	public static $offsets   = [5, 7, -10, 18, -13, -16, -11];

	public static function caesarEnc($message) {
		$text = base64_encode($message);
		for ($i = 0; $i < count(CODER_caesar_FULL::$offsets); $i++) { 
			$text = CODER_caesar_FULL::caesarEncode($text, CODER_caesar_FULL::$offsets[$i]);
		}
		return $text;
	}

	public static function caesarDec($message) {
		$text = $message;
		for ($i = 0; $i < count(CODER_caesar_FULL::$offsets); $i++) { 
			$text = CODER_caesar_FULL::caesarDecode($text, CODER_caesar_FULL::$offsets[$i]);
		}
		return base64_decode($text);
	}

	public static function caesarEncode($message, $key) {
		$from = CODER_caesar_FULL::$abc;
		$to   = substr($from, $key) . substr($from, 0, $key);
		return strtr($message, $from, $to);
	}

	public static function caesarDecode($message, $key) {
		$from = CODER_caesar_FULL::$abc;
		$to   = substr($from, $key) . substr($from, 0, $key);
		return strtr($message, $from, $to);
	}
}

/**
 * caesar method enrypt
 * метод шифрования Цезаря
 */
class CODER_my_caesar
{
	/**
	 * @var string
	 */
	public static $abc = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

	public static function caesarEncode($message, $key) {
		$from = CODER_my_caesar::$abc;
		$to   = substr($from, $key) . substr($from, 0, $key);
		return CODER_my_caesar::strtr_main($message, $from, $to);
	}

	public static function caesarDecode($message, $key) {
		$key = -($key);
		$from = CODER_my_caesar::$abc;
		$to   = substr($from, $key) . substr($from, 0, $key);
		return CODER_my_caesar::strtr_main($message, $from, $to);
	}

	public static function strtr_main($text, $from = '', $to = ''){
		if (is_array($from)){
			$newText = '';
			$zam = [];
			$text_arr = [];
			foreach ($from as $what => $to) {
				$zam[$what] = $to;
			}
			for ($i = 0; $i < strlen($text); $i++) { 
				$text_arr[] = $text[$i];
			}
	        foreach ($text_arr as $char) { 
				if (isset($zam[$char])){
					$newText .= $zam[$char];
				} else {
					$newText .= $char;
				}
			}
			return $newText;
		} elseif (is_string($from) && is_string($to)) {
			$newText = '';
			$zam = [];
			$text_arr = [];
			if (strlen($from) !== strlen($to)) return false;
			for ($i = 0; $i < strlen($from); $i++) {
				$zam[$from[$i]] = $to[$i];
			}
			for ($i = 0; $i < strlen($text); $i++) { 
				$text_arr[] = $text[$i];
			}
	        foreach ($text_arr as $char) { 
				if (isset($zam[$char])){
					$newText .= $zam[$char];
				} else {
					$newText .= $char;
				}
			}
			return $newText;
		}
	}
}

/**
 * Aes256cbc method enrypt
 * метод шифрования Aes256cbc
 */
class CODER_Aes256cbc
{
	
	public static function encrypt($payload, $key) {
		$iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
		$encrypted = openssl_encrypt($payload, 'aes-256-cbc', $key, 0, $iv);
		return base64_encode($encrypted . '::' . $iv);
	}

	public static function decrypt($garble, $key) {
		list($encrypted_data, $iv) = explode('::', base64_decode($garble), 2);
		return openssl_decrypt($encrypted_data, 'aes-256-cbc', $key, 0, $iv);
	}

}

class CODER_Private_cipher
{
	public static $alphs = [
		[
			'alph'   => 'abcdefghijklmnopqrstuvwxyz',
			'offset' => 8
		],
		[
			'alph'   => 'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
			'offset' => 6
		],
		[
			'alph'   => '0123456789',
			'offset' => 4
		]
	];

	public static function encrypt($text)
	{
		foreach (CODER_Private_cipher::$alphs as $data_alph) { 
			CODER_caesar::$abc = $data_alph['alph'];
			$calph = CODER_caesar::caesarEncode(CODER_caesar::$abc, $data_alph['offset']);
			$text = strtr($text, CODER_caesar::$abc, $calph);
		}
		return $text;
	}

	public static function decrypt($text)
	{
		foreach (CODER_Private_cipher::$alphs as $data_alph) {
			CODER_caesar::$abc = $data_alph['alph'];
			$calph = CODER_caesar::caesarDecode(CODER_caesar::$abc, $data_alph['offset']);
			$text = strtr($text, CODER_caesar::$abc, $calph);
		}
		return $text;
	}
}

class CODER_methods {
	
	public static function shuff($str, $c, $b = false)
	{
		if ($b !== false) {
			if (strlen($str) % 2 == 0) {
				$s = $str;
				for ($i = 0; $i < strlen($str); $i += $c) {
					if ($i < strlen($s)) {
						if (!in_array($s[$i], $b) && !in_array($s[$i+1], $b)) {
							list($s[$i], $s[$i+1]) = [$s[$i+1], $s[$i]];
						}
					}
				}
				return $s;
			} else {
				$s = $str;
				for ($i = 0; $i < strlen($str); $i += $c) {
					if ($i != strlen($s)-1) {
						if (!in_array($s[$i], $b) && !in_array($s[$i+1], $b)) {
							list($s[$i], $s[$i+1]) = [$s[$i+1], $s[$i]];
						}
					}
				}
				return $s;
			}
		} else {
			if (strlen($str) % 2 == 0) {
				$s = $str;
				for ($i = 0; $i < strlen($str); $i += $c) {
					if ($i < strlen($s)) {
						list($s[$i], $s[$i+1]) = [$s[$i+1], $s[$i]];
					}
				}
				return $s;
			} else {
				$s = $str;
				for ($i = 0; $i < strlen($str); $i += $c) {
					if ($i != strlen($s)-1) {
						list($s[$i], $s[$i+1]) = [$s[$i+1], $s[$i]];
					}
				}
				return $s;
			}
		}
	}
}

class CODER_ABC {
	
	/**
	 * @var string
	 */
	public static $standart     = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

	/**
	 * @var string
	 */
	public static $standart_num = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	
	/**
	 * @var string
	 */
	public static $standart_viz = ' 0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	
	/**
	 * @var string
	 */
	public static $base64 	    = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ+=\\/';
	
	/**
	 * @var string
	 */
	public static $stext 	    = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ+=\\/.,!?@:';
}

class CODER_twitter {
	public static $abc = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ+=\\/';
	public static $offset_one = 16;
	public static $offset_two = 11;
	public static $offset_tree = 8;
	public static $offset_four = 5;
	public static $offset_five = 4;

	public static function encrypt($text) {
		CODER_caesar::$abc = CODER_twitter::$abc;
		$enc_base64 = base64_encode($text);
		$adb = CODER_caesar::caesarEncode($enc_base64, CODER_twitter::$offset_one);
		$cez = CODER_caesar::caesarEncode($adb, CODER_twitter::$offset_two);
		CODER_Private_cipher::$alphs[0]['offset'] = 8;
		CODER_Private_cipher::$alphs[1]['offset'] = 5;
		CODER_Private_cipher::$alphs[2]['offset'] = 4;
		$enc_four = CODER_Private_cipher::encrypt($cez);
		return $enc_four;
	}

	public static function decrypt($text) {
		CODER_Private_cipher::$alphs[0]['offset'] = 8;
		CODER_Private_cipher::$alphs[1]['offset'] = 5;
		CODER_Private_cipher::$alphs[2]['offset'] = 4;
		$f = CODER_Private_cipher::decrypt($text);
		CODER_caesar::$abc = CODER_twitter::$abc;
		$adb = CODER_caesar::caesarDecode($f, CODER_twitter::$offset_two);
		$cez = CODER_caesar::caesarDecode($adb, CODER_twitter::$offset_one);
		$dec_base64 = base64_decode($cez);
		return $dec_base64;
	}
}

class CODER_miner {
	public static $abc = '';
}

/**
 * VCoder method enrypt
 * метод шифрования Виженера
 */
class CODER_VCoder {

	/**
	 * @var string
	 */
	public static $abc = ' 0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

	public static function vizhener_encode($text, $kod) {
        $abc = CODER_VCoder::$abc;
        $mode = 'encrypt';
        $maxlength = max(strlen($text), strlen($kod));
        $r = '';
        for ($i = 0; $i < $maxlength; $i++) {
            $mi = strpos($abc, $text[ ( ($i >= strlen($text)) ? $i % strlen($text) : $i ) ] );
            $ki_s = $kod[$i >= strlen($kod) ? $i % strlen($kod) : $i];
            $ki = $mode !== null && strpos($mode, 'gronsfeld') !== false ? is_int($ki_s) : strpos($abc, $ki_s);
            $ki = $mode !== null && strpos($mode, 'decrypt') !== false ? (-$ki) : $ki;
            $c  = $abc[ ( ( ( strlen($abc) + ( $mi + $ki ) ) % strlen($abc) ) ) ];
            $c  = $mode === 'shifted_atbash' ? $abc[strlen($abc) - 1 - strpos($abc, $c)] : $c;
            $r .= $c;
        }
        return $r;
    }

    public static function vizhener_decode($text, $kod) {
        $abc = CODER_VCoder::$abc;
        $mode = 'decrypt';
        $maxlength = max(strlen($text), strlen($kod));
        $r = '';
        for ($i = 0; $i < $maxlength; $i++) { 
            $mi = strpos($abc, $text[ ( ($i >= strlen($text)) ? $i % strlen($text) : $i ) ] );
            $ki_s = $kod[$i >= strlen($kod) ? $i % strlen($kod) : $i];
            $ki = $mode !== null && strpos($mode, 'gronsfeld') !== false ? is_int($ki_s) : strpos($abc, $ki_s);
            $ki = $mode !== null && strpos($mode, 'decrypt') !== false ? (-$ki) : $ki;
            $c  = $abc[ ( ( ( strlen($abc) + ( $mi + $ki ) ) % strlen($abc) ) ) ];
            $c  = $mode === 'shifted_atbash' ? $abc[strlen($abc) - 1 - strpos($abc, $c)] : $c;
            $r .= $c;
        }
        return $r;
	}
	
	public static function vizhener_adb_encode($text, $kod) {
        $abc = CODER_VCoder::$abc;
        $mode = 'shifted_atbash';
        $maxlength = max(strlen($text), strlen($kod));
        $r = '';
        for ($i = 0; $i < $maxlength; $i++) { 
            $mi = strpos($abc, $text[ ( ($i >= strlen($text)) ? $i % strlen($text) : $i ) ] );
            $ki_s = $kod[$i >= strlen($kod) ? $i % strlen($kod) : $i];
            $ki = $mode !== null && strpos($mode, 'gronsfeld') !== false ? is_int($ki_s) : strpos($abc, $ki_s);
            $ki = $mode !== null && strpos($mode, 'decrypt') !== false ? (-$ki) : $ki;
            $c  = $abc[ ( ( ( strlen($abc) + ( $mi + $ki ) ) % strlen($abc) ) ) ];
            $c  = $mode === 'shifted_atbash' ? $abc[strlen($abc) - 1 - strpos($abc, $c)] : $c;
            $r .= $c;
        }
        return $r;
    }

    public static function vizhener_adb_decode($text, $kod) {
        $abc = CODER_VCoder::$abc;
        $mode = 'shifted_atbash';
        $maxlength = max(strlen($text), strlen($kod));
        $r = '';
        for ($i = 0; $i < $maxlength; $i++) { 
            $mi = strpos($abc, $text[ ( ($i >= strlen($text)) ? $i % strlen($text) : $i ) ] );
            $ki_s = $kod[$i >= strlen($kod) ? $i % strlen($kod) : $i];
            $ki = $mode !== null && strpos($mode, 'gronsfeld') !== false ? is_int($ki_s) : strpos($abc, $ki_s);
            $ki = $mode !== null && strpos($mode, 'decrypt') !== false ? (-$ki) : $ki;
            $c  = $abc[ ( ( ( strlen($abc) + ( $mi + $ki ) ) % strlen($abc) ) ) ];
            $c  = $mode === 'shifted_atbash' ? $abc[strlen($abc) - 1 - strpos($abc, $c)] : $c;
            $r .= $c;
        }
        return $r;
    }

}