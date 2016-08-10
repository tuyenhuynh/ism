<?php
require_once ('Mail.php');
require_once ('Mail/mime.php');
if(!function_exists('SendMsg'))
{
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
	$bcc = array('gerrit.hoogenboom@wsu.edu', 'sehill@wsu.edu');
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
}
}

class Validation
{
	var $errors = array(); // store validation errors
	var $num_errors = 0; 		// number of validation errors
	
	// class constructor
	function Validation()
	{
		// get error array and set the number of errors
		if(isset($_SESSION['vErrors_array']) )
		{
			$this->errors = $_SESSION['vErrors_array'];
			$this->num_errors = count($this->errors);
			
			unset($_SESSION['vErrors_array']);
		}
		else
		{
			$this->num_errors = 0;	
		}
	}
	

	function getValidationErrors()
	{
		return $this->errors;	
	}
	
	function listValidationErrors()
	{
			
	}
	
	
	function setValidationError( $errorMsg)
	{
		$this->errors[] = $errorMsg;
		$this->num_errors++;
	}
	
	
	function sendMail($name, $emailFrom, $subject, $message)
	{
		$email_to 	= $emailFrom;
		$headers	= "From: " . EMAIL_FROM_NAME_I . "<" . EMAIL_FROM_ADDR_I . ">\r\n" . "Reply-To: " . EMAIL_FROM_NAME_I . "<" . EMAIL_FROM_ADDR_I . ">\r\n";

		
		// test email, subject, message, etc. for injection
		if($this->contains_bad_str($emailFrom) && $this->contains_bad_str($subject) && $this->contains_bad_str($message))
		{
			$this->setValidationError("Contains injection code");
			return false;	
		}
		else
		{
			// test for newlines in email
			if($this->contains_newlines($email_to))
			{
				$this->setValidationError("Contains newlines");
				return false;	
			}
			else // no injection or newlines
			{
				if(mail($email_to, $subject, $message, $headers))
				{

					$this->setValidationError("Sent");					
					return true;		
				}
				else // couldn't send mail
				{
					$this->setValidationError("Could not send email");					
					return false;
				}
			}
		}		
		
	}
	// returns false if it cannot submit due to validation errors or can't send the email
	function submitEmail($name, $email, $subject, $message)
	{
		$email_to 	= EMAIL_FROM_ADDR_I;
		$headers	= "From: " . $name . "<" . $email . ">\r\n" . "Reply-To: " . $name . "<" . $email . ">\r\n";
		
		// test email, subject, message, etc. for injection
		if($this->contains_bad_str($email) && $this->contains_bad_str($subject) && $this->contains_bad_str($message))
		{
			$this->setValidationError("Contains injection code");

			return false;	
		}
		else
		{
			// test for newlines in email
			if($this->contains_newlines($email))
			{
				$this->setValidationError("Contains newlines");
	
				return false;	
			}
			else // no injection or newlines
			{
				if(mail($email_to, $subject, $message, $headers))
				{
					return true;		
				}
				else // couldn't send mail
				{
					$this->setValidationError("Could not send email");
							
					return false;
				}
			}
		}
	}
	// checks a string against naming conventions such as being alphanumeric
	function validateName($name){
		$name = trim($name);
		
		if( empty($name) )
		{
			$this->setValidationError( "Username not entered");

			return false;
		}
		else
		{
			// return error if the username is not alphanumeric
			if(!eregi("^([0-9a-z])*$", $name))
			{
				$this->setValidationError( "Username must only contain alphanumeric characters");
	
				return false;
			}
			else
				return true;
		}
	}

	// checks a string against naming conventions such as being alphanumeric
	function validateFieldName($name){
		$name = trim($name);
		if( empty($name) )
		{
			$this->setValidationError( "Field Name not entered");

			return false;
		}
		else
		{
				return true;
		}
	}	


	// checks a string against naming conventions such as being alphanumeric
	function validateStation($station){
		$station = trim($station);

		if( empty($station) || $station == -1)
		{
			
			$this->setValidationError( "Station Not Selected");

			return false;
		}
		else
		{

				return true;
		}
	}	

	// checks a string against naming conventions such as being alphanumeric
	function validateCrop($crop){
		$crop = trim($crop);

		if( empty($crop) || $crop == -1)
		{
			
			$this->setValidationError( "Crop Not Selected");

			return false;
		}
		else
		{

				return true;
		}
	}	

	// checks a string against naming conventions such as being alphanumeric
	function validateSoilType($soil){
		$soil = trim($soil);

		if( empty($soil) || $soil == -1)
		{
			
			$this->setValidationError( "Soil Type Not Selected");

			return false;
		}
		else
		{

				return true;
		}
	}			
	function validateNumber($number)
	{
		$number = trim($number);
		if(empty($number))
		{
			$this->setValidationError("Field Year not entered.");

			return false;
		}
		else
		{
			if(!eregi("^([0-9])*$", $number))
			{
				$this->setValidationError("Field Year must only contain numeric characters.");
	
				return false;
			}
			else
			{
				return true;
			}
		}
		
	}
	function validatePassword($password)
	{
		// remove any spaces
		$password = trim($password);	
		if(empty($password))
		{
			$this->setValidationError( "Password not entered");

			return 0;
		}
		else
		{
			return 1;	
			
		}
	}
	
	function validateEmail($email){
		// remove any spaces
		$email = trim($email);
		if(empty($email))
		{
			$this->setValidationError("Email not entered");
			
			return 0;
		}
		else
		{
			if(eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $email)) { 
				return 1;
			} 
		else{ 
				$this->setValidationError( "Not in a valid email form");
				
				return 0;
			}
		}
	}
	
	function validateMessage($message){
		$message = trim($message);
		
		if(strlen($message) == 0)
		{
			$this->setValidationError("Message not entered");

			return 0;
		}
		else
		{
			return 1;
		}
	}
	
	function validateCaptcha($captcha, $result){
		if(strlen($captcha) == 0)
		{
			$this->setValidationError( "Captcha not entered");
			
			return 0;
		}
		else
		{
			if($result == $captcha)
			{
				return 1;
			}
			else
			{
				$this->setValidationError( "Incorrect captcha value" . $captcha . " result = " . $result);	
				return 0;
			}
		}
	}
	
	function createRandomNumber()
	{
		return rand(0,10);
	}
	
	function setRandomNumbers()
	{
		$rn1 = $this->createRandomNumber();
		$rn2 = $this->createRandomNumber();
		$result = $rn1 + $rn2;
		
		$_SESSION['rn1'] = $rn1;
		$_SESSION['rn2'] = $rn2;
		$_SESSION['result'] = $result;	
		
		return $result;
			
	}
	
	function contains_bad_str($str_to_test) {
	  $bad_strings = array(
					"content-type:"
					,"mime-version:"
					,"multipart/mixed"
			,"Content-Transfer-Encoding:"
					,"bcc:"
			,"cc:"
			,"to:"
	  );
	  foreach($bad_strings as $bad_string) {
		if(eregi($bad_string, strtolower($str_to_test))) 
		{
			$this->setValidationError( "Contains a bad string");  

		  return true;      
		}
	  }
	  
	  return false;
	}
	
	function contains_newlines($str_to_test) 
	{
	   if(preg_match("/(%0A|%0D|\\n+|\\r+)/i", $str_to_test) != 0) 
	   {
		   $this->setValidationError( "Contains newlines");
  
		 return true;
	   }
	   else
	   return false;
	
		// clear any messages
		$submit_msg = "";
	
		if(isset($_POST['submit']))
		{
		
			//check if captcha variables exist
			 if(strlen($_SESSION['rn1'])==0 && strlen($_SESSION['rn2'])==0) //not found
			 {
				 //create random numbers, set session variables for them
				 setRandomNumbers();
			 }
		
			$name = trim($_POST['name']);
			$email = trim($_POST['email']);
			$message = trim($_POST['message']);
			$captcha = trim($_POST['captcha']);
			$result = $_SESSION['result'];
			$subject = trim($_POST['subject']);
		
			//validating inputs
			if( validateName($name) && validateEmail($email) && validateMessage($message) && validateCaptcha($captcha, $result))
			{
				// returns a error value of zero when true
				$errorSubmission = submitEmail($name, $email, $subject, $message);
				if($errorSubmission == 0)
				{
					$submit_msg = "Email successfully sent";
					unset($_SESSION['rn1']);
					unset( $_SESSION['rn2']);
					unset( $_SESSION['result']);
					unset( $_SESSION['name']);
					unset( $_SESSION['email']);
					unset( $_SESSION['message']);
					unset( $_SESSION['captcha']);
					setRandomNumbers();		
				}
				else
				{	
					switch($errorSubmission)
					{
						case 1: $submit_msg = "Error: Please try again"; 
							break;
						case 2: $submit_msg = "$bad_string found. Suspected injection attempt - mail not being sent.";
							break;
						case 3: $submit_msg = "newline found in $str_to_test. Suspected injection attempt - mail not being sent.";
							break;
						default:
							$submit_msg = "Error: Please try again"; 
					}
				}
			}
			else //one or more of the inputs are invalid
			{
			
				if(validateName($name) == 0)
				{
					$submit_msg .= "<p>Could not validate name</p>" . $name;
					$_SESSION['name'] = '';
				}
				elseif(validateEmail($email) == 0)
				{
					$test = validateEmail($email);
					$submit_msg .= "<p> Could not validate email</p>" . $email;
					$_SESSION['email'] = '';
				}
				elseif(validateMessage($message) == 0)
				{
					$submit_msg .= "<p> Could not validate message</p>" . $message;
					$_SESSION['message'] = '';
				}
				elseif(validateCaptcha($_SESSION['captcha'], $result) == 0)
				{
					$submit_msg .= "<p> Could not validate captcha</p>";
					$_SESSION['captcha'] = '';
				}
			}
		}
		else // brand new page loading
		{
			 // check for session variables
			 // have the random numbers been set up?
			 if(strlen($_SESSION['rn1'])==0 && strlen($_SESSION['rn2'])==0) //not found
			 {
				 //create random numbers, set session variables for them
				 setRandomNumbers();
			 }
		}
	}
};

// Initialize validation
$validation = new Validation;
?>