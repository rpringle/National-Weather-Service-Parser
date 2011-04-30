<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Untitled Document</title>
</head>

<body>
<div>
  <p>&lt;?php</p>
  <p>// Get Weather Routine //</p>
  <p>$filename = $_SERVER['DOCUMENT_ROOT'] . '/feeds/KARR.xml';</p>
  <p>$weatherurl = '<span id="OBJ_PREFIX_DWT41"><a target="_blank" href="http://www.nws.noaa.gov/data/current_obs/KARR.xml">http://www.nws.noaa.gov/data/current_obs/KARR.xml</a></span>';</p>
  <p>$weatherdata = file_get_contents($weatherurl);</p>
  <p>// check to see if the local file exists</p>
  <p>if (file_exists($filename)) {</p>
  <p>                // get difference in seconds between now and last modified date</p>
  <p>                $diff = (time() - filemtime(&quot;$filename&quot;))/60*60;</p>
  <p>                // if greater than 1 hr (3600 seconds) get new file from source</p>
  <p>                if ($diff &gt;= 3600) {</p>
  <p>                                // check to make sure file has write permissions</p>
  <p>                                if(is_writable($filename)) {</p>
  <p>                                                file_put_contents($filename,$weatherdata, LOCK_EX);</p>
  <p>                                                }</p>
  <p>                                };</p>
  <p>                } else {</p>
  <p>                                // file doesn't exist, get data and create new file</p>
  <p>                                file_put_contents($filename,$weatherdata);</p>
  <p>                                }</p>
  <p>                <span id="OBJ_PREFIX_DWT42"><a target="_blank" href="file:////check%20again%20to%20be%20sure%20file%20exists">//check again to be sure file exists</a></span></p>
  <p>                if (file_exists($filename)) {</p>
  <p>                                // write it out</p>
  <p>                                $xml = simplexml_load_file($filename);</p>
  <p>                                echo '&lt;h3 class=&quot;centertxt&quot;&gt;Current Weather&lt;/h3&gt;';</p>
  <p>                                echo '&lt;img class=&quot;floatleftnoclearsmallborder&quot; src=&quot;/images/weather/' . $xml-&gt;icon_url_name . '&quot; alt=&quot;&quot;&gt;';</p>
  <p>                                echo '&lt;h2&gt;' . $xml-&gt;temp_f . '&amp;#176; F&lt;/h2&gt;';</p>
  <p>                                echo '&lt;p&gt;' . $xml-&gt;weather . '&lt;/p&gt;';</p>
  <p>                                echo '&lt;br class=&quot;clear&quot; &gt;';</p>
  <p>                                echo '&lt;ul class=&quot;nobulletlist&quot;&gt;';</p>
  <p>                                echo '&lt;li&gt;&lt;strong&gt;Wind Chill (&amp;#176;F): &lt;/strong&gt;' . $xml-&gt;windchill_f . '&lt;/li&gt;';</p>
  <p>                                echo '&lt;li&gt;&lt;strong&gt;Heat Index (&amp;#176;F): &lt;/strong&gt;' . $xml-&gt;heat_index_f . '&lt;/li&gt;';</p>
  <p>                                echo '&lt;li&gt;&lt;strong&gt;Humidity: &lt;/strong&gt;' . $xml-&gt;relative_humidity . '%&lt;/li&gt;';</p>
  <p>                                echo '&lt;li&gt;&lt;strong&gt;Wind: &lt;/strong&gt;' . $xml-&gt;wind_string . '&lt;/li&gt;';</p>
  <p>                                echo '&lt;li&gt;&lt;strong&gt;Pressure: &lt;/strong&gt;' . $xml-&gt;pressure_in . '&lt;/li&gt;';</p>
  <p>                                echo '&lt;li&gt;&lt;strong&gt;Dewpoint: &lt;/strong&gt;' . $xml-&gt;dewpoint_f . '&lt;/li&gt;';</p>
  <p>                                echo '&lt;/ul&gt;';</p>
  <p>                                echo '&lt;p class=&quot;centertxt&quot;&gt;&lt;em&gt;' . $xml-&gt;observation_time . '&lt;/em&gt;&lt;/p&gt;';</p>
  <p>                                echo '&lt;p class=&quot;centertxt&quot;&gt;&lt;a href=&quot;<span id="OBJ_PREFIX_DWT43"><a target="_blank" href="http://www.crh.noaa.gov/forecast/MapClick.php?CityName=Aurora&amp;state=IL&amp;site=LOT">http://www.crh.noaa.gov/forecast/MapClick.php?CityName=Aurora&amp;amp;state=IL&amp;amp;site=LOT</a></span>&quot;&gt;view forecast&lt;/a&gt;&lt;/p&gt;';</p>
  <p>                                }</p>
  <p>                // if no file and it could not be created</p>
  <p>                // then no weather data is shown. Do nothing.</p>
  <p>?&gt; </p>
  <p> </p>
</div>
</body>
</html>