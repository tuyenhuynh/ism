<?php
	class register
	{
		public function toHTML()
		{
			global $irrigationScheduler;
			global $iform, $validation;
			$register = "";
			if (isset ( $_SESSION ['forgotpass'] ) && $_SESSION['forgotpass']) {
				/**
				 * New password was generated for user and sent to user's
				 * email address.
				 */
				$register .= '<h1>Registration Successful</h1><br /><p>Your new password has been generated and sent to the email associated with your account. </p>';
				unset ( $_SESSION ['forgotpass'] );
			} else {
			/**
			 * Forgot password form is displayed, if error found
			 * it is displayed.
			 */
				$register .="<h2>Register</h2>";
				$register .="<form action='".$irrigationScheduler->session->processPath."' method='post'>";
				$register .= "<table width='100%'>";
				$register .= "<thead></thead><tbody>";
				$register .= "<tr>";
				$register .= "<td>Full Name:</td>";
				$register .= "<td>";
				$register .= "<input type='text' name='fullname' maxlength='30' value='".$iform->value ( 'fullname' )."' />";
				$register .= "<br/>".$iform->error ( 'fullname' );
				$register .= "</td>";
				$register .= "</tr>";
				$register .= "<tr>";
				$register .= "<td>Desired Username:</td>";
				$register .= "<td>";
				$register .= "<input type='text' name='username' maxlength='30' value='".$iform->value ( 'username' )."' />";
				$register .= "<br/>".$iform->error ( 'username' );
				$register .= "</td>";
				$register .= "</tr>";				
				$register .= "<tr>";
				$register .= "<td>Password:</td>";
				$register .= "<td>";
				$register .= "<input type='password' name='password' maxlength='30' value='".$iform->value ( 'password' )."' />";
				$register .= "<br/>".$iform->error ( 'password' );
				$register .= "</td>";
				$register .= "</tr>";				
				$register .= "<tr>";
				$register .= "<td>Confirm Password:</td>";
				$register .= "<td>";
				$register .= "<input type='password' name='passwordconfirm' maxlength='30' value='".$iform->value ( 'passwordconfirm' )."' />";
				$register .= "<br/>".$iform->error ( 'passwordconfirm' );
				$register .= "</td>";
				$register .= "</tr>";				
				$register .= "<tr>";
				$register .= "<td>Email Address:</td>";
				$register .= "<td>";
				$register .= "<input type='text' name='emailaddress' maxlength='30' value='".$iform->value ( 'emailaddress' )."' />";
				$register .= "<br/>".$iform->error ( 'emailaddress' );
				$register .= "</td>";
				$register .= "</tr>";				
				$register .= "<tr>";
				$register .= "<td>Confirm Email:</td>";
				$register .= "<td>";
				$register .= "<input type='text' name='emailaddressconfirm' maxlength='30' value='".$iform->value ( 'emailaddressconfirm' )."' />";
				$register .= "<br/>".$iform->error ( 'emailaddressconfirm' );
				$register .= "</td>";
				$register .= "</tr>";				
				$register .= "<tr>";
				$register .= "<td>Organization:</td>";
				$register .= "<td>";
				$register .= "<input type='text' name='organization' maxlength='30' value='".$iform->value ( 'organization' )."' />";
				$register .= "<br/>".$iform->error ( 'organization' );
				$register .= "</td>";
				$register .= "</tr>";				
				$register .= "<tr>";
				$register .= "<td>Address 1:</td>";
				$register .= "<td>";
				$register .= "<input type='text' name='address1' maxlength='30' value='".$iform->value ( 'address1' )."' />";
				$register .= "<br/>".$iform->error ( 'address1' );
				$register .= "</td>";
				$register .= "</tr>";				
				$register .= "<tr>";
				$register .= "<td>Address 2:</td>";
				$register .= "<td>";
				$register .= "<input type='text' name='address2' maxlength='30' value='".$iform->value ( 'address2' )."' />";
				$register .= "<br/>".$iform->error ( 'address2' );
				$register .= "</td>";
				$register .= "</tr>";				
				$register .= "<tr>";
				$register .= "<td>City:</td>";
				$register .= "<td>";
				$register .= "<input type='text' name='city' maxlength='30' value='".$iform->value ( 'city' )."' />";
				$register .= "<br/>".$iform->error ( 'city' );
				$register .= "</td>";
				$register .= "</tr>";				
				$register .= "<tr>";
				$register .= "<td>State:</td>";
				$register .= "<td>";
				$register .= "<input type='text' name='state' maxlength='30' value='".$iform->value ( 'state' )."' />";
				$register .= "<br/>".$iform->error ( 'state' );
				$register .= "</td>";
				$register .= "</tr>";				
				$register .= "<tr>";
				$register .= "<td>Zipcode:</td>";
				$register .= "<td>";
				$register .= "<input type='text' name='zip' maxlength='30' value='".$iform->value ( 'zip' )."' />";
				$register .= "<br/>".$iform->error ( 'zip' );
				$register .= "</td>";
				$register .= "</tr>";				
				$register .= "<tr>";
				$register .= "<td>Agree to terms of service:</td>";
				$register .= "<td>";
				$register .= "<input type='text' name='zip' maxlength='30' value='".$iform->value ( 'zip' )."' />";
				$register .= "<br/>".$iform->error ( 'zip' );
				$register .= "</td>";
				$register .= "</tr>";	
				$register .= "<tr>";
				$register .= "<td>Information provider:</td>";
				$register .= "<td>";
				$register .= "<input type='text' name='zip' maxlength='30' value='".$iform->value ( 'zip' )."' />";
				$register .= "<br/>".$iform->error ( 'zip' );
				$register .= "</td>";
				$register .= "</tr>";								
				$register .= "<tbody>";
				$register .= "</table>";
				
				$register .="<input type='hidden' name='subregister' value='1' /> <br />";
				$register .="<input type='submit' value='Register' /> <br />";
				$register .="</form>";
			}
			$register .= "<p>&nbsp;</p> ";
			return $register;

		}
	}

?>