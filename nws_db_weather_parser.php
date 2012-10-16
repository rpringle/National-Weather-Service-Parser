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
    "database" => "nws",		// Database name
	"table"    => "test_nws"    // Table name
);

/**
 * nws_db_weather_parser
 *
 * @category	XML Weather Widget
 * @author		Ron Pringle
 * @link		https://github.com/rpringle/National-Weather-Service-Parser
 */

// Open connection to database 
function open_db_connection()
{
	global $db_info;
	
	// Connect to the DB with the supplied credentials	
	$link = mysql_connect($db_info['hostname'],$db_info['username'],$db_info['password']);
	if (!$link)
	{
		$error = "Unable to connect to database.";
	}
	// Select the database
	$db_select = mysql_select_db($db_info['database']);
	if (!$db_select)
	{
		$error = "Unable to select database";
	}
	if (isset($error))
	{
		return array("error" => $error);
	}
}


function db_parse_weather()
{
	global $remote_feed;
	global $db_info;
	
	// Open datbase connection
	open_db_connection($db_info);
	
	if (isset($link['error']))
	{
		$error = $link['error'];
		return array("error" => $error);
		break;
	}
	else
	{
		// Make sure table exists. If it doesn't, create it
		create_table();
		
		$table = $db_info['table'];
		
		// Retrieve difference in minutes between now and last record entered
		$qry = "SELECT TIMESTAMPDIFF(MINUTE, timestamp, NOW()) AS diff FROM $table ORDER BY nws_id DESC LIMIT 1";
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
				// Load new XML data from National Weather Service
				load_xml_data();
			}
			
			// Load the last entry from the database
			// I recommend you replace the * with the specific values you wish to load.
			// In most cases, you're not going to use all the values passed from the NWS.
			$qry = "SELECT * FROM $table ORDER BY nws_id DESC LIMIT 1";
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

// Create new table function
// Called from within db_parse_weather
function create_table()
{
	global $remote_feed;
	global $db_info;
	
	// Open datbase connection
	open_db_connection();
	
	// Assign table name to variable
	$table = $db_info['table'];
	
	// Check to see if table already exists
	$qry = "SHOW TABLES LIKE '$table'";
	$check_table = mysql_query($qry);
	$table_exists = mysql_num_rows($check_table) > 0 ? 'true' : 'false';
	
	// Create the table if it doesn't already exist
	if ($table_exists == 'false')
	{
		// Prepare the query
		$qry = "CREATE TABLE $table (
		  nws_id int(11) NOT NULL auto_increment,
		  location varchar(255) default NULL,
		  station_id varchar(4) default NULL,
		  latitude double(10,6) default NULL,
		  longitude double(10,6) default NULL,
		  elevation varchar(45) default NULL,
		  observation_time varchar(255) default NULL,
		  observation_time_rfc822 varchar(255) default NULL,
		  weather varchar(255) default NULL,
		  temperature_string varchar(25) default NULL,
		  temp_f decimal(4,1) default NULL,
		  temp_c decimal(4,1) default NULL,
		  relative_humidity tinyint(4) default NULL,
		  wind_string varchar(45) default NULL,
		  wind_dir varchar(45) default NULL,
		  wind_degrees int(3) default NULL,
		  wind_mph decimal(4,1) default NULL,
		  wind_gust_mph decimal(4,1) default NULL,
		  wind_kt int(3) default NULL,
		  wind_gust_kt int(3) default NULL,
		  pressure_in decimal(5,2) default NULL,
		  dewpoint_string varchar(45) default NULL,
		  dewpoint_f decimal(3,1) default NULL,
		  dewpoint_c decimal(3,1) default NULL,
		  heat_index_string varchar(45) default NULL,
		  heat_index_f decimal(3,1) default NULL,
		  heat_index_c decimal(3,1) default NULL,
		  windchill_string varchar(45) default NULL,
		  windchill_f decimal(3,1) default NULL,
		  windchill_c decimal(3,1) default NULL,
		  visibility_mi decimal(5,2) default NULL,
		  icon_url_name varchar(255) default NULL,
		  timestamp timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
		  PRIMARY KEY  (nws_id)
		) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8";
	
		// Execute the query
		$result = mysql_query($qry);
		
		// Check for errors
		if (!$result)
		{
			// If the query didn't run, return an error
	    	$error = 'Could not run query: ' . mysql_error();
	    	// Return errors
			return array("error" => $error);
		}
		else
		{
			// Load data for the first time
			load_xml_data();
		}
	}
}


// Load XML data function
function load_xml_data()
{
	global $remote_feed;
	global $db_info;
	
	// Open datbase connection
	open_db_connection();
	
	// Assign table name to variable
	$table = $db_info['table'];
	
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
	$qry = "INSERT INTO $table (nws_id, location, station_id, latitude, longitude,
			elevation, observation_time, observation_time_rfc822, weather, temperature_string,
			temp_f, temp_c, relative_humidity, wind_string, wind_dir, wind_degrees,
			wind_mph, wind_gust_mph, wind_kt, wind_gust_kt, pressure_in, dewpoint_string,
			dewpoint_f, dewpoint_c, heat_index_string, heat_index_f, heat_index_c,
			windchill_string, windchill_f, windchill_c, visibility_mi, icon_url_name, timestamp) 
			VALUES ('', '$xml->location', '$xml->station_id', '$xml->latitude', '$xml->longitude',
			'$xml->elevation', '$xml->observation_time', '$xml->observation_time_rfc822', '$xml->weather',
			'$xml->temperature_string', '$xml->temp_f', '$xml->temp_c', '$xml->relative_humidity',
			'$xml->wind_string', '$xml->wind_dir', '$xml->wind_degrees', '$xml->wind_mph',
			'$xml->wind_gust_mph', '$xml->wind_kt', '$xml->wind_gust_kt', '$xml->pressure_in',
			'$xml->dewpoint_string', '$xml->dewpoint_f', '$xml->dewpoint_c',
			'$xml->heat_index_string', '$xml->heat_index_f', '$xml->heat_index_c',
			'$xml->windchill_string', '$xml->windchill_f', '$xml->windchill_c', '$xml->visibility_mi',
			'$xml->icon_url_name', NOW())";
					
			mysql_query($qry);			
	}
}

// A function to convert weather icon filenames from .png to .jpg
function replace_filename($filename)
{
	$icon = substr_replace($filename, 'jpg', -3, 3);
	return $icon;
}

/* end of file nws_db_weather_parser.php */