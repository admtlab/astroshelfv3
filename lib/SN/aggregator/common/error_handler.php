<?php
/*
============================================================================================
Filename: 
---------
error_handler.php

Description: 
------------
This PHP file is an error handler to record abnormal behaviors when running
such an aggregator. The errors are appended to log files in "./log" directory.

Di Bao
02/13/2013
ADMT Lab - Supernovae Project
============================================================================================
*/

function error_handler($error_message, $log_file){
	$timestamp = date("F j, Y, g:i a");
	$message = "\n===================================\n";
	$message .= "Error when running aggregator, " . strval($timestamp) . ":\n";
	$message .= $error_message . "\n";
	
	file_exists($log_file) or die('Could not find file ' . $log_file);
	$fp = fopen($log_file, "a");
	if($fp){
		fwrite($fp, $message);
		fclose($fp);
	}
}
?>