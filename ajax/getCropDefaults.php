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
		$cropID = -1;
	if(isset($_REQUEST['crop']))
	{
		$cropID = mysql_real_escape_string($_REQUEST['crop']);	
	}

	
	$query = "SELECT * FROM ".TBL_CROPS." WHERE cropDefaultsID = $cropID";

	$retVal = "";
	$retVal .= " {   \"cropid\": \"".$cropID."\" , ";
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