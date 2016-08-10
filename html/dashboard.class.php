<?php
	class dashboard
	{
		public function toHTML()
		{
			global $irrigationScheduler;
			global $iform, $validation;
			$register = "";

			$register .="<h2>Soil Water Dashboard</h2>";
			$register .= "<form action=\"".$irrigationScheduler->session->basepath."&amp;action=dashboard\" method=\"post\" name=\"SoilWaterChartForm\">";
			$register .=  $irrigationScheduler->htmlFactory->selectField(true,"SoilWaterChartForm");
			$register .= "</form>"; 		

			$cnt = date("z") +1;
			
			if( !isset($irrigationScheduler->row_FieldInfo['year']) || $irrigationScheduler->row_FieldInfo['year'] <> date("Y") || $cnt <= $irrigationScheduler->row_FieldInfo['plantDate'] || $cnt > $irrigationScheduler->row_FieldInfo['seasonEndDate']  )
			{
				$register .= "<center>Dashboard does not work outside of the growing season. Use Daily Budget Table or Soil Water Chart to see historical data.</center>";
			}
			else
			{
				$label = "hrs";
				$strWaterDeficitAmountHrs = round($irrigationScheduler->row_tblIndividField[date("z")]['rootZoneWaterDeficit'] / $irrigationScheduler->row_FieldInfo['applicationrate'],1);
	      if($irrigationScheduler->row_FieldInfo['applicationrate'] == 1)
	      {
	      	$label = "in.";
	      }
				$strWaterDeficitAmount = round($irrigationScheduler->row_tblIndividField[date("z")]['rootZoneWaterDeficit'],1);
	
	
	
				$register .="<form onsubmit='document.getElementById(\"editLink$cnt\").innerHTML=\"Wait\"; ";
				$register .= "$.ajax({ ".PHP_EOL;
				$register .= "data: $(\"#gaugeForm\").serialize() ";
				$register .= ", ";
				$register .= "type: \"POST\",";
				$register .= "url: \"/ism/".$irrigationScheduler->session->processPath."\",";
				
				$register .= "dataType: \"text\" ";
				$register .= ", success: function MySuccess(data, textStatus, jqXHR) { parent.location.href = \"".$irrigationScheduler->session->basepath."index.php?m=1&action=dashboard\"; } ";
				$register .= ", error: function MyFailure(data, textStatus, jqXHR) {  parent.location.href = \"".$irrigationScheduler->session->basepath."index.php?m=1&action=dashboard\"; } ";
				$register .= "}); ";
				$register .= " return false;' id='gaugeForm' name='gaugeForm' action='' method='post'>";
				$register .= "<div style='width:39%;float:left;' id='soilWater'>Loading...please wait</div>";
				$register .= "<div style='width:59%;float:right;'>";
				$register .= "<table style='border-collapse:collapse'>";
	
				$register .= "<tr><td style='border-bottom:1px solid black;'>";
				$register .= "<br/>This Morning's Soil Water Deficit:";
				$register .= "</td>";
				$register .= "<td style='border-bottom:1px solid black;vertical-align:bottom'>";
	
	      if($irrigationScheduler->row_FieldInfo['applicationrate'] == 1)
	      {
					$register .= "$strWaterDeficitAmount $label";
					$irrigationToday = sprintf('%3.2f',round($irrigationScheduler->row_tblIndividField[$cnt]['irrig'],2));
				}
				else
				{
					$register .= "$strWaterDeficitAmount in. or<br/>$strWaterDeficitAmountHrs hrs";
					$irrigationToday = sprintf('%3.2f',$irrigationScheduler->row_tblIndividField[$cnt]['irrig']/round($irrigationScheduler->row_FieldInfo['applicationrate'] ,2));
				}
	
				$register .= "</td>";
				$register .= "</tr>";
				
				$register .= "<tr><td style='border-bottom:1px solid black;vertical-align:top;'>";
				$register .= "Today's Irrigation:";
				$register .= "</td><td style='border-bottom:1px solid black;vertical-align:bottom;'>";
				$register .= "$irrigationToday $label";
				$register .= "</td></tr>";
	
	
				$register .= "<tr><td>I Irrigated Today:";
				$register .= "</td><td nowrap=nowrap>";
				$register .= "<input type='text' size=2 name='irrigation' id='irrigation' value=''> $label";
				$register .= "</td></tr>";
				$register .= "<tr>";
				$register .= "<td align='center' colspan=2>";
				$register .= "<a id='editLink$cnt' style='text-decoration:underline;' href='javascript:void(0);' onclick='this.innerHTML=\"Wait\"; ";
				$register .= "$.ajax({ ".PHP_EOL;
				$register .= "data: $(\"#gaugeForm\").serialize() ";
				$register .= ", ";
				$register .= "type: \"POST\",";
				$register .= "url: \"/ism/".$irrigationScheduler->session->processPath."\",";
				
				$register .= "dataType: \"text\" ";
				$register .= ", success: function MySuccess(data, textStatus, jqXHR) { parent.location.href = \"".$irrigationScheduler->session->basepath."index.php?m=1&action=dashboard\"; } ";
				$register .= ", error: function MyFailure(data, textStatus, jqXHR) {  parent.location.href = \"".$irrigationScheduler->session->basepath."index.php?m=1&action=dashboard\"; } ";
				$register .= "}); ";
				$register .= "'>Save</a>";
				$register .= "</td></tr>";			
				
				$register .= "<tr><td colspan=2 style='font-size:10px;'>";
				$register .= "<br/>Green is good. Crops increasingly stressed below green.";
				$register .= "</td></tr>";

				$register .= "</table>";
				$register .= "</div>";
	
				$register .="<input type='hidden' name='subdashboard' value='1' />";
				//$register .="<input type='submit' value='Register' /> <br />";
				$register .="</form><div style='clear:both'></div>".PHP_EOL;;

				$register .= "<script type='text/javascript'>".PHP_EOL;
				$register .= "<!-- ".PHP_EOL;
				$register .= "$(document).ready(function(){ ".PHP_EOL;
				$register .= "var myDirection = new FusionCharts( ";
				$register .= "'/FusionWidgets/AngularGauge.swf',";
				$register .= "'myDirectionId', ";
				$register .= "'100%', ";
				$register .= "'210', ";
				$register .= "'0', ";
				$register .= "'1' );      ".PHP_EOL;
				$register .= "myDirection.setXMLUrl('ajax/angularGauge.php');      ".PHP_EOL;
				$register .= "myDirection.setTransparent(true); ".PHP_EOL;
				$register .= "myDirection.render('soilWater');         ".PHP_EOL;
				$register .= "}); ";
				$register .= "//-->";
				$register .= "</script>";

			}

			return $register;

		}
	}

?>