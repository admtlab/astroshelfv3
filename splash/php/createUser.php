<?php
$from = "Alexandros Labrinidis <labrinid@cs.pitt.edu>";
$replyto = "Alexandros Labrinidis <labrinid@cs.pitt.edu>";

$emailRegex = "/^[a-z0-9!#$%&'*+\/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+\/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?$/";
$eduRegex = "/.+\.edu$/";

if(isset($_POST["firstName"]) &&
	isset($_POST["lastName"]) &&
	isset($_POST["email"]) && preg_match($emailRegex, $_POST["email"]) == 1 && preg_match($eduRegex, $_POST["email"]) == 1 &&
	isset($_POST["password"]) && strlen($_POST["password"]) >= 8 && strlen($_POST["password"]) <= 32 &&
	isset($_POST["username"]) && strlen($_POST["username"]) >= 4 && strlen($_POST["username"]) <= 20) {
	
	require_once("db/functions.php");
	$mysqli = connectToDB("astroDB");
	$mysqli->autocommit(FALSE);
	
	$available_query = $mysqli->prepare("SELECT COUNT(*) FROM user WHERE email = ? OR username = ?");
	$available_query->bind_param("ss", $_POST["email"], $_POST["username"]);
	if(!$available_query->execute()) {
		echo "0";
		exit(1);
	}
	$available_query->store_result();
	$available_query->bind_result($count);
	$available_query->fetch();
	if($count > 0) {
		echo "0";
		exit(1);
	}
	
	$query = $mysqli->prepare("INSERT INTO user_email_confirmation(activation_key, fname, lname, username, email, password, url, affiliation) VALUES(PASSWORD(NOW() + ?), ?, ?, ?, ?, PASSWORD(?), ?, ?)");
	$query->bind_param("ssssssss", $_POST["username"], $_POST["firstName"], $_POST["lastName"], $_POST["username"], $_POST["email"], $_POST["password"], $_POST["url"], $_POST["affiliation"]);
	
	if(!$query->execute()) {
		echo "0";
		exit(1);
	}
	
	$id = $mysqli->insert_id;
	$key_query = $mysqli->prepare("SELECT activation_key FROM user_email_confirmation WHERE confirmation_id = ?");
	$key_query->bind_param("i", $id);
	if(!$key_query->execute()) {
		$mysqli->rollback();
		echo "0";
		exit(1);
	}
	$key_query->store_result();
	$key_query->bind_result($activation_key);
	if($key_query->num_rows() == 0) {
		$mysqli->rollback();
		echo "0";
		exit(1);
	}
	$key_query->fetch();
	
	$name = $_POST["firstName"];
	$url = "https://astro.cs.pitt.edu/eric2015/splash/php/confirmAccount.php?confid=".$id."&key=".$activation_key;
	
	$to = $_POST["email"];
	$subject = "E-mail confirmation from AstroShelf";
	$message = "Hi $name,\n\n".
	"Welcome to AstroShelf! Please click on the following link to confirm your account creation:\n\n".$url.
	"\n\nThe link above will expire in 24 hours. If you have trouble confirming your account, please visit https://astro.cs.pitt.edu/eric2015/ to find contact information for the current developer.";
	
	$headers = "From: $from" . "\r\n" .
		"Cc: $from" . "\r\n" .
		"Reply-To: $replyto" . "\r\n" .
		'X-Mailer: PHP/' . phpversion();
	
	if(!mail($to, $subject, $message, $headers)) {
		$mysqli->rollback();
		echo "0";
		exit(1);
	}
	
	$mysqli->commit();
	echo "1";
}
	

?>