<?php

$servername = "iot-db.cpylzoycvxza.ap-southeast-1.rds.amazonaws.com";
$username = "admin";
$password = "lab-password";
$dbname = "sensordb";

$conn = new mysqli($servername, $username, $password, $dbname);

?>
<!DOCTYPE html>
<html>
	<head>
		<title>Temp Light Read</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">

	</head>
<body>
  
<h1 class="text-center">Temperature & Light Reading for 3 Pi</h1>

<hr/>  
<!-------------------- Pi 1 -------------------->


    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1>Pi 1 Chart</h1>
                <canvas id="myChart_1" height="80"></canvas>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-12">
                <!--Add min max mean here, update every second also-->
                <h1>Analytics Table for Pi 1</h1>
                <sub>*Data from newest 15 rows</sub>
                <table class="table">
                    <thead class="table-danger">
                        <tr>
                            <th>Temperature Average (°c)</th>
                            <th>Light Level Average</th>
                            <th>Temperature Max (°c)</th>
                            <th>Light Level Max</th>
                            <th>Temperature Min (°c)</th>
                            <th>Light Level Min</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="text-center">
                            <td id="AvgTemp_1" class="table-info"></td>
                            <td id="AvgLight_1" class="table-info"></td>
                            <td id="HighTemp_1" class="table-info"></td>
                            <td id="LowTemp_1" class="table-info"></td>
                            <td id="HighLight_1" class="table-info"></td>
                            <td id="LowLight_1" class="table-info"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
		<div class="row mt-3">
            <div class="col-md-12">
                <!--Add min max mean here, update every second also-->
                <h1>State of Aircon and Curtain</h1>
                <table class="table">
                    <thead class="table-danger">
                        <tr>
                            <th class="text-center">Aircon</th>
                            <th class="text-center">Curtain</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="text-center">
                            <td id="aircon_1" class="table-info"></td>
                            <td id="curtain_1" class="table-info"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
	
		<script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
		<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
		<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
        $(document).ready(() => {
            var labels = [];
            var temp = [];
            var light = [];
            $.ajax({
                url: "data1.php",
                type: "get",
                dataType: "json",
                success: function(res) {
                    $.each(res, (i, item) => {
                        var timestamp = item.timestamp
                        timestamp = timestamp.split(" ");
                        labels.push(timestamp[1]);
                        temp.push(item.temp);
                        light.push(item.light);
                    })
                    var ctx = $('#myChart_1');
                    var start = 7;
                    var myChart = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Temperature',
                                data: temp,
                                fill: false,
                                borderColor: 'rgb(255, 0, 0)',
                                tension: 0.1
                            }, {
                                label: 'Light Level',
                                data: light,
                                fill: false,
                                borderColor: 'rgb(255, 255, 0)',
                                tension: 0.1
                            }]
                        }
                    });

                    setInterval(() => {
                        // console.log("Chart called!");
                        $.ajax({
                            url: "data1.php",
                            type: "get",
                            dataType: "json",
                            success: function(res) {
                                var labels = [];
                                var temp = [];
                                var light = [];
                                $.each(res, (i, item) => {
                                    var timestamp = item.timestamp
                                    timestamp = timestamp.split(" ");
                                    labels.push(timestamp[1]);
                                    temp.push(item.temp);
                                    light.push(item.light);
                                })
                                myChart.data.labels = labels;
                                myChart.data.datasets[0].data = temp;
                                myChart.data.datasets[1].data = light;

                                myChart.update();
                            }
                        })
                    }, 2000);
					
					
                }
            });
			
			 $.ajax({
                url: "data1_analysis.php",
                type: "get",
                dataType: "json",
                success: (res) => {
                    if (res == "No data") {
                        alert("No data returned!");
                    } else {
                        $('#AvgTemp_1').html(parseFloat(res.AvgTemp));
                        $('#AvgLight_1').html(parseFloat(res.AvgLight));
                        $('#HighTemp_1').html(res.HighTemp);
                        $('#LowTemp_1').html(res.LowTemp);
                        $('#HighLight_1').html(res.HighLight);
                        $('#LowLight_1').html(res.LowLight);

                        if(res.AvgTemp >= 37){
                          $('#aircon_1').html("Aircon is on!");
                        }else{
                          $('#aircon_1').html("Aircon is off!");
                        }
                        
                        console.log(res.AvgLight);
                        if(res.AvgLight <= 150){
                          $('#curtain_1').html("Curtains are drawn open!");
                        }else{
                          $('#curtain_1').html("Curtains are drawn closed!");
                        }
					
                        setInterval(() => {
                            // console.log("Analysis called!");
                            $.ajax({
                                url: "data1_analysis.php",
                                type: "get",
                                dataType: "json",
                                success: (res) => {
                                    if (res == "No data") {
                                        alert("No data returned!");
                                    } else {
                  										$('#AvgTemp_1').html(parseFloat(res.AvgTemp));
                  										$('#AvgLight_1').html(parseFloat(res.AvgLight));
                  										$('#HighTemp_1').html(res.HighTemp);
                  										$('#LowTemp_1').html(res.LowTemp);
                  										$('#HighLight_1').html(res.HighLight);
                  										$('#LowLight_1').html(res.LowLight);

                                      if(res.AvgTemp >= 37){
                                        $('#aircon_1').html("Aircon is on!");
                                      }else{
                                        $('#aircon_1').html("Aircon is off!");
                                      }
                                      
                                      if(res.AvgLight <=150){
                                        $('#curtain_1').html("Curtains are drawn open!");
                                      }else{
                                        $('#curtain_1').html("Curtains are drawn closed!");
                                      }
                                    }
									
                                }
                            })
                        }, 2000);
                    }
                }
            });
        })
</script>
<!-------------------- End -------------------->

<hr/>

<!-------------------- Pi 2 -------------------->
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1>Pi 2 Chart</h1>
                <canvas id="myChart_2" height="80"></canvas>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-12">
                <!--Add min max mean here, update every second also-->
                <h1>Analytics Table for Pi 2</h1>
                <sub>*Data from newest 15 rows</sub>
                <table class="table">
                    <thead>
                        <tr class="table-success">
                            <th>Temperature Average (°c)</th>
                            <th>Light Level Average</th>
                            <th>Temperature Max (°c)</th>
                            <th>Temperature Min (°c)</th>
                            <th>Light Level Max</th>
                            <th>Light Level Min</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="text-center">
                            <td id="AvgTemp_2" class="table-info"></td>
                            <td id="AvgLight_2" class="table-info"></td>
                            <td id="HighTemp_2" class="table-info"></td>
                            <td id="LowTemp_2" class="table-info"></td>
                            <td id="HighLight_2" class="table-info"></td>
                            <td id="LowLight_2" class="table-info"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
		<div class="row mt-3">
            <div class="col-md-12">
                <!--Add min max mean here, update every second also-->
                <h1>State of Aircon and Curtain</h1>
                <table class="table">
                    <thead class="table-success">
                        <tr>
                            <th class="text-center">Aircon</th>
                            <th class="text-center">Curtain</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="text-center">
                            <td id="aircon_2" class="table-info"></td>
                            <td id="curtain_2" class="table-info"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
	
		<script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
		<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
		<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
	
<script>
        $(document).ready(() => {
            var labels = [];
            var temp = [];
            var light = [];
            $.ajax({
                url: "data2.php",
                type: "get",
                dataType: "json",
                success: function(res) {
                    $.each(res, (i, item) => {
                        var timestamp = item.timestamp
                        timestamp = timestamp.split(" ");
                        labels.push(timestamp[1]);
                        temp.push(item.temp);
                        light.push(item.light);
                    })
                    var ctx = $('#myChart_2');
                    var start = 7;
                    var myChart = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Temperature',
                                data: temp,
                                fill: false,
                                borderColor: 'rgb(255, 0, 0)',
                                tension: 0.1
                            }, {
                                label: 'Light Level',
                                data: light,
                                fill: false,
                                borderColor: 'rgb(255, 255, 0)',
                                tension: 0.1
                            }]
                        }
                    });

                    setInterval(() => {
                        // console.log("Chart called!");
                        $.ajax({
                            url: "data2.php",
                            type: "get",
                            dataType: "json",
                            success: function(res) {
                                var labels = [];
                                var temp = [];
                                var light = [];
                                $.each(res, (i, item) => {
                                    var timestamp = item.timestamp
                                    timestamp = timestamp.split(" ");
                                    labels.push(timestamp[1]);
                                    temp.push(item.temp);
                                    light.push(item.light);
                                })
                                myChart.data.labels = labels;
                                myChart.data.datasets[0].data = temp;
                                myChart.data.datasets[1].data = light;

                                myChart.update();
                            }
                        })
                    }, 2000);
                }
            });
			
			 $.ajax({
                url: "data2_analysis.php",
                type: "get",
                dataType: "json",
                success: (res) => {
                    if (res == "No data") {
                        alert("No data returned!");
                    } else {
            					$('#AvgTemp_2').html(parseFloat(res.AvgTemp));
            					$('#AvgLight_2').html(parseFloat(res.AvgLight));
            					$('#HighTemp_2').html(res.HighTemp);
            					$('#LowTemp_2').html(res.LowTemp);
            					$('#HighLight_2').html(res.HighLight);
            					$('#LowLight_2').html(res.LowLight);
                                                          
                      if(res.AvgTemp >= 37){
                        $('#aircon_2').html("Aircon is on!");
                      }else{
                        $('#aircon_2').html("Aircon is off!");
                      }
                      
                      if(res.AvgLight <= 150){
                        $('#curtain_2').html("Curtains are drawn open!");
                      }else{
                        $('#curtain_2').html("Curtains are drawn closed!");
                      }

                        setInterval(() => {
                            // console.log("Analysis called!");
                            $.ajax({
                                url: "data2_analysis.php",
                                type: "get",
                                dataType: "json",
                                success: (res) => {
                                    if (res == "No data") {
                                        alert("No data returned!");
                                    } else {
                  										$('#AvgTemp_2').html(parseFloat(res.AvgTemp));
                  										$('#AvgLight_2').html(parseFloat(res.AvgLight));
                  										$('#HighTemp_2').html(res.HighTemp);
                  										$('#LowTemp_2').html(res.LowTemp);
                  										$('#HighLight_2').html(res.HighLight);
                  										$('#LowLight_2').html(res.LowLight);
                                                                                                                                                                                     if(res.AvgTemp >= 37){
                                        $('#aircon_2').html("Aircon is on!");
                                      }else{
                                        $('#aircon_2').html("Aircon is off!");
                                      }
                                      
                                      if(res.AvgLight <= 150){
                                        $('#curtain_2').html("Curtains are drawn open!");
                                      }else{
                                        $('#curtain_2').html("Curtains are drawn closed!");
                                      }
                                    }
                                }
                            })
                        }, 2000);
                    }
                }
            });
        })
</script>

<!-------------------- End -------------------->

<hr/>

<!-------------------- Pi 3 -------------------->
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1>Pi 3 Chart</h1>
                <canvas id="myChart_3" height="80"></canvas>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-12">
                <!--Add min max mean here, update every second also-->
                <h1>Analytics Table for Pi 3</h1>
                <sub>*Data from newest 15 rows</sub>
                <table class="table">
                    <thead>
                        <tr class="table-warning">
                            <th>Temperature Average (°c)</th>
                            <th>Light Level Average</th>
                            <th>Temperature Max (°c)</th>
                            <th>Temperature Min (°c)</th>
                            <th>Light Level Max</th>
                            <th>Light Level Min</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="text-center">
                            <td id="AvgTemp_3" class="table-info"></td>
                            <td id="AvgLight_3" class="table-info"></td>
                            <td id="HighTemp_3" class="table-info"></td>
                            <td id="LowTemp_3" class="table-info"></td>
                            <td id="HighLight_3" class="table-info"></td>
                            <td id="LowLight_3" class="table-info"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
		<div class="row mt-3">
            <div class="col-md-12">
                <!--Add min max mean here, update every second also-->
                <h1>State of Aircon and Curtain</h1>
                <table class="table">
                    <thead class="table-warning">
                        <tr>
                            <th class="text-center">Aircon</th>
                            <th class="text-center">Curtain</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="text-center">
                            <td id="aircon_3" class="table-info"></td>
                            <td id="curtain_3" class="table-info"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
	
		<script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
		<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
		<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
	
<script>
        $(document).ready(() => {
            var labels = [];
            var temp = [];
            var light = [];
            $.ajax({
                url: "data3.php",
                type: "get",
                dataType: "json",
                success: function(res) {
                    $.each(res, (i, item) => {
                        var timestamp = item.timestamp
                        timestamp = timestamp.split(" ");
                        labels.push(timestamp[1]);
                        temp.push(item.temp);
                        light.push(item.light);
                    })
                    var ctx = $('#myChart_3');
                    var start = 7;
                    var myChart = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Temperature',
                                data: temp,
                                fill: false,
                                borderColor: 'rgb(255, 0, 0)',
                                tension: 0.1
                            }, {
                                label: 'Light Level',
                                data: light,
                                fill: false,
                                borderColor: 'rgb(255, 255, 0)',
                                tension: 0.1
                            }]
                        }
                    });

                    setInterval(() => {
                        // console.log("Chart called!");
                        $.ajax({
                            url: "data3.php",
                            type: "get",
                            dataType: "json",
                            success: function(res) {
                                var labels = [];
                                var temp = [];
                                var light = [];
                                $.each(res, (i, item) => {
                                    var timestamp = item.timestamp
                                    timestamp = timestamp.split(" ");
                                    labels.push(timestamp[1]);
                                    temp.push(item.temp);
                                    light.push(item.light);
                                })
                                myChart.data.labels = labels;
                                myChart.data.datasets[0].data = temp;
                                myChart.data.datasets[1].data = light;

                                myChart.update();
                            }
                        })
                    }, 2000);
                }
            });
			
			 $.ajax({
                url: "data3_analysis.php",
                type: "get",
                dataType: "json",
                success: (res) => {
                    if (res == "No data") {
                        alert("No data returned!");
                    } else {
              					$('#AvgTemp_3').html(parseFloat(res.AvgTemp));
              					$('#AvgLight_3').html(parseFloat(res.AvgLight));
              					$('#HighTemp_3').html(res.HighTemp);
              					$('#LowTemp_3').html(res.LowTemp);
              					$('#HighLight_3').html(res.HighLight);
              					$('#LowLight_3').html(res.LowLight);
                                                            
                        if(res.AvgTemp >= 37){
                          $('#aircon_3').html("Aircon is on!");
                        }else{
                          $('#aircon_3').html("Aircon is off!");
                        }
                        
                        if(res.AvgLight <= 150){
                          $('#curtain_3').html("Curtains are drawn open!");
                        }else{
                          $('#curtain_3').html("Curtains are drawn closed!");
                        }

                        setInterval(() => {
                            // console.log("Analysis called!");
                            $.ajax({
                                url: "data3_analysis.php",
                                type: "get",
                                dataType: "json",
                                success: (res) => {
                                    if (res == "No data") {
                                        alert("No data returned!");
                                    } else {
                  										$('#AvgTemp_3').html(parseFloat(res.AvgTemp));
                  										$('#AvgLight_3').html(parseFloat(res.AvgLight));
                  										$('#HighTemp_3').html(res.HighTemp);
                  										$('#LowTemp_3').html(res.LowTemp);
                  										$('#HighLight_3').html(res.HighLight);
                  										$('#LowLight_3').html(res.LowLight);
                                                                                                                                                                                     if(res.AvgTemp >= 37){
                                        $('#aircon_3').html("Aircon is on!");
                                      }else{
                                        $('#aircon_3').html("Aircon is off!");
                                      }
                                      
                                      if(res.AvgLight <= 150){
                                        $('#curtain_3').html("Curtains are drawn open!");
                                      }else{
                                        $('#curtain_3').html("Curtains are drawn closed!");
                                      }
                                    }
                                }
                            })
                        }, 2000);
                    }
                }
            });
        })
</script>
<!-------------------- End -------------------->

</body>

</html>