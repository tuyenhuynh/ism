<?php
	class help
	{
		public function toHTML()
		{
			global $irrigationScheduler;
			global $iform, $validation;
			$help = "<h2>Login<a name=\"top\"></a></h2><p>&nbsp;</p>";
	   	//if user has logged in unsuccessfully, then show errors and no of errors
			$help .=  "<form ACTION=\"".$irrigationScheduler->session->processPath."\" METHOD=\"POST\"  name=\"login\">";
			$help .=  "<div>";
			$help .=  "<label>Username:</label>";
			$help .=  "</div>";
			$help .=  "<div>";
			$help .=  "<input name=\"username\" type=\"text\" maxlength=\"25\" value=\"".$iform->value("username")."\"/>";
			$help .=  "</div>";
			$help .=  "<div>";
			$help .=  "<label>Password:</label>";
			$help .=  "</div>";
			$help .=  "<div>";
			$help .=  "<input name=\"password\" type=\"password\" maxlength=\"25\" value=\"".$iform->value("password")."\"/>";
			$help .=  "</div>";
			$help .=  "<div>Remember me";
			if ($iform->value("remember") != "")
				$help .=  "<input name=\"remember\" type=\"checkbox\" checked />";
			else
				$help .=  "<input name=\"remember\" type=\"checkbox\" />";
			$help .=  "</div>";
			$help .=  "<input type=\"hidden\" name=\"sublogin\" value=\"1\" />";
			$help .=  "<div class='errorPHP'><label>";
			if(isset($_SESSION['error_array']))
			{
				foreach($_SESSION['error_array'] as $key => $value)
				{
					$help .= $value;
				}
			}
			$help .=  "</label></div>";
			$help .=  "<div>";
			$help .=  "<input name=\"login\" type=\"submit\" value=\"Login\" />";
			$help .=  "</div>";
			$help .=  "</form>";
			$help .=  "<p>&nbsp;</p>";
			$help .=  "<p><a target='_blank' href=\"/awn.php?page=forgotuser\">Forgot Username?</a></p>";
			$help .=  "<p><a target='_blank' href=\"/awn.php?page=forgotpass\">Forgot Password?</a></p>";
			$help .=  "<div class=\"separator\">&nbsp;</div>";

			$help .=  "<h2>Register</h2>";
			$help .=  "<p>";
			$help .=  "In order to use the irrigation scheduler, please <a target='_blank' href='/awn.php?page=register'>register</a> for an AgWeatherNet account which can be used to log in.";
			$help .=  "</p>";
	
			return $help;
		}
	}

?>