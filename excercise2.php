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



    
$stmt = "SELECT `serialnumber_license`, COUNT(*) AS `anzahl` FROM `log_data` GROUP BY `serialnumber_license` HAVING COUNT(*) > 1 ORDER BY `anzahl` DESC;";
$data = $db->query($stmt);

$multiple_licenses = [];

while($row = $data->fetch(PDO::FETCH_ASSOC)){
    echo "<pre>";
    var_dump($row);
    echo "</pre><br>--------------------<br>";
    array_push($multiple_licenses, $row['serialnumber_license']);
}
echo "<pre>";
var_dump($multiple_licenses);
echo "</pre><br>--------------------<br>";

//$stmt2 = $SELECT `serialnumber_license`, `key`, `mac` FROM 'log_data';

