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
  * @file scraper.php
  */

//---------------------
// INCLUDES
//---------------------

// Libraries
require_once 'libraries/yaml/spyc.php';

// Core
require_once 'helpers.php';
require_once 'filters.php';

//---------------------
// FILTER
//---------------------

class Filter
{
	public $name;
	public $args;

	public function __construct($filter)
	{
		$parts = explode("(", $filter);
		$this->name = $parts[0];
		$this->args = array();

		if (count($parts)>1)
		{
			$args = trim($parts[1],")");
			$this->args = explode(",", $args);

			foreach ($this->args as &$arg)
			{
				$arg = trim($arg,"\"");
			}
		}
	}

	public function applyTo(&$arr)
	{
		foreach ($arr as $i=>&$item)
		{
			$ret = call_user_func_array(array("Filters",$this->name), array_merge(array($item),$this->args));

			if ($ret) $item = $ret; 
			else unset($arr[$i]); // Item was filtered
		}
	}

	public function dump()
	{
		return $this->name."(".implode(",", $this->args).")";
	}
}

//---------------------
// TOKEN
//---------------------

class Token
{
	public $name;
	public $pattern, $filters, $action;

	public function __construct($name,$token)
	{
		$this->name = $name;
		$this->filters = array();

		if (is_array($token)) // includes stuff other than just pattern
		{
			$content = current($token);
			$this->pattern = trim(key($token),"\"");


			foreach ($content as $value) // get filters & action (if exists)
			{
				if (String::startsWith($value,"@")) // that's an action
					$this->action = str_replace("@", "", $value);
				else // add it as a filter
					$this->filters[] = new Filter($value);
			}
		}
		else
		{
			$this->pattern = $token;
			$this->filters = array();
			$this->action  = NULL;
		}
	}

	public function scrapeFromSource($src,$scraper)
	{
		if (String::startsWith($this->pattern,"//")) // Is it an XPath
		{
			echo "XPath token : ".$this->pattern."\n";
			$results = XPath::getByPattern($this->pattern, $src);
		}
		else // treat it as a RegEx
		{
			echo "RegEx token : ".$this->pattern."\n";
			//echo "RegEx subject : ".$src."\n";
			preg_match_all("/".$this->pattern."/",$src,$matches);
			print_r($matches);
			if (isset($matches[1])) $results=$matches[1];
		}

		foreach ($this->filters as $filter)
		{
			$filter->applyTo($results);
		}

		if ($this->action) // there is an action
		{
			$ret = array();
			foreach ($results as $result)
			{
				$subresults = $scraper->models[$this->action]->scrapeFromUrl($result,$scraper);
				$ret[] = $subresults;
			}
			return $ret;
		}
		else
			return $results;
	}

	public function getCode($parent)
	{
		$ret = "";
		$ret.= "class ".$parent.$this->name." extends Token {\n";
		$ret.= "\tpublic function __construct() {\n";
		$ret.= "\t\t\$this->pattern = \"".$this->pattern."\";\n";
		$ret.= "\t\t\$this->filters = array(\n";

		foreach ($this->filters as $filter)
		{
			$filter_code = "";
			$filter_code .= $filter->name;
			if (count($filter->args)>0)
			{
				$filter_code.= "(";
				foreach ($filter->args as $arg)
				{
					$filter_code.= "\"".$arg."\"";
					if ($arg!=end($filter->args)) $filter_code.=",";
				}
				$filter_code.= ")";
			}
			$ret.= "\t\t\tnew Filter('$filter_code')";

			if ($filter!=end($this->filters)) $ret.=",";
			$ret.="\n";
		}
		$ret.= "\t\t);\n";
		$ret.= "\t}\n";
		$ret.= "}\n\n";
		
		return $ret;
	}

	public function dump()
	{
		$filts = "";
		foreach ($this->filters as $filter)
		{
			$filts .= $filter->dump();
			if ($filter!=end($this->filters)) $filts .= ",";
		}
		return $this->name." : (pattern: ".$this->pattern.", action: ".$this->action.", filters: ".$filts.")"; 
	}
}

//---------------------
// MODEL
//---------------------

class Model
{
	public $name;
	public $tokens;

	public function __construct($name,$model)
	{	
		$this->name = $name;
		$this->tokens = array();

		foreach ($model as $token_name=>$token_content)
		{
			$this->tokens[] = new Token($token_name, $token_content);
		}
	}

	public function scrapeFromSource($source,$scraper)
	{
		$results = array();

		foreach ($this->tokens as $token)
		{
			$results[$token->name] = $token->scrapeFromSource($source,$scraper);
		}

		return $results;
	}

	public function scrapeFromUrl($url,$scraper)
	{
		if (Url::isValid($url)) // URL is valid
		{
			$html = file_get_contents($url);
			return $this->scrapeFromSource($html,$scraper);
		}
		else // Treat it a simple text
		{
			return $this->scrapeFromSource($url,$scraper);
		}
	}

	public function getCode()
	{
		$ret = "";

		foreach ($this->tokens as $token)
		{
			$ret.= $token->getCode($this->name);
		}

		$ret.= "class ".$this->name." extends Model {\n";
		$ret.= "\tpublic function __construct() {\n";
		$ret.= "\t\t\$this->tokens = array(\n";
			foreach ($this->tokens as $token)
			{
				$ret.= "\t\t\tnew ".$this->name.$token->name."()";
				if ($token!=end($this->tokens)) $ret .= ",";
				$ret.="\n";
			}
		$ret.= "\t\t);\n";
		$ret.= "\t}\n";
		$ret.= "}\n\n";

		return $ret;
	}

	public function dump()
	{
		$ret = $this->name." : \n";

		foreach ($this->tokens as $token)
		{
			$ret .= "\t".$token->dump()."\n";
		}
		return $ret; 
	}
}

//---------------------
// MAIN SCRAPER
//---------------------

class Scraper
{
	public $options, $sources;
	public $models;

	public $entry;

	public $results;

	public function __construct($options=NULL,$sources=NULL,$script=NULL)
	{
		if ($script&&$sources)
		{
			$this->options = $options;
			$this->sources = $sources;

			$script_array = Spyc::YAMLLoadString($script);

			foreach ($script_array as $model_name=>$model_content)
			{
				$model = new Model($model_name,$model_content);
				echo $model->dump();

				$this->models[$model_name] = $model;
			}

			if ($this->options['entry']=="-") $this->entry = end(array_keys($this->models));
			else $this->entry = $this->options['entry'];

			print_r($scr);
		}
	}

	public function init()
	{
		$this->results = array();

		foreach ($this->sources as $i=>$source) 
		{
			if ($this->options['verbose'])
				echo "\rProcessing : ".($i+1)." / ".count($this->sources)."...";

			$this->results[$source] = $this->models[$this->entry]->scrapeFromUrl($source,$this);
		}

		echo "\n\n========================\nResult :\n========================\n";
		Arr::sanitize($this->results);
		print_r($this->results);
	}

	public function getCode()
	{
		$ret = "<?php\n";

		$ret.= "/**************************************************\n";
		$ret.= "/*\n";
		$ret.= "/* Scraper\n";
		$ret.= "/* Automatically generated using Scra.PHP\n";
		$ret.= "/*\n";
		$ret.= "/**************************************************/\n\n";

		$ret.= "//---------------------\n";
		$ret.= "// INCLUDES\n";
		$ret.= "//---------------------\n\n";

		$ret.= "require_once 'scraper.php';\n\n";

		$ret.= "//---------------------\n";
		$ret.= "// MODELS\n";
		$ret.= "//---------------------\n\n";

		foreach ($this->models as $model)
		{
			$ret.= $model->getCode();
		}

		$ret.= "\n";
		$ret.= "//---------------------\n";
		$ret.= "// INITIALIZATION\n";
		$ret.= "//---------------------\n\n";

		$ret.= "\$scraper = new Scraper();\n\n";
		$ret.= "\$scraper->entry = \"".$this->entry."\";\n";
		$ret.= "\$scraper->sources = ".print_r($this->sources,true).";\n";

		foreach ($this->models as $model)
		$ret.= "\$scraper->models[] = new ".$model->name."();\n";
		$ret.= "\n\$scraper->init();\n\n?>";

		return $ret;
	}
}

?>