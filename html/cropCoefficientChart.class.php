<?php
	class cropCoefficientChart
	{
		public function toHTML()
		{
			global $irrigationScheduler;
			global $iform, $validation, $database;
	 		$cropCoefficientChart = "";
	 		if($irrigationScheduler->session->isMobileDevice > 0)
			{
				$cropCoefficientChart .= "<form action=\"".$irrigationScheduler->session->basepath."&amp;action=crop-coefficient-chart\" method=\"post\" name=\"cropCoefficientChartForm\">";
		 		$cropCoefficientChart .=  $irrigationScheduler->htmlFactory->selectField(true,"cropCoefficientChartForm");
		 		$cropCoefficientChart .= "</form>"; 		
				$cropCoefficientChart .= "<div style=\"clear:both;\"><center><a href='".$irrigationScheduler->session->basepath."&amp;help=crop-coefficient-chart'>Help</a></center></div>";
		 	}
		 	else
			{
				$cropCoefficientChart .= "<div style=\"clear:both;\"><center><a id='cropcoefficientchartHelp' class='helplink' href='/is/ajaxPage.php?help=crop-coefficient-chart'>Help</a></center></div>";
				$cropCoefficientChart .= "<script type=\"text/javascript\">";
				$cropCoefficientChart .= "$(document).ready(function(){";
				$cropCoefficientChart .= "$('#cropcoefficientchartHelp').fancybox({ 'width':725, 'autoDimensions': false });".PHP_EOL;
				$cropCoefficientChart .= "})";
				$cropCoefficientChart .= "</script>";
			}
			if(isset($_SESSION['field']) && $_SESSION['field'] > 0)
			{
				//If the current date is after the start of the field season, then show the chart
				if(dateDiff(dayofyear2date($irrigationScheduler->row_FieldInfo['plantDate'],"M d, Y",$irrigationScheduler->row_FieldInfo['year']),date("M d, Y")) >= 1)
				{	
				 	if($irrigationScheduler->session->isMobileDevice > 0)
				 	{
				 		$cropCoefficientChart .= "<center><img width='98%' src=\"images/jpgraph_crop-coefficient-chart.php\" alt=\"Soil Water Chart\" /></center>";
				 	}
				 	else
				 	{
				 		$mt = microtime();
				 		$cropCoefficientChart .= "<center><img src=\"/is/jpgraph_crop-coefficient-chart.php?mt=$mt\" alt=\"Soil Water Chart\" /></center>";
				 	}
				}
				else
				{
				 		$cropCoefficientChart .= "<center>Too early in the season to plot the current field.</center>";
				}
			}
			else
			{
				$cropCoefficientChart .= "<center>Select a field to view the Crop Coefficient Chart.</center>";
			}
	 		return $cropCoefficientChart;
		}
	}

?>