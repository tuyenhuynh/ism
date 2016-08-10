<?php
	class cropCoefficientChart
	{
		public function toHTML()
		{
			global $irrigationScheduler;
	 		$cropCoefficientChartHelp = "";
	 		$cropCoefficientChartHelp .= "<h2><a name=\"top\"></a>Crop Coefficient Chart</h2>";
	 		$cropCoefficientChartHelp .= "<p>Crop coefficients (Kc) are multiplied by the daily reference alfalfa evapotranspiration (ETr) rate that is calculated from the measured weather parameters from your chosen weather station.  This chart shows the crop coefficient curve used for this field over the growing season.  Also shown is how the root zone depth is estimated over time.  The values that define these curves can be viewed and edited on the \"Field Settings\" page.</p>";
			if($irrigationScheduler->session->isMobileDevice > 0)
				$cropCoefficientChartHelp .= "<div style=\"clear:both;\"><center><a href='".$irrigationScheduler->session->basepath."&amp;action=crop-coefficient-chart'>Back</a></center></div>";
			else
				$cropCoefficientChartHelp .= "<div style=\"clear:both;\"><center><a href='javascript:void(0)' onclick='$.fancybox.close();'>Close</a></center></div>";
	
	 		return $cropCoefficientChartHelp;
		}
	}

?>