<?php
$servername = "iot-db.cpylzoycvxza.ap-southeast-1.rds.amazonaws.com";
$username = "admin";
$password = "lab-password";
$dbname = "sensordb";

$conn = new mysqli($servername, $username, $password, $dbname);

//Pi 3
if($conn){
$query1 = "SELECT 
			AVG(temp) AS `AvgTemp` , 
			AVG(light) AS `AvgLight` ,
			MAX(temp) AS `HighTemp`,
			MIN(temp) AS `LowTemp` ,			
			MAX(light) AS `HighLight` ,  
			MIN(light) AS `LowLight` 
			FROM sensordb WHERE thing_id='rpi_3' ORDER BY id DESC LIMIT 15"; 
			
$filter_data1 = mysqli_query($conn, $query1);
$row1 = mysqli_fetch_assoc( $filter_data1 );
echo json_encode($row1);
}else{
	echo json_encode("No data");
}

?>