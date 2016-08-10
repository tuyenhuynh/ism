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
	
	$success = false;
	$results = array();

	$station = "-1";
	$weather_network = "-1";
	if(isset($_REQUEST['weather_network']))
	{
		$weather_network = mysql_real_escape_string($_REQUEST['weather_network']);
	}
	if(isset($_REQUEST['station']) && $_REQUEST['station'] <> -1 && $weather_network <> -1)
	{
		$station = mysql_real_escape_string($_REQUEST['station']);

		$regionID = 720;
		$query = "SELECT regionID from irrigation.table_station2region where weather_network = $weather_network and stationID = '$station' ";
		$regionResult = $database->query($query);
		if($regionRow = mysql_fetch_assoc($regionResult))
		{
			$regionID = $regionRow['regionID'];
		}
		if($weather_network == 620)
			$regionID = 620;

		$cropQuery = "SELECT cropDefaultsID, cropName from irrigation.tblcropdefaults where status = 1 and cropRegion = $regionID order by cropName ";
		$cropResult = $database->query($cropQuery);
		if(mysql_num_rows($cropResult) > 0)
		{
			$retVal = "[ {\"optionValue\":\"-1\", \"optionDisplay\": \"Select Crop\"} ";
			while($cropRow = mysql_fetch_assoc($cropResult))
			{
				$retVal .= ", {\"optionValue\":\"".$cropRow['cropDefaultsID']."\", \"optionDisplay\": \"".$cropRow['cropName']."\"}";
			}
		}
		else
		{
				$retVal = "[ {\"optionValue\":\"-1\", \"optionDisplay\": \"No Crops Found\"} ";
		}
	}
	else
	{
		$retVal = "[ {\"optionValue\":\"-1\", \"optionDisplay\": \"Select Station First\"} ";
	}
	$retVal .= "]";
	echo $retVal;

?>