<?php
	class loginForm
	{
		public function toHTML()
		{
			global $irrigationScheduler;
			global $iform, $validation;
			$loginForm = "<h2>Login<a name=\"top\"></a></h2><p>Using your AgWeatherNet account.</p><p>&nbsp;</p>";
	   	//if user has logged in unsuccessfully, then show errors and no of errors
			$loginForm .=  "<form ACTION=\"".$irrigationScheduler->session->processPath."\" METHOD=\"POST\"  name=\"login\">";
			$loginForm .=  "<div>";
			$loginForm .=  "<label>Username:</label>";
			$loginForm .=  "</div>";
			$loginForm .=  "<div>";
			$loginForm .=  "<input name=\"username\" type=\"text\" maxlength=\"25\" value=\"".$iform->value("username")."\"/>";
			$loginForm .=  "</div>";
			$loginForm .= $iform->error ( 'user' );
			$loginForm .=  "<div>";
			$loginForm .=  "<label>Password:</label>";
			$loginForm .=  "</div>";
			$loginForm .=  "<div>";
			$loginForm .=  "<input name=\"password\" type=\"password\" maxlength=\"25\" value=\"".$iform->value("password")."\"/>";
			$loginForm .=  "</div>";
			$loginForm .= $iform->error ( 'pass' );
			$loginForm .=  "<div>Remember me";
			if ($iform->value("remember") != "")
				$loginForm .=  "<input name=\"remember\" type=\"checkbox\" checked />";
			else
				$loginForm .=  "<input name=\"remember\" type=\"checkbox\" />";
			$loginForm .=  "</div>";
			$loginForm .=  "<input type=\"hidden\" name=\"sublogin\" value=\"1\" />";
			$loginForm .=  "<div class='errorPHP'><label>";
			if(isset($_SESSION['error_array']))
			{
				foreach($_SESSION['error_array'] as $key => $value)
				{
					$loginForm .= $value;
				}
			}
			$loginForm .=  "</label></div>";
			$loginForm .=  "<div>";
			$loginForm .=  "<input name=\"login\" type=\"submit\" value=\"Login\" />";
			$loginForm .=  "</div>";
			$loginForm .=  "</form>";
			$loginForm .=  "<p>&nbsp;</p>";
			$loginForm .=  "<p><a href='".$irrigationScheduler->session->basepath."&amp;action=forgotname'>Forgot Username?</a></p>";
			$loginForm .=  "<p><a href='".$irrigationScheduler->session->basepath."&amp;action=forgotpass'>Forgot Password?</a></p>";
			$loginForm .=  "<div class=\"separator\">&nbsp;</div>";

			$loginForm .=  "<h2>Register</h2>";
			$loginForm .=  "<p>";
			$loginForm .=  "In order to use the irrigation scheduler, please <a target='_blank' href='http://weather.wsu.edu/awn.php?page=register'>register</a> for an AgWeatherNet account which can be used to log in.";
			$loginForm .=  "</p>";
	
			return $loginForm;
		}
	}

?>