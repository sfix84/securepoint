<html>
<head>
    <meta charset="utf-8">
    <html lang="en">
    <title>Seceruepoint recruitment excercise</title>
    <style>
        table {
            border: 1px solid grey;
            border-collapse: collapse;
        }
        td, th {
            border: 1px solid grey;
            padding: 0.25rem;
            width: 25%;
        }
    </style>
</head>
<body>

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

    array_push($multiple_licenses, [
        'serialnumber_license' => $row['serialnumber_license'],
        'anzahl' => $row['anzahl']
    ]);
}

echo "<div><h2>Question 1: What are the 10 serial numbers that try to access the server the most and how many times are they doing this?</h2>";
echo "<br><h3>Answer:</h3><br>";
echo "<table><tr><th>serial number</th><th>connection calls</th></tr>";

for($i=0; $i<10; $i++){
    echo "<tr>";
    echo "<td>{$multiple_licenses[$i]['serialnumber_license']}</td>";
    echo "<td>{$multiple_licenses[$i]['anzahl']}</td>";
    echo "</tr>";
}

echo "</table></div>";

// echo "<pre>";
// print_r($multiple_licenses);
// echo "<pre>";

$multiple_license_values =[];

echo "<pre>";
var_dump($multiple_license_values);
echo "</pre><br>------------<br>";
foreach($multiple_licenses AS $value){
    $actual_license = $value['serialnumber_license'];
    if(in_array($actual_license, $multiple_license_values)){
     }else{
         array_push($multiple_license_values, $actual_license);
     }
}
echo "<pre>";
var_dump($multiple_license_values);
echo "</pre><br>------------<br>";


// $stmt2 = SELECT `mac` FROM log_data WHERE serialnumber_license IN (
//     SELECT
// )

?>

</body>
</html>

