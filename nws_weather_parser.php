/*

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
or implied, of City of Aurora, Illinois.


*/

<?php

// Get Weather Routine //

$filename = $_SERVER['DOCUMENT_ROOT'] . '/feeds/KARR.xml';

$weatherurl = 'http://www.nws.noaa.gov/data/current_obs/KARR.xml';

$weatherdata = file_get_contents($weatherurl);

// check to see if the local file exists

if (file_exists($filename)) {

                // get difference in seconds between now and last modified date

                $diff = (time() - filemtime("$filename"))/60*60;

                // if greater than 1 hr (3600 seconds) get new file from source

                if ($diff >= 3600) {

                                // check to make sure file has write permissions

                                if(is_writable($filename)) {

                                                file_put_contents($filename,$weatherdata, LOCK_EX);

                                                }

                                };

                } else {

                                // file doesn't exist, get data and create new file

                                file_put_contents($filename,$weatherdata);

                                }

                //check again to be sure file exists

                if (file_exists($filename)) {

                                // write it out

                                $xml = simplexml_load_file($filename);

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

                                }

                // if no file and it could not be created

                // then no weather data is shown. Do nothing.

?> 

 

