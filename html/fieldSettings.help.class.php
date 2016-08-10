<?php
	class fieldSettings
	{
		public function toHTML()
		{
			global $irrigationScheduler;
			$advancedUpdateFieldHelp = "";
      if($irrigationScheduler->session->isMobileDevice > 0)
          $MobileSpace = "<p>&nbsp;</p>";
      else
          $MobileSpace = "";
			$advancedUpdateFieldHelp .= "<h2><a name=\"top\"></a>Field Settings Help</h2>";
			$advancedUpdateFieldHelp .= "<p>This page is for advanced users.  Defaults for these values are based on the crop and soil type chosen upon field setup. Entering alternate values here overwrites these defaults. The &ldquot;Update Field&rdquot; button must be clicked to save and use any altered values.</p>".$MobileSpace;
			$advancedUpdateFieldHelp .= "<p><b>Show Forecast Values</b> - If checked, the model will get a seven day forecast of the maximum and minimum temperatures from the National Weather Service based on the location of the chosen weather station.  The Hargreaves equation is then used to estimate grass reference evapotranspiration (ETo) and multiplied by 1.2 to estimate alfalfa reference evapotranspiration (ETr).  Forecasts are refreshed every 2 hours.</p>"; 		
			$advancedUpdateFieldHelp .= "<p><b>Send Me Notifications </b> - Check this box to get email or text message notifications sent to you on the status of your field.  If you choose to be notified by email you will be asked for your email address.  If you choose to be notified by text (SMS) message you will be asked for your mobile phone number and your service provider.  You can also choose what time of day the notification will be sent.  You can also elect to <i>only</i> be notified when your percent of available soil water has dried to less than an entered threshold value.</p>".$MobileSpace; 		
			$advancedUpdateFieldHelp .= "<p><b>Use Hours Instead of Inches </b> - Many irrigators think in terms of hours of irrigation run time instead of inches of water applied. Applied irrigation can be entered in hours, and the soil water deficit can be displayed in hours instead of inches.  If you prefer to use hours an irrigation application rate in inches per hour must be provided.  Calculators are available on this page to &ldquot;Help Calculate My Application Rate&rdquot; for drip, sprinkle, and general irrigation systems using a variety of different units.  Reasonable assumptions of irrigation application efficiency are provided for each system.</p>".$MobileSpace; 		
			$advancedUpdateFieldHelp .= "<p><b>Use Volumetric Soil Water Content </b> - Most soil moisture sensors display volumetric soil water content (volume of water/volume of soil) instead of the percent of available water (which is easier to understand).  If you would prefer to see and enter volumetric soil water content in the Daily Budget Table then check this box.</p>".$MobileSpace; 		
			$advancedUpdateFieldHelp .= "<p><b>For Drip/Micro, % of Soil Wetted </b> - In many perennial cropping systems under drip or micro irrigation, the entire soil volume is not used.  For example a drip irrigation system in a wine grape vineyard may wet a 4 ft width of soil in an 8 ft row spacing.  In this case only 50% of the soil is used to store water since the inter-rows remain dry.  The soil's water holding capacity can be reduced by multiplying by this percentage to reflect this.</p>".$MobileSpace; 		
			$advancedUpdateFieldHelp .= "<p><b>Soil Water Content at Full (Field Capacity)</b> - This is the maximum amount of water that the soil can hold long term against gravity.  After a soil is at field capacity adding more water will result in the water moving down through the soil profile and past the bottom of the root zone (tracked on the \"Deep Water Loss Chart\").  This is measured in inches of water per foot of soil depth.</p>".$MobileSpace; 		
			$advancedUpdateFieldHelp .= "<p><b>Soil Available Water Holding Capacity</b> - Sometimes referred to as available water (AW) or total available water (TAW), this is field capacity minus wilting point, or the amount of water the soil can hold between full and empty.  The Empty/Dead (permanent wilting point) is calculated as field capacity (Full) minus this number.  This is measured in inches of water per foot of soil depth.</p>".$MobileSpace; 		
			$advancedUpdateFieldHelp .= "<p><b>Management Allowable Deficit (MAD)</b> - This is the percent <i>depletion</i> of the total available water below which the plant begins to experience water stress.  <font style='color:red'>100% - MAD is the First Water Stress point</font> as a percent of the available water holding capacity. As the soil dries down below this point the plant will experience increasing amounts of water stress until the plant will die when it reaches the Empty/Dead (permanent wilting) point.  Daily crop water use estimates are proportionately decreased from the full value to zero as the soil water content decrease from MAD to the soil's permanent wilting point.</p>".$MobileSpace; 		
			$advancedUpdateFieldHelp .= "<p><b>Planting/Emergence Date</b> - Date that the crop emerges and/or the plant starts using water.</p>".$MobileSpace; 		
			$advancedUpdateFieldHelp .= "<p><b>Root Depth on Start Date</b> - The effects of a growing root depth are modeled.  This is the root depth in inches on the starting, or plant emergence date.</p>".$MobileSpace; 		
			$advancedUpdateFieldHelp .= "<p><b>Maximum Managed Root Zone Depth</b> - The maximum root depth reached in the season.  It is assumed that the plant root reaches this depth on the Crop Canopy Full Cover Date.</p>".$MobileSpace; 		
			$advancedUpdateFieldHelp .= "<p><b>Crop Canopy Cover Exceeds 10% of Field</b> - The date that crop water use starts increasing.</p>".$MobileSpace; 		
			$advancedUpdateFieldHelp .= "<p><b>Crop Canopy Full Cover Date</b> - The date that the crop canopy exceeds 70% - 80% of the field area or shades 70% - 80% of the ground area.</p>".$MobileSpace; 		
			$advancedUpdateFieldHelp .= "<p><b>Crop Initial Maturation Date</b> - After this date the crop begins to dry up, senesce or otherwise shut down and water use begins to decrease.</p>".$MobileSpace; 		
			$advancedUpdateFieldHelp .= "<p><b>End of Growing Season Date</b> - Water use stops on this date.  Often this coincides with harvest, or the first killing frost.</p>".$MobileSpace; 		
			$advancedUpdateFieldHelp .= "<p><b>Initial Crop Coefficient</b> - The crop coefficient (Kc) from emergence to the 10% Cover date.  Based on alfalfa reference ET.</p>".$MobileSpace; 		
			$advancedUpdateFieldHelp .= "<p><b>Full Cover Crop Coefficient</b> - The crop coefficient (Kc) at full cover.  This is the peak, or maximum crop coefficient.  Based on alfalfa reference ET.</p>".$MobileSpace; 		
			$advancedUpdateFieldHelp .= "<p><b>Final Crop Coefficient</b> - Crop coefficient (Kc) at the end of the season.  Based on alfalfa reference ET.</p>".$MobileSpace; 		
			$advancedUpdateFieldHelp .= "<p><b>Post Cutting Kc Flat Days</b> - After cutting a forage, this is the number of days before regrowth starts.</p>".$MobileSpace; 		
			$advancedUpdateFieldHelp .= "<p><b>Post Cutting Kc Recovery Days</b> - After cutting a forage this is the number of days after regrowth starts for the forage to regrow to full cover again.</p>".$MobileSpace; 		
			if($irrigationScheduler->session->isMobileDevice > 0)
			{
				$advancedUpdateFieldHelp .= "<p><a href='/irrigation-scheduler/KC_RZ_Explanation.png'><img width='310px' src='/irrigation-scheduler/KC_RZ_Explanation.png' alt='KC and RZ Explanation'></a></p>";
				$advancedUpdateFieldHelp .= "<div style=\"clear:both;\"><center><a href='".$irrigationScheduler->session->basepath."&amp;action=advanced-update-field'>Back</a></center></div>";
			}
			else
			{
				$advancedUpdateFieldHelp .= "<p><a href='/irrigation-scheduler/KC_RZ_Explanation.png'><img width='600px' src='/irrigation-scheduler/KC_RZ_Explanation.png' alt='KC and RZ Explanation'></a></p>";
				$advancedUpdateFieldHelp .= "<div style=\"clear:both;\"><center><a href='javascript:void(0);' onclick='$.fancybox.close();'>Close</a></center></div>";
			}
			return $advancedUpdateFieldHelp;
 	}
 	
	}

?>