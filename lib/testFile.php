<?php
	
	header('Content-type: text/plain');
	
	if(@fopen("http://das.sdss.org/imaging/2662/41/Zoom/6/fpC-002662-6-41-0283-z20.jpeg","r"))
		echo "exist";
	else{
		echo "does not exist";
	}
	
?>