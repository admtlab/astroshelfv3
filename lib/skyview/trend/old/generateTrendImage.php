<?php 
// Generates trend image gif

// Sort the supernovas by redshift
function sortZ($a, $b) {
	$val = floatval($a[0]) - floatval($b[0]);
	if ($val < 0) return -1;
	if ($val > 0) return 1;
	return $val;
}

// Sort the wavelengths -- asc
function sortWave($a, $b) {
	$val = floatval($a->wave) - floatval($b->wave);
	if ($val < 0) return -1;
	if ($val > 0) return 1;
	return $val;
}

// Get an array of all the files
$handle = opendir(dirname(__FILE__) . "/proc/parsed");
$file_list = array();
while(FALSE !== ($file = readdir($handle))) {
	if(stristr($file, ".fit")) {
		$file_list[] = file(dirname(__FILE__) . "/proc/parsed/$file");
	}
}
closedir($handle);
usort($file_list, "sortZ");
$num_waves = 500;
$num_lines = count($file_list);
// Initialize the image -- each nova 2 rows of px so easier to use/see
$img = imagecreatetruecolor($num_waves, $num_lines*2);

$pixY = 0;
foreach ($file_list as $file) {
  $pixX = 0;
  foreach ($file as $value) {
	$colorID = imagecolorallocate($img, $value, $value, $value);
		  imagesetpixel($img, $pixX, 2*$pixY, $colorID);
		  imagesetpixel($img, $pixX, 2*$pixY+1, $colorID);
		$pixX++;
	  }
	$pixY++;
}
header("Content-type: image/gif");
imagegif($img);
imagedestroy($img);
?>
