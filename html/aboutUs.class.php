<?php
	class aboutUs
	{
		public function toHTML()
		{
			$aboutUs = "";
			$aboutUs .= "<h2><a name=\"top\"></a>About Us</h2>";
	 		$aboutUs .= "<p>&nbsp;</p>";
			$aboutUs .= "<p>The Irrigation Scheduler was initially developed at the ";
	  	$aboutUs .= "<a href=\"http://www.prosser.wsu.edu\">";
	  	$aboutUs .= "Washington State University (WSU) Irrigated Agriculture ";
	  	$aboutUs .= "Research and Extension Center (IAREC)";
	  	$aboutUs .= "</a>";
	  	$aboutUs .= " in Prosser, ";
			$aboutUs .= "Washington by Sean Hill and Cynthia Tiwana under the direction of ";
			$aboutUs .= "Troy Peters, Extension Irrigation Specialist and ";
			$aboutUs .= "Assistant Professor at Washington State University.</p>";
			$aboutUs .= "<p>&nbsp;</p>";
			$aboutUs .= "<p>This project received funding from the following ";
			$aboutUs .= "sources:</p>";
			$aboutUs .= "<p>&nbsp;</p>";
			$aboutUs .= "<a href=\"http://www.usda.gov\">";
			$aboutUs .= "<p>United States Department of Agriculture";
			$aboutUs .= "</a>";
			$aboutUs .= ", National ";
			$aboutUs .= "Water Quality Initiative</p><br/>";
			$aboutUs .= "<a href=\"http://www.wsu.edu\">";
			$aboutUs .= "<p>Washington State University Agricultural Research";
			$aboutUs .= " Center</p>";
			$aboutUs .= "</a><br/>";
			$aboutUs .= "<a href=\" http://www.asabe.org\">";
			$aboutUs .= "<p>American Society of Agricultural and Biological Engineers</p>";
			$aboutUs .= "</a><br/>";
			$aboutUs .= "<a href=\"http://weather.wsu.edu\"><p>WSU AgWeatherNet</p>";
			$aboutUs .= "</a>";
			$aboutUs .= "<br/><p>The source code for the Irrigation Scheduler is freely ";
			$aboutUs .= "available by ";
			$aboutUs .= "<a href=\"mailto:troy_peters@wsu.edu\">";
			$aboutUs .= "emailing Troy Peters";
			$aboutUs .= "</a>";
			$aboutUs .= " with the request that ";
			$aboutUs .= "you credit this project in any future developments using ";
			$aboutUs .= "the code.</p>";
			$aboutUs .= "<p>&nbsp;</p>";
			$aboutUs .= "<p>To contact us please click <a href=\"mailto:troy_peters@wsu.edu\">here</a>.</p>";
			
			return $aboutUs;

		}
	}

?>