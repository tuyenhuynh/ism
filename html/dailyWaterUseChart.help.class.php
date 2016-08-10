<?php
	class dailyWaterUseChart
	{
		public function toHTML()
		{
			global $irrigationScheduler;
	 		$ETChartHelp = "";
	 		$ETChartHelp .= "<h2><a name=\"top\"></a>Daily Water Use Chart</h2>";
	 		$ETChartHelp .= "<p>This shows the daily crop water use (evapotranspiration, or ETc) over the specified growing season.  This is calculated as ETc = ETr x Kc where ETr is alfalfa reference evapotranspiration and Kc is the crop coefficient for that day.  These values are affected by the weather (hot, dry, sunny, and windy days cause the plants to use more water), the crop coefficients, and the water stress status of the plant (below MAD, the crop water use is proportionately decreased as described in the users manual).</p>";
			if($irrigationScheduler->session->isMobileDevice > 0)
				$ETChartHelp .= "<div style=\"clear:both;\"><center><a href='".$irrigationScheduler->session->basepath."&amp;action=ET-chart'>Back</a></center></div>";
			else
				$ETChartHelp .= "<div style=\"clear:both;\"><center><a href='javascript:void(0)' onclick='$.fancybox.close();'>Close</a></center></div>";
	
	 		return $ETChartHelp;
	 	}
	}

?>