<?php
	class waterStressChart
	{
		public function toHTML()
		{
			global $irrigationScheduler;
	 		$cropCoefficientChartHelp = "";
	 		$cropCoefficientChartHelp .= "<h2><a name=\"top\"></a>Water Stress Chart</h2>";
	 		$cropCoefficientChartHelp .= "<p>When the soil water content falls below the First Water Stress point (MAD) then the plant uses less water because it shuts down.  The reduction in water use for that day is roughly proportional to the reduction in yield.  This chart shows the % yield reduction on each day due to water stress (soil water content falling below the MAD line).  The season total estimated yield loss due to water stress is also calculated and displayed at the bottom of the chart.</p>";
			if($irrigationScheduler->session->isMobileDevice > 0)
				$cropCoefficientChartHelp .= "<div style=\"clear:both;\"><center><a href='".$irrigationScheduler->session->basepath."&amp;action=water-stress-coefficient-chart'>Back</a></center></div>";
			else
				$cropCoefficientChartHelp .= "<div style=\"clear:both;\"><center><a href='javascript:void(0)' onclick='$.fancybox.close();'>Close</a></center></div>";
	
	 		return $cropCoefficientChartHelp;
		}
	}

?>