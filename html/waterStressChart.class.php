<?php
	class waterStressChart
	{
		public function toHTML()
		{
			global $irrigationScheduler;
			global $iform, $validation, $database;
	 		$waterStressCoefficientChart = "";
	 		if($irrigationScheduler->session->isMobileDevice > 0)
			{
				$waterStressCoefficientChart .= "<form action=\"".$irrigationScheduler->session->basepath."&amp;action=crop-coefficient-chart\" method=\"post\" name=\"waterStressCoefficientChartForm\">";
		 		$waterStressCoefficientChart .=  $irrigationScheduler->htmlFactory->selectField(true,"waterStressCoefficientChartForm");
		 		$waterStressCoefficientChart .= "</form>"; 		
				$waterStressCoefficientChart .= "<div style=\"clear:both;\"><center><a href='".$irrigationScheduler->session->basepath."&amp;help=water-stress-coefficient-chart'>Help</a></center></div>";
		 	}
		 	else
			{
				$waterStressCoefficientChart .= "<div style=\"clear:both;\"><center><a id='waterStressCoefficientChartHelp' class='helplink' href='/is/ajaxPage.php?help=crop-coefficient-chart'>Help</a></center></div>";
				$waterStressCoefficientChart .= "<script type=\"text/javascript\">";
				$waterStressCoefficientChart .= "$(document).ready(function(){";
				$waterStressCoefficientChart .= "$('#waterStressCoefficientChartHelp').fancybox({ 'width':725, 'autoDimensions': false });".PHP_EOL;
				$waterStressCoefficientChart .= "})";
				$waterStressCoefficientChart .= "</script>";
			}
			if(isset($_SESSION['field']) && $_SESSION['field'] > 0)
			{
				//If the current date is after the start of the field season, then show the chart
				if(dateDiff(dayofyear2date($irrigationScheduler->row_FieldInfo['plantDate'],"M d, Y",$irrigationScheduler->row_FieldInfo['year']),date("M d, Y")) >= 1)
				{	
				 	if($irrigationScheduler->session->isMobileDevice > 0)
				 	{
				 		$waterStressCoefficientChart .= "<center><img width='98%' src=\"images/jpgraph_water-stress-coefficient-chart.php\" alt=\"Soil Water Chart\" /></center>";
				 	}
				 	else
				 	{
				 		$mt = microtime();
				 		$waterStressCoefficientChart .= "<center><img src=\"/is/jpgraph_water-stress-coefficient-chart.php?mt=$mt\" alt=\"Soil Water Chart\" /></center>";
				 	}
				}
				else
				{
				 		$waterStressCoefficientChart .= "<center>Too early in the season to plot the current field.</center>";
				}
			}
			else
			{
				$waterStressCoefficientChart .= "<center>Select a field to view the Crop Coefficient Chart.</center>";
			}
	 		return $waterStressCoefficientChart;
		}
	}

?>