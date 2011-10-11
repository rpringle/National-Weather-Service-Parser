<?php
// Error reporting; turn off for production
//error_reporting(E_ALL);

// Include the NWS Parser function
include 'nws_weather_parser.php';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>NWS Parser Sample Output</title>
</head>

<body>

<?php

// Run the function
$xml = parseWeather($localfeed, $remotefeed);
if ($xml) {
	echo '<h3>Current Weather</h3>';
	echo '<img src=' . $iconspath . $xml->icon_url_name . ' alt=" ">';
	echo '<h2>' . $xml->temp_f . '&#176; F</h2>';
	echo '<p>' . $xml->weather . '</p>';
	echo '<br/>';
	echo '<ul>';
	echo '<li><strong>Wind Chill (&#176;F): </strong>' . $xml->windchill_f . '</li>';
	echo '<li><strong>Heat Index (&#176;F): </strong>' . $xml->heat_index_f . '</li>';
	echo '<li><strong>Humidity: </strong>' . $xml->relative_humidity . '%</li>';
	echo '<li><strong>Wind: </strong>' . $xml->wind_string . '</li>';
	echo '<li><strong>Pressure: </strong>' . $xml->pressure_in . '</li>';
	echo '<li><strong>Dewpoint: </strong>' . $xml->dewpoint_f . '</li>';
	echo '</ul>';
	echo '<p><em>' . $xml->observation_time . '</em></p>';
	echo '<p><a href="http://www.crh.noaa.gov/forecast/MapClick.php?CityName=Aurora&amp;state=IL&amp;site=LOT">view forecast</a></p>';
} else {
	echo "Could not load weather data.";
}
?>
</body>
</html>