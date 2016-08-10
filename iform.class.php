<?php
//iform.php
class iForm
{
	var $values = array(); // holds submitted form field values
	var $errors = array(); // holds submitted form error messages
	var $num_errors = 0; 		// number of errors in a form
	
	// class constructor
	function __construct()
	{
		
		// get form values and error arrays
		if(isset($_SESSION['value_array']))
		{
			$this->values = $_SESSION['value_array'];
			unset($_SESSION['value_array']);
		}
		if( isset($_SESSION['error_array']))
		{
			$this->errors = $_SESSION['error_array'];
			$this->num_errors = count($this->errors);	
			
			unset($_SESSION['error_array']);
		}
		else
		{
			//echo "No Errors";
			$this->num_errors = 0;
		}
	}
	
	// store the value typed into a form field
	function setValue($field, $value)
	{
		$this->values[$field] = $value;
	}
	
	/**
	 * setError - Records new form error given the form
	 * field name and the error message attached to it.
	 */
	public function setError($field, $errmsg) {
		$this->errors [$field] = $errmsg;
		$this->num_errors = count ( $this->errors );
	}

	// store a form error into an array using the field name to store the msg
	function setFormError($msg)
	{
		$this->errors[] = $msg;
		$this->num_errors++;	
	}
	
	// return an error message from a given field, if none exists, we return an empty string
	function returnMsg($field)
	{
		// check if the field
		if(array_key_exists($field, $this->errors))
		{
			// multiple error messages for a field may exist
			return $this->errors[$field];
		}
		else
		{
			return "";
		}
			
	}
	   /**
    * value - Returns the value attached to the given
    * field, if none exists, the empty string is returned.
    */
   function value($field){
      if(array_key_exists($field,$this->values)){
         return htmlspecialchars(stripslashes($this->values[$field]));
      }else{
         return "";
      }
   }
	//return the array of error messages
	function getErrorArray()
	{
		return $this->errors;
	}

	/**
	 * error - Returns the error message attached to the
	 * given field, if none exists, the empty string is returned.
	 */
	public function error($field) {
		if (array_key_exists ( $field, $this->errors )) {
			return "<font size=\"2\" color=\"#ff0000\">" . $this->errors [$field] . "</font>";
		} else {
			return "";
		}
	}		
		
};

$iform = new iForm();
//error_log("New IForm");
?>