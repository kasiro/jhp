<?php


class Request {
	public static function get($url, $post = [], $data = false) {
		$headers[] = 'Mozilla/5.0 (Windows NT 6.2; WOW64; rv:17.0) Gecko/20100101 Firefox/17.0';
		$headers[] = 'Accept: application/json, text/javascript, */*; q=0.01';
		$headers[] = 'Accept-Language: ru,en;q=0.5';
		$headers[] = 'Connection: keep-alive';
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_VERBOSE, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		if(count($post) > 0) {
			curl_setopt($ch, CURLOPT_POST, TRUE);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		}
		curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookie.txt');
		curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie.txt');
		$data = curl_exec($ch);
		return $data;
	}
}