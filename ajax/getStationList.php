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

	$retVal = "[ {\"optionValue\":\"-1\", \"optionDisplay\": \"Select Station\"} ";
	$weatherNetwork = "-1";
	$theYear = "";
	if(isset($_REQUEST['year']) && intval(mysql_real_escape_string($_REQUEST['year'])) >= 1900)
	{
		$theYear = intval(mysql_real_escape_string($_REQUEST['year']));
	}
	if(isset($_REQUEST['weather_network']))
	{
		$weatherNetwork = mysql_real_escape_string($_REQUEST['weather_network']);
		switch($weatherNetwork)
		{
			case "20":
				$stations = $database->getAllStations($theYear);
				while($row = mysql_fetch_assoc($stations))
				{
					$retVal .= ", {\"optionValue\":\"".$row['UNIT_ID']."\", \"optionDisplay\": \"".$row['STATION_NAME']."\"}";
				}
			break;
			case "120":
			  $AgriMetQuery = "select * from irrigation.agrimetstations order by state, name";
			  $AgriMetResults = $database->query($AgriMetQuery);
			  while($AgriMetRow = mysql_fetch_assoc($AgriMetResults))
			  {
					$retVal .= ", {\"optionValue\":\"".$AgriMetRow['StnID']."\", \"optionDisplay\": \"".$AgriMetRow['Name'].", ".$AgriMetRow['State']."\"}";
			  }
			break;
			case "220":
				$URL = "http://ccc.atmos.colostate.edu/~coagmet/station_index.php";
				
				$dom = new DOMDocument;
				if(@$dom->loadHTMLFile($URL))
				{
					$tables = $dom->getElementsByTagName('table');
					$process=false;
					foreach($tables as $table)
					{
						if(substr($table->childNodes->item(0)->nodeValue,0,2) == 'ID')
						{
							$process = true;
						}
						else
						{
							$process = false;
						}
						if($process)
						{
							foreach($table->childNodes as $childNode)
							{
								$someArray = explode(PHP_EOL,$childNode->nodeValue);
								$valueCount = 0;
								$id = "";
								foreach($someArray as $key => $value)
								{
									if(strlen($value) > 0 && $someArray[0] <> "ID")
									{
										switch($valueCount)
										{
											case 0:
												$id = $value;
												$results[$id]['ID'] = $id;
											break;
											case 1:
												$extraString = "";
												if($value == 'Yuma')
													$extraString = " ($id)";
												$results[$id]['NAME'] = $value.$extraString;
											break;
											case 2:
												$results[$id]['LOCATION'] = $value;
											break;
											case 3:
												$results[$id]['LAT'] = $value;
											break;
											case 4:
												$results[$id]['LNG'] = $value;
											break;
											case 5:
												$results[$id]['ELE'] = $value;
											break;
											case 6:
												$results[$id]['INSTALL'] = $value;
											break;
											case 7:
												$results[$id]['THROUGH'] = $value;
											break;
										}
										$valueCount++;
									}
								}
							}
						}
					}
					if(isset($results))
					foreach($results as $key=> $value)
					{
						$retVal .= ", {\"optionValue\":\"".$value['ID']."\", \"optionDisplay\": \"".$value['NAME']."\"}";
					}
				}
			break;
			case "320":
			  $AgriMetQuery = "select * from irrigation.table_azmet_stations order by station_name";
			  $AgriMetResults = $database->query($AgriMetQuery);
			  while($AgriMetRow = mysql_fetch_assoc($AgriMetResults))
			  {
					$retVal .= ", {\"optionValue\":\"".$AgriMetRow['unit_id']."\", \"optionDisplay\": \"".$AgriMetRow['station_name']."\"}";
			  }
			break;
			case "420":
			  $AgriMetQuery = "select * from irrigation.table_ndawn_stations order by station_name";
			  $AgriMetResults = $database->query($AgriMetQuery);
			  while($AgriMetRow = mysql_fetch_assoc($AgriMetResults))
			  {
					$retVal .= ", {\"optionValue\":\"".$AgriMetRow['unit_id']."\", \"optionDisplay\": \"".$AgriMetRow['station_name']."\"}";
			  }
			break;
			case "520":  //No installation dates available currently
			  $AgriMetQuery = "select * from irrigation.table_awdn_stations order by station_name";
			  $AgriMetResults = $database->query($AgriMetQuery);
			  while($AgriMetRow = mysql_fetch_assoc($AgriMetResults))
			  {
					$retVal .= ", {\"optionValue\":\"".$AgriMetRow['unit_id']."\", \"optionDisplay\": \"".$AgriMetRow['station_name']."\"}";
			  }
			break;
			case "620":  //Can we get installation date?
				$URLFile = "http://et.water.ca.gov/api/station";
				$lines = file ( $URLFile );
				$obj = json_decode($lines[0]);
				foreach($obj->{'Stations'} as $station)
				{
					$pos = strpos($station->{'HmsLatitude'},"/");
					$lat = trim(substr($station->{'HmsLatitude'},$pos+1));
					$pos = strpos($station->{'HmsLongitude'},"/");
					$lng = trim(substr($station->{'HmsLongitude'},$pos+1));
					$ele = $station->{'Elevation'};
					$disconnect = $station->{'DisconnectDate'};
					if(date("Y-m-d") < date("Y-m-d",strtotime($disconnect)))
					{
						$retVal .= ", {\"optionValue\":\"".$station->{'StationNbr'}."\", \"optionDisplay\": \"".$station->{'Name'}."\"}";
					}
				}

			break;
			case "720":
			  $AgriMetQuery = "select * from irrigation.mtagrimetstations order by state, name";
			  $AgriMetResults = $database->query($AgriMetQuery);
			  while($AgriMetRow = mysql_fetch_assoc($AgriMetResults))
			  {
					$retVal .= ", {\"optionValue\":\"".$AgriMetRow['StnID']."\", \"optionDisplay\": \"".$AgriMetRow['Name']."\"}";
			  }
			break;
			default:
				$retVal = "[ {\"optionValue\":\"-1\", \"optionDisplay\": \"Select Network First\"} ";
			break;
		}
	}
	else
	{
		$retVal = "[ {\"optionValue\":\"-1\", \"optionDisplay\": \"Select Network First\"} ";
	}
	$retVal .= "]";
	echo $retVal;

?>