<?php
  /* Inputs:
      DecMax, DexMin = range of Dec
      RAMax, RAMin = range of RA
      width = width (in pixels) of output image
      keyVal = which sdss attribute from SpecObj table
      keyMax, keyMin = range of key
  */

  /*
  // Test values
  $_GET['RAMax'] = 139.621;
  $_GET['RAMin'] = 136.621;
  $_GET['DecMax'] = 5.81;
  $_GET['DecMin'] = 4.71;
  $_GET['width'] = 1200;
  $_GET['height'] = 800;
  $_GET['keyVal'] = 'xFocal';
  $_GET['keyMax'] = 400;
  $_GET['keyMin'] = 0;
  $_GET['red'] = 0;
  $_GET['blue'] = 255;
  $_GET['green'] = 0;
  $_GET['diam'] = 5;
  */
  
	// Set header to HTML if you want to be able to print out stats and debug info
	//header("Content-type: text/html");
	//error_reporting(-1); // report all errors
	$scale = 10000.0;
	$RAMin = (float)$_GET['RAMin']*$scale;
	$RAMax = (float)$_GET['RAMax']*$scale;
	$DecMin = (float)$_GET['DecMin']*$scale;
	$DecMax = (float)$_GET['DecMax']*$scale;
	// Set header to png if you just want the returned image to show up
	header("Content-type: image/png");

	// Timer for stats
	//$time_start = microtime(true);

	// Submit query to some database and store results in xml format
  $xml = simplexml_load_file("http://cas.sdss.org/dr7/en/tools/search/x_sql.asp?format=xml&cmd=" . urlencode("select ra, dec, " . $_GET['keyVal'] . " as keyVal from SpecObj where dec BETWEEN " . $_GET['DecMin'] . " and " . $_GET['DecMax'] . " AND ra BETWEEN " . $_GET['RAMin'] . " and " . $_GET['RAMax']));
  //header("Content-type: text/xml"); echo $xml->asXML(); // uncomment to view xml results

	$totalLat = abs($DecMax-$DecMin);	// Used to position each object relative to the bounds of the image
	$totalLon = abs($RAMax-$RAMin);
	
	$range = $_GET['keyMax'] - $_GET['keyMin'];		// Needed to determine the opacity of the object marker in the image

	$width = $_GET['width']; 	// Total width in pixels of the output image
	$height = $_GET['height'];	// Total height in pixels of the output image

	//echo("Width: " . $width . " Height: " . $height . "<br />");	//Andrew: This line was causing the image/png output to be corrupted (since the header specifies that the browser should expect just an image, not extra text)

	$im = imagecreatetruecolor($width, $height);
	imagealphablending($im, true);
	imagesavealpha($im, true);
	imagefill($im, 0, 0, imagecolorallocatealpha($im, 0, 0, 0, 127));
  
	
	$howmany = 0;	// Used for stats
	foreach($xml->Answer->Row as $row)
	{
		$rowRA = (float)$row['ra']*$scale;
		$rowDec = (float)$row['dec']*$scale;
    $rowX = ceil(($rowRA - $RAMin) * ($width / $totalLon));
		$rowY = ($height - 1) - ceil(($rowDec - $DecMin) * ($height / $totalLat));
	  //if ($rowX < 0 || $rowX > $width || $rowY < 0 || $rowY > $height) echo "$rowRA, $rowDec --- $rowX, $rowY <br />";
		// Determine the color with which to draw based on the keyValue
		if((float)$row['keyVal'] < (float)$_GET['keyMin'])
		{
			$color = imagecolorallocatealpha($im, $_GET['red'], $_GET['green'], $_GET['blue'], 127);
		}
		else if((float)$row['keyVal'] > (float)$_GET['keyMax'])
		{
			$color = imagecolorallocatealpha($im, $_GET['red'], $_GET['green'], $_GET['blue'], 0);
		}
		else
		{
		  /*
			//Encode the information into a 32-bit unsigned integer
			$colorRGB = 16777215 * ((float)$row[$_GET['keyVal']] - (float)$_GET['keyMin'])/(float)$range;
			//Use bit shifting to encode the information into the Red, Green, Blue, and Alpha channels
			$colorR = ($colorRGB & 16711680) >> 16;
			$colorG = ($colorRGB & 65280) >> 8;
			$colorB = ($colorRGB & 255);
			$colorA = 0;
			*/
			$colorA = 127 * ((float)$row[$_GET['keyVal']] - (float)$_GET['keyMin'])/(float) $range;
			//Put the finite information into the picture to be returned to the client
			$color = imagecolorallocatealpha($im, $_GET['red'], $_GET['green'], $_GET['blue'], $colorA);
		}
		// Draw a single pixel to the image
		//imagesetpixel($im, $rowX, $rowY, $color);
	  // echo "RA=$rowRA, Dec=$rowDec -- X=$rowX, Y=$rowY -- $rowVal <br />";
		imagefilledellipse ( $im , $rowX, $rowY , $_GET['diam'] , $_GET['diam'], $color);
		imagecolordeallocate($im, $color);
		$howmany++;
	}
	
	
	
	//Send highly compressed PNG image to the client
	imagepng($im, NULL, 9, PNG_ALL_FILTERS);
  //imagegif($im);
  
	// Clean up
	imagedestroy($im);
	
	//$time_end = microtime(true);
	//$time = $time_end - $time_start;

	// Print out stats
	//echo ("<br />created image in " . $time . " seconds with " . $howmany . " points.");	

?>
