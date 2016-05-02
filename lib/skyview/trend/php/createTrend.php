<?php

// <script type="text/javascript">
// 	
// 	function progress(inc) {
// 		
//   		var val = progressbar.progressbar( "value" ) || 0;
//   		progressbar.progressbar( "value", val + inc );
//   }
// }
// </script>

error_reporting(-1);
ini_set("display_errors", 1);

function argmin($arr, $val_min, $startind, $log) {
  
    for($i = (int)$startind; $i < count($arr)-1; $i++){
        
		// $log->logInfo("i: ",$i);
		// $log->logInfo("startind: ",$startind);
		// $log->logInfo("arr: ",$arr[$i]);
		// $log->logInfo("valmin: ",$val_min);
		
        $val = (float)$arr[$i] - (float)$val_min;
        $val1 = (float)$arr[ max(0, (int)$i-1) ] - (float)$val_min;
        
		// $log->logInfo("val: ",$val);
		// $log->logInfo("val1: ",$val1);
		// $log->logInfo("i, i-1: ", $i . " " .$i-1);
		
		if ((float)$val >= 0.0){
            
			if( abs($val1) < abs($val) ){
                return $i-1;
			}
			else{
                return $i;
			}
		} // end if 
	} // end for
	
	return count($arr);
}

function &array_divide($arr, $divid){
	
	$ret = array();
	
	foreach ($arr as $key => $value) {
		$ret[] = (float)abs( (float)$value / (float)$divid);
	}
	
	return $ret;
}

function &interpolate($wave, $flux, $common, $log){

	$common_flux = array_fill(0, count($common), 0 );
    #print min(wave), max(wave)

    $last = 0;

    #iterate over the remaining value (most of them), and interpolate the new flux
    for($i = 1; $i < count($common)-1; $i++){
	
		// $log->logInfo("last: ", $last);
		// $log->logInfo("common[i]: ", $common[$i]);
		
        $index = argmin( $wave, $common[$i], $last, $log);
		
        $prevind = max(0,$index-1);
        $nextind = $index+1;
	    
        if($nextind < count($wave)){
            #/* do the interpolation */
            $value = $flux[$prevind] + ($flux[$nextind] - $flux[$prevind]) 
                * ( ($common[$i]-$wave[$prevind]) / ($wave[$nextind] - $wave[$prevind]) );
		}
	
		$common_flux[$i] = $value;
		
		$last = $index;
		
    }
    # return the common flux array
    return $common_flux;
}

function construct_image($wave, $flux, $wave_type, $rwave, $Z, $left_color, $right_color, 
	$min, $max, $obj, &$lines, $id, $size, $inc, $log){
	
	$times = 50;

	// if($inc == 0)
	// 	$times = 100;

	if(!is_dir("/var/www/html/TREND/$id/rest/")){
		exec("mkdir /var/www/html/TREND/$id/rest/", $out, $ret);	
		exec("mkdir /var/www/html/TREND/$id/obs/", $out, $ret);
	}
	
	$HSV_upp = RGBtoHSV(floatval($left_color['r']),floatval($left_color['g']),floatval($left_color['b']));
	$HSV_low = RGBtoHSV( floatval($right_color['r']),floatval($right_color['g']),floatval($right_color['b']) );
	
	// min / max values for rest
	$amin = 0; $amax = 0;
	
	// foreach($Z as $z){
	// 	$log->logInfo("redshift: ", $z);
	// }
	
	$log->loginfo("num rows in wave:", mysqli_num_rows($wave));
	
	# compute our new flux array based off of the common wave bin	
	while( $init_wave = mysqli_fetch_row($wave) ){
		
		/* get the next row of the queries */
		$init_flux = mysqli_fetch_row($flux);
		
		$w = array_slice($init_wave, 1, count($init_wave));
	 	$f = array_slice($init_flux, 1, count($init_flux));
		
		#$log->logInfo("wave size",$w);
		
		#TODO: better way of eliminating 0's from the end of this array
		
		$W = array();
		$F = array();
		
		#$log->logInfo("first index:", $w[0]);
		#$log->logInfo("last index:", $w[count($w)-1]);
		
		# gets rid of the 0s at the end of the array
		for($ii = 0; $ii < count($w); $ii++){
			
			if((float)$w[$ii] > 0.0){ 
				$W[] = (float)$w[$ii];
				$F[] = (float)$f[$ii];
			}
		}	
		
		
				
		$log->logInfo("init_flux count: ", count($W));
		
		$iflux = "";
		
		// foreach($rest_arr as $r){
		// 	$log->logInfo("divide: ", $r);
		// }
		
		if($wave_type == "rest"){
			
			$rest_arr = array_divide($W, 1.0 + (float)$Z[$obj] );
			
			$amin = argmin( $rwave, min( $rest_arr ), 0, $log );
			$amax = argmin( $rwave, max( $rest_arr ), 0, $log );
			
			// $amin = $b[0];
			// $amax = $b[1];
			 
			#$log->logInfo("amin: ", $amin);
			#$log->logInfo("amax: ", $amax);
		
			# interpolate over the common wave to get the common flux
			$iflux = interpolate($rest_arr, $F, $rwave, $log);
			$iflux = array_divide($iflux, (float)(array_sum($iflux) / count($iflux)) );
			
			// foreach($iflux as $if){
			// 	$log->logInfo("iflux: ", $if);
			// }
			
			for($ii = 0; $ii < count($iflux); $ii++){
				
				if($ii < $amin || $ii > $amax){
					$iflux[$ii] = 5.0;
					#$log->logInfo("new iflux val:", $iflux[$ii]);
				}
			}
			
		}else{
			$iflux = interpolate($W, $F, $rwave, $log);
			$iflux = array_divide($iflux, (float)array_sum($iflux)/count($iflux));
		}
		
		// foreach($iflux as $if){
		// 	$log->logInfo("iflux: ", $if);
		// }
		
		# create the default image
		$img = imagecreatetruecolor(count($iflux), 1);
		$fp = null;
		
		#scale the value between -1 and 5
		$scale_min = -1; $scale_max = 5;
		
		$cwave = "";	
			
		for($jj = 0; $jj < count($iflux); $jj++){
			
			$cwave = 0.0;
			if ($wave_type == "rest"){
				$cwave = $rwave[$jj];# / (1.0+$Z);	
			}else{
				#$log->logInfo("iflux: " , count($iflux));
				#$log->logInfo("Z: " , count($Z));
				#$log->logInfo("obj: " , $obj);
				
				
				$cwave = $rwave[$jj] / (1.0+$Z[$obj]);	
			}			
			
			# get the hue -- 240 equals blue for now
			//$hue = (($HSV_low['h']-$HSV_upp['h']) * (1.0 - ($cwave-$min) / $max ) + $HSV_upp['h']) % 360.0;
			//$hue = (($HSV_upp['h']-$HSV_low['h']) * (1.0 - ($cwave-$min) / $max ));
			$hue = ((240.0-0.0) * (1.0 - ($cwave-600) / 10000));
			
			# keep saturation at 1 for now
			$sat = 1.0;//($HSV_upp['s']-$HSV_low['s']) / 100.0;s
			
			# get the value
			$v = (255.0 + 0.9999) * ($iflux[$jj]-($scale_min)) / ($scale_max - $scale_min);
			
			//$log->logInfo("v: ", $v);
			
			if($wave_type == "rest"){
				
				if ((float)$iflux[$jj] <= -1.0){
					$v = 0.0;	
				}
				else if((float)$iflux[$jj] >= 5.0){
					$v = 255.0;
				}
			}
			
			#	$log->logInfo("v: ", $v);
			
			$val = (float)$v / 255.0;//* 1.5;
			
			# get rgb color from hsv
			$color = HSVtoRGB($hue, $sat, $val);
			
			#$log->logInfo("r ",(int) ($color[0]*255.0));
			#$log->logInfo("g ",(int) ($color[1]*255.0));
			#$log->logInfo("b ",(int) ($color[2]*255.0));
			
			# get php color
			$c = imagecolorallocate($img,
				(int) ($color[0]*255.0), (int) ($color[1]*255.0), (int) ($color[2]*255.0));
			
			#set the pixel
			imagesetpixel($img, $jj, 0, $c);
			
		} // end for
						
		# create the image
		if($wave_type == "rest"){
			$ret = imagepng($img, "/var/www/html/TREND/$id/rest/line$id" . "_O_$obj.png");
			#set the name	
			array_push($lines, "line$id"."_O_$obj.png");
		}else{
			$ret = imagepng($img, "/var/www/html/TREND/$id/obs/line$id" . "_R_$obj.png");
			array_push($lines, "line$id"."_R_$obj.png");
		}
		
		# destroy resource
		imagedestroy($img);
		
		# next object
		$obj++; 
		
		$incr = floatval($obj/$size) * $times + $inc; 
		
		file_put_contents("/var/www/html/TREND/$id/progress.txt", $incr);
				
	} // end while	

} // end construct image

?>
