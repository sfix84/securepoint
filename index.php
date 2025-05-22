<?php
/*
$logdata = file_get_contents('../updatev12-access-pseudonymized.log', false, null, 0, 100000);


$datasplit = preg_split('/^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/',$logdata);

print_r($datasplit);
*/
$file = fopen('../updatev12-access-pseudonymized.log', 'r');

if ($file){
    $count = 0;
    while($line = fgets($file) !== false){
        echo $count++ . "<br>";
    }
    fclose($file);
} 

