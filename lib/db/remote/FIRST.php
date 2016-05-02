<?php
        $image = $_GET['image'];
		header("Content-type: image/jpeg");
		$data = file_get_contents("http://astro.cs.pitt.edu/FIRST/tim/images/$image");
		echo $data;
?>
