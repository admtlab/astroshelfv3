<?php
/*
============================================================================================
Filename: 
---------
convert_ra_dec.php

Description: 
------------
This PHP file is a general functiont to convert HMSDMS to ra and dec

Di Bao
02/13/2013
ADMT Lab - Supernovae Project
============================================================================================
*/

function convert_ra_dec($raH, $raM, $raS, $decD, $decM, $decS, $decSign){
	
	$my_ra = ($raH + ($raM / 60) + ($raS / 3600)) * 15;
	$my_dec = ($decD + ($decM / 60) + ($decS / 3600));
	if($decSign == "-"){
		$my_dec = $my_dec * -1.0;
	}
	
	return array($my_ra, $my_dec);
}

function convert_hmsdms($ra, $dec){
	
	$tmp1 = $ra / doubleval(15);
	if($tmp1 >= 0)	$hour = floor($tmp1);
	else	$hour = ceil($tmp1);
	
	$tmp2 = ($tmp1 - $hour) * doubleval(60);
	if($tmp2 >= 0)	$minute = floor($tmp2);
	else	$minute = ceil($tmp2);
	
	$tmp3 = ($tmp2 - $minute) * doubleval(60);
	$second = round($tmp3, 2);
	
	if($dec >= 0)	$degree = floor($dec);
	else	$degree = ceil($dec);
	
	$tmp4 = ($dec - $degree) * doubleval(60);
	if($tmp4 >= 0)	$mind = floor($tmp4);
	else	$mind = ceil($tmp4);
	
	$tmp5 = ($tmp4 - $mind) * doubleval(60);
	$secd = round($tmp5, 2);
	
	if($dec >= 0){
		$hms = $hour . 'h ' . $minute . 'm ' . $second . 's ';
		$dms = '+'. $degree . 'd ' . $mind . '\' ' . $secd . '"';
	}else{
		$hms = $hour . 'h ' . $minute . 'm ' . $second . 's ';
		$dms = '-'. abs($degree) . 'd ' . abs($mind) . '\' ' . abs($secd) . '"';		
	}
	
	return array($hms, $dms);
}
?>