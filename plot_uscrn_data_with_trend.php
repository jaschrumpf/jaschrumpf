<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$data = file_get_contents("https://www.ncei.noaa.gov/pub/data/uscrn/products/monthly01/");
$lines = explode("\n",$data);
print ("
<!DOCTYPE html>
 <HTML>
 <HEAD>
 <TITLE>Graphing Datasets</TITLE>
 <META charset=\"UTF-8\">
 <script src=\"https://cdn.plot.ly/plotly-2.8.3.min.js\"></script>
 <script>
 var station_ids = {
");
 
 
 $saved_state = "XX";
 foreach ($lines as $line) {
	 if ( strpos($line,"CRN")) {
		 $line_array = explode("\"", $line);
		 $state = substr($line_array[1],9,2);
		 if ($state != $saved_state) {
			 if ($saved_state != "XX") {
				$saved_state = $state;
				$array_builder = $array_builder . "],";
				print ("$array_builder\n");
				$array_builder = "\"$state\":[\"$line_array[1]\"";
			 } else {
					$saved_state =$state;
					$array_builder = "\"$state\":[\"$line_array[1]\"";
			 }
		 } else {
			 $array_builder = $array_builder . ", \"$line_array[1]\"";
		 }
	 }
 }
 print ("$array_builder]\n}\n");
 
 ?>
 

 function get_station_id(object) {
	 var state = object.value;
	 var idSel = document.getElementById("station_id");
	 idSel.length = 0;
	 for (var y in station_ids[state]) {
         idSel.options[idSel.options.length] = new Option(station_ids[state][y], station_ids[state][y]);
	 }
 }
 </script>
 <script>
 function get_station_data(id, plot_month) {
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
		var plotdiv = document.getElementById("plot");
		plotdiv.style.height = "500px";
    plotdiv.innerHTML = "<iframe width=800 height=500 frameborder=\"0\" src=get_station_data_with_trend.php?id="+ id + "&plot_month=" + plot_month + "\>"; //this.responseText;
    }
  };
  xhttp.open("GET", "get_station_data_with_trend.php?id="+ id + "&plot_month=" + plot_month, true);
  xhttp.send();
 }
 </script>
 
 </HEAD>
 <BODY>
 
  <h2 style="margin-left:auto;margin-right:auto">Plotting USCRN Monthly Summary data</h2>
 <br />
 <div id="parameters" style="float:left;width:425px;height:400px;padding:5px;border:1px solid black">
 <form  action="get_station_ids.php">
 <label for="state" >States</label>
 <select  name="state" id="state" onchange="get_station_id(this)">
<option value="XX" selected>[Select a state]</option>
<option value="AL">Alabama</option>
<option value="AK">Alaska</option>
<option value="AZ">Arizona</option>
<option value="AR">Arkansas</option>
<option value="CA">California</option>
<option value="CO">Colorado</option>
<option value="CT">Connecticut</option>
<option value="DE">Delaware</option>
<option value="FL">Florida</option>
<option value="GA">Georgia</option>
<option value="HI">Hawaii</option>
<option value="ID">Idaho</option>
<option value="IL">Illinois</option>
<option value="IN">Indiana</option>
<option value="IA">Iowa</option>
<option value="KS">Kansas</option>
<option value="KY">Kentucky</option>
<option value="LA">Louisiana</option>
<option value="ME">Maine</option>
<option value="MD">Maryland</option>
<option value="MA">Massachusetts</option>
<option value="MI">Michigan</option>
<option value="MN">Minnesota</option>
<option value="MS">Mississippi</option>
<option value="MO">Missouri</option>
<option value="MT">Montana</option>
<option value="NE">Nebraska</option>
<option value="NV">Nevada</option>
<option value="NH">New Hampshire</option>
<option value="NJ">New Jersey</option>
<option value="NM">New Mexico</option>
<option value="NY">New York</option>
<option value="NC">North Carolina</option>
<option value="ND">North Dakota</option>
<option value="OH">Ohio</option>
<option value="OK">Oklahoma</option>
<option value="OR">Oregon</option>
<option value="PA">Pennsylvania</option>
<option value="RI">Rhode Island</option>
<option value="SC">South Carolina</option>
<option value="SD">South Dakota</option>
<option value="TN">Tennessee</option>
<option value="TX">Texas</option>
<option value="UT">Utah</option>
<option value="VT">Vermont</option>
<option value="VA">Virginia</option>
<option value="WA">Washington</option>
<option value="WV">West Virginia</option>
<option value="WI">Wisconsin</option>
<option value="WY">Wyoming</option>
</select>
</form>
<br />
<br />
<form action="" name=station_id_form" id="station_id_form">
<label for="station_id" style="margin-top:150px" >Stations</label>
<select id="station_id" name="station_id">
<option value="" selected="selected">Please select state first</option>
</select>
<br />
<br />
<label for="month">Select month to plot</label>
<br />
<label for="jan">Jan</label>
<input type="radio" id="jan" name="plot_month" value="01">
<label for="feb">Feb</label>
<input type="radio" id="feb" name="plot_month" value="02">
<label for="mar">Mar</label>
<input type="radio" id="mar" name="plot_month" value="03">
<label for="apr">Apr</label>
<input type="radio" id="apr" name="plot_month" value="04">
<label for="may">May</label>
<input type="radio" id="may" name="plot_month" value="05">
<label for="jun">Jun</label>
<input type="radio" id="jun" name="plot_month" value="06">
<br />
<label for="jul">Jul</label>
<input type="radio" id="jul" name="plot_month" value="07">
<label for="aug">Aug</label>
<input type="radio" id="aug" name="plot_month" value="08">
<label for="sep">Sep</label>
<input type="radio" id="sep" name="plot_month" value="09">
<label for="oct">Oct</label>
<input type="radio" id="oct" name="plot_month" value="10">
<label for="nov">Nov</label>
<input type="radio" id="nov" name="plot_month" value="11">
<label for="dec">Dec</label>
<input type="radio" id="dec" name="plot_month" value="12">
<br /><br />
<button type="button" onclick="get_station_data(station_id.value, plot_month.value)">Submit</button>
<input type="reset">
</form>
<!--
<label for="station_id2" ">Station ID</label>
<input type="text" id="station_id2" size="11" name="station_id2">
<br />
<br />
<br />

<br />
<br />
-->
</div>
<div id="plot" style="margin-left:450px;border:1px solid red;width:800px;height:450px"></div>
 </body>
 </html>
 
