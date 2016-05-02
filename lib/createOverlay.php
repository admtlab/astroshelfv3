<?php
  /**
   * createOverlay
   *
   * creates a transparent overlay of a subset of objects
   *
   * Inputs ($_POST):
   *  raStr, decStr - name of attributes to map ra, dec to (String)
   *  DecMax, DexMin - range of Dec 
   *  RAMax, RAMin - range of RA
   *  width, height - width, height (in pixels) of output image
   *  db - which database to query
   *  query - sql query to use (must include attributes called RA, Dec)
   *    also include an attribute called keyVal in query if want to map alpha to
   *  keyValStr - name of attribute to map alpha value to (String)
   *  keyMax, keyMin - range of keyVal so can map alpha value
   *  red, green, blue - rgb values (0 to 255) of overlay color
   *  diam - size of each datapoint
   *
   * Outputs
   *   - 2-d image of custom overlay
   *
   **/

  // Set header to png if you just want the returned image to show up
    //header("Content-type: image/gif");
  // Set header to HTML if you want to be able to print out stats and debug info

//require_once('PhpConsole.php')
//PhpConsole::start();

//require dirname(__FILE__) . '/klogger/KLogger.php';
//$log = KLogger::instance('/var/www/html/brian/');

//	header("Content-type: image/png");
	
	//header("Content-type: text/plain");
	error_reporting(-1); // report all errors 
   
	function hex2rgb($hex){
				
		if(strlen($hex) == 3){
			
			$r = hexdec(substr($hex,0,1).substr($hex,0,1));
			$g = hexdec(substr($hex,1,1).substr($hex,1,1));
			$b = hexdec(substr($hex,2,1).substr($hex,2,1));
		
		}else{
			
			$r = hexdec(substr($hex,0,2));
			$g = hexdec(substr($hex,2,2));
			$b = hexdec(substr($hex,4,2));
			
		}
		
		$rgb = array($r,$g,$b);
		return $rgb;
	}

  function currPageURL() {
   $pageURL = 'http';
   if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
   $pageURL .= "://";
   if ($_SERVER["SERVER_PORT"] != "80") {
    $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
   } else {
    $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
   }
   return $pageURL;
  }

  $scale = 1.0;
  $norm = 0.0;

  $RAMin = (float)$_REQUEST['RAMin'];
  //$RAMax = (float)$_REQUEST['RAMax'];
  $DecMin = (float)$_REQUEST['DecMin'];
  //$DecMax = (float)$_REQUEST['DecMax'];
  $spanx = (float)$_REQUEST['spanx'];
  $spany = (float)$_REQUEST['spany'];
  // Attributes to map to
  $keyValStr = $_REQUEST['dataType'];
  
  /*------Di Bao--------*/
//  $type = $_REQUEST['type'];
//  $query = $_REQUEST['query'];
  $color = $_REQUEST['color'];
  $raDecTable = json_decode($_REQUEST['table']);
	
  /*--------End---------*/
	//   
// 
// $RAMin = 1.0;
// $RAMax = 2.0;
// $DecMin = 1.0;
// $DecMax = 2.0;

	//print_r($raDecTable);exit; //Check the results set here!!!!!!!!!!!!!!!!!!!!!!!!
	
/*------------------------------------------------------------------------------------------------------------------------------------------*/
        
	if (count($raDecTable) > 0) {
	  // Parameters of overlay
	  // $totalLat = abs((float)$DecMax + $spany);
	  // $totalLon = abs((float)$RAMax - $RAMin);
	  
	  // echo "$RAMin, $RAMax : $DecMin, $DecMax <br />";
	  // Needed to determine the opacity of the object marker in the image
	  
	  $ratio = $spany / $spanx;

	  $width = 1024.0;
	  $height = 1024.0;
	  $symSizeX = 10;#$width / 40.; 
	  $symSizeY = 10;#$height / 40.;

	  // Initialize image
	  $im = imagecreatetruecolor($width, $height);
	  //echo "width = $width, height = $height <br />";
	  imagealphablending($im, true);
	  imagesavealpha($im, true);
	  imagefill($im, 0, 0, imagecolorallocatealpha($im, 0, 0, 0, 127));
	  //imagefill($im,0,0,imagecolorallocatealpha($im, 255, 255, 255, 127));
	  
	  $rgb = hex2rgb($color);
	  
	  foreach($raDecTable as $RADec){
			
	    $RA = (float) $RADec[0];
	    $Dec = (float) $RADec[1];
	    //$RA = 1.0;
	    //$Dec = 1.0;
		
	    if (isset($RA) && isset($Dec) ) {
  	      
	      $rowX = ( abs($RA - $RAMin) * ($width / $spanx));
	      $rowY = ( abs($Dec - $DecMin) * ($height / $spany));
	      //$rowX = ( abs($RA - $RAMin) * ($width / $totalLon));
	      //$rowY = ( abs($Dec - $DecMin) * ($height / $totalLat));
		 		  
	     // $log->logInfo("x,y of radec: " . $rowX . ", " . $rowY . ", " . $RA . ", " . $Dec);
	      
	      // Determine the color with which to draw based on the keyValue
	      $color = imagecolorallocatealpha($im, $rgb[0], $rgb[1], $rgb[2], 0);

	      // Draw a single pixel to the image
	      imageellipse ( $im , $rowX, $rowY , $symSizeX , $symSizeY, $color);
	      imagecolordeallocate($im, $color);
	    }
	  }
	
	  //Send highly compressed PNG image to the client	  
	  $filename = hash("md5", "".microtime().$minX.$maxX).".png";
	  imagepng($im, "/var/www/html/Custom/".$filename);
	  echo $filename;
	  
	  //echo $im;
	  // imagegif($im);
	  // Clean up
	  //imagedestroy($im);
	}
	else {
	  //@TODO curl_exec ERROR
	}

?>
