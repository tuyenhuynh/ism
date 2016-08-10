<?php
	class deepWaterLossChart
	{
		public function toHTML()
		{
			global $irrigationScheduler;
			global $iform, $validation, $database;
	 		$deepPercolationChart = "";
	 		if($irrigationScheduler->session->isMobileDevice > 0)
			{
				$deepPercolationChart .= "<form action=\"".$irrigationScheduler->session->basepath."&amp;action=deep-percolation-chart\" method=\"post\" name=\"deepPercolationChartForm\">";
		 		$deepPercolationChart .=  $irrigationScheduler->htmlFactory->selectField(true,"deepPercolationChartForm");
		 		$deepPercolationChart .= "</form>"; 		
				$deepPercolationChart .= "<div style=\"clear:both;\"><center><a href='".$irrigationScheduler->session->basepath."&amp;help=deep-percolation-chart'>Help</a></center></div>";
		 	}
		 	else
			{
				$deepPercolationChart .= "<div style=\"clear:both;\"><center><a id='deeppercolationchartHelp' class='helplink' href='/is/ajaxPage.php?help=deep-percolation-chart'>Help</a></center></div>";
				$deepPercolationChart .= "<script type=\"text/javascript\">";
				$deepPercolationChart .= "$(document).ready(function(){";
				$deepPercolationChart .= "$('#deeppercolationchartHelp').fancybox({ 'width':725, 'autoDimensions': false });".PHP_EOL;
				$deepPercolationChart .= "})";
				$deepPercolationChart .= "</script>";
			}
	
			if(isset($_SESSION['field']) && $_SESSION['field'] > 0)
			{
	
				$modelDate = dayofyear2date($irrigationScheduler->row_FieldInfo['plantDate'],"M d, Y",$irrigationScheduler->row_FieldInfo['year']);
				$todaysDate = date("M d, Y");
				if(dateDiff($modelDate,$todaysDate) >= 1)
				{	
				 	if($irrigationScheduler->session->isMobileDevice > 0)
				 	{
				 		$deepPercolationChart .= "<center><img width='98%' src=\"images/jpgraph_dp-chart.php\" alt=\"Soil Water Chart\" /></center>";
				 	}
				 	else
				 	{
				 		$mt = microtime();
				 		$deepPercolationChart .= "<center><img src=\"/is/jpgraph_dp-chart.php?mt=$mt\" alt=\"Soil Water Chart\" /></center>";
				 	}
				}
				else
				{
				 		$deepPercolationChart .= "<center>Too early in the season to plot the current field.</center>";
				}
			 }
			else
			{
				$deepPercolationChart .= "<center>Select a field to view the Deep Water Loss Chart.</center>";
			}
	 		return $deepPercolationChart;
		}
	}

?>