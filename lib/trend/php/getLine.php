<?php
        $end_url = $_GET['url'];
		header("Content-type: image/png");
		$data = file_get_contents("http://astro.cs.pitt.edu/trend/" . $end_url);
		echo $data;
?>
