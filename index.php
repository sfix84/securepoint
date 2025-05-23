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
$file = fopen('../updatev12-access-pseudonymized.log', 'r');

if ($file){
    // Initialize count variable for readability later on
    $count = 0;
    // As long as Data comes from file:
    while(($log = fgets($file)) !== false){
        // For readability show count-variable
        echo "Zeile: " . $count++ . "<br>";
        // Create array by function explode and space as separator
        $logarray = explode(" ", $log);
        echo "<pre>";
        var_dump($logarray);
        echo "</pre><br>";
        //Write only the ip adress into DB for testing
        $ip = $logarray[0];
        $stmt = $db->prepare("INSERT INTO log_data (ip) VALUES (:ip)");
        $stmt->execute(['ip' => $ip]);
    }

    fclose($file);

} 

