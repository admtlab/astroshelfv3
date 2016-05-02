<?php
error_reporting(E_ERROR);

function fix_CBAT_time_string($_curr_unix_time){
	$pattern1 = '/(\S+)\:(\d{1})\.(\d+)ZZ/i';
	$replacement1 = '$1:0$2.$3Z';
	$pattern2 = '/(\S+)\:(\d{2})\.(\d+)ZZ/i';
	$replacement2 = '$1:$2.$3Z';
	$_curr_unix_time = preg_replace($pattern1, $replacement1, $_curr_unix_time);
	$_curr_unix_time = preg_replace($pattern2, $replacement2, $_curr_unix_time);
	return $_curr_unix_time;
}

//$str = "2013-02-07T11:17:15.28Z";
//$str = fix_CBAT_time_string($str);
//echo $str . "\n";
$time = strtotime($str);
//echo $time . "\n";
//exit;

$string = <<<XML
<?xml version='1.0' ?>
<feed xmlns:dc="shit">
	<title>fwefwe</title>
	<entry>
		<value>1</value>
	</entry>
	<entry>
		<value>22323</value>
		<link rel="sdf" href="hsssttp://cbat.feijf.com"/>
		<link rel="xml" href="http://cbat.feijf.com"/>
		<dc:date>2013/02/13</dc:date>
	</entry>
	<entry>
		<value>3</value>
	</entry>
</feed>
XML;

$str = <<<XML
<entry>
	<title>CBAT</title>
	<id>2r2323432</id>
	<author>di bao</author>
	<voevent:RA>1.4343</voevent:RA>
	<voevent:DEC>1.4343</voevent:DEC>
</entry>
XML;

//$xml = new SimpleXMLElement($string);
$xml = new SimpleXMLElement($str);
//$obj = $xml->entry;
//echo$xml->entry[1]->children('dc', true)->date;exit;
//print_r($obj);
//echo sizeof($obj);
//echo "\n";
//echo $obj[2]->asXML();
//$i = 0;
//foreach($obj as $a){
	//echo $a->value . "\n";
	//echo $obj->asXML();
	//echo "i\n";
	//$str = $a->asXML();
	//echo $str . "\n";
//}
//echo $xml->entry[1]->link[0]['href'];
var_dump($xml);
//print strval($xml->children('voevent', true)->RA);
print strval($xml->RA);

echo "\n\nflushing\n\n";
?>