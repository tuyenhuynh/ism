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
	$fieldID = -1;
	if(isset($_REQUEST['basedonfield']))
	{
		$fieldID = mysql_real_escape_string($_REQUEST['basedonfield']);	
	}

	
	$query = "SELECT * FROM irrigation.tblfield WHERE fieldID = $fieldID";

	$retVal = "";
	$retVal .= " {   \"fieldID\": \"".$fieldID."\" , ";
	$methodtext = "Unknown -- Error State 1";
	if($result = $database->query($query))
	{
		$row = mysql_fetch_assoc($result);
		foreach($row as $key => $value)
		{
			$retVal .= "    \"".$key."\": \"".$value."\" , ";
		}
	}
	$retVal .= "    \"query\": \"$query\" ";
	$retVal .= " } ";
	$retVal .= "";

	echo $retVal;

?>