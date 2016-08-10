<?php
	class forgotPassword
	{
		public function toHTML()
		{
			global $irrigationScheduler;
			global $iform, $validation;
			$forgotPassword = "";
			if (isset ( $_SESSION ['forgotpass'] ) && $_SESSION['forgotpass']) {
				/**
				 * New password was generated for user and sent to user's
				 * email address.
				 */
				$forgotPassword .= '<h1>New Password Generated</h1><br /><p>Your new password has been generated and sent to the email associated with your account. </p>';
				unset ( $_SESSION ['forgotpass'] );
			} else {
			/**
			 * Forgot password form is displayed, if error found
			 * it is displayed.
			 */
				$forgotPassword .="<h2>Forgot Password?</h2>";
				$forgotPassword .="Your password will be sent to the email address associated with your account, all you have to do is enter your username. <br /><br />";
				$forgotPassword .="<form action='".$irrigationScheduler->session->processPath."' method='post'><b>Username:</b><br />";
				$forgotPassword .="<input type='text' name='user' maxlength='30' value='".$iform->value ( 'user' )."' /> <br /> ";
				$forgotPassword .=$iform->error ( 'user' );
				$forgotPassword .="<input type='hidden' name='subforgotpass' value='1' /> <br />";
				$forgotPassword .="<input type='submit' value='Email Password' /> <br />";
				$forgotPassword .="</form>";
				$forgotPassword .="<br />";
			}
			$forgotPassword .="<p>Note: Depending on Internet traffic, it may take up to 10 minutes to receive your password!</p>";
			$forgotPassword .= "<p>&nbsp;</p> ";
			return $forgotPassword;

		}
	}

?>