<?php

date_default_timezone_set('Etc/GMT-7');


class DateMain {
	
	/**
	 * return date with format day.month.year
	 *
	 * @return string
	 **/
	public static function get_date(){
		return date('d.m.Y');
	}

	/**
	 * return time with format Hours:minutes
	 *
	 * @return string
	 **/
	public static function get_time(){
		return date('G:i');
	}

	/**
	 * return time with format Hours:minutes:seconds
	 *
	 * @return string
	 **/
	public static function get_time_s(){
		return date('G:i:s');
	}
}