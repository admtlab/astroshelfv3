<?php
/*
============================================================================================
Filename: 
---------
query_func.php

Description: 
------------
This PHP file is a function to do detailed query/retrieve.

Di Bao
02/25/2013
ADMT Lab - Supernovae Project
============================================================================================
*/

function query_func(&$_mysqli, $_offset = "all", $_limit = "all", $_orderby = "unique_id", $_sort = "DESC", $_para_array){

	$searchType = intval($_POST['search']);

	$res = array();
	//$res["aaData"] = array();
	
	/*
	* SQL queries
	* Get data to display
	*/

	if(empty($_para_array)){
		$query_uni = "SELECT * FROM `SN_uniques` ORDER BY " . $_orderby . " " . $_sort;
		if(strcasecmp($_offset, "all") && strcasecmp($_limit, "all")){
			$query_uni .= " LIMIT " . $_offset . ", " . $_limit;
		}
	}
	
	else if(array_key_exists('name', $_para_array)){
		$query_uni = "SELECT unique_id, unique_ra, unique_dec, unique_ra_hmsdms, unique_dec_hmsdms";
		$query_uni .= " FROM `SN_uniques` as u, `SN_matches` as m, `SN_objects` as o";
		$query_uni .= " WHERE u.unique_id = m.match_unique_id AND o.object_id = m.match_object_id";
		$query_uni .= " AND o.object_name LIKE '%" . $_para_array["name"] . "%' ";
		$query_uni .= "GROUP BY u.unique_id ";
		$query_uni .= " ORDER BY " . $_orderby . " " . $_sort;

			// $fp = fopen('data.txt', 'a');
			// fwrite($fp, $query_uni . "\n");
			// fclose($fp);
	}
	
	else if(array_key_exists('contain', $_para_array)){
		$query_uni = "SELECT * FROM `SN_uniques` WHERE unique_id IN (" . $_para_array["contain"] . ") ORDER BY " . $_orderby . " " . $_sort;
		if(strcasecmp($_offset, "all") && strcasecmp($_limit, "all")){
			$query_uni .= " LIMIT " . $_offset . ", " . $_limit;
		}
	}
	
	else{
		$query_uni = "SELECT * FROM `SN_uniques` as u, `SN_matches` as m, `SN_objects` as o";
		$query_uni .= " WHERE u.unique_id = m.match_unique_id AND o.object_id = m.match_object_id AND (unique_ra BETWEEN " . ($_para_array["ra"] - $_para_array["epsilon"]) . " AND " . ($_para_array["ra"] + $_para_array["epsilon"]);
		$query_uni .= ") AND (unique_dec BETWEEN " . ($_para_array["dec"] - $_para_array["epsilon"]) . " AND " . ($_para_array["dec"] + $_para_array["epsilon"]) . ")";
		$query_uni .= " ORDER BY " . $_orderby . " " . $_sort;		
	}

	/*
	* Output
	*/
	$res_uni = $_mysqli->query($query_uni);

	// $found_rows_query = "SELECT FOUND_ROWS()";
	// $rResultFilterTotal = $_mysqli->query($found_rows_query) or die($_mysqli->error);
	// list($iFilteredTotal) = $rResultFilterTotal->fetch_row();

	// $res["sEcho"] = intval($_POST['sEcho']);
	// $res["iTotalRecords"] = $iFilteredTotal;
	// $res["iTotalDisplayRecords"] = $iFilteredTotal;

	while($row_uni = $res_uni->fetch_assoc()){

		$record = array();
		
		$record["id"] = $row_uni["unique_id"];
		$record["ra"] = round(floatval($row_uni["unique_ra"]), 5);
		$record["dec"] = round(floatval($row_uni["unique_dec"]), 5);
		$record["hmsdms"] = $row_uni["unique_ra_hmsdms"] . $row_uni["unique_dec_hmsdms"];
		
		// start first join on `SN_objects`
		$record["names"] = array();
		$record["miscs"] = array();
		$record["messages"] = array();
		$query_obj = "SELECT * FROM SN_matches AS m JOIN SN_objects AS o ON m.match_object_id = o.object_id";
		$query_obj .= " WHERE m.match_unique_id = " . $row_uni["unique_id"];
		$res_obj = $_mysqli->query($query_obj);
		while($row_obj = $res_obj->fetch_assoc()){
			array_push($record["names"], $row_obj["object_name"]);
			
			$misc = array();
			$misc['type'] = $row_obj["object_type"];
			$misc['redshift'] = $row_obj["object_redshift"];
			$misc['disc_mag'] = $row_obj["object_disc_mag"];
			$misc['phase'] = $row_obj["object_phase"];
			array_push($record["miscs"], $misc);
			
			//check to see if the object msg is null - this is only for objects that come from the SN_known_list
			if(is_null($row_obj["object_msg_hashed"])){
				//join the SN_known_list with the relationship table connecting it to SN_uniques
				$query_msg = "SELECT * FROM `SN_known_list_match` AS k JOIN `SN_known_list` AS l ON k.kl_match_sn_id = l.sn_id";
				$query_msg .= " WHERE k.kl_match_unique_id = " . $row_obj["match_unique_id"];
				$res_msg = $_mysqli->query($query_msg);
				$new_row_msg = $res_msg->fetch_assoc();
				$res_msg->free();

				$msg = array();
				$msg["title"] = $new_row_msg["sn_name"];
				$msg["link"] = "N/A";
				$msg["description"] = "N/A";
				$msg["update_time"] = $new_row_msg["sn_date"];
				$msg["type"] = "object";

				$msg["feed"] = array();
				// start third join on `SN_feeds`
				$query_feed = "SELECT * FROM `SN_feeds` WHERE feed_id = 7";
				$res_feed = $_mysqli->query($query_feed);
				$row_feed = $res_feed->fetch_assoc();
				$res_feed->free();
				$msg["feed"]["name"] = $row_feed["feed_name"];
				$msg["feed"]["url"] = $row_feed["feed_url"];
				$msg["feed"]["description"] = $row_feed["feed_description"];

				array_push($record["messages"], $msg);
			} else {
				// start second join on `SN_messages`
				$query_msg = "SELECT * FROM `SN_messages` WHERE msg_hashed = '" . $row_obj["object_msg_hashed"];
				$query_msg .= "' AND msg_end_ts IS NULL LIMIT 1";
				$res_msg = $_mysqli->query($query_msg);
				$row_msg = $res_msg->fetch_assoc();
				$res_msg->free();
				$msg = array();
				$msg["title"] = $row_msg["msg_title"];
				$msg["link"] = $row_msg["msg_link"];
				$msg["description"] = $row_msg["msg_description"];
				$msg["update_time"] = $row_msg["msg_update_ts"];
				$msg["type"] = $row_msg["msg_type"];
			
				$msg["feed"] = array();
				// start third join on `SN_feeds`
				$query_feed = "SELECT * FROM `SN_feeds` WHERE feed_id = " . $row_msg["msg_feed_id"];
				$res_feed = $_mysqli->query($query_feed);
				$row_feed = $res_feed->fetch_assoc();
				$res_feed->free();
				$msg["feed"]["name"] = $row_feed["feed_name"];
				$msg["feed"]["url"] = $row_feed["feed_url"];
				$msg["feed"]["description"] = $row_feed["feed_description"];
			
				array_push($record["messages"], $msg);
			}
		}
		$res_obj->free();
		
		$record["names"] = array_unique($record["names"], SORT_STRING);
		// $record["DT_RowId"] = "row_" . $row_uni["unique_id"];

		//array_push($res["aaData"], $record);
		array_push($res, $record);
	}
	$res_uni->free();
	
	
	/*
	var_dump($res);
	print "\n\n\n";
	echo json_encode($res);
	exit;
	*/

	// $res = array('iTotalDisplayRecords' => $res["iTotalDisplayRecords"]) + $res;
	// $res = array('iTotalRecords' => $res["iTotalRecords"]) + $res;
	// $res = array('sEcho' => $res["sEcho"]) + $res;

	if(array_key_exists('contain', $_para_array))	return $res;
	else	return json_encode($res);
	
	// $sOutput = substr_replace( $sOutput, "", -1 );
	// $sOutput .= '] }';

	// echo $sOutput;
}

/////////////////////////////////////////////////////////
// TEST
/*
require_once(".dbinfo.php");
$mysqli = new mysqli($dbinfo['host'], $dbinfo['username'], $dbinfo['password'], $dbinfo['dbname']);
if($mysqli->connect_error){
	$error_msg = "Could not connect to AstroDB. " . $mysqli->connect_errno . " :" . $mysqli->connect_error;
	die($error_msg);
}
query_func($mysqli, 0, 5);
$mysqli->close();
*/
?>