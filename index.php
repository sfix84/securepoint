<html>
<head>
    <meta charset="utf-8">
    <html lang="en">
    <title>Securepoint recruitment excercise</title>
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

echo "<h2>Question 2: Describe how you identify a single device as such.Describe how you identify a single device as such. ";
echo "Provide a way to identify licenses that are installed on more than one device. ";  
echo "What are the 10 license serials that violoate this rule the most?</h2>";
echo "<h3>Answer:</h3>";
echo "<p>One can identify a single device by field 'mac' (address).</p>";
echo "<p>There are <b class='violation'>$violation_counter</b> rule violations.</p>";
echo "<table><tr><th>serial number</th><th>rule violations</th></tr>";


for($i=0; $i<10; $i++){
    echo "<tr>";
    echo "<td>{$violation_array[$i]['serialnumber_license']}</td>";
    echo "<td>{$violation_array[$i]['anzahl']}</td>";
    echo "</tr>";
}
echo "</table>";
echo "<br>";

#####################################################
####################BONUS Question###################
#####################################################
echo "<h2>Bonus Question (3): Based on the information given in the specs metadata, try to identify the different classes of hardware ";
echo "that are in use and provide the number of licenses that are active on these types of hardware.</h2>";
echo "<h3>Answer:</h3>";
echo "<p>Thinking: A combination of the fields: machine, mem, cpu should give a unique type of hardware.</p>";

$stmt_select_hardwaretypes = "SELECT machine, mem, cpu, COUNT(*) AS anzahl FROM log_data GROUP BY machine, mem, cpu ORDER BY anzahl DESC;";
$hardware_types = $db->query($stmt_select_hardwaretypes);
$type_number = 0;
$hardware_key_value_array = [];
while($row = $hardware_types->fetch(PDO::FETCH_ASSOC)){
    $type_number++;
    $hardware_type = $row['machine'] . $row['mem'] . $row['cpu'];
    $hardware_key_value_array[$hardware_type] = $type_number;
};


echo "<p>There are <b>$type_number</b> different classes of hardware.";


$stmt_license_on_hardware_type_query ="SELECT hardware_type, COUNT(DISTINCT serialnumber_license) AS licences FROM log_data WHERE hardware_type IS NOT NULL GROUP BY hardware_type ORDER BY hardware_type ASC;";
$license_on_hardware_type_array = $db->query($stmt_license_on_hardware_type_query);

echo "<table><tr><th>hardware type</th><th>amount of serial numbers</th>";

while($row = $license_on_hardware_type_array->fetch(PDO::FETCH_ASSOC)){
    echo "<tr><td>{$row['hardware_type']}</td>";
    echo "<td>{$row['licences']}</tr>";
}


?>

</body>
</html>

