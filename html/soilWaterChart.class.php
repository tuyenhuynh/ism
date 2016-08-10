<?php
	class soilWaterChart
	{
		public function toHTML()
		{
			global $irrigationScheduler;
			global $iform, $validation;
			global $database;
			
	 		
	 		$soilWaterChart = "";
	 		if($irrigationScheduler->session->isMobileDevice > 0)
			{
				$soilWaterChart .= "<form action=\"".$irrigationScheduler->session->basepath."&amp;action=soil-water-chart\" method=\"post\" name=\"SoilWaterChartForm\">";
		 		$soilWaterChart .=  $irrigationScheduler->htmlFactory->selectField(true,"SoilWaterChartForm");
		 		$soilWaterChart .= "</form>"; 		
				$soilWaterChart .= "<div style=\"clear:both;\"><center><a href='".$irrigationScheduler->session->basepath."&amp;help=soil-water-chart'>Help</a></center></div>";
		 	}
		 	else
			{
				$soilWaterChart .= "<div style=\"clear:both;\"><center><a id='soilwaterchartHelp' class='helplink' href='/is/ajaxPage.php?help=soil-water-chart'>Help</a></center></div>";
				$soilWaterChart .= "<script type=\"text/javascript\">";
				$soilWaterChart .= "$(document).ready(function(){";
				$soilWaterChart .= "$('#soilwaterchartHelp').fancybox({ 'width':725, 'autoDimensions': false });".PHP_EOL;
				$soilWaterChart .= "})";
				$soilWaterChart .= "</script>";
			}
			if(isset($_SESSION['field']) && $_SESSION['field'] > 0)
			{
				$modelDate = dayofyear2date($irrigationScheduler->row_FieldInfo['plantDate'],"M d, Y",$irrigationScheduler->row_FieldInfo['year']);
				$todaysDate = date("M d, Y");
				if(dateDiff($modelDate,$todaysDate) >= 1)
				{	
				 	if($irrigationScheduler->session->isMobileDevice > 0)
				 	{
				 		$soilWaterChart .= "<center><img width='98%' src=\"images/jpgraph_soil-water-chart.php\" alt=\"Soil Water Chart\" /></center>";
				 	}
				 	else
				 	{
				 		$mt = microtime();
				 		$soilWaterChart .= "<center><img src=\"/is/jpgraph_soil-water-chart.php?mt=$mt\" alt=\"Soil Water Chart\" /></center>";
				 	}
				}
				else
				{
				 		$soilWaterChart .= "<center>Too early in the season to plot the current field.</center>";
				}
			 }
			else
			{
				$soilWaterChart .= "<center>Select a field to view the soil water chart.</center>";
			}


	
			return $soilWaterChart;
		}
	}

?>