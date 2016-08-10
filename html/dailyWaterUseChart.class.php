<?php
	class dailyWaterUseChart
	{
		public function toHTML()
		{
			global $irrigationScheduler;
			global $iform, $validation;
			global $database;
			
	 		$ETChart = "";
	 		if($irrigationScheduler->session->isMobileDevice > 0)
			{
				$ETChart .= "<form action=\"".$irrigationScheduler->session->basepath."&amp;action=ET-chart\" method=\"post\" name=\"ETChartForm\">";
		 		$ETChart .=  $irrigationScheduler->htmlFactory->selectField(true,"ETChartForm");
		 		$ETChart .= "</form>"; 		
				$ETChart .= "<div style=\"clear:both;\"><center><a href='".$irrigationScheduler->session->basepath."&amp;help=ET-chart'>Help</a></center></div>";
		 	}
		 	else
			{
				$ETChart .= "<div style=\"clear:both;\"><center><a id='etchartHelp' class='helplink' href='/is/ajaxPage.php?help=ET-chart'>Help</a></center></div>";
				$ETChart .= "<script type=\"text/javascript\">";
				$ETChart .= "$(document).ready(function(){";
				$ETChart .= "$('#etchartHelp').fancybox({ 'width':725, 'autoDimensions': false });".PHP_EOL;
				$ETChart .= "})";
				$ETChart .= "</script>";
			}
			if(isset($_SESSION['field']) && $_SESSION['field'] > 0)
			{
				$modelDate = dayofyear2date($irrigationScheduler->row_FieldInfo['plantDate'],"M d, Y",$irrigationScheduler->row_FieldInfo['year']);
				$todaysDate = date("M d, Y");
				if(dateDiff($modelDate,$todaysDate) >= 1)
				{	
				 	if($irrigationScheduler->session->isMobileDevice > 0)
				 	{
				 		$ETChart .= "<center><img width='98%' src=\"images/jpgraph_et-chart.php\" alt=\"Soil Water Chart\" /></center>";
				 	}
				 	else
				 	{
				 		$mt = microtime();
				 		$ETChart .= "<center><img src=\"/is/jpgraph_et-chart.php?mt=$mt\" alt=\"Soil Water Chart\" /></center>";
				 	}
				}
				else
				{
				 		$ETChart .= "<center>Too early in the season to plot the current field.</center>";
				}
			 }
			else
			{
				$ETChart .= "<center>Select a field to view the Daily Water User Chart.</center>";
			}
	 		return $ETChart;
		}
	}

?>