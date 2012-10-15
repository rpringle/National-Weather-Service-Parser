<?php

/*

COPYRIGHT AND LICENSING NOTICE

Copyright 2012 Ron Pringle. All rights reserved.

Redistribution and use in source and binary forms, with or without modification, are
permitted provided that the following conditions are met:

   1. Redistributions of source code must retain the above copyright notice, this list of
      conditions and the following disclaimer.

   2. Redistributions in binary form must reproduce the above copyright notice, this list
      of conditions and the following disclaimer in the documentation and/or other materials
      provided with the distribution.

THIS SOFTWARE IS PROVIDED BY Ron Pringle ''AS IS'' AND ANY EXPRESS OR IMPLIED
WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND
FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL <COPYRIGHT HOLDER> OR
CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF
ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.


****************************************************
IMPORTANT! SEE THE README DOC FOR SETUP INSTRUCTIONS
****************************************************


*/

$remote_feed		= 'KBDU.xml';			// Replace with name of your chosen local feed's XML file
$icons_path			= 'images/weather/';	// Replace with path to local image directory
$forecast			= 'http://forecast.weather.gov/MapClick.php?CityName=Boulder&amp;state=CO&amp;site=BOU'; // Optional link for full forecast on NWS site.
$mobile_forecast	= 'http://mobile.weather.gov/index.php?lat=40.04&amp;lon=-105.23'; // Optional link to mobile version of full forecast on NWS


// Define MySQL connection info as an array

$db_info = array(
    "hostname" => "localhost",	// Your DB hostname or IP address
    "username" => "username",	// Database username
    "password" => "password",	// Database password
    "database" => "nws"			// Database name, defaults to "nws". If you change here, be sure to change in query as well.
);

/**
 * db_parse_weather function
 *
 * @category	XML Weather Widget
 * @author		Ron Pringle
 * @link		https://github.com/rpringle/National-Weather-Service-Parser
 */
 
function db_parse_weather($remote_feed, $db_info)
{
	// Open connection to database	
	$link = mysql_connect($db_info['hostname'],$db_info['username'],$db_info['password']);
	mysql_select_db($db_info['database']) or die("Unable to select database");
	
	// Retrieve difference in minutes between now and last record entered
	$qry = "SELECT TIMESTAMPDIFF(MINUTE, timestamp, NOW()) AS diff FROM nws ORDER BY nws_id DESC LIMIT 1";
	$result = mysql_query($qry);
	
	if (!$result)
	{
    	$error = 'Could not run query: ' . mysql_error();
	}
	else
	{
		$row = mysql_fetch_row($result);
		
		$diff = $row[0];
		
		// If greater than 1 hr (60 minutes) write new data to database
		if ($diff >= 60)
		{
			// Load XML data from National Weather Service
			$weather_url	= 'http://www.nws.noaa.gov/data/current_obs/' . $remote_feed;
			$weather_data	= file_get_contents($weather_url);
			
			// Check to see if the file loaded
			if ($weather_data === FALSE)
			{
				$error = "Sorry, the XML weather file failed to load.";
			}
			else
			{			
				// Load the XML weather data into a variable
				$xml = simplexml_load_string($weather_data);
				
				// Insert data into database
				$qry = "INSERT INTO nws (nws_id, location, station_id, latitude, longitude,
						observation_time, observation_time_rfc822, weather, temperature_string,
						temp_f, temp_c, relative_humidity, wind_string, wind_dir, wind_degrees,
						wind_mph, wind_gust_mph, wind_kt, wind_gust_kt, pressure_in, dewpoint_string,
						dewpoint_f, dewpoint_c, visibility_mi, icon_url_name, timestamp) 
	    				VALUES ('', '$xml->location', '$xml->station_id', '$xml->latitude', '$xml->longitude',
	    				'$xml->observation_time', '$xml->observation_time_rfc822', '$xml->weather',
	    				'$xml->temperature_string', '$xml->temp_f', '$xml->temp_c', '$xml->relative_humidity',
	    				'$xml->wind_string', '$xml->wind_dir', '$xml->wind_degrees', '$xml->wind_mph',
	    				'$xml->wind_gust_mph', '$xml->wind_kt', '$xml->wind_gust_kt', '$xml->pressure_in',
						'$xml->dewpoint_string', '$xml->dewpoint_f', '$xml->dewpoint_c', '$xml->visibility_mi',
						'$xml->icon_url_name', NOW())";
						
				mysql_query($qry);			
	    	}
		}
		
		// Load the last entry from the database
		// I highly recommend you replace the * with the specific values you wish to load.
		// In most cases, you're not going to use all the values passed from the NWS.
		$qry = "SELECT * FROM nws ORDER BY nws_id DESC LIMIT 1";
		$result = mysql_query($qry);
		
		// Check to see if query executed properly
		if (!$result)
		{
			$error = "Could not successfully run query ($qry) from DB: " . mysql_error();
		}
		// Check to see if any results were returned
		else if (mysql_num_rows($result) == 0)
		{
			$error = "Sorry, no rows were found.";
		}
		else
		{
			// Return the results as an associative array so that we can reference them by
			// name in the sample_output.php page.
			$xml = mysql_fetch_assoc($result);
		}	
	}

	// If there were no errors, load data
	if (!isset($error))
	{
		return $xml;
	}
	else
	{
		// Return errors
		return array("error" => $error);
	}
}

/* end of file nws_db_weather_parser.php */