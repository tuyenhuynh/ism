<?php

	require_once('../irrigationScheduler.class.php');
	if(isset($_SESSION['field']))
		$irrigationScheduler = new irrigationScheduler( $_SESSION['field']);
	else
		$irrigationScheduler = new irrigationScheduler( );
	
	if(!$irrigationScheduler->session->logged_in )
	{
		die('Please log in to http://weather.wsu.edu for access');
	}

	global $database;
	$stationID = -1;
	if(isset($_REQUEST['station']))
	{
		$stationID = mysql_real_escape_string($_REQUEST['station']);	
	}
	$weather_network = -1;
	if(isset($_REQUEST['weather_network']))
	{
		$weather_network = mysql_real_escape_string($_REQUEST['weather_network']);	
	}

	
	$query = "SELECT * FROM irrigation.table_station2region WHERE stationID = '$stationID' and weather_network = $weather_network";

	$retVal = "";
	$retVal .= " { ";
	$methodtext = "Unknown -- Error State 1";
	if($result = $database->query($query) )
	{
		if(true && mysql_num_rows($result) == 1)
		{
			$row = mysql_fetch_assoc($result);
			foreach($row as $key => $value)
			{
				$retVal .= "    \"".$key."\": \"".$value."\" , ";
			}
		}
		else
		{
			$retVal .= "    \"objid\": \"-2\", ";
		}
	}
	$retVal .= "    \"query\": \"$query\" ";
	$retVal .= " } ";
	$retVal .= "";

	echo $retVal;

?>