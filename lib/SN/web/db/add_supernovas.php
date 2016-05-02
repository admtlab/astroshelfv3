<?php
/*
============================================================================================
Filename: 
---------
add_supernovas.php

Description: 
------------
This PHP file handles parsing a batch text file containing a list of supernovas and their
relevant information and storing it in the database.

Nikhil Venkatesh
09/29/2013
ADMT Lab - SNeT v0.2
============================================================================================
*/

require_once("./funcs/.dbinfo.php");
include('../supernova_parse/funcs.php');

if (isset($_POST['addNewSupernovas'])) {
	$filename = $_POST['filename'];

	//Create connection
	$link = mysqli_connect($dbinfo['host'], $dbinfo['username'], $dbinfo['password'], $dbinfo['dbname']);

	//Check connection
	if (mysqli_connect_errno($link)) {
	  //echo "Failed to connect to MySQL: " . mysqli_connect_error();
	} else {
	  //echo "Connected to database sn_astroshelf";
	  processFile(parseFile($filename), $link);
	}
}

function parseFile($fname){
	if($lines = file($fname)) {		// split the file into lines and store it in an array
		return $lines;
	}	
}

/* Order of each item in each line
	Name      | RA (J2000)  | Dec (J2000) | Discovery | Mag  | Redshift | Type     | Spec.    | Phase | Instrument     | Notes  
*/

function processFile($lines, $link){
	$t_ra_str = "";		// temp variable to hold the RA string version
	$t_dec_str = "";	// temp variable to hold the Dec string version

	foreach($lines as $line) {	// loop through the line array and process each line
		$line_ar = preg_split("/\s{1,2}\|\s{1,2}/", trim($line));	// split the line based on max 2 spaces before or after a pipe - takes into account entries that don't exist

		// create temp variables to hold each of the fields to add to the appropriate tables
		$t_name = $line_ar[0];	// store the name

		$t_ra_split = preg_split("/\s{1}|:/", trim($line_ar[1]));	// holds the ra split into hours, minutes, and seconds
		calculateRA($line_ar[1], $t_ra_split, -1);		// calculate the RA and store it back into $line_ar[1]
		convertRAtoString($t_ra_str, $t_ra_split, -1);	// convert the RA into a string format and store it in $t_ra_str

		$t_dec_split = preg_split("/\s{1}|:/", trim($line_ar[2]));	// holds the dec split into degrees, minutes, and seconds
		$t_dec_fchar = $t_dec_split[0][0];	//get the first character of the dec - this tells if the dec is positive or negative
		// check to see the sign of the declination (positive or negative)
		if(preg_match("/\+/", $t_dec_fchar))
			calculateDEC($line_ar[2], $t_dec_split, "pos", -1);
		elseif(preg_match("/-/", $t_dec_fchar))
			calculateDEC($line_ar[2], $t_dec_split, "neg", -1);

		convertDECtoString($t_dec_str, $t_dec_split, -1);		//$t_dec_str now holds the string version of the declination

		//check to see if the date is empty, save the discovery date back to line_ar[3] 
		if(trim($line_ar[3]) == '')
			$line_ar[3] = 'NULL';
		else
			convertDatetoTimestamp($line_ar[3]);

		$t_mag = (trim($line_ar[4]) == '') ? 'NULL' : $line_ar[4];	//check to see if the mag is empty
		$t_redshift = (preg_match("/\s{4,}/", $line_ar[5])) ? 'NULL' : preg_replace("/[^0-9,.]/", "", trim($line_ar[5])); //check to see if the redshift is empty otherwise , if it isn't remove all non numeric characters (except decimal point)
		$t_type = (trim($line_ar[6]) == '') ? 'NULL' : $line_ar[6];

		//save the spectrum date back to line_ar[7]
		if(trim($line_ar[7]) == '')
			$line_ar[7] = 'NULL';
		else
			convertDate($line_ar[7]);

		$t_phase = $line_ar[8];
		$t_instrument = (trim($line_ar[9]) == '') ? 'NULL' : $line_ar[9];
		$t_notes = $line_ar[10];

		/* Store the information in arrays that will be passed to helper functions to add to the database */
		$SN_k_list_ar = array($t_name, 'NULL', $line_ar[3], $line_ar[1], $line_ar[2], $t_ra_str, $t_dec_str, $t_type, $t_mag, $t_phase, $t_redshift, 'NULL', $t_instrument, $line_ar[7], $t_notes);
		$SN_obj_list_ar = array($line_ar[1], $line_ar[2], $t_name, 'NULL', $t_type, $t_redshift, $t_mag, $t_phase);

		/* Retain the inserted id from the SN_known_list table. */
		$last_inserted_id = updateKnownListTable($link, $SN_k_list_ar);

		/* Update the unique list array with the inserted id from the known list table for use in the matches table */
		$SN_uni_list_ar = array($line_ar[1], $line_ar[2], $t_ra_str, $t_dec_str, $last_inserted_id);

		updateObjectsTable($link, $SN_obj_list_ar); 
      	updateUniquesTable($link, $SN_uni_list_ar);
	}
	
	echo "Parsing complete.";
}


?>
