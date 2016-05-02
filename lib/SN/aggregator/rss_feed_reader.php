<?php
/*
============================================================================================
Filename: 
---------
rss_feed_reader.php

Description: 
------------
This PHP file provides functions that will read the RSS feeds available at a given URL.
It uses the CURL and SimpleXML PHP library functions to perform this task.

Di Bao
02/13/2013
ADMT Lab - Supernovae Project
============================================================================================
*/

function fix_CBAT_time_string($_curr_unix_time){
	$pattern1 = '/(\S+)\:(\d{1})\.(\d+)ZZ/i';
	$replacement1 = '$1:0$2.$3Z';
	$pattern2 = '/(\S+)\:(\d{2})\.(\d+)ZZ/i';
	$replacement2 = '$1:$2.$3Z';
	if(preg_match($pattern1, $_curr_unix_time)){
		$_curr_unix_time = preg_replace($pattern1, $replacement1, $_curr_unix_time);
	}else if(preg_match($pattern2, $_curr_unix_time)){
		$_curr_unix_time = preg_replace($pattern2, $replacement2, $_curr_unix_time);
	}else{
		;
	}
	return $_curr_unix_time;
}

function parse_rss_feed_xml(&$iprovider_url, &$ireceived_rss_feeds, $last_updated_time, &$inew_updated_time,
		&$irss_title_array, 
		&$irss_url_array, 
		&$irss_description_array, 
		&$irss_update_time_array,
		&$irss_id_array, 
		&$irss_entry_content_array,
		&$irss_hashed_id_array
){
	$xml = simplexml_load_string($ireceived_rss_feeds);
	
	if((is_object($xml) == false) || (sizeof($xml) <= 0)){	
		error_handler("RSS feed sources ". $iprovider_url ." contains invalid data.", ERROR_LOG_FETCH);
		return(false);
	}
	$last_unix_time = (int)$last_updated_time;
	$curr_unix_time = 0;
	$ATel_flag = false;

	if(isset($xml->updated)){
		$curr_unix_time = trim(strval($xml->updated));
		if(strpos($curr_unix_time, "no events") !== false){
			return(false);
		}

		if($iprovider_url == "http://skyalert.org/feeds/290/"){
			$curr_unix_time = fix_CBAT_time_string($curr_unix_time);
		}
		$curr_unix_time = strtotime($curr_unix_time);
		if($curr_unix_time == $last_unix_time){
			return(false);
		}
	}else{
		$ATel_flag = true;
	}

	if($ATel_flag){
		$obj_array = $xml->item;
	}else{
		$obj_array = $xml->entry;
	}
	
	if((is_object($obj_array) == false) || (sizeof($obj_array) <= 0)){
		error_handler("Failed to fetch array of entries/items from " . $iprovider_url . ".", ERROR_LOG_FETCH);
		return(false);
	}
	
	$count_of_rss_items_retrieved = 0;
	
	foreach($obj_array as $item){
		if($ATel_flag == false){
			$item_unix_time = trim(strval($item->updated));
			if($iprovider_url == "http://skyalert.org/feeds/290/"){
				$item_unix_time = fix_CBAT_time_string($item_unix_time);
			}
		}else{
			$item_unix_time = trim(strval($item->children('dc', true)->date));
		}
		$item_unix_time = strtotime($item_unix_time);
		
		if($item_unix_time > $last_unix_time){
			if($iprovider_url == "http://www.cbat.eps.harvard.edu/rss/cbat/supernova.xml"){
				$title = trim(strval($item->title));
				$url = trim(strval($item->link['href']));
				$description = substr(trim(substr(strval($item->content), 6)), 0, 1000);
				$update_time = $item_unix_time;
				$id = trim(strval($item->id));
				$hashed_id = hash('md5', trim(strval($item->id)));
				$entry_content = trim(strval($item->asXML()));
			}else if($iprovider_url == "http://www.astronomerstelegram.org/?rss+supernovae"){
				$title = trim(strval($item->title));
				$url = trim(strval($item->link));
				$description = substr(trim(strval($item->description)), 0, 1000);
				$update_time = $item_unix_time;
				$id = trim(strval($item->identifier));
				$hashed_id = hash('md5', trim(strval($item->identifier)));
				$entry_content = trim(strval($item->asXML()));
			}else if(strpos($iprovider_url, "http://skyalert.org") !== false){
				$title = trim(strval($item->title));
				$url = trim(strval($item->link[1]['href']));
				$description = "Not Available...";
				$update_time = $item_unix_time;
				$id = trim(strval($item->id));
				$hashed_id = hash('md5', trim(strval($item->id)));
				$entry_content = trim(strval($item->asXML()));
			}else{
				;
			}

			array_push($irss_title_array, $title);
			array_push($irss_url_array, $url);
			array_push($irss_description_array, $description);
			array_push($irss_update_time_array, $update_time);
			array_push($irss_id_array, $id);
			array_push($irss_entry_content_array, $entry_content);
			array_push($irss_hashed_id_array, $hashed_id);

			$count_of_rss_items_retrieved++;
		}
		
		if($ATel_flag){
			if($curr_unix_time < $item_unix_time){
				$curr_unix_time = $item_unix_time;
			}
		}
	}
	
  	if ($count_of_rss_items_retrieved > 0) {
		$inew_updated_time = $curr_unix_time;
    	return(true);
  	} else {
    	return(false);
  	}
}

function get_rss_feeds($_provider_name, $_provider_url, $_last_updated_time, $_auth = null){	
	
	$received_rss_feeds = perform_curl_operation($_provider_url, $_auth);
	$received_rss_feeds = utf8_encode($received_rss_feeds);	
	
	if(empty($received_rss_feeds)){
		error_handler("The curl() HTTP request failed to get response from " . $_provider_url . ".", ERROR_LOG_FETCH);
		$empty_array = array();
		return($empty_array); 	
	}
	
	$_new_updated_time = "";
	
	$rss_title_array = array();
	$rss_url_array = array();
	$rss_description_array = array();
	$rss_update_time_array = array();
	$rss_id_array = array();
	$rss_entry_content_array = array();
	$rss_hashed_id_array = array();

	$parser_result = parse_rss_feed_xml($_provider_url, $received_rss_feeds, $_last_updated_time, $_new_updated_time,
		$rss_title_array, 
		$rss_url_array, 
		$rss_description_array, 
		$rss_update_time_array,
		$rss_id_array, 
		$rss_entry_content_array,
		$rss_hashed_id_array
	);
	
	if($parser_result == true){
		$result_array = array();
		$result_array[0] = $_provider_name;
		$result_array[1] = sizeof($rss_title_array);
		$result_array[2] = $_new_updated_time;
		$result_array[3] = $rss_title_array;
		$result_array[4] = $rss_url_array;
		$result_array[5] = $rss_description_array;
		$result_array[6] = $rss_update_time_array;
		$result_array[7] = $rss_id_array;
		$result_array[8] = $rss_entry_content_array;
		$result_array[9] = $rss_hashed_id_array;

		return($result_array);		
	}else{
		$empty_array = array();
		return($empty_array); 			
	}
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

// Uncomment the following code block to do a stand-alone test of this program.
/*
date_default_timezone_set('America/New_York');
$rss_provider_name = "whatever";
$rss_provider_url = "http://skyalert.org/feeds/290/";
$rss_provider_url = "http://skyalert.org/feeds/149/";
$rss_provider_url = "http://www.astronomerstelegram.org/?rss+supernovae";
$rss_provider_url = "http://www.cbat.eps.harvard.edu/rss/cbat/supernova.xml";
$last_updated_time = strtotime("2013-01-26T18:57:36.00Z");
$last_updated_time = strtotime("2013-02-08T03:17:01Z");
$last_updated_time = strtotime("2013-02-09T03:17:01Z");
$last_updated_time = strtotime("2013-02-10T23:01:09.022Z");

$auth = array("MWV", "KSPLIC");

$rss_results_array = get_rss_feeds($rss_provider_name, $rss_provider_url, $last_updated_time, $auth);
print_r($rss_results_array);
*/
?> 