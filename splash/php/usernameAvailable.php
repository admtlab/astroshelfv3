<?php
$username = $_POST["username"];
if(!isset($username) || strlen($username) < 4 || strlen($username) > 20) {
	echo "0";
} else {
	require_once("db/functions.php");
	$mysqli = connectToDB("astroDB");
	
	$query = $mysqli->prepare("SELECT COUNT(*) FROM user WHERE username = ?");
	$query->bind_param("s", $username);
	$query->execute();
	$query->store_result();
	$query->bind_result($count);
	
	$query->fetch();
	if($count == 0) {
		echo "1";
	} else {
		echo "0";
	}
}
?>