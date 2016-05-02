<?php
/*
============================================================================================
Filename: 
---------
querySN.php

Description: 
------------
This PHP file is a general server-side script handling HTTP requests.

Di Bao
02/25/2013
ADMT Lab - Supernovae Project
============================================================================================
*/

require_once("./funcs/.dbinfo.php");
require_once("./funcs/query_func_unit_testing.php");

if(isset($_POST['query'])){
	$offset = $_POST['offset'];
	$limit = $_POST['limit'];
	$orderby = $_POST['orderby'];
	$sort = $_POST['sort'];
	
	$mysqli = new mysqli($dbinfo['host'], $dbinfo['username'], $dbinfo['password'], $dbinfo['dbname']);
	if($mysqli->connect_error){
		$error_msg = "Could not connect to AstroDB. " . $mysqli->connect_errno . " :" . $mysqli->connect_error;
		die($error_msg);
	}
	$json_result = query_func($mysqli, $offset, $limit, $orderby, $sort, array());
	$mysqli->close();
	header('Content-Type: application/json');
	echo $json_result;
	exit;
}

elseif(isset($_POST['search'])){
	$offset = $_POST['offset'];
	$limit = $_POST['limit'];
	$orderby = $_POST['orderby'];
	$sort = $_POST['sort'];

	$mysqli = new mysqli($dbinfo['host'], $dbinfo['username'], $dbinfo['password'], $dbinfo['dbname']);
	if($mysqli->connect_error){
		$error_msg = "Could not connect to AstroDB. " . $mysqli->connect_errno . " :" . $mysqli->connect_error;
		die($error_msg);
	}
	
	if($_POST['search'] == 1){
		$name = $_POST['_name'];
		$json_result = query_func($mysqli, $offset, $limit, $orderby, $sort, array("name" => $name));
	}
	
	if($_POST['search'] == 2){
		$ra = $_POST['_ra'];
		$dec = $_POST['_dec'];
		$epsilon = $_POST['_epsilon'];
		$json_result = query_func($mysqli, $offset, $limit, $orderby, $sort, array("ra" => $ra, "dec" => $dec, "epsilon" => $epsilon));
	}
	
	$mysqli->close();
	header('Content-Type: application/json');
	//echo json_encode(array('ERROR'=>$json_result));
	echo $json_result;
	exit;
}

else{
	echo json_encode(array('ERROR'=>'Invalid access to querySN.php'));
	exit;
}

?>