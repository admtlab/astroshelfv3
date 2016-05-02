<?php
/*
============================================================================================
Filename: 
---------
rss_result_fix.php

Description: 
------------
This PHP file is used to fix the collected result on the fly,
it will retrieve the "unclassified"/"unmatch"/"failure" messages, and do the analysis again.

Di Bao
02/13/2013
ADMT Lab - Supernovae Project
============================================================================================
*/

function rss_result_fix(&$mysqli_handler, &$record){

	$feed_id = $record["msg_feed_id"];
	$raw_entry_content = gzuncompress($record["msg_blob"]);
	if($raw_entry_content == false){
		$error_msg = "Could not uncompress the message blob to original data.";
		error_handler($error_msg, ERROR_LOG_STORE);
		die($error_msg);
	}
	$msg_type = $record["msg_type"];
	
	$object_arrays = array();
	$type_array = array(
		0 => "object",
		1 => "annotation",
		2 => "unclassified",
		3 => "unmatch",
		4 => "failure"
	);
	$type_flag = pre_processing($mysqli_handler, $feed_id, $raw_entry_content, $object_arrays);
	
	if($type_array[$type_flag] == $msg_type){
		$log_msg = "Finished at " . strval(date("F j, Y, g:i a")) . ":\n";
		$log_msg .= "Message " . $record["msg_id"] . " of type " . $record["msg_type"] . " cannot be fixed now...\n\n\n";
		echo $log_msg;
	}else if(in_array($type_array[$type_flag], array("unclassified", "unmatch", "failure"))){		
		$update = "UPDATE `SN_messages` SET msg_type='" . $type_array[$type_flag] . "' WHERE msg_id=" . $record["msg_id"];
		$success = $mysqli_handler->query($update);
		if(!$success){
			$error_message = "Could not update `SN_messages` table.\n" . $mysqli_handler->connect_errno . " :" . $mysqli_handler->connect_error;
			error_handler($error_message, ERROR_LOG_STORE);
			$mysqli_handler->close();
			die($error_message);
		}
		
		$log_msg = "Finished at " . strval(date("F j, Y, g:i a")) . ":\n";
		$log_msg .= "Message " . $record["msg_id"] . " of type " . $record["msg_type"] . " now become " . $type_array[$type_flag] . " type...\n\n\n";
		echo $log_msg;
	}else{
		$mysqli_handler->autocommit(false);
	
		// phase 1: udpating SN_messages table
		$update = "UPDATE `SN_messages` SET msg_type='" . $type_array[$type_flag] . "' WHERE msg_id=" . $record["msg_id"];
		$success = $mysqli_handler->query($update);
		if(!$success){
			$error_message = "Could not update `SN_messages` table.\n" . $mysqli_handler->connect_errno . " :" . $mysqli_handler->connect_error;
			error_handler($error_message, ERROR_LOG_STORE);
			$mysqli_handler->rollback();
			$mysqli_handler->close();
			die($error_message);
		}

		// phase 2: inserting SN_objects table
		$stmt = $mysqli_handler->stmt_init();
		$stmt->prepare("INSERT INTO `SN_objects` (object_ra, object_dec, object_name, object_msg_hashed) VALUES (?, ?, ?, ?)");
		$stmt->bind_param("ddss", $_ra, $_dec, $_name, $_hashed);
		foreach($object_arrays as &$object_arr){
			$_ra = $object_arr[0];
			$_dec = $object_arr[1];
			$_name = $object_arr[2];
			$_hashed = $record["msg_hashed"];
			$success = $stmt->execute();
			if(!$success){
				$error_message = "Could not insert `SN_objects` table.\n" . $mysqli_handler->connect_errno . " :" . $mysqli_handler->connect_error;
				error_handler($error_message, ERROR_LOG_STORE);
				$stmt->close();
				$mysqli_handler->rollback();
				$mysqli_handler->close();
				die($error_message);
			}
			$object_arr[3] = $stmt->insert_id;
		}
		$stmt->close();

		// phase 3: inserting SN_uniques, SN_matches tables
		foreach($object_arrays as &$object_arr){
			$_ra = $object_arr[0];
			$_dec = $object_arr[1];
			$_object_t_id = $object_arr[3];
			$_unique_t_id = null;
			$query = "SELECT unique_id FROM `SN_uniques` WHERE " . $_ra . " BETWEEN (unique_ra - " . EPSILON . ") AND (unique_ra + " . EPSILON . ")";
			$query .= " AND " . $_dec . "BETWEEN (unique_dec - " . EPSILON . ") AND (unique_dec + " . EPSILON . ") LIMIT 1";
			$res_query = $mysqli_handler->query($query);
			if($res_query->num_rows > 0){ /* range query success */
				//$_unique_t_id = $res_query->fetch_assoc()['unique_id'];
				$tmp_fix = $res_query->fetch_assoc();
				$_unique_t_id = $tmp_fix['unique_id'];
			}else{ /* range query fail */
				list($_ra_hms, $_dec_dms) = convert_hmsdms($_ra, $_dec);
				$stmt = $mysqli_handler->stmt_init();
				$stmt->prepare("INSERT INTO `SN_uniques` (unique_ra, unique_dec, unique_ra_hmsdms, unique_dec_hmsdms) VALUES (?, ?, ?, ?)");
				$stmt->bind_param("ddss", $_ra, $_dec, $_ra_hms, $_dec_dms);
				$success = $stmt->execute();
				if(!$success){
					$error_message = "Could not insert `SN_uniques` table.\n" . $mysqli_handler->connect_errno . " :" . $mysqli_handler->connect_error;
					error_handler($error_message, ERROR_LOG_STORE);
					$stmt->close();
					$mysqli_handler->rollback();
					$mysqli_handler->close();
					die($error_message);
				}
				$_unique_t_id = $stmt->insert_id;
				$stmt->close();
			}
			$res_query->free();
			
			$stmt = $mysqli_handler->stmt_init();
			$stmt->prepare("INSERT INTO `SN_matches` (match_unique_id, match_object_id) VALUES (?, ?)");
			$stmt->bind_param("ii", $_unique_t_id, $_object_t_id);
			$success = $stmt->execute();
			if(!$success){
				$error_message = "Could not insert `SN_matches` table.\n" . $mysqli_handler->connect_errno . " :" . $mysqli_handler->connect_error;
				error_handler($error_message, ERROR_LOG_STORE);
				$stmt->close();
				$mysqli_handler->rollback();
				$mysqli_handler->close();
				die($error_message);
			}			
			$stmt->close();
		}
		$mysqli_handler->commit();
			
		$log_msg = "Finished at " . strval(date("F j, Y, g:i a")) . ":\n";
		$log_msg .= "Message " . $record["msg_id"] . " has been fixed...\n\n\n";
		echo $log_msg;		
	}
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
require_once("./common/pre_processing.php");
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

$query = "SELECT * FROM `SN_messages` WHERE `msg_type` NOT IN ('object', 'annotation') AND `msg_end_ts` IS NULL";
$res_query = $mysqli->query($query);
if($res_query->num_rows > 0){
	while($row = $res_query->fetch_assoc()){
		rss_result_fix($mysqli, $row);
	}
}
$res_query->free();

$mysqli->query("SET unique_checks=1");
$mysqli->query("SET foreign_key_checks=1");
$mysqli->close();

?>