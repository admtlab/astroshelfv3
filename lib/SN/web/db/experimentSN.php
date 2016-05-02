<?php
/*
============================================================================================
Filename: 
---------
experimentSN.php

Description: 
------------
This PHP file handles adding/updating experiments.

Nikhil Venkatesh
10/17/2013
ADMT Lab - SNeT v0.2
============================================================================================
*/

require_once("./funcs/.dbinfo.php");

/*
{"strategy":0,"algorithm":2,"trainning":0,
"data1":[[{"_Name":"CRTS mag 19.8","_Id":282,"_RA":207.66501,"_Dec":14.7796,"_Type":"undefine","_Redshift":0.002,"_Mag":19.8,"_B-Peak":"2013-10-15","_Priority":0.5},{"_obsGap":1,"_obsTimes":1}]],
"data2":{"NNights":1,"LNights":[{"2013-10-16":"3a"}]},"user_id":120,"exp_name":"Test Experiment"}
*/

if (isset($_POST['saveExpParams'])){

	// Retrieve the JSON object passed in
	$json_obj = json_decode(stripslashes($_POST['json_str']), true);
	$insert_id;
	//
	$mysqli = new mysqli($dbinfo['host'], $dbinfo['username'], $dbinfo['password'], $dbinfo['dbname']);
	if($mysqli->connect_error){
		$error_msg = "Could not connect to " . $dbinfo['dbname'] . "." . $mysqli->connect_errno . " :" . $mysqli->connect_error;
		die($error_msg);
	}

	$fp = fopen("exptest.txt", "a");

	// Step 1: Make sure the experiment name entered does not already exist for the user
	$exists = false;
	$check_query = "SELECT * FROM `SN_experiment` WHERE user_id = " . $json_obj['user_id'] . " AND exp_name = '" . mysqli_real_escape_string($mysqli, $json_obj['exp_name']) . "'";
	if($result = $mysqli->query($check_query)) {
		if($result->num_rows == 0)
			$exists = false;
		else {
			$exists = true;
			echo json_encode(array('Error'=>'Experiment name already exists for user!'));
			$result->close();
			$mysqli->close(); // close connection before exiting
			exit;
		}
		$result->close();
	}


	$save = "INSERT INTO `SN_experiment` (user_id, exp_name, num_nights)";
	$save .= " VALUES (" . $json_obj['user_id'] . ", '";
	$save .= $json_obj['exp_name'] . "', ";
	$save .= $json_obj['data2']['NNights'] . ")";


	$success = $mysqli->query($save);
	if($success){
		$insert_id = $mysqli->insert_id;
		foreach($json_obj['data2']['LNights'] as $night)
		{
			$array_key_night = current(array_keys($night));

			// fwrite($fp, $array_key_night . "\n");
			// fwrite($fp, $night[$array_key_night] . "\n");

			preg_match('/\d+/', $night[$array_key_night], $hours);
			preg_match('/[A-Za-z]/', $night[$array_key_night], $half_night);

			$save = "INSERT INTO `SN_exp_nights` (exp_id, exp_night, num_hours, half_ab)";
			$save .= " VALUES (" . $insert_id . ", '" . $array_key_night . "', " . $hours[0] . ", '" . $half_night[0] . "')";
			if($success = $mysqli->query($save)) {
				// Insert successful
			} else {
				echo json_encode(array('Error'=>'Failure in inserting into SN_exp_nights.'));
				$mysqli->close();
				exit;
			}
			//fwrite($fp, "Save 2: " . $save . "\n");
		}

		fclose($fp); 
	} else {
		echo json_encode(array('Error'=>'Failure in inserting into SN_experiment.'));
		$mysqli->close();
		exit;
	}
	$ret_val = array();
	// Return JSON object with all of the user's experiments.
	$query = "SELECT exp_id, exp_name FROM `SN_experiment`";

	if ($result = $mysqli->query($query)) {

	    /* fetch object array */
	    while ($row = $result->fetch_row()) {
	    	$inner = array('_Id'=>$row[0]);
	        $ret_val[$row[1]] = $inner;
	    }

	    /* free result set */
	    $result->close();
	}
	echo json_encode($ret_val);
	/* Close connection. */
	$mysqli->close();
}

else if (isset($_POST['saveObjectInfo'])){
	// Retrieve the JSON object passed in
	$json_obj = json_decode(stripslashes($_POST['json_str']), true);
	//
	$exp_id = $_POST['exp_id'];
	//
	$ret_val = -1;

	$mysqli = new mysqli($dbinfo['host'], $dbinfo['username'], $dbinfo['password'], $dbinfo['dbname']);
	if($mysqli->connect_error){
		$error_msg = "Could not connect to " . $dbinfo['dbname'] . "." . $mysqli->connect_errno . " :" . $mysqli->connect_error;
		die($error_msg);
	}

	$fp = fopen("exptest.txt", "w");

	$obj_count = sizeof($json_obj['data1']);
	for ($i = 0; $i < $obj_count; $i++) {
		$obj_main_fields = $json_obj['data1'][$i][0];
		$obj_sup_fields = $json_obj['data1'][$i][1];

		$delete = "DELETE FROM `SN_exp_objects` WHERE exp_id = " . $exp_id . " AND unique_id = " . $obj_main_fields['_Id'];
		$mysqli->query($delete);

		$save = "INSERT INTO `SN_exp_objects` (exp_id, unique_id, b_peak, obs_gap, num_obs_times, priority)";
		$save .= " VALUES (" . $exp_id . ", " . $obj_main_fields['_Id'] . ", '" . $obj_main_fields['_B-Peak'] . "', ";
		$save .= $obj_sup_fields['_obsGap'] . ", " . $obj_sup_fields['_obsTimes'] . ", " . $obj_main_fields['_Priority'] . ")";
		
		fwrite($fp, $save . "\n");

		if($success = $mysqli->query($save)) {	// Step 1: Save the object supplemental info - ASSOCIATED WITH PARTICULAR EXPERIMENT
			$update_query = "UPDATE `SN_objects` AS o JOIN `SN_matches` AS m ON o.object_id = m.match_object_id ";
			$update_query .= "JOIN `SN_uniques` AS u ON u.unique_id = m.match_unique_id SET o.object_disc_mag = " . $obj_main_fields['_Mag'];
			$update_query .= ", o.object_redshift = " . $obj_main_fields['_Redshift'] . " WHERE u.unique_id = " . $obj_main_fields['_Id'];

			if($success = $mysqli->query($update_query)) {	// Step 2: Save the object's magnitude and redshift - ASSOCIATED WITH OBJECT ITSELF
				$ret_val = 1;
			} else {
				echo json_encode(array('Error'=>'Failure in inserting object information second time.'));
				$mysqli->close();
				exit;
			}
		} else {
			echo json_encode(array('Error'=>'Failure in inserting into SN_exp_objects first time.'));
			$mysqli->close();
			exit;
		}
	}

	fclose($fp);
	echo json_encode(array('Success'=>$ret_val));

	/* Close connection. */
	$mysqli->close();
}

else if (isset($_POST['updateExpParams'])) {
	// Retrieve the JSON object passed in
	$json_obj = json_decode(stripslashes($_POST['json_str']), true);

	$exp_id = $_POST['exp_id'];
	$fp = fopen("exptest.txt", "a");
	// Temporary object storage
	$exp_objects = array();

	$mysqli = new mysqli($dbinfo['host'], $dbinfo['username'], $dbinfo['password'], $dbinfo['dbname']);
	if($mysqli->connect_error){
		$error_msg = "Could not connect to " . $dbinfo['dbname'] . "." . $mysqli->connect_errno . " :" . $mysqli->connect_error;
		die($error_msg);
	}

	//////// SELECT the objects for this particular experiment, store it, and insert it after the deletion/insertion sequence ///////////
	$select_query = "SELECT * FROM `SN_exp_objects` WHERE exp_id = " . $exp_id;
		//fwrite($fp, $select_query . "\n");

	if($result = $mysqli->query($select_query)){
		while($row = $result->fetch_assoc()) {
			$exp_objects['objects'][] = array('unique_id'=>$row['unique_id'], 'b_peak'=>$row['b_peak'], 'obs_gap'=>$row['obs_gap'], 'obs_times'=>$row['num_obs_times'], 'priority'=>$row['priority']);
		}
	} // end select query check
	///////////////////////////////////
		//fwrite($fp, "WE ARE AFTER THE SELECT CHECK:\n");
		//fwrite($fp, print_r($exp_objects['objects'], true));
	// Remove the experiment first before inserting
	$delete = "DELETE FROM `SN_experiment` WHERE exp_id = " . $exp_id . " AND user_id = " . $json_obj['user_id'];
	$success = $mysqli->query($delete);
	if($success){
		// If successful, insert the updated experiment parameters with the same experiment ID
		$save = "INSERT INTO `SN_experiment` (exp_id, user_id, exp_name, num_nights)";
		$save .= " VALUES (" . $exp_id . ", " . $json_obj['user_id'] . ", '";
		$save .= $json_obj['exp_name'] . "', ";
		$save .= $json_obj['data2']['NNights'] . ")";


		$success = $mysqli->query($save);
		if($success){
			$insert_id = $mysqli->insert_id;
			foreach($json_obj['data2']['LNights'] as $night)
			{
				$array_key_night = current(array_keys($night));

				preg_match('/\d+/', $night[$array_key_night], $hours);
				preg_match('/[A-Za-z]/', $night[$array_key_night], $half_night);

				$save = "INSERT INTO `SN_exp_nights` (exp_id, exp_night, num_hours, half_ab)";
				$save .= " VALUES (" . $insert_id . ", '" . $array_key_night . "', " . $hours[0] . ", '" . $half_night[0] . "')";
				if($success = $mysqli->query($save)) {
					// Insert successful
				} else {
					echo json_encode(array('Error'=>'Failure in inserting into SN_exp_nights.'));
					$mysqli->close();
					exit;
				}
			}

			// Add the objects back into the table SN_exp_objects
			for($i = 0; $i < count($exp_objects['objects']); $i++){
				$insert = "INSERT INTO `SN_exp_objects` (exp_id, unique_id, b_peak, obs_gap, num_obs_times, priority)";
				$insert .= " VALUES (" . $exp_id . ", " . $exp_objects['objects'][$i]['unique_id'] . ", '" . $exp_objects['objects'][$i]['b_peak'] . "', " . $exp_objects['objects'][$i]['obs_gap'] . ", ";
				$insert .= $exp_objects['objects'][$i]['obs_times'] . ", " . $exp_objects['objects'][$i]['priority'] . ")";
				
				if($success = $mysqli->query($insert)) {
					// Insert successful
				} else {
					echo json_encode(array('Error'=>'Failure in inserting into SN_exp_objects.'));
					$mysqli->close();
					exit;
				}
			}

		} else {
			echo json_encode(array('Error'=>'Failure in inserting into SN_experiment.'));
			$mysqli->close();
			exit;
		}
	}else{
		echo json_encode(array('Error'=>'Failure in removing the experiment.'));
	}
	fclose($fp);
}

////////////////////////////////////////////////////////
// Used to retrieve the user's experiment IDs and names.
////////////////////////////////////////////////////////

else if (isset($_POST['retrieveExperiments'])){
	// Return the experiment names
	$userId = $_POST['user_id'];
	$exp_names = array();

	$mysqli = new mysqli($dbinfo['host'], $dbinfo['username'], $dbinfo['password'], $dbinfo['dbname']);
	if($mysqli->connect_error){
		$error_msg = "Could not connect to " . $dbinfo['dbname'] . "." . $mysqli->connect_errno . " :" . $mysqli->connect_error;
		die($error_msg);
	}

	$select_query = "SELECT exp_id, exp_name FROM `SN_experiment` WHERE user_id = " . $userId;
	if($result = $mysqli->query($select_query)){
		while($row = $result->fetch_row()) {
			$inner = array();
			array_push($inner, $row[0], $row[1]);
			array_push($exp_names, $inner);
		}

		$result->close();
	} else {

	}
	echo json_encode($exp_names);
	$mysqli->close();
}

/////////////////////////////////////////////////////
// Used to retrieve the user's experiment parameters
// and object info, if available.
/////////////////////////////////////////////////////

else if (isset($_POST['retrieveExpParams'])){
	// Return the experiment names

	$userId = $_POST['user_id'];
	$expId = $_POST['exp_id'];
	$exp_params = array();

	$mysqli = new mysqli($dbinfo['host'], $dbinfo['username'], $dbinfo['password'], $dbinfo['dbname']);
	if($mysqli->connect_error){
		$error_msg = "Could not connect to " . $dbinfo['dbname'] . "." . $mysqli->connect_errno . " :" . $mysqli->connect_error;
		die($error_msg);
	}

	$fp = fopen("exptest.txt", "a");
	
	// First query to obtain the nights from the experiment
	$select_query = "SELECT * FROM `SN_experiment` AS e JOIN `SN_exp_nights` AS n ON e.exp_id = n.exp_id WHERE e.exp_id = " . $expId; 
	$select_query .= " AND e.user_id = " . $userId;
	fwrite($fp, $select_query . "\n");

	if($result = $mysqli->query($select_query)){
		while($row = $result->fetch_assoc()) {
			$exp_params['num_nights'] = intval($row['num_nights']);
			$exp_params['nights'][] = array($row['exp_night']=>array('hours'=>$row['num_hours'], 'halfab'=>$row['half_ab']));
		}
		
		$result->close();
	}

	// Second query to obtain the objects from the experiment
	$select_query = "SELECT * FROM `SN_experiment` AS e JOIN `SN_exp_objects` AS eo ON e.exp_id = eo.exp_id WHERE e.exp_id = " . $expId; 
	$select_query .= " AND e.user_id = " . $userId;
	if($result = $mysqli->query($select_query)){
		if($result->num_rows == 0){
			// At this point, we assume that the experiment number of nights and the nights have been populated.
			// There were no objects associated with the experiment.
		} else {
			while($row = $result->fetch_assoc()) {
				$exp_params['objects'][] = array('unique_id'=>$row['unique_id'], 'b_peak'=>$row['b_peak'], 'obs_gap'=>$row['obs_gap'], 'obs_times'=>$row['num_obs_times'], 'priority'=>$row['priority']);
			}
		}
		
		$result->close();
	}

	fwrite($fp, print_r($exp_params['nights'], true));
	echo json_encode($exp_params);
	fclose($fp);
	$mysqli->close();
}

else if (isset($_POST['retrieveObjects'])){
	// Retrieve experiment parameters

	// Format: [user_id, exp_name]
	$json_obj = json_decode(stripslashes($_POST['json_str']), true);
	$userId = $json_obj[0];
	$exp_name = $json_obj[1];
}

?>