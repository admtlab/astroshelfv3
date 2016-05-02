<?php
/*
============================================================================================
Filename: 
---------
listmanagementSN.php

Description: 
------------
This PHP file handles incoming server requests to modify lists.

Nikhil Venkatesh
12/02/2013
ADMT Lab - Supernovae Project
============================================================================================
*/

require_once("./funcs/.dbinfo.php");
require_once("./funcs/query_func_unit_testing.php");

function addObjects($mysqli, $add_list_id, $objectIDs) {
	
	$sql = array(); 
	foreach( $objectIDs as $_id ) {
	    $sql[] = '('.$add_list_id .', '. $_id .')';
	}

	$add = "INSERT INTO `SN_contains` (contain_list_id, contain_unique_id) VALUES " . implode(",", $sql);
			
	if( $success = $mysqli->query($add) ) {
		echo json_encode( array( 'Success'=>1 ) );
	} else{
		echo json_encode( array( 'Failure'=>1 ) );
	}

}

/*
 * Handle updating the new list with the new name.
 */

if ( isset( $_POST['renameList'] ) ) {
	// Obtain the parameters passed in the request
	$new_list_name = $_POST['_name'];
	$list_id = $_POST['_list_id'];

	// Obtain the MySQL connection
	$mysqli = new mysqli($dbinfo['host'], $dbinfo['username'], $dbinfo['password'], $dbinfo['dbname']);
	if ( $mysqli->connect_error ) {
		$error_msg = "Could not connect to AstroDB. " . $mysqli->connect_errno . " :" . $mysqli->connect_error;
		die($error_msg);
	}

	// Construct the update statement to update the list name
	$update_stmt = "UPDATE `SN_lists` SET list_name = '" . mysqli_real_escape_string($mysqli, $new_list_name) . "'";
	$update_stmt .= " WHERE list_id = " . $list_id;

	// Execute the statement
	$success = $mysqli->query($update_stmt);
	if ( $success )
		echo json_encode( array( 'Success'=>1 ) );
	else
		echo json_encode( array( 'Failure'=>1 ) );
}

/*
 * Handle updating the given list.
 */
else if ( isset( $_POST['update'] ) ) {
	// Store the ID of the list to updated and the object IDs.
	$update_list_id = $_POST['_list_id'];
	$object_ids = json_decode( stripslashes( $_POST['_objectIDs'] ) );

	// Create the $mysqli object.
	$mysqli = new mysqli($dbinfo['host'], $dbinfo['username'], $dbinfo['password'], $dbinfo['dbname']);
	if ( $mysqli->connect_error ) {
		$error_msg = "Could not connect to AstroDB. " . $mysqli->connect_errno . " :" . $mysqli->connect_error;
		die($error_msg);
	}

	// First retrieve all of the metadata associated with the list.
	$stmt = $mysqli->stmt_init();
	$stmt->prepare("SELECT * FROM `SN_lists` WHERE list_id = ?");
	$stmt->bind_param("i", $update_list_id);
	$stmt->execute();
	$stmt->bind_result($list_id, $list_name, $list_description, $list_owner, $list_owner_id, $list_create_time, $list_update_time, $list_delete_time);
	$stmt->fetch();
	$stmt->close();

	// Second, delete the list completely.
	$delete_stmt = "DELETE FROM `SN_lists` WHERE list_id = " . $update_list_id;
	if ( $success = $mysqli->query($delete_stmt) ) {
		// Now reinsert the list and the objects that it contains
		$insert_query_one = "INSERT INTO `SN_lists` VALUES( " . $list_id . ", '" . $list_name . "', '" . $list_description . "', '" . $list_owner .  "', ";
		$insert_query_one .= $list_owner_id . ", '" . $list_create_time . "', '" . $list_update_time . "', '" . $list_delete_time . "' )";

		if ( $success_one = $mysqli->query($insert_query_one) ) {	// if the insert into SN_lists is successful
			// Loop through the objects and add each one into the SN_contains table
			for($i = 0; $i < count($object_ids); ++$i) {
				$insert_query_two = "INSERT INTO `SN_contains` VALUES( " . $update_list_id . ", " . $object_ids[$i] . " )";
				if ( $success_two = $mysqli->query($insert_query_two) ) {

				} else {
					echo json_encode( array( 'Failure in inserting into SN_contains' => 1 ) );
					return;
				}
			}
		} else {
			echo json_encode( array( 'Failure in inserting into SN_lists' => 1 ) );
			return;
		}
	} else
		echo json_encode( array( 'Failure' => 1) );
}

/*
 * Handle deleting the given list.
 */
else if ( isset( $_POST['delete'] ) ) {
	// Store the ID of the list to be deleted.
	$delete_list_id = $_POST['_list_id'];

	// Create the $mysqli object.
	$mysqli = new mysqli($dbinfo['host'], $dbinfo['username'], $dbinfo['password'], $dbinfo['dbname']);
	if ( $mysqli->connect_error ) {
		$error_msg = "Could not connect to AstroDB. " . $mysqli->connect_errno . " :" . $mysqli->connect_error;
		die($error_msg);
	}

	// Construct the delete statement
	$delete_stmt = "DELETE FROM `SN_lists` WHERE list_id = " . $delete_list_id;

	if ( $success = $mysqli->query($delete_stmt) ) 
		echo json_encode( array( 'Success' => 1 ) );
	else 
		echo json_encode( array( 'Failure' => 1) );

}

/*
 * Handle saving a new list given the objects.
 */
else if ( isset( $_POST['save'] ) ) {
	// Store the object IDs and list info.
	$list_info = json_decode( stripslashes( $_POST['_listInfo'] ) );
	$user_id = intval( $_POST['_userID'] );
	$object_ids = json_decode( stripslashes( $_POST['_objectIDs'] ) );

	// Create the $mysqli object.
	$mysqli = new mysqli($dbinfo['host'], $dbinfo['username'], $dbinfo['password'], $dbinfo['dbname']);
	if ( $mysqli->connect_error ) {
		$error_msg = "Could not connect to AstroDB. " . $mysqli->connect_errno . " :" . $mysqli->connect_error;
		die($error_msg);
	}

	// First retrieve user info from the `user` table.
	$stmt = $mysqli->stmt_init();
	$stmt->prepare("SELECT fname FROM `user` WHERE user_id = ?");
	$stmt->bind_param("i", $user_id);
	$stmt->execute();
	$stmt->bind_result($author_name);
	$stmt->fetch();
	$stmt->close();

	// Secondly, insert the values into the SN_lists table
	$save_stmt = "INSERT INTO `SN_lists` (list_name, list_description, list_owner, list_owner_id, list_create_ts, list_update_ts, list_delete_ts)";
	$save_stmt .= " VALUES ('" . mysqli_real_escape_string($mysqli, $list_info[0]) . "', '";
	$save_stmt .= mysqli_real_escape_string($mysqli, $list_info[1]) . "', '";
	$save_stmt .= $author_name . "', ";
	$save_stmt .= $user_id . ", ";
	$save_stmt .= "NOW(), NULL, NULL)";

	if( $success = $mysqli->query($save_stmt) ) {
		$insert_id = $mysqli->insert_id;
		addObjects( $mysqli, $insert_id, $object_ids );	//call the method to add objects from the list into SN_contains
	} else {
		echo json_encode( array( 'Failure'=>1 ) );
		exit;
	}
}

/*
 * Handle retrieving all lists for all users in the database.
 */
else if ( isset( $_POST['retrieveAll'] ) ) {

	// Variables to use in this function
	$lists = array();	// store all of the lists and their information

	// Initialize the database connection
	$mysqli = new mysqli($dbinfo['host'], $dbinfo['username'], $dbinfo['password'], $dbinfo['dbname']);
	if ( $mysqli->connect_error ) {
		$error_msg = "Could not connect to AstroDB. " . $mysqli->connect_errno . " :" . $mysqli->connect_error;
		die($error_msg);
	}

	// Create a prepared statement
	$stmt = $mysqli->stmt_init();
	if ( $stmt->prepare("SELECT list_id, list_name, list_description, list_owner, list_owner_id FROM `SN_lists`")) {
		$stmt->execute();
		$stmt->bind_result($list_id, $list_name, $list_description, $list_owner, $list_owner_id);
		while( $stmt->fetch() ) {
			// Add the lists to the master list array
			array_push($lists, array($list_id, $list_name, $list_description, $list_owner, $list_owner_id));
		}
		$stmt->close();
	}
	
	// Return the populated list array
	header('Content-Type: application/json');
	echo json_encode($lists);
	exit;
}

?>