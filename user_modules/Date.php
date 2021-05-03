<?php

class DateMain {
	
	/**
	 * return date with format day.month.year
	 *
	 * @return string
	 **/
	public static function get_date(){
		date_default_timezone_set('Etc/GMT-7');
		$date = date('d.m.Y');
		return $date;
	}

	/**
	 * return time with format Hours:minutes
	 *
	 * @return string
	 **/
	public static function get_time(){
		date_default_timezone_set('Etc/GMT-7');
		$time = date('G:i');
		return $time;
	}

	/**
	 * return time with format Hours:minutes:seconds
	 *
	 * @return string
	 **/
	public static function get_time_s(){
		date_default_timezone_set('Etc/GMT-7');
		$time = date('G:i:s');
		return $time;
	}
}