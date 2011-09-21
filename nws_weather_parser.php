<?php

/*

COPYRIGHT AND LICENSING NOTICE

Copyright 2011 City of Aurora, Illinois. All rights reserved.

Redistribution and use in source and binary forms, with or without modification, are
permitted provided that the following conditions are met:

   1. Redistributions of source code must retain the above copyright notice, this list of
      conditions and the following disclaimer.

   2. Redistributions in binary form must reproduce the above copyright notice, this list
      of conditions and the following disclaimer in the documentation and/or other materials
      provided with the distribution.

THIS SOFTWARE IS PROVIDED BY City of Aurora, Illinois ''AS IS'' AND ANY EXPRESS OR IMPLIED
WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND
FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL <COPYRIGHT HOLDER> OR
CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF
ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

The views and conclusions contained in the software and documentation are those of the
authors and should not be interpreted as representing official policies, either expressed
or implied, of the City of Aurora, Illinois.


****************************************************
IMPORTANT! SEE THE README DOC FOR SETUP INSTRUCTIONS
****************************************************


*/

$localfeed	 = '/clients/nwsparser/feeds/KARR.xml';	// Replace with whatever file name you want
$remotefeed	 = 'KARR.xml';			// Replace with name of your chosen local feed's XML file
$iconspath	 = '/images/weather/';	// Replace with path to local image directory

/**
 * parseWeather function
 *
 * @category	XML Weather Widget
 * @author		Ron Pringle
 * @link		https://github.com/rpringle/National-Weather-Service-Parser
 */
function parseWeather($localfeed, $remotefeed)
{

	$filename = $_SERVER['DOCUMENT_ROOT'] . $localfeed;
	$weatherurl = 'http://www.nws.noaa.gov/data/current_obs/' . $remotefeed;
	$weatherdata = file_get_contents($weatherurl);

	$xml = false;
		
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
				file_put_contents($filename,$weatherdata, LOCK_EX);
			}
		}
		$xml = simplexml_load_file($filename);
	}
	else
	{
		// File doesn't exist, get data and create new file
		file_put_contents($filename, $weatherdata);
		$xml = simplexml_load_file($filename);
	}
	// return false or xml data
	return $xml;
}
	// If no file and it could not be created
	// then no weather data is shown. Do nothing.

/* end of file nws_weather_parser.php */
