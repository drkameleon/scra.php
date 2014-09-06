<?php
/**
  * Scra.PHP
  *
  * The Ultimate customiseable YAML-ised
  * Web Scraper for PHP.
  *
  * @package Scra.PHP
  * @version 0.4
  * @author Dr.Kameleon <drkameleon@gmail.com>
  * @copyright 2013-2014 InSili.co
  * @license http://opensource.org/licenses/lgpl-3.0.html GNU LGPL 3.0
  *
  * @file filters.php
  */

class Filters
{
	//---------------------
	// ACTIONS
	//---------------------

	public static function prepend($str,$what) { return $what.$str; }
	public static function append($str,$what) { return $str.$what; }
	public static function trim($str,$what=" \t\n\r\0\x0B") { return trim($str,$what); }
	public static function ltrim($str,$what=" \t\n\r\0\x0B") { return ltrim($str,$what); }
	public static function rtrim($str,$what=" \t\n\r\0\x0B") { return rtrim($str,$what); }
	public static function sha1($str) { return sha1($str); }
	public static function md5($str) { return md5($str); }
	public static function lcase($str) { return strtolower($str); }
	public static function ucase($str) { return strtoupper($str); }
	public static function lcaseFirst($str) { return lcfirst($str); }
	public static function ucaseFirst($str) { return ucfirst($str); }
	public static function ucaseWords($str) { return ucwords($str); }
	public static function stripTags($str) { return strip_tags($str); }
	public static function replace($str,$what,$with) { return str_replace($what, $with, $str); }
	public static function reverse($str) { return strrev($str); }
	public static function decodeHtml($str) { return html_entity_decode($str); }
	public static function substr($str,$from,$length) { return substr($str, $from, $length); }
	public static function wordwrap($str,$break,$length) { return wordwrap($str,$length,$break); }
	public static function cut($str,$length) { return substr($str,0,$length); }

	//---------------------
	// FILTERS
	//---------------------

	public static function beginsWith($str,$what) { return (substr($str,0,strlen($what)) == $what) ? $str : false; }
	public static function endsWith($str,$what) { return (substr($str,strlen($str)-strlen($what)) == $what) ? $str : false; }
	public static function contains($str,$what) { return (stristr($str, $what) == false) ? false : $str; }

	//-----------------------------------------------------------------
	// An ultra-crazy workaround for 'negative' method calls
	//
	// Foreach bool-type filter function, e.g. "beginsWith"
	// we're auto-creating its inverse function ("!beginsWith"), 
	// and saving space
	//
	// Why? Coz' we can... lol!
	//-----------------------------------------------------------------

	public static function __callStatic($name, $arguments)
    {
    	echo "Called method : $name with arguments ".implode(",", $arguments);
    	if (self::beginsWith($name,"!")==$name)
    	{
    		$func_name = str_replace("!", "", $name);
    		$str = $arguments[0];
    		return (!call_user_func_array(array("Filters",$func_name), $arguments)) ? $str : false;
    	}
    }
}

?>