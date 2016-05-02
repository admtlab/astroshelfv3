<?php

$ramax = $_GET['RAMax'];
$ramin = $_GET['RAMin'];
$decmax = $_GET['DecMax'];
$decmin = $_GET['DecMin'];

//$output = null;

exec("/u/astro/astro_env/bin/python /u/astro/Mongo_Astro/src/tim_demo.py $ramax $ramin $decmax $decmin", $output, $result);
//echo var_export($output,TRUE);
echo stripslashes(json_encode($output));

?>
