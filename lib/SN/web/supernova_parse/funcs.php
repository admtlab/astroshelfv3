<?php

function IsEmptyString(&$val){
    if (trim($val) === '' || preg_match("/\s*/", $val)) {
        $val = "NULL";
    }
}

function calculateRA(&$SN_ra_tmp, &$ra, $c){
    $float_ra_hrs = floatval($ra[0]);
    $float_ra_min = floatval($ra[1]);
    $float_ra_secs = 0.0;

    if(!is_null($ra[2])){
      $float_ra_secs = floatval($ra[2]);
      if($c != -1)
        $SN_ra_tmp[$c] = number_format((($float_ra_hrs * 15) + ($float_ra_min / 4) + ($float_ra_secs / 240)), 5, '.', '');
      else 
        $SN_ra_tmp = number_format((($float_ra_hrs * 15) + ($float_ra_min / 4) + ($float_ra_secs / 240)), 5, '.', '');
    }

}

function convertRAtoString(&$SN_ra_string_tmp, &$ra, $c){
    $ra_h = $ra[0] . "h";
    $ra_m = $ra[1] . "m";
    $ra_s = $ra[2] . "s";
    if($c != -1)
        $SN_ra_string_tmp[$c] = $ra_h . " " . $ra_m . " " . $ra_s;
    else
        $SN_ra_string_tmp = $ra_h . " " . $ra_m . " " . $ra_s;
}

function calculateDEC(&$SN_dec_tmp, &$dec, $sign, $c){
    $float_dec_deg = floatval($dec[0]);
    if(strcmp($sign, "neg") == 0){
        $float_dec_deg = -abs($float_dec_deg);  //convert the degrees to negative
        $float_dec_min = floatval($dec[1]);
        $float_dec_secs = 0.0;

        if(!is_null($dec[2])){
            $float_dec_secs = floatval($dec[2]);
            if($c != -1)
                $SN_dec_tmp[$c] = number_format((($float_dec_deg) - ($float_dec_min / 60) - ($float_dec_secs / 3600)), 5, '.', '');
            else
                $SN_dec_tmp = number_format((($float_dec_deg) - ($float_dec_min / 60) - ($float_dec_secs / 3600)), 5, '.', '');
        }
    } else {
        $float_dec_min = floatval($dec[1]);
        $float_dec_secs = 0.0;

        if(!is_null($dec[2])){
            $float_dec_secs = floatval($dec[2]);
            if($c != -1)
                $SN_dec_tmp[$c] = number_format((($float_dec_deg) + ($float_dec_min / 60) + ($float_dec_secs / 3600)), 5, '.', '');
            else
                $SN_dec_tmp = number_format((($float_dec_deg) + ($float_dec_min / 60) + ($float_dec_secs / 3600)), 5, '.', '');
        }
    }

}

function convertDECtoString(&$SN_dec_string_tmp, &$dec, $c){
    $dec_deg = $dec[0] . "d";
    $dec_min = $dec[1] . "'";
    $dec_sec = $dec[2] . '"';
    if($c!= -1)
        $SN_dec_string_tmp[$c] = $dec_deg . " " . $dec_min . " " . $dec_sec;
    else
        $SN_dec_string_tmp = $dec_deg . " " . $dec_min . " " . $dec_sec;
}

function convertDatetoTimestamp(&$SN_discovery_date){
    $SN_disc_date_split = preg_split("/\s/", trim($SN_discovery_date));     //split the date based on spaces (discovery)
    $SN_disc_date_split[1] = substr($SN_disc_date_split[1], 0, 2);  //replace the fraction of a day (not needed for storing in database)

    $SN_disc_full_date = $SN_disc_date_split[0] . "-" . $SN_disc_date_split[1] . "-" . $SN_disc_date_split[2];
    $SN_discovery_date = date("Y-m-d H:i:s", strtotime($SN_disc_full_date));      // convert the date to timestamp format
}

function convertDate(&$SN_spectrum_date){
    $SN_spectrum_date = date("Y-m-d", strtotime($SN_spectrum_date));      // convert the date to timestamp format
}

// return the last inserted id to use for the matches table later

function updateKnownListTable($link, &$SN_list){
    $last_inserted_row_id = -1;

    // Specify a very tight range so that exact objects can be matched
    $object_ra_check_lower = $SN_list[3] - 0.0001;
    $object_ra_check_upper = $SN_list[3] + 0.0001;

    $object_dec_check_lower = $SN_list[4] - 0.0001;
    $object_dec_check_upper = $SN_list[4] + 0.0001;

    // check to see if the exact RA, DEC, and name exist in the objects table - even if there are multiple ones with the same RA and Dec, the names will differentiate
    // objects from different sources
    $check_query = "SELECT * FROM `SN_objects` WHERE (`object_ra` BETWEEN " . $object_ra_check_lower . " AND " . $object_ra_check_upper . ") ";
    $check_query .= "AND (`object_dec` BETWEEN " . $object_dec_check_lower . " AND " . $object_dec_check_upper . ") AND `object_name` = '" . $SN_list[0] . "'";
    
    //echo $check_query . "\n";
    if ($result = mysqli_query($link, $check_query)) {
            
            if(mysqli_num_rows($result) == 0){
                // the object was not in the object list (if this is the case, it won't be in the SN_known_list either since I update all tables when performing the larger update.)
                $insert_query = "INSERT INTO `SN_known_list`(`sn_name`, `sn_host_galaxy`, `sn_date`, `sn_ra`, `sn_dec`, `sn_ra_hmsdms`, `sn_dec_hmsdms`, `sn_type`, `sn_mag`, `sn_phase`, `sn_redshift`, `sn_discoverer`, `sn_instrument`, `sn_spectrum`, `sn_notes`, `sn_timestamp`) "; 
                $insert_query .= "VALUES ('" . $SN_list[0] . "', " . $SN_list[1] . ", '" . $SN_list[2] . "', " . $SN_list[3] . ", " . $SN_list[4] . ", ";
                $insert_query .= "'" . mysqli_real_escape_string($link, $SN_list[5]) . "', '" . mysqli_real_escape_string($link, $SN_list[6]) . "', '" . $SN_list[7] . "', " . $SN_list[8] . ", '" . $SN_list[9] . "', " . $SN_list[10] . ", ";
                $insert_query .= $SN_list[11] . ", '" . $SN_list[12] . "', '" . $SN_list[13] . "', '" . $SN_list[14] . "', CURDATE() )";
                //echo $insert_query . "\n";
                mysqli_query($link, $insert_query) or die(mysqli_error($link));
                $last_inserted_row_id = mysqli_insert_id($link);
            } else {
                echo "Supernova is already in the object list, therefore it must be in the known list!\n";
            }
    }

    return $last_inserted_row_id;

}

function updateObjectsTable($link, &$SN_list){
    //$SN_list_count = count($SN_list);

    // Specify a very tight range so that exact objects can be matched
    $object_ra_check_lower = $SN_list[0] - 0.0001;
    $object_ra_check_upper = $SN_list[0] + 0.0001;

    $object_dec_check_lower = $SN_list[1] - 0.0001;
    $object_dec_check_upper = $SN_list[1] + 0.0001;
  
    // check to see if the exact RA, DEC, and name exist in the objects table - even if there are multiple ones with the same RA and Dec, the names will differentiate
    // objects from different sources
    $check_query = "SELECT * FROM `SN_objects` WHERE (`object_ra` BETWEEN " . $object_ra_check_lower . " AND " . $object_ra_check_upper . ") ";
    $check_query .= "AND (`object_dec` BETWEEN " . $object_dec_check_lower . " AND " . $object_dec_check_upper . ") AND `object_name` = '" . $SN_list[2] . "'";
    //echo $check_query . "\n";
    if ($result = mysqli_query($link, $check_query)) {
            
            if(mysqli_num_rows($result) == 0){
                $insert_query = "INSERT INTO `SN_objects`(`object_ra`, `object_dec`, `object_name`, `object_msg_hashed`, `object_type`, `object_redshift`, `object_disc_mag`, `object_phase`) 
                    VALUES (" . $SN_list[0] . ", " . $SN_list[1] . ", '" . $SN_list[2] . "', " . $SN_list[3] . ", '" . $SN_list[4] . "', " . $SN_list[5] . ", " . $SN_list[6] . ", '" . $SN_list[7] . "')";
                //echo $insert_query . "\n";
                mysqli_query($link, $insert_query) or die(mysqli_error($link)); 
            } else {
                echo "Object is already in SN_objects table!\n";
            }
    } 
}

function updateUniquesTable($link, &$SN_list){
    $unique_ra_check_lower = $SN_list[0] - 0.0001;
    $unique_ra_check_upper = $SN_list[0] + 0.0001;

    $unique_dec_check_lower = $SN_list[1] - 0.0001;
    $unique_dec_check_upper = $SN_list[1] + 0.0001;

    $unique_id = 0;
    $object_id = 0;

    // Set up a check to see if the unique object already exists in the SN_uniques table
    // use the RA and Dec because names can vary
    $check_query = "SELECT * FROM `SN_uniques` WHERE (`unique_ra` BETWEEN " . $unique_ra_check_lower . " AND " . $unique_ra_check_upper . ") AND (`unique_dec` BETWEEN " . $unique_dec_check_lower . " AND " . $unique_dec_check_upper . ")";

    if ($result = mysqli_query($link, $check_query)) {  // if we get back a valid result
            
            if(mysqli_num_rows($result) == 0){  // check to see that we don't have any rows returned
                    // we don't have any so insert into the SN_uniques table
                    $insert_query = "INSERT IGNORE INTO `SN_uniques`(`unique_ra`, `unique_dec`, `unique_ra_hmsdms`, `unique_dec_hmsdms`) 
                    VALUES (" . $SN_list[0] . ", " . $SN_list[1] . ", '" . $SN_list[2] . "', '" . mysqli_real_escape_string($link, $SN_list[3]) . "')";
                    //echo $insert_query . "\n";
                    mysqli_query($link, $insert_query) or die(mysqli_error($link)); 

                    $last_inserted_row_id = mysqli_insert_id($link);

                    if($SN_list[4] != -1)
                        updateSNknownmatchTable($link, $last_inserted_row_id, $SN_list[4]);


                $sec_retrieve_query = "SELECT `object_id` FROM `SN_objects` WHERE (`object_ra` BETWEEN " . $unique_ra_check_lower . " AND " . $unique_ra_check_upper . ") AND (`object_dec` BETWEEN " . $unique_dec_check_lower . " AND " . $unique_dec_check_upper . ")";
                //echo $sec_retrieve_query . "\n";
                if ($result3 = mysqli_query($link, $sec_retrieve_query)) {
                    /* fetch associative array */
                    while ($row = mysqli_fetch_row($result3)) {
                        $object_id = $row[0];
                        updateMatchesTable($link, $last_inserted_row_id, $object_id);   // there should only be one row but just in case...
                    }
                    /* free result set */
                    mysqli_free_result($result3);
                }

            } else {
                echo "Supernova isn't unique.  " . $SN_list[0] . ", " . $SN_list[1] . " is already in the SN_uniques table.\n\n";

                // get the unique id of the supernova that we tried to add, but was already in the database
                $retrieve_query = "SELECT `unique_id` FROM `SN_uniques` WHERE (`unique_ra` BETWEEN " . $unique_ra_check_lower . " AND " . $unique_ra_check_upper . ") AND (`unique_dec` BETWEEN " . $unique_dec_check_lower . " AND " . $unique_dec_check_upper . ")";
                    //echo $retrieve_query . "\n";
                if ($result2 = mysqli_query($link, $retrieve_query)) {
                     /* fetch associative array */
                    while ($row = mysqli_fetch_row($result2)) {
                        $unique_id = $row[0];   // save the unique id
                    }                   
                    /* free result set */
                    mysqli_free_result($result2);
                }

                // get the object id 
                $sec_retrieve_query = "SELECT `object_id` FROM `SN_objects` WHERE (`object_ra` BETWEEN " . $unique_ra_check_lower . " AND " . $unique_ra_check_upper . ") AND (`object_dec` BETWEEN " . $unique_dec_check_lower . " AND " . $unique_dec_check_upper . ")";
                //echo $sec_retrieve_query . "\n";
                if ($result3 = mysqli_query($link, $sec_retrieve_query)) {
                    /* fetch associative array */
                    while ($row = mysqli_fetch_row($result3)) {
                        $object_id = $row[0];
                        echo $object_id . "\n";
                        updateMatchesTable($link, $unique_id, $object_id);
                    }
                    /* free result set */
                    mysqli_free_result($result3);
                }

                if($SN_list[4] != -1)
                    updateSNknownmatchTable($link, $unique_id, $SN_list[4]);
            }

            /* free result set */
            mysqli_free_result($result);
    }
}

function updateMatchesTable($link, $match_unique_id, $match_object_id){
        // First check query
        $check_query = "SELECT * FROM `SN_matches` WHERE match_unique_id = " . $match_unique_id;
        if ($result = mysqli_query($link, $check_query)) {  // if we get back a valid result
            if(mysqli_num_rows($result) == 0){  // if the number of rows returned is 0 (the unique id doesn't exist)
                $insert_query = "INSERT INTO `SN_matches` (`match_unique_id`, `match_object_id`) VALUES (" . $match_unique_id . ", " . $match_object_id . ") ";
                //echo $insert_query . "\n\n";
                mysqli_query($link, $insert_query) or die(mysqli_error($link));  
                
            } else {
                // The unique id exists in the table, now check if the object id is there too
                // Second check query
                $check_query = "SELECT * FROM `SN_matches` WHERE match_unique_id = " . $match_unique_id . " AND match_object_id = " . $match_object_id;
                if ($result = mysqli_query($link, $check_query)) {  // if we get back a valid result
                    if(mysqli_num_rows($result) == 0) {
                        // The object id does not exist for this particular unique id so add a new row
                        $insert_query = "INSERT INTO `SN_matches` (`match_unique_id`, `match_object_id`) VALUES (" . $match_unique_id . ", " . $match_object_id . ") ";
                        //echo $insert_query . "\n\n";
                        mysqli_query($link, $insert_query) or die(mysqli_error($link)); 
                    } else {
                        // The object trying to be added already exists in the matches table
                        echo "This object is already in the SN_matches table!\n";
                    }
                }
            }
        }
        
}

function updateSNknownmatchTable($link, $kl_match_unique_id, $kl_match_sn_id){
        
        $check_query = "SELECT * FROM `SN_known_list_match` WHERE kl_match_unique_id = " . $kl_match_unique_id;
        if ($result = mysqli_query($link, $check_query)) {  // if we get back a valid result
            if(mysqli_num_rows($result) == 0){  // if the number of rows returned is 0 (the unique id doesn't exist)
                $insert_query = "INSERT IGNORE INTO `SN_known_list_match` (`kl_match_unique_id`, `kl_match_sn_id`) VALUES (" . $kl_match_unique_id . ", " . $kl_match_sn_id . ")";
                //echo $insert_query . "\n\n";
                mysqli_query($link, $insert_query) or die(mysqli_error($link));  
                
            } else {
                // The unique id exists in the table, now check if the object id is there too
                // Second check query
                $check_query = "SELECT * FROM `SN_known_list_match` WHERE kl_match_unique_id = " . $kl_match_unique_id . " AND kl_match_sn_id = " . $kl_match_sn_id;
                if ($result = mysqli_query($link, $check_query)) {  // if we get back a valid result
                    if(mysqli_num_rows($result) == 0) {
                        // The object id does not exist for this particular unique id so add a new row
                        $insert_query = "INSERT IGNORE INTO `SN_known_list_match` (`kl_match_unique_id`, `kl_match_sn_id`) VALUES (" . $kl_match_unique_id . ", " . $kl_match_sn_id . ")";
                        //echo $insert_query . "\n\n";
                        mysqli_query($link, $insert_query) or die(mysqli_error($link)); 
                    } else {
                        // The object trying to be added already exists in the matches table
                        echo "This object is already in the SN_known_list table!\n";
                    }
                }
            }
        }
}

?>