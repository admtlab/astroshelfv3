<?php
/*
============================================================================================
Filename: 
---------
rss_result_store.php

Description: 
------------
This PHP file is the general interface to interact with MySQL database, taking care
of all kinds of logical transaction.

Di Bao
02/13/2013
ADMT Lab - Supernovae Project
============================================================================================
*/

function store_rss_feed_results(&$result_array, &$provider_url){
	global $dbinfo;
	
	$mysqli = new mysqli($dbinfo['host'], $dbinfo['username'], $dbinfo['password'], $dbinfo['dbname']);
	if($mysqli->connect_error){
		$error_msg = "Could not connect to AstroDB. " . $mysqli->connect_errno . " :" . $mysqli->connect_error;
		error_handler($error_msg, ERROR_LOG_STORE);
		die($error_msg);
	}
	
	$mysqli->query("SET unique_checks=0");
	$mysqli->query("SET foreign_key_checks=0");
	
	$counter = (int)$result_array[1];
	$source = $provider_url;
	if($source == "http://skyalert.org/feeds/290/"){
		$feed_id = 1;
	}else if($source == "http://skyalert.org/feeds/147/"){
		$feed_id = 2;
	}else if($source == "http://skyalert.org/feeds/149/"){
		$feed_id = 3;
	}else if($source == "http://skyalert.org/feeds/228/"){
		$feed_id = 4;
	}else if($source == "http://www.astronomerstelegram.org/?rss+supernovae"){
		$feed_id = 5;
	}else if($source == "http://www.cbat.eps.harvard.edu/rss/cbat/supernova.xml"){
		$feed_id = 6;
	}else{
		error_handler("Invalid feed url, exit pre_processing. ", ERROR_LOG_FETCH);
		return(false);
	}
	$title_array = $result_array[3];
	$url_array = $result_array[4];
	$description_array = $result_array[5];
	$update_time_array = $result_array[6];
	$id_array = $result_array[7];
	$entry_content_array = $result_array[8];
	$hashed_id_array = $result_array[9];

	for($index = 0; $index < $counter; $index++){
		$mysqli->autocommit(false);
	
		$raw_id = $id_array[$index];
		$raw_hashed_id = $hashed_id_array[$index];
		$raw_feed_id = $feed_id;
		$raw_type = "";
		$raw_title = $title_array[$index];
		$raw_url = $url_array[$index];
		$raw_description = $description_array[$index];
		$raw_entry_content = $entry_content_array[$index];
		$raw_entry_content_compressed = gzcompress($raw_entry_content, 9);
		$raw_update_time = $update_time_array[$index];
		
		$object_arrays = array();
		$type_array = array(
			0 => "object",
			1 => "annotation",
			2 => "unclassified",
			3 => "unmatch",
			4 => "failure"
		);
		$type_flag = pre_processing($mysqli, $feed_id, $raw_entry_content, $object_arrays);

		if(DEBUG_MODE)	echo "\n\n";
		if(DEBUG_MODE)	var_dump($object_arrays);
		if(DEBUG_MODE)	echo "\n\n";
		
		$raw_type = $type_array[$type_flag];
		
		/* Phase one - inserting/updating SN_messsages table */
		$insert_flag = true;
		
		$query = "SELECT msg_id FROM `SN_messages` WHERE msg_hashed = '" . $raw_hashed_id . "' LIMIT 1";
		$res_query = $mysqli->query($query);
		if($res_query->num_rows > 0){
			$insert_flag = false;
			/* Existing message, logical delete old ones */
			$update = "UPDATE `SN_messages` SET msg_end_ts=CURRENT_TIMESTAMP() WHERE msg_hashed='" . $raw_hashed_id . "' AND msg_end_ts IS NULL";
			$success = $mysqli->query($update);
			if(!$success){
				$error_message = "Could not update `SN_messages` table.\n" . $mysqli->connect_errno . " :" . $mysqli->connect_error;
				error_handler($error_message, ERROR_LOG_STORE);
				$res_query->free();
				$mysqli->rollback();
				$mysqli->close();
				die($error_message);
			}
		}
		$res_query->free();
		
		/* New message, insert */
		$stmt = $mysqli->stmt_init();
		$stmt->prepare("INSERT INTO `SN_messages` (msg_identifier, msg_hashed, msg_feed_id, msg_type, msg_title, msg_link, msg_description, 
			msg_blob, msg_update_ts, msg_start_ts) VALUES (?, ?, ?, ?, ?, ?, ?, ?, FROM_UNIXTIME(?), CURRENT_TIMESTAMP())");
		$stmt->bind_param("ssisssssi", $raw_id, $raw_hashed_id, $raw_feed_id, $raw_type, $raw_title, $raw_url, $raw_description,
			$raw_entry_content_compressed, $raw_update_time);
		$success = $stmt->execute();
		if(!$success){
			$error_message = "Could not insert `SN_messages` table.\n" . $mysqli->connect_errno . " :" . $mysqli->connect_error;
			error_handler($error_message, ERROR_LOG_STORE);
			$stmt->close();
			$mysqli->rollback();
			$mysqli->close();
			die($error_message);
		}
		$stmt->close();
		
		// ##fetching the misc. info.
		if(($raw_type == "object" || $raw_type == "annotation") && $insert_flag){
			$misc_arrays = array();
			$succ_flag = false;
			$succ_flag = post_processing($mysqli, $feed_id, $raw_entry_content, $misc_arrays);

			if(DEBUG_MODE)	echo "\n\n";
			if(DEBUG_MODE)	var_dump($misc_arrays);
			if(DEBUG_MODE)	echo "\n\n";
			
			if(!$succ_flag){
				$error_message = "Fail to do post-processing to fetch misc. info.\n";
				error_handler($error_message, ERROR_LOG_FETCH);
				$mysqli->rollback();
				$mysqli->close();
				die($error_message);
			}
		}
		
		/* Phase two - inserting SN_objects table */		
		if(($raw_type == "object" || $raw_type == "annotation") && $insert_flag){
			$stmt = $mysqli->stmt_init();
			$stmt->prepare("INSERT INTO `SN_objects` (object_ra, object_dec, object_name, object_msg_hashed, object_type, object_redshift, object_disc_mag, object_phase) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
			$stmt->bind_param("ddsssdds", $_ra, $_dec, $_name, $_hashed, $_type, $_redshift, $_disc_mag, $_phase);
			$sync_arr_count = 0;
			foreach($object_arrays as &$object_arr){
				$_ra = $object_arr[0];
				$_dec = $object_arr[1];
				$_name = $object_arr[2];
				$_hashed = $raw_hashed_id;
				
				// ##adding the misc. info.
				if(count($object_arrays) == count($misc_arrays)){
					$_type = is_null($misc_arrays[$sync_arr_count]['type']) ? NULL : strval($misc_arrays[$sync_arr_count]['type']);
					$_redshift = is_null($misc_arrays[$sync_arr_count]['redshift']) ? NULL : floatval($misc_arrays[$sync_arr_count]['redshift']);
					$_disc_mag = is_null($misc_arrays[$sync_arr_count]['disc_mag']) ? NULL : floatval($misc_arrays[$sync_arr_count]['disc_mag']);
					$_phase = is_null($misc_arrays[$sync_arr_count]['phase']) ? NULL : strval($misc_arrays[$sync_arr_count]['phase']);
					$sync_arr_count++;
				}else{
					$_type = NULL;
					$_redshift = NULL;
					$_disc_mag = NULL;
					$_phase = NULL;
				}
				
				$success = $stmt->execute();
				if(!$success){
					$error_message = "Could not insert `SN_objects` table.\n" . $mysqli->connect_errno . " :" . $mysqli->connect_error;
					error_handler($error_message, ERROR_LOG_STORE);
					$stmt->close();
					$mysqli->rollback();
					$mysqli->close();
					die($error_message);
				}
				$object_arr[3] = $stmt->insert_id;
			}
			$stmt->close();
		}
		
		/* Phase three - inserting SN_uniques, SN_matches tables */
		if(($raw_type == "object" || $raw_type == "annotation") && $insert_flag){
			foreach($object_arrays as &$object_arr){
				$_ra = $object_arr[0];
				$_dec = $object_arr[1];
				$_object_t_id = $object_arr[3];
				$_unique_t_id = null;
				$query = "SELECT unique_id FROM `SN_uniques` WHERE " . $_ra . " BETWEEN (unique_ra - " . EPSILON . ") AND (unique_ra + " . EPSILON . ")";
				$query .= " AND " . $_dec . "BETWEEN (unique_dec - " . EPSILON . ") AND (unique_dec + " . EPSILON . ") LIMIT 1";
				$res_query = $mysqli->query($query);
				if($res_query->num_rows > 0){ /* range query success */
					//$_unique_t_id = $res_query->fetch_assoc()['unique_id'];
					$tmp_fix = $res_query->fetch_assoc();
					$_unique_t_id = $tmp_fix['unique_id'];
				}else{ /* range query fail */
					list($_ra_hms, $_dec_dms) = convert_hmsdms($_ra, $_dec);
					$stmt = $mysqli->stmt_init();
					$stmt->prepare("INSERT INTO `SN_uniques` (unique_ra, unique_dec, unique_ra_hmsdms, unique_dec_hmsdms) VALUES (?, ?, ?, ?)");
					$stmt->bind_param("ddss", $_ra, $_dec, $_ra_hms, $_dec_dms);
					$success = $stmt->execute();
					if(!$success){
						$error_message = "Could not insert `SN_uniques` table.\n" . $mysqli->connect_errno . " :" . $mysqli->connect_error;
						error_handler($error_message, ERROR_LOG_STORE);
						$stmt->close();
						$mysqli->rollback();
						$mysqli->close();
						die($error_message);
					}
					$_unique_t_id = $stmt->insert_id;
					$stmt->close();
				}
				$res_query->free();
				
				$stmt = $mysqli->stmt_init();
				$stmt->prepare("INSERT INTO `SN_matches` (match_unique_id, match_object_id) VALUES (?, ?)");
				$stmt->bind_param("ii", $_unique_t_id, $_object_t_id);
				$success = $stmt->execute();
				if(!$success){
					$error_message = "Could not insert `SN_matches` table.\n" . $mysqli->connect_errno . " :" . $mysqli->connect_error;
					error_handler($error_message, ERROR_LOG_STORE);
					$stmt->close();
					$mysqli->rollback();
					$mysqli->close();
					die($error_message);
				}			
				$stmt->close();
			}
		}
		
		$mysqli->commit();
	}
	
	$mysqli->query("SET unique_checks=1");
	$mysqli->query("SET foreign_key_checks=1");
	$mysqli->close();
}

?>