<?php
$unique_names = array(
	0=> "shit",
	2=> "fuck",
	4=> "asshole"
);

var_dump(array_values($unique_names));

echo "\n\n";

$new_arr = array_values($unique_names);
var_dump($new_arr);
?>