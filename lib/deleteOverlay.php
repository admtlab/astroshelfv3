<?php

/*
 deleteOverlay

 DESCRIPTION
    deletes the overlay image file from the server

 INPUTS
    filename - filename of the overlay

 */

//----------------------------------------------------------------
// Main Code
//

//require_once('PhpConsole.php')
//PhpConsole::start();

require dirname(__FILE__) . '/klogger/KLogger.php';
$log   = KLogger::instance('/tmp/log/');	
error_reporting(-1); // report all errors 

$file = $_REQUEST['filename'];
echo "this is the file name for deletion " . $file . "\n";
$imgURL = "/u/astro/images/CUSTOM/".$file;
echo "file path: " . $imgURL . "\n";

$del = unlink($imgURL);

echo "delete: " .$del . "\n";

?>