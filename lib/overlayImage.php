<?php

	// Set header to HTML if you want to be able to print out stats and debug info
	//header("Content-type: text/xml");
	
	// Set header to png if you just want the returned image to show up
  header("Content-type: image/png");
  // Don't cache these so they will update in case new annotations
  header("Expires: 1 Jan 1990 00:00:00 GMT"); 
  header("Pragma: no-cache"); 
  header("Cache-control: no-cache, no-store, must-revalidate"); 
  header("Cache-control: pre-check=0,post-check=0", false);

	// Timer for stats
	//$time_start = microtime(true);
  //$postdata = file_get_contents("php://input");
  //preg_match('/\<result\>.+/', $postdata, $matches);
	// Submit query to some database and store results in xml format
	//$xml = simplexml_load_string($matches[0]);
	
	require("../db/connectDB.php");
  require("../db/dbQueryLib.php");
  connectDB();
 
  /*
  $_GET['ra'] = $_GET['dec'] = 1;
  $_GET['RAMin'] = $_GET['DecMin'] = -1;
  $_GET['RAMax'] = $_GET['DecMax'] = 3;
  $_GET['width'] = 500;
  $_GET['height'] = 500;
  $_GET['markerSize'] = 5;
  */
  
	$ra = $_GET['ra'];
	$RAMin = min($_GET['RAMin'], $_GET['RAMax']);
	$RAMax = max($_GET['RAMin'], $_GET['RAMax']);
	$dec = $_GET['dec'];
	$DecMin = min($_GET['DecMin'], $_GET['DecMax']);	
	$DecMax = max($_GET['DecMin'], $_GET['DecMax']);
      //echo ("parameters for search: ".$ra." ".$RAMin." ".$RAMax." ".$dec." ".$DecMin." ".$DecMax);
	$result = getAnnotRADecByRange($ra,$RAMin,$RAMax,$dec,$DecMin,$DecMax);
	$xml = simplexml_load_string(createXMLObj($result));

	$totalLat = abs($_GET['DecMax'] - $_GET['DecMin']);	// Used to position each object relative to the bounds of the image
	$totalLon = abs($_GET['RAMin'] - $_GET['RAMax']);
	$_GET['keyMax'] = 1;
	$_GET['keyMin'] = 0;
	$range = $_GET['keyMax'] - $_GET['keyMin'];		// Needed to determine the opacity of the object marker in the image

	$width = $totalLon + 1; 	// Total width in pixels of the output image
	$height = $totalLat + 1;	// Total height in pixels of the output image
	$width = $_GET['width'];
	$height = $_GET['height'];
	$im = imagecreatetruecolor($width, $height);
	//imagealphablending($im, true);
	//imagesavealpha($im, true);
	//imagefill($im, 0, 0, imagecolorallocate($im, 0, 0, 0));
  
  $anno = FALSE;
  $_GET['keyVal'] = 'anno';
	if ($_GET['keyVal'] == 'anno') {
	  $anno = TRUE;
	}
	//echo "height: $height, width; $width <br />";
	$howmany = 0;	// Used for stats'
	$white = imagecolorallocate($im, 255, 255, 255);
	$blue = imagecolorallocate($im, 0, 191, 255);
	$scale = $_GET['markerSize'] * 0.01;
	foreach($xml->row as $row)
	{
		// Determine the x,y coordinates of the pixel to be drawn in the range of [0.0, 1.0]
		$rowRA = 'hello';
	  $rowDec = 'hello';
	  $rowVal = 1;
		// Determine the x,y coordinates of the pixel to be drawn in the range of [0.0, 1.0]
		foreach ($row->field as $field) {
		  $attrib = $field->attributes();
		  $col = $attrib["col"];
		  //$val = $field[0] - $south;
		  if (strcasecmp($col, "Declination") == 0) {
		    //$r = floatval($field[0]);
		    $rowDec = $field[0];
		  }
		  if (strcasecmp($col, "RA") == 0) {
		    $rowRA = $field[0];
		  }
		  if (strcasecmp($col, $_GET['keyVal']) == 0) {
		    $rowVal = $field[0];
		  }
		}
		$rowX = ceil(($rowRA - $RAMin) * ($width / $totalLon));
		$rowY = ($height - 1) - ceil(($rowDec - $DecMin) * ($height / $totalLat));
		if ($rowRA != 'hello' && $rowDec != 'hello') {
			//echo "$rowRA, $rowDec, $rowX, $rowY <br />";
		
  		$minX = ceil(($rowRA - $scale - $RAMin) * ($width / $totalLon));
  		$maxY = ($height - 1) - ceil(($rowDec - $scale - $DecMin) * ($height / $totalLat));
  		$minY = ($height - 1) - ceil(($rowDec + $scale - $DecMin) * ($height / $totalLat));
  		$maxX = ceil(($rowRA + $scale - $RAMin) * ($width / $totalLon));
  		
  		// cx, cy, width, height
  		imageellipse($im , $rowX , $rowY , $maxX - $minX , $maxY - $minY , $blue );
  		// (x1, y1) (x2, y2)
  		imageline( $im, $minX , $rowY, $maxX , $rowY, $blue);
  		imageline($im, $rowX, $minY, $rowX, $maxY, $blue);
  		imagesetpixel($im, $rowX, $rowY, $white);
	  }
	}
	
	//Send highly compressed PNG image to the client
	//imagepng($im, NULL, 9, PNG_ALL_FILTERS);
	//$im2 = imagecreatetruecolor($_GET['width'], $_GET['height']);
	//imagefill($im2, 0, 0, imagecolorallocate($im2, 255, 255, 255));
  //imagecopyresampled($im2, $im, 0, 0, 0, 0, $_GET['width'], $_GET['height'], $width, $height);
  imagepng($im, NULL, 9, PNG_ALL_FILTERS);
	// Clean up
	imagedestroy($im);
	//imagedestroy($im2);
	$time_end = microtime(true);
	$time = $time_end - $time_start;

	// Print out stats
	// echo ("<br />created image in " . $time . " seconds with " . $howmany . " points.");	

?>
