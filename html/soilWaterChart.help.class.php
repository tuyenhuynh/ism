<?php
	class soilWaterChart
	{
		public function toHTML()
		{
			global $irrigationScheduler;
	 		$soilWaterChartHelp = "";
	 		$soilWaterChartHelp .= "<h2><a name=\"top\"></a>Soil Water Chart</h2>";
	 		$soilWaterChartHelp .= "<p>The soil water chart shows your estimated Soil Water content (<font color='blue'>blue line</font>) over time in relation to the Full line (field capacity; <font color='b6bf00'>green line</font>), First Water Stress line (management allowable depletion; MAD; <font color='981e32'>red line</font>), and the Empty/Dead line (permanent wilting point; <font color='black'>black line</font>). For additional information about these terms see the <a target='_blank' href='/is/ISMManualOptimized.pdf'>Documentation and User's Manual</a>.  All of these may change over time as the soil volume increases with the growing plant roots which can cause the upward slants at the beginning of the season as the root grows. The dashed lines are from a 7 day forecast based on National Weather Service projected maximum and minimum temperatures for those days and the location information of the chosen weather station. The vertical line is today.  Enter your irrigation events (<font color='green'>green points</font>), or correct the estimated % available water content based on soil moisture measurements or estimates in the <a href='".$irrigationScheduler->session->basepath."&amp;action=daily-budget-existing-fields'>Daily Budget Table</a> to make the soil water content better represent your field conditions. Rainfall amounts (<font color='blue'>blue points</font>) are from the weather station. If you find that this model is consistently off, try editing the crop growth dates and crop coefficients in <a href='".$irrigationScheduler->session->basepath."&amp;action=advanced-update-field'>Field Settings</a>.</p>";
			if($irrigationScheduler->session->isMobileDevice > 0)
				$soilWaterChartHelp .= "<div style=\"clear:both;\"><center><a href='".$irrigationScheduler->session->basepath."&amp;action=soil-water-chart'>Back</a></center></div>";
			else
				$soilWaterChartHelp .= "<div style=\"clear:both;\"><center><a href='javascript:void(0)' onclick='$.fancybox.close();'>Close</a></center></div>";
	
	 		return $soilWaterChartHelp;

		}
	}

?>