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

$local_feed			= 'feeds/KBDU.xml';		// Replace with whatever file name you want
$remote_feed		= 'KBDU.xml';			// Replace with name of your chosen local feed's XML file
$icons_path			= 'images/weather/';	// Replace with path to local image directory
$forecast			= 'http://forecast.weather.gov/MapClick.php?CityName=Boulder&state=CO&site=BOU'; // Optional link for full forecast on NWS site.
$mobile_forecast	= 'http://mobile.weather.gov/index.php?lat=40.04&lon=-105.23'; // Optional link to mobile version of full forecast on NWS

/**
 * parseWeather function
 *
 * @category	XML Weather Widget
 * @author		Ron Pringle
 * @link		https://github.com/rpringle/National-Weather-Service-Parser
 */
 
function parse_weather($local_feed, $remote_feed)
{

	$filename		= $local_feed;
	$weather_url	= 'http://www.nws.noaa.gov/data/current_obs/' . $remote_feed;
	$weather_data	= file_get_contents($weather_url);

	$xml = false;
	
	// Check permissions on directory
	if (is_writable(dirname($filename)))
	{			
		// Check to see if the local file exists
		if (file_exists($filename))
		{
			// Get difference in seconds between now and last modified date
			$diff = (time() - filemtime($filename)) / 60 * 60;
			// If greater than 1 hr (3600 seconds) get new file from source
			if ($diff >= 3600)
			{
				// Check to make sure file has write permissions
				if (is_writable($filename))
				{
					file_put_contents($filename,$weather_data, LOCK_EX);
				}
				else
				{
					// Log error if file isn't writable
					$error = "Sorry, can't write to file. Please check file permissions.";
				}
			}
		}
		else
		{
			// File doesn't exist, get data and create new file
			file_put_contents($filename, $weather_data);
		}
	}
	else
	{
		// Log error if directory isn't writable
		$error = "Sorry, can't write to directory. Please check directory permissions.";
	}
	
	// If there were no errors, load data
	if (!isset($error))
	{
		// Load the XML weather data into a variable and return the data
		$xml = simplexml_load_file($filename);
		return $xml;
	}
	else
	{
		// Return errors
		return $error;
	}
}

/* end of file nws_weather_parser.php */