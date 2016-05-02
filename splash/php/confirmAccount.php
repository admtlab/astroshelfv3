<?php
$confirmation_id = $_GET["confid"];
$key = $_GET["key"];
if(!isset($confirmation_id) || !is_numeric($confirmation_id) || !isset($key)) {
	echo "The information provided is invalid. We are unable to confirm your account creation.";
} else {
	require_once("db/functions.php");
	$mysqli = connectToDB("astroDB");
	$mysqli->autocommit(FALSE);
	
	$valid_query = $mysqli->prepare("SELECT fname, lname, username, email, password, url, affiliation FROM user_email_confirmation WHERE confirmation_id = ? AND activation_key = ? AND TIMESTAMPDIFF(HOUR, time_created, NOW()) < 24");
	$valid_query->bind_param("is", $confirmation_id, $key);
	if(!$valid_query->execute()) {
		echo "There was a problem confirming your account. Please contact the current developer.";
		exit(1);
	}
	$valid_query->store_result();
	$valid_query->bind_result($fname, $lname, $username, $email, $password, $url, $affiliation);
	if($valid_query->num_rows() < 1) {
		echo "The provided information does not match anything in our records. Your confirmation link may have expired; please create your account again.";
		exit(1);
	}
	$valid_query->fetch();
	
	$insert_query = $mysqli->prepare("INSERT INTO user(fname, lname, username, email, password, url, affiliation) VALUES(?, ?, ?, ?, ?, ?, ?)");
	$insert_query->bind_param("sssssss", $fname, $lname, $username, $email, $password, $url, $affiliation);
	if(!$insert_query->execute()) {
		$mysqli->rollback();
		echo "There was a problem creating your account after confirmation. Please contact the current developer.";
		exit(1);
	}
	
	$delete_query = $mysqli->prepare("DELETE FROM user_email_confirmation WHERE confirmation_id = ?");
	$delete_query->bind_param("i", $confirmation_id);
	if(!$delete_query->execute() || $mysqli->affected_rows == 0) {
		$mysqli->rollback();
		echo "There was a problem clearing your confirmation details. Please contact the current developer.";
		exit(1);
	}
	
	$mysqli->commit();
	echo "Congratulations! Your account has been confirmed. Please visit https://astro.cs.pitt.edu/ and click Enter Site to log in.";
}
?>