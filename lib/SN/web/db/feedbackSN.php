<?php
/*
============================================================================================
Filename: 
---------
feedbackSN.php

Description: 
------------

Di Bao
04/07/2013
ADMT Lab - Supernovae Project
============================================================================================
*/

require_once("./funcs/.dbinfo.php");
require_once("./funcs/query_func.php");

if(isset($_POST['feedback'])){
	
	/* PHP File IO - log user's feedback */
	$feedback = $_POST['_feedback'];
	if($_POST['feedback'] == 1){
		$uni_id = $_POST['_uni_id'];
		$fname = "../feedback/feedback1.txt";
	}else if($_POST['feedback'] == 2){
		$msg_id = $_POST['_msg_id'];
		$fname = "../feedback/feedback2.txt";
	}else{
		;
	}

	$file = fopen($fname, "a+");
	if(!$file){
		echo json_encode(array('failure'=>1));
		exit;
	}
	
	if($_POST['feedback'] == 1)	$msg = "Feedback for unique_id " . $uni_id . ": \n";
	else if($_POST['feedback'] == 2)	$msg = "Feedback for msg_id " . $msg_id . ": \n";
	else	;
	$msg .= $feedback . "\n";
	$msg .= date('l jS \of F Y h:i:s A') . "\n";
	$msg .= "------------------------------------------------------\n\n";
	fwrite($file, $msg);
	fclose($file);
	
	/* PHP Mail() - email user's feedback to admin */
	$to = "illidan.bao@gmail.com";
	$subject = "Feedback type " . $_POST['feedback'] . " for SN Project";
	$message = $msg;
	$from = "SN_Project@astro.cs.pitt.edu";
	$headers = "From:" . $from;
	mail($to, $subject, $message, $headers);
	
	echo json_encode(array('success'=>1));
	exit;	
}

else if(isset($_POST['query'])){
	
	$mysqli = new mysqli($dbinfo['host'], $dbinfo['username'], $dbinfo['password'], $dbinfo['dbname']);
	if($mysqli->connect_error){
		$error_msg = "Could not connect to AstroDB. " . $mysqli->connect_errno . " :" . $mysqli->connect_error;
		die($error_msg);
	}
	
	$select = "SELECT * FROM `SN_messages` WHERE `msg_type` NOT IN ('object', 'annotation')";
	$result = $mysqli->query($select);
	if($result->num_rows == 0){
		echo json_encode(array('failure'=>1));
		exit;
	}
	
	$res = array();
	while($row = $result->fetch_assoc()){
		$record = array();
		
		$record["id"] = $row["msg_id"];
		$record["type"] = $row["msg_type"];
		$record["title"] = $row["msg_title"];
		$record["link"] = $row["msg_link"];
		$record["description"] = $row["msg_description"];
		
		array_push($res, $record);
	}
	echo json_encode($res);
	exit;
}

else{
	echo json_encode(array('ERROR'=>'Invalid access to feedbackSN.php'));
	exit;
}

?>