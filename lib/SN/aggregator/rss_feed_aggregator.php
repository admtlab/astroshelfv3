<?php
/*
============================================================================================
Filename: 
---------
rss_feed_aggregator.php

Description: 
------------
This PHP file provides functions that will get the input about different RSS feed
providers and then read the requested number of RSS feeds from those providers.

This program requires an input file (rss_feed_sources.xml) to be present in the
same directory where this PHP file is executed from. This XML file should contain
information about a list of RSS feed sources. This input file can be customized as needed.

Di Bao
02/13/2013
ADMT Lab - Supernovae Project
============================================================================================
*/

error_reporting(E_ALL);
date_default_timezone_set('America/New_York');

define ("RSS_FEED_SOURCES_FILE_NAME", "rss_feed_sources.xml");
define ("ERROR_LOG_FETCH", "./log/error_log_fetch.log");
define ("ERROR_LOG_STORE", "./log/error_log_store.log");
define ("DEBUG_MODE", false);
define ("EPSILON", 0.001);

require_once("rss_feed_reader.php");
require_once("rss_result_store.php");
require_once("./common/.dbinfo.php");
require_once("./common/pre_processing.php");
require_once("./common/post_processing.php");
require_once("./common/error_handler.php");
require_once("./common/perform_curl.php");
require_once("./common/convert_ra_dec.php");

function get_list_of_rss_feed_sources($input_xml_file){
	file_exists($input_xml_file) or die('Could not find file ' . $input_xml_file);
	$xml_string_contents = file_get_contents($input_xml_file); 
	return($xml_string_contents);
}

function update_list_of_rss_feed_sources($input_xml_string, $input_xml_file){
	file_exists($input_xml_file) or die('Could not find file ' . $input_xml_file);
	$fhandle = fopen($input_xml_file, "w");
	if($fhandle){
		fwrite($fhandle, $input_xml_string);
		fclose($fhandle);
	}
}

function aggregate_rss_feeds($input_xml_file = RSS_FEED_SOURCES_FILE_NAME) {	

	$feed_source_sequence_number = 0;
	
	$xml_string_contents = get_list_of_rss_feed_sources($input_xml_file);
	$xml = simplexml_load_string($xml_string_contents);
	
	if ($xml == false) {
		error_handler("Sorry. Your RSS feed sources input file contains invalid data.", ERROR_LOG_FETCH);
		return;
	}
	
	if(DEBUG_MODE)	echo "\n";

	/*
	Stay in a loop and get the RSS feeds from each source.
	The document root element of the input xml file is <ListOfRssFeedSources>
	Under the root element, we will have one or more blocks of data with the
	following format.
	
	<RssFeedSourceInfo>
          	<rssFeedProviderName>....</rssFeedProviderName>
     	    <rssFeedProviderUrl>....</rssFeedProviderUrl>
    	    <lastUpdatedTime>....</lastUpdatedTime>
 	</RssFeedSourceInfo>	
 	
 	We are going to iterate over all the <RssFeedSourceInfo> elements.
	*/
	
	$flag_check_update = false;
	$global_update_counter = 0;
	foreach ($xml->RssFeedSourceInfo as $feed_source) {
		$feed_source_sequence_number++;		
		$rss_provider_name = trim(strval($feed_source->rssFeedProviderName));
		$rss_provider_url = trim(strval($feed_source->rssFeedProviderUrl));
		$last_updated_time = trim(strval($feed_source->lastUpdatedTime));
		if(DEBUG_MODE)	echo "Getting RSS feeds from $rss_provider_name ...\n";
		
		if(isset($feed_source->username) && isset($feed_source->password)){
			$proprietary_auth = array(trim(strval($feed_source->username)), trim(strval($feed_source->password)));
			$rss_feeds_result_array = get_rss_feeds($rss_provider_name, $rss_provider_url, $last_updated_time, $proprietary_auth);
		}else{
			$rss_feeds_result_array = get_rss_feeds($rss_provider_name, $rss_provider_url, $last_updated_time);
		}
		
		if(empty($rss_feeds_result_array) == false){
			$flag_check_update = true;
			$global_update_counter += $rss_feeds_result_array[1];
			$feed_source->lastUpdatedTime = (string)$rss_feeds_result_array[2];
			if(DEBUG_MODE)	echo $feed_source_sequence_number . "\n====================================\n\n";
			if(DEBUG_MODE)	print_r($rss_feeds_result_array);
			if(DEBUG_MODE)	echo "\n\n\n\n";
			store_rss_feed_results($rss_feeds_result_array, $rss_provider_url);
		}
	}
	
	if($flag_check_update){
		$xml_string_contents = $xml->asXML();
		update_list_of_rss_feed_sources($xml_string_contents, $input_xml_file);
	}
	
	if(DEBUG_MODE)	echo "\nFinished getting RSS feeds from $feed_source_sequence_number feed sources.\n\n";
	echo "Finished at " . strval(date("F j, Y, g:i a")) . ":\n";
	echo "$global_update_counter updates in total...\n\n";
}

// Program execution starts here.
$feed_sources_xml_file = null;

// Read the optional input filename from the command line if it is given.
// Syntax: php -f rss_feed_aggregator.php my_feed_sources.xml
// If there is no input file specified as a command-line argument, then
// it will try to open a default file name called rss_feed_sources.xml
// In PHP, argv[0] will have the name of the PHP program being run.
// argv[1] and above will have the command-line arguments.

// DEBUG MODE: php -f rss_feed_aggregator.php [my_feed_sources.xml] > output.txt
if($argc >= 2){
	$feed_sources_xml_file = $argv[1];
}

if($feed_sources_xml_file == null){
	// No input xml file specified. We will use a default file name.
	aggregate_rss_feeds();
}else{
	// We will use the user-specified feed sources input file.
	aggregate_rss_feeds($feed_sources_xml_file);
}
?>