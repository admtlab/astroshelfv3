<?php

header("Content-type: text/plain");

error_reporting(-1);

/* read header from jpeg */

$url = $_GET['url'];
$survey = $_GET['survey'];
$data = '';

$ret_val = array(
	"CRVAL_1" => -1,
	"CRVAL_2" => -1,
	"CRPIX_1" => -1,
	"CRPIX_2" => -1,
	"CD1_1" => -1,
	"CD1_2" => -1,
	"CD2_1" => -1,
	"CD2_2" => -1,
	"CTYPE1" => -1,
	"CTYPE2" => -1
);

if($_GET['type'] == "JPEG"){
	
	if($survey == "LSST"){
				
		$path = "/var/www/html/LSST/images/";
		$path .= $url;
		$url = $path;
		
		exec("/var/www/html/jhead/jhead $url", $data, $ret);
		
	foreach($data as $line){						
	        $values = explode(" : ", $line);
				foreach($values as $v){
	          		if(strncmp("CRVAL1 ",$v,7) == 0){
					 	
	                	$value = explode("=",$v);
						$newval = explode("/",$value[1]);
	                	$float = floatval($newval[0]);
	                	$ret_val["CRVAL_1"] = $float;
	        		}
	        		else if(strncmp("CRVAL2 ",$v,7) == 0){
						
	                	$value = explode("=",$v);
						$newval = explode("/",$value[1]);
	                	$float = floatval($newval[0]);
	                	$ret_val["CRVAL_2"] = $float;
	        		}
	        		else if(strncmp("CRPIX1",$v,6) == 0){
	                	$value = explode("=",$v);
	                	$float = floatval($value[1]);
	                	$ret_val["CRPIX_1"] = $float;
	        		}
	        		else if(strncmp("CRPIX2",$v,6) == 0){
	                	$value = explode("=",$v);
	                	$float = floatval($value[1]);
	                	$ret_val["CRPIX_2"] = $float;
	        			}
	        		else if(strncmp("CD1_1",$v,5) == 0){
	                	$value = explode("=",$v);
										$newval = explode("/",$value[1]);
	                	$float = floatval($newval[0]);
	                	$ret_val["CD1_1"] = $float;
	        				}
	       			else if(strncmp("CD1_2",$v,5) == 0){
	                	$value = explode("=",$v);
										$newval = explode("/",$value[1]);
	                	$float = floatval($newval[0]);
	                	$ret_val["CD1_2"] = $float;
	        				}
	        		else if(strncmp("CD2_1",$v,5) == 0){
	                	$value = explode("=",$v);
						$newval = explode("/",$value[1]);
	                	$float = floatval($newval[0]);
	                	$ret_val["CD2_1"] = $float;
	        				}
	        		else if(strncmp("CD2_2",$v,5) == 0){
	                		$value = explode("=",$v);
							$newval = explode("/",$value[1]);
	                		$float = floatval($newval[0]);
	                		$ret_val["CD2_2"] = $float;
	        				}
	        		else if(strncmp("CTYPE1 ",$v,7) == 0){
	                	$value= explode("=",$v);
										$newval = explode("/",$value[1]);
	                	$ret_val["CTYPE1"] = $newval[0];
	        				}
	        		else if(strncmp("CTYPE2 ",$v,7) == 0){
	                	$value= explode("=",$v);
										$newval = explode("/",$value[1]);
	                	$ret_val["CTYPE2"] = $newval[0];
	        		}
				} // for each
			} // for each
		} // if LSST
		else if ($survey == "FIRST"){
				
		$path = "/var/www/html/FIRST/tim/images/";
		$path .= $url;
		$url = $path;
				
		exec("/var/www/html/jhead/jhead $url", $data, $ret);
		
		foreach($data as $line){
			$values = explode(" : ", $line);
			foreach($values as $v){
				if(strncmp("CRVAL1 ",$v,7) == 0){
					$value = explode("=",$v);
					$newval = explode("/",$value[1]);
					$float = floatval($newval[0]);
					$ret_val["CRVAL_1"] = $float;
				}
				else if(strncmp("CRVAL2 ",$v,7) == 0){
					$value = explode("=",$v);
					$newval = explode("/",$value[1]);
					$float = floatval($newval[0]);
					$ret_val["CRVAL_2"] = $float;
				}
				else if(strncmp("CRPIX1",$v,6) == 0){
					$value = explode("=",$v);
					$float = floatval($value[1]);
					$ret_val["CRPIX_1"] = $float;
				}
				else if(strncmp("CRPIX2",$v,6) == 0){
					$value = explode("=",$v);
					$float = floatval($value[1]);
					$ret_val["CRPIX_2"] = $float;
				}
				else if(strncmp("CDELT1",$v,6) == 0){
					$value = explode("=",$v);
					$newval = explode("/",$value[1]);
					$float = floatval($newval[0]);
					$ret_val["CDELT_1"] = $float;
				}
				else if(strncmp("CDELT2",$v,6) == 0){
					$value = explode("=",$v);
					$newval = explode("/",$value[1]);
					$float = floatval($newval[0]);
					$ret_val["CDELT_2"] = $float;
				}
				else if(strncmp("CD1_1",$v,5) == 0){
					$ret_val["CD1_1"] = 0.0;
				}
				else if(strncmp("CD1_2",$v,5) == 0){
					$ret_val["CD1_2"] = 0.0;
				}
				else if(strncmp("CD2_1",$v,5) == 0){
					$ret_val["CD2_1"] = 0.0;
				}
				else if(strncmp("CD2_2",$v,5) == 0){
					$ret_val["CD2_2"] = 0.0;
				}
				else if(strncmp("CTYPE1 ",$v,7) == 0){
					$value= explode("=",$v);
					$ret_val["CTYPE1"] = $value[1];
				}
				else if(strncmp("CTYPE2 ",$v,7) == 0){
					$value= explode("=",$v);
					$ret_val["CTYPE2"] = $value[1];
				}
			}	// for each
		} // for each 
	} // if FIRST
} // if JPEG
else if ($_GET['type'] == "TEXT" && $survey == "SDSS"){
	
	$fh = fopen($url,'r');
	$data = fread($fh,filesize($url));
	
	$values = explode("\n",$data);
	
	foreach($values as $v){
		
		$line = explode(',',$v);
		
				if(strncmp("CRVAL1",$line[0],6) == 0){
					$float = floatval($line[1]);
					$ret_val["CRVAL_1"] = $float;
				}
				else if(strncmp("CRVAL2",$line[0],6) == 0){
					$float = floatval($line[1]);
					$ret_val["CRVAL_2"] = $float;
				}
				else if(strncmp("CRPIX1",$line[0],6) == 0){
					$float = floatval($line[1]);	
					$ret_val["CRPIX_1"] = $float;
				}
				else if(strncmp("CRPIX2",$line[0],6) == 0){
					$float = floatval($line[1]);
					$ret_val["CRPIX_2"] = $float;
				}
				else if(strncmp("CD1_1",$line[0],5) == 0){
					$float = floatval($line[1]);
					$ret_val["CD1_1"] = $float;
				}
				else if(strncmp("CD1_2",$line[0],5) == 0){
					$float = floatval($line[1]);
					$ret_val["CD1_2"] = $float;
				}
				else if(strncmp("CD2_1",$line[0],5) == 0){
					$float = floatval($line[1]);
					$ret_val["CD2_1"] = $float;
				}
				else if(strncmp("CD2_2",$line[0],5) == 0){
					$float = floatval($line[1]);
					$ret_val["CD2_2"] = $float;
				}
			else if(strncmp("CTYPE1",$line[0],6) == 0){
				$ret_val["CTYPE1"] = $line[1];
			}
			else if(strncmp("CTYPE2",$line[0],6) == 0){
				$ret_val["CTYPE2"] = $line[1];
			}
		
		}	
}
echo json_encode($ret_val);

?>
