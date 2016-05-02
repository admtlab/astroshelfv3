<?php
        $end_url = $_GET['url'];
		header("Content-type: image/jpeg");
		$data = file_get_contents($end_url);
		echo $data;
?>