#!/usr/bin/env php
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
  * @file scra.php
  */

//---------------------
// INCLUDES
//---------------------

// Libraries
require_once 'libraries/console/CommandLine.php';

// Core
require_once 'scraper.php';

//---------------------
// DEFINITIONS
//---------------------

// Application
define(APP_VERSION,'0.3');
define(APP_DESCRIPTION,'');

// Error messages
define(ERR_NOSOURCESFILE,'Source file not found');
define(ERR_NOSCRIPTFILE,'YAML script not found');

//---------------------
// INITIALIZATION
//---------------------

//error_reporting(E_ERROR | E_PARSE);

// Running from the Command Line

if ( basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"]) ) 
{ 
	$cmd = new Console_CommandLine();

	$cmd->version = APP_VERSION;
	$cmd->description = APP_DESCRIPTION;
	$cmd->addArgument("sources");
	$cmd->addArgument("script");

	$cmd->addOption('output', array('short_name'=>'-o', 'long_name'=>'--output', 'description'=>'write output to FILE', 'help_name'=>'FILE', 'action'=>'StoreString'));
	$cmd->addOption('output_format', array('short_name'=>'-f', 'long_name'=>'--output-format', 'description'=>'set output file format (csv,xml,sql,json)', 'help_name'=>'FORMAT', 'choices'=>array('csv','xml','sql','json'), 'default'=>'csv', 'action'=>'StoreString'));
	$cmd->addOption('entry', array('short_name'=>'-e', 'long_name'=>'--entry-point', 'description'=>'set entry point to MODEL', 'help_name'=>'MODEL', 'default'=>'-', 'action'=>'StoreString'));
	$cmd->addOption('verbose', array('short_name'=>'-v', 'long_name'=>'--verbose', 'description'=>'show all messages', 'default'=>true, 'action'=>'StoreTrue'));

	try 
	{ 
		$parsed = $cmd->parse();

		if (!File::exists($parsed->args['sources'])) Error::halt(ERR_NOSOURCESFILE);
		$sources = file_get_contents($parsed->args['sources']);
		$sources = explode("\n",$sources);

		if (!File::exists($parsed->args['script'])) Error::halt(ERR_NOSCRIPTFILE);
		$script = file_get_contents($parsed->args['script']);

		$scraper = new Scraper($parsed->options, $sources, $script);
		// echo $scraper->getCode();
		$scraper->init();


		echo "\n\n";
	} 
	catch (Exception $e) 
	{ 
		$cmd->displayError($e->getMessage()); 
	}
}

/********************************************
 This is the end,
 my only friend, the end...
 ********************************************/

?>