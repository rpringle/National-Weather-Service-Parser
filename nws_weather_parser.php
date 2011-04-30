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

 

