<?php
/*
Di Bao
Summer 2012
This php script is used to search LSST remote server
*/

require("../DBFunctions.php");
connectToDB("lsst");

// Don't want to allow creation/deletion of tables through this
if(stristr($_POST['query'], 'drop table') || stristr($_POST['query'], 'create table') || stristr($_POST['query'], 'delete') || stristr($_POST['query'], 'update') || stristr($_POST['query'], 'insert')) {
	$json_msg = '{"error": "Bad SQL query..."}';
	echo $json_msg;
	exit;
} else {
	$query = $_POST['query'];
	$query = mysql_real_escape_string($query);
	$query = str_replace('\n', ' ', $query);
	$result = mysql_query($query);
	
	// No results to parse so just return
	if($result === TRUE){
		$json_msg = '{"error": "Bad SQL query..."}';
		echo $json_msg;
		exit;
	}else if($result === FALSE){
		$json_msg = '{"error": "' . mysql_error() . '"}';
		echo $json_msg;
		exit;
	}else if(mysql_num_rows($result) == 0){
		$json_msg = '{"error": "No row returned..."}';
		echo $json_msg;
		exit;
	}else if(mysql_num_rows($result) > 100000){
		$json_msg = '{"error": "Timeout...<br/><br/><font size=\"2\">Due to large result set, please limit the query range to get quick response.</font>"}';
		echo $json_msg;
		exit;		
	}
	// Return a string formatted as xml
	else{
		//for object details
		if(isset($_POST['more'])){
			$json_msg = '{"bPaginate": false, "bLengthChange": false, "iDisplayLength": 25, "bFilter": false, "aaSorting" : [], 
			"aoColumns":[{"sTitle": "Attributes", "bSortable": false}, {"sTitle": "Values", "bSortable": false}], "aaData":[["name", "' . $_POST['name'] . '"], ';
			$row = mysql_fetch_assoc($result);
			foreach($row as $col => $value){
				if($value === null){
					$json_msg .= '["'. $col . '","null"],';
				}else{
					$json_msg .= '["'. $col . '","' . $value . '"],';
				}
			}
			$json_msg = substr($json_msg, 0, -1);
			$json_msg .= ']}';
			
			echo $json_msg;
			exit;
		}
		
		//for overlay
		/*
		if(isset($_POST['overlay'])){
			$xml = "<result>";
			$num_rows = mysql_num_rows($result);
			$num_cols = mysql_num_fields($result);
			$xml .= "<row>";
			for ($i = 0; $i < $num_cols; $i++) {
			$xml .= "<fieldHeader>" . mysql_field_name($result, $i) . "</fieldHeader>";
			}
			$xml .= "</row>";
			for ($i = 1; $i <= $num_rows; $i++) {
			$xml .= "<row>";
			// Get the next row as an array indexed column name
			$row = mysql_fetch_assoc($result);
			// Add this row of results
			foreach ($row as $col => $value) {
			$xml .= "<field col='$col'> $value </field>";
			}
			$xml .= "</row>";
			}
			$xml .= "</result>";
			echo $xml;exit;
		}
		*/
		
		/*--------convert to JSON--------*/
		$table = $_POST['table'];
		if($table == "SimRefObject"){
			$name = "LSST_t1";
		}else{
			$name = "LSST_t2";
		}
		
		$json_msg = '{"aoColumns":[';
		$num_rows = mysql_num_rows($result);
		$num_cols = mysql_num_fields($result);
		
		//for object details
		$json_msg .= '{"sTitle": "&nbsp;&nbsp;", "bSortable": false}, {"sTitle": "&nbsp;&nbsp;", "bSortable": false},';
		
		for($i = 0; $i < $num_cols; $i++){
			$json_msg .= '{"sTitle": "' . mysql_field_name($result, $i) . '", "sType": "html"},';
		}
		//for object details
		$json_msg .= '{"sTitle": "Object details", "bSortable": false}';
		//$json_msg = substr($json_msg, 0, -1);
		$json_msg .= '], "aaData":[';
		
		for($i = 1; $i <= $num_rows; $i++){
			$row = mysql_fetch_assoc($result);
			$json_msg .= '[';
			
			//for object details
			$json_msg .= '"<a href=\'#\' class=\"more\" name=\"' . $name . '\"><span class=\"ui-icon ui-icon-info\"/></a>", ';
			$json_msg .= '"<a href=\'#\' class=\"delete\" name=\"LSST\"><span class=\"ui-icon ui-icon-circle-close\"/></a>", ';
			
			foreach($row as $col => $value){
				if($col == 'ra' or $col == 'decl' or $col == 'ra_PS' or $col == 'decl_PS'){
					$json_msg .= '"<a href=\'#\' class=\'jump\' name=\''. $col .'\'>'. $value . '</a>",';
				}else{
					if($value === null){
						$json_msg .= '"null", ';
					}else{
						$json_msg .= '"'. $value . '",';
					}
				}
			}
			//for object details
			$json_msg .= '"<a href=\'#\' class=\'more\' name=\"' . $name . '\">more</a>"';
			//$json_msg = substr($json_msg, 0, -1);
			$json_msg .= '],';
		}

		$json_msg = substr($json_msg, 0, -1);
		$json_msg .= ']}';
		/*-------end--------*/
		echo $json_msg;
		exit;
	}
}
?>