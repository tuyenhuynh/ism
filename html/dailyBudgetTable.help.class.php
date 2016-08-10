<?php
	class dailyBudgetTable
	{
		public function toHTML()
		{
			global $irrigationScheduler;
			$dailyBudgetTableHelp = "";
			$dailyBudgetTableHelp .= "<h2><a name=\"top\"></a>7-Day Daily Budget Table Help</h2>";
			$dailyBudgetTableHelp .= "<p>Use this table to view the calculated daily crop water use, percent available soil water, and soil water deficit.  From this table you can enter irrigation events and/or soil moisture measurements using the &ldquo;Edit&rdquo; link.  If the percent available water is within the range for maximum production the line will be green.  As it gets close to the First Water Stress line (Management Allowable Deficit; MAD) it turns yellow. If the soil water content is depleted below the MAD or First Water Stress point it will turn red.</p>";
			if($irrigationScheduler->session->isMobileDevice > 0)
					$dailyBudgetTableHelp .= "<p>&nbsp;</p>";
			$dailyBudgetTableHelp .= "<p><b>Water Use (in/day)</b> - This is the daily crop water use (evapotranspiration or ETc) estimated from measured weather parameters from the selected weather station, and the entered crop coefficients.  This model uses alfalfa reference evapotranspiration (ETr) calculated using the standardized ASCE Penman-Monteith method.</p>";
			if($irrigationScheduler->session->isMobileDevice > 0)
					$dailyBudgetTableHelp .= "<p>&nbsp;</p>";
			$dailyBudgetTableHelp .= "<p><b>Rain& Irrig. (in)</b> - This is the rainfall for that day and/or and irrigation amount that is stored in the soil.  It is assumed that all measured rainfall will help satisfy the calculated water use demand. Irrigation amounts are also included in this column.<i>  Irrigation amounts must be entered using the Edit link for the model to be accurate.  </i>Some applied irrigation water is lost to evaporation.  Therefore <i>gross</i> irrigation amounts must be discounted for irrigation efficiency.  Typical irrigation efficiency values are: drip-95%, center pivot-85%, wheel/hand lines/lawn sprinklers-70%, big guns-60%.  For example if a gross depth of 1 inch of water is applied by a center pivot, enter 0.85 here (1 inch x 85%/100).  If you use <i>measured</i> application depths, don't correct for efficiency.  For surface irrigation, a reasonable assumption is that you completely refill the soil to field capacity, or fully replace the soil water deficit.</p>";
			if($irrigationScheduler->session->isMobileDevice > 0)
					$dailyBudgetTableHelp .= "<p>&nbsp;</p>";
			$dailyBudgetTableHelp .= "<p><b>Soil Water (%)</b> - This is the calculated daily soil water content expressed as a percent of the available soil water (AW; field capacity - wilting point). 100% is equivalent to field capacity (full), and 0% is equivalent to the permanent wilting point (empty/dead plants).  You can reset or correct the soil moisture value on any day using the \"Edit\" link.  This will reset or correct the model to the entered value from that day forward. Most soil water sensors display volumetric soil water content (Vol. SWC; volume of water/volume of soil).  If you chose to \"Use Volumetric Soil Water Content\" in Field Settings then this will be displayed, and Vol. SWC values will have to be entered to reset or correct the model.</p>";

			if($irrigationScheduler->session->isMobileDevice > 0)
					$dailyBudgetTableHelp .= "<p>&nbsp;</p>";
			$dailyBudgetTableHelp .= "<p><b>Water Deficit (in) or (hrs)</b> - The soil water deficit in the root zone. This is the amount of 'space' in the soil, or the depth of irrigation water that can be applied before the soil is full again (reaches field capacity).  This morning's estimated soil water deficit is highlighted in <b><font style='color:red'>red</font></b>.  If you chose in Field Settings to use hours of irrigation instead of inches, then the numbers in this column will be the hours of irrigation run time required to refill the soil profile back to field capacity.</p>";
			if($irrigationScheduler->session->isMobileDevice > 0)
					$dailyBudgetTableHelp .= "<p>&nbsp;</p>";
			$dailyBudgetTableHelp .= "<p><b>Edit Data</b> - Use this link at each line to add irrigation amounts or correct the model for measured soil water contents. A description of the fieldsd in this box are below.</p>";
			if($irrigationScheduler->session->isMobileDevice > 0)
					$dailyBudgetTableHelp .= "<p>&nbsp;</p>";
	                //Troy additions
			$dailyBudgetTableHelp .= "<p><b>Irrigation (in or hrs)</b> - Enter the net amount of irrigation applied to the field on this date in inches, or hours of irrigation depending on your selected method in &ldquo;<a href='".$irrigationScheduler->session->basepath."&amp;action=advanced-update-field'>Field Settings</a>&rdquo;. Some applied irrigation water is lost to evaporation and irrigation systems aren't 100% efficient. When using hours of irrigation, irrigation efficiency is taken into account in the application rate calculation.  However, when entering irrigation amounts in inches the gross irrigation amounts must be discounted for irrigation efficiency.  Typical irrigation efficiency values are: drip-95%, center pivot-85%, wheel/hand lines/lawn sprinklers-70%, big guns-60%.  For example a gross depth of 1 inch of water is applied by a center pivot, enter 0.85 here (1 inch x 85%/100).  If you use measured application depths, don't correct for efficiency.  For surface irrigation, a reasonable assumption is that you completely refill the soil to field capacity to 100% Available Water, or completely replace the soil water deficit.</p>"; 		
			if($irrigationScheduler->session->isMobileDevice > 0)
					$dailyBudgetTableHelp .= "<p>&nbsp;</p>";
	 		$dailyBudgetTableHelp .= "<p><b>Soil Water Availability</b> - The default value in this is the calculated percent of available soil water.  Overwrite this number to correct the model using data from a soil moisture measurement, or if the model gets off over time and needs to be corrected based on estimated values.</p>"; 		
			if($irrigationScheduler->session->isMobileDevice > 0)
					$dailyBudgetTableHelp .= "<p>&nbsp;</p>";
	 		$dailyBudgetTableHelp .= "<p><b>Reset/Correct Soil Water Availability</b> - The model can be corrected using measured or estimated soil water content estimates.  Check this box and enter the percent (%) of the of total available water (100% is full or field capacity, down to 0% which is empty/dead or permanent wilting point).  If checked then the entered number will be used.  Uncheck to use the calculated percent available water for that day.</p>"; 		
			if($irrigationScheduler->session->isMobileDevice > 0)
					$dailyBudgetTableHelp .= "<p>&nbsp;</p>";
	 		$dailyBudgetTableHelp .= "<p><b>Apply Forage Cutting Today</b> - If you are growing a forage the crop coefficients are reset to account for the lowered water use of a cut and regrowing forage. Check this box on the days that the forage was cut.</p>"; 		
			if($irrigationScheduler->session->isMobileDevice > 0)
					$dailyBudgetTableHelp .= "<p>&nbsp;</p>";
	               // End Troy
			$dailyBudgetTableHelp .= "<p><b>Forecast</b> - The last day on the Budget Table represents yesterday evening and/or this morning. A seven-day forecast is available.  This is based on the projected maximum and minimum temperatures from the National Weather Service for those days at the location of the chosen weather station.  The Hargreaves equation is used with these temperature data to estimate grass reference ETo which is then multiplied by 1.2 for alfalfa reference ETr.  If the model is viewed late in the day, the 7th forecasted day is from the NWS.  However before 6 PM, then the 6th forecasted day is repeated for the 7th forecasted day.</p>";
			if($irrigationScheduler->session->isMobileDevice > 0)
					$dailyBudgetTableHelp .= "<p>&nbsp;</p>";
	
	
			if($irrigationScheduler->session->isMobileDevice > 0)
				$dailyBudgetTableHelp .= "<div style=\"clear:both;\"><center><a href='".$irrigationScheduler->session->basepath."&amp;action=daily-budget-existing-fields'>Back</a></center></div>";
			else
				$dailyBudgetTableHelp .= "<div style=\"clear:both;\"><center><a href='javascript:void(0)' onclick='$.fancybox.close();'>Close</a></center></div>";
				
			return $dailyBudgetTableHelp;
		}
	}

?>