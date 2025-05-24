<?php

// DB connection
try {
    $db = new PDO(
        "mysql:dbname=securepoint-logs;
        host=localhost",
        "root",
        ""
        );
    } catch (PDOException $e) {
        echo 'Fehler: ' . htmlspecialchars($e->getMessage());
        exit();
    }

// Open file pointer to log-file
$file = fopen('../updatev12-access-pseudonymized.log', 'r');

if ($file){

    // As long as Data comes from file:
    while(($log = fgets($file)) !== false){

        $log = fgets($file);
        $logarray = explode(" ", $log);
        // decodation manual stackoverflow for logfiles with gzip part in it:
        // gets the part of the array, where the decoded data is
        $jpart = $logarray[12];
        //gets the gzip and base64 decoded json part via cutting off the "specs=" with regex
        $json = preg_match('/specs=([A-Za-z0-9+\/=]+)/', $jpart, $matches);
        // puts the decoded data in a variable: matches[0] outside the brackets(), matches [1] is the part inside the brackets
        $basegzipjson = $matches[1];
        // fist decoding with base64 is needed
        $gzipjson = base64_decode($basegzipjson);
        //then decoding with gzip is needed
        $json = gzdecode($gzipjson);
        // creating an assoziative array
        $specsarray = json_decode($json, true);
                
        // putting the values into variables for sql placeholders, with coalescent operater cause as seen in tests sometimes there is no value
        $ip = $logarray[0] ?? NULL;
        $updateserver = $logarray[1] ?? NULL;
        $datetime = $logarray[2] ?? NULL;
        $method = $logarray[3] ?? NULL;
        $url = $logarray[4] ?? NULL;
        $protocol = $logarray[5] ?? NULL;
        $status = $logarray[6] ?? NULL;
        $size = $logarray[7] ?? NULL;
        $reverse_proxy = $logarray[8] ?? NULL;
        $duration = $logarray[9] ?? NULL;
        $serialnumber_license = $logarray[10] ?? NULL;
        $firmware_version = $logarray[11] ?? NULL;
        $system_status = $logarray[12] ?? NULL;

        $mac = $specsarray["mac"] ?? NULL;
        $architecture = $specsarray["architecture"] ?? NULL;
        $machine = $specsarray["machine"] ?? NULL;
        $nic = $specsarray["nic"] ?? NULL;
        $mem = $specsarray["mem"] ?? NULL;
        $cpu = $specsarray["cpu"] ?? NULL;
        $disk_root = $specsarray["disk_root"] ?? NULL;
        $disk_data = $specsarray["disk_data"] ?? NULL;
        $uptime = $specsarray["uptime"] ?? NULL;
        $fwversion = $specsarray["fwversion"] ?? NULL;
        $l2tp = $specsarray["l2tp"] ?? NULL;
        $qos = $specsarray["qos"] ?? NULL;
        $httpaveng = $specsarray["httpaveng"] ?? NULL;
        $spcf = $specsarray["spcf"] ?? NULL;
        $architecture_json = $specsarray["architecture"] ?? NULL;
        $footer = $specsarray["footer"] ?? NULL;


        //sql part for filling the database
        $stmt = $db->prepare("INSERT INTO log_data (
        ip, 
        updateserver, 
        datetime, 
        method, 
        url, 
        protocol, 
        status, 
        size, 
        reverse_proxy, 
        duration,
        serialnumber_license, 
        firmware_version, 
        system_status,
        mac,
        architecture,
        machine,
        nic,
        mem,
        cpu,
        disk_root,
        disk_data,
        uptime,
        fwversion,
        l2tp,
        qos,
        httpaveng,
        spcf,
        architecture_json,
        footer
        ) VALUES 
        (
        :ip, 
        :updateserver, 
        :datetime, 
        :method,  
        :url, 
        :protocol, 
        :status, 
        :size, 
        :reverse_proxy,  
        :duration,  
        :serialnumber_license, 
        :firmware_version, 
        :system_status,
        :mac,
        :architecture,
        :machine,
        :nic,
        :mem,
        :cpu,
        :disk_root,
        :disk_data,
        :uptime,
        :fwversion,
        :l2tp,
        :qos,
        :httpaveng,
        :spcf,
        :architecture_json,
        :footer
        )
        ");
        $stmt->execute(
            [
            'ip' => $ip,
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
            'system_status' => $system_status,
            'mac' => $mac,
            'architecture' => $architecture,
            'machine' => $machine,
            'nic' => $nic,
            'mem' => $mem,
            'cpu' => $cpu,
            'disk_root' => $disk_root,
            'disk_data' => $disk_data,
            'uptime' => $uptime,
            'fwversion' => $fwversion,
            'l2tp' => $l2tp,
            'qos' => $qos,
            'httpaveng' => $httpaveng,
            'spcf' => $spcf,
            'architecture_json' => $architecture_json,
            'footer' => $footer
            ]
        );
        
    }

    fclose($file);

} 

