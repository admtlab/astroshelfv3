<?php

	header("Access-Control-Allow-Origin: http://astro.cs.pitt.edu");
	//require('/var/www/html/KLogger.php');

	function Init(){
		//make connection, select DB, select table from Mongo db
		$m = new MongoClient();		// connect
		$db = $m->astro;			// select a database
		$collection = $db->sdss;	// select a collection
		
		$dir = getcwd();
		
		return $collection;
	}

	function inputChecking(){
	//////////////////////////////////////////////////////
	//input checking Here!

	//////////////////////////////////////////////////////
	}

	function findwithMBR($collection,$RA_min,$RA_max,$DEC_min,$DEC_max,$RA_cent, $DEC_cent,$ra_offset, $dec_offset, $mylog){
		
		$lowerLeft = array((float)$DEC_min - (float)$dec_offset, (float)$RA_min - (float)$ra_offset);
		$upperRight= array((float)$DEC_max + (float)$dec_offset, (float)$RA_max + (float)$ra_offset);
				
		#print_r($lowerLeft); print_r($upperRight);

		//$mylog->logInfo("lowerLeft", $lowerLeft);
		//$mylog->logInfo("upper right", $upperRight);
		
		$cond = array('loc' => array('$within' => array('$box' => array($lowerLeft,$upperRight))));
		#var_dump($cond);
		
		#$cond = array('loc' => array('$near' => array('$geometry' => array('type' => 'Point', 'coordinates' => array($DEC_cent, $RA_cent) ) ), '$maxDistance' => (float)$ra_offset) );
		
		#var_dump($cond);
		
		$cursor = $collection->find($cond);
		
		return $cursor;
	}	

	function queryMongoDBwithMBR($RA_min_deg,$DEC_min_deg,$RA_max_deg,$DEC_max_deg, $RA_cent_deg, $DEC_cent_deg,$mylog){
		//just copy from first_config and hard code here first
		
		$ra_offset = 0.5;//(int)(153/2);
		$dec_offset = 0.5;//(int)(153/2);
		
		//make connection, select DB, select table from Mongo db, 
		$collection = init();

		//convert input to ra and dec
//		echo "(not converted) DEC_min=".$DEC_min_deg." RA_min=".$RA_min_deg." DEC_max=".$DEC_max_deg." RA_max=".$RA_max_deg."\n";
		
		$RA_min=(float)($RA_min_deg);
		$DEC_min=(float)($DEC_min_deg);
		$RA_max=(float)($RA_max_deg);
		$DEC_max=(float)($DEC_max_deg);
		$RA_cent = (float)($RA_cent_deg);
		$DEC_cent = (float)($DEC_cent_deg);
		
		#echo "$RA_Min, $RA_Max, $DEC_Min, $DEC_Max";
		#echo "<html></br></html>";
		//$mylog->logInfo("RA_min:hours:", $RA_min);
		//$mylog->logInfo("DEC_min:hours:", $DEC_min);
		//$mylog->logInfo("RA_max:hours:", $RA_max);
		//$mylog->logInfo("DEC_max:hours:", $DEC_max);
		
		

		// find from the collection
		$cursor=findwithMBR($collection,$RA_min,$RA_max,$DEC_min,$DEC_max,$RA_cent, $DEC_cent, $ra_offset,$dec_offset, $mylog);

		return $cursor;
	}

	function printResult($cursor){
		
		$count=1;
		$ret = array();
		foreach ($cursor as $doc) {
				
				$link = "http://das.sdss.org/imaging/" . (int)substr($doc["path"],4,6) . "/" . (int)$doc["rerun"] . "/Zoom/" . substr($doc["path"],12,1) . "/fpC-" . substr($doc["path"],4,6) . "-" .  substr($doc["path"],12,1) . "-" . (int)$doc["rerun"] . "-" . substr($doc["path"],14,4);	
		
				$params = array( (float)$doc["crval1"], (float)$doc["crval2"], (float)$doc["crpix1"], (float)$doc["crpix2"], (float)$doc["cd1_1"], (float)$doc["cd1_2"], 
					(float)$doc["cd2_1"], (float)$doc["cd2_2"], substr($doc["ctype1"], 1, count($doc["ctype1"])), substr($doc["ctype2"], 1, count($doc["ctype1"])), 
						(float)$doc["naxis1"], (float)$doc["naxis2"] );

		array_push($ret, $link, $params);
		}
		echo json_encode($ret);
	}

	function QueryMongDB(){//a function that support MBR,kNN,circular range query in MongoDB
		//use switch!!
		//$cursor=queryMongoDBwithMBR($RA_min_deg,$DEC_min_deg,$RA_max_deg,$DEC_max_deg);
	}

//this line check whether php-mongo driver installed

	//////read input from URL
//	$RA_min_deg=2;
//	$DEC_min_deg=1;
//	$RA_max_deg=10;
//	$DEC_max_deg=10;
	
	$RA_min_deg = $_GET['RAMin'];
	$DEC_min_deg = $_GET['DecMin'];
	$RA_max_deg = $_GET['RAMax'];
	$DEC_max_deg = $_GET['DecMax'];
	$RA_cent_deg = $_GET['RACenter'];
	$DEC_cent_deg = $_GET['DecCenter'];
		
	$mylog = 0;//KLogger::instance(dirname(__FILE__), KLogger::DEBUG);
	
	/////call the queryMongoDB function to query the mongoDB
	$cursor=queryMongoDBwithMBR($RA_min_deg,$DEC_min_deg,$RA_max_deg,$DEC_max_deg, $RA_cent_deg, $DEC_cent_deg, $mylog);
	
	// iterate through the results //lack of flexibility, may change later 
	printResult($cursor);
?>

