<?php
/*
============================================================================================
Filename: 
---------
post_processing.php

Description: 
------------
This PHP file is the general interface to interact with MySQL database, taking care
of all kinds of logical transaction.

Di Bao
02/13/2013
ADMT Lab - Supernovae Project
============================================================================================
*/

function post_processing(&$mysqli_handler, $_feed_id, $_entry_content, &$_misc_arrays){

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
		return false;
	}
	
	if($_feed_id == 1){
	/* Parse (name, ra, dec) for "Skyalert/CBAT: Central Bureau for Astronomical Telegrams" */
		$_misc = array();
		$_misc['type'] = NULL;
		$_misc['redshift'] = NULL;
		$_misc['disc_mag'] = NULL;
		$_misc['phase'] = NULL;
	
		$link_url = trim(strval($xml->link[1]['href']));
		$recv = utf8_encode(perform_curl_operation($link_url, null));
		$_xml = simplexml_load_string($recv);
		if((is_object($_xml) == false) || (sizeof($_xml) <= 0)){	
			error_handler("cannot parse XML string, exit pre_processing. ", ERROR_LOG_FETCH);
			return false;
		}
		$_misc['disc_mag'] = trim(strval($_xml->What->Param[0]['value']));
		
		array_push($_misc_arrays, $_misc);
		
		return true;
	}else if($_feed_id == 2){
	/* Parse (name, ra, dec) for "Skyalert/CRTS: CRTS and SDSS Galaxy" */
		$_misc = array();
		$_misc['type'] = NULL;
		$_misc['redshift'] = NULL;
		$_misc['disc_mag'] = NULL;
		$_misc['phase'] = NULL;

		$title = trim(strval($xml->title));
		$pattern_mag = '/(\d+)\.(\d+)/i';
		preg_match($pattern_mag, $title, $matched);
		$_misc['disc_mag'] = $matched[0];
		
		array_push($_misc_arrays, $_misc);
		
		return true;
	}else if($_feed_id == 3){
	/* Parse (name, ra, dec) for "Skyalert/CRTS2: Bright CRTS2" */
		$_misc = array();
		$_misc['type'] = NULL;
		$_misc['redshift'] = NULL;
		$_misc['disc_mag'] = NULL;
		$_misc['phase'] = NULL;

		$title = trim(strval($xml->title));
		$pattern_mag = '/(\d+)\.(\d+)/i';
		preg_match($pattern_mag, $title, $matched);
		$_misc['disc_mag'] = $matched[0];
		
		array_push($_misc_arrays, $_misc);
		
		return true;
	}else if($_feed_id == 4){
	/* Parse (name, ra, dec) for "Skyalert/CRTS: CRTS and P60" */
		$_misc = array();
		$_misc['type'] = NULL;
		$_misc['redshift'] = NULL;
		$_misc['disc_mag'] = NULL;
		$_misc['phase'] = NULL;

		$title = trim(strval($xml->title));
		$pattern_mag = '/(\d+)\.(\d+)/i';
		preg_match($pattern_mag, $title, $matched);
		$_misc['disc_mag'] = $matched[0];
		
		array_push($_misc_arrays, $_misc);
		
		return true;
	}else if($_feed_id == 5){
	/* Parse (name, ra, dec) for "The Astronomer's Telegram: supernovae" */
		$_misc = array();
		$_misc['type'] = NULL;
		$_misc['redshift'] = NULL;
		$_misc['disc_mag'] = NULL;
		$_misc['phase'] = NULL;

		$title_str = trim(strval($xml->title));
		$link_url = trim(strval($xml->link));
		$recv_str = utf8_encode(perform_curl_operation($link_url, null));

		// situation 1: the item is annotation on one particular SN candicate, with informal name provided.
		if(preg_match('/PSN|(SN|SUPERNOVA)\s*(\d+\s*\w+)|CSS|MASTER OT/i', $title_str)){
		
			// *** search for type info. ***
			$pattern_type1 = '/type (\w+)/i';
			$pattern_type2 = '/type\-(\w+)/i';
			if(preg_match_all($pattern_type1, $recv_str, $matched_type, PREG_SET_ORDER)){
				foreach($matched_type as &$type){
					if($type[1] == "HTML"){
						continue;
					}else{
						$_misc['type'] = $type[1];
						break;
					}
				}
			}else if(preg_match_all($pattern_type2, $recv_str, $matched_type, PREG_SET_ORDER)){
				foreach($matched_type as &$type){
					if($type[1] == "HTML"){
						continue;
					}else{
						$_misc['type'] = $type[1];
						break;
					}
				}
			}else{
				;
			}
			
			// *** search for redshift info. ***
			$pattern_redshift1 = '/redshift\s*(.+?)\s*(\d+\.\d+)/i';
			$pattern_redshift2 = '/z\s*(.+?)\s*(\d+\.\d+)/i';
			if(preg_match($pattern_redshift1, $recv_str, $matched_redshift)){
				$_misc['redshift'] = $matched_redshift[2];
			}else if(preg_match($pattern_redshift2, $recv_str, $matched_redshift)){
				$_misc['redshift'] = $matched_redshift[2];
			}else{
				;
			}
			
			array_push($_misc_arrays, $_misc);
		}
		
		else if(preg_match('/classification|spectroscopic|confirmation/i', $title_str)){
			
			// double check such case
			// *** the following Regex under watching ***
			$format_of_table = '/(Name|CRTS Detection ID)(.)+?RA(.)+?Dec|LSQ ID/i';
			if(preg_match($format_of_table, $recv_str, $matches)){
				;
			}else{
				//var_dump($matches);
				return 4;
			}	
			
			if(preg_match('/PESSTO/i', $title_str)){
				$pattern = '/^.+?\|.+?\|.+?\|.+?\|.+?\|(.+?)\|(.+?)\|(.+?)\|(.+?)\|.+?$/im';
				$i = 1;
				
				preg_match_all($pattern, $recv_str, $matched, PREG_SET_ORDER);
				for(; $i < count($matched); $i++){
					$_misc['type'] = trim(strval($matched[$i][3]));
					$_misc['redshift'] = trim(strval($matched[$i][2]));
					$_misc['disc_mag'] = trim(strval($matched[$i][1]));
					$_misc['phase'] = trim(strval($matched[$i][4]));
					
					array_push($_misc_arrays, $_misc);
				}
			}else if(preg_match('/La\s*\-?\s*Silla\s*\-?\s*QUEST/i', $title_str)){
				$pattern = '/^.+?\|.+?\|.+?\|.+?\|.+?\|(.+?)\|.+?\|(.+?)\|(.+?)\|(.+?)$/im';
				$i = 2;
				
				preg_match_all($pattern, $recv_str, $matched, PREG_SET_ORDER);
				for(; $i < count($matched); $i++){
					$_misc['type'] = trim(strval($matched[$i][3]));
					$_misc['redshift'] = trim(strval($matched[$i][2]));
					$_misc['disc_mag'] = trim(strval($matched[$i][1]));
					$_misc['phase'] = trim(strval($matched[$i][4]));
					
					array_push($_misc_arrays, $_misc);
				}
			}else if(preg_match('/APO/i', $title_str)){
				$pattern = '/^.+?\|.+?\|.+?\|.+?\|(.+?)\|(.+?)\|(.+?)\|.+?\|(.+?)\|.+?$/im';
				$i = 1;
				
				preg_match_all($pattern, $recv_str, $matched, PREG_SET_ORDER);
				for(; $i < count($matched); $i++){
					$_misc['type'] = trim(strval($matched[$i][3]));
					$_misc['redshift'] = trim(strval($matched[$i][2]));
					$_misc['disc_mag'] = trim(strval($matched[$i][1]));
					$_misc['phase'] = trim(strval($matched[$i][4]));
					
					array_push($_misc_arrays, $_misc);
				}
			}else if(preg_match('/DES/i', $title_str)){
				$pattern = '/^.+?\|.+?\|.+?\|.+?\|(.+?)\|.+?\|(.+?)\|(.+?)\|(.+?)$/im';
				$i = 2;
				
				preg_match_all($pattern, $recv_str, $matched, PREG_SET_ORDER);
				for(; $i < count($matched); $i++){
					$_misc['type'] = trim(strval($matched[$i][3]));
					$_misc['redshift'] = trim(strval($matched[$i][2]));
					$_misc['disc_mag'] = trim(strval($matched[$i][1]));
					$_misc['phase'] = trim(strval($matched[$i][4]));
					
					array_push($_misc_arrays, $_misc);
				}
			}else if(preg_match('/CRTS/i', $title_str)){
				$pattern = '/^.+?\s+(\w+)\s+(\d+\.\d+)\s+(.+?)\s+.+?$/im';
				$i = 0;
				
				preg_match_all($pattern, $recv_str, $matched, PREG_SET_ORDER);
				for(; $i < count($matched); $i++){
					$_misc['type'] = trim(strval($matched[$i][1]));
					$_misc['redshift'] = trim(strval($matched[$i][2]));
					$_misc['phase'] = trim(strval($matched[$i][3]));
					
					array_push($_misc_arrays, $_misc);
				}
			}else{
				;
			}
		}
		
		else{
			; // very hard to parse...
		}
	
		return true;
	}else if($_feed_id == 6){
	/* Parse (name, ra, dec) for "CBET: Supernovae" */
		$_misc = array();
		$_misc['type'] = NULL;
		$_misc['redshift'] = NULL;
		$_misc['disc_mag'] = NULL;
		$_misc['phase'] = NULL;
		
		$_content = trim(strval($xml->content));

		$pattern_ra = '/R\.A\./i';
		$pattern_dec = '/Decl\./i';		
		if(preg_match($pattern_ra, $_content) && preg_match($pattern_dec, $_content)){
			$pattern_type = '/type\-(\w+)/i';
			$pattern_redshift = '/redshift\s*(.|\r|\n)+?\s*(\d+\.\d+)/i';
			$pattern_mag1 = '/mag\s*\w*?\s*?(\d+\.\d+)/i';
			$pattern_mag2 = '/Mag\.[\s|\r|\n]*.+?(\d+\.\d+)\s*$/im';
			$pattern_mag3 = '/R\.A\.\s*\(\d+\.\d+\)\s*Decl\.\s*Mag\.(.|\r|\n)+?(\d+)\s+(\d+)\s+(\d+\.\d+)\s+(\+|\-)\s*(\d+)\s+(\d+)\s+(\d+\.\d+)\s+(\d+\.\d+)/i';
			
			if(preg_match($pattern_type, $_content, $matched_type)){
				$_misc['type'] = $matched_type[1];
			}
			
			if(preg_match($pattern_redshift, $_content, $matched_redshift)){
				$_misc['redshift'] = $matched_redshift[2];
			}
			
			if(preg_match($pattern_mag1, $_content, $matched_mag)){
				$_misc['disc_mag'] = $matched_mag[1];
			}elseif(preg_match($pattern_mag2, $_content, $matched_mag)){
				$_misc['disc_mag'] = $matched_mag[1];
			}elseif(preg_match($pattern_mag3, $_content, $matched_mag)){
				$_misc['disc_mag'] = $matched_mag[9];
			}else{
				;
			}
		}else{
			; // if the entry is annotation for other object, no need to fetch duplicated misc. info.
		}
		
		array_push($_misc_arrays, $_misc);
		
		return true;
	}else{
		;
	}
	
	return false;
}
?>