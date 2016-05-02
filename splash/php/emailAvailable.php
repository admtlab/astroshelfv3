<?php
$email = $_POST["email"];
if(!isset($email)) {
	echo "0";
} else {
	require_once("db/functions.php");
	$mysqli = connectToDB("astroDB");
	
	$query = $mysqli->prepare("SELECT COUNT(*) FROM user WHERE email = ?");
	$query->bind_param("s", $email);
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