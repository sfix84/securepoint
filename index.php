<html>
<head>
    <meta charset="utf-8">
    <html lang="en">
    <title>Seceruepoint recruitment excercise</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            color: #444;
        }
        table {
            border: 1px solid grey;
            border-collapse: collapse;
        }
        td, th {
            border: 1px solid grey;
            padding: 0.25rem;
            width: 300px;
        }
        .violation{
            color:red;
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

#####################################################
####################Question 1#######################
#####################################################

// Which serial_numbers are connecting to the server the most (10 DESC)

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
echo "<h3>Answer:</h3>";
echo "<table><tr><th>serial number</th><th>connection calls</th></tr>";

for($i=0; $i<10; $i++){
    echo "<tr>";
    echo "<td>{$multiple_licenses[$i]['serialnumber_license']}</td>";
    echo "<td>{$multiple_licenses[$i]['anzahl']}</td>";
    echo "</tr>";
}

echo "</table></div>";

#####################################################
####################Question 2#######################
#####################################################

// Which serialnumbers are violating the "1 device, 1 number" rule (the most, 10 DESC)

echo "<h2>Question 2: Describe how you identify a single device as such. Provide a way to identify licenses that are installed on more than one device. ";  echo "What are the 10 license serials that violoate this rule the most?</h2>";
echo "<h3>Answer:</h3>";
echo "<p>There are <b class='violation'>$violation_counter</b> rule violations.</p>";
echo "<table><tr><th>serial number</th><th>rule violations</th></tr>";

$stmt_violating_rule = "SELECT `serialnumber_license`, COUNT(DISTINCT `mac`) AS `anzahl` FROM `log_data` GROUP BY `serialnumber_license` HAVING `anzahl` > 1 ORDER BY `anzahl` DESC;";
$data_violating_rule = $db->query($stmt_violating_rule);
$violation_counter = 0;
$violation_array = [];
while($row = $data_violating_rule->fetch(PDO::FETCH_ASSOC)){
    $violation_counter++;
    array_push($violation_array, [
        'serialnumber_license' => $row['serialnumber_license'],
        'anzahl' => $row['anzahl']]
    );

};
for($i=0; $i<10; $i++){
    echo "<tr>";
    echo "<td>{$violation_array[$i]['serialnumber_license']}</td>";
    echo "<td>{$violation_array[$i]['anzahl']}</td>";
    echo "</tr>";
}

#####################################################
####################BONUS Question###################
#####################################################


?>

</body>
</html>

