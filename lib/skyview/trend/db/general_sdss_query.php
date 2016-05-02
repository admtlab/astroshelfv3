<?php
/*
Di Bao
Fall 2012
This is a wrapper should be included by any script that need to interact with SDSS server.
Basically, it will send an SQL query string to SDSS and getting back an answer in XML
*/

// FUNC: sdss_fix
// DESC: removes extra XML tags from result set
// AUTH: Alexandros Labrinidis (labrinid@cs.pitt.edu)
// DATE: Tue Oct 30 22:29:16 EDT 2012
function sdss_fix ($input) {
	// convert input into array of lines
	// http://php.net/manual/en/function.preg-split.php
	$lines = preg_split('/\n/', $input);

	// debugging
	// http://us2.php.net/manual/en/function.print-r.php
	//print_r($lines);

	$found_row = 0;
	$output = "";
	//$count = 0;

	// traverse over array of lines
	// http://us2.php.net/manual/en/control-structures.foreach.php
	foreach ($lines as $oneline) {
		// http://us2.php.net/manual/en/function.preg-match.php
		if (preg_match("/<Row>/i", $oneline)) {
			// regular row
			$found_row = 1;
			$output .= $oneline. "\n";
			//$count++;

		} elseif (!$found_row) {
			// header info
			$output .= $oneline. "\n";

		} else {
			// extra tags, ignore
		}
	}
	$output .= "</Answer></root>";

	//print "==== $count ====\n";
	return $output;
}

/*
	RETURN VALUE: 
	errno = 0, successful
	errno = -1, timeout
	errno = -2, diagnostic
	errno = -3, error message
	errno = -4, no rows returned
*/
function general_sdss_query_tim($sql_string, &$xml_output_string, &$xml_output_object, &$error_message, &$log, $timeout = 0	){
	
	$errno_array = array(
		0 => "success",
		-1 => "timeout",
		-2 => "diagnostic",
		-3 => "error_message",
		-4 => "no_rows_returned"
	);
	
	$errno = NULL;
	
	$sql_string = urlencode($sql_string);
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "http://cas.sdss.org/astrodr7/en/tools/search/x_sql.asp?format=xml&cmd=" . $sql_string);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	if($timeout)	curl_setopt($ch, CURLOPT_TIMEOUT, 60);
	$output = curl_exec($ch);
	curl_close($ch);
	
	$log->logInfo("\nsql_string: ", $sql_string);
	$log->logInfo("\ncurl output: ", $output);
	
	
	
	if(empty($output)){
		$errno = -1;
		
		$xml_output_string = NULL;
		$xml_output_object = NULL;
		$error_message = $errno_array[$errno];
		
		return $errno;
	}

	if(strpos($output, 'Diagnostic') !== false){
		$errno = -2;
		
		$pos1 = strpos($output, '<Diagnostic>');
		$pos2 = strpos($output, '</Diagnostic>');
		$msg = substr($output, ($pos1 + 13), ($pos2 - $pos1 - 13));
		
		$xml_output_string = NULL;
		$xml_output_object = NULL;
		$error_message = $errno_array[$errno] . " - " . $msg;
		
		return $errno;
	}
	
	$output = sdss_fix($output);
	$pos1 = strpos($output, '<Answer>');
	$pos2 = strpos($output, '</Answer>');
	$result = substr($output, ($pos1 + 8), ($pos2 - $pos1 - 8));
	$result_str = '<result>' . $result . '</result>';
	$result_obj = simplexml_load_string($result_str);
	
	/*
	echo $result_str . "\n";
	print_r($result_obj);
	echo "\n";
	*/	
	
	if(strpos($result_str, 'error_message') !== false){
		$errno = -3;
		
		$xml_output_string = NULL;
		$xml_output_object = NULL;
		$error_message = $errno_array[$errno] . " - " . $result_obj->Row->error_message; 
		
		return $errno;
	}
	
	if(strpos($result_str, 'No rows returned') !== false){
		$errno = -4;
		
		$xml_output_string = NULL;
		$xml_output_object = NULL;
		$error_message = $errno_array[$errno];
		
		return $errno;
	}
	
	$errno = 0;
	
	$xml_output_string = $result_str;
	$xml_output_object = $result_obj;

	$error_message = $errno_array[$errno];
	
	return $errno;
}
?>