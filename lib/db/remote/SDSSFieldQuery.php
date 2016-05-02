<?php

require_once(dirname(__FILE__) . '/general_sdss_query.php');
//error_reporting(-1);	


#require_once('../../klogger/KLogger.php');
// PhpConsole::start(true, true, dirname(__FILE__));

#$log = KLogger::instance(dirname(__FILE__), KLogger::INFO);

/* parse the query and wget images */
function getImages($input){
	$flag = false;
	$ret_val = array();
		
	// Construct a file with a list of the jpeg and fits urls, one on each line
	foreach($input->Row as $imageFields){
		
		$jpegurl = $imageFields->run . "/" . $imageFields->rerun . "/Zoom/" . $imageFields->camcol . "/fpC-" . str_pad($imageFields->run,6,"0",STR_PAD_LEFT) . "-" .  $imageFields->camcol . "-" . $imageFields->rerun . "-" . str_pad($imageFields->field,4,"0",STR_PAD_LEFT) . "-z10.jpeg";
		
		$jpegname = "fpC-" . str_pad($imageFields->run,6,"0",STR_PAD_LEFT) . "-" . $imageFields->camcol . "-" . $imageFields->rerun . "-" . str_pad($imageFields->field,4,"0",STR_PAD_LEFT) . "-z00.jpeg";

		$fitsname = "fpC-" . str_pad($imageFields->run,6,"0",STR_PAD_LEFT) . "-r" . $imageFields->camcol . "-" . str_pad($imageFields->field,4,"0",STR_PAD_LEFT) . ".txt";

		array_push($ret_val, $jpegurl);
		array_push($ret_val, $fitsname);
	}
	echo json_encode($ret_val);
}

$the_query = "SELECT distinct n.fieldid, n.distance, f.ra, f.dec, f.run, f.rerun, f.camcol, f.field FROM";
$the_query .= " dbo.fGetNearbyFrameEq(" . $_GET["ra"] . "," . $_GET["dec"] . "," . $_GET["radius"] . "," . $_GET["zoom"] . ") as n";
$the_query .= " JOIN Frame as f on n.fieldid = f.fieldid ORDER by n.distance";

$start = microtime(true);

$error_code = general_sdss_query($the_query, $output_str, $output_obj, $error_msg);

$time_taken = microtime(true) - $start;
#$log->logInfo("query time: ", $time_taken);

if($error_code == 0){
	//echo $output_str . "\n";
	//print_r($output_obj);exit;
	$start = microtime(true);
	getImages($output_obj);
	$time_taken = microtime(true) - $start;
	#$log->logInfo("time to get image names: ", $time_taken);
}else{
	echo "#(" . $error_code . ") : " . $error_msg . "\n";
	exit;
}
?>
