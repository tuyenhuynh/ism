<?php
	class deepWaterLossChart
	{
		public function toHTML()
		{
			global $irrigationScheduler;
	 		$deepPercolationChartHelp = "";
	 		$deepPercolationChartHelp .= "<h2><a name=\"top\"></a>Deep Water Loss Chart</h2>";
	 		$deepPercolationChartHelp .= "<p>When more water is applied than can be held in the root zone (soil water content exceeds field capacity), then this excess water moves down past the bottom of the root zone and is lost to deep percolation.  This chart shows the cumulative water losses to deep percolation.</p>";
			if($irrigationScheduler->session->isMobileDevice > 0)
				$deepPercolationChartHelp .= "<div style=\"clear:both;\"><center><a href='".$irrigationScheduler->session->basepath."&amp;action=deep-percolation-chart'>Back</a></center></div>";
			else
				$deepPercolationChartHelp .= "<div style=\"clear:both;\"><center><a href='javascript:void(0)' onclick='$.fancybox.close();'>Close</a></center></div>";
	
	 		return $deepPercolationChartHelp;
		}
	}

?>