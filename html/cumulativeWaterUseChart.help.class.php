<?php
	class cumulativeWaterUseChart
	{
		public function toHTML()
		{
			global $irrigationScheduler;
	 		$cumulativeWaterChartHelp = "";
	 		$cumulativeWaterChartHelp .= "<h2><a name=\"top\"></a>Cumulative Water Chart</h2>";
	 		$cumulativeWaterChartHelp .= "<p>Shows the cumulative crop evapotranspiration (ETc, or crop water use), irrigation, and rainfall over the specified growing season.  The season totals are given in the chart legend.</p>";
	
			if($irrigationScheduler->session->isMobileDevice > 0)
				$cumulativeWaterChartHelp .= "<div style=\"clear:both;\"><center><a href='".$irrigationScheduler->session->basepath."&amp;action=cumulative-water-chart'>Back</a></center></div>";
			else
				$cumulativeWaterChartHelp .= "<div style=\"clear:both;\"><center><a href='javascript:void(0)' onclick='$.fancybox.close();'>Close</a></center></div>";
	
	 		return $cumulativeWaterChartHelp;

		}
	}

?>