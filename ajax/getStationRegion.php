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
	$networkID = -1;
	if(isset($_REQUEST['weather_network']))
	{
		$networkID = mysql_real_escape_string($_REQUEST['weather_network']);	
	}

	
	$query = "SELECT objid, regionID FROM irrigation.table_station2region ";
	$query .= "WHERE weather_network = $networkID and stationID = '$stationID' ";

	$retVal = "";
	$retVal .= " {   \"stationID\": \"".$stationID."\" , ";
	$methodtext = "Unknown -- Error State 1";
	if($result = $database->query($query)  )
	{
		if(mysql_num_rows($result) == 1)
		{
			$row = mysql_fetch_assoc($result);
			foreach($row as $key => $value)
			{
				$retVal .= "    \"".$key."\": \"".$value."\" , ";
			}
		}
		else
		{
			$retVal .= " \"objid\":\"-7\", ";
			$retVal .= " \"regionID\":\"720\", ";
		}
	}
	else
	{
		$retVal .= " \"objid\":\"-5\", ";
		$retVal .= " \"regionID\":\"720\", ";
	}
	$retVal .= "    \"query\": \"$query nr:". mysql_num_rows($result)."\" ";
	$retVal .= " } ";
	$retVal .= "";

	echo $retVal;

?>