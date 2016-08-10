<?php
	class cumulativeWaterUseChart
	{
		public function toHTML()
		{
			global $irrigationScheduler;
			global $iform, $validation;
			global $database;
			
	 		$cumulativeWaterChart = "";
	 		if($irrigationScheduler->session->isMobileDevice > 0)
			{
				$cumulativeWaterChart .= "<form action=\"".$irrigationScheduler->session->basepath."&amp;action=cumulative-water-chart\" method=\"post\" name=\"cumulativeWaterChartForm\">";
		 		$cumulativeWaterChart .=  $irrigationScheduler->htmlFactory->selectField(true,"cumulativeWaterChartForm");
		 		$cumulativeWaterChart .= "</form>"; 		
				$cumulativeWaterChart .= "<div style=\"clear:both;\"><center><a href='".$irrigationScheduler->session->basepath."&amp;help=cumulative-water-chart'>Help</a></center></div>";
		 	}
		 	else
			{
				$cumulativeWaterChart .= "<div style=\"clear:both;\"><center><a id='cumulativewaterchartHelp' class='helplink' href='/is/ajaxPage.php?help=cumulative-water-chart'>Help</a></center></div>";
				$cumulativeWaterChart .= "<script type=\"text/javascript\">";
				$cumulativeWaterChart .= "$(document).ready(function(){";
				$cumulativeWaterChart .= "$('#cumulativewaterchartHelp').fancybox({ 'width':725, 'autoDimensions': false });".PHP_EOL;
				$cumulativeWaterChart .= "})";
				$cumulativeWaterChart .= "</script>";
			}
			if(isset($_SESSION['field']) && $_SESSION['field'] > 0)
			{
				$modelDate = dayofyear2date($irrigationScheduler->row_FieldInfo['plantDate'],"M d, Y",$irrigationScheduler->row_FieldInfo['year']);
				$todaysDate = date("M d, Y");
				if(dateDiff($modelDate,$todaysDate) >= 1)
				{	
				 	if($irrigationScheduler->session->isMobileDevice > 0)
				 	{
				 		$cumulativeWaterChart .= "<center><img width='98%' src=\"images/jpgraph_cumulative-water-chart.php\" alt=\"Soil Water Chart\" /></center>";
				 	}
				 	else
				 	{
				 		$mt = microtime();
				 		$cumulativeWaterChart .= "<center><img src=\"/is/jpgraph_cumulative-water-chart.php?mt=$mt\" alt=\"Soil Water Chart\" /></center>";
				 	}
				}
				else
				{
				 		$cumulativeWaterChart .= "<center>Too early in the season to plot the current field.</center>";
				}
			}
			else
			{
				$cumulativeWaterChart .= "<center>Select a field to view the Cumulative Water Chart.</center>";
			}
	 		return $cumulativeWaterChart;
		}
	}

?>