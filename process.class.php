<?php
require_once('irrigationScheduler.class.php');
if(isset($_SESSION['field']))
	$irrigationScheduler = new irrigationScheduler( $_SESSION['field']);
else
	$irrigationScheduler = new irrigationScheduler();
require_once ('Mail.php');
require_once ('Mail/mime.php');

function endsWith($haystack, $needle)
{
    return $needle === "" || substr($haystack, -strlen($needle)) === $needle;
}

function SendMsg($emailTo, $subject, $emailFrom, $data, $datahtml) {

	$headers = array ('From' => $emailFrom, 'Subject' => $subject );
	$mime = new Mail_mime ( );

	$status = $mime->setTXTBody ( $data );
	if (PEAR::isError ( $status )) {
		echo "addAttachment( ) returned with error " . $status->getMessage () . "\n";
	}
	$status = $mime->setHTMLBody ( $datahtml );
	if (PEAR::isError ( $status )) {
		echo "addAttachment( ) returned with error " . $status->getMessage () . "\n";
	}


	$body = $mime->get ();
	// Call after body has been set from $mime
	$headers = $mime->headers ( $headers );
	$mail = & Mail::factory ( 'mail' );
	
	$to = $emailTo;
	$cc = ''; //'sehill@wsu.edu';
	$bcc = 'sehill@wsu.edu';
	$recipients = $to;
	$headers['From'] =  $emailFrom; //'weather@wsu.edu';
	$headers['To'] = $to;
	$headers['Subject'] = $subject;
	$headers['Cc'] = $cc;
	$headers['Bcc'] = $bcc;
	$headers['Reply-To'] =  $emailFrom; //'weather@wsu.edu';
	
	
	$status = $mail->send ( $recipients, $headers, $body );
	if (PEAR::isError ( $status )) {
		echo "Error sending mail: " . $status->getMessage () . "\n";
		return FALSE;
	}
	return true;
}


class Process
{
	//class constructor
	function Process()
	{
		global $irrigationScheduler;
		unset($_SESSION['error_array']);
		unset($_SESSION['value_array']);
		//user submitted login form
		if(isset($_POST['login']))
		{
			$this->procLogin();	
		}
		else if(isset($_POST['budgettablepreviousnext']))
		{
			$this->procBudgetPreviousNext();
		}
		else if(isset($_POST['updatecropdefault']))
		{
			$this->procUpdateCropDefault();
		}
		// user submitted add a field form
		else if(isset($_POST['add-field']))
		{
			$this->procAddField();	
		}
		// user submitted selectField form
		else if(isset($_POST['select-field']))
		{
			$this->procGetFieldInfo();		
		}
		// user submitted budget edit form
		else if(isset($_REQUEST['savebudgetedit']))
		{
			$this->procSaveDailyBudgetEdit();		
		}
		else if(isset($_REQUEST['subdashboard']))
		{
			$this->procSaveDashboardEdit();		
		}
		// user submitted advanced field information
		else if(isset($_POST['advanced-field-update']))
		{
			$this->procUpdateAdvFieldInfo();	
		}
		// user submitted advanced field information
		else if(isset($_POST['field-update']))
		{
			$this->procUpdateFieldInfo();	
		}
		// user submitted delete field information
		else if(isset($_REQUEST['delete-field']))
		{
			$this->procDeleteField();	
		}

		else if(isset($_POST['register']))
		{
			$this->procRegister();
		}
		
		// user submitted forgot password form
		else if(isset($_POST['subforgotpass']))
		{
			$this->procForgotPW();
		}
		// user submitted forgot password form
		else if(isset($_POST['subcontactus']))
		{
			$this->procContactUs();
		}
		// user wants to edit a day in the daily budget table
		else if(isset($_REQUEST['editbudgetday']))
		{
			$this->procDailyBudgetEdit();
		}
		
		else if(isset($_REQUEST['subforgotuser']))
		{
			$this->procForgotLogin();	
		}
		// user submitted edit account form
		else if(isset($_POST['account-update']))
		{
			$this->procUpdateAccount();	
		}
		
		//logout - assume the user is logged in
		else if($irrigationScheduler->session->logged_in)
		{
				$this->procLogout();
		}
		else // should not reach here
		{
				$this->procLogout();
		}
		
	}
	
	// process the user login, if successful, user is logged in
	// if errors found, user is redirected to correct the issues
	function procLogin()
	{
		global $irrigationScheduler, $iform, $validation;
		//login attempt

		$retval = $irrigationScheduler->session->login($_REQUEST['username'], $_REQUEST['password'], isset($_REQUEST['remember']));
		// successful login
		if($retval)
		{
				$uName = mysql_real_escape_string($_REQUEST['username']);
				header("Location: ".$irrigationScheduler->session->basepath."");
		}
		else
		{
			// login failed
			$_SESSION['value_array'] = $_REQUEST;
			$_SESSION['error_array'] = $iform->getErrorArray();
			header("Location: index.php");	
		}
	}

	// process user registration, if successful, the user is added
	// to the grower database, logged in and redirected to the "logged-in"
	// page.
	// If errors found, the user is redirected to correct the issues
	function procRegister()
	{
		global $isession, $iform, $validation;
		
		// set the username to lowercase     
		if(ALL_LOWERCASE_I)
		{
         $_POST['reg-username'] = strtolower($_POST['reg-username']);
      	}
      /* Registration attempt */
      $retval = $isession->register($_POST['reg-username'], $_POST['reg-password'], $_POST['reg-email']);
      
      /* Registration Successful */
      if($retval == 0){
         $_SESSION['regname'] = $_POST['reg-username'];
         $_SESSION['regsuccess'] = true;
         header("Location: successful-registration.php");
      }
      /* Error found with form */
      else if($retval == 1){
         $_SESSION['value_array'] = $_POST;
         $_SESSION['error_array'] = $iform->getErrorArray();
         header("Location: ".$isession->basepath."");
      }
      /* Registration attempt failed */
      else if($retval == 2){
         $_SESSION['reguname'] = $_POST['reg-username'];
         $_SESSION['regsuccess'] = false;
         header("Location: register-error.php");
      }	
	}	
	// process user inputs for creating a new field
	// if successful, a new field row is added to the db and linked
	// to the grower, as well as create a new row in the individual 
	// field db
	// TODO -- Calculate values to put into the field and individField dbs
	function procUpdateCropDefault()
	{
		header("Location: ".$session->basepath);
	}
	function procAddField()
	{
		global $irrigationScheduler, $database;
		
		// add a field attempt
		$retval = $irrigationScheduler->session->addField($_POST['field-name'], $_POST['year'], $_POST['station'], $_POST['weather_network'], $_POST['crop'], $_POST['soil-type']);
		// successful add
		if($retval)
		{
			require_once("irrigationScheduler.class.php");
			$scheduler = new irrigationScheduler($_SESSION['field'], 1);
			if($irrigationScheduler->session->isMobileDevice > 0)
			{
					header("Location: ".$irrigationScheduler->session->basepath."&action=field-added");
			}
			else
				header("Location: ".$irrigationScheduler->session->basepath."");
		}
		else // failed
		{
			$_SESSION['value_array'] = $_POST;
			if($irrigationScheduler->session->isMobileDevice > 0)
				header("Location: ".$irrigationScheduler->session->basepath."&action=add-a-field");
			else
				header("Location: ".$irrigationScheduler->session->basepath."");
		}
	}
	
	// gets grower fields from db if they exist
	// if they they exist, then it also displays the available years
	// as well as gives the user the choice of update type, such as
	// viewing/editing a specific field or modifying the advanced
	// field information for a specific field/year combo
	function procGetFieldInfo()
	{
		global $isession;
		global $idatabase;
		$_SESSION['field'] = GetSQLValueString($_POST['field'],"int");
		if(isset($_POST['update-type']) )
		{
			if( $_POST['update-type'] == 1)
			{
				header("Location: ".$isession->basepath."&action=update-field");
			}
			elseif ($_POST['update-type'] == 2)
			{
				header("Location: ".$isession->basepath."&action=advanced-update-field");
			}
			else
			{
				header("Location: ".$isession->basepath."&action=my-fields");
			}
		}
		else
		{
			header("Location: ".$isession->basepath."&action=my-fields");
		}

	}
	
	// gets user inputs for updating an existing field
	// assumes that at least one field exists already
	function procUpdateAdvFieldInfo()
	{
		global $database; 
		global $irrigationScheduler;
		$useNDFDforecast = 0;
		if(isset($_REQUEST['chkUseNDFD']))
		{
			$useNDFDforecast = 1;
		}

		$useVWC = 0;
		if(isset($_REQUEST['chkDispVWC']))
		{
			$useVWC = 1;
		}

		$notifygrower = 0;
		if(isset($_REQUEST['chkNotifyMe']) && mysql_real_escape_string($_REQUEST['chkNotifyMe']) == "on")
		{
			$notifygrower = 1; //mysql_real_escape_string($_REQUEST['chkNotifyMe']);
		}
		$notifypercent = 100;
		if(isset($_REQUEST['notifymecheck']) && mysql_real_escape_string($_REQUEST['notifymecheck']) == 'on' && isset($_REQUEST['notifypercent']))
		{
			$notifypercent = mysql_real_escape_string($_REQUEST['notifypercent']);
		}
		$notifyaddress = "";
		if(isset($_REQUEST['notifyaddress']))
		{
			$notifyaddress = mysql_real_escape_string($_REQUEST['notifyaddress']);
		}
		$notifyHour = "";
		if(isset($_REQUEST['notifyhour']))
		{
			$notifyHour = mysql_real_escape_string($_REQUEST['notifyhour']);
		}
		$groundWetted = "100";
		if(isset($_REQUEST['groundwetted']))
		{
			$groundWetted = mysql_real_escape_string($_REQUEST['groundwetted']);
		}
		$notificationtype = "0";
		if(isset($_REQUEST['alertmethod']))
		{
			$notificationtype = mysql_real_escape_string($_REQUEST['alertmethod']);
		}
		$notifynumber = "";
		if(isset($_REQUEST['notifyphone']))
		{
			$notifynumber = mysql_real_escape_string($_REQUEST['notifyphone']);
		}
		$txtprovider = "-1";
		if(isset($_REQUEST['serviceprovider']))
		{
			$txtprovider = mysql_real_escape_string($_REQUEST['serviceprovider']);
		}

		$postCuttingFlatDays = "-1";
		if(isset($_REQUEST['pckfd']))
		{
			$postCuttingFlatDays = mysql_real_escape_string($_REQUEST['pckfd']);
		}
		$postCuttingRecoveryDays = "-1";
		if(isset($_REQUEST['pckrd']))
		{
			$postCuttingRecoveryDays = mysql_real_escape_string($_REQUEST['pckrd']);
		}
		
		//die("Test");

		$deleteSQL = sprintf("DELETE from irrigation.tblindividfield where measdpcntAvail = null and modified=0 and irrig=0 and foragecutting <> 1 and fieldID = %s",GetSQLValueString($_SESSION['field'], "int"));
		$database->query($deleteSQL);
		
		$updateSQL = sprintf("UPDATE irrigation.tblfield 
  											SET soilFC=%s, 
  												soilAWC=%s, 
  												plantDate=%s, 
  												growth10PcntDate=%s, 
  												growthMaxDate=%s, 
  												growthDeclineDate=%s, 
  												growthEndDate=%s, 
  												seasonEndDate=%s,
  												rz_val1=%s,
  												rz_val2=%s,
  												kc1=%s,
  												kc2=%s,
  												kc3=%s,
  												applicationrate=%s,
  												useNDFDforecast=%s,
  												notifygrower=%s,
  												notifypercent=%s,
  												notifyaddress=%s,
  												notificationtype=%s,
  												notifynumber=%s,
  												txtprovider=%s,
  												notifyhour=%s,
  												groundWetted=%s,
  												postcuttingflatdays=%s,
  												postcuttingrecoverydays=%s,
  												DispVWC=%s,
  												mad=%s 
  										 	WHERE fieldID=%s",
                       GetSQLValueString($_POST['field-capacity'], "double"),
                       GetSQLValueString($_POST['sawhc'], "double"),
                       GetSQLValueString(date("z",strtotime($_POST['emergence-date']))+1, "int"),
                       GetSQLValueString(date("z",strtotime($_POST['water-budget-date']))+1, "int"),
                       GetSQLValueString(date("z",strtotime($_POST['ccc-10']))+1, "int"),
                       GetSQLValueString(date("z",strtotime($_POST['ccc-70']))+1, "int"),
                       GetSQLValueString(date("z",strtotime($_POST['crop-maturation']))+1, "int"),
                       GetSQLValueString(date("z",strtotime($_POST['seasonend']))+1, "int"),
                       GetSQLValueString($_POST['root-depth'], "double"),
                       GetSQLValueString($_POST['mmrzd'], "double"),
                       GetSQLValueString($_POST['icc'], "double"),
                       GetSQLValueString($_POST['fccc'], "double"),
                       GetSQLValueString($_POST['fcc'], "double"),
                       GetSQLValueString($_POST['IrrApRt'], "double"),
                       GetSQLValueString($useNDFDforecast, "int"),
                       GetSQLValueString($notifygrower, "int"),
                       GetSQLValueString($notifypercent, "int"),
                       GetSQLValueString($notifyaddress, "text"),
                       GetSQLValueString($notificationtype, "int"),
                       GetSQLValueString($notifynumber, "text"),
                       GetSQLValueString($txtprovider, "int"),
                       GetSQLValueString($notifyHour, "text"),
                       GetSQLValueString($groundWetted, "double"),
                       GetSQLValueString($postCuttingFlatDays, "double"),
                       GetSQLValueString($postCuttingRecoveryDays, "double"),
                       GetSQLValueString($useVWC, "int"),
                       GetSQLValueString($_POST['mad'], "double"),
                       GetSQLValueString($_SESSION['field'], "int"));
                       
  			$Result1 = $database->query($updateSQL);
  	
  	unset($_SESSION['lastUpdate']);
		require_once("irrigationScheduler.class.php");
		$scheduler = new irrigationScheduler($_SESSION['field'], 1);
  	if($irrigationScheduler->session->isMobileDevice > 0 )
  	{  		
			header("Location: ".$irrigationScheduler->session->basepath."&action=soil-water-chart");
		}
		else
		{
			header("Location: ".$irrigationScheduler->session->basepath."");
		}
	}

	// gets user inputs for updating an existing field
	// assumes that at least one field exists already
	function procUpdateFieldInfo()
	{
		global $isession, $iform, $validation, $idatabase;	
		$cropID = GetSQLValueString($_POST['crop'],"int");
		$soilID = GetSQLValueString($_POST['soil-type'],"int");


		$soilDefaultQuery = "select * from tblsoildefaults where soilID = $soilID";
		$soilDefaultResults = $idatabase->query_irrigation($soilDefaultQuery);

		$cropDefaultsQuery = "select * from tblcropdefaults where cropdefaultsid = $cropID";
		$cropDefaultsResults = $idatabase->query_irrigation($cropDefaultsQuery);
		
		if(!$cropDefaultsResults || !$soilDefaultResults)
		{
			//"Crop Defaults NOT Found";
			$plantDate = rand(0,50);
			$growth10PctDate = $plantDate + rand(0,5);
			$growthMaxDate = $growth10PctDate + rand(0,5);
			$growthDeclineDate = $growthMaxDate + rand(0,5);
			$growthEndDate = $growthDeclineDate + rand(0,5);
			$kc1 = rand(0,5);
			$kc2 = rand(0,5);
			$kc3 = rand(0,5);
			$mad = rand(0,5);
			$rz_val1 = rand(0,5);
			$rz_val2 = rand(0,5);
			$rz_val3 = rand(0,5);						
		}
		else //crop defaults found
		{

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
			$plantDate = $cropDefaultsRow['plantDate'];
			$growth10PctDate = $cropDefaultsRow['growth10PcntDate'];
			$growthMaxDate = $cropDefaultsRow['growthMaxDate'];
			$growthDeclineDate = $cropDefaultsRow['growthDeclineDate'];
			$growthEndDate = $cropDefaultsRow['growthEndDate'];
		}


	  $updateSQL = sprintf("UPDATE tblfield SET	soilID=%s, cropID=%s, weatherStnID=%s, weather_network=%s, tableName=%s, fieldName=%s, year=%s, soilfc=%s, soilwp=%s, soilawc=%s, plantdate=%s, growthmaxdate=%s, growthdeclinedate=%s, growthenddate=%s, seasonenddate=%s, kc1=%s, kc2=%s, kc3=%s, mad=%s, rz_val1=%s, rz_val2=%s WHERE fieldID=%s",
	                       GetSQLValueString($_POST['soil-type'], "int"),
	                       GetSQLValueString($_POST['crop'], "int"),
	                       GetSQLValueString($_POST['station'], "text"),
	                       GetSQLValueString($_POST['weather_network'], "int"),
	                       GetSQLValueString($_POST['field-name'], "text"),
	                       GetSQLValueString($_POST['field-name'], "text"),
	                       GetSQLValueString($_POST['year'], "int"),
	                       GetSQLValueString($soilFC, "double"),
	                       GetSQLValueString($soilWP, "double"),
	                       GetSQLValueString($soilAWC, "double"),
	                       GetSQLValueString($plantDate, "int"),
	                       GetSQLValueString($growth10PctDate, "int"),
	                       GetSQLValueString($growthMaxDate, "int"),
	                       GetSQLValueString($growthDeclineDate, "int"),
	                       GetSQLValueString($growthEndDate, "int"),
	                       GetSQLValueString($kc1, "double"),
	                       GetSQLValueString($kc2, "double"),
	                       GetSQLValueString($kc3, "double"),
	                       GetSQLValueString($mad, "double"),
	                       GetSQLValueString($rz_val1, "double"),
	                       GetSQLValueString($rz_val2, "double"),
	                       GetSQLValueString($_SESSION['field'], "int")
													);

  	$Result1 = $idatabase->query_irrigation($updateSQL);

		require_once("irrigation-scheduler-class.php");
		$scheduler = new irrigationScheduler($_SESSION['field'], 1);
  	if($isession->isMobileDevice > 0)
  	{
			header("Location: ".$isession->basepath."&action=soil-water-chart");
		}
		else
		{
			header("Location: ".$isession->basepath."");
		}
	}
	
	// lists field/year combos that can be deleted by the
	// user
	function procDeleteField()
	{
		global $irrigationScheduler, $database;
		$qry = "delete from irrigation.tblfield where fieldid = ".GetSQLValueString($_SESSION['field'], "int");
		$_SESSION['field'] = -1;
		$database->query($qry);
		header("Location: ".$irrigationScheduler->session->basepath."&action=my-fields");
	}
		
	// processes the user logout
	// redirects the user to the front page of the website
	// if successful
	function procLogout()
	{
		global $irrigationScheduler;
		$retval = $irrigationScheduler->session->logout();
		header("Location: ".$irrigationScheduler->session->basepath."");
	}
	

	
	// procForgotPW - takes a username and sends a newly generated password
	// to the user's email address
	function procForgotPW()
	{
		global $database, $irrigationScheduler, $iform;
		/* Username error checking */
		$subuser = $_POST ['user'];
		$field = "user"; //Use field name for username
		if (! $subuser || strlen ( $subuser = trim ( $subuser ) ) == 0) {
			$iform->setError ( $field, "* Username not entered<br />" );
		} else {
			/* Make sure username is in database */
			$subuser = mysql_real_escape_string ( $subuser );
			if (strlen ( $subuser ) < 5 || strlen ( $subuser ) > 30 || ! eregi ( "^([0-9a-z])+$", $subuser ) || (! $database->usernameTaken ( $subuser ))) {
				$iform->setError ( $field, "* Username does not exist<br />" );
			}
		}

		/* Errors exist, have user correct them */
		if ($iform->num_errors > 0) {
			//die("Errors? ".$iform->num_errors);
			$_SESSION ['value_array'] = $_POST;
			$_SESSION ['error_array'] = $iform->getErrorArray ();
			$_SESSION ['forgotpass'] = false;
		} else {
			/* Generate new password and email it to user */
			/* Generate new password */
			$newpass = $irrigationScheduler->session->generateRandStr ( 8 );

			/* Get email of user */
			$usrinf = $database->getUserInfo ( $subuser );
			$email = $usrinf ['email'];

			/* Attempt to send the email with new password */
			$from = EMAIL_FROM_NAME . ' <' . EMAIL_FROM_ADDR . '>';
			$subject = 'AgWeatherNet Site - Your new password';
			$ipAddress = mysql_real_escape_string($_SERVER['REMOTE_ADDR']);
			if(strlen($ipAddress) > 0)
				$ip = " based on a request from IP $ipAddress";
			$body = "$subuser,\n\nWe've generated a new password for you$ip, you can use this new password with your username to log in to AgWeatherNet.\n\n Username:  $user \n New Password: $newpass \n\nIt is recommended that you change your password to something that is easier to remember, which can be done by going to the My Account page after signing in.\n\n- AgWeatherNet Administrator";
			if (SendMsg($email, $subject, $from, $body, $bodyHTML)) {
				/* Email sent, update database */
				$database->updateUserField ( $subuser, "password", md5 ( $newpass ) );
				$_SESSION ['forgotpass'] = true;
			} else { /* Email failure, do not change password */
				$_SESSION ['forgotpass'] = false;
			}
		}
		header("Location: ".$irrigationScheduler->session->basepath."&action=forgotpass");
	}

	// procForgotPW - takes a username and sends a newly generated password
	// to the user's email address
	function procContactUs()
	{
		global $irrigationScheduler, $iform, $validation;

		$name = $_SESSION['name'] = $_POST['name'];
		$email = $_SESSION['email'] = $_POST['email'];
		$message = $_SESSION['message'] = $_POST['message'];
		$captcha = $_SESSION['captcha'] = $_POST['captcha'];
		$result = $_SESSION['result'];
		$subject = "[Irrigation-Scheduler] Contact Form 20140512";
	
		// validate form values on server-side
		// checks a string against naming conventions such as being alphanumeric
		$name = trim($name);
		if( empty($name) )
		{
			$iform->setError ( "name", "* Username not entered<br />" );
		}
		elseif(!eregi("^([0-9a-z])*$", $name))
		{
			$iform->setError ( "name", "Username must only contain alphanumeric characters<br />" );
		}
		$email = trim($email);
		if(empty($email))
		{
			$iform->setError ( "email", "Email not entered<br />" );			
		}
		else
		{
			if(eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $email)) { 
			}else{ 
				$iform->setError ( "email", "Not in a valid email form<br />" );			
			}
		}
		$message = trim($message);
		
		if(strlen($message) == 0)
		{
			$iform->setError ( "message", "Message not entered<br />" );			
		}

		if(strlen($captcha) == 0)
		{
			$iform->setError ( "captcha", "Captcha not entered<br />" );
		}
		else
		{
			if($result == $captcha)
			{
			}
			else
			{
				$iform->setError ( "captcha", "Incorrect captcha value" . $captcha . " result = " . $result."<br />" );
			}
		}
	
		// Errors exist, have user correct them 
		if ($iform->num_errors > 0) {
			$_SESSION ['value_array'] = $_POST;
			$_SESSION ['error_array'] = $iform->getErrorArray ();
			
		} else {
				// Attempt to send the email with new password 
				SendMsg(array("troy_peters@wsu.edu"), $subject, $email, "From $name: ".$message,"From $name: ".$message);
		}
	
		header("Location: ".$irrigationScheduler->session->basepath."&action=contact-us&asd=".$iform->num_errors);
	}
	  /**
    * procForgotLogin - Validates the given username then if
    * everything is fine, a new password is generated and
    * emailed to the address the user gave on sign up.
    */
   function procForgotLogin()
   {
		global $database, $irrigationScheduler,  $iform;
		/* Username error checking */
		$subemail = mysql_real_escape_string($_POST ['email']);
		$field = "email"; //Use field name for username
		if (! $subemail || strlen ( $subemail = trim ( $subemail ) ) == 0) {
			$iform->setError ( $field, "* Email not entered<br />" );
		} else {
			/* Check if valid email address */
			$regex = "^[_+a-z0-9-]+(\.[_+a-z0-9-]+)*" . "@[a-z0-9-]+(\.[a-z0-9-]{1,})*" . "\.([a-z]{2,}){1}$";
			if (! eregi ( $regex, $subemail )) {
				$iform->setError ( $field, "* <br />Email invalid" );
			}
			/* Make sure email is in database */
			$subemail = mysql_real_escape_string ( $subemail );
			

			if ((! $database->emailTaken ( $subemail ))) {
				$iform->setError ( $field, "* Email address not found.  If you forgot the email address you signed up with, please contact us at <a href='mailto:weather@wsu.edu'>weather@wsu.edu</a> for further assistance.<br />" );
			}
		}

		/* Errors exist, have user correct them */
		if ($iform->num_errors > 0) {
			$_SESSION ['value_array'] = $_POST;
			$_SESSION ['error_array'] = $iform->getErrorArray ();
			$_SESSION ['forgotuser'] = false;
		} else {
			/* Generate new password and email it to user */
			/* Generate new password */
			$usernames = $database->getUsernames ( $subemail );
			if(mysql_num_rows($usernames) > 0)
			{
				while($thisUsername = mysql_fetch_assoc($usernames))
				{
						$from = EMAIL_FROM_NAME . ' <' . EMAIL_FROM_ADDR . '>';
						$subject = 'AgWeatherNet Site - Your username';
						$body = $from." We have recovered a username associated with this email address.  \n\nThe username is:  ".$thisUsername['username'].",\n\nIt is recommended that you change your password to something that is easier to remember, which can be done by going to the My Account page after signing in.\n\n - AgWeatherNet Administrator";
						$bodyHTML = $body;
						
						/* Attempt to send the email with username */
						if (SendMsg($subemail, $subject, $from, $body, $bodyHTML)) {
							/* Email sent, update database */
							$_SESSION ['forgotuser'] = true;
						} else { /* Email failure, do not change password */
							$_SESSION ['forgotuser'] = false;
						}
				}
			}
		}
		header("Location: ".$irrigationScheduler->session->basepath."&action=forgotname");
   }
  
  function procBudgetPreviousNext()
  {
		if(isset($_POST['BUDGETSTARTROW']))
			$_SESSION['BUDGETSTARTROW']  = mysql_real_escape_string($_POST['BUDGETSTARTROW']);
		else
			$_SESSION['BUDGETSTARTROW'] = 0;
  	header("Location: ".$isession->basepath."&action=daily-budget-existing-fields");
  } 
   /**
    * procEditAccount - Attempts to edit the user's account
    * information, including the password
	* assumes that the user is logged in
    */
	function procEditAccount()
	{
	  global $isession, $iform;
	  /* Account edit attempt */
	  $retval = $isession->editAccount($_POST['new-password'], $_POST['new-email']);
	
	  /* Account edit successful */
	  if($retval)
	{
	     $_SESSION['useredit'] = true;
	     header("Location: ".$isession->referrer);
	  }
	  /* Error found with form */
	  else
	{
	     $_SESSION['value_array'] = $_POST;
	     $_SESSION['error_array'] = $iform->getErrorArray();
	     header("Location: " . $isession->referrer);
	  }
	}
   
	function procDailyBudgetEdit()
	{
		global $idatabase;
		global $isession;
		$_SESSION['doy'] = GetSQLValueString($_POST['doy']);
			
		if($isession->isMobileDevice > 0)
		{
			header("Location: ".$isession->basepath."&action=editdailybudgetday");
		}
	}
	
	function procSaveDashboardEdit()
	{
		global $database;
		global $irrigationScheduler;
		$irrigation = GetSQLValueString($_REQUEST['irrigation'],"double") * GetSQLValueString($irrigationScheduler->row_FieldInfo['applicationrate'],"double");


		$doy = date("z") + 1;

  	$updateSQL = sprintf("UPDATE irrigation.tblindividfield SET irrig=%s WHERE fieldid = %s  and status = 0 and doy = %s",
                       GetSQLValueString($irrigation, "double"),
                       GetSQLValueString($_SESSION['field'], "int"),
                       GetSQLValueString($doy, "int")      
		);
	  $Result1 = $database->query($updateSQL);
	
		$query_fieldAudit = sprintf("insert into irrigation.table_fieldaudit(fieldid,growerid,name,description,typeid,stationID,networkID)values(%s,%s,%s,%s,%s,%s,%s)",
			GetSQLValueString($_SESSION['field'], "int"),
			GetSQLValueString($_SESSION['growerID'], "int"),
			GetSQLValueString("dashboardedit", "text"),
			GetSQLValueString($updateSQL, "text"),
			GetSQLValueString(1, "int"),
			GetSQLValueString($irrigationScheduler->row_FieldInfo['weatherStnID'], "text"),
			GetSQLValueString($irrigationScheduler->row_FieldInfo['weather_network'], "text")
			);
		$database->query($query_fieldAudit);
		$irrigationScheduler = new irrigationScheduler($_SESSION['field'], 1);

	}	
	
	function procSaveDailyBudgetEdit()
	{
		global $database;
		global $irrigationScheduler;
		if(isset($_REQUEST['rainchanged']))
		{
			$rain = GetSQLValueString($_REQUEST['rain'],"double");
		}
		if(isset($_REQUEST['BUDGETSTARTROW']))
		{
			$BUDGETSTARTROW = mysql_real_escape_string($_REQUEST['BUDGETSTARTROW']);
			$_SESSION['BSR'] = $BUDGETSTARTROW;
		}
		if(isset($_REQUEST['irrigationchanged']) )
		{
			$irrigation = GetSQLValueString($_REQUEST['irrigation'],"double") * GetSQLValueString($_SESSION['applicationrate'],"double");
		}
		
		if(isset($_REQUEST['usemeasured']))
		{
			$modifiedValue = GetSQLValueString($_REQUEST['usemeasured']);
		}
		else
		{
			$modifiedValue = 0;
		}
		
		if($modifiedValue == "on") 
		{
			$modifiedValue = 1;
		}
		else
		{
			$modifiedValue = 0;
		}
		if(isset($_REQUEST['mswachanged']) && $_REQUEST['mswachanged'] == 1)
		{
			$mswa = GetSQLValueString($_REQUEST['mswa'],"double");
			$whichPcnt = "measdPcntAvail";
		}
		else
		{
			$mswa = GetSQLValueString($_REQUEST['mswa'],"double");
			$whichPcnt = "measdPcntAvail=Null, pcntAvail";
		}
		if(isset($_REQUEST['refETchanged']) && isset($_REQUEST['refET']))
		{
			$refET = GetSQLValueString($_REQUEST['refET'],"double");
		}
		$foreageCut = 0;
		if(isset($_REQUEST['foragecut']))
		{
			if($_REQUEST['foragecut'] == "on" || $_REQUEST['foragecut'] == 'true') 
			{
				$foreageCut = 1;
			}
			else
			{
				$foreageCut = 0;
			}
		}
		else
		{
			$forageCut = 0;
		}
		$doy = $_REQUEST['doy'];

			

		require_once("irrigationScheduler.class.php");
		$scheduler = new irrigationScheduler($_SESSION['field']);
		if($scheduler->row_FieldInfo['DispVWC'] == 1)
		{
			//Query to return wpWater and fcWater for this DOY for this field
			//If DispVWC == 1 then  
			$wpWater = $scheduler->row_tblIndividField[$doy]['wpWater'];
			$fcWater = $scheduler->row_tblIndividField[$doy]['fcWater'];
			$rzDepth = $scheduler->row_tblIndividField[$doy]['rootDepth'];
			
			//small issue, if it is already mswa then don't want to change the value??
			$mswa = ($mswa /100 - $wpWater/$rzDepth )/ ($fcWater/$rzDepth - $wpWater/$rzDepth) * 100;

		}
		if($mswa > 100)
			$mswa = 100;
		if($mswa < 0)
			$mswa = 0;
  	$updateSQL = sprintf("UPDATE irrigation.tblindividfield SET foragecutting=%s, irrig=%s, ".$whichPcnt."=%s, modified=$modifiedValue WHERE fieldid = %s  and status = 0 and doy = %s",
                       GetSQLValueString($foreageCut, "int"),
                       GetSQLValueString($irrigation, "double"),
                       GetSQLValueString($mswa, "double"),
                       GetSQLValueString($_SESSION['field'], "int"),
                       GetSQLValueString($doy, "int")      
		);
		$_SESSION['whichday'] = $doy;
	  $Result1 = $database->query($updateSQL);
	
		$query_fieldAudit = sprintf("insert into irrigation.table_fieldaudit(fieldid,growerid,name,description,typeid,stationID,networkID)values(%s,%s,%s,%s,%s,%s,%s)",
			GetSQLValueString($_SESSION['field'], "int"),
			GetSQLValueString($_SESSION['growerID'], "int"),
			GetSQLValueString("dailybudgetedit", "text"),
			GetSQLValueString($updateSQL, "text"),
			GetSQLValueString(1, "int"),
			GetSQLValueString($scheduler->row_FieldInfo['weatherStnID'], "text"),
			GetSQLValueString($scheduler->row_FieldInfo['weather_network'], "text")
			);
		$database->query($query_fieldAudit);
		

		$scheduler = new irrigationScheduler($_SESSION['field'], 1);

		
	}
};

/* Initialize process */
require_once('irrigationScheduler.class.php');
$irrigationScheduler = new irrigationScheduler();
$process = new Process;

?>
