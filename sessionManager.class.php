<?php
	require_once("ism.constants.php");
	require_once("databaseManager.class.php");

	$database =  new databaseManager();
	class sessionManager
	{
		public $pageFactory;
		public $username;				// string, username chosen by user on sign-up
		public $userid; 				// random integer generated when logged in
		public $access;				// integer, the level of data access given to a user
		public $logged_in;				// true if user is logged in, otherwise false
		public $userInfo = array();	// an array storing a user's account info
		public $currentURL;			//current url bein viewed
		public $referrer;				// last recorded site page viewed
		public $basepath = "/ism/index.php?m=1"; 
		public $processPath = "process.class.php";
		public $isMobileDevice = 1;
		public $isApp = 0;
		public $time; //Time user was last active (page loaded)

		function __construct()
		{
			$this->startSession();	
		}
		
		private function startSession()
		{
			global $database;
			$this->time = time ();
			if(!isset($_SESSION)) 
	    { 
	    	//echo "Starting Session";
					session_start (); //Tell PHP to start the session
	    }
			// determine if the user is logged in or not
			$this->checkLogin();
			if(isset($_REQUEST['androidApp']) || isset($_SESSION['androidApp']) )
			{
				$this->basepath .= "&androidApp=true";
				$this->isApp = 1;
				$_SESSION['androidApp'] = true;
			}

			/**
			 * Set guest value to users not logged in, and update
			 * active guests table accordingly.
			 */
			if (! $this->logged_in) {
				$_SESSION [USERNAME_COOKIE] = $this->username = GUEST_NAME;
				$this->userlevel = GUEST_LEVEL;
				if(isset($_SERVER['REMOTE_ADDR']))
					$database->addActiveGuest ( $_SERVER ['REMOTE_ADDR'], $this->time );
			} else { // Update users last active timestamp 
				$database->addActiveUser ( $this->username, $this->time );
			}
	
			//Remove inactive visitors from database 
			$database->removeInactiveUsers ();
			$database->removeInactiveGuests ();
	
			// Set referrer page
			if (isset ( $_SESSION ['url'] )) {
				$this->referrer = $_SESSION ['url'];
			} else {
				$this->referrer = "/";
			}
	
			// Set current url 
			$this->url = $_SESSION ['url'] = $_SERVER ['PHP_SELF'];
		}

		// check if the user has already logged in, and the session the user
		// has been using.  Also checks if the user is remembered
		// if any are true, then we check to see that the user is real
		// returns true if the user has logged in and is authentic
		private function checkLogin()
		{
			global $database;
			// check if user set the Remember Me cookie
			if(isset($_COOKIE[USERNAME_COOKIE]) && isset($_COOKIE[ID_COOKIE]))
			{
				$this->username = $_SESSION[USERNAME_COOKIE] = $_COOKIE[USERNAME_COOKIE];
				$this->userid 	= $_SESSION[ID_COOKIE] = $_COOKIE[ID_COOKIE];
			}
			
			// check to see if userid, username are set, and user is not a guest
			if(isset($_SESSION[USERNAME_COOKIE]) && isset($_SESSION[ID_COOKIE]) )
			{
				// confirm that the userid/username combo are valid
				if($database->confirmUserID($_SESSION[ID_COOKIE], $_SESSION[USERNAME_COOKIE]) != 0 )
				{
					// not a valid combo, the user is not logged in
					unset($_SESSION[ID_COOKIE]);
					unset($_SESSION[USERNAME_COOKIE]);
					$this->logged_in = false;
					return false;
				}
				
				// user is logged in, so we set the class variables
				$this->userInfo = $database->getUserInfo($_SESSION[USERNAME_COOKIE]);
				$this->username = $this->userInfo[COL_USERNAME];
				$this->userid	= $this->userInfo[COL_OBJID];
				$this->access	= $this->userInfo[COL_ACCESSLEVEL];
				$_SESSION ['growerID'] = $this->userInfo [COL_OBJID];

				$this->logged_in = true;
				return true;
			}
			else // user is not logged in
			{
				$this->username = $_SESSION[USERNAME_COOKIE] = GUEST_NAME;
				$this->access = GUEST_LEVEL;
				$this->logged_in = false;
				return false;
			}
		}
		
		/**
		 * login - The user has submitted his username and password
		 * through the login form, this function checks the authenticity
		 * of that information in the database and creates the session.
		 * Effectively logging in the user if all goes well.
		 */
		public function login($subuser, $subpass, $subremember) {
			global $iform; //The database and form object
			global $database;
	
			/* Username error checking */
			$field = "user"; //Use field name for username
			if (! $subuser || strlen ( $subuser = trim ( $subuser ) ) == 0) {
				$iform->setError ( $field, "* Username not entered<br/>" );
			} else {
				/* Check if username is not alphanumeric */
				if (! preg_match ( "/^([0-9a-zA-z])*$/i", $subuser )) {
					$iform->setError ( $field, "* Username not alphanumeric<br/>" );
				}
			}

			/* Password error checking */
			$field = "pass"; //Use field name for password
			if (! $subpass) {
				$iform->setError ( $field, "* Password not entered<br/>" );
			}
	
			/* Return if form errors exist */
			if ($iform->num_errors > 0) {
				return false;
			}

			/* Checks that username is in database and password is correct */
			$result = $database->confirmUserPass ( $subuser, md5 ( $subpass ) );

			/* Check error codes */
			if ($result == 1) {
				$field = "user";
				$iform->setError ( $field, "* Username not found<br/>" );
				//"bad name";
			} else if ($result == 2) {
				$field = "pass";
				$iform->setError ( $field, "* Invalid password<br/>" );
				//"bad pass";
			}
	
			/* Return if form errors exist */
			if ($iform->num_errors > 0) {
				return false;
			}
	
			/* Return if disable user */
			$isDisableUser = $database->confirmDisableUser ( $subuser );
			if ($isDisableUser) {
				unset ( $_SESSION [USERNAME_COOKIE] );
				unset ( $_SESSION [ID_COOKIE] );
				return false;
			}
	
			/* Username and password correct, register session variables */
			$this->userinfo = $database->getUserInfo ( $subuser );
			$this->username = $_SESSION [USERNAME_COOKIE] = $this->userinfo [COL_USERNAME];
			$_SESSION ['growerID'] = $this->userinfo [COL_OBJID];
			$this->userid = $_SESSION [ID_COOKIE] = $this->generateRandID ();
			$this->userlevel = $this->userinfo ['userlevel'];
			$this->favorite_station = $this->userinfo ['FAVORITE_STATION'];
			$this->favorite_zoom = $this->userinfo ['FAVORITE_ZOOM'];
			$this->favorite_lat = $this->userinfo ['FAVORITE_LAT'];
			$this->favorite_lng = $this->userinfo ['FAVORITE_LNG'];

	
			/* Insert userid into database and update active users table */
			$database->updateUserField ( $this->username, 'useridcookie', $this->userid );
			if(isset($_SERVER ['REMOTE_ADDR']))
				$database->removeActiveGuest ( $_SERVER ['REMOTE_ADDR'] );
	
			if (! $subremember) {
				setcookie ( USERNAME_COOKIE, "", time () - COOKIE_EXPIRE, COOKIE_PATH, COOKIE_DOMAIN );
				setcookie ( ID_COOKIE, "", time () - COOKIE_EXPIRE, COOKIE_PATH, COOKIE_DOMAIN );
				$database->updateUserField ( $this->username, 'useridcookie', $this->userid );
			}
	
			/**
			 * the user has requested that we remember that
			 * he's logged in, so we set two cookies. One to hold his username,
			 * and one to hold his random value userid. It expires by the time
			 * specified in constants.php. Now, next time he comes to our site, we will
			 * log him in automatically, but only if he didn't log out before he left.
			 */
			if ($subremember ) {
				setcookie ( USERNAME_COOKIE, $this->username, time () + COOKIE_EXPIRE, COOKIE_PATH, COOKIE_DOMAIN );
					$database->updateUserField ( $this->username, 'useridcookie', $this->userid );
					setcookie ( ID_COOKIE, $this->userid, time () + COOKIE_EXPIRE, COOKIE_PATH, COOKIE_DOMAIN );
			}
	
			/* Login completed successfully */
			return true;
		}
	
		/**
		 * logout - Gets called when the user wants to be logged out of the
		 * website. It deletes any cookies that were stored on the users
		 * computer as a result of him wanting to be remembered, and also
		 * unsets session variables and demotes his user level to guest.
		 */
		public function logout() {
			global $database; //The database connection
			/**
			 * Delete cookies - the time must be in the past,
			 * so just negate what you added when creating the
			 * cookie.
			 */
			if (isset ( $_COOKIE [USERNAME_COOKIE] ) && isset ( $_COOKIE [ID_COOKIE] )) {
				setcookie ( USERNAME_COOKIE, "", time () - COOKIE_EXPIRE, COOKIE_PATH, COOKIE_DOMAIN );
				setcookie ( ID_COOKIE, "", time () - COOKIE_EXPIRE, COOKIE_PATH, COOKIE_DOMAIN );
			}
	
			/* Unset PHP session variables */
			unset ( $_SESSION [USERNAME_COOKIE] );
			unset ( $_SESSION [ID_COOKIE] );
			unset ( $_SESSION['field']	);
			/* Reflect fact that user has logged out */
			$this->logged_in = false;
	
			/**
			 * Remove from active users table and add to
			 * active guests tables.
			 */
			 global $database;
			$database->removeActiveUser ( $this->username );
			if(isset($_SERVER['REMOTE_ADDR']))
				$remoteAddress = mysql_real_escape_string($_SERVER['REMOTE_ADDR']);
			else
				$remoteAddress = "UNKNOWNREMOTEADDRESS";
			$database->addActiveGuest ( $remoteAddress, $this->time );
	
			/* Set user level to guest */
			$this->username = GUEST_NAME;
			$this->userlevel = GUEST_LEVEL;
			$this->favorite_station = NULL;
			$this->favorite_zoom = 7;
			$this->favorite_lat = Null;
			$this->favorite_lng = Null;
		}

		/**
		 * generateRandID - Generates a string made up of randomized
		 * letters (lower and upper case) and digits and returns
		 * the md5 hash of it to be used as a userid.
		 */
		public function generateRandID() {
			return md5 ( $this->generateRandStr ( 16 ) );
		}
	
		/**
		 * generateRandStr - Generates a string made up of randomized
		 * letters (lower and upper case) and digits, the length
		 * is a specified parameter.
		 */
		public function generateRandStr($length) {
			$randstr = "";
			for($i = 0; $i < $length; $i ++) {
				$randnum = mt_rand ( 0, 61 );
				if ($randnum < 10) {
					$randstr .= chr ( $randnum + 48 );
				} else if ($randnum < 36) {
					$randstr .= chr ( $randnum + 55 );
				} else {
					$randstr .= chr ( $randnum + 61 );
				}
			}
			return $randstr;
		}
		
		// validates user inputs and selections, as well as calculating
		// data values for input into the field and individField tables
		// for a specific grower and field
		// returns true if there are no validation or form errors and there
		// is a successful insertion of a row into the field and individField tables
		// returns false if there are validation or form errors or there was an issue
		// inserting the data into the field and individField tables
		function addField($fieldName, $year, $station, $weatherNetwork, $crop, $soilType)
		{
	
			//"Adding New Field";
			global $database, $iform, $validation;
			$flatDays = 0;
			$recoveryDays = 0;
			// clean up inputs, remove slashes and whitespace
			$fieldName = mysql_real_escape_string(stripslashes($fieldName));
			$year = mysql_real_escape_string(stripslashes($year));
			$weatherNetwork = mysql_real_escape_string(stripslashes($weatherNetwork));
			$station = mysql_real_escape_string(stripslashes($station));
			$crop = mysql_real_escape_string(stripslashes($crop));
			$soilType = mysql_real_escape_string(stripslashes($soilType));
			
			// validate
			$validation->validateFieldName($fieldName);
			$validation->validateNumber($year);
			$validation->validateStation($station);
			$validation->validateCrop($crop);
			$validation->validateSoilType($soilType);
			
			// check for validation errors
			if($validation->num_errors > 0 )
			{
				//"There were validation errors";
				$_SESSION['error_array'] = $validation->getValidationErrors();
				return false;
			}
			else
			{
				// calculate field table values
					$soilDefaultQuery = "select * from irrigation.tblsoildefaults where soilID = $soilType";
					$soilDefaultResults = $database->query($soilDefaultQuery);
					$useDefaults = true;
					if(isset($_REQUEST['fieldbasedonchk']) && $_REQUEST['fieldbasedonchk'] == 'on' )
					{
						$copyFromField = mysql_real_escape_string($_REQUEST['basedonfield']); //mysql_real_escape_string($_REQUEST['fieldBasedOn']);
						$cropDefaultsQuery = "select * from irrigation.tblfield where fieldID = $copyFromField ";
						$cropDefaultsResults = $database->query($cropDefaultsQuery);
						if(mysql_num_rows($cropDefaultsResults) > 0)
						{
							$useDefaults = false;
						}
					}
					if($useDefaults)
					{
						$cropDefaultsQuery = "select * from irrigation.tblcropdefaults where cropdefaultsid = $crop";
						$cropDefaultsResults = $database->query($cropDefaultsQuery);
					}			
					if(!$cropDefaultsResults || !$soilDefaultResults)
					{
						//field defaults
						$soilFC = rand(0,5);
						$soilWP = rand(0,5);
						$soilAWC = rand(0,5);
						$initialSWC = rand(0,5);
	
						$mad = rand(0,5);
	
						//crop defaults
						$kc1 = rand(0,5);
						$kc2 = rand(0,5);
						$kc3 = rand(0,5);
						$rz_val1 = rand(0,5);
						$rz_val2 = rand(0,5);
						$rz_val3 = rand(0,5);
						$plantDate = rand(0,50);
						$growth10PctDate = $plantDate + rand(0,5);
						$growthMaxDate = $growth10PctDate + rand(0,5);
						$growthDeclineDate = $growthMaxDate + rand(0,5);
						$growthEndDate = $growthDeclineDate + rand(0,5);
					}
					else
					{
						//field defaults
						$soilDefaultsRow = mysql_fetch_assoc($soilDefaultResults);
						$soilFC = $soilDefaultsRow['soilFC'];
						$soilWP = $soilDefaultsRow['soilWP'];
						$soilAWC = $soilDefaultsRow['soilAWC'];
						$initialSWC = $soilAWC;
					
						$cropDefaultsRow = mysql_fetch_assoc($cropDefaultsResults);
						$mad = $cropDefaultsRow['mad']; ///100*$soilFC;
						
						$kc1 = $cropDefaultsRow['kc1'];
						$kc2 = $cropDefaultsRow['kc2'];
						$kc3 = $cropDefaultsRow['kc3'];
						$rz_val1 = $cropDefaultsRow['rz_val1'];
						$rz_val2 = $cropDefaultsRow['rz_val2'];
						$rz_val3 = rand(0,5);
	
					if(isset($_REQUEST['fieldbasedonchk']) && $_REQUEST['fieldbasedonchk'] == 'on' )
					{
						$plantDate = $cropDefaultsRow['plantDate'];
						$growth10PctDate = $cropDefaultsRow['growthMaxDate'];
						//Fix default water budget start date to plant 
						//$growth10PctDate = $cropDefaultsRow['growth10PcntDate'];
						$growthMaxDate = $cropDefaultsRow['growthDeclineDate'];
						$growthDeclineDate = $cropDefaultsRow['growthEndDate'];
						$growthEndDate = $cropDefaultsRow['seasonEndDate'];
					}
					else
					{
						$plantDate = $cropDefaultsRow['plantDate'];
						$growth10PctDate = $cropDefaultsRow['growth10PcntDate'];
						//Fix default water budget start date to plant 
						//$growth10PctDate = $cropDefaultsRow['growth10PcntDate'];
						$growthMaxDate = $cropDefaultsRow['growthMaxDate'];
						$growthDeclineDate = $cropDefaultsRow['growthDeclineDate'];
						$growthEndDate = $cropDefaultsRow['growthEndDate'];
					}					
					$flatDays = $cropDefaultsRow['postCuttingFlatDays'];
					$recoveryDays = $cropDefaultsRow['postCuttingRecoveryDays'];
	
					}
		
	
				// calculate individfield table values
				$doy = $plantDate;// get from plantDate ;
				$etr = rand(0,5); //from AWN;
				//Root Zone Depth for that day (created from rz_val1, rz_val2, and Plant/Emergence Date and Growth Max Date) using interpolation

				$prevRootZone = 0;							
				$rz = $database->rootZone($doy, $prevRootZone, $rz_val1, $rz_val2, $plantDate, $growthMaxDate);
				$kc= rand(0,5);
				$etc= rand(0,5);
				$rain= 0; //rand(0,5);
				$irrig= 0; //rand(0,5);
				$fcWater= rand(0,5);
				$wpWater= rand(0,5);
				$madWater= rand(0,5);
				$swc= rand(0,5);
				$pcntAvail= rand(0,5);
				$deficit= rand(0,5);
				$remaining= rand(0,5);
				$measdPcntAvail= "100"; //rand(0,5);

				
				// successfully insert data into tblfield
				$retVal =$database->addNewField($fieldName, $this->userid, $fieldName, $weatherNetwork, $station, $year, $soilFC, $soilWP, $soilAWC, $plantDate, $growth10PctDate, $growthMaxDate, $growthDeclineDate, $growthEndDate, $kc1, $kc2, $kc3, $mad, $rz_val1, $rz_val2, $rz_val3, $initialSWC, $crop, $soilType, $flatDays, $recoveryDays);
				if($retVal)
				{
					$row = mysql_fetch_array($retVal);
					$fieldID = $row['NewObjid'];
					$_SESSION['field'] = $fieldID;
					// successfully insert data into tblindividfield
					if($database->addNewIndividField($fieldID, $doy, $etr, $rz, $kc1, $etc, $rain, $irrig, $fcWater, $wpWater, $madWater, $swc, $pcntAvail, $deficit, $remaining, $measdPcntAvail))
					{
						return true;
					}
					else
					{
						die("Failed to add Individual Field, please contact an administrator!");
						return false; // couldn't insert into tblindividfield	
					}
				}
				else
				{
					return false; // coudn't insert into tblfield	
				}
			}
		}
		public function toHTML()
		{
			$retVal = "";
			$retVal .= date("Y-m-d H:i:s")."<br/>";
			if($this->logged_in)
				$retVal .= "Logged in!!<br/>";
			else
				$retVal .= "Not Logged in!!<br/>";
			return $retVal;
		}
	}

			if(!isset($_SESSION)) 
	    { 
					session_start (); //Tell PHP to start the session
	    }
?>