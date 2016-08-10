<?php
	class contactUs
	{
		public function toHTML()
		{
			global $irrigationScheduler;
			global $iform;
			global $validation;

			$contactUs = ""; 
			$contactUs .= "<h2><a name='top'></a>Contact Us</h2>";
			// user hasn't submitted the form
			// so we set up the random values
			$result = $validation->setRandomNumbers();

			$contactUs .= "<p>Free support for using this tool is offered by calling (509-786-9247) or emailing <a href=\"mailto:troy_peters@wsu.edu\">Troy Peters</a> (troy_peters@wsu.edu), or by the submission form below.  We would love to hear from you about your experience using this tool, and any enhancements, changes, or bug-fixes that you would like to see.  ";
			$contactUs .= "form below.</p>";
			$contactUs .= "<p>&nbsp;</p> ";
			$contactUs .= "<div id=\"contact-form\"> ";
			$contactUs .= "<form action=\"".$irrigationScheduler->session->processPath."\" method=\"POST\" name='contact' > ";
			$contactUs .= "<fieldset> ";
			$contactUs .= "<div id=\"name-input\"> ";
			$contactUs .= "<label for=\"name\">Name:</label> ";
			$contactUs .= "<br /> ";
			$contactUs .= "<input name=\"name\" type=\"text\" id=\"name\" size=\"30\" maxlength=\"30\" value=\"".$iform->value("name")."\"> ";
			$contactUs .= "</div> ";
			$contactUs .= $iform->error ( 'name' );
			$contactUs .= "<div class=\"error\" id=\"name-error\">This field is required</div> ";
			$contactUs .= "<div id=\"email-input\"> ";
			$contactUs .= "<label for=\"email\" >Email:</label> ";
			$contactUs .= "<br /> ";
			if(isset($irrigationScheduler->session->userInfo['email']))
			{
				$contactUs .= "<input name=\"email\" type=\"text\" id=\"email\" size=\"30\" maxlength=\"30\" value=\"".$irrigationScheduler->session->userInfo['email']."\"> ";
			}
			else
			{
				$contactUs .= "<input name=\"email\" type=\"text\" id=\"email\" size=\"30\" maxlength=\"30\" value=\"".$iform->value("email")."\"> ";
			}
			$contactUs .= "</div> ";
			$contactUs .= $iform->error ( 'email' );
			$contactUs .= "<div class=\"error\" id=\"email-error\">This field is required</div> ";
			$contactUs .= "<div class=\"error\" id=\"email-format-error\">Incorrect email format (Ex. john.doe@uni.west.edu, bob@bob.com)</div> ";
			$contactUs .= "<div id=\"message-input\" > ";
			$contactUs .= "<label for=\"message\" >Message:</label> ";
			$contactUs .= "<br /> ";
			$contactUs .= "<textarea name=\"message\" cols=\"30\" rows=\"10\" id=\"message\"></textarea> ";
			$contactUs .= "</div> ";
			$contactUs .= $iform->error ( 'message' );
			$contactUs .= "<div class=\"error\" id=\"message-error\">This field is required</div> ";
			$contactUs .= "<div id=\"captcha-input\" > Are you human?<br/> ";
			$contactUs .= $_SESSION['rn1'] . ' + ' . $_SESSION['rn2'] . ' = ' ;  
			$contactUs .= "<input name=\"result\" type=\"hidden\" value=\"".$_SESSION['result']."\" id=\"result\"/> ";
			$contactUs .= "<input name=\"captcha\" type=\"text\" size=\"20\" maxlength=\"20\" value=\"\" id=\"captcha\"/> ";
			$contactUs .= "</div> ";
			$contactUs .= $iform->error ( 'captcha' );
			$contactUs .= "<div class=\"error\" id=\"captcha-error\">This field is required</div> ";
			$contactUs .= "<div class=\"error\" id=\"captcha-input-error\">The captcha result is incorrect</div> ";
			$contactUs .= "<br /> ";
			$contactUs .= "<div> ";
			$contactUs .= "<input type='hidden' name='subcontactus' value='1' /> <br />";
			$contactUs .= "<input name=\"submit\" type=\"submit\" value=\"Send Message\"> ";
			$contactUs .= "</div> ";
			$contactUs .= "</fieldset> ";
			$contactUs .= "</form> ";
			$contactUs .= "</div> ";
			return $contactUs;

		}
	}

?>