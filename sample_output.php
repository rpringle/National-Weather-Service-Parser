<?php
// Error reporting; turn off for production
// ini_set("display_errors", true);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>NWS Parser Sample Output</title>
</head>

<body>

<?php
// Include the NWS Parser function
include 'nws_weather_parser.php';

// Run the function
parseWeather($localfeed, $remotefeed);

echo '<h3 class="centertxt">Current Weather</h3>';
		echo '<img class="floatleftnoclearsmallborder" src="/images/weather/' . $xml->icon_url_name . '" alt="">';
		echo '<h2>' . $xml->temp_f . '&#176; F</h2>';
		echo '<p>' . $xml->weather . '</p>';
		echo '<br class="clear" >';
		echo '<ul class="nobulletlist">';
		echo '<li><strong>Wind Chill (&#176;F): </strong>' . $xml->windchill_f . '</li>';
		echo '<li><strong>Heat Index (&#176;F): </strong>' . $xml->heat_index_f . '</li>';
		echo '<li><strong>Humidity: </strong>' . $xml->relative_humidity . '%</li>';
		echo '<li><strong>Wind: </strong>' . $xml->wind_string . '</li>';
		echo '<li><strong>Pressure: </strong>' . $xml->pressure_in . '</li>';
		echo '<li><strong>Dewpoint: </strong>' . $xml->dewpoint_f . '</li>';
		echo '</ul>';
		echo '<p class="centertxt"><em>' . $xml->observation_time . '</em></p>';
		echo '<p class="centertxt"><a href="http://www.crh.noaa.gov/forecast/MapClick.php?CityName=Aurora&amp;state=IL&amp;site=LOT">view forecast</a></p>';


?>
</body>
</html>