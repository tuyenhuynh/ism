<?php
	class forgotUsername
	{
		public function toHTML()
		{
			global $irrigationScheduler;
			global $iform, $validation;
			$forgotUsername = "";
			if (isset ( $_SESSION ['forgotuser'] ) && $_SESSION['forgotuser']) {
					/**
					 * New password was generated for user and sent to user's
					 * email address.
					 */
					$forgotUsername .= '<h1>Username sent</h1><br /><p>Your username(s) have been sent to the email associated with your account. </p>';
				unset ( $_SESSION ['forgotuser'] );
			} else {
			/**
			 * Forgot password form is displayed, if error found
			 * it is displayed.
			 */
				$forgotUsername .="<h2>Forgot Username?</h2>";
				$forgotUsername .="Your username will be sent to the email address associated with your account, all you have to do is enter your email address. <br /><br />";
				$forgotUsername .="<form action='".$irrigationScheduler->session->processPath."' method='post'><b>Email address:</b><br />";
				$forgotUsername .="<input type='text' name='email' maxlength='30' value='".$iform->value ( 'email' )."' /> <br /> ";
				$forgotUsername .=$iform->error ( 'email' );
				$forgotUsername .="<input type='hidden' name='subforgotuser' value='1' /> <br />";
				$forgotUsername .="<input type='submit' value='Email Username' /> <br />";
				$forgotUsername .="</form>";
				$forgotUsername .="<br />";
			}
			$forgotUsername .="<p>Note: Depending on Internet traffic, it may take up to 10 minutes to receive your username!</p>";
			$forgotUsername .= "<p>&nbsp;</p> ";
			return $forgotUsername;

		}
	}

?>