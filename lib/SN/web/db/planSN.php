<?php
/*
============================================================================================
Filename: 
---------
planSN.php

Description: 
------------
This PHP file is used to return variable DataTables-specific columns back to the Supernovae v0.1 extension.

Nikhil Venkatesh
09/03/2013
ADMT Lab - Supernovae Project
============================================================================================
*/

$json = json_decode($_POST['json_str'], true);

$plan_data = $json['plan'];     //store the plan data
$res = array();
$res['aaData'] = array();
$res['aoColumnDefs'] = array(
    array( 
        'aTargets' => array(0),
        'sTitle' => 'Dates'
    ),
    array(
        'aTargets' => array(1),
        'sTitle' => 'Supernovas'
    )
);

foreach ($plan_data as $key => $value) {
    $row_data = array();
    array_push($row_data, $key);
    //$new_arr = array_filter($value);
    if(!empty($value)){     //check to see if there are supernovas scheduled for that day
        $supernovas = "";   //used to concatenate the names of all the supernovas for that day
        foreach ($value as $key2 => $value2) {
            $supernovas .= $value2['name'] . ", ";
        }
        array_push($row_data, rtrim($supernovas, ", "));
    } else {
        array_push($row_data, 'No supernovas scheduled.');
    }
    array_push($res['aaData'], $row_data);
}

$res['bJQueryUI'] = true;
$res['bDestroy'] = true;
$res['bLengthChange'] = false;
$res['bDeferRender'] = true;

$results = print_r($res, true);

echo json_encode($res);

?>