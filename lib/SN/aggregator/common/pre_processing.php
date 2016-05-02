<?php
/*
============================================================================================
Filename: 
---------
pre_processing.php

Description: 
------------
This PHP file is the general interface to interact with MySQL database, taking care
of all kinds of logical transaction.

Di Bao
02/13/2013
ADMT Lab - Supernovae Project
============================================================================================
*/
function pre_processing(&$mysqli_handler, $_feed_id, $_entry_content, &$_object_arrays){

	if(in_array($_feed_id, array(1, 2, 3, 4))){
		$_entry_content = str_replace('<entry>', '<entry xmlns:voevent="http://www.skyalert.org/static/voevent_namespace.html">', $_entry_content);
	}else if($_feed_id == 5){
		$_entry_content = str_replace('<item', '<item xmlns:rdf="#" xmlns:dc="#"', $_entry_content);
	}else{
		;
	}

	$xml = new SimpleXMLElement($_entry_content);
	if((is_object($xml) == false) || (sizeof($xml) <= 0)){
		error_handler("cannot parse XML string, exit pre_processing. ", ERROR_LOG_FETCH);
		return 4;
	}

	if($_feed_id == 1){
	/* Parse (name, ra, dec) for "Skyalert/CBAT: Central Bureau for Astronomical Telegrams" */
		$my_ra = doubleval(trim(strval($xml->children('voevent', true)->RA)));
		$my_dec = doubleval(trim(strval($xml->children('voevent', true)->Dec)));
		
		$link_url = trim(strval($xml->link[1]['href']));
		$recv = utf8_encode(perform_curl_operation($link_url, null));
		$_xml = simplexml_load_string($recv);
		if((is_object($_xml) == false) || (sizeof($_xml) <= 0)){	
			error_handler("cannot parse XML string, exit pre_processing. ", ERROR_LOG_FETCH);
			return 4;
		}
		$my_name = trim(strval($_xml->What->Param[1]['value']));
		array_push($_object_arrays, array($my_ra, $my_dec, $my_name));
		
		return 0;
	}else if($_feed_id == 2){
	/* Parse (name, ra, dec) for "Skyalert/CRTS: CRTS and SDSS Galaxy" */
		$my_ra = doubleval(trim(strval($xml->children('voevent', true)->RA)));
		$my_dec = doubleval(trim(strval($xml->children('voevent', true)->Dec)));
		$my_name = trim(strval($xml->title));
		array_push($_object_arrays, array($my_ra, $my_dec, $my_name));
		
		return 0;
	}else if($_feed_id == 3){
	/* Parse (name, ra, dec) for "Skyalert/CRTS2: Bright CRTS2" */
		$my_ra = doubleval(trim(strval($xml->children('voevent', true)->RA)));
		$my_dec = doubleval(trim(strval($xml->children('voevent', true)->Dec)));
		$my_name = trim(strval($xml->title));
		array_push($_object_arrays, array($my_ra, $my_dec, $my_name));
		
		return 0;
	}else if($_feed_id == 4){
	/* Parse (name, ra, dec) for "Skyalert/CRTS: CRTS and P60" */
		$my_ra = doubleval(trim(strval($xml->children('voevent', true)->RA)));
		$my_dec = doubleval(trim(strval($xml->children('voevent', true)->Dec)));
		$my_name = trim(strval($xml->title));
		array_push($_object_arrays, array($my_ra, $my_dec, $my_name));
		
		return 0;
	}else if($_feed_id == 5){
	/* Parse (name, ra, dec) for "The Astronomer's Telegram: supernovae" */
		$my_ra = null;
		$my_dec = null;
		$my_name = null;
		
		$title_str = trim(strval($xml->title));
		$link_url = trim(strval($xml->link));
		$recv_str = utf8_encode(perform_curl_operation($link_url, null));

		// situation 1: the item is annotation on one particular SN candicate, with informal name provided.
		if(preg_match('/PSN|(SN|SUPERNOVA)\s*(\d+\s*\w+)|CSS|MASTER OT/i', $title_str)){
			
			// format 1 - PSN
			$pattern1 = '/PSN\s*J\s*\d+[\-|\+]\d+/i';
			// format 2 - SN type 1
			$pattern2 = '/SUPERNOVA\s*(\d+\w+)/i';
			// format 3 - SN type 2
			$pattern3 = '/SN\s*(\d+\w+)/i';
			// format 4 - CSS
			$pattern4 = '/CSS\s*\d+\s*\:\s*\d+\s*[\-|\+]\s*\d+/i';
			// format 5 - MASTER OT
			$pattern5 = '/MASTER\s*OT\s*J\s*\d+\.\d+[\-|\+]\d+\.\d+/i';
			
			if(preg_match($pattern1, $title_str, $match)){
				$my_name = $match[0];
				$search_name = $my_name;
			}

			else if(preg_match($pattern2, $title_str, $match)){
				$my_name = $match[0];
				$search_name = $match[1];
			}

			else if(preg_match($pattern3, $title_str, $match)){
				$my_name = $match[0];
				$search_name = $match[1];
			}

			else if(preg_match($pattern4, $title_str, $match)){
				$my_name = $match[0];
				$search_name = $my_name;
			}
			
			else if(preg_match($pattern5, $title_str, $match)){
				$my_name = $match[0];
				$search_name = $my_name;
			}
			
			else{
				return 2;
			}
			
			$stmt = $mysqli_handler->stmt_init();
			$stmt->prepare("SELECT object_ra, object_dec FROM `SN_objects` WHERE object_name LIKE CONCAT('%', ?, '%') LIMIT 1");
			$stmt->bind_param("s", $search_name);
			$stmt->execute();
			$stmt->bind_result($my_ra, $my_dec);
			if($stmt->fetch()){
				$my_ra = doubleval($my_ra);
				$my_dec = doubleval($my_dec);
				$stmt->close();
				
				array_push($_object_arrays, array($my_ra, $my_dec, $my_name));
				return 1;
			}else{
				$stmt->close();
				return 3;
			}
		}
		
		// situation 2: the item is annotation on mutiple SN candicates, with (ra, dec, name) listed.
		else if(preg_match('/classification|spectroscopic|confirmation/i', $title_str)){
			/*
			echo "\n\n######\n\n";
			echo $recv_str;
			echo "\n\n######\n\n";
			*/
			
			// double check such case
			// *** the following Regex under watching ***
			$format_of_table = '/(Name|CRTS Detection ID)(.)+?RA(.)+?Dec|LSQ ID/i';
			if(preg_match($format_of_table, $recv_str, $matches)){
				;
			}else{
				//var_dump($matches);
				return 4;
			}
			
			$match_flag = false; // match exactly once and stop
			
			// format 1 - PESSTO standard
			$pattern1 = '/^(.+?)[\s|\|]{1,}?(\d+)\s+?(\d+)\s+?(\d+\.\d+)[\s|\|]{1,}?(\-|\+)\s*?(\d+)\s+?(\d+)\s+?(\d+\.\d+)/im';
			// format 2 - colon separated verison, multiple applications...
			$pattern2 = '/^(.+?)\s+?(\d+)\s*\:\s*(\d+)\s*\:\s*(\d+\.\d+)\s+?(\-|\+)\s*(\d+)\s*\:\s*(\d+)\s*\:\s*(\d+\.\d+)/im';
			// format 3 - unknown version
			$pattern3 = '/^(.+?)\s+?\d?\-?\s+?(\d+)\:(\d+)\:(\d+\.\d+)\s+?(\-|\+)*(\d+)\:(\d+)\:(\d+\.\d+)/im';
			// format 4 - the | and : version
			$pattern4 = '/^(.+?)[\s|\|]+?(\d+)\s*\:\s*(\d+)\s*\:\s*(\d+\.\d+)[\s|\|]+?(\-|\+)\s*(\d+)\s*\:\s*(\d+)\s*\:\s*(\d+\.\d+)/im';
			// format 5 - another column between name column and ra/dec columns
			$pattern5 = '/^(.+?)\s+March \d+\.\d+\s+(\d+)\s*\:\s*(\d+)\s*\:\s*(\d+\.\d+)\s+?(\-|\+)\s*(\d+)\s*\:\s*(\d+)\s*\:\s*(\d+\.\d+)/im';
			
			if(
				preg_match_all($pattern1, $recv_str, $matches, PREG_SET_ORDER) ||
				preg_match_all($pattern2, $recv_str, $matches, PREG_SET_ORDER) ||
				preg_match_all($pattern3, $recv_str, $matches, PREG_SET_ORDER) ||
				preg_match_all($pattern4, $recv_str, $matches, PREG_SET_ORDER) ||
				preg_match_all($pattern5, $recv_str, $matches, PREG_SET_ORDER)
			){
				$match_flag = true;
				foreach($matches as $item){
					$my_name = strval($item[1]);
					$rah = doubleval($item[2]);
					$ram = doubleval($item[3]);
					$ras = doubleval($item[4]);
					
					$decd = doubleval($item[6]);
					$decm = doubleval($item[7]);
					$decs = doubleval($item[8]);
					$dec_sign = strval($item[5]);
					if($dec_sign != "-")	$dec_sign = "+";
					
					list($my_ra, $my_dec) = convert_ra_dec($rah, $ram, $ras, $decd, $decm, $decs, $dec_sign);
					array_push($_object_arrays, array($my_ra, $my_dec, $my_name));
				}
			}else{
				return 2;
			}
			
			if(count($_object_arrays) > 0){
				return 1;
			}else{
				return 4;
			}
		}

		// situation 3: currently no pattern, trying to find (name, ra, dec) again in free-text.
		else{
			
			// possible 1
			if(preg_match('/MASTER OT/i', $recv_str) || preg_match('/\([ |\s]*RA[ |\s]*\,[ |\s]*Dec[ |\s]*\)/i', $recv_str)){
				$pattern_name = '/MASTER OT[ |\s]*J(\d+)(\.\d+)*[\-|\+](\d+)(\.\d+)*/i';
				$pattern_ra_dec = '/\([ |\s]*RA[ |\s]*\,[ |\s]*Dec[ |\s]*\)[ |\s]*\=[ |\s]*(\d+)h[ |\s]*(\d+)m[ |\s]*(\d+\.\d+)s[ |\s]*([\-|\+])[ |\s]*(\d+)d[ |\s]*(\d+)m[ |\s]*(\d+\.\d+)s/i';
				if(preg_match_all($pattern_name, $recv_str, $matches_name, PREG_PATTERN_ORDER) &&
					preg_match_all($pattern_ra_dec, $recv_str, $matches_ra_dec, PREG_SET_ORDER)){
					$unique_names = array_values(array_unique($matches_name[0]));
					
					//if(DEBUG_MODE)	
					if(DEBUG_MODE)	echo "\n\n";
					if(DEBUG_MODE)	var_dump($unique_names);
					if(DEBUG_MODE)	echo "\n\n";
					
					for($i = 0; $i < count($unique_names); $i++){
						$my_name = strval($unique_names[$i]);
						$rah = doubleval($matches_ra_dec[$i][1]);
						$ram = doubleval($matches_ra_dec[$i][2]);
						$ras = doubleval($matches_ra_dec[$i][3]);
						
						$decd = doubleval($matches_ra_dec[$i][5]);
						$decm = doubleval($matches_ra_dec[$i][6]);
						$decs = doubleval($matches_ra_dec[$i][7]);
						$dec_sign = strval($matches_ra_dec[$i][4]);
						
						list($my_ra, $my_dec) = convert_ra_dec($rah, $ram, $ras, $decd, $decm, $decs, $dec_sign);
						array_push($_object_arrays, array($my_ra, $my_dec, $my_name));
					}
					
					return 1;
				}else{
					return 4;
				}
			}
			
			// possible n
			else{
				// format 1 - PSN
				$pattern1 = '/PSN\s*J\s*\d+[\-|\+]\d+/i';
				// format 2 - SN type 1
				$pattern2 = '/SUPERNOVA\s*(\d+\w+)/i';
				// format 3 - SN type 2
				$pattern3 = '/SN\s*(\d+\w+)/i';
				// format 4 - CSS
				$pattern4 = '/CSS\s*\d+\s*\:\s*\d+\s*[\-|\+]\s*\d+/i';
				// format 5 - MASTER OT
				$pattern5 = '/MASTER\s*OT\s*J\s*\d+\.\d+[\-|\+]\d+\.\d+/i';
				preg_match_all($pattern1, $recv_str, $matches1, PREG_SET_ORDER);
				preg_match_all($pattern2, $recv_str, $matches2, PREG_SET_ORDER);
				preg_match_all($pattern3, $recv_str, $matches3, PREG_SET_ORDER);
				preg_match_all($pattern4, $recv_str, $matches4, PREG_SET_ORDER);
				preg_match_all($pattern5, $recv_str, $matches5, PREG_SET_ORDER);
				
				$names_array = array();
				foreach($matches1 as $match){
					array_push($names_array, $match[0]);
				}
				foreach($matches2 as $match){
					array_push($names_array, $match[0]);
				}
				foreach($matches3 as $match){
					array_push($names_array, $match[0]);
				}
				foreach($matches4 as $match){
					array_push($names_array, $match[0]);
				}
				foreach($matches5 as $match){
					array_push($names_array, $match[0]);
				}
				
				$unique_names_array = array_values(array_unique($names_array));
				
				foreach($unique_names_array as $obj_name){
					$stmt = $mysqli_handler->stmt_init();
					$stmt->prepare("SELECT object_ra, object_dec FROM `SN_objects` WHERE object_name LIKE CONCAT('%', ?, '%') LIMIT 1");
					$stmt->bind_param("s", $obj_name);
					$stmt->execute();
					$stmt->bind_result($my_ra, $my_dec);
					if($stmt->fetch()){
						$my_ra = doubleval($my_ra);
						$my_dec = doubleval($my_dec);
						$stmt->close();
						
						array_push($_object_arrays, array($my_ra, $my_dec, $obj_name));
					}else{
						$stmt->close();
					}					
				}
				
				//var_dump($unique_names_array);
				//var_dump($_object_arrays);
				//exit;
				
				if(count($_object_arrays) > 0){
					return 1;
				}else if(count($unique_names_array) > 0){
					return 3;
				}
			}
			
			// other possibles...
			return 2;
		}
	}else if($_feed_id == 6){
	/* Parse (name, ra, dec) for "CBET: Supernovae" */
		$my_ra = null;
		$my_dec = null;
		$my_name = trim(strval($xml->title));
		
		// situation 1: the entry report new SN candicate
		$pattern_ra = '/R\.A\./i';
		$pattern_dec = '/Decl\./i';
		if(preg_match($pattern_ra, trim(strval($xml->content))) && preg_match($pattern_dec, trim(strval($xml->content)))){
		
			// format 1
			$pattern1 = '/
						R\.A\.[\s|\r|\n]*=[\s|\r|\n]*(\d+)h(\d+)m(\d+)s\.(\d+)	(?# catch RA)
						[\s|\r|\n]*.*[\s|\r|\n]*\,[\s|\r|\n]*
						Decl\.[\s|\r|\n]*=[\s|\r|\n]*(\+|\-)[\s|\r|\n]*(\d+)[d|o](\d+)\'(\d+)\"\.(\d+)	(?# catch Dec)
						/ix';
						
			// format 2
			$pattern2 = '/
						R\.A\.[\s|\r|\n]*\(\d+\.\d+\)[\s|\r|\n]*Decl\.	(?# catch first line of list)
						(.|\r|\n)+?
						(\d+)\s+(\d+)\s+(\d+\.\d+)\s+(\+|\-)\s*(\d+)\s+(\d+)\s+(\d+\.\d+)	(?# catch second line of list)
						/ix';
			
			if(preg_match($pattern1, trim(strval($xml->content)), $matches)){
				$rah = doubleval($matches[1]);
				$ram = doubleval($matches[2]);
				$ras = doubleval(strval($matches[3]) . '.' . strval($matches[4]));
				
				$decd = doubleval($matches[6]);
				$decm = doubleval($matches[7]);
				$decs = doubleval(strval($matches[8]) . '.' . strval($matches[9]));
				$dec_sign = strval($matches[5]);
				
				list($my_ra, $my_dec) = convert_ra_dec($rah, $ram, $ras, $decd, $decm, $decs, $dec_sign);
			}
			else if(preg_match($pattern2, trim(strval($xml->content)), $matches)){
				$rah = doubleval($matches[2]);
				$ram = doubleval($matches[3]);
				$ras = doubleval($matches[4]);
				
				$decd = doubleval($matches[6]);
				$decm = doubleval($matches[7]);
				$decs = doubleval($matches[8]);
				$dec_sign = strval($matches[5]);
				
				list($my_ra, $my_dec) = convert_ra_dec($rah, $ram, $ras, $decd, $decm, $decs, $dec_sign);
			}else{
				return 2;
			}
			
			if($my_ra && $my_dec){
				array_push($_object_arrays, array($my_ra, $my_dec, $my_name));
				return 0;
			}else{
				return 4;
			}
		}else{ // situation 2: the entry is annotation for existing SN candicate
			$the_title_name = $my_name;
			$pattern_name_SN = '/SUPERNOVA\s*(\d+\w+)/i';
			$pattern_name_PSN = '/PSN\s*J\s*\d+\s*(\+|\-)\s*\d+/i';
			$SN_name = "not_possible_name";
			$PSN_name = "not_possible_name";
			if(preg_match($pattern_name_SN, $the_title_name, $match))	$SN_name = $match[1];
			if(preg_match($pattern_name_PSN, $the_title_name, $match))	$PSN_name = $match[0];
			if($SN_name == "not_possible_name" && $PSN_name == "not_possible_name"){
				return 2; // the title has unclassified format, or even the entry's format not belongs to situation 2
			}
		
			$stmt = $mysqli_handler->stmt_init();
			$stmt->prepare("SELECT object_ra, object_dec FROM `SN_objects` WHERE object_name LIKE CONCAT('%', ?, '%') OR object_name LIKE CONCAT('%', ?, '%') LIMIT 1");
			$stmt->bind_param("ss", $SN_name, $PSN_name);
			$stmt->execute();
			$stmt->bind_result($my_ra, $my_dec);
			if($stmt->fetch()){
				$my_ra = doubleval($my_ra);
				$my_dec = doubleval($my_dec);
				$stmt->close();
				
				array_push($_object_arrays, array($my_ra, $my_dec, $my_name));
				return 1;
			}else{
				$stmt->close();
				return 3;
			}
		}
		
		/*
		if(DEBUG_MODE)	echo "Matched pattern pos1:\n";
		if(DEBUG_MODE)	echo "ra is $my_ra, dec is $my_dec\n";
		if(DEBUG_MODE)	echo "Matched pattern pos2:\n";
		if(DEBUG_MODE)	echo "ra is $my_ra, dec is $my_dec\n";
		if(DEBUG_MODE)	echo "SN name is $my_name\n---------------------\n\n";
		*/
	}else{
		return 4;
	}
	
	return 4;
}
?>