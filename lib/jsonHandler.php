<?php

require_once("db/DBFunctions.php");
header("Content-Type: application/json");
$mysqli = connectToDB("astroDB");

$url = $_POST['url'];
$jsonData = $_POST['data'];
$jsonData = stripslashes($jsonData);

$data = json_decode($jsonData, true);
if($data["targetType"] == "area/point") {
	$mysqli->autocommit(false);
	
	$stmt = $mysqli->prepare("INSERT INTO annotation(anno_type_id, anno_title, anno_value, user_id, target_type, ts_created) VALUES(?, ?, ?, ?, ?, NOW())");
	$stmt->bind_param("issis", $data["annoTypeId"], $data["annoTitle"], $data["annoValue"], $data["userId"]["userId"], $data["targetType"]);
	$stmt->execute();
	
	$anno_id = $stmt->insert_id;
	$target = $data["targetObj"];
	$type = "type2-2000";
	$stmt = $mysqli->prepare("INSERT INTO anno_to_area_point(anno_src_id, RA_bl, Dec_bl, type_bl, RA_tr, Dec_tr, type_tr) VALUES(?, ?, ?, ?, ?, ?, ?)");
	$stmt->bind_param("iddsdds", $anno_id, $target["RA_bl"], $target["Dec_bl"], $type, $target["RA_tr"], $target["Dec_tr"], $type);
	$stmt->execute();
	$mysqli->commit();
	
	$mysqli->autocommit(true);
} else {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json; charset=utf-8","Accept:application/json, text/javascript, */*; q=0.01"));
	curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
	$myResponse = curl_exec($ch);
	curl_close($ch);
}
?>
