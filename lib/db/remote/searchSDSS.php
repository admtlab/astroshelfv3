<?php
/*
Di Bao
Summer 2012

Eric Gratta
2015-16

This php script is used to search SDSS remote server and cache SDSS objects
*/

require_once(dirname(__FILE__) . '/general_sdss_query.php');

$query = html_entity_decode($_POST['query']);
$error_code = general_sdss_query($query, $output_str, $output_obj, $error_msg, 60);

switch($error_code){
	case 0:
		break;
	case -1:
		$my_timeout = rand(1, 3);
		if($my_timeout == 1)	$time_string = "1m 0s";
		elseif($my_timeout == 2)	$time_string = "1m 1s";
		else	$time_string = "1m 2s";
		$json_msg = '{"error": "Timeout after ' . $time_string . '...<br/><br/><font size=\"2\">Due to large result set, please limit the query range/simplify the query to get quick response.</font>"}';
		echo $json_msg;
		exit;break;
	case -2:
	case -3:
		$json_msg = '{"error": "'. $error_msg .'"}';
		echo $json_msg;
		exit;break;
	case -4:
		$json_msg = '{"error": "No row returned..."}';
		echo $json_msg;
		exit;break;
	default:
		break;
}
//for object details
if(isset($_POST['more'])){
	$json_msg = '{"bPaginate": false, "bLengthChange": false, "iDisplayLength": 25, "bFilter": false, "aaSorting" : [], 
	"aoColumns":[{"sTitle": "Attributes", "bSortable": false}, {"sTitle": "Values", "bSortable": false}], "aaData":[';
	foreach($output_obj->Row->attributes() as $field => $value){
		$json_msg .= '["'. $field . '","' . $value . '"],';
	}
	$json_msg = substr($json_msg, 0, -1);
	$json_msg .= ']}';
	
	// Cache the object in the local database before returning results
	require_once("../DBFunctions.php");
	$mysqli = connectToDB("astroDB");

	$row = $output_obj->Row;
	$objid = (string)$row['objid'];
	$name = (string)$row['name'];
	$type = (string)$row['type'];
	if($type == 'GALAXY') {
		$type = '3';
	} else if($type == 'STAR') {
		$type = '6';
	} else {
		$type = '0';
	}
	$Z = $row['redshift'];
	$specClass = $row['specclass'];
	if($specClass == 'STAR') {
		$specClass = 1;
	} else if($specClass == 'GALAXY') {
		$specClass = 2;
	} else if($specClass == 'QSO') {
		$specClass = 3;
	} else if($specClass == 'HIZ_QSO') {
		$specClass = 4;
	} else {
		$specClass = 0;
	}
	$insert = $mysqli->prepare("INSERT INTO object_info(survey_id, survey_obj_id, name, _RA_, _DEC_, Z, obj_type, specClass) VALUES(2, ?, ?, ?, ?, ?, ?, ?)");
	$insert->bind_param("ssdddsi", $objid, $name, $row['ra'], $row['dec'], $Z, $type, $specClass);
	$insert->execute();
	
	echo $json_msg;
	exit;
}

//for overlay
/*
if(isset($_POST['overlay'])){
	$xml = "<result> ";
	// Add in column name information 
	$xml .= " <row> ";
	foreach ($result->Row[0] as $field => $value) {
		$xml .= " <fieldHeader> $field </fieldHeader> ";
	}
	$xml .= " </row> ";
	// Change attributes to tags in xml so matches formatting expected to display properly
	foreach($result->Row as $row) {
		$xml .= " <row> ";
		foreach($row as $field => $value) {
			$xml .= " <field col='$field'> $value </field> ";
		}
		$xml .= " </row> ";
	}
	$xml .= " </result>";
	echo $xml;exit;
}
*/

/*-------convert to JSON style--------*/
$json_msg = '{"aoColumns":[';

//for object details
$json_msg .= '{"sTitle": "&nbsp;&nbsp;", "bSortable": false}, {"sTitle": "&nbsp;&nbsp;", "bSortable": false},';

foreach($output_obj->Row->attributes() as $field => $value){
	$json_msg .= '{"sTitle": "' . $field . '" , "sType": "html"},';
}

//for object details
$json_msg .= '{"sTitle": "Object details", "bSortable": false}';
//$json_msg = substr($json_msg, 0, -1);

$json_msg .= '], "aaData":[';
foreach($output_obj->Row as $row){

	$json_msg .= '[';
	
	//for object details
	$json_msg .= '"<a href=\'#\' class=\"more\" name=\"SDSS\"><span class=\"ui-icon ui-icon-info\"/></a>", ';
	$json_msg .= '"<a href=\'#\' class=\"delete\" name=\"SDSS\"><span class=\"ui-icon ui-icon-circle-close\"/></a>", ';
	foreach($row->attributes()	as $field => $value) {
		if($field == "ra" or $field == "dec"){
			$json_msg .= '"<a href=\'#\' class=\'jump\' name=\''. $field .'\'>'. $value . '</a>",';
		}else{
			$json_msg .= '"'. $value . '",';
		}
	}
	//for object details
	$json_msg .= '"<a href=\'#\' class=\'more\' name=\'SDSS\'>more</a>"';
	//$json_msg = substr($json_msg, 0, -1);
	$json_msg .= '],';
}

$json_msg = substr($json_msg, 0, -1);
$json_msg .= ']}';
/*-------end--------*/

echo $json_msg;
?>

