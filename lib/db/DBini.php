<?php
	/* Need to set the time limit and memory higher so can insert FIRST catalog
	 * --- don't run this script too often (preg_split takes a while)
	 * (Also keep in mind this script will delete and recreate an empty db for everybody)
	 */
       ini_set("memory_limit","500M");
			 set_time_limit("60"); // set to 60 sec instead of 30 sec
       //import file that has the DB info
       require("dbinfo.php");
       //Connect to the DB
        $db = mysql_connect($database, $username, $password);
       if($db):
       //If the connection was made then select the DB
            echo "Successfully connected to database<br />";
            if(mysql_select_db($dbname)):
                  echo "Successfully selected database<br />";
            else:
            //Error if the DB could not be select.
                  die ("Could not select DB " . mysql_error());
            endif;
       else:
       //Error if the connection could not be made.
               die ("Could not connect to DB " . mysql_error());
       endif;
       
       //drop the tables
      mysql_query("DROP TABLE Objects");
      mysql_query("DROP TABLE Files");
	  	
	  	mysql_query("DROP TABLE FIRSTimages");
	  	mysql_query("DROP TABLE Annotations");
			mysql_query("DROP TABLE FIRSTcatalog");
      
      //create the Objects table: Object_id | RA | Declination | z | Type |  Name | Info
      $r = mysql_query(
            "CREATE TABLE Objects (
			Object_id int NOT NULL AUTO_INCREMENT PRIMARY KEY,
			RA float NOT NULL,
			Declination float NOT NULL,
			z float,
			Type char(30),
			Name char(30),
			Info text
			)") 
            or die ("Invalid: " . mysql_error());
      //create the Annotations table: Anno_id | Object_id  | File_id | Subject | Body | DateTime
      $r = mysql_query(
            "CREATE TABLE Annotations (
			Anno_id int NOT NULL AUTO_INCREMENT PRIMARY KEY,
			Object_id char(30) NOT NULL,
			FileName char(30),
			Subject char(255) NOT NULL,
			Body text,
			DateTime datetime NOT NULL
			)") 
            or die ("Invalid: " . mysql_error());
      //create the Files table: FileName, FileType
      $r = mysql_query(
            "CREATE TABLE Files (
      FileName char(30) NOT NULL PRIMARY KEY,
      FileType char(30) NOT NULL)")
            or die ("Invalid: " . mysql_error());
			// Create FIRSTcatalog Table: RA, Dec, Ps, Fpeak, Fint, RMS, Maj, Min, PA, fMaj, fMin, fPA, Field
			$r = mysql_query(
						"CREATE TABLE FIRSTcatalog (
							RA float, 
							Declination float, 
							Ps float, 
							Fpeak float,
							Fint float,
							RMS float,
							Maj float,
							Min float,
							PA float, 
							fMaj float, 
							fMin float, 
							fPA float, 
							Field char(15))")
							or die ("Invalid: " . mysql_error());
			// Populate the FIRSTcatalog Table
			$catalog = file('/afs/cs.pitt.edu/projects/vis/visweb/webtest/astroshelf/FIRSTcatalog');
			$num = count($catalog);
			for ($i = 2; $i < $num; $i++) {
				//Insert values for row
				$row = $catalog[$i];
				// Separate out the different values
				$values = preg_split("/(\s)+/",$row,18);
				// Change RA, Dec to degrees
				$RA = ($values[0] + (($values[1] + ($values[2]/60)) / 60))*15;
				$sign = substr($values[0],0,1);
				$values[0] = substr($values[0],1);
				$Dec = $values[3] + (($values[4] + ($values[5]/60)) / 60);
				if (strcmp($sign, '-') === 0) {
					$Dec = $Dec * -1;
				}
				// Get the other values
				$Ps = $values[6]; $Fpeak = $values[7]; $Fint = $values[8]; $RMS = $values[9]; $Maj = $values[10]; $Min = $values[11]; $PA = $values[12]; $fMaj = $values[13]; $fMin = $values[14]; $fPA = $values[15]; $Field = $values[16];
				// Create the query
				$query = "INSERT INTO FIRSTcatalog values($RA,$Dec,$Ps,$Fpeak,$Fint,$RMS,$Maj,$Min,$PA,$fMaj,$fMin,$fPA,'$Field')";
				$r = mysql_query($query) or die("Invalid: " .  mysql_error());
			}
      // Create FIRSTimages Table: Image_name | RAMaxHours | RAMaxMinutes | RAMin | DecMax | DecMin
	  $r = mysql_query(
			"CREATE TABLE FIRSTimages (
			 Image_name char(30) NOT NULL,
			 RAMax float NOT NULL, 
			 RAMin float NOT NULL,
			 DecMax float NOT NULL,
			 DecMin float NOT NULL)")
			or die ("Invalid: " . mysql_error());
	  // Get the first subdirectory names
	  $dir_names = file("FIRSTRA.txt");
	  if ($dir_names === FALSE) {
			die("Could not read FIRST image subdirectories from file");
	  }
	  else {
			// Get the Decs for a subdirectory
			$decs = file("FIRSTDec.txt");
			if ($decs === FALSE) {
				die("Could not read FIRST decs from file");
			}
			else {
				foreach ($dir_names as $dir) {
					foreach ($decs as $dec) {
						$filename = substr($dir,0,5) . substr($dec,0,12);
						// Get RA range
						$RAHours = substr($dir,0,2); 
						$RAMinutes = substr($dir,2,2) . "." . substr($dir,4,1);
						$RAMinMinutes = $RAMinutes - 23.25;
						$RAMaxMinutes = $RAMinutes + 23.25;
						$RAMin = ($RAHours + ($RAMinMinutes/60))*15;
						$RAMax = ($RAHours + ($RAMaxMinutes/60))*15;
						if ($RAMin < 0) $RAMin += 360;
						// Get Dec range
						$DecDegrees = substr($dec,1,2);
						$DecMinutes = substr($dec,3,2) . "." . substr($dec,5,1);
						if (substr($dec,0,1) == '-') {
							$DecMinutes = $DecMinutes * -1;
							$DecDegrees = $DecDegrees * -1;
						}
						$DecMinMinutes = $DecMinutes - 17.25;
						$DecMaxMinutes = $DecMinutes + 17.25;
						$DecMax = ($DecMaxMinutes/60) + $DecDegrees;
						$DecMin = ($DecMinMinutes/60) + $DecDegrees;
						$query = "INSERT INTO FIRSTimages values('$filename', $RAMax, $RAMin, $DecMax, $DecMin)";
						//echo "<br /> $query <br />";
						$r = mysql_query($query) or die("Error: " . mysql_error());
					}
				}
				/*
				//$result = mysql_query("SELECT RAMax, RAMin, DecMax, DecMin FROM Images WHERE RAMin > 360 OR RAMax < 0 OR DecMin > 90 or DecMax < -90");
				$result = mysql_query("SELECT * from Catalog limit 10");
				if ($result === FALSE) echo " Query failed ";
				else {
					echo "<table>";
					$num = mysql_num_rows($result);
					//echo "$num";
					for ($i = 1; $i <= 3; $i++){
						$row = mysql_fetch_row($result);
						echo "<tr>";
						foreach($row as $col){
							echo "<td>" . $col . "</td>";
						}
						echo "</tr>";
					}
					echo "</table>";
				}
				*/
				echo "Successfully initialized database<br />";
			}
	  }
      
?>
