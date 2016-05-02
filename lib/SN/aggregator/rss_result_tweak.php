<?php
/*
============================================================================================
Filename: 
---------
rss_result_tweak.php

Description: 
------------
This PHP file is used to update the object misc. info.

Di Bao
02/13/2013
ADMT Lab - Supernovae Project
============================================================================================
*/

function rss_result_tweak(&$mysqli_handler, &$record){

	$feed_id = $record["msg_feed_id"];
	$raw_entry_content = gzuncompress($record["msg_blob"]);
	if($raw_entry_content == false){
		$error_msg = "Could not uncompress the message blob to original data.";
		error_handler($error_msg, ERROR_LOG_STORE);
		die($error_msg);
	}
	$msg_type = $record["msg_type"];
	
	// ##fetching the misc. info.
	$misc_arrays = array();
	$succ_flag = false;
	$succ_flag = post_processing($mysqli_handler, $feed_id, $raw_entry_content, $misc_arrays);
	for($i = 0; $i < count($misc_arrays); $i++){
		if(is_null($misc_arrays[$i]['type']))	$misc_arrays[$i]['type'] = "undefine";
		if(is_null($misc_arrays[$i]['redshift']))	$misc_arrays[$i]['redshift'] = 0;
		if(is_null($misc_arrays[$i]['disc_mag']))	$misc_arrays[$i]['disc_mag'] = 0;
		if(is_null($misc_arrays[$i]['phase']))	$misc_arrays[$i]['phase'] = "undefine";
	}
	
	/*
	echo "\n\n";
	print_r($misc_arrays);
	echo "\n\n";
	return;
	*/
	
	if(!$succ_flag){
		$error_message = "Fail to do post-processing to fetch misc. info.\n";
		error_handler($error_message, ERROR_LOG_FETCH);
		$mysqli_handler->close();
		die($error_message);
	}
	
	// ##updating the misc. info.
	$query2 = "SELECT * FROM `SN_objects` WHERE object_msg_hashed='" . $record["msg_hashed"] . "'";
	$res_query2 = $mysqli_handler->query($query2);
	if($res_query2->num_rows == count($misc_arrays)){
		$arr_count = 0;
		while($row = $res_query2->fetch_assoc()){
			$update = "UPDATE `SN_objects` SET object_type='" . strval($misc_arrays[$arr_count]['type']) . "', object_redshift=" . floatval($misc_arrays[$arr_count]['redshift']);
			$update .= ", object_disc_mag=" . floatval($misc_arrays[$arr_count]['disc_mag']) . ", object_phase='" . strval($misc_arrays[$arr_count]['phase']) . "'";
			$update .= " WHERE object_id =" . $row['object_id'];
			$arr_count++;
			
			$success = $mysqli_handler->query($update);
			if(!$success){
				$error_message = "Could not update `SN_objects` table.\n" . $mysqli_handler->connect_errno . " :" . $mysqli_handler->connect_error;
				error_handler($error_message, ERROR_LOG_STORE);
				$mysqli_handler->close();
				die($error_message);
			}
		}
	}else{
		echo "\n\nParsing error for the following messages:\n";
		echo $record['msg_id'] . ":  " . $record['msg_link'];
		echo "\n\n";
	}
	$res_query2->free();
}

/*
##########################
The beginning of script
##########################
*/

error_reporting(E_ALL);
date_default_timezone_set('America/New_York');

define ("ERROR_LOG_FETCH", "./log/error_log_fetch.log");
define ("ERROR_LOG_STORE", "./log/error_log_store.log");
define ("EPSILON", 0.001);

require_once("./common/.dbinfo.php");
require_once("./common/post_processing.php");
require_once("./common/error_handler.php");
require_once("./common/perform_curl.php");
require_once("./common/convert_ra_dec.php");

global $dbinfo;

$mysqli = new mysqli($dbinfo['host'], $dbinfo['username'], $dbinfo['password'], $dbinfo['dbname']);
if($mysqli->connect_error){
	$error_msg = "Could not connect to AstroDB. " . $mysqli->connect_errno . " :" . $mysqli->connect_error;
	error_handler($error_msg, ERROR_LOG_STORE);
	die($error_msg);
}

$mysqli->query("SET unique_checks=0");
$mysqli->query("SET foreign_key_checks=0");

$query = "SELECT * FROM `SN_messages` WHERE `msg_type` IN ('object', 'annotation') AND `msg_end_ts` IS NULL";
$res_query = $mysqli->query($query);
if($res_query->num_rows > 0){
	while($row = $res_query->fetch_assoc()){
		rss_result_tweak($mysqli, $row);
	}
}
$res_query->free();

$mysqli->query("SET unique_checks=1");
$mysqli->query("SET foreign_key_checks=1");
$mysqli->close();

?>