<?php
require_once('../db/funcs/.dbinfo.php');
include('funcs.php');
//Create connection
//$link = mysqli_connect($HOST, $USERNAME, $PASSWORD, $DB_NAME);
$link = mysqli_connect($dbinfo['host'], $dbinfo['username'], $dbinfo['password'], $dbinfo['dbname']);

//Check connection
if (mysqli_connect_errno($link)) {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
} else {
  echo "Connected to database sn_astroshelf";
}

/* mysql_connect("localhost", "root", "root") or die(mysql_error());
echo "Connected to MySQL<br />";
mysql_select_db("test") or die(mysql_error());
echo "Connected to Database";
*/

$doc = new DomDocument();
$doc->loadHTMLFile("http://www.cbat.eps.harvard.edu/lists/Supernovae.html");	//TODO: Find a more efficient way to parse the HTML file - possibly split the file and run in parallel?

$elements = $doc->getElementsByTagName('pre');	//all the SN objects are within the <pre></pre> tags so extract it and remove the rest of the stuff
$matches = array();
$SNmatches = array();	//holds the arrays for each SN object using the SN name as the key
$SNcount = 0;	//incremental variable that will keep track of the total number of supernovas being parsed

/* keeps track of the text within each SN object - fluctuates depending on the information provided so it is better to track them dynamically */
$specific_SN_count = 0;		

if (!is_null($elements)) {
	$counter = 0;
  foreach ($elements as $element) {
    $nodes = $element->childNodes;
    foreach ($nodes as $node) {
    	if($counter >= 1){
    	//echo "<br/>". $node->nodeName . ": ";

      	if(preg_match("/^\(?\d*?\w*?\)?$/", trim($node->nodeValue)) && strcmp($node->nodeName, "a") == 0){	//handles the majority of the SN, even ones without names
      		//echo $node->nodeValue . " IT MATCHES!" . "\n";
      		if(preg_match("/^\s*$/", trim($node->nodeValue))){		//check to see if there is no name for the SN and assign it a temporary name
      			$matches[$SNcount] = "undefined" . $SNcount;
      		} else {
      			$matches[$SNcount] = $node->nodeValue;		//set the SN name at each index
      		}
      		$specific_SN_count = 0;						//initialize the specified SN count each time a new SN object is parsed
      		$SNcount++;									//increment the total SN count
      	} elseif(preg_match("/(^[\w\s]+[^\d]+$)/", trim($node->nodeValue)) && strcmp($node->nodeName, "a") == 0) {		//edge case with SN with just letters
      		//echo $node->nodeValue . " IT MATCHES!" . "\n";
      		$matches[$SNcount] = $node->nodeValue;		//set the SN name at each index
      		$specific_SN_count = 0;
      		$SNcount++;
      	} else {                 //this else statement takes care of emitting the specific supernova information and storing it in the $SNmatches array using the SN name as the key
      		//echo $SNcount . "\n";
      		$SNmatches[$matches[$SNcount-1]][$specific_SN_count] = $node->nodeValue;
      		//echo $node->nodeValue. "\n";
      		$specific_SN_count++;
      	}
      }
      $counter++;
    }
  }

  echo "<pre>";
  //echo print_r($SNmatches);
  echo "</pre>";

  $SN_name = array();   //contains the supernova names
  $SN_host_gal = array();  //contains the supernova's host galaxy (e.g. Anon., ESO 576-17, etc.)
  $SN_date = array();   //contains the supernova's reported date
  $SN_ra = array();     //contains the supernova right ascensions
  $SN_dec = array();    //contains the supernova declinations
  $SN_ra_string = array();
  $SN_dec_string = array();
  $SN_type = array();   //contains the supernova types
  $SN_mag = array();    //contains the supernova magnitudes
  $SN_discover = array();   //contains the supernova's discoverer (if any)

  $c = 0;
  //echo "<pre>";
  foreach ($SNmatches as $key => $value){   //loop through the keys in the SNmatches array
  	$SN_name[$c] = "SN " . $key;
  	$posnegbool = false;   //boolean to keep track if the supernova has a positive or negative declination
    $count_value = count($value);

    /* 
      Loop through the values and obtain the necessary information - Sample entry from the SNmatches array - typical situation
        [2013ch] => Array
        (
            [0] =>   ESO 7-6          2013 04 30  12 41.7 -84 28   34W   2N  16.9   => [Host Galaxy] [Date] [R.A.] [Decl.] [Offset] [Magnitude]
            [1] => CBET 3518
            [2] =>        12 41 43.41 -84 27 52.4   => SN Position - [R.A. - h m s] [Decl. - d m s]
            [3] => CBET 3518
            [4] =>         IIb   2013ch  Bock, Marples    => [type] [Supernova] [Discoverer]
        )
    */
  	for($i=0; $i<$count_value; $i++){    
  		if(preg_match("/.*\+.*/", $value[$i])){
  			//echo "Positive Dec: " . $value[$i] . "\n";
  			$posnegbool = true;
  		}
  		elseif(preg_match("/.*-.*/", $value[$i])) {
  			//echo "Negative Dec: " . $value[$i] . "\n";
  			$posnegbool = false;
  		} else {
  		}

  		/* 
          Check to see if the first line is the current one being parsed and see if the declination is POSITIVE 
          First checks to see if the 
      */
  		if($i == 0 && $posnegbool === true){

        $found_mag = false;                               //boolean that handles some nasty parsing of the magnitude

        /*
            Match all the occurrences of the right ascension in the line.  There are edge cases where the lines don't correctly 
            split, so the preg_match_all takes care of retrieving all instances of the right ascension. 
        */
        preg_match_all("/\d{2}\s{0,1}\d{0,2}\s\d{0,2}\.+?\d+(?=\s*\+)/", $value[$i], $ra_matches);

        if(count($ra_matches[0]) == 1){
            //you only have one occurrence, most likely the one under the 'Decl.' column, NOT 'SN position'
            $ra = preg_split("/\s/", trim($ra_matches[0][0]));
            calculateRA($SN_ra, $ra, $c);
            convertRAtoString($SN_ra_string, $ra, $c);
        } elseif(count($ra_matches[0]) == 2){
            //you have two occurrences of the right ascension in the first line!
            $ra = preg_split("/\s/", trim($ra_matches[0][1]));
            calculateRA($SN_ra, $ra, $c);
            convertRAtoString($SN_ra_string, $ra, $c);
        } elseif(count($ra_matches[0]) == 0){
            $SN_ra[$c] = 0.0;
        }

        preg_match_all("/\+\d{2}\s\d{0,2}\s?\d{0,2}\.*\d{0,2}/", $value[$i], $dec_matches);
       
        if(count($dec_matches[0]) == 1){
            //you only have one occurrence, most likely the one under the 'Decl.' column, NOT 'SN position'
            $dec = preg_split("/\s/", trim($dec_matches[0][0]));
            calculateDEC($SN_dec, $dec, "pos", $c);
            convertDECtoString($SN_dec_string, $dec, $c);
        } elseif(count($dec_matches[0]) == 2){
            //you have two occurrences of the declination in the first line!
            $dec = preg_split("/\s/", trim($dec_matches[0][1]));
            calculateDEC($SN_dec, $dec, "pos", $c);
            convertDECtoString($SN_dec_string, $dec, $c);
        } elseif(count($dec_matches[0]) == 0){
            $SN_dec[$c] = 0.0;
        }

        $split_spaces = preg_split("/\s\s+/", trim($value[$i]));   //split the line based on any number of spaces
        $count_pos_split_spaces = count($split_spaces);     //cache the count so it doesn't have to be calculated for each iteration of the loop

        //edge case where there is no host galaxy and the first entry is the date
        if(preg_match("/\d{4}\s\d{2}\s\d{2}/", $split_spaces[0])){
            $SN_date[$c] = $split_spaces[0]; 
        } else {
          $SN_host_gal[$c] = $split_spaces[0];              //set the host galaxy to the first entry in the split string - this is always listed as Anon. or a specific galaxy
        }

        /* 
            Dummy-proof the date entry to make sure the date only contains numbers from the 
            specified location in the split_spaces array.
        */
        if(preg_match("/\d{4}\s\d{2}\s\d{2}/", $split_spaces[1])) {
          $SN_date[$c] = $split_spaces[1]; 
        }

        /*
            Loop through the first line using the cached count of the split_spaces array.
        */

  			 for($k=0; $k<$count_pos_split_spaces; $k++){

          /*
              Check to see if the magnitude has been found and then check the reg exp with a xx.x or xx or -x format.
              Usually the magnitude is the last entry in the split line, but sometimes there are irregularities which
              are taken care of using the regular expressions.
          */
          if($found_mag === false && (preg_match("/^\d{1,2}\.+\d*$/", trim($split_spaces[$k])) || preg_match("/^-[0-9]$/", trim($split_spaces[$k])))){
            $SN_mag[$c] = $split_spaces[$k];
            $found_mag = true;
          } elseif ($found_mag === false) {
            if(preg_match("/^\d{1,2}\.*\d*$/", trim($split_spaces[$count_pos_split_spaces - 1]))){
              $SN_mag[$c] = $split_spaces[$count_pos_split_spaces - 1];
            } else {
              $SN_mag[$c] = 0.0;
            }
          }
  			}
  			
  		} 
		  /* Check to see if the first line is the current one being parsed and see if the declination is NEGATIVE */
  		elseif($i == 0 && $posnegbool === false){

        $found_mag = false;     //initialize a boolean to false for finding the magnitude within the line

        /*
            Match all the occurrences of the right ascension in the line.  There are edge cases where the lines don't correctly 
            split, so the preg_match_all takes care of retrieving all instances of the right ascension. 
        */
        preg_match_all("/\d{2}\s{0,1}\d{0,2}\s\d{0,2}\.+?\d+(?=\s*-)/", $value[$i], $ra_matches);
        if(count($ra_matches[0]) == 1){
            //you have two occurrences of the right ascension in the first line!
            $ra = preg_split("/\s/", trim($ra_matches[0][0]));     //create an array with the different parts of the right ascension (h, m, s)
            calculateRA($SN_ra, $ra, $c);                             //call the function to calculate the right ascension, giving it a boolean parameter to denote that it has a seconds value too
            convertRAtoString($SN_ra_string, $ra, $c);
        } elseif(count($ra_matches[0]) == 2){
            $ra = preg_split("/\s/", trim($ra_matches[0][1]));     //create an array with the different parts of the right ascension (h, m, s)
            calculateRA($SN_ra, $ra, $c);                             //call the function to calculate the right ascension, giving it a boolean parameter to denote that it has a seconds value too
            convertRAtoString($SN_ra_string, $ra, $c);
        } elseif(count($ra_matches[0]) == 0){
            $SN_ra[$c] = 0.0;
        }

        preg_match_all("/-\d{2}\s\d{0,2}\s?\d{0,2}\.*\d{0,2}/", $value[$i], $dec_matches);
        if(count($dec_matches[0]) == 1){
            //you have two occurrences of the right ascension in the first line!
            $dec = preg_split("/\s/", trim($dec_matches[0][0]));
            calculateDEC($SN_dec, $dec, "neg", $c);
            convertDECtoString($SN_dec_string, $dec, $c);
        } elseif(count($dec_matches[0]) == 2){
            //you only have one occurrence, most likely the one under the 'Decl.' column, NOT 'SN position'
            $dec = preg_split("/\s/", trim($dec_matches[0][1]));
            calculateDEC($SN_dec, $dec, "neg", $c);
            convertDECtoString($SN_dec_string, $dec, $c);
        } elseif(count($dec_matches[0]) == 0){
            $SN_dec[$c] = 0.0;
        }

        $split_spaces = preg_split("/\s\s+/", trim($value[$i]));   //split the line based on any number of spaces
        $count_neg_split_spaces = count($split_spaces);     //cache the count so it doesn't have to be calculated for each iteration of the loop

        //edge case where there is no host galaxy and the first entry is the date
        if(preg_match("/\d{4}\s\d{2}\s\d{2}/", $split_spaces[0])){
            $SN_date[$c] = $split_spaces[0]; 
        } else {
          $SN_host_gal[$c] = $split_spaces[0];              //set the host galaxy to the first entry in the split string - this is always listed as Anon. or a specific galaxy
        }
        
        if(preg_match("/[0-9]+/", $split_spaces[1])) {
          $SN_date[$c] = $split_spaces[1]; }

          /* 
              Loops through the first line split array, and determines if there is a match for the magnitude - there is a variety 
              of formats for the magnitude so the conditional statements look for different patterns, and there is a default,
              which is the last split of the first line.  Usually, this is the case but sometimes it doesn't appear to be the last
              item in the split, hence why there is a need to parse through each of the splits to find the magnitude.  If nothing
              matches the pattern in any case, set the magnitude to 0.0 so it can be added to the database.
          */
  			for($k=0; $k<$count_neg_split_spaces; $k++){
          if($found_mag === false && (preg_match("/^\d{1,2}\.+\d*$/", trim($split_spaces[$k])) || preg_match("/^-[0-9]$/", trim($split_spaces[$k])))){
            $SN_mag[$c] = $split_spaces[$k];
            $found_mag = true;
          } elseif ($found_mag === false) {
            if(preg_match("/^\d{1,2}\.*\d*$/", trim($split_spaces[$count_neg_split_spaces - 1]))){
              $SN_mag[$c] = $split_spaces[$count_neg_split_spaces - 1];
            } else {
              $SN_mag[$c] = 0.0;
            }
          }
  			}
  			
  		}

      /* 
        If the SN position is fully given, extract the ra and dec and add them to the database as strings - 
        this is located on line 3 of the current supernova being parsed
      */
      if($i == 2 && $posnegbool === true){
        preg_match_all("/\d{2}\s{0,1}\d{0,2}\s\d{0,2}\.+?\d+(?=\s*\+)/", $value[$i], $ra_matches);
        
        if(strlen($ra_matches[0][0]) != 0){
          $ra = preg_split("/\s/", trim($ra_matches[0][0]));     //create an array with the different parts of the right ascension (h, m, s)
          calculateRA($SN_ra, $ra, $c);
          convertRAtoString($SN_ra_string, $ra, $c);

          preg_match_all("/\+\s*\d{1,2}\s\d{0,2}\s?\d{0,2}\.*\d{0,2}/", $value[$i], $dec_matches);
          $dec = preg_split("/\s/", trim($dec_matches[0][0]));
          calculateDEC($SN_dec, $dec, "pos", $c);
          convertDECtoString($SN_dec_string, $dec, $c);
     
        }
      } elseif($i == 2 && $posnegbool === false){
        preg_match_all("/\d{2}\s{0,1}\d{0,2}\s\d{0,2}\.+?\d+(?=\s*-)/", $value[$i], $ra_matches);

        if(strlen($ra_matches[0][0]) != 0){
          $ra = preg_split("/\s/", trim($ra_matches[0][0]));     //create an array with the different parts of the right ascension (h, m, s)
          calculateRA($SN_ra, $ra, $c);
          convertRAtoString($SN_ra_string, $ra, $c);

          preg_match_all("/-\s*\d{1,2}\s\d{0,2}\s?\d{0,2}\.*\d{0,2}/", $value[$i], $dec_matches);
          echo "<pre>";
        //echo $key . "\n";
        //echo print_r($dec_matches);
        echo "</pre>";
          $dec = preg_split("/\s/", trim($dec_matches[0][0]));
          calculateDEC($SN_dec, $dec, "neg", $c);
          convertDECtoString($SN_dec_string, $dec, $c);

        }
      }

  		if($i == count($value) - 1){		//the last array from each supernova contains the type
  			$split_spaces = preg_split("/\s\s+/", trim($value[$i]));     //split the line based on spaces, the first value is the type, second is the SN name, third is discoverer
        $count_ss = count($split_spaces);

  			if(preg_match("/(I){1,}.*|^\?$/", $split_spaces[0]))
  				$SN_type[$c] = $split_spaces[0];
  			else {
  				$SN_type[$c] = "undefined";
  			}

        for($u=0; $u < $count_ss; $u++){
          if(preg_match("/[0-9]+/", $split_spaces[$u])) {}
          elseif (preg_match("/^\(?[0-9]+?\w*?\)?$/", $split_spaces[$u])) {}
          elseif(preg_match("/(I){1,}.*|^\?$/", $split_spaces[$u])) {} 
          else {
            $SN_discover[$c] = $split_spaces[$u];
          }
        }
  		}
  	}
  	$c++;
  }
  
echo "<pre>";
  	
  	$SN_name_count = count($SN_name);
	for($l=0; $l<$SN_name_count; $l++){
		echo "Object Number: " . ($l + 1) . "\n";
    echo "Supernova Name: " . $SN_name[$l] . "\n";
    echo "Supernova Host Galaxy: " . $SN_host_gal[$l] . "\n";
    $SN_date[$l] = preg_replace("/\s/", "-", trim($SN_date[$l]));     //convert the date to the correct MySQL format YYYY-MM-DD
    echo "Supernova Date: " . date("Y-m-d H:i:s", strtotime($SN_date[$l])) . "\n";
		echo "RA: " . $SN_ra[$l] . "\n";
    echo "RA(string): " . $SN_ra_string[$l] . "\n";
		echo "Dec: " . $SN_dec[$l] . "\n";
    echo "Dec(string): " . $SN_dec_string[$l] . "\n";
		echo "Type: " . $SN_type[$l]. "\n";
		echo "Redshift: 0\n";
		echo "Mag: " . $SN_mag[$l] . "\n";
		echo "Phase: undefined\n";
    echo "Discoverer: " . $SN_discover[$l] . "\n\n";

    $SN_list = array($SN_ra[$l], $SN_dec[$l], $SN_name[$l], NULL, $SN_type[$l], 0, $SN_mag[$l], "undefine");
    $SN_list2 = array($SN_ra[$l], $SN_dec[$l], $SN_ra_string[$l], $SN_dec_string[$l], ($l + 1));

    $insert_query = "INSERT IGNORE INTO `SN_known_list`(`sn_id`, `sn_name`, `sn_host_galaxy`, `sn_date`, `sn_ra`, `sn_dec`, `sn_ra_hmsdms`, `sn_dec_hmsdms`, `sn_type`, `sn_mag`, `sn_phase`, `sn_redshift`, `sn_discoverer`, `sn_timestamp`) 
    VALUES (" . ($l + 1) . ", '" . $SN_name[$l] . "', '" . $SN_host_gal[$l] . "', '". date("Y-m-d H:i:s", strtotime($SN_date[$l])) ."', " . $SN_ra[$l] . ", " . $SN_dec[$l] . ", '" . $SN_ra_string[$l] . "', '" . mysqli_real_escape_string($link, $SN_dec_string[$l]) . "', '" . $SN_type[$l] . "', " . $SN_mag[$l] . ", 'undefined', 0.0, '" . mysqli_real_escape_string($link, $SN_discover[$l]) . "', CURDATE() )";

    echo $insert_query . "\n";
    if(is_null($SN_ra[$l]) || $SN_ra[$l] == 0 || is_null($SN_dec[$l]) || $SN_dec[$l] == 0){} else {
      //mysqli_query($link, $insert_query) or die(mysqli_error($link));
      //updateObjectsTable($link, $SN_list); 
      //updateUniquesTable($link, $SN_list2);
    }

	}
  	echo "</pre>";

    mysqli_close($link);

}

?>