<?php

// Sample SQL statement that can be used to create the nws table in your database.

function create_table($db_info)
{
	// Open connection to database	
	$link = mysql_connect($db_info['hostname'],$db_info['username'],$db_info['password']);
	mysql_select_db($db_info['database']) or die("Unable to select database");
	
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
	}
};

?>