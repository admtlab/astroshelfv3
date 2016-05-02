<?php
/*
============================================================================================
Filename: 
---------
perform_curl.php

Description: 
------------
This PHP file is a general functiont to send HTTP request

Di Bao
02/13/2013
ADMT Lab - Supernovae Project
============================================================================================
*/
function perform_curl_operation($remote_url, $site_auth){
	$remote_contents = "";
	$empty_contents = "";
	
  	$curl_handle = curl_init();
  	if($curl_handle){
	  	curl_setopt($curl_handle, CURLOPT_URL, $remote_url);
	  	curl_setopt($curl_handle, CURLOPT_HEADER, false);
	  	curl_setopt($curl_handle, CURLOPT_FOLLOWLOCATION, true);
	  	curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
		
		if($site_auth != null){
			$the_username = $site_auth[0];
			$the_password = $site_auth[1];
			curl_setopt($curl_handle, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
			curl_setopt($curl_handle, CURLOPT_USERPWD, "$the_username:$the_password");
		}

	  	$remote_contents = curl_exec($curl_handle);
	  	curl_close($curl_handle);
	  
	  	if($remote_contents != false){
	  		return($remote_contents);
	  	}else{
	  		return($empty_contents);
	  	}
  	}else{
  		return($empty_contents);
  	}
}
?>