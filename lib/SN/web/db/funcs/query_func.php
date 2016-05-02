<?php
/*
============================================================================================
Filename: 
---------
query_func.php

Description: 
------------
This PHP file is a function to do detailed query/retrieve.

Di Bao
02/25/2013

Edited: Nikhil Venkatesh - 06/25/13
ADMT Lab - Supernovae Project
============================================================================================
*/

function query_func(&$_mysqli, $_offset = "all", $_limit = "all", $_orderby = "unique_id", $_sort = "DESC", $_para_array){
	
	/* Array of database columns which should be read and sent back to DataTables */
	$nColumns = array( 'object_name', 'object_ra', 'object_dec', 'object_type', 'object_disc_mag', 'object_redshift', 'msg_update_ts' );
	$rdColumns = array( 'unique_ra', 'unique_dec', 'unique_ra_hmsdms', 'unique_dec_hmsdms', 'object_name', 'object_type', 'object_disc_mag', 'object_redshift', 'msg_update_ts' );
	$paramColumns = array( 'o.object_ra', 'o.object_dec', 'o.object_disc_mag', 'o.object_redshift', 'o.object_type');
	$operatorColumns = array( 'BETWEEN', '=', '!=', '<', '<=', '>', '>=');

	$searchType = intval($_POST['search']);
	/*
	* Paging
	*/
	$sLimit = "";
	if ( isset( $_POST['iDisplayStart'] ) && $_POST['iDisplayLength'] != '-1' )
	{
		$sLimit = "LIMIT ".mysqli_real_escape_string($_mysqli, $_POST['iDisplayStart'] ).", ".
			mysqli_real_escape_string($_mysqli, $_POST['iDisplayLength'] );
	}
	
	//Commented out by eric 09-14-15, causing errors - 'testTable' does not exist
	/*
	* Ordering
	*
	if ( isset( $_POST['iSortCol_0'] ) )
	{
		$sOrder = "ORDER BY `testTable`.";
		
		for ( $i=0; $i<intval( $_POST['iSortingCols'] ); $i++ )
		{
			$sort_col_val = intval( $_POST['iSortCol_'.$i] );

			if ( $_POST[ 'bSortable_'. $sort_col_val ] == "true" )
			{
				if ($searchType == 1 || $searchType == 2 || $searchType == 3 || $searchType == 4)
					$sOrder .= "`" . $nColumns[ $sort_col_val ]."` ".mysqli_real_escape_string($_mysqli, $_POST['sSortDir_'.$i] ) .", ";
			}
		}

		$sOrder = substr_replace( $sOrder, "", -2 );
		if ( $sOrder == "ORDER BY" )
		{
			$sOrder = "";
		}
	}
	*/
	/*
	* Filtering
	*/
	$sWhere = "";
	if ( $_POST['sSearch'] != "")
	{
		$sWhere = "WHERE (";
			if ($searchType == 1 || $searchType == 2 || $searchType == 3 || $searchType == 4) {
				for ( $i=0; $i<count($nColumns); $i++ )
				{
					$sWhere .= $nColumns[$i]." LIKE '%".mysqli_real_escape_string($_mysqli, $_POST['sSearch'] )."%' OR ";
				}
			}
			// } elseif ($searchType == 2) {
			// 	for ( $i=0; $i<count($rdColumns); $i++ )
			// 	{
			// 		$sWhere .= $rdColumns[$i]." LIKE '%".mysqli_real_escape_string($_mysqli, $_POST['sSearch'] )."%' OR ";
			// 	}
			// }
			$sWhere = substr_replace( $sWhere, "", -3 );
			$sWhere .= ')';
	}

	if ($searchType == 1 || $searchType == 2 || $searchType == 3 || $searchType == 4) {
		/* Individual column filtering */
		for ( $i=0; $i<count($nColumns); $i++ )
		{
			if ( $_POST['bSearchable_'.$i] == "true" && $_POST['sSearch_'.$i] != '' )
			{
				if ( $sWhere == "" )
				{
					$sWhere = "WHERE ";
				}	 
				else 
				{
					$sWhere .= " AND ";
				}
				$sWhere .= $nColumns[$i]." LIKE '%".mysqli_real_escape_string($_mysqli, $_POST['sSearch_'.$i])."%' ";
			}
		}
	} 
	// elseif ($searchType == 2) {
	// 	for ( $k=0; $k<count($rdColumns); $k++ )
	// 	{
	// 		if ( $_POST['bSearchable_'.$k] == "true" && $_POST['sSearch_'.$k] != '' )
	// 		{
	// 			if ( $sWhere == "" )
	// 			{
	// 				$sWhere = "WHERE ";
	// 			}	 
	// 			else 
	// 			{
	// 				$sWhere .= " AND ";
	// 			}
	// 			$sWhere .= $rdColumns[$k]." LIKE '%".mysqli_real_escape_string($_mysqli, $_POST['sSearch_'.$k])."%' ";
	// 		}
	// 	}
	// }

	$res = array();
	$res["aaData"] = array();
	/*
	* SQL queries
	* Get data to display
	*/

	if(array_key_exists('browse', $_para_array)){
		$query_uni = "SELECT SQL_CALC_FOUND_ROWS unique_id, unique_ra, unique_dec, object_ra, object_dec, object_name, object_type, object_disc_mag, object_redshift, msg_update_ts FROM ";
		$query_uni .= "(SELECT unique_id, unique_ra, unique_dec, object_ra, object_dec, object_name, object_type, object_disc_mag, object_redshift, msg_update_ts";
		$query_uni .= " FROM `SN_uniques` as u INNER JOIN `SN_matches` as m ON u.unique_id = m.match_unique_id";
		$query_uni .= " INNER JOIN `SN_objects` as o ON m.match_object_id = o.object_id";
		$query_uni .= " INNER JOIN `SN_messages` as mes ON o.object_msg_hashed = mes.msg_hashed";
		$query_uni .= " GROUP BY u.unique_id";
		$query_uni .= " UNION";
		$query_uni .= " SELECT unique_id, unique_ra, unique_dec, object_ra, object_dec, sn_name, object_type, object_disc_mag, object_redshift, sn_date";
		$query_uni .= " FROM `SN_uniques` as u INNER JOIN `SN_matches` as m ON u.unique_id = m.match_unique_id";
		$query_uni .= " INNER JOIN `SN_objects` as o ON m.match_object_id = o.object_id";
		$query_uni .= " INNER JOIN `SN_known_list_match` AS k ON k.kl_match_unique_id = u.unique_id";
		$query_uni .= " INNER JOIN `SN_known_list` AS l ON k.kl_match_sn_id = l.sn_id";
		$query_uni .= " WHERE k.kl_match_unique_id = m.match_unique_id GROUP BY u.unique_id)testTable ";

		if ($sWhere != null) {
			/* For DataTable */
			$query_uni .= $sWhere . " ";
			$query_uni .= "GROUP BY unique_id ";
			$query_uni .= $sOrder . " ";
			$query_uni .= $sLimit;
		} else {
			$query_uni .= "GROUP BY unique_id ";
			if($sOrder != null){
				$query_uni .= " " . $sOrder;
			} else {
				$query_uni .= " ORDER BY " . $_orderby . " " . $_sort;
			}
			
			$query_uni .= " " . $sLimit;
		}
	}
	
	else if(array_key_exists('name', $_para_array)){
		$query_uni = "SELECT SQL_CALC_FOUND_ROWS unique_id, unique_ra, unique_dec, object_ra, object_dec, object_name, object_type, object_disc_mag, object_redshift, msg_update_ts FROM ";
		$query_uni .= "(SELECT unique_id, unique_ra, unique_dec, object_ra, object_dec, object_name, object_type, object_disc_mag, object_redshift, msg_update_ts";
		$query_uni .= " FROM `SN_uniques` as u INNER JOIN `SN_matches` as m ON u.unique_id = m.match_unique_id";
		$query_uni .= " INNER JOIN `SN_objects` as o ON m.match_object_id = o.object_id";
		$query_uni .= " INNER JOIN `SN_messages` as mes ON o.object_msg_hashed = mes.msg_hashed";
		$query_uni .= " WHERE o.object_name LIKE '%" . $_para_array["name"] . "%' GROUP BY u.unique_id";
		$query_uni .= " UNION";
		$query_uni .= " SELECT unique_id, unique_ra, unique_dec, object_ra, object_dec, sn_name, object_type, object_disc_mag, object_redshift, sn_date";
		$query_uni .= " FROM `SN_uniques` as u INNER JOIN `SN_matches` as m ON u.unique_id = m.match_unique_id";
		$query_uni .= " INNER JOIN `SN_objects` as o ON m.match_object_id = o.object_id";
		$query_uni .= " INNER JOIN `SN_known_list_match` AS k ON k.kl_match_unique_id = u.unique_id";
		$query_uni .= " INNER JOIN `SN_known_list` AS l ON k.kl_match_sn_id = l.sn_id";
		$query_uni .= " WHERE k.kl_match_unique_id = m.match_unique_id";
		$query_uni .= " AND o.object_name LIKE '%" . $_para_array["name"] . "%' GROUP BY u.unique_id)testTable ";
		
		if ($sWhere != null) {
			/* For DataTable */
			$query_uni .= $sWhere . " ";
			$query_uni .= "GROUP BY unique_id ";
			$query_uni .= $sOrder . " ";
			$query_uni .= $sLimit;
		} else {
			$query_uni .= "GROUP BY unique_id ";
			if($sOrder != null){
				$query_uni .= " " . $sOrder;
			} else {
				$query_uni .= " ORDER BY " . $_orderby . " " . $_sort;
			}
			
			$query_uni .= " " . $sLimit;
		}
	}
	
	else if(array_key_exists('contain', $_para_array)){
		$query_uni = "SELECT * FROM `SN_uniques` WHERE unique_id IN (" . $_para_array["contain"] . ") ORDER BY " . $_orderby . " " . $_sort;
		if(strcasecmp($_offset, "all") && strcasecmp($_limit, "all")){
			$query_uni .= " LIMIT " . $_offset . ", " . $_limit;
		}
	}

	else if(array_key_exists('param', $_para_array)){
		$val_passed;
		$query_uni = "SELECT SQL_CALC_FOUND_ROWS unique_id, unique_ra, unique_dec, unique_ra_hmsdms, unique_dec_hmsdms";
		$query_uni .= " FROM `SN_uniques` as u, `SN_matches` as m, `SN_objects` as o";
		$query_uni .= " WHERE u.unique_id = m.match_unique_id AND o.object_id = m.match_object_id";
		for($i=0; $i<count($_para_array["param"]); $i++){
			if(is_numeric($_para_array["value"][$i]) || $operatorColumns[$_para_array["operator"][$i]] == "BETWEEN")
				$val_passed = str_replace("'", "", $_para_array["value"][$i]);
			else
				$val_passed = "'" . $_para_array["value"][$i] . "'";
			$param_used = str_replace("'", "", $paramColumns[$_para_array["param"][$i]]);
			$operator_used = str_replace("'", "", $operatorColumns[$_para_array["operator"][$i]]);
			$query_uni .= " AND " . $param_used . " " . $operator_used . " " . $val_passed;
		}

		if ($sWhere != null) {
			/* For DataTable */
			$query_uni .= $sWhere . " ";
			$query_uni .= "GROUP BY u.unique_id ";
			$query_uni .= $sOrder . " ";
			$query_uni .= $sLimit;
		} else {
			$query_uni .= " GROUP BY u.unique_id ";
			if($sOrder != null){
				$query_uni .= " " . $sOrder;
			} else {
				$query_uni .= " ORDER BY " . $_orderby . " " . $_sort;
			}
			
			$query_uni .= " " . $sLimit;
		}
	}
	
	else{
		$query_uni = "SELECT SQL_CALC_FOUND_ROWS unique_id, unique_ra, unique_dec, object_ra, object_dec, object_name, object_type, object_disc_mag, object_redshift, msg_update_ts FROM ";
		$query_uni .= "(SELECT unique_id, unique_ra, unique_dec, object_ra, object_dec, object_name, object_type, object_disc_mag, object_redshift, msg_update_ts";
		$query_uni .= " FROM `SN_uniques` as u INNER JOIN `SN_matches` as m ON u.unique_id = m.match_unique_id";
		$query_uni .= " INNER JOIN `SN_objects` as o ON m.match_object_id = o.object_id";
		$query_uni .= " INNER JOIN `SN_messages` as mes ON o.object_msg_hashed = mes.msg_hashed";
		$query_uni .= " GROUP BY u.unique_id";
		$query_uni .= " UNION";
		$query_uni .= " SELECT unique_id, unique_ra, unique_dec, object_ra, object_dec, sn_name, object_type, object_disc_mag, object_redshift, sn_date";
		$query_uni .= " FROM `SN_uniques` as u INNER JOIN `SN_matches` as m ON u.unique_id = m.match_unique_id";
		$query_uni .= " INNER JOIN `SN_objects` as o ON m.match_object_id = o.object_id";
		$query_uni .= " INNER JOIN `SN_known_list_match` AS k ON k.kl_match_unique_id = u.unique_id";
		$query_uni .= " INNER JOIN `SN_known_list` AS l ON k.kl_match_sn_id = l.sn_id";
		$query_uni .= " WHERE k.kl_match_unique_id = m.match_unique_id GROUP BY u.unique_id)testTable ";

		

		if ($sWhere != null) {
			/* For DataTable */
			$query_uni .= $sWhere;
			$query_uni .= " AND (unique_ra BETWEEN " . ($_para_array["ra"] - $_para_array["epsilon"]) . " AND " . ($_para_array["ra"] + $_para_array["epsilon"]);
			$query_uni .= ") AND (unique_dec BETWEEN " . ($_para_array["dec"] - $_para_array["epsilon"]) . " AND " . ($_para_array["dec"] + $_para_array["epsilon"]) . ") ";
			$query_uni .= "GROUP BY unique_ra ";
			$query_uni .= $sOrder . " ";
			$query_uni .= $sLimit;
		} else {
			$query_uni .= "WHERE (unique_ra BETWEEN " . ($_para_array["ra"] - $_para_array["epsilon"]) . " AND " . ($_para_array["ra"] + $_para_array["epsilon"]);
			$query_uni .= ") AND (unique_dec BETWEEN " . ($_para_array["dec"] - $_para_array["epsilon"]) . " AND " . ($_para_array["dec"] + $_para_array["epsilon"]) . ") ";
			$query_uni .= "GROUP BY unique_ra ";
			if($sOrder != null){
				$query_uni .= $sOrder;
			} else {
				$query_uni .= " ORDER BY " . $_orderby . " " . $_sort;
			}
			
			$query_uni .= " " . $sLimit;
		}
	}
	
	/*
	* Output
	*/
	$res_uni = $_mysqli->query($query_uni);
	$found_rows_query = "SELECT FOUND_ROWS()";
	$rResultFilterTotal = $_mysqli->query($found_rows_query) or die($_mysqli->error);
	list($iFilteredTotal) = $rResultFilterTotal->fetch_row();

	$res["sEcho"] = intval($_POST['sEcho']);
	$res["iTotalRecords"] = $iFilteredTotal;
	$res["iTotalDisplayRecords"] = $iFilteredTotal;
	while($row_uni = $res_uni->fetch_assoc()){
		//$sOutput .= "[";
		$record = array();
		$record["id"] = $row_uni["unique_id"];
		$record["ra"] = round(floatval($row_uni["unique_ra"]), 5);
		$record["dec"] = round(floatval($row_uni["unique_dec"]), 5);
		$record["hmsdms"] = $row_uni["unique_ra_hmsdms"] . $row_uni["unique_dec_hmsdms"];
		
		// start first join on `SN_objects`
		$record["names"] = array();
		$record["miscs"] = array();
		$record["messages"] = array();
		$query_obj = "SELECT * FROM SN_matches AS m JOIN SN_objects AS o ON m.match_object_id = o.object_id";
		$query_obj .= " WHERE m.match_unique_id = " . $row_uni["unique_id"];
		$res_obj = $_mysqli->query($query_obj);
		while($row_obj = $res_obj->fetch_assoc()){
			array_push($record["names"], $row_obj["object_name"]);
			
			$misc = array();
			$misc['type'] = $row_obj["object_type"];
			$misc['redshift'] = $row_obj["object_redshift"];
			$misc['disc_mag'] = $row_obj["object_disc_mag"];
			$misc['phase'] = $row_obj["object_phase"];
			array_push($record["miscs"], $misc);
			
			//check to see if the object msg is null - this is only for objects that come from the SN_known_list
			if(is_null($row_obj["object_msg_hashed"])){
				//join the SN_known_list with the relationship table connecting it to SN_uniques
				$query_msg = "SELECT * FROM `SN_known_list_match` AS k JOIN `SN_known_list` AS l ON k.kl_match_sn_id = l.sn_id";
				$query_msg .= " WHERE k.kl_match_unique_id = " . $row_obj["match_unique_id"];
				$res_msg = $_mysqli->query($query_msg);
				$new_row_msg = $res_msg->fetch_assoc();
				$res_msg->free();

				$msg = array();
				$msg["title"] = $new_row_msg["sn_name"];
				$msg["link"] = "N/A";
				$msg["description"] = "N/A";
				$msg["update_time"] = $new_row_msg["sn_date"];	
				$msg["type"] = "object";

				$msg["feed"] = array();
				// start third join on `SN_feeds`
				$query_feed = "SELECT * FROM `SN_feeds` WHERE feed_id = 7";
				$res_feed = $_mysqli->query($query_feed);
				$row_feed = $res_feed->fetch_assoc();
				$res_feed->free();
				$msg["feed"]["name"] = $row_feed["feed_name"];
				$msg["feed"]["url"] = $row_feed["feed_url"];
				$msg["feed"]["description"] = $row_feed["feed_description"];

				array_push($record["messages"], $msg);
			} else {
				// start second join on `SN_messages`
				$query_msg = "SELECT * FROM `SN_messages` WHERE msg_hashed = '" . $row_obj["object_msg_hashed"];
				$query_msg .= "' AND msg_end_ts IS NULL LIMIT 1";
				$res_msg = $_mysqli->query($query_msg);
				$row_msg = $res_msg->fetch_assoc();
				$res_msg->free();
				$msg = array();
				$msg["title"] = $row_msg["msg_title"];
				$msg["link"] = $row_msg["msg_link"];
				$msg["description"] = $row_msg["msg_description"];
				$msg["update_time"] = $row_msg["msg_update_ts"];
				$msg["type"] = $row_msg["msg_type"];
			
				$msg["feed"] = array();
				// start third join on `SN_feeds`
				$query_feed = "SELECT * FROM `SN_feeds` WHERE feed_id = " . $row_msg["msg_feed_id"];
				$res_feed = $_mysqli->query($query_feed);
				$row_feed = $res_feed->fetch_assoc();
				$res_feed->free();
				$msg["feed"]["name"] = $row_feed["feed_name"];
				$msg["feed"]["url"] = $row_feed["feed_url"];
				$msg["feed"]["description"] = $row_feed["feed_description"];
			
				array_push($record["messages"], $msg);
			}
		}
		$res_obj->free();
		
		$record["names"] = array_unique($record["names"], SORT_STRING);
		$record["DT_RowId"] = "row_" . $row_uni["unique_id"];

		array_push($res["aaData"], $record);
	}
	$res_uni->free();

	$res = array('iTotalDisplayRecords' => $res["iTotalDisplayRecords"]) + $res;
	$res = array('iTotalRecords' => $res["iTotalRecords"]) + $res;
	$res = array('sEcho' => $res["sEcho"]) + $res;

	if(array_key_exists('contain', $_para_array))	return $res;
	else	return json_encode($res);
	
}
?>