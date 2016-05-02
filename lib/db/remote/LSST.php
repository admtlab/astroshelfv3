<?php
	$image = $_GET['image'];
	header("Content-type: image/jpeg");
	$data = file_get_contents("/var/www/html/LSST/images/$image");
	echo $data;
?>
