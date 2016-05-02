<?php
/*
============================================================================================
Filename: 
---------
listSN.php

Description: 
------------
This PHP file is a general server-side script handling HTTP requests.

Di Bao
04/07/2013
ADMT Lab - Supernovae Project
============================================================================================
*/

require_once("./funcs/.dbinfo.php");
require_once("./funcs/query_func_unit_testing.php");

if(isset($_POST['list'])){
	$result = array();

	$_usr = $_POST['_usr'];
	$_pwd = $_POST['_pwd'];
	
	$mysqli = new mysqli($dbinfo['host'], $dbinfo['username'], $dbinfo['password'], $dbinfo['dbname']);
	if($mysqli->connect_error){
		$error_msg = "Could not connect to AstroDB. " . $mysqli->connect_errno . " :" . $mysqli->connect_error;
		die($error_msg);
	}
	
	$stmt = $mysqli->stmt_init();
	$stmt->prepare("SELECT user_id FROM `user` WHERE username = ? AND password = PASSWORD(?) LIMIT 1");
	$stmt->bind_param("ss", $_usr, $_pwd);
	$stmt->execute();
	$stmt->store_result();
	if($stmt->num_rows == 0){
		$stmt->close();
		$mysqli->close();
		header('Content-Type: application/json');
		echo json_encode(array('ERROR'=>'Invalid username/password pair'));
		exit;		
	}else{
		$stmt->bind_result($_author_id);
		$stmt->fetch();
	}
	
	$stmt->prepare("SELECT * FROM `SN_lists` WHERE list_owner_id = ?");
	$stmt->bind_param("i", $_author_id);
	$stmt->execute();
	$lists = array();
	$stmt->bind_result($list_id, $list_name, $list_description, $list_owner, $list_owner_id, $list_create_time, $list_update_time, $list_delete_time);
	while($stmt->fetch()){
		array_push($lists, array($list_id, $list_name, $list_description, $list_owner, $list_owner_id, $list_create_time, $list_update_time, $list_delete_time));
	}
	$stmt->close();
	
	//var_dump($lists);
	//exit;
	
	foreach($lists as &$list){
		//var_dump($list);
		//exit;
		
		$one_list = array();
		$one_list["id"] = $list[0];
		$one_list["name"] = $list[1];
		$one_list["description"] = $list[2];
		$one_list["owner"] = $list[3];
		$one_list["owner_id"] = $list[4];
		$one_list["create_time"] = $list[5];
		$one_list["update_time"] = $list[6];
		$one_list["delete_time"] = $list[7];
		$one_list["objects"] = array();
		
		$objects_array = array();
		$query_uni = "SELECT contain_unique_id FROM `SN_contains` WHERE contain_list_id = " . $list[0];
		$res_uni = $mysqli->query($query_uni);
		if(!$res_uni){
			echo $mysqli->error;
			exit;
		}
		while($row_uni = $res_uni->fetch_assoc()){
			array_push($objects_array, $row_uni["contain_unique_id"]);
		}
		
		if(!empty($objects_array)){
			$objects_string = implode(", ", $objects_array);
			$one_list["objects"] = query_func($mysqli, "all", "all", "unique_id", "DESC", array("contain" => $objects_string));
			array_push($result, $one_list);
		}
	}
	
	$mysqli->close();
	
	header('Content-Type: application/json');
	echo json_encode($result);
	exit;
}

else if(isset($_POST['delete'])){
	$delete_list_id = $_POST['_list_id'];
	$delete_uni_id = $_POST['_uni_id'];
	
	$mysqli = new mysqli($dbinfo['host'], $dbinfo['username'], $dbinfo['password'], $dbinfo['dbname']);
	if($mysqli->connect_error){
		$error_msg = "Could not connect to AstroDB. " . $mysqli->connect_errno . " :" . $mysqli->connect_error;
		die($error_msg);
	}
	
	$delete = "DELETE FROM `SN_contains` WHERE contain_list_id = " . $delete_list_id . " AND contain_unique_id = " . $delete_uni_id;
	$success = $mysqli->query($delete);
	if($success){
		echo json_encode(array('success'=>1));
	}else{
		echo json_encode(array('failure'=>1));
	}
}

else if (isset($_POST['save'])){

	$listInfo = json_decode(stripslashes($_POST['_listInfo']));
	$userID = intval($_POST['_userID']);
	$objectIDs = json_decode(stripslashes($_POST['_objectIDs']));

	$mysqli = new mysqli($dbinfo['host'], $dbinfo['username'], $dbinfo['password'], $dbinfo['dbname']);
	if($mysqli->connect_error){
		$error_msg = "Could not connect to AstroDB. " . $mysqli->connect_errno . " :" . $mysqli->connect_error;
		die($error_msg);
	}

	if(isset($_POST['deletion'])) {
		$delete = "DELETE FROM `SN_lists` WHERE list_name = '" . $listInfo[0] . "'";
		$success = $mysqli->query($delete);
	}
	
	$stmt = $mysqli->stmt_init();	//initialize the statement and return an object to use for mysqli_stmt_prepare
	if($stmt->prepare("SELECT fname FROM `user` WHERE user_id = ?")){	//prepare SQL statement for execution
		$stmt->bind_param("i", $userID);	//bind the parameters for markers
		$stmt->execute();	//execute the SELECT query
		$stmt->bind_result($author_name);	//bind the result to the $author_name variable
		$stmt->fetch();
		$stmt->close();
	}
	
	$save = "INSERT INTO `SN_lists` (list_name, list_description, list_owner, list_owner_id, list_create_ts, list_update_ts, list_delete_ts)";
	$save .= " VALUES ('" . mysqli_real_escape_string($mysqli, $listInfo[0]) . "', '";
	$save .= mysqli_real_escape_string($mysqli, $listInfo[1]) . "', '";
	$save .= $author_name . "', ";
	$save .= $userID . ", ";
	$save .= "NOW(), NULL, NULL)";

	$success = $mysqli->query($save);
	if($success){
		$insert_id = $mysqli->insert_id;
		addObjects($mysqli, $insert_id, $objectIDs);	//call the method to add objects from the list into the DB
	} else {
		echo json_encode(array('failure'=>1));
		exit;
	}
}

else if (isset($_POST['retrieveLists'])) {

	$listNames = array();
	$userID = intval($_POST['_userID']);	//store the user's ID

	/* Initialize the db connection */
	$mysqli = new mysqli($dbinfo['host'], $dbinfo['username'], $dbinfo['password'], $dbinfo['dbname']);
	if ($mysqli->connect_error){
		$error_msg = "Could not connect to AstroDB. " . $mysqli->connect_errno . " :" . $mysqli->connect_error;
		die($error_msg);
	}

	//Create a prepared statement to execute
	$stmt = $mysqli->stmt_init();
	if ($stmt->prepare("SELECT list_id, list_name, list_description FROM `SN_lists` WHERE list_owner_id = ?")){
		$stmt->bind_param("i", $userID);
		$stmt->execute();
		$stmt->bind_result($list_id, $list_name, $list_description);
		while($stmt->fetch()){
			// Return the list ID, the list name, and the description back
			array_push($listNames, array($list_id, $list_name, $list_description));
		}
		$stmt->close();
	}

	$mysqli->close();	//close the db connection

	header('Content-Type: application/json');
	echo json_encode($listNames);
	exit;
}

else if (isset($_POST['objects'])) {

	$selectedListID = intval($_POST['_selectListID']);
	$userID = intval($_POST['_userID']);

	$results = array();
	$results['messages'] = array();
	$objects_array = array();
	$messages_array = array();

	/* Initialize the db connection */
	$mysqli = new mysqli($dbinfo['host'], $dbinfo['username'], $dbinfo['password'], $dbinfo['dbname']);
	if ($mysqli->connect_error){
		$error_msg = "Could not connect to AstroDB. " . $mysqli->connect_errno . " :" . $mysqli->connect_error;
		die($error_msg);
	}

	$fp = fopen("exptest.txt", 'w');
	$stmt = $mysqli->stmt_init();

	//Create a prepared statement to perform the SELECT
	if ( $_POST['_listMan'] ) {
		$sql_query = "SELECT unique_id, GROUP_CONCAT(DISTINCT object_name) as object_name, unique_ra, unique_dec, unique_ra_hmsdms, unique_dec_hmsdms, GROUP_CONCAT(DISTINCT object_type) as object_type, GROUP_CONCAT(DISTINCT object_disc_mag) as object_disc_mag, GROUP_CONCAT(DISTINCT object_redshift) as object_redshift, GROUP_CONCAT(DISTINCT object_phase) as object_phase ";
		$sql_query .= "FROM `SN_lists` as l ";
		$sql_query .= "INNER JOIN `SN_contains`as c ON l.list_id = c.contain_list_id ";
		$sql_query .= "INNER JOIN `SN_uniques` as u ON c.contain_unique_id = u.unique_id ";
		$sql_query .= "INNER JOIN `SN_matches` as m ON u.unique_id = m.match_unique_id ";
		$sql_query .= "INNER JOIN `SN_objects` as o ON o.object_id = m.match_object_id ";
		$sql_query .= "WHERE l.list_id = ? GROUP BY u.unique_ra";

		if($stmt->prepare($sql_query)) {
			$stmt->bind_param("i", $selectedListID);
			$stmt->execute();
			$stmt->bind_result($o_id, $o_name, $o_ra, $o_dec, $o_ra_hmsdms, $o_dec_hmsdms, $o_type, $o_mag, $o_red, $o_phase);
			while($stmt->fetch()){
				array_push($objects_array, array("object_id" => $o_id, "object_name" => $o_name, "object_ra" => $o_ra, "object_dec" => $o_dec, "object_ra_hmsdms" => $o_ra_hmsdms, "object_dec_hmsdms" => $o_dec_hmsdms, "object_type" => $o_type, "object_mag" => $o_mag, "object_redshift" => $o_red, "object_phase" => $o_phase));
			}
			$stmt->close();
		}
	} else {
		$sql_query = "SELECT unique_id, GROUP_CONCAT(DISTINCT object_name) as object_name, unique_ra, unique_dec, unique_ra_hmsdms, unique_dec_hmsdms, GROUP_CONCAT(DISTINCT object_type) as object_type, GROUP_CONCAT(DISTINCT object_disc_mag) as object_disc_mag, GROUP_CONCAT(DISTINCT object_redshift) as object_redshift, GROUP_CONCAT(DISTINCT object_phase) as object_phase ";
		$sql_query .= "FROM `SN_lists` as l ";
		$sql_query .= "INNER JOIN `SN_contains`as c ON l.list_id = c.contain_list_id ";
		$sql_query .= "INNER JOIN `SN_uniques` as u ON c.contain_unique_id = u.unique_id ";
		$sql_query .= "INNER JOIN `SN_matches` as m ON u.unique_id = m.match_unique_id ";
		$sql_query .= "INNER JOIN `SN_objects` as o ON o.object_id = m.match_object_id ";
		$sql_query .= "WHERE l.list_id = ? AND l.list_owner_id = ? GROUP BY u.unique_ra";

		if($stmt->prepare($sql_query)) {
			$stmt->bind_param("ii", $selectedListID, $userID);
			$stmt->execute();
			$stmt->bind_result($o_id, $o_name, $o_ra, $o_dec, $o_ra_hmsdms, $o_dec_hmsdms, $o_type, $o_mag, $o_red, $o_phase);
			while($stmt->fetch()){
				array_push($objects_array, array("object_id" => $o_id, "object_name" => $o_name, "object_ra" => $o_ra, "object_dec" => $o_dec, "object_ra_hmsdms" => $o_ra_hmsdms, "object_dec_hmsdms" => $o_dec_hmsdms, "object_type" => $o_type, "object_mag" => $o_mag, "object_redshift" => $o_red, "object_phase" => $o_phase));
			}
			$stmt->close();
		}
	}
    
	array_push($results, $objects_array);
	for ($j = 0; $j < count($results[0]); $j++)
	{
		$uniq_id = $results[0][$j]['object_id'];
		$messages_array["$uniq_id"] = array();
		// fwrite($fp, $uniq_id . "\n");

		$f_select_query = "SELECT object_msg_hashed, match_unique_id FROM `SN_uniques` as u ";
		$f_select_query .= "INNER JOIN `SN_matches` as m ON u.unique_id = m.match_unique_id ";
		$f_select_query .= "INNER JOIN `SN_objects` as o ON o.object_id = m.match_object_id ";
		$f_select_query .= "WHERE u.unique_id = " . $uniq_id;
		fwrite($fp, $f_select_query . "\n");

		$res_msg = $mysqli->query($f_select_query);
		while($row_obj = $res_msg->fetch_assoc()) {
			fwrite($fp, $res_msg->num_rows . "\n");
			if(is_null($row_obj["object_msg_hashed"])) { 
				//join the SN_known_list with the relationship table connecting it to SN_uniques
				$query_msg = "SELECT * FROM `SN_known_list_match` AS k JOIN `SN_known_list` AS l ON k.kl_match_sn_id = l.sn_id";
				$query_msg .= " WHERE k.kl_match_unique_id = " . $row_obj["match_unique_id"];
				fwrite($fp, $query_msg . "\n");
				$res_msg_one = $mysqli->query($query_msg);
				$new_row_msg = $res_msg_one->fetch_assoc();
				$res_msg_one->free();

				$msg = array();
				$msg["title"] = $new_row_msg["sn_name"];
				$msg["link"] = "Not Available";
				$msg["description"] = "Not Available";
				$msg["update_time"] = $new_row_msg["sn_date"];
				$msg["type"] = "object";

				$msg["feed"] = array();
				// start third join on `SN_feeds`
				$query_feed = "SELECT * FROM `SN_feeds` WHERE feed_id = 7";
				$res_feed_one = $mysqli->query($query_feed);
				$new_row_feed = $res_feed_one->fetch_assoc();
				$res_feed_one->free();
				$msg["feed"]["name"] = $new_row_feed["feed_name"];
				$msg["feed"]["url"] = $new_row_feed["feed_url"];
				$msg["feed"]["description"] = $new_row_feed["feed_description"];
				array_push($messages_array["$uniq_id"], $msg);
			} 
			else {
				$query_msg = "SELECT * FROM `SN_messages` WHERE msg_hashed = '" . $row_obj["object_msg_hashed"];
				$query_msg .= "' AND msg_end_ts IS NULL LIMIT 1";
				fwrite($fp, $query_msg . "\n");
				$res_msg_two = $mysqli->query($query_msg);
				$row_msg = $res_msg_two->fetch_assoc();
				$res_msg_two->free();
				$msg = array();
				$msg["title"] = $row_msg["msg_title"];
				$msg["link"] = $row_msg["msg_link"];
				$msg["description"] = $row_msg["msg_description"];
				$msg["update_time"] = $row_msg["msg_update_ts"];
				$msg["type"] = $row_msg["msg_type"];
				$msg["feed"] = array();
				// start third join on `SN_feeds`
				$query_feed = "SELECT * FROM `SN_feeds` WHERE feed_id = " . $row_msg["msg_feed_id"];
				$res_feed_two = $mysqli->query($query_feed);
				$row_feed = $res_feed_two->fetch_assoc();
				$res_feed_two->free();
				$msg["feed"]["name"] = $row_feed["feed_name"];
				$msg["feed"]["url"] = $row_feed["feed_url"];
				$msg["feed"]["description"] = $row_feed["feed_description"];
			
				array_push($messages_array["$uniq_id"], $msg);
				//fwrite($fp, print_r($messages_array, true) . "\n");
			}
		}
		$res_msg->free();
	}

	array_push($results['messages'], $messages_array);
	fwrite($fp, print_r($results, true) . "\n");
	fclose($fp);
	$mysqli->close();

	header('Content-Type: application/json');
	echo json_encode($results);
	exit;
}

else{
	echo json_encode(array('ERROR'=>'Invalid access to listSN.php'));
	exit;
}

function addObjects($mysqli, $add_list_id, $objectIDs) {
	
	$sql = array(); 
	foreach( $objectIDs as $_id ) {
	    $sql[] = '('.$add_list_id .', '. $_id .')';
	}

	$add = "INSERT INTO `SN_contains` (contain_list_id, contain_unique_id) VALUES " . implode(",", $sql);
			
	$success = $mysqli->query($add);
	if($success){
		echo json_encode(array('success'=>1));
	}else{
		echo json_encode(array('failure'=>1));
	}
}

?>
