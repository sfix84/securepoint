<?php

// DB connection
try {
    $db = new PDO("mysql:dbname=securepoint-logs;host=localhost",
    "root",
    "");
    } catch (PDOException $e) {
    echo 'Fehler: ' . htmlspecialchars($e->getMessage());
    exit();
    }

// Open file pointer to log-file
$file = fopen('../updatev12-access-pseudonymized_temp.log', 'r');

if ($file){

    // As long as Data comes from file:
    while(($log = fgets($file)) !== false){

        $log = fgets($file);
        $logarray = explode(" ", $log);
        // decodation manual stackoverflow for logfiles with gzip part in it:
        // gets the part of the array, where the decoded data is
        $jpart = $logarray[12];
        echo $jpart;
        //gets the gzip and base64 decoded json part via cutting off the "specs=" with regex
        $json = preg_match('/specs=([A-Za-z0-9+\/=]+)/', $jpart, $matches);
        // puts the decoded data in a variable: matches[0] outside the brackets(), matches [1] is the part inside the brackets
        $basegzipjson = $matches[1];
        // fist decoding with base64 is needed
        $gzipjson = base64_decode($basegzipjson);
        //then decoding with gzip is needed
        $json = gzdecode($gzipjson);
        // creating an assoziative array
        $specsarray = json_decode($json);
        echo "<pre>";
        var_dump($specsarray);
        echo "</pre>";
        echo "<br>--------------------------<br>";
        echo "<pre>";
        var_dump($logarray);
        echo "</pre>";
        echo "<br>--------------------------<br>";
        
        $ip = $logarray[0];
        $updateserver = $logarray[1];
        $datetime = $logarray[2];
        $method = $logarray[3];
        $url = $logarray[4];
        $protocol = $logarray[5];
        $status = $logarray[6];
        $size = $logarray[7];
        $reverse_proxy = $logarray[8];
        $duration = $logarray[9];
        $serialnumber_license = $logarray[10];
        $firmware_version = $logarray[11];
        $system_status = $logarray[12];

        $stmt = $db->prepare("INSERT INTO log_data (ip, updateserver, datetime, method, url, protocol, status, size, reverse_proxy, duration,serialnumber_license, firmware_version, system_status) VALUES 
        (:ip, :updateserver, :datetime, :method,  :url,  :protocol,  :status,  :size, :reverse_proxy,  :duration,  :serialnumber_license, :firmware_version, :system_status)
        ");
        $stmt->execute(
            ['ip' => $ip,
            'updateserver' => $updateserver,
            'datetime' => $datetime,
            'method' => $method,
            'url' => $url,
            'protocol' => $protocol, 
            'status' => $status,
            'size' => $size,
            'reverse_proxy' => $reverse_proxy, 
            'duration' => $duration,
            'serialnumber_license' => $serialnumber_license,
            'firmware_version' => $firmware_version,
            'system_status' => $system_status]
        );
        
    }

    fclose($file);

} 

