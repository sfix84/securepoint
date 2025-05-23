<?php

$file = fopen('../updatev12-access-pseudonymized.log', 'r');

if ($file){

    $count = 0;

    while($line = fgets($file) !== false){
        echo "Zeile: " . $count++ . "<br>";
        $log = fgets($file);
        // echo $log;
        $logarray = explode(" ", $log);
        echo "<pre>";
        var_dump($logarray);
        echo "</pre><br>";
    }

    fclose($file);

} 

