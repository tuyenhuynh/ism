<?php
	class databaseManager
	{
		public $connection; // database connection
		public $num_active_users; //Number of active users viewing site
		public $num_active_guests; //Number of active guests viewing site
		public $num_members; //Number of signed-up users
		
		function __construct()
		{
			$this->connection = mysql_connect( DB_SERVER, DB_USER, DB_PASSWORD) or die("Connection error: ".mysql_error());
			mysql_select_db(DB_NAME, $this->connection) or die(mysql_error());		
		}
		
		// Takes the username and returns an array of all the account information from a user account
		// returns an array if successful, if the username is not found, it returns a NULL
		public function getUserInfo($username)
		{
			$getUserInfoQuery = "SELECT * FROM " . TBL_USERS . " WHERE ".COL_USERNAME." = '$username'";
			$result = $this->query($getUserInfoQuery);
			// did not find username
			if(!$result || mysql_num_rows($result) < 1 )
			{
					return NULL;
			}
			else
			{
				$growerArray = mysql_fetch_array($result);
				return $growerArray;
			}
		}

		function getCropInfo($cropID)
		{
			$query_CropInfo = sprintf("SELECT * FROM ".TBL_CROPS." WHERE cropDefaultsID = %s", GetSQLValueString($cropID,"int"));
			return mysql_fetch_assoc($this->query($query_CropInfo));
		}
		
		function getFieldInfo($fieldID)
		{
			$query_FieldInfo = sprintf("SELECT * FROM ".TBL_FIELD." WHERE fieldID = %s", GetSQLValueString($fieldID, "int"));
			return mysql_fetch_assoc($this->query($query_FieldInfo));
		}
		public function getDefaultField($username)
		{
			$qry = "SELECT irrigation_field FROM awn.users where username = '$username'";
			return mysql_fetch_assoc($this->query($qry));
		}
		public function setDefaultField($growerID, $fieldID)
		{
			$qry = "update awn.users set irrigation_field = $fieldID where OBJID = $growerID";
			return $this->query($qry);
		}

		function getDayInfo($fieldID, $fieldYear, $plantDate)
		{
			$retVal = array();
			$query_AdvancedFieldInfo = sprintf("SELECT * FROM ".TBL_INDIVID_FIELD." WHERE fieldID = %s and (fieldYear = ".$fieldYear." or fieldYear is null) and status = 0 and doy >= $plantDate ORDER BY doy DESC", GetSQLValueString($fieldID, "text"));
			$AdvancedFieldInfo = $this->query($query_AdvancedFieldInfo);	
			while($row_AdvancedFieldInfo = mysql_fetch_assoc($AdvancedFieldInfo) )
			{
				$_SESSION['lastUpdate'] = $row_AdvancedFieldInfo['lastupdated'];
				$retVal[$row_AdvancedFieldInfo['doy']] = $row_AdvancedFieldInfo;
				if($row_AdvancedFieldInfo['waterStorageAtFieldCapacity'] > $_SESSION['maxy'])
				{
					$_SESSION['maxy'] = $row_AdvancedFieldInfo['waterStorageAtFieldCapacity']+0.5;
				}
			}
			return $retVal;
		}


		/**
		 * query - Performs the given query on the database and
		 * returns all metadata .
		 */
		public function GetStationMetaData($unitID) {
		
			$qry = "select * from awn.METADATA where unit_id = '$unitID'";
			$result = $this->query ( $qry );
			if (! $result) {
				return (False);
			}
			$arrStationMetaData = mysql_fetch_assoc ( $result );
			return $arrStationMetaData;
		}	
		/**
		 * updateUserField - Updates a field, specified by the field
		 * parameter, in the user's row of the database.
		 */
		public function updateUserField($username, $field, $value) {
			$q = 'UPDATE ' . TBL_USERS . ' SET ' . $field . " = '$value' WHERE username = '$username'";
			return mysql_query ( $q, $this->connection );
		}
	
		public function confirmDisableUser($username) {
			/* Add slashes if necessary (for query) */
			$username = mysql_real_escape_string ( $username );
			/* Verify that user is in database */
			$q = 'SELECT username FROM ' . TBL_BANNED_USERS . " WHERE username = '$username'";
			$result = $this->query ( $q );
			if ($result && (mysql_num_rows ( $result ) >= 1)) {
				return true; //Indicates username failure
			}
		}
		/**
		 * confirmUserPass - Checks whether or not the given
		 * username is in the database, if so it checks if the
		 * given password is the same password in the database
		 * for that user. If the user doesn't exist or if the
		 * passwords don't match up, it returns an error code
		 * (1 or 2). On success it returns 0.
		 */
		public function confirmUserPass($username, $password) {
			/* Add slashes if necessary (for query) */
			$username = mysql_real_escape_string ( $username );
	
			/* Verify that user is in database */
			$q = 'SELECT password FROM ' . TBL_USERS . " WHERE username = '$username'";
			$result = $this->query( $q );
			if (! $result || (mysql_num_rows ( $result ) < 1)) {
				return 1; //Indicates username failure
			}
	
			/* Retrieve password from result, strip slashes */
			$dbarray = mysql_fetch_assoc ( $result );
	
			/* Validate that password is correct */
			if ($password == $dbarray ['password']) {
				return 0; //Success! Username and password confirmed
			} else {
				return 2; //Indicates password failure
			}
		}
			/**
		 * confirmUserID - Checks whether or not the given
		 * username is in the database, if so it checks if the
		 * given userid is the same userid in the database
		 * for that user. If the user doesn't exist or if the
		 * userids don't match up, it returns an error code
		 * (1 or 2). On success it returns 0.
		 */
		public function confirmUserID($username, $userid) {
			/* Add slashes if necessary (for query) */
			$username = mysql_real_escape_string ( $username );
	
			/* Verify that user is in database */
			$q = 'SELECT useridcookie FROM ' . TBL_USERS . " WHERE ".COL_USERNAME." = '$userid'";
			$result = $this->query($q);
			if (! $result || (mysql_num_rows ( $result ) < 1)) {
				return 1; //Indicates username failure
			}
			/* Retrieve userid from result, strip slashes */
			$dbarray = mysql_fetch_assoc ( $result );
	
			/* Validate that userid is correct */
			if ($username == $dbarray ['useridcookie']) {
				return 0; //Success! Username and userid confirmed
			} else {
				return 2; //Indicates userid invalid
			}
		}

		public function query($query) {
			$result = mysql_query ( $query, $this->connection );
			if (! $result) {
					die( 'Invalid Query.  Please contact an administrator. '.$query);
			}
			return $result;
		}

		/**
		 * addActiveUser - Updates username's last active timestamp
		 * in the database, and also adds him to the table of
		 * active users, or updates timestamp if already there.
		 */
		public function addActiveUser($username, $time) {
	
			$q = 'select numqueries from ' . TBL_USERS . " WHERE username = '$username'";
			$result = mysql_query ( $q, $this->connection );
			$row = mysql_fetch_assoc ( $result );
			$numqueries = $row ['numqueries'];
			$numqueries ++;
			$lastipaddress = mysql_real_escape_string($_SERVER['REMOTE_ADDR']);
			$q = 'UPDATE ' . TBL_USERS . " SET timestamp = '$time', numqueries= '$numqueries', lastipaddress = '$lastipaddress' WHERE ".COL_USERNAME." = '$username'";
			mysql_query ( $q, $this->connection );
	
			if (! TRACK_VISITORS)
				return;
			$lastquery = substr(mysql_real_escape_string($_SERVER['REQUEST_URI']),0,255);
			$q = 'REPLACE INTO ' . TBL_ACTIVE_USERS . " (username,timestamp,last_query,server_id,status) VALUES ('$username', '$time','$lastquery','".trim($_SERVER['SERVER_NAME'])."',1)";
			mysql_query ( $q, $this->connection );
			$this->calcNumActiveUsers ();
		}
	
		/* addActiveGuest - Adds guest to active guests table */
		public function addActiveGuest($ip, $time) {
			if (! TRACK_VISITORS)
				return;
			if(isset($_SERVER['REQUEST_URI']))
				$lastquery = substr(mysql_real_escape_string($_SERVER['REQUEST_URI']),0,255);
			else
				$lastquery = "UNKNOWNQUERY";
			if(isset($_SERVER['SERVER_NAME']))
				$serverName = $_SERVER['SERVER_NAME'];
			else
				$serverName = "UNKNOWNSERVERNAME";
			$q = 'REPLACE INTO ' . TBL_ACTIVE_GUESTS . " (ip,timestamp,last_query,server_id,status) VALUES ('$ip', '$time','$lastquery','".trim($serverName)."',1)";
			mysql_query ( $q, $this->connection );
			$this->calcNumActiveGuests ();
		}
	
		/* These functions are self explanatory, no need for comments */
	
		/* removeActiveUser */
		public function removeActiveUser($username) {
			if (! TRACK_VISITORS)
				return;
			$q = 'UPDATE ' . TBL_ACTIVE_USERS . " SET STATUS = 0 WHERE ".COL_USERNAME." = '$username'";
			mysql_query ( $q, $this->connection );
			$this->calcNumActiveUsers ();
		}
	
	/**
	 * usernameTaken - Returns true if the username has
	 * been taken by another user, false otherwise.
	 */
	public function usernameTaken($username) {
		$username = mysql_real_escape_string ( $username );
		$q = 'SELECT username FROM ' . TBL_USERS . " WHERE username = '$username'";
		$result = mysql_query ( $q, $this->connection );
		return (mysql_num_rows ( $result ) > 0);
	}

	/**
	 * usernameTaken - Returns true if the username has
	 * been taken by another user, false otherwise.
	 */
	public function emailTaken($email) {
		$username = mysql_real_escape_string ( $email );
		$q = 'SELECT username FROM ' . TBL_USERS . " WHERE email = '$email'";
		$result = mysql_query ( $q, $this->connection );
		return (mysql_num_rows ( $result ) > 0);
	}	
	
	/**
	 * usernameTaken - Returns true if the username has
	 * been taken by another user, false otherwise.
	 */
	public function getUsernames($email) {
		$username = mysql_real_escape_string ( $email );
		$q = 'SELECT username FROM ' . TBL_USERS . " WHERE email = '$email'";
		$result = mysql_query ( $q, $this->connection );
		return ($result);
	}		

	/* removeActiveGuest */
		public function removeActiveGuest($ip) {
			if (! TRACK_VISITORS)
				return;
			$q = 'UPDATE ' . TBL_ACTIVE_GUESTS . " SET STATUS = 0 WHERE ".COL_IP." = '$ip'";
			mysql_query ( $q, $this->connection );
			$this->calcNumActiveGuests ();
		}
	
		/* removeInactiveUsers */
		public function removeInactiveUsers() {
			if (! TRACK_VISITORS)
				return;
			$timeout = time () - USER_TIMEOUT * 60;
			$q = 'UPDATE ' . TBL_ACTIVE_USERS . " SET STATUS = 0 WHERE STATUS > 0 AND timestamp < $timeout";
			mysql_query ( $q, $this->connection );
			$this->calcNumActiveUsers ();
		}
	
		/* removeInactiveGuests */
		public function removeInactiveGuests() {
			if (! TRACK_VISITORS)
				return;
			$timeout = time () - GUEST_TIMEOUT * 60;
			$q = 'UPDATE ' . TBL_ACTIVE_GUESTS . " SET STATUS = 0 WHERE STATUS > 0 AND timestamp < $timeout";
			mysql_query ( $q, $this->connection );
			$this->calcNumActiveGuests ();
		}
		/**
		 * calcNumActiveUsers - Finds out how many active users
		 * are viewing site and sets class variable accordingly.
		 */
		public function calcNumActiveUsers() {
			/* Calculate number of users at site */
			$q = 'SELECT * FROM ' . TBL_ACTIVE_USERS." WHERE STATUS > 0 ";
			$result = mysql_query ( $q, $this->connection );
			$this->num_active_users = mysql_num_rows ( $result );
		}
	
		/**
		 * calcNumActiveGuests - Finds out how many active guests
		 * are viewing site and sets class variable accordingly.
		 */
		public function calcNumActiveGuests() {
			/* Calculate number of guests at site */
			$q = 'SELECT * FROM ' . TBL_ACTIVE_GUESTS." WHERE STATUS > 0 ";
			$result = mysql_query ( $q, $this->connection );
			$this->num_active_guests = mysql_num_rows ( $result );
		}


	public function awnET($stationID, $StrtDate, $EndDate)
	{
		require_once 'ETCalc.class.php';
		$metaRow = $this->GetStationMetaData($stationID);
		$lat = $metaRow['STATION_LATDEG'];
		$ele = $metaRow['STATION_ELEVATION'];

		$etr = "";
		$eto = "";
		$AnemomH = 2;
		
		$retVal = array();
	  $stationQuery = "select 
	          date_format(tstamp, '%Y-%m-%d') as SelDate, 
	          max(if(AIR_TEMP<>99999,AIR_TEMP,NULL)) as MaxAirTemp, 
	          min(if(AIR_TEMP<>99999,AIR_TEMP,NULL)) as MinAirTemp, 
	          avg(if(DEWPOINT<>99999,DEWPOINT,NULL)) as AvgDewpoint, 
	          avg(if(SOLAR_RAD<>99999,SOLAR_RAD,NULL)) as AvgSolarRad, 
	          avg(if(WIND_SPEED<>99999,WIND_SPEED,NULL)) as AvgWindSpeed, 
	          sum(if(PRECIP<>99999,PRECIP,NULL)) as Precip, 
	          dayofyear(tstamp) as JulDay FROM station".$stationID."  
	          where tstamp between '$StrtDate' and '$EndDate'  
	          and tstamp < curdate()
	          group by dayofyear(tstamp) order by tstamp;"; 
	  $stationResults = $this->query($stationQuery);
	  
	  while($thisRow = mysql_fetch_assoc($stationResults))
	  {
	  	
			
	  	//For each day in the result set
	    $AnemomH = 2;
	    ETCalc ( $thisRow ['MaxAirTemp'], $thisRow ['MinAirTemp'], $thisRow ['AvgDewpoint'], ($thisRow ['AvgSolarRad'] * 24), ($thisRow ['AvgWindSpeed'] * 24), $thisRow ['JulDay'], $lat, $ele, $AnemomH, $etr, $eto );     	    
	    //fill results array to send back for processing
	    $retVal[$thisRow ['JulDay']]['et'] = $etr;
	    $retVal[$thisRow ['JulDay']]['precip'] = $thisRow['Precip'];
	  }
	  return $retVal;
	}
        

	public function GetCoAgMetStationMetaData($unitID)
	{
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
				return $results;
			else
				return null;
		}
	}
        
	public function azmetET($stationID, $StrtDate, $EndDate)
	{
		require_once 'ETCalc.class.php';
		$metaRow = $this->GetAzmetStationMetaData($stationID);
		$lat = $metaRow['station_latdeg'];
		$ele = $metaRow['elevation'];

		$etr = "";
		$eto = "";
		$AnemomH = 2;

		$retVal = array();
	
		
		$sD = substr(date("Y",strtotime($StrtDate)),-2);
		$URLFile = "http://ag.arizona.edu/azmet/data/".$stationID.$sD."rd.txt";
		$lines = file ( $URLFile );
		$numLines = count ( $lines );
		for($i = 0; $i < $numLines; $i++)
		{
			$thisLine = explode(",",$lines[$i]);
			if($thisLine[1] >= date("z",strtotime($StrtDate)) + 1 && $thisLine[1] <= date("z",strtotime($EndDate)) + 1)
			{
				$jDay = $thisLine[1];
				$maxAirTemp = $thisLine[3] * 9/5 + 32;
				$minAirTemp = $thisLine[4] * 9/5 + 32;
				$avgDP = $thisLine[27] * 9/5 + 32;
				$totalSolarRad = $thisLine[10] * 23.9 * 11.622;
				$totalWindRun = $thisLine[18] * 24 * 2.237;
				$precip = $thisLine[11] * 0.03937;
		    ETCalc ( $maxAirTemp, $minAirTemp, $avgDP, $totalSolarRad, $totalWindRun, $jDay, $lat, $ele, $AnemomH, $etr, $eto );     	    
		    //fill results array to send back for processing
		    $retVal[$jDay-1]['et'] = $etr;
		    $retVal[$jDay-1]['precip'] = $precip;
			}
		}
  	return $retVal;
	}
	
	public function GetNDAWNStationMetaData($unitID) {

	
		$qry = "select * from irrigation.table_ndawn_stations where unit_id = '$unitID'";
		$result = $this->query( $qry );
		if (! $result) {
			return (False);
		}
		$arrStationMetaData = mysql_fetch_assoc ( $result );
		return $arrStationMetaData;
	}	

	public function NDAWNET($stationID, $StrtDate, $EndDate)
	{
		require_once 'ETCalc.class.php';
		$metaRow = $this->GetNDAWNStationMetaData($stationID);
		$lat = $metaRow['station_latdeg'];
		$ele = $metaRow['elevation'];

		$etr = "";
		$eto = "";
		$AnemomH = 2;

		$retVal = array();
	
		$sD = date("Y",strtotime($StrtDate));
		$URLFile = "http://ndawn.ndsu.nodak.edu/table.csv?station=$stationID&variable=ddmxt&variable=ddmnt&variable=ddws&variable=ddsr&variable=ddapetp&variable=ddapetjh&variable=ddr&variable=dddp&year=$sD&ttype=daily&quick_pick=&begin_date=$StrtDate&end_date=$EndDate";
		$lines = file ( $URLFile );
		$numLines = count ( $lines );
		for($i = 3; $i < $numLines; $i++)
		{
			$thisLine = explode(",",$lines[$i]);
			if(date("z",strtotime($thisLine[4]."-".$thisLine[5]."-".$thisLine[6]))  >= date("z",strtotime($StrtDate))  && date("z",strtotime($thisLine[4]."-".$thisLine[5]."-".$thisLine[6])) <= date("z",strtotime($EndDate)) )
			{
				$jDay = date("z",strtotime($thisLine[4]."-".$thisLine[5]."-".$thisLine[6])) + 1;
				$maxAirTemp = $thisLine[7];
				$minAirTemp = $thisLine[9];
				$avgDP = $thisLine[21];
				$totalSolarRad = $thisLine[13] * 11.622;
				$totalWindRun = $thisLine[11] * 24;
				$PETP = $thisLine[15];
				$PETJ = $thisLine[17];
				$precip = $thisLine[19];
		    ETCalc ( $maxAirTemp, $minAirTemp, $avgDP, $totalSolarRad, $totalWindRun, $jDay, $lat, $ele, $AnemomH, $etr, $eto );     	    
		    //fill results array to send back for processing
		    $retVal[$jDay-1]['et'] = $etr;
		    $retVal[$jDay-1]['precip'] = $precip;
			}
		}
	

	  return $retVal;
	}	

	public function GetAWDNStationMetaData($unitID) {

		$qry = "select * from irrigation.table_awdn_stations where unit_id = '$unitID'";
		$result = $this->query( $qry );
		if (! $result) {
			return (False);
		}
		$arrStationMetaData = mysql_fetch_assoc ( $result );
		return $arrStationMetaData;
	}	


	public function GetMTAgriMetStationMetaData($unitID) {

		$qry = "select * from ".TBL_AGRIMET_STATIONS." where StnID = '$unitID'";
		$result = $this->query( $qry );
		if (! $result) {
			return (False);
		}
		$arrStationMetaData = mysql_fetch_assoc ( $result );
		return $arrStationMetaData;
	}		


	public function AWDNET($stationID, $StrtDate, $EndDate)
	{
		require_once 'ETCalc.class.php';
		$metaRow = $this->GetAWDNStationMetaData($stationID);
		$lat = $metaRow['station_latdeg'];
		$ele = $metaRow['elevation'];

		$etr = "";
		$eto = "";
		$AnemomH = 2;
	
		$retVal = array();
		$sM = date("m",strtotime($StrtDate));
		$sD = date("d",strtotime($StrtDate));
		$sY = date("Y",strtotime($StrtDate));
		$eM = date("m",strtotime($EndDate));
		$eD = date("d",strtotime($EndDate));
		$eY = date("Y",strtotime($EndDate));
		$URLFile = "http://climate.sdstate.edu/awdn/archive/archive.asp?txtfrom=$sM%2F$sD%2F$sY&txtto=$eM%2F$eD%2F$eY&station=$stationID&Submit=Get+Data";

		$lines = file ( $URLFile );
		
		$numLines = count ( $lines );
		$isData = false;
		$continue = false;
		for($i = 0; $i < $numLines; $i++)
		{
			$thisLine = trim($lines[$i])."";
			if(strlen($thisLine) > 0)
			{
				$pos1 = strpos($thisLine,"<tr bgcolor='#ffffff'>");
				$pos2 = strpos($thisLine,"<tr bgcolor='#f8f8f8'>");
				$pos3 = strpos($thisLine,"<tr bgcolor='#f0f0f0'>");
	
				if($pos1 === false && $pos2 === false)
				{
				}
				else
				{
					$column = 0;
					$thisLine = substr($thisLine,22);
					$isData = true;
					$continue = true;
				}
				if($pos3===false && $isData)
				{
					$thisLine = substr($thisLine,44);
					$pos4 = strpos($thisLine,"<");
					if($pos4===false)
					{
						$pos4 = strpos($thisLine,"</div>");
					}
					if($pos4 === false)
					{
						die("AWDN FORMAT CHANGE PLEASE NOTIFY AN ADMINISTRATOR</br>ThisLine $i: ".$lines[$i]."</br>");
					}
					else
					{
						$thisLine = substr($thisLine,0,$pos4);
					}
				}
				else
				{
					$continue=false;
					if($isData)
						$isData = false;
				}
				if($isData && $continue)
				{
					$column++;
					switch($column)
					{
						case 1:
							$jDay = date("z",strtotime($thisLine)) + 1;
						break;
						case 2:
							$maxAirTemp = $thisLine;
						break;
						case 3:
							$minAirTemp = $thisLine;
						break;
						case 4:
							$MyAT= $thisLine;
						break;
						case 7:
							$MyATc = ($MyAT - 32) * 5 / 9;
	            $MySVP = (6.107799961
	                    + 4.436518521E-1 * $MyATc
	                    + 1.428945805E-2 * $MyATc * $MyATc
	                    + 2.650648471E-4 * $MyATc * $MyATc * $MyATc
	                    + 3.031240396E-6 * $MyATc * $MyATc * $MyATc * $MyATc
	                    + 2.034080948E-8 * $MyATc * $MyATc * $MyATc * $MyATc * $MyATc
	                    + 6.136820929E-11 * $MyATc * $MyATc * $MyATc * $MyATc * $MyATc * $MyATc) / 10;
							if($MyATc < 0)
							{
								$MySVP = -0.00486 + 0.85471 * $MySVP + 0.2441 * $MySVP * $MySVP;
							}
							$MyVp = $thisLine * $MySVP/ 100;
							$dewPointC = (241.88 * log($MyVp / 0.61078)) / (17.558 - log($MyVp / 0.61078));
							$dewPoint = $dewPointC * 9 / 5 + 32;
	            $dewPoint = round($dewPoint, 5);

							$avgDP = $dewPoint;
							//die($avgDP);
						break;
						case 8:
							$totalSolarRad = $thisLine * 23.9 * 11.622;
							//echo "Total Solar Rad: ".$totalSolarRad."</br>";
						break;
						case 9:
							$totalWindRun = $thisLine * 24;
						break;
						case 13:
							$precip = $thisLine;
					    ETCalc ( $maxAirTemp, $minAirTemp, $avgDP, $totalSolarRad, $totalWindRun, $jDay, $lat, $ele, $AnemomH, $etr, $eto );     	    
					    //fill results array to send back for processing
					    $retVal[$jDay-1]['et'] = $etr;
					    $retVal[$jDay-1]['precip'] = $precip;
		    			//die("MaxAT: $maxAirTemp</br>MinAT: $minAirTemp</br>DP: $avgDP</br>Total Solar Rad: $totalSolarRad</br>Total Wind Run: $totalWindRun</br>JDay: $jDay</br>Lat: $lat</br>Ele: $ele</br>AnemH: $AnemomH</br>ETR: $etr</br>ETO: $eto</br>");
						break;
						default:
							//echo $URLFile." ".$column.": ".$thisLine.PHP_EOL;					
						break;
					}
				}

			}

		}
	  return $retVal;
	}	

	public function CIMISET($stationID, $StrtDate, $EndDate)
	{
		require_once 'ETCalc.class.php';
		$metaRow = $this->GetCIMISStationMetaData($stationID);
		$lat = $metaRow['station_latdeg'];
		$ele = $metaRow['elevation'];

		$etr = "";
		$eto = "";
		$AnemomH = 2;
	
		$retVal = array();
		if(date("Y-m-d",strtotime($EndDate)) > date("Y-m-d"))
		{
			$EndDate = date("Y-m-d");
		}
		$URLFile = "http://et.water.ca.gov/api/data?appKey=52FFECDC-2F50-456B-A725-714AFC4CF537&targets=".$stationID."&startDate=$StrtDate&endDate=$EndDate";
		
		//echo $URLFile;
		$lines = file ( $URLFile );
		$obj = json_decode($lines[0],true);
		foreach($obj['Data']['Providers'][0]['Records'] as $value)
		{
                    $jDay = $value['Julian'];
                    $maxAirTemp = $value['DayAirTmpMax']['Value'];
                    $minAirTemp = $value['DayAirTmpMin']['Value'];
                    $avgDP = $value['DayDewPnt']['Value'];
                    $totalSolarRad = $value['DaySolRadAvg']['Value'] * 11.622 ;
                    $totalWindRun = $value['DayWindRun']['Value'];
                    $precip = $value['DayPrecip']['Value'];
                    ETCalc ( $maxAirTemp, $minAirTemp, $avgDP, $totalSolarRad, $totalWindRun, $jDay, $lat, $ele, $AnemomH, $etr, $eto );     	    
                    //fill results array to send back for processing
                    $retVal[$jDay-1]['et'] = $eto;
                    $retVal[$jDay-1]['precip'] = $precip;

		}
		return $retVal;
	}	

	function updateDay($cnt, $scheduler)
	{
		$updateQuery = sprintf("update irrigation.tblindividfield set 
		fieldID = %s,
		doy= %s,
		fieldYear= %s,
		fieldDate= %s,
		etr= %s,
		kc= %s,
		etc= %s,
		rain= %s,
		irrig= %s,
		fcWater= %s,
		wpWater= %s,
		madWater= %s,
		measdPcntAvail= %s,
		modified= %s,
		rootdepth= %s,
		currentSoilProfileWaterStorage= %s,
		waterStorageAtFieldCapacity= %s,
		waterStorageAtMad= %s,
		waterStorageAtPermanentWiltingPoint= %s,
		rootZoneWaterDeficit= %s,
		calculatedSoilWaterAvailibility= %s,
		availableSoilWaterContentAbovePWP = %s,
		deepPercolation= %s,
		Ks= %s,
		lastupdated = %s,
		foragecutting = %s
		where individFieldID = %s",
		GetSQLValueString($scheduler->row_FieldInfo['fieldID'], "int"),
		GetSQLValueString($scheduler->row_tblIndividField[$cnt]['doy'], "int"),
		GetSQLValueString($scheduler->row_tblIndividField[$cnt]['fieldYear'], "text"),
		GetSQLValueString($scheduler->row_tblIndividField[$cnt]['fieldDate'], "date"),
		GetSQLValueString($scheduler->row_tblIndividField[$cnt]['et'], "double"),
		GetSQLValueString($scheduler->row_tblIndividField[$cnt]['kc'], "double"),
		GetSQLValueString($scheduler->row_tblIndividField[$cnt]['etc'], "double"),
		GetSQLValueString($scheduler->row_tblIndividField[$cnt]['rain'], "double"),
		GetSQLValueString($scheduler->row_tblIndividField[$cnt]['irrig'], "double"),
		GetSQLValueString($scheduler->row_tblIndividField[$cnt]['fcWater'], "double"),
		GetSQLValueString($scheduler->row_tblIndividField[$cnt]['wpWater'], "double"),
		GetSQLValueString($scheduler->row_tblIndividField[$cnt]['madWater'], "double"),
		GetSQLValueString($scheduler->row_tblIndividField[$cnt]['measdPcntAvail'], "double"),
		GetSQLValueString($scheduler->row_tblIndividField[$cnt]['modified'], "int"), 
		GetSQLValueString($scheduler->row_tblIndividField[$cnt]['rootDepth'], "double"),
		GetSQLValueString($scheduler->row_tblIndividField[$cnt]['currentSoilProfileWaterStorage'], "double"),
		GetSQLValueString($scheduler->row_tblIndividField[$cnt]['waterStorageAtFieldCapacity'], "double"),
		GetSQLValueString($scheduler->row_tblIndividField[$cnt]['waterStorageAtMad'], "double"),
		GetSQLValueString($scheduler->row_tblIndividField[$cnt]['waterStorageAtPermanentWiltingPoint'], "double"),
		GetSQLValueString($scheduler->row_tblIndividField[$cnt]['rootZoneWaterDeficit'], "double"),
		GetSQLValueString($scheduler->row_tblIndividField[$cnt]['calculatedSoilWaterAvailability'], "double"),
		GetSQLValueString($scheduler->row_tblIndividField[$cnt]['availableSoilWaterContentAbovePWP'], "double"),
		GetSQLValueString($scheduler->row_tblIndividField[$cnt]['deepPercolation'], "double"),
		GetSQLValueString($scheduler->row_tblIndividField[$cnt]['Ks'], "double"),
		GetSQLValueString(date("Y-m-d H:i:s"), "text"),
		GetSQLValueString($scheduler->row_tblIndividField[$cnt]['foragecutting'], "int"),
		GetSQLValueString($scheduler->row_tblIndividField[$cnt]['individFieldID'], "int")				
		);
		$this->query($updateQuery);
	}


	function insertDay($cnt, $scheduler)
	{
		$fCut = 0;
		if(isset($scheduler->row_tblIndividField[$cnt]['foragecutting']))
			$fCut = $scheduler->row_tblIndividField[$cnt]['foragecutting'];
		$indInsQry = sprintf("insert into irrigation.tblindividfield 
		(
		fieldID, 
		doy, 
		fieldYear, 
		fieldDate, 
		etr, 
		kc, 
		etc, 
		rain, 
		irrig, 
		fcWater, 
		wpWater, 
		madWater,  
		measdPcntAvail, 
		modified, 
		rootdepth, 
		currentSoilProfileWaterStorage, 
		waterStorageAtFieldCapacity, 
		waterStorageAtMad, 
		waterStorageAtPermanentWiltingPoint, 
		rootZoneWaterDeficit, 
		calculatedSoilWaterAvailibility, 
		deepPercolation, 
		Ks, 
		foragecutting, 
		availableSoilWaterContentAbovePWP
		) 
		values (
		%s, 
		%s, 
		%s, 
		%s, 
		%s, 
		%s, 
		%s, 
		%s, 
		%s, 
		%s, 
		%s, 
		%s, 
		%s, 
		%s, 
		%s, 
		%s, 
		%s, 
		%s, 
		%s, 
		%s, 
		%s, 
		%s, 
		%s, 
		%s, 
		%s)",
		GetSQLValueString($scheduler->row_FieldInfo['fieldID'], "int"),
		GetSQLValueString($scheduler->row_tblIndividField[$cnt]['doy'], "int"),
		GetSQLValueString($scheduler->row_tblIndividField[$cnt]['fieldYear'], "text"),
		GetSQLValueString($scheduler->row_tblIndividField[$cnt]['fieldDate'], "date"),
		GetSQLValueString($scheduler->row_tblIndividField[$cnt]['et'], "double"),
		GetSQLValueString($scheduler->row_tblIndividField[$cnt]['kc'], "double"),
		GetSQLValueString($scheduler->row_tblIndividField[$cnt]['etc'], "double"),
		GetSQLValueString($scheduler->row_tblIndividField[$cnt]['rain'], "double"),
		GetSQLValueString($scheduler->row_tblIndividField[$cnt]['irrig'], "double"),
		GetSQLValueString($scheduler->row_tblIndividField[$cnt]['fcWater'], "double"),
		GetSQLValueString($scheduler->row_tblIndividField[$cnt]['wpWater'], "double"),
		GetSQLValueString($scheduler->row_tblIndividField[$cnt]['madWater'], "double"),
		GetSQLValueString($scheduler->row_tblIndividField[$cnt]['measdPcntAvail'], "double"),
		GetSQLValueString($scheduler->row_tblIndividField[$cnt]['modified'], "int"), 
		GetSQLValueString($scheduler->row_tblIndividField[$cnt]['rootDepth'], "double"),
		GetSQLValueString($scheduler->row_tblIndividField[$cnt]['currentSoilProfileWaterStorage'], "double"),
		GetSQLValueString($scheduler->row_tblIndividField[$cnt]['waterStorageAtFieldCapacity'], "double"),
		GetSQLValueString($scheduler->row_tblIndividField[$cnt]['waterStorageAtMad'], "double"),
		GetSQLValueString($scheduler->row_tblIndividField[$cnt]['waterStorageAtPermanentWiltingPoint'], "double"),
		GetSQLValueString($scheduler->row_tblIndividField[$cnt]['rootZoneWaterDeficit'], "double"),
		GetSQLValueString($scheduler->row_tblIndividField[$cnt]['calculatedSoilWaterAvailability'], "double"),
		GetSQLValueString($scheduler->row_tblIndividField[$cnt]['deepPercolation'], "double"),
		GetSQLValueString($scheduler->row_tblIndividField[$cnt]['Ks'], "double"),
		GetSQLValueString($fCut, "int"),
		GetSQLValueString($scheduler->row_tblIndividField[$cnt]['availableSoilWaterContentAbovePWP'], "double")
		);
		$this->query($indInsQry);		
	
	}

	//get GrowerEmail, takes growerName and gets associated email address
	function getGrowerEmail($growerName)
	{
		$qry = "SELECT email from awn.users where username = '$growerName'";
		$result = $this->query($qry);
		$row = mysql_fetch_assoc($result);
		$email = $row['email'];
		return $email;
	}

	function getAllWeatherNetworks()
	{
		$getCropQuery = "SELECT * FROM irrigation.table_weather_network ORDER BY objid";
		$result = $this->query($getCropQuery);
		
		if(!$result || (mysql_num_rows($result) < 1))
			return NULL;
		else
			return $result;
	}
	function getAllStations($theYear = "")
	{
		$getStationQuery = "SELECT * FROM " . TBL_STATIONS . " where active_station = 'Y' and station_type = 'public' ";
		if(strlen($theYear) > 0 && $theYear >= 1900)
		{
			$getStationQuery .= " AND INSTALLATION_DATE <= '".$theYear."-01-01' ";
		}
		$getStationQuery .= " ORDER BY station_name ASC";
		$result = $this->query($getStationQuery);
		
		if(!$result || (mysql_num_rows($result) < 1))
			return NULL;
		else
			return $result;
	}

	// list all the crops in the table
	// @return	returns a resource if the crop is found in the db, otherwise returns a false	
	function getAllCrops()
	{
		$getCropQuery = "SELECT * FROM " . TBL_CROPS . " where status = 1 ORDER BY cropName ASC";
		$result = $this->query($getCropQuery);
		
		if(!$result || (mysql_num_rows($result) < 1))
			return NULL;
		else
			return $result;
	}

	// list all the soils in the table
	// @return	returns a resource if the crop is found in the db, otherwise returns a false	
	function getAllSoils()
	{
		$getSoilQuery = "SELECT * FROM " . TBL_SOILS . " ORDER BY soilID ASC";
		$result = $this->query($getSoilQuery);

		if(!$result || (mysql_num_rows($result) < 1))
			return NULL;
		else
			return $result;	
	}
	
	public function GetAzmetStationMetaData($unitID) {

		$qry = "select * from irrigation.table_azmet_stations where unit_id = '$unitID'";
		$result = mysql_query ( $qry, $this->connection );
		if (! $result) {
			return (False);
		}
		$arrStationMetaData = mysql_fetch_assoc ( $result );
		return $arrStationMetaData;
	}	

	public function GetCIMISStationMetaData($unitID) {
		$URLFile = "http://et.water.ca.gov/api/station/$unitID";
		$lines = file ( $URLFile );
		$station = json_decode($lines[0],true);
		$pos = strpos($station['Stations'][0]['HmsLatitude'],"/");
		$lat = trim(substr($station['Stations'][0]['HmsLatitude'],$pos+1));
		$pos = strpos($station['Stations'][0]['HmsLongitude'],"/");
		$lng = trim(substr($station['Stations'][0]['HmsLongitude'],$pos+1));
		$ele = $station['Stations'][0]['Elevation'];
		$result['station_latdeg'] = $lat;
		$result['station_lngdeg'] = $lng;
		$result['elevation'] = $ele;
		return $result;
	}	

		public function GetAgriMetStationMetaData($unitID) {

		$qry = "select * from `irrigation`.agrimetstations where StnID = '$unitID'";
		$result = $this->query( $qry );
		if (! $result) {
			return (False);
		}
		$arrStationMetaData = mysql_fetch_assoc ( $result );
		return $arrStationMetaData;
	}			
	
	function countFields($growerID)
	{
	  $queryFieldCount = "SELECT COUNT(*) as FIELDCOUNT from irrigation.tblfield where growerid = ".$growerID;
		$resultsFieldCount = $this->query($queryFieldCount);
		if($rowFieldCount = mysql_fetch_assoc($resultsFieldCount))
			return $rowFieldCount['FIELDCOUNT'];
		else
			return false;
	}	
	// inserts a new field row into the tblfield table
	// assumes all values are already validated both client and server-side
	// @param	tablename/fieldname - alphanumeric unique value representing a field name
	// @param	growerID
	// @param	weatherStnID
	// @param	year
	// @param	soilFC
	// @param	soilWP
	// @param	soilAWC
	// @param	plantDate						
	// @param	growth10PcntDate
	// @param	growthMaxDate
	// @param	growthDeclineDate
	// @param	growthEndDate
	// @param	kc1
	// @param	kc2
	// @param	kc3
	// @param	mad
	// @param	rz_val1
	// @param	rz_val2
	// @param	rz_val3
	// @param	initialSWC
	// @return	returns a resource if successfully inserted into the db, otherwise returns a false
	function addNewField($tableName, $growerID, $fieldName, $weatherNetwork, $weatherStnID, $year, $soilFC, $soilWP, $soilAWC, $plantDate, $growth10PcntDate, $growthMaxDate, $growthDeclineDate, $growthEndDate, $kc1, $kc2, $kc3, $mad, $rz_val1, $rz_val2, $rz_val3, $initialSWC, $cropID, $soilID, $flatDays = -1, $recoveryDays = -1)
	{
		$addNewFieldQuery = "select irrigation.sp_InsertNewField2('$tableName', $growerID, '$fieldName', $weatherNetwork, '$weatherStnID', $year, $soilFC, $soilWP, $soilAWC, 
				$plantDate, $plantDate, $growth10PcntDate, $growthMaxDate, $growthDeclineDate, 
				$kc1, $kc2, $kc3, $mad, $rz_val1, $rz_val2, $rz_val3, $initialSWC, $cropID, $soilID,$growthEndDate) as NewObjid; ";////, $flatDays, $recoveryDays);";
		 $retVal = $this->query($addNewFieldQuery);
		 return $retVal;
	}
	

	// inserts a new row into the tblindividfield table
	// assumes all values are already validated both client and server-side
	// @param	doy
	// @param	etr
	// @param	rz
	// @param	kc
	// @param	etc
	// @param	rain
	// @param	irrig
	// @param	fcWater
	// @param	wpWater
	// @param	madWater
	// @param	swc
	// @param	pcntAvail
	// @param	deficit
	// @param	remaining
	// @param	measPcntAvail
	// @return	returns a resource if successfully inserted into the db, otherwise returns a false	
	function addNewIndividField($fieldID, $doy, $etr, $rz, $kc, $etc, $rain, $irrig, $fcWater, $wpWater, $madWater, $swc, $pcntAvail, $deficit, $remaining, $measdPcntAvail)
	{
		$addNewIndividFieldQuery = "INSERT INTO " . TBL_INDIVID_FIELD . " (fieldid, doy, etr, rz, kc, etc, rain, irrig, fcwater, wpwater, madwater, swc, pcntavail, deficit, remaining, measdpcntavail) VALUES($fieldID, $doy, $etr, $rz, $kc, $etc, $rain, $irrig, $fcWater, $wpWater, $madWater, $swc, $pcntAvail, $deficit, $remaining, $measdPcntAvail); ";
		
		
		return $this->query($addNewIndividFieldQuery);
	}		
	/********************************
	Calculations
	********************************/
	function rootZone($doy, $prevRootZone, $rz_val1, $rz_val2, $plantDate, $growthMaxDate)
	{
		if($doy > $growthMaxDate)
		{
			$rootZone += $prevRootZone;	
		}
		elseif($doy < $plantDate)
		{
			$rootZone = 0;
		}
		else
		{
			$rootZone = ($plantDate-$doy)* $this->rootGrowthRate($rz_val2, $rz_val1, $growthMaxDate, $doy) + $rz_val1;
		}
		return $rootZone;
	}


	function rootGrowthRate( $maxRootDepth, $startingRootDepth, $growthMaxDate, $doy)
	{
			return ($maxRootDepth - $startingRootDepth)/($growthMaxDate-$doy);
	}
			
	}

?>
