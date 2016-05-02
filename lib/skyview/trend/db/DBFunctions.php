<?php
	function connectToDB($host)
	{	
		//import file that has the DB info
		require("../db/dbinfo.php");
		
		if($host == "spectra"){
			$database = $dbinfo['spectra']['host'];
			$dbname = $dbinfo['spectra']['dbname'];
			$username = $dbinfo['spectra']['username'];
			$password = $dbinfo['spectra']['password'];
		
			//Connect to the DB
	
			$link = mysqli_connect($database, $username, $password, $dbname);
			if (mysqli_connect_errno()) {
			    printf("Connect failed: %s\n", mysqli_connect_error());
			    exit();
			}
			
			return $link;
			
		} // end if host == spectra
		
	} // end connectToDB()
?>