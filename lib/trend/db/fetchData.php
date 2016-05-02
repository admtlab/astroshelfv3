<?php
	
error_reporting(-1);
ini_set("display_errors", 1);

function &check_table($list, $link){
	
	# create a temp table to house the objects we want
	$sql = "DROP TEMPORARY TABLE IF EXISTS requested_spectra";
	mysqli_query ($link, $sql ) or die( "Error1 " . mysqli_error ($sql)  ."\n") ;
	
	$sql = "CREATE TEMPORARY TABLE requested_spectra( ";
	$sql .= "OBJID varchar(20) NOT NULL, ";
	$sql .= "PRIMARY KEY(OBJID) )"; 
	
	mysqli_query ($link, $sql ) or die( "Error2 " . mysqli_error($sql) . "\n") ;
	
	# add our requested spectra
	foreach($list as $o){
		
		$obj = substr($o,1);
		$sql = "REPLACE INTO requested_spectra (OBJID) VALUES (";
		$sql .= "'$o'";
		$sql .= ")";
		
		mysqli_query ($link, $sql ) or die( "Error3 " . mysqli_error ($sql) . "\n") ;
	}
	
	$sql = "SELECT OBJID FROM requested_spectra WHERE OBJID NOT IN( ";
	$sql .= "SELECT OBJID FROM sdssSpectra)";
	
	$diff = mysqli_query ($link, $sql ) or die( "Error4 " . mysqli_error ($sql) . "\n") ;
	$diff_array = array();
	
	while($row = mysqli_fetch_row($diff)){
		$diff_array[] = $row[0];
	}
	
	return $diff_array;
}

function &querySDSS($objID, $id, &$inc, $link, $log){
			
	$object_names = array();
	$query_list = array();
	
	#First, check all objects against the tbl8 sdssSpectra table - only send request to SDSS if the data hasn't been cached locally
	$unique = array_unique($objID);
	$diff = check_table($unique, $link);
	
	$inc = 0;
	
	/* objects exist */
	if(count($diff) == 0){
		return $unique;
	}
	$stored = array_diff($unique, $diff);
	
	# Need to split the array of ids into smaller components; if the query is too long (lists too many objects) it gets rejected
	$num_objs = 100;
	$diff_arrays = array();
	for($i = 0; $i < count($diff)/$num_objs; $i++) {
		array_push($diff_arrays, array());
		for($j = $i*$num_objs; $j < min($i*$num_objs + $num_objs, count($diff)); $j++) {
			array_push($diff_arrays[$i], $diff[$j]);
		}
	}
	
	# TODO: Need to add links to the search query 
	# TODO: Make temporary table to return, not just IDs
	
	$xml_rows = array();
	for($i = 0; $i < count($diff_arrays); $i++) {
		# construct query into sdss
		$sql_base = "SELECT p.objid as OBJID, p.ra as RA, p.dec as DECL, p.dered_u as U_MAG, p.dered_z as Z_MAG, p.dered_i as I_MAG, p.dered_r as R_MAG, ";
		$sql_base .= "p.dered_g as G_MAG, s.z as REDSHIFT, s.specobjid as SPEC_OBJID, s.specclass as SPEC_CLASS, ";
		$sql_base .= "s.plate as PLATE, s.mjd as MJD, s.fiberID as FIBER_ID ";
		$sql_base .= "FROM PhotoObj as p join SpecObj as s on p.objid=s.bestobjid ";
		$sql = $sql_base . "WHERE p.objid IN (".join(",", $diff_arrays[0]).") AND s.specobjid > 0";
		
		# get result from sdss
		$ret = general_sdss_query($sql, $xml_output_string, $xml_output_object, $error_message, $log);
		sleep(2);
		$log->logInfo("error message: ", $error_message);
		foreach($xml_output_object->Row as $row) {
			$xml_rows[] = $row;
		}
	}
	
	$exist = 0;
	$sql = "";
	$object = 0;
	$objIDs = array();
	
	foreach($xml_rows as $row){
		
		$size = -1;
		if (intval($row["SPEC_OBJID"]) > 0){
			// simple exists flag 
			
			$objIDs[] = $row["OBJID"][0];
			$exist = 1;
			
			$maintable = 'REPLACE INTO sdssSpectra (OBJID,SPEC_OBJID,RA,DECL,SPEC_CLASS,SIZE,Z_MAG,G_MAG,R_MAG,I_MAG,U_MAG,REDSHIFT, ';
			$maintable .= 'MAX_WAVE, MIN_WAVE, MAX_REST, MIN_REST) VALUES( ';
			
			$url = "http://api.sdss3.org/spectrum?plate=" . $row["PLATE"][0] . "&mjd=" . $row["MJD"][0] . "&fiber=" . $row["FIBER_ID"][0];
			$url .= "&format=json&fields=wavelengths,flux";
			
			#echo "$url";
			
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$params = curl_exec($ch);
			curl_close($ch);
			
			$z = floatval($row["REDSHIFT"][0]);			
			
			$json = json_decode($params,true);
						
			$wave_def = array();
			$wave_val = array();
						
			$flux_def = array();
			$flux_val = array();
			
			$min_wave = 0; $max_wave = 0;
			$min_rest = 0; $max_rest = 0;
			$min_flux = 0; $max_flux = 0;
						
			foreach($json as $key => $value){
				
				if($key == "wavelengths"){
												
					$min_wave = $value[0];
					$max_wave = $value[0];
					
					$min_rest = $value[0]/(1.0+$z);
					$max_rest = $value[0]/(1.0+$z);
					
					$count = 0;
					$curr_val = 1000;
					
					foreach($value as $wave){
						
						$size = count($value);
						
						// find min and max rest
						$rest = $wave / (1.0 + $z);
						
						if($rest > $max_rest){ 
							$max_rest = $rest;
						}
						if($rest < $min_rest) {
							$min_rest = $rest;
						}
						
						// find min and max wave				
						if($wave > $max_wave){ 
							$max_wave = $wave;
						}
						if($wave < $min_wave) {
							$min_wave = $wave;
						}

						if($count < 750){
												
							if($count == 0){
							
								$wave_def[] = "REPLACE INTO sdssSpectraWaveLen1 (OBJID,";
								$wave_val[] = "Values('";
																
								$wave_val[0] .= (string)$row["OBJID"][0];
								$wave_val[0] .= "',";	
							} // end if 0
							
							$wave_def[0] .= "w$count,";
							$wave_val[0] .= "$wave,"; 
							// end if 750
						}else if($count < 1500){
							
								if($count == 750){
																	
									$wave_def[] = "REPLACE INTO sdssSpectraWaveLen2 (OBJID,";
									$wave_val[] = "Values('" . $row["OBJID"][0] . "',";	
								
									$wave_def[0] = substr($wave_def[0], 0, -1);
									$wave_val[0] = substr($wave_val[0], 0, -1);
								
									$wave_def[0] .= ")";
									$wave_val[0] .= ")";
																										
							} // end if 750

							$wave_def[1] .= "w" . $count . ",";
							$wave_val[1] .= $wave . ",";
						} // end if < 1500
						else if($count < 2250){
														
							if($count == 1500){
																	
								$wave_def[] = "REPLACE INTO sdssSpectraWaveLen3 (OBJID,";
								$wave_val[] = "Values('" . $row["OBJID"][0] . "',";	
								
								$wave_def[1] = substr($wave_def[1], 0, -1);
								$wave_val[1] = substr($wave_val[1], 0, -1);
								
								$wave_def[1] .= ")";
								$wave_val[1] .= ")";
																										
							} // end if 1500

							$wave_def[2] .= "w" . $count . ",";
							$wave_val[2] .= $wave . ",";
						} // end if < 2250
						else if($count < 3000){
							
							if($count == 2250){
																	
								$wave_def[] = "REPLACE INTO sdssSpectraWaveLen4 (OBJID,";
								$wave_val[] = "Values('" . $row["OBJID"][0] . "',";	
								
								$wave_def[2] = substr($wave_def[2], 0, -1);
								$wave_val[2] = substr($wave_val[2], 0, -1);
								
								$wave_def[2] .= ")";
								$wave_val[2] .= ")";
																										
							} // end if 2250

							$wave_def[3] .= "w" . $count . ",";
							$wave_val[3] .= $wave . ",";
							
						} // end if < 3000
						else if($count < 3750){
							
							if($count == 3000){
																	
								$wave_def[] = "REPLACE INTO sdssSpectraWaveLen5 (OBJID,";
								$wave_val[] = "Values('" . $row["OBJID"][0] . "',";	
								
								$wave_def[3] = substr($wave_def[3], 0, -1);
								$wave_val[3] = substr($wave_val[3], 0, -1);
								
								$wave_def[3] .= ")";
								$wave_val[3] .= ")";
																										
							} // end if 3000	

							$wave_def[4] .= "w" . $count . ",";
							$wave_val[4] .= $wave . ",";
							
						} // end if < 3750
						else if($count < 4000){
							
							if($count == 3750){
																	
								$wave_def[] = "REPLACE INTO sdssSpectraWaveLen6 (OBJID,";
								$wave_val[] = "Values('" . $row["OBJID"][0] . "',";	
								
								$wave_def[4] = substr($wave_def[4], 0, -1);
								$wave_val[4] = substr($wave_val[4], 0, -1);
								
								$wave_def[4] .= ")";
								$wave_val[4] .= ")";
																										
							} // end if 3750

							$wave_def[5] .= "w" . $count . ",";
							$wave_val[5] .= $wave . ",";
							
						} // end if < 4000
						
						$count++;
							
					} // end foreach wave
					
					$wave_def[5] = substr($wave_def[5], 0, -1);
					$wave_val[5] = substr($wave_val[5], 0, -1);

					$wave_def[5] .= ")";
					$wave_val[5] .= ")";
					
					} // end if wave
					else if($key == "flux"){
										
						$min_flux = $value[0];
						$max_flux = $value[0];

						$count = 0;
						foreach($value as $flux){
							
							if($flux > $max_flux){ 
								$max_flux = $flux;
							}
							if($flux < $min_flux) {
								$min_flux = $flux;
							}

							if($count < 750){
																
								if($count == 0){

									$flux_def[] = "REPLACE INTO sdssSpectraFlux1(OBJID,";
									$flux_val[] = "Values('";

									$flux_val[0] .= (string)$row["OBJID"][0];
									$flux_val[0] .= "',";	
								} // end if 0

								$flux_def[0] .= "f$count,";
								$flux_val[0] .= "$flux,"; 

							}else if($count < 1500){

								if($count == 750){

									$flux_def[] = "REPLACE INTO sdssSpectraFlux2(OBJID,";
									$flux_val[] = "Values('" . $row["OBJID"][0] . "',";	

									$flux_def[0] = substr($flux_def[0], 0, -1);
									$flux_val[0] = substr($flux_val[0], 0, -1);

									$flux_def[0] .= ")";
									$flux_val[0] .= ")";

								} // end if 750

								$flux_def[1] .= "f" . $count . ",";
								$flux_val[1] .= $flux . ",";
							} // end if < 1500
							else if($count < 2250){

								if($count == 1500){

									$flux_def[] = "REPLACE INTO sdssSpectraFlux3(OBJID,";
									$flux_val[] = "Values('" . $row["OBJID"][0] . "',";	

									$flux_def[1] = substr($flux_def[1], 0, -1);
									$flux_val[1] = substr($flux_val[1], 0, -1);

									$flux_def[1] .= ")";
									$flux_val[1] .= ")";

								} // end if 1500

								$flux_def[2] .= "f" . $count . ",";
								$flux_val[2] .= $flux . ",";
							} // end if < 2250
							else if($count < 3000){

								if($count == 2250){

									$flux_def[] = "REPLACE INTO sdssSpectraFlux4(OBJID,";
									$flux_val[] = "Values('" . $row["OBJID"][0] . "',";	

									$flux_def[2] = substr($flux_def[2], 0, -1);
									$flux_val[2] = substr($flux_val[2], 0, -1);

									$flux_def[2] .= ")";
									$flux_val[2] .= ")";

								} // end if 2250

								$flux_def[3] .= "f" . $count . ",";
								$flux_val[3] .= $flux . ",";

							} // end if < 3000
							else if($count < 3750){

								if($count == 3000){

									$flux_def[] = "REPLACE INTO sdssSpectraFlux5(OBJID,";
									$flux_val[] = "Values('" . $row["OBJID"][0] . "',";	

									$flux_def[3] = substr($flux_def[3], 0, -1);
									$flux_val[3] = substr($flux_val[3], 0, -1);

									$flux_def[3] .= ")";
									$flux_val[3] .= ")";

								} // end if 3000

								$flux_def[4] .= "f" . $count . ",";
								$flux_val[4] .= $flux . ",";

							} // end if < 3750
							else if($count < 4000){

								if($count == 3750){

									$flux_def[] = "REPLACE INTO sdssSpectraFlux6(OBJID,";
									$flux_val[] = "Values('" . $row["OBJID"][0]	 . "',";	

									$flux_def[4] = substr($flux_def[4], 0, -1);
									$flux_val[4] = substr($flux_val[4], 0, -1);

									$flux_def[4] .= ")";
									$flux_val[4] .= ")";

								} // end if 3750

								$flux_def[5] .= "f" . $count . ",";
								$flux_val[5] .= $flux . ",";

							} // end if < 4000

							$count++;

						} // end foreach flux

						$flux_def[5] = substr($flux_def[5], 0, -1);
						$flux_val[5] = substr($flux_val[5], 0, -1);

						$flux_def[5] .= ")";
						$flux_val[5] .= ")";
						
					} // else if flux
				 	
			} // end for json array
			
			$maintable .= $row["OBJID"][0].','. $row["SPEC_OBJID"][0] .','. $row["RA"][0] .','. $row["DECL"][0] .','. $row["SPEC_CLASS"][0].',';
			$maintable .= $size .',' . $row["Z_MAG"][0] .','. $row["G_MAG"][0] .','. $row["R_MAG"][0] .','. $row["I_MAG"][0] .',' . $row["U_MAG"][0] .',' . $row["REDSHIFT"][0] .',' ;
			$maintable .= $max_wave .',' .$min_wave. ','. $max_rest .','. $min_rest. ' );';
			
			mysqli_autocommit($link, FALSE);			
			
			if ( !mysqli_query($link,$maintable) ){
				$log->logInfo("error: $sql", mysqli_error($link));
				$log->logInfo("error: $sql", mysqli_error($link));
				$log->logInfo("error: $sql", mysqli_error($link));
				$log->logInfo("error: $sql", mysqli_error($link));
				$log->logInfo("error: $sql", mysqli_error($link));
				$log->logInfo("error: $sql", mysqli_error($link));
				$log->logInfo("sql error list: ",mysqli_error_list($link));
				$log->logInfo("error: $sql", mysqli_error($sql));
				mysqli_rollback($link);
				array_pop($objIDs);
				$object+=1;
				continue;
			}	
			
			$sql = "$wave_def[0] $wave_val[0]; ";				
			if ( !mysqli_query($link,$sql) ){
				
				$log->logInfo("sql error list: ",mysqli_error_list($link));
				$log->logInfo("error: $sql", mysqli_error($sql));
				mysqli_rollback($link);
				array_pop($objIDs);
				$object+=1;
				continue;
			}			
			$sql = "$flux_def[0] $flux_val[0]; "; 	
			if ( !mysqli_query($link,$sql) ){
				
				$log->logInfo("sql error list: ",mysqli_error_list($link));
				$log->logInfo("error: $sql", mysqli_error($sql));
				mysqli_rollback($link);
				array_pop($objIDs);
				$object+=1;
				continue;
			}
			$sql = "$wave_def[1] $wave_val[1]; "; 
			if (!mysqli_query($link,$sql))
			{
				$log->logInfo("sql error list: ",mysqli_error_list($link));
				$log->logInfo("error: $sql", mysqli_error($sql));
				mysqli_rollback($link);
				array_pop($objIDs);
				$object+=1;
				continue;
			} 		
			
			$sql = "$flux_def[1] $flux_val[1]; "; 
			if (!mysqli_query($link,$sql))
			{
				$log->logInfo("sql error list: ",mysqli_error_list($link));
				$log->logInfo("error: $sql", mysqli_error($sql));
				mysqli_rollback($link);
				array_pop($objIDs);
				$object+=1;
				continue;
			} 			
			
			$sql = "$wave_def[2] $wave_val[2]; "; 
			if (!mysqli_query($link,$sql))
			{
				$log->logInfo("sql error list: ",mysqli_error_list($link));
				$log->logInfo("error: $sql", mysqli_error($sql));
				mysqli_rollback($link);
				array_pop($objIDs);
				$object+=1;
				continue;
			} 		
			
			$sql = "$flux_def[2] $flux_val[2]; "; 
			if (!mysqli_query($link,$sql))
			{
				$log->logInfo("sql error list: ",mysqli_error_list($link));
				$log->logInfo("error: $sql", mysqli_error($sql));
				mysqli_rollback($link);
				array_pop($objIDs);
				$object+=1;
				continue;
			} 
			$sql = "$wave_def[3] $wave_val[3]; "; 
			if (!mysqli_query($link,$sql))
			{
				$log->logInfo("sql error list: ",mysqli_error_list($link));
				$log->logInfo("error: $sql", mysqli_error($sql));
				mysqli_rollback($link);
				array_pop($objIDs);
				$object+=1;
				continue;
				
			} 
			$sql = "$flux_def[3] $flux_val[3]; "; 
			if (!mysqli_query($link,$sql))
			{
				$log->logInfo("sql error list: ",mysqli_error_list($link));
				$log->logInfo("error: $sql", mysqli_error($sql));
				mysqli_rollback($link);
				array_pop($objIDs);
				$object+=1;
				continue;
				
			} 	
			$sql = "$wave_def[4] $wave_val[4]; "; 
			if (!mysqli_query($link,$sql))
			{
				$log->logInfo("sql error list: ",mysqli_error_list($link));
				$log->logInfo("error: $sql", mysqli_error($sql));
				mysqli_rollback($link);
				array_pop($objIDs);
				$object+=1;
				continue;
				
			} 
			$sql = "$flux_def[4] $flux_val[4]; "; 
			if (!mysqli_query($link,$sql))
			{
				$log->logInfo("sql error list: ",mysqli_error_list($link));
				$log->logInfo("error: $sql", mysqli_error($sql));
				mysqli_rollback($link);
				array_pop($objIDs);
				$object+=1;
				continue;
			} 	
			
			$sql = "$wave_def[5] $wave_val[5]; "; 
			if (!mysqli_query($link,$sql))
			{
				$log->logInfo("sql error list: ",mysqli_error_list($link));
				$log->logInfo("error: $sql", mysqli_error($sql));
				mysqli_rollback($link);
				array_pop($objIDs);
				$object+=1;
				continue;
				
			} 				
			$sql = "$flux_def[5] $flux_val[5]; "; 
			if (!mysqli_query($link,$sql))
			{
				$log->logInfo("sql error list: ",mysqli_error_list($link));
				$log->logInfo("error: $sqsl", mysqli_error($sql));
				mysqli_rollback($link);
				array_pop($objIDs);
				$object+=1;
				continue;	
			} 
			
			$link->commit();
			mysqli_autocommit($link, TRUE);			
			unset($flux_def); unset($flux_val); 
			unset($wave_def); unset($wave_val); 
			
			$object+=1;
			
		} // end if spec > 0
	
	} // end for each row
	
	if($exist == 0){
		echo json_encode("http://cas.sdss.org/dr7/en/tools/search/x_sql.asp?cmd=" . $sql . "&format=html");
		die;
	} // end if !exists
	
	$log->logInfo("len unique:", count($unique));
	$log->logInfo("len stored:", count($stored));
	$log->logInfo("len objid:",count($objID));
	
	$ret = array_merge($stored, $objIDs);
	
	return $ret;
	
} // end querySDSS()


?>
