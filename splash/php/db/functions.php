<?php
	function connectToDB($host)
	{
		//import file that has the DB info
		require("info.php");
		
		if($host == "first"){
			$database = $dbinfo['first']['host'];
			$dbname = $dbinfo['first']['dbname'];
			$username = $dbinfo['first']['username'];
			$password = $dbinfo['first']['password'];
		}elseif($host == "lsst"){
			$database = $dbinfo['lsst']['host'];
			$dbname = $dbinfo['lsst']['dbname'];
			$username = $dbinfo['lsst']['username'];
			$password = $dbinfo['lsst']['password'];		
		}elseif($host == "astroDB"){
			$database = $dbinfo['astroDB']['host'];
			$dbname = $dbinfo['astroDB']['dbname'];
			$username = $dbinfo['astroDB']['username'];
			$password = $dbinfo['astroDB']['password'];			
		}else{
			;
		}
		
		//Connect to the DB
		if($host == "astroDB"){
			$db = new mysqli($database, $username, $password, $dbname);
			if($db->connect_errno){
				echo "Failed to connect to MySQL: (" . $db->connect_errno . ") " . $db->connect_error;
				exit;
			}
			return $db;
		}else{
			$db = mysql_connect($database, $username, $password);
			if($db):
			//If the connection was made then select the DB
				//echo "Successfully connected to database<br />";
				if(mysql_select_db($dbname)):
					//echo "Successfully selected database<br />";echo "yes!";
				else:
				//Error if the DB could not be select.
					die ("Could not select DB " . mysql_error());
				endif;
			else:
			//Error if the connection could not be made.
				die ("Could not connect to DB " . mysql_error());
			endif;
		}
	}
?>