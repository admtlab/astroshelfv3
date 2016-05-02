<?php

	header("Access-Control-Allow-Origin: http://astro.cs.pitt.edu");
	//require('/var/www/html/KLogger.php');

	function Init(){
		//make connection, select DB, select table from Mongo db
		$m = new MongoClient();		// connect
		$db = $m->astro;			// select a database
		$collection = $db->first;	// select a collection
		
		$dir = getcwd();
		
		return $collection;
	}

	function inputChecking(){
	//////////////////////////////////////////////////////
	//input checking Here!

	//////////////////////////////////////////////////////
	}

	function findwithMBR($collection,$RA_min,$RA_max,$DEC_min,$DEC_max,$ra_offset, $dec_offset, $mylog){
		
		$nRA = (float)$RA_min - (float)$ra_offset;
		$lowerLeft = array((float)$DEC_min - (float)$dec_offset, $nRA);
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

	function queryMongoDBwithMBR($RA_min_deg,$DEC_min_deg,$RA_max_deg,$DEC_max_deg,$scale, $mylog){
		//just copy from first_config and hard code here first
		
		$ra_offset = 1.0;//(int)(153/2);
		$dec_offset = 1.0;//(int)(153/2);
		
		//make connection, select DB, select table from Mongo db, 
		$collection = init();

		//convert input to ra and dec
//		echo "(not converted) DEC_min=".$DEC_min_deg." RA_min=".$RA_min_deg." DEC_max=".$DEC_max_deg." RA_max=".$RA_max_deg."\n";
		
		$RA_min=(int)($RA_min_deg);
		$DEC_min=(int)($DEC_min_deg);
		$RA_max=(int)($RA_max_deg);
		$DEC_max=(int)($DEC_max_deg);
		
		#echo "$RA_Min, $RA_Max, $DEC_Min, $DEC_Max";
		#echo "<html></br></html>";
		//$mylog->logInfo("RA_min:hours:", $RA_min);
		//$mylog->logInfo("DEC_min:hours:", $DEC_min);
		//$mylog->logInfo("RA_max:hours:", $RA_max);
		//$mylog->logInfo("DEC_max:hours:", $DEC_max);
		
		

		// find from the collection
		$cursor=findwithMBR($collection,$RA_min,$RA_max,$DEC_min,$DEC_max, $ra_offset,$dec_offset, $mylog);

		return $cursor;
	}

	function printResult($cursor){
		
		$count=1;
		$ret = array();
		foreach ($cursor as $doc) {
				

				
				#$link = "http://astro.cs.pitt.edu/FIRST/tim/images/" .  $doc["path"];
		
				$params = array( $doc["loc"]["ra"], $doc["loc"]["dec"], $doc["crpix1"], $doc["crpix2"], $doc["ctype1"], $doc["ctype2"],  $doc["cdelt1"], $doc["cdelt2"] );

		array_push($ret, $doc["path"], $params);
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
	$scale = $_GET['scale'];
	
	$mylog = 0;//KLogger::instance(dirname(__FILE__), KLogger::DEBUG);
	
	/////call the queryMongoDB function to query the mongoDB
	$cursor=queryMongoDBwithMBR($RA_min_deg,$DEC_min_deg,$RA_max_deg,$DEC_max_deg, $scale, $mylog);
	
	// iterate through the results //lack of flexibility, may change later 
	printResult($cursor);
?>

