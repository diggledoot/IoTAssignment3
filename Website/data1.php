<?php
$servername = "iot-db.cpylzoycvxza.ap-southeast-1.rds.amazonaws.com";
$username = "admin";
$password = "lab-password";
$dbname = "sensordb";
$conn = new mysqli($servername, $username, $password, $dbname);

if($conn){
	//Pi 1
	$sql = "SELECT * FROM sensordb WHERE thing_id='rpi_1' ORDER BY id DESC LIMIT 15";
	$query = mysqli_query($conn, $sql);
	$data = array();
	for ($x = 0; $x < mysqli_num_rows($query); $x++) {
	  $data[] = mysqli_fetch_assoc($query);
	}
	echo json_encode($data);
} else {
    echo "No connection!";
}

?>