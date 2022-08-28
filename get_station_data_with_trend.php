<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

print "<script src=\"https://cdn.plot.ly/plotly-2.8.3.min.js\"></script>\n";

if( isset($_GET['id']) )  {

    //be sure to validate and clean your variables
    $id = htmlentities($_GET['id']);
	$station_name = substr($id, 1, strlen($id)-5);
	$plot_month = $_GET['plot_month'];
//	print ("id = " . $id);
	$data = file_get_contents("https://www.ncei.noaa.gov/pub/data/uscrn/products/monthly01/" . $id);
	$lines = explode("\n",$data);

} else {
     print "You must select a country and station.";
	 exit;
}
    $x = "";
    $y = 0;
	$array_x = array();
	$array_y = array();
	$x_string = "";
	$y_string = "";
    $count = 0;
   $sum_x = 0;
    $sum_y = 0;
    $sum_xy = 0;
    $sum_xx = 0;

 foreach ($lines as $line) {
	 $year= substr($line, 6,4);
	 if ($year == "") {
		 continue;
	 }
	 $month = substr($line, 10, 2);
	 $temp = substr($line, 40, 8);
//	 $date = $year . "-" . $month . "-01";
     $date = $year . "-" . $month;
//	 print "<br />$year\t$month\t$temp\n";
//	 print("year = $year; month = $month\n");


	
	if ($plot_month == "01" ) {
		$month_name = "January";
	} elseif ($plot_month == "02") {
		$month_name = "February";
	} elseif ($plot_month == "03") {
		$month_name = "March";
	} elseif ($plot_month == "04") {
		$month_name = "April";
	} elseif ($plot_month == "05") {
		$month_name = "May";
	} elseif ($plot_month == "06") {
		$month_name = "June";
	} elseif ($plot_month == "07") {
		$month_name = "July";
	} elseif ($plot_month == "08") {
		$month_name = "August";
	} elseif ($plot_month == "09") {
		$month_name = "September";
	} elseif ($plot_month == "10") {
		$month_name = "October";
	} elseif ($plot_month == "11") {
		$month_name = "November";
	} elseif ($plot_month == "12") {
		$month_name = "December";
	}
		
	if ($month == $plot_month) {
	if ($temp == -9999.0) {
		continue;
	}
	$count++;
	$x = $year;
	$y = floatval($temp);
	if ($count ==1) {
		$x_string = "var xArray = [" . $x; 
		$y_string = "var yArray = [" . $y; 
		$x2_string = "var x2Array = [" . $count;
		$year_start = $year;
    } else {
		$x_string = $x_string . "," . $x; 
		$y_string = $y_string . "," . $y; 
		$x2_string = $x2_string . "," . $count;
	}	
	$sum_x = $sum_x + $x;
	$sum_y = $sum_y + $y;
	$sum_xx = $sum_xx + ($x*$x);
	$sum_xy = $sum_xy + ($x*$y);
				
	
	}
    $year_end = $year;
}
	$x_string = $x_string . "];";
	$y_string = $y_string . "];";
	$x2_string = $x2_string . "];";
print "<body onload=\"plot_data()\">\n";
print "<SCRIPT>\n";
print "function plot_data() { \n";
print "$x_string\n";
print "$y_string\n";
print "$x2_string\n";

print "\nconsole.log(\"x string = $x_string\");";
print "\nconsole.log(\"y string = $y_string\");";
print "\nconsole.log(\"x2 string = $x2_string\");";

print "\nconsole.log(\"sumX = $sum_x\");";	
print "\nconsole.log(\"sumY = $sum_y\");";	
print "\nconsole.log(\"sumXY = $sum_xy\");";	
print "\nconsole.log(\"sumXX = $sum_xx\");";	
print "\nconsole.log(\"count = $count\");";	

	$m = ($count*$sum_xy - $sum_x*$sum_y) / ($count*$sum_xx - $sum_x*$sum_x);
    $b = ($sum_y - ($m*$sum_x))/$count;
print "\nconsole.log(\"m = $m\");";
print "\nconsole.log(\"b = $b\");";

	$x_trend_min = $year_start;
	$x_trend_max = $year_end;
	print "\nvar trend_x = [" . $x_trend_min . "," . $x_trend_max . "];\n";
	$y_trend_start = $x_trend_min * $m + $b;
	$y_trend_end = $x_trend_max * $m + $b;
	print "var trend_y = [" . round($y_trend_start,1) . "," . round($y_trend_end, 1) . "];\n";
	
//	round all final results

	$b = round($b, 3);
	$m = round($m, 3);
	
//	$trend = ($y_trend_end - $y_trend_start)/($x_trend_max - $x_trend_min);
	$trend = round((($y_trend_end - $y_trend_start)/$count) * 10, 2);
	
  print "var slope = " . round($trend, 3) . ";
  var m = " . round($m, 3) . ";
  var b = " . round($b, 3) . ";
  var station_name = \"" . $station_name . "\";
  var sign = \"+\";
  if (b < 0) {
	  sign = \"\";
  }
var data = [{
  x: xArray,
  y: yArray,
  mode:\"lines\",
  type:\"scatter\"
}];

var data2 = [{
  x: trend_x,
  y: trend_y,
  mode:\"trend\",
  type:\"scatter\"
}];

// Define Layout

var layout = {
  title: 'Station $station_name<br />TMAX Average for $month_name',
  xaxis: {
    title: 'Year',
    showgrid: true,
    zeroline: true
  },
  yaxis: {
    title: 'Temp (deg. C)',
    showline: true
  }
};

var plotdata = [
  {x: xArray, y: yArray, mode:\"lines\", type:\"scatter\"},
  {x: trend_x, y: trend_y, mode:\"lines\", type: \"scatter\"}
];

Plotly.newPlot(\"plot\", plotdata, layout);

	document.getElementById(\"data\").innerHTML =\"". $x_string . "\" + \n\"<br />" . $y_string . "\";
}
</script>


<div id=\"plot\" style=\"height:350px;width:800px\"></div>
 <div id=\"data\" style=\"display:block;font-size:0.7em;padding:20px;height:50px;width:710px;border:1px solid green\"></div>

</body>";


 

?>

























