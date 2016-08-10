<?php
	class editCropDefaults
	{
		public function toHTML()
		{
			global $irrigationScheduler;
			global $iform, $validation;
			$editCropDefaults = "<h2>Login<a name=\"top\"></a></h2><p>&nbsp;</p>";
			$editCropDefaults .=  "<form ACTION=\"".$irrigationScheduler->session->processPath."\" METHOD=\"POST\"  name=\"login\">";
			$editCropDefaults .=  "<div>";
			$editCropDefaults .=  "<label>Username:</label>";
			$editCropDefaults .=  "</div>";
			$editCropDefaults .=  "<div>";
			$editCropDefaults .=  "<input name=\"username\" type=\"text\" maxlength=\"25\" value=\"".$iform->value("username")."\"/>";
			$editCropDefaults .=  "</div>";
			$editCropDefaults .=  "<div>";
			$editCropDefaults .=  "<label>Password:</label>";
			$editCropDefaults .=  "</div>";
			$editCropDefaults .=  "<div>";
			$editCropDefaults .=  "<input name=\"password\" type=\"password\" maxlength=\"25\" value=\"".$iform->value("password")."\"/>";
			$editCropDefaults .=  "</div>";
			$editCropDefaults .=  "<div>Remember me";
			if ($iform->value("remember") != "")
				$editCropDefaults .=  "<input name=\"remember\" type=\"checkbox\" checked />";
			else
				$editCropDefaults .=  "<input name=\"remember\" type=\"checkbox\" />";
			$editCropDefaults .=  "</div>";
			$editCropDefaults .=  "<input type=\"hidden\" name=\"sublogin\" value=\"1\" />";
			$editCropDefaults .=  "<div class='errorPHP'><label>";
			if(isset($_SESSION['error_array']))
			{
				foreach($_SESSION['error_array'] as $key => $value)
				{
					$editCropDefaults .= $value;
				}
			}
			$editCropDefaults .=  "</label></div>";
			$editCropDefaults .=  "<div>";
			$editCropDefaults .=  "<input name=\"login\" type=\"submit\" value=\"Login\" />";
			$editCropDefaults .=  "</div>";
			$editCropDefaults .=  "</form>";
			$editCropDefaults .=  "<p>&nbsp;</p>";
			$editCropDefaults .=  "<p><a target='_blank' href=\"/awn.php?page=forgotuser\">Forgot Username?</a></p>";
			$editCropDefaults .=  "<p><a target='_blank' href=\"/awn.php?page=forgotpass\">Forgot Password?</a></p>";
			$editCropDefaults .=  "<div class=\"separator\">&nbsp;</div>";

			$editCropDefaults .=  "<h2>Register</h2>";
			$editCropDefaults .=  "<p>";
			$editCropDefaults .=  "In order to use the irrigation scheduler, please <a target='_blank' href='/awn.php?page=register'>register</a> for an AgWeatherNet account which can be used to log in.";
			$editCropDefaults .=  "</p>";
	
			return $editCropDefaults;
		}
	}

?>