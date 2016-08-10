<?php
	class editStationDefaults
	{
		public function toHTML()
		{
			global $irrigationScheduler;
			global $iform, $validation;
			$editStationDefaults = "<h2>Login<a name=\"top\"></a></h2><p>&nbsp;</p>";
	   	$editStationDefaults .=  "<form ACTION=\"".$irrigationScheduler->session->processPath."\" METHOD=\"POST\"  name=\"login\">";
			$editStationDefaults .=  "<div>";
			$editStationDefaults .=  "<label>Username:</label>";
			$editStationDefaults .=  "</div>";
			$editStationDefaults .=  "<div>";
			$editStationDefaults .=  "<input name=\"username\" type=\"text\" maxlength=\"25\" value=\"".$iform->value("username")."\"/>";
			$editStationDefaults .=  "</div>";
			$editStationDefaults .=  "<div>";
			$editStationDefaults .=  "<label>Password:</label>";
			$editStationDefaults .=  "</div>";
			$editStationDefaults .=  "<div>";
			$editStationDefaults .=  "<input name=\"password\" type=\"password\" maxlength=\"25\" value=\"".$iform->value("password")."\"/>";
			$editStationDefaults .=  "</div>";
			$editStationDefaults .=  "<div>Remember me";
			if ($iform->value("remember") != "")
				$editStationDefaults .=  "<input name=\"remember\" type=\"checkbox\" checked />";
			else
				$editStationDefaults .=  "<input name=\"remember\" type=\"checkbox\" />";
			$editStationDefaults .=  "</div>";
			$editStationDefaults .=  "<input type=\"hidden\" name=\"sublogin\" value=\"1\" />";
			$editStationDefaults .=  "<div class='errorPHP'><label>";
			if(isset($_SESSION['error_array']))
			{
				foreach($_SESSION['error_array'] as $key => $value)
				{
					$editStationDefaults .= $value;
				}
			}
			$editStationDefaults .=  "</label></div>";
			$editStationDefaults .=  "<div>";
			$editStationDefaults .=  "<input name=\"login\" type=\"submit\" value=\"Login\" />";
			$editStationDefaults .=  "</div>";
			$editStationDefaults .=  "</form>";
			$editStationDefaults .=  "<p>&nbsp;</p>";
			$editStationDefaults .=  "<p><a target='_blank' href=\"/awn.php?page=forgotuser\">Forgot Username?</a></p>";
			$editStationDefaults .=  "<p><a target='_blank' href=\"/awn.php?page=forgotpass\">Forgot Password?</a></p>";
			$editStationDefaults .=  "<div class=\"separator\">&nbsp;</div>";

			$editStationDefaults .=  "<h2>Register</h2>";
			$editStationDefaults .=  "<p>";
			$editStationDefaults .=  "In order to use the irrigation scheduler, please <a target='_blank' href='/awn.php?page=register'>register</a> for an AgWeatherNet account which can be used to log in.";
			$editStationDefaults .=  "</p>";
	
			return $editStationDefaults;
		}
	}

?>