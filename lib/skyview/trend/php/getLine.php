<?php
        $end_url = $_GET['url'];
		header("Content-type: image/png");
		$data = file_get_contents("http://timothy.forgot.his.name/TREND/" . $end_url);
		echo $data;
?>
