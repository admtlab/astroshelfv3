<?php

	//- ===================================  -->
	//-- = File Written By Timothy Luciani = -->
	//--          = 3/18/13 				 -->
	//-- = Modified 4/10/13 				 -->
	//-- =================================== -->
	
	$dir = getcwd();
	date_default_timezone_set('America/New_York');
	
	 require('../../klogger/KLogger.php');
	// PhpConsole::start(true, true, dirname(__FILE__));
	
	$log = KLogger::instance(dirname(__FILE__), KLogger::INFO);
	// files that has connectToDB function
	require("../db/DBFunctions.php");
	
	include "../db/fetchData.php";
	include "./colorConversions.php";
	include "./createTrend.php";
	include "../../db/remote/general_sdss_query.php";
	
	error_reporting(-1);
	ini_set("display_errors", 1);
	// ini_set("memory_limit","128M");	
	### Main entry point into the script ###
	
	//connect to DB
	$link = connectToDB("spectra");
	gc_enable(); // Enable Garbage Collector
	
	$query = "";	
	
	$command = $_POST['funct'];
	$gran = $_POST['granularity'];	
	
	#objects
	$objID = json_decode($_POST['id'], true);	
	$wave_type = $_POST['wavetype'];
	
	# sorting
	$by = $_POST['by'];
	$order = $_POST['order'];
	
	# colors
	$left_color = $_POST['left'];
	$right_color = $_POST['right'];
	
	$inc = 0;
	$line_names = null;

	try{
		switch($command){
			
			case "constructFromResults":
								
				# colors for both ends of the spectrum
				$color_left = HEXtoRGB($left_color);
				$color_right = HEXtoRGB($right_color);
				
				# get the redshift values of our spectra
				$redshift = array();
				$rwave	  = array();
				
				# get the ra/dec, objid
				$coords = array();
				$objid = array();
				$specid = array();
				
				#min and max wave values
				$min = 0;
				$max = 0;
				
				# make directory to work in
				$time_id = microtime(true) * 10000;
				
				exec("mkdir /var/www/html/trend/$time_id", $out, $ret);
				
				# TODO: Fix by making a progress text file for every folder
				
				$list = querySDSS($objID, $time_id, $inc, $link, $log);
				$line_names = array();
				
				/*#temp fix for ascending / descending but breaks color scheme 
				if($order == 'ASC'){
					$order = 'DESC';
				} else {
					$order = 'ASC';
				}*/

				# find the common wave bin range 
				$query = "SELECT S.OBJID, S.SPEC_OBJID, S.RA, S.DECL, S.REDSHIFT as Z ";  
				$query .= "FROM sdssSpectra S WHERE S.OBJID IN ('".join("','",$list)."') ";
				$query .= "ORDER BY $by $order";
				
				$ret = mysqli_query($link,$query) or die("error at line 73: ".mysqli_error($query));
				
				$results = array();
				
				while($line = mysqli_fetch_assoc($ret)){
				    
					$results[] = $line;
					
					$objid[] = $line['OBJID'];
					$specid[] = $line['SPEC_OBJID'];
					$coords[] = array($line['RA'], $line['DECL']);
					
					$redshift[] = $line['Z'];
				
				}
				
				/* free result set */
				mysqli_free_result($ret);
				
				# query for the min and max
				$query = "SELECT MAX(S.MAX_WAVE) as maxWAVE, MIN(S.MIN_WAVE) as minWAVE, ";
				$query .= "MAX(S.MAX_REST) as maxREST, MIN(S.MIN_REST) as minREST ";
				$query .= "FROM sdssSpectra S WHERE S.OBJID IN ('".join("','",$list)."')";
				
				#echo "$query\n";
				
				$ret = mysqli_query($link,$query) or die("error: at line 94".mysqli_error($ret));
				
				$bounds = array();
				while($line = mysqli_fetch_assoc($ret)){
				    $bounds[] = $line;
				}
				/* free result set */
				mysqli_free_result($ret);
				
				# min and max common wavelength values
				$max_obs = $bounds[0]['maxWAVE'];
				$min_obs = $bounds[0]['minWAVE'];
				
				# min and max common wavelength values
				$max_rest = $bounds[0]['maxREST'];
				$min_rest = $bounds[0]['minREST'];
				
				# we want to construct a binning based on the granularity
				# that encapsalates the global spectra range of our objects
				
				if($wave_type == "rest"){
					$min = $min_rest;
					$max = $max_rest;
				}else{
					$min = $min_obs;
					$max = $max_obs;
				}
				
				$rwave = range($min,$max,$gran);
				
				for($i = 0; $i < count($list); $i+=500){
				
					/* check if there are still 500 to query for */
					$amount = (count($list) - $i >= 500 ? 500 : (count($list) - $i)-1 );
					
			 		/* get the objects requested from tables via query */
					$query = "SELECT * FROM sdssSpectraWaveLen1 ";
					$query .= "NATURAL RIGHT JOIN (SELECT OBJID FROM sdssSpectra S ";
					$query .= "WHERE OBJID IN ('".join("','",$list)."') ORDER BY $by $order LIMIT $i, $amount) AS M ";
					$query .= "NATURAL LEFT JOIN sdssSpectraWaveLen2 AS N ";
					$query .= "NATURAL LEFT JOIN sdssSpectraWaveLen3 AS O ";
					$query .= "NATURAL LEFT JOIN sdssSpectraWaveLen4 AS P ";
					$query .= "NATURAL LEFT JOIN sdssSpectraWaveLen5 AS Q ";
					$query .= "NATURAL LEFT JOIN sdssSpectraWaveLen6 AS R ";
					// $query .= "WHERE R.OBJID IN ('".join("','",$list)."')";
					
					$wave = mysqli_query($link,$query) or die("error at line 129: ".mysqli_error($link,$query));
		 			
					/* get the objects requested from tables via query */
					$query = "SELECT * FROM sdssSpectraFlux1 ";
					$query .= "NATURAL RIGHT JOIN (SELECT OBJID FROM sdssSpectra S ";
					$query .= "WHERE OBJID IN ('".join("','",$list)."') ORDER BY $by $order LIMIT $i, $amount ) AS M ";
					$query .= "NATURAL LEFT JOIN sdssSpectraFlux2 AS N ";
					$query .= "NATURAL LEFT JOIN sdssSpectraFlux3 AS O ";
					$query .= "NATURAL LEFT JOIN sdssSpectraFlux4 AS P ";	
					$query .= "NATURAL LEFT JOIN sdssSpectraFlux5 AS Q ";
					$query .= "NATURAL LEFT JOIN sdssSpectraFlux6 AS R ";
					
					$flux = mysqli_query($link,$query) or die("error at line 142: ".mysqli_error($link,$query));
					
					construct_image($wave, $flux, $wave_type, $rwave, array_slice($redshift, $i, $amount), 
						$color_left, $color_right, $min, $max, $i, 
						$line_names, $time_id, count($redshift), $inc, $log);
				
					/* free result set */
					mysqli_free_result($flux);
					
					/* free result set */
					mysqli_free_result($wave);
						
				}
				
				# echo line names, width and height
				echo json_encode( array($line_names, $objid, $specid, $coords, 
					count($rwave), count($line_names), $time_id ) ) ;
				
				
				break;
			
			case "reSort":
				
				$query = "SELECT S.SPEC_OBJID ";  
				$query .= "FROM sdssSpectra S WHERE S.OBJID IN ('".join("','",$objID)."') ";
				$query .= "ORDER BY $by $order";
				
				$ret = mysqli_query($link,$query) or die("error at line 73: ".mysqli_error($query));
				
				$new_order = array();
				while($line = mysqli_fetch_assoc($ret)){
				    $new_order[] = $line['SPEC_OBJID'];
				}
				
				echo json_encode( array($new_order) );
				
				/* free result set */
				mysqli_free_result($ret);
				
				break;
				
			default :
				break;
		} // end switch
			
	} // end try
	
	catch (Exception $e) {
		echo 'Caught exception: ',  $e->getMessage(), "\n";
	}
	
	gc_disable(); // Disable Garbage Collector
		
?>
