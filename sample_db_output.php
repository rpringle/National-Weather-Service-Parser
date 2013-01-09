<?php

error_reporting(E_ALL);
ini_set("display_errors", 1);

	// Include the NWS DB Parser function
	include 'nws_db_weather_parser.php';
		
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>NWS Parser Sample Database Output</title>

<!-- Sample stylesheet included for testing purposes -->
<link rel="stylesheet" type="text/css" href="css/nws_sample.css" />

</head>

<body>

<?php

	// Run the function
	$xml = db_parse_weather($remote_feed, $db_info);
	
	// Check for errors first
	if (isset($xml['error']))
	{
		// Display generic error message
		echo '<p>Sorry, weather data not available at this time.</p>' . "\n";
		// Displaying the actual error message below is a bad idea because
		// it can reveal your directory/file structure to malicious users.
		// Unless you're in a test environment or trying to debug a permissions
		// issue, it is recommended that you leave the line below commented out. 
		echo '<p>' . $xml['error'] . '</p>' . "\n";
	}
	// Display the data
	else
	{
		// Extract array values as variables and assign
		// a prefix to them to avoid name collisions.
		extract($xml, EXTR_PREFIX_ALL, "nws");
		
		echo '<div id="nws-container">' . "\n";
		echo '<h1>Current Weather</h1>' . "\n";
		echo '<span class="primary">' . "\n";
		echo '<img src="' . $icons_path . replace_filename($nws_icon_url_name) . '" alt=" ">'  . "\n";
		// Casting temp_f as a float removes trailing zeros
		echo '<h2>' . (float)$nws_temp_f . '&#176; F</h2>' . "\n";
		echo '<p class="nws-description">' . $nws_weather . '</p>' . "\n";
		echo '</span>' . "\n";
		echo '<ul>' . "\n";
		// Some things are seasonal, only show them if they exist
		
		// If there's a windchill, display it
		if ($nws_windchill_f && $nws_windchill_f != '0.0')
		{
			echo '<li><strong>Wind Chill: </strong>' . $nws_windchill_f . '&#176; F</li>' . "\n";
		}
		// If there's a heat index, display it
		if ($nws_heat_index_f && $nws_heat_index_f != '0.0')
		{
			echo '<li><strong>Heat Index: </strong>' . $nws_heat_index_f . '&#176; F</li>' . "\n";
		}
		echo '<li><strong>Humidity: </strong>' . $nws_relative_humidity . '%</li>' . "\n";
		echo '<li><strong>Wind: </strong>' . $nws_wind_string . '</li>' . "\n";
		echo '<li><strong>Pressure: </strong>' . (float)$nws_pressure_in . ' In.</li>' . "\n";
		echo '<li><strong>Dewpoint: </strong>' . (float)$nws_dewpoint_f . '&#176; F</li>' . "\n";
		echo '</ul>' . "\n";
		echo '<p class="nws-centered nws-observation-time"><em>' . $nws_observation_time . '</em></p>' . "\n";
		// If there's a link to the full forecast, display it
		if (isset($forecast))
		{
			echo '<p class="nws-centered"><a href="' . $forecast . '">view forecast</a></p>' . "\n";
		}
		// If there's a link to a mobile forecast, display it
		if (isset($mobile_forecast))
		{
			echo '<p class="nws-centered"><a href="' . $mobile_forecast . '">view mobile forecast</a></p>' . "\n";
		}
	};

?>
</body>
</html>