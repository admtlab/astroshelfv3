<?php
$ra = $_REQUEST['ra'];
$dec = $_REQUEST['dec'];
$numObj = $_REQUEST['numObj'];
$typeObj = $_REQUEST['typeObj'];
$minWave = $_REQUEST['minWave'];
$maxWave = $_REQUEST['maxWave'];
$numWave = $_REQUEST['numWave'];

error_reporting(-1);
$query = "SELECT TOP $numObj S.specObjID as specObjID, dbo.fGetUrlFitsSpectrum(S.specObjID) as FITS, dbo.fGetUrlSpecImg(S.specObjID) as Img, S.ra as Ra, S.dec as Declination
		FROM SpecObj as S 
		WHERE S.specClass = $typeObj 
		ORDER BY dbo.fDistanceArcMinEq($ra, $dec, S.ra, S.dec) asc";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://cas.sdss.org/astrodr7/en/tools/search/x_sql.asp?format=xml&cmd=" . urlencode($query));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$output = curl_exec($ch);
curl_close($ch); 
//echo $output;

$i = preg_match('/<Answer>(.*?)<\/Answer>/is', $output, $matches);
$result = "<result> " . $matches[1] . " </result>";
//echo $result;exit;

$result = simplexml_load_string($result);

$currDir = dirname(__FILE__);
//echo $currDir . '<br/>';
//var_dump($result); exit;

exec("rm $currDir/proc/parsed/*");
exec("rm $currDir/proc/fits/*");

print "<map name='trendMap'>";

$a = 0; 
$b = 2;
foreach($result->Row as $row){
	$filename = $row->FITS;
	$specobjid = $row->specObjID;
	$ra = $row->Ra;
	$dec = $row->Declination;
	$plot = $row->Img;
	
	$newPath = dirname(__FILE__) . "/proc/fits/$specobjid.fit";
	$succ = copy($filename, $newPath);
	print "<area shape='rect' coords='0,$a,700,$b' title='$specobjid' href='#' onclick='top.showPlot(\"$plot\", $ra, $dec)'>";
	$a += 2;
	$b += 2;
}

print "</map>";
exec("/usr/local/bin/python2.7 $currDir/parseSpecFiles.py");
?>
