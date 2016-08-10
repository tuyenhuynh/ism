<?php
	class editSoilDefaults
	{
		public function toHTML()
		{
			global $irrigationScheduler;
			global $iform, $validation;
			$editSoilDefaults = "<h2>Login<a name=\"top\"></a></h2><p>&nbsp;</p>";
			$editSoilDefaults .=  "<form ACTION=\"".$irrigationScheduler->session->processPath."\" METHOD=\"POST\"  name=\"login\">";
			$editSoilDefaults .=  "<div>";
			$editSoilDefaults .=  "<label>Username:</label>";
			$editSoilDefaults .=  "</div>";
			$editSoilDefaults .=  "<div>";
			$editSoilDefaults .=  "<input name=\"username\" type=\"text\" maxlength=\"25\" value=\"".$iform->value("username")."\"/>";
			$editSoilDefaults .=  "</div>";
			$editSoilDefaults .=  "<div>";
			$editSoilDefaults .=  "<label>Password:</label>";
			$editSoilDefaults .=  "</div>";
			$editSoilDefaults .=  "<div>";
			$editSoilDefaults .=  "<input name=\"password\" type=\"password\" maxlength=\"25\" value=\"".$iform->value("password")."\"/>";
			$editSoilDefaults .=  "</div>";
			$editSoilDefaults .=  "<div>Remember me";
			if ($iform->value("remember") != "")
				$editSoilDefaults .=  "<input name=\"remember\" type=\"checkbox\" checked />";
			else
				$editSoilDefaults .=  "<input name=\"remember\" type=\"checkbox\" />";
			$editSoilDefaults .=  "</div>";
			$editSoilDefaults .=  "<input type=\"hidden\" name=\"sublogin\" value=\"1\" />";
			$editSoilDefaults .=  "<div class='errorPHP'><label>";
			if(isset($_SESSION['error_array']))
			{
				foreach($_SESSION['error_array'] as $key => $value)
				{
					$editSoilDefaults .= $value;
				}
			}
			$editSoilDefaults .=  "</label></div>";
			$editSoilDefaults .=  "<div>";
			$editSoilDefaults .=  "<input name=\"login\" type=\"submit\" value=\"Login\" />";
			$editSoilDefaults .=  "</div>";
			$editSoilDefaults .=  "</form>";
			$editSoilDefaults .=  "<p>&nbsp;</p>";
			$editSoilDefaults .=  "<p><a target='_blank' href=\"/awn.php?page=forgotuser\">Forgot Username?</a></p>";
			$editSoilDefaults .=  "<p><a target='_blank' href=\"/awn.php?page=forgotpass\">Forgot Password?</a></p>";
			$editSoilDefaults .=  "<div class=\"separator\">&nbsp;</div>";

			$editSoilDefaults .=  "<h2>Register</h2>";
			$editSoilDefaults .=  "<p>";
			$editSoilDefaults .=  "In order to use the irrigation scheduler, please <a target='_blank' href='/awn.php?page=register'>register</a> for an AgWeatherNet account which can be used to log in.";
			$editSoilDefaults .=  "</p>";
	
			return $editSoilDefaults;
		}
	}

?>