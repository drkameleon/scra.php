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
  * @file helpers.php
  */

class Arr
{
	function sanitize(&$arr)
	{
		if ((is_array($arr))&&(count($arr)==0)) $arr=NULL;
		if ((is_array($arr))&&(count($arr)==1)) $arr=array_values($arr)[0];

		if (is_array($arr))
			foreach ($arr as &$item)
				self::sanitize($item);
	}
}

class XPath
{
	function getByPattern($_pattern, $_source)
	{	
	    $dom = new DOMDocument();
	    @$dom->loadHTML($_source);

	    $xpath = new DOMXPath($dom);
	    $_results = $xpath->evaluate($_pattern);

	    $response = array();
	    foreach ($_results as $_result) 
	    { 
	    	$response[] = $_result->textContent; 
	    }

	    return $response;
	}
}

class String
{
	function startsWith($str,$sub) { return (substr($str,0,strlen($sub)) == $sub); }
	function endsWith($str,$sub) { return (substr($str,strlen($str)-strlen($sub)) == $sub); }
	function contains($str,$sub, $casesensitive = false)
	{
        if ($casesensitive) return (strstr($str, $sub) == false) ? false : true;
        else return (stristr($str, $sub) == false) ? false : true;
	}

	function inArray($str,$arr)
	{
		foreach ($arr as $item) if ($str==$item) return true;
		return false;
	}
}

class Url
{
	function baseUrl($url) 
	{
		$parts = parse_url($url);
		return $parts['scheme']."://".$parts['host'];
	}

	function isValid($url)
	{
		return !(filter_var($url, FILTER_VALIDATE_URL) === FALSE);
	}
}

class CSV
{
	function output($array) { $fp = fopen('php://output', 'w'); if (!is_array($array)) $array=array($array); foreach ($array as &$item) { if (is_array($item)) $item = implode(", ",$item); }  fputcsv($fp, $array); fclose($fp); }
	function getString($array) { ob_start(); self::output($array); return ob_get_clean(); }
}

class Json
{
	function getString($array) { return json_encode($array); }
}

class File
{
	function exists($filename) { return file_exists($filename); }
	function saveUrl($url,$as=NULL)
	{
    	$path = ($as==NULL) ? basename($url) : $as;
 
    	$ch = curl_init($url);
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    	$data = curl_exec($ch);
 
    	curl_close($ch);
 
    	file_put_contents($path, $data);

    	return $path;
	}
}

class Error
{
	function halt($error) { 
		die("ERROR : $error\n\n"); 
	}
}

?>