<?php
	class dailyBudgetTable
	{
		public function toHTML()
		{
			global $irrigationScheduler;
			global $iform, $validation;
			global $database;
			unset ($_SESSION['BSR']);
			
			if(isset($_REQUEST['BUDGETSTARTROW']))
			{
				//Get first row
				$firstRow = mysql_real_escape_string($_REQUEST['BUDGETSTARTROW']);
			}
			else
			{
				//Default first row
				$firstRow = 0;
			}
			$_SESSION['BSR'] = $firstRow;
				
			$displayTable = $this->DisplayBudgetTable($firstRow);
			$dailyBudgetTable = "";
			$dailyBudgetTable .= $this->BudgetTableJavaScripts();
	 		if($irrigationScheduler->session->isMobileDevice > 0)
			{
				$dailyBudgetTable .= "<h2><a name=\"top\"></a>7-Day Daily Budget Table</h2>";
				$dailyBudgetTable .= "<form action=\"".$irrigationScheduler->session->basepath."&amp;action=daily-budget-existing-fields\" method=\"post\" name=\"dailyBudgetTableForm\">";
				$dailyBudgetTable .= $irrigationScheduler->htmlFactory->selectField(true,"dailyBudgetTableForm");
				$dailyBudgetTable .= "</form>";		
				$dailyBudgetTable .= "<div style=\"clear:both;\"><center><a href='".$irrigationScheduler->session->basepath."&amp;help=daily-budget-existing-fields'>Help</a></center></div>";
			}
		 	else
			{
				$dailyBudgetTable .= "<div style=\"clear:both;\"><center><a id='dailybudgetexistingfieldsHelp' class='helplink' href='/is/ajaxPage.php?help=daily-budget-existing-fields'>Help</a></center></div>";
				$dailyBudgetTable .= "<script type=\"text/javascript\">";
				$dailyBudgetTable .= "$(document).ready(function(){";
				$dailyBudgetTable .= "$('#dailybudgetexistingfieldsHelp').fancybox({ 'width':725, 'autoDimensions': false });".PHP_EOL;
				$dailyBudgetTable .= "})";
				$dailyBudgetTable .= "</script>";
			}
			
			$dailyBudgetTable .= "<form name='BudgetForm' method='post' action=''>";
			$dailyBudgetTable .= "<input type=hidden id='doy' name='doy'>";
			$dailyBudgetTable .= "<input type=hidden id='editbudgetday' name='editbudgetday' value=''>";
			$dailyBudgetTable .= "<input type=hidden id='BUDGETSTARTROW' name='BUDGETSTARTROW' value=''>";
			$dailyBudgetTable .= "</form>";
	
			$modelDate = dayofyear2date($irrigationScheduler->row_FieldInfo['plantDate'],"M d, Y",$irrigationScheduler->row_FieldInfo['year']);
			$todaysDate = date("M d, Y");
			if(dateDiff($modelDate,$todaysDate) >= 1)
			{
				$dailyBudgetTable .= $displayTable;
		 		if($irrigationScheduler->session->isMobileDevice == 0)
				{
					$_SESSION['csvFileName'] = "ismData.csv";
					$dailyBudgetTable .= "<form name='CsvOutputForm' class='noprint' method='post' action='/utils/CsvOutput.php' style='display:inline'>".PHP_EOL;
					$dailyBudgetTable .= "<input name='csvFile' type='hidden' value='".$_SESSION['csvFileName']."' />".PHP_EOL;
					$dailyBudgetTable .= "<input onclick='if(this.value == \"Download Data\") { document.CsvOutputForm.submit(); return false; } else {alert(\"Sorry, the system is still preparing data for download. This feature is normally available in 60 seconds or less.  The button will update to show Download Data when the current output is available.  Please contact AWN if you would like further assistance.\")}' id='downloadbutton' name='downloadbutton' type=button value='Download Data'>".PHP_EOL;
					$dailyBudgetTable .= "</form>".PHP_EOL;
				}
			}
			else
			{
				$dailyBudgetTable .= "The field's season has not started yet, the budget table is not available.";
			}
			return $dailyBudgetTable;
		}


		public function DisplayBudgetTable($firstRow)
		{
			global $irrigationScheduler;
			if(isset($_SESSION['whichday']))
			{
				$tDay = $_SESSION['whichday'];
				unset($_SESSION['whichday']);
			}
			unset($_SESSION['BUDGETSTARTROW']);
			$_SESSION['BSR'] = $firstRow;


			$todaysDOY = date("z") + 1;
			$counterStart = $this->getCounterStart($todaysDOY, $firstRow);
			$StarterNumber = $this->getStarterNumber($counterStart);

			if($irrigationScheduler->session->isMobileDevice > 0) //if mobile device
			{
				$endCount = $StarterNumber + 6;
				$csv = "";
				$detailsFontSize= ".7em";
				$detailsPadding = "";
			}
			else //if desktop mode
			{
				$StarterNumber = $irrigationScheduler->row_FieldInfo['plantDate'];
				$endCount = date("z");
				
				if($irrigationScheduler->row_FieldInfo['useNDFDforecast'] == 1)
					$endCount += 7;;			
				if($endCount > $irrigationScheduler->row_FieldInfo['seasonEndDate'] )
					$endCount = $irrigationScheduler->row_FieldInfo['seasonEndDate'];

				$csv = $this->initializeCSV();
				//set font size for desktop mode
				$detailsFontSize= "12pt";
				$detailsPadding = "padding-right:25px;";
	      	
	    }
	    
			$retVal = $this->makeTableHeader(); 
			//Loop through each day that needs to be displayed
			for($cnt = $StarterNumber; ($cnt <=  $endCount ) && ($cnt <= $irrigationScheduler->row_FieldInfo['seasonEndDate']); $cnt++)
			{
				$retVal .= $this->makeTableRow($cnt);
				$_SESSION['applicationrate'] = $irrigationScheduler->row_FieldInfo['applicationrate'];
				$retVal .= $this->detailsDiv($cnt,$detailsFontSize,$detailsPadding);
				$retVal .= $this->editDiv($cnt); 
			}
			$retVal .= $this->closeTable();
	
	 		$_SESSION ['csvData'] = $csv;

			if($irrigationScheduler->session->isMobileDevice > 0)
			{
				$retVal .= $this->buildNavigation($counterStart, $StarterNumber, $todaysDOY);
				$retVal .= $this->intializeDatePicker();
			}
			else//This runs only if not a mobile device
			{
				//Make CSV info
				if(isset($irrigationScheduler->row_tblIndividField[$cnt]['fieldYear']))
				{
					$this->makeCSVDay($csv, $cnt);
				}//End Make CSV Info	
				
			 	if(isset($tDay) && is_numeric($tDay) && $tDay > 0)  //If there was a day edited, make sure it remains visible
			 	{
			 		$retVal .= $this->makeDayVisible($tDay);
			 	}
			 	
			}
			$retVal .= "<form name='BTable' method='post' action='".$irrigationScheduler->session->basepath."&action=daily-budget-existing-fields'><input type=hidden name='budgettablepreviousnext' value='1'/><input type=hidden name='BUDGETSTARTROW' value='$firstRow'/></form>";
			return $retVal;
		}

		public function makeDayVisible($tDay)
		{
		 	$whichDay = mysql_real_escape_string($_REQUEST['whichday']);
		 	$retVal = "<script type='text/javascript'>  ".PHP_EOL;
 			$retVal .= "$(document).ready(function(){  $(window).scrollTop($('#row".$tDay."').position().top); ".PHP_EOL;
			$retVal .= "}); ".PHP_EOL;
			$retVal .= "</script>";								
			return $retVal;
		}
		public function getStarterNumber($counterStart)
		{
			global $irrigationScheduler;
			//Make table for counterStart to 7 days		
			if( ($counterStart-7) < $irrigationScheduler->row_FieldInfo['plantDate'])
			{
				$StarterNumber = $irrigationScheduler->row_FieldInfo['plantDate'];
			}
			else
			{
				$StarterNumber = $counterStart - 7;
			}
			return $StarterNumber;
		}
		
		public function getCounterStart($todaysDOY,&$firstRow)
		{
			global $irrigationScheduler;
			if(date("Y") == $irrigationScheduler->row_FieldInfo['year'])
			{
				if($todaysDOY > $irrigationScheduler->row_FieldInfo['seasonEndDate'])
				{
					$counterStart = $irrigationScheduler->row_FieldInfo['seasonEndDate'];
				}
				else
				{
					//If this is going to be a forecast, don't set it to todaysDOY; echo "First Row: $firstRow</br>";
					if($firstRow == $todaysDOY + 7)
					{
						$counterStart = $firstRow;
					}
					else
					{
						$counterStart = $todaysDOY;
					}
				}
			}
			else
			{
				$counterStart = $irrigationScheduler->row_FieldInfo['seasonEndDate'];
			}
			if($firstRow >= $counterStart)
				$firstRow = $counterStart;
				
			if($firstRow > 0 && $firstRow < $counterStart)
				$counterStart = $firstRow;

			return $counterStart;
		}
		public function initializeCSV()
		{
				global $irrigationScheduler;
		    // create and allow CSV file    
	      $csv = "Field Report from Irrigation Scheduler Mobile.\n";
	      $csv .= "All units are in inches.\n";
	      $csv .= "\"Washington State University, IAREC, and AgWeatherNet.\n\"";
	      $csv .= "User: ".$irrigationScheduler->session->username."";
	      //Fix soilTexture name
	      $csv .= "Soil Type: ".$irrigationScheduler->row_FieldInfo['soilID']."\n";  
	      //Fix start date
	      $csv .= "Start Date: ".dayofyear2date( $irrigationScheduler->row_FieldInfo['plantDate'], "m-d-Y",$irrigationScheduler->row_FieldInfo['year'])."\n";
	      //Fix enddate
	      $csv .= "End Date: ".dayofyear2date( $irrigationScheduler->row_FieldInfo['seasonEndDate'], "m-d-Y",$irrigationScheduler->row_FieldInfo['year'])."\n";
	      $csv .= "\n";
	      $csv .= "\"Day of Year\",\"Date\",\"Reference ET\",\"Crop Coefficient\",\"Crop ET\",\"Precipitation\",\"Irrigation\",";
	      $csv .= "\"Root Depth\",\"Field Capacity\",\"Wilting Point\",\"Avail. Water Capacity\",\"Water Storage At MAD\",\"Current Water Storage\",";
	      $csv .= "\"Modeled Available Water\",\"Root Zone Water Deficit\",\"Deep Percolation\",\"Volumetric Water Content\",\"Stress Coefficient\",";
	      $csv .= "\"Measured Available Water\",\"Modified Flag\",\"Forage Cutting Flag\",";
	      $csv .= "\n";		
	      return $csv;
		}
		public function closeTable()
		{
	 		$retVal = "</tbody>";
	 		$retVal .= "</table>";		
	 		return $retVal;
		}
		public function buildNavigation($counterStart, $StarterNumber, $todaysDOY )
		{
				global $irrigationScheduler;
				$retVal = "";
				$retVal .= "<table summary='DatePicker' width='100%'>";
				if(( $counterStart - $irrigationScheduler->row_FieldInfo['plantDate']) < 7)
				{
					$retVal .= "<tr>";
					$retVal .= "<td width='33%'  align=left>&nbsp;&nbsp;&nbsp;</td>";
					$retVal .= "<td width='33%' align='center'><input type='text' style='text-align:center' id='jumpdate' size=12  name='jumpdate' value='".dayofyear2date($StarterNumber,"M d, Y",$irrigationScheduler->row_FieldInfo['year'])."'></td>";
					$retVal .= "<td align='right'><a href='javascript:void(0);' onclick='document.BTable.BUDGETSTARTROW.value=".($counterStart+7).";document.BTable.submit(); return false;'>&gt;&gt;&gt;</a>&nbsp;&nbsp;&nbsp;<a href='javascript:void(0);' onclick='document.BTable.BUDGETSTARTROW.value=".($irrigationScheduler->row_FieldInfo['seasonEndDate']).";document.BTable.submit(); return false;'>&gt;&gt;|</a></td>";
					$retVal .= "</tr>";
				}
				elseif($counterStart >= $irrigationScheduler->row_FieldInfo['seasonEndDate'] || (date("Y") == $irrigationScheduler->row_FieldInfo['year'] && dateDiff(dayofyear2date($counterStart,"M d, Y",$irrigationScheduler->row_FieldInfo['year']),date("M d, Y")) <= 0 ))
				{
					$retVal .= "<tr>";
					$retVal .= "<td width='33%' align=left><a href='javascript:void(0);' onclick='document.BTable.BUDGETSTARTROW.value=".($irrigationScheduler->row_FieldInfo['plantDate']+6).";document.BTable.submit(); return false;'>|&lt;&lt;</a>&nbsp;&nbsp;&nbsp;<a href='javascript:void(0);' onclick='document.BTable.BUDGETSTARTROW.value=".($counterStart-7).";document.BTable.submit(); return false;'>&lt;&lt;&lt;</a></td>";
					$retVal .= "<td align='center' width='33%'><input type='text' style='text-align:center' id='jumpdate' size=12  name='jumpdate' value='".dayofyear2date($StarterNumber,"M d, Y",$irrigationScheduler->row_FieldInfo['year'])."'></td>";
					if($irrigationScheduler->row_FieldInfo['useNDFDforecast'] == 1 && $counterStart == $todaysDOY && $irrigationScheduler->row_FieldInfo['seasonEndDate']  > date("z") && $irrigationScheduler->row_FieldInfo['year'] == date("Y") )
					{
						$retVal .= "<td  width='33%'>&nbsp;&nbsp;&nbsp;<a href='javascript:void(0);' onclick='document.BTable.BUDGETSTARTROW.value=".($counterStart + 7).";document.BTable.submit(); return false;'>Forecast</a></td>";
					}
					else
					{
						$retVal .= "<td  width='33%'>&nbsp;&nbsp;&nbsp;</td>";
					}
					$retVal .= "</tr>";
				}
				else
				{
					$retVal .= "<tr><td width='33%' align=left><a href='javascript:void(0);' onclick='document.BTable.BUDGETSTARTROW.value=".($irrigationScheduler->row_FieldInfo['plantDate']+6).";document.BTable.submit(); return false;'>|&lt;&lt;</a>&nbsp;&nbsp;&nbsp;<a href='javascript:void(0);' onclick='document.BTable.BUDGETSTARTROW.value=".($counterStart-7).";document.BTable.submit(); return false;'>&lt;&lt;&lt;</a></td><td width='33%' align='center'><input type='text' style='text-align:center' id='jumpdate' size=12 name='jumpdate' value='".dayofyear2date($StarterNumber,"M d, Y",$irrigationScheduler->row_FieldInfo['year'])."'></td><td align='right' width='33%'><a href='javascript:void(0);' onclick='document.BTable.BUDGETSTARTROW.value=".($counterStart+7).";document.BTable.submit(); return false;'>&gt;&gt;&gt;</a>&nbsp;&nbsp;&nbsp;<a href='javascript:void(0);' onclick='document.BTable.BUDGETSTARTROW.value=".($irrigationScheduler->row_FieldInfo['seasonEndDate']).";document.BTable.submit(); return false;'>&gt;&gt;|</a></td></tr>";
				}
				$retVal .= "</table>";	
				return $retVal;
		}
		public function makeTableHeader()
		{
			global $irrigationScheduler;
			$retVal = "";
			$retVal .= "<table summary='Water Use, Rain, Irrigation, Available Water, Water Deficit table' style='width:100%'>";
			$retVal .= "<thead>";
			$retVal .= "<tr><th><br/><br/>Date</th><th>Water Use<br/>(in)</th><th>Rain &amp; Irrig<br/>(in)</th>";
			if($irrigationScheduler->row_FieldInfo['DispVWC'] == 1)
				$retVal .= "<th>Vol. SWC<br/>(%)</th>";
			else
				$retVal .= "<th>Avail. Water<br/>(%)</th>";
      if($irrigationScheduler->row_FieldInfo['applicationrate'] == 1)
				$retVal .= "<th>Water Deficit<br/>(in)</th>";
			else
				$retVal .= "<th>Water Deficit<br/>(hrs)</th>";
			$retVal .= "<th><br/>Edit<br/>Data</th>";
			$retVal .= "</tr>";
			$retVal .= "</thead>";
			$retVal .= "<tbody>";           
			return $retVal;
		}
		
		public function makeTableRow($cnt)
		{
			global $irrigationScheduler;
			$retVal = "";
			$fillColor = " class='dangerCol' ";
			if(isset($irrigationScheduler->row_tblIndividField[$cnt]['calculatedSoilWaterAvailibility']))
			{
				if($irrigationScheduler->row_tblIndividField[$cnt]['calculatedSoilWaterAvailibility'] >= (100 - 0.85 *($irrigationScheduler->row_FieldInfo['mad'])  ))
				{
					$fillColor = " class='healthyCol' ";
				}
				elseif($irrigationScheduler->row_tblIndividField[$cnt]['calculatedSoilWaterAvailibility'] >= (100 - $irrigationScheduler->row_FieldInfo['mad'] ))
				{
					$fillColor = " class='warningCol' ";
				}
			}
			$currentStyle = " class='currentRow' ";
			
			$leftColor = $fillColor;
			$middleColor = $fillColor;
			$rightColor = $fillColor;
			if(isset($irrigationScheduler->row_tblIndividField[$cnt]['foragecutting']) && $irrigationScheduler->row_tblIndividField[$cnt]['foragecutting'] == 1)
			{
				$leftColor = $fillColor . " style='border-top:2px dotted black; border-bottom:2px dotted black; border-left:2px dotted black; ' ";
				$middleColor = $fillColor . " style='border-top:2px dotted black; border-bottom:2px dotted black; ' ";
				$rightColor = $fillColor . " style='width:25%;border-top:2px dotted black; border-bottom:2px dotted black; border-right:2px dotted black;' ";
			}
			else
			{
				$rightColor = $fillColor . " style='width:25%;' ";
			}
			$retVal .= "<tr id='row$cnt' $currentStyle>";
			
			$fieldYear = $irrigationScheduler->row_FieldInfo['year'];
			$strDisplayDate = date("m/d",strtotime(dayofyear2date($cnt,'M d',$fieldYear).", ".$fieldYear));
			if($irrigationScheduler->session->isMobileDevice <= 0)
			{
				$strDisplayDate .= "/".$fieldYear;					
			}
			if(isset($irrigationScheduler->row_tblIndividField[$cnt]['etc']))
				$strWaterUse = round($irrigationScheduler->row_tblIndividField[$cnt]['etc'],2);
			elseif($irrigationScheduler->row_tblIndividField[$cnt-1]['etc'])
				$strWaterUse = round($irrigationScheduler->row_tblIndividField[$cnt-1]['etc'],2);
			else
				$strWaterUse = 0.00;
			if(isset($irrigationScheduler->row_tblIndividField[$cnt]))
				$strRainIrrig = $irrigationScheduler->row_tblIndividField[$cnt]['rain'] + $irrigationScheduler->row_tblIndividField[$cnt]['irrig'];
			else
				$strRainIrrig = 0.00;
	                      
			if(isset($irrigationScheduler->row_tblIndividField[$cnt]))
			{
				if($irrigationScheduler->row_FieldInfo['DispVWC'] == 1)
		    	$strAvailWater = round($irrigationScheduler->row_tblIndividField[$cnt]['currentSoilProfileWaterStorage']/$irrigationScheduler->row_tblIndividField[$cnt]['rootDepth']*100,1);
		    else
		      $strAvailWater = round($irrigationScheduler->row_tblIndividField[$cnt]['calculatedSoilWaterAvailibility'],1);
	
				if($irrigationScheduler->row_FieldInfo['applicationrate'] == 1)
					$strWaterDeficitAmount = round($irrigationScheduler->row_tblIndividField[$cnt]['rootZoneWaterDeficit'],1);
				else
					$strWaterDeficitAmount = round($irrigationScheduler->row_tblIndividField[$cnt]['rootZoneWaterDeficit'] / $irrigationScheduler->row_FieldInfo['applicationrate'],1);
			}elseif(isset($irrigationScheduler->row_tblIndividField[$cnt-1]))
			{
				if($irrigationScheduler->row_FieldInfo['DispVWC'] == 1)
		    	$strAvailWater = round($irrigationScheduler->row_tblIndividField[$cnt-1]['currentSoilProfileWaterStorage']/$irrigationScheduler->row_tblIndividField[$cnt-1]['rootDepth']*100,1);
		    else
		      $strAvailWater = round($irrigationScheduler->row_tblIndividField[$cnt-1]['calculatedSoilWaterAvailibility'],1);
	
				if($irrigationScheduler->row_FieldInfo['applicationrate'] == 1)
					$strWaterDeficitAmount = round($irrigationScheduler->row_tblIndividField[$cnt-1]['rootZoneWaterDeficit'],1);
				else
					$strWaterDeficitAmount = round($irrigationScheduler->row_tblIndividField[$cnt-1]['rootZoneWaterDeficit'] / $irrigationScheduler->row_FieldInfo['applicationrate'],1);
			}

			if(date("Y-m-d",strtotime("yesterday")) === date("Y-m-d",strtotime(dayofyear2date($cnt,'M d',$irrigationScheduler->row_FieldInfo['year']))) && (date("Y") == $irrigationScheduler->row_FieldInfo['year']) )
				$strWaterDeficitAmount = "<b><font style='color:red'>".$strWaterDeficitAmount."</font></b>";
			
			$retVal .= "<td nowrap=nowrap ".$leftColor."><a style='text-decoration:underline;' href='javascript:void(0)' onclick='toggleIt(document.getElementById(\"details$cnt\"));'>$strDisplayDate</a></td>";
			$retVal .= "<td ".$middleColor."><center>".$strWaterUse."</center></td>";
			$retVal .= "<td ".$middleColor."><center>".sprintf('%3.2f',$strRainIrrig)."</center></td>";
			$retVal .= "<td ".$middleColor."><center>".$strAvailWater."</center></td>";
			$retVal .= "<td ".$middleColor."><center>".$strWaterDeficitAmount."</center></td>";
	
			$retVal .= "<td ".$rightColor."><center><a  style='text-decoration:underline;'  id='editLink$cnt' href='javascript:void(0);' onclick='toggleIt(document.getElementById(\"editDiv$cnt\")); if(this.innerHTML == \"Edit\") {this.innerHTML = \"Cancel\"; } else { this.innerHTML = \"Edit\";  }";
			$retVal .= "'>Edit</a></center></td>";
			$retVal .= "</tr>".PHP_EOL;
			return $retVal;
		}
		
		public function detailsDiv($cnt,$detailsFontSize,$detailsPadding )
		{
			global $irrigationScheduler;
			$fillColor = " bgcolor=#ffcccc ";
			if(isset($irrigationScheduler->row_tblIndividField[$cnt]['calculatedSoilWaterAvailibility']) && $irrigationScheduler->row_tblIndividField[$cnt]['calculatedSoilWaterAvailibility'] >= (1 - 0.85 *($irrigationScheduler->row_FieldInfo['mad'])  ))
			{
				$fillColor = " bgcolor=#acd473 ";
			}
			elseif(isset($irrigationScheduler->row_tblIndividField[$cnt]['calculatedSoilWaterAvailibility']) && $irrigationScheduler->row_tblIndividField[$cnt]['calculatedSoilWaterAvailibility'] >= (1 - $irrigationScheduler->row_FieldInfo['mad'] ))
			{
				$fillColor = " bgcolor=#fff899 ";
			}
			$detailsDiv = "<tr><td  style='width:100%' align=center colspan=6>";
			$detailsDiv .= "<div id='details$cnt' style='width:100%;overflow:hidden;height:100%;background-color:#b6bcbf;display:none' >";
				
			$detailsDiv .= "<div style='clear:both;float:left;text-align:left;background-color:#b6bcbf;font-size:$detailsFontSize;'>";
			$detailsDiv .= "Day of Year:&nbsp;<br/>";
			$detailsDiv .= "Irrigation:&nbsp;<br/>";
			$detailsDiv .= "Precipitation:&nbsp;<br/>";
			$detailsDiv .= "Reference ET:&nbsp;<br/>";
			$detailsDiv .= "Crop Coefficient:&nbsp;<br/>";
			$detailsDiv .= "Crop ET:&nbsp;<br/>";
			$detailsDiv .= "Root Depth:&nbsp;<br/>";
			$detailsDiv .= "RZ Water Deficit:&nbsp;";
			$detailsDiv .= "</div>";

			$detailsDiv .= "<div style='float:left;  display:table-cell; vertical-align:bottom; text-align:right;background-color:#b6bcbf;font-size:$detailsFontSize; $detailsPadding  '>";
			$detailsDiv .= "$cnt<br/>";
			$detailsDiv .= round($irrigationScheduler->row_tblIndividField[$cnt]['irrig'],2)." in.<br/>";
			$detailsDiv .= round($irrigationScheduler->row_tblIndividField[$cnt]['rain'],2)." in.<br/>";
			$detailsDiv .= round($irrigationScheduler->row_tblIndividField[$cnt]['etr'],2)." in.<br/>";
			$detailsDiv .= round($irrigationScheduler->row_tblIndividField[$cnt]['kc'],2)."<br/>";
			$detailsDiv .= round($irrigationScheduler->row_tblIndividField[$cnt]['etc'],2)." in.<br/>";
			$detailsDiv .= round($irrigationScheduler->row_tblIndividField[$cnt]['rootDepth'],2)." in.<br/>";
			$detailsDiv .= round($irrigationScheduler->row_tblIndividField[$cnt]['rootZoneWaterDeficit'],2)." in.";
			$detailsDiv .= "</div>";
			
			$detailsDiv .= "<div style='float:right;  display:table-cell; vertical-align:bottom; text-align:right;background-color:#b6bcbf;font-size:$detailsFontSize; $detailsPadding  '>";
			$detailsDiv .= round($irrigationScheduler->row_tblIndividField[$cnt]['measdPcntAvail'],2)."%<br/>";
			$detailsDiv .= round($irrigationScheduler->row_tblIndividField[$cnt]['calculatedSoilWaterAvailibility'],0)."%<br/>";
			$detailsDiv .= round($irrigationScheduler->row_tblIndividField[$cnt]['waterStorageAtFieldCapacity'],2)." in.<br/>";
			$detailsDiv .= round($irrigationScheduler->row_tblIndividField[$cnt]['waterStorageAtPermanentWiltingPoint'],2)." in.<br/>";
			$detailsDiv .= round($irrigationScheduler->row_tblIndividField[$cnt]['wpWater'],2)." in.<br/>";
			$detailsDiv .= round($irrigationScheduler->row_tblIndividField[$cnt]['waterStorageAtMad'],2)." in.<br/>";
			$detailsDiv .= round($irrigationScheduler->row_tblIndividField[$cnt]['currentSoilProfileWaterStorage'],2)." in.<br/>";
			$detailsDiv .= round($irrigationScheduler->row_tblIndividField[$cnt]['currentSoilProfileWaterStorage']/$irrigationScheduler->row_tblIndividField[$cnt]['rootDepth']*100,1)." %";
			$detailsDiv .= "</div>";

			$detailsDiv .= "<div style='float:right;text-align:left;background-color:#b6bcbf;font-size:$detailsFontSize;'>";
			$detailsDiv .= "Measured Available Water:&nbsp;<br/>";
			$detailsDiv .= "Modeled Available Water:&nbsp;<br/>";
			$detailsDiv .= "Field Capacity:&nbsp;<br/>";
			$detailsDiv .= "Wilting Point:&nbsp;<br/>";
			$detailsDiv .= "Avail. Water Capacity:&nbsp;<br/>";
			$detailsDiv .= "Water Storage At MAD:&nbsp;<br/>";
			$detailsDiv .= "Current Water Storage:&nbsp;<br/>";
			$detailsDiv .= "Volumetric Water Content:&nbsp;";
			$detailsDiv .= "</div>";
			$detailsDiv .= "</div>";
			$detailsDiv .= "</td></tr>";

			return $detailsDiv;
		}
		public function editDiv($cnt)
		{
			global $irrigationScheduler;

			$irrigation = $irrigationScheduler->row_tblIndividField[$cnt]['irrig'];
			if($irrigationScheduler->row_tblIndividField[$cnt]['modified'] == 1)
			{
				$mswaChange = 1;
				$isChecked = "checked=CHECKED";
				$thisStyle = ' display:block; ';
			}
			else
			{
				$mswaChange = 0;
				$isChecked = "";
				$thisStyle = ' display:none; ';
			}

			$editDiv = "<tr><td  style='width:100%' colspan=6>";
			$editDiv .= "<div id='editDiv$cnt' style='width:100%;clear:both;display:none;'>";
			$editDiv .= "<form action=\"".$irrigationScheduler->session->processPath."\" method=\"POST\" onsubmit='toggleIt(document.getElementById(\"editDiv$cnt\")); var tLink = document.getElementById(\"editLink$cnt\"); if(tLink.innerHTML == \"Cancel\") {tLink.innerHTML=\"Wait\"; ajaxSaveBudgetEdit(document.getElementById(\"editDailyBudgetDayForm$cnt\")); }else{tLink.innerHTML=\"Cancel\";} return false;'  name=\"editDailyBudgetDayForm$cnt\" id=\"editDailyBudgetDayForm$cnt\">";
			$editDiv .= "<div style=\"clear:both; display:none\"><div style=\"float:left\">Measured Rainfall:</div><div style=\"width:40%;float:right\"><input  size=5  onchange=\"document.editDailyBudgetDayForm$cnt.rainchanged.value=1;\"  name=\"rain\" type=\"text\" value=\"0\" maxlength=\"10\"/> in</div></div>";
			$editDiv .= "<div style='clear:both;width:50%;float:left;text-align:right'>Irrigation:&nbsp;</div>";
			$editDiv .= "<div style='width:50%;float:right;text-align:left'>";
			
			//If the application rate is == 1 inch per hour then 
			if($irrigationScheduler->row_FieldInfo['applicationrate'] == 1)
			{
				$editDiv .= "<input  size=5 onchange=\"document.editDailyBudgetDayForm$cnt.irrigationchanged.value=1;\" name=\"irrigation\" type=\"text\" value=\"". round($irrigation,3) ."\" maxlength=\"10\"/> in ";
			}
			else //otherwise calculate it
			{
				$irrigation = $irrigation / $irrigationScheduler->row_FieldInfo['applicationrate'];
				$editDiv .= "<input  size=5 onchange=\" document.editDailyBudgetDayForm$cnt.irrigationchanged.value=1;\" name=\"irrigation\" type=\"text\" value=\"". round($irrigation,1) ."\" maxlength=\"10\"/> hours";
			}
			$editDiv .= "</div>";

			//If user wants to use volumetric water content, then calculate
      if($irrigationScheduler->row_FieldInfo['DispVWC'] == 1) {
          $mswa = $irrigationScheduler->row_tblIndividField[$cnt]['currentSoilProfileWaterStorage']/$irrigationScheduler->row_tblIndividField[$cnt]['rootDepth']*100;
          $waterLabel = "Reset/Correct Volumetric Water Content";
					$wpWater = $irrigationScheduler->row_tblIndividField[$cnt]['wpWater'];
					$fcWater = $irrigationScheduler->row_tblIndividField[$cnt]['fcWater'];
					$rzDepth = $irrigationScheduler->row_tblIndividField[$cnt]['rootDepth'];
         	$validateScript = " var soilPercent = (this.value/100 - $wpWater/$rzDepth) / ($fcWater/$rzDepth - $wpWater/$rzDepth) * 100; ";
					$validateScript .= " if(soilPercent > 100 ) {alert('The entered value is greater than field capacity which is ".round($fcWater/$rzDepth*100,1)."%. Volumetric water content will be set to field capacity.'); } ";      
					$validateScript .= " if(soilPercent < 0 ) { alert('The entered value is less than the wilting point which is ".round($wpWater/$rzDepth*100,1)."%. Volumetric water content will be set to wilting point.'); } ";      
      }
      else { //otherwise just set it
          $mswa = $irrigationScheduler->row_tblIndividField[$cnt]['calculatedSoilWaterAvailibility'];
          $waterLabel = "Reset/Correct % Available Water Content";
          $validateScript = " var soilPercent = this.value; ";
					$validateScript .= " if(soilPercent > 100 ) { alert('The entered value is above field capacity which is 100%. It will be reset to field capacity.'); } ";      
					$validateScript .= " if(soilPercent < 0 ) { alert('The entered value is less than the wilting point which is 0%. It will be reset to wilting point.'); } ";      
      }
			$editDiv .= "<div style=\"clear:both;float:left\"><input type=checkbox $isChecked name='usemeasured' onclick='toggleIt(document.getElementById(\"soilAvailabilityDiv$cnt\")); if(this.checked) { this.form.mswachanged.value=1; } else { this.form.mswachanged.value=0; } '/>$waterLabel</div>";
			$editDiv .= "<div id='soilAvailabilityDiv$cnt' style=\"$thisStyle clear:both\"><div style=\"width:50%;float:left;text-align:right;\">Set To:</div>";
			$editDiv .= "<div style=\"width:50%;float:right;text-align:left;\"><input  size=5 onchange=\"$validateScript document.editDailyBudgetDayForm$cnt.mswachanged.value=1;\" name=\"mswa\" type=\"text\" value=\"". round($mswa,1) ."\" maxlength=\"10\"/> %</div></div>";

			if(isset($irrigationScheduler->row_tblIndividField[$cnt]['foragecutting']) && $irrigationScheduler->row_tblIndividField[$cnt]['foragecutting'] == 1)
				$isChecked = "checked";
			else
				$isChecked = "";
			if($irrigationScheduler->row_CropInfo['postCuttingFlatDays'] > 0 && $irrigationScheduler->row_CropInfo['postCuttingRecoveryDays'] > 0)
			{
				$thisStyle = " style='display:block;clear:both;float:left' ";
			}
			else
			{
				$thisStyle = " style='display:none;clear:both;float:left' ";
			}
	
			$editDiv .= "<div $thisStyle><input type=checkbox $isChecked name='foragecut' />Apply Forage Cutting Today</div>";
			$editDiv .= "<div style=\"clear:both;float:left;width:100%;\"><center><a style='text-decoration:underline;' href='javascript:void(0);' onclick='toggleIt(document.getElementById(\"editDiv$cnt\")); var tLink = document.getElementById(\"editLink$cnt\"); if(tLink.innerHTML == \"Cancel\") {tLink.innerHTML=\"Wait\"; ajaxSaveBudgetEdit(document.getElementById(\"editDailyBudgetDayForm$cnt\")); }else{tLink.innerHTML=\"Cancel\";}'>Save</a></center></div>";
	
			$editDiv .= "<input type=\"hidden\" name=\"savebudgetedit\" value=\"1\" />";
			$editDiv .= "<input type=\"hidden\" name=\"doy\" value=\"". $cnt ."\" />";
			$editDiv .= "<input type=\"hidden\" name=\"refETchanged\" value=\"0\">";		
			$editDiv .= "<input type=\"hidden\" name=\"rainchanged\" value=\"0\">";
			$editDiv .= "<input type=\"hidden\" name=\"irrigationchanged\" value=\"0\">";
			$editDiv .= "<input type=\"hidden\" name=\"mswachanged\" value=\"$mswaChange\">";
			
			$BUDGETSTARTROW = $_SESSION['BSR']; 
			$editDiv .= "<input type=hidden name='BUDGETSTARTROW' value='$BUDGETSTARTROW'>";
	
			$editDiv .= "</form>";
			$editDiv .= "</div>";	
			$editDiv .= "</td></tr>";
			return $editDiv;
		}
		public function makeCSVDay(&$csv, $cnt)
		{
			global $irrigationScheduler;
			$csv .= $cnt.", ";
			$csv .= dayofyear2date($cnt,"m-d-Y",$irrigationScheduler->row_tblIndividField[$cnt]['fieldYear']).", ";
			$csv .= $irrigationScheduler->row_tblIndividField[$cnt]['etr'].", ";
			$csv .= $irrigationScheduler->row_tblIndividField[$cnt]['kc'].", ";
			$csv .= $irrigationScheduler->row_tblIndividField[$cnt]['etc'].", ";
			$csv .= $irrigationScheduler->row_tblIndividField[$cnt]['rain'].", ";
			$csv .= $irrigationScheduler->row_tblIndividField[$cnt]['irrig'].", ";
			$csv .= $irrigationScheduler->row_tblIndividField[$cnt]['rootDepth'].", ";
			$csv .= $irrigationScheduler->row_tblIndividField[$cnt]['waterStorageAtFieldCapacity'].", ";
			$csv .= $irrigationScheduler->row_tblIndividField[$cnt]['waterStorageAtPermanentWiltingPoint'].", ";
	    $csv .= $irrigationScheduler->row_tblIndividField[$cnt]['wpWater'].", ";
			$csv .= $irrigationScheduler->row_tblIndividField[$cnt]['waterStorageAtMad'].", ";
			$csv .= $irrigationScheduler->row_tblIndividField[$cnt]['currentSoilProfileWaterStorage'].", ";
			$csv .= $irrigationScheduler->row_tblIndividField[$cnt]['calculatedSoilWaterAvailibility'].", ";
			$csv .= $irrigationScheduler->row_tblIndividField[$cnt]['rootZoneWaterDeficit'].", ";
			$csv .= $irrigationScheduler->row_tblIndividField[$cnt]['deepPercolation'].", ";
			$csv .= round($irrigationScheduler->row_tblIndividField[$cnt]['currentSoilProfileWaterStorage']/$irrigationScheduler->row_tblIndividField[$cnt]['rootDepth']*100,1).", ";
			$csv .= $irrigationScheduler->row_tblIndividField[$cnt]['Ks'].", ";
			$csv .= $irrigationScheduler->row_tblIndividField[$cnt]['measdPcntAvail'].", ";
			if($irrigationScheduler->row_tblIndividField[$cnt]['modified'] == 1)
				$csv .= "Yes, ";
			else
				$csv .= "No, ";
				
			if($irrigationScheduler->row_tblIndividField[$cnt]['foragecutting'] == 1)
				$csv .= "Yes, ";
			else
				$csv .= "No, ";
								
			$csv .= "\n";
		}
		public function intializeDatePicker()
		{
			 	$retVal = "<script type='text/javascript'>  ".PHP_EOL;
				$retVal .= "Date.prototype.getDOY = function() { ".PHP_EOL;
				$retVal .= "var onejan = new Date(this.getFullYear(),0,1); ".PHP_EOL;
				$retVal .= "return Math.ceil((this - onejan) / 86400000); ".PHP_EOL;
				$retVal .= "} ".PHP_EOL;
	 			$retVal .= "$(document).ready(function(){   ".PHP_EOL;
				$retVal .= "$('#jumpdate').datepicker({  ".PHP_EOL;
				$retVal .= "yearRange: '".(date("Y")-4).":".(date("Y")+1)."', ".PHP_EOL;
				$retVal .= "dateFormat: 'M dd, yy', ".PHP_EOL;
				$retVal .= "changeMonth: true, ".PHP_EOL;
				$retVal .= "changeYear: true, ".PHP_EOL;
				$retVal .= "onClose: function(input, inst) {".PHP_EOL;
				$retVal .= "document.BTable.BUDGETSTARTROW.value=$('#jumpdate').datepicker('getDate').getDOY() + 7 ;".PHP_EOL;
				$retVal .= "document.BTable.submit();  ".PHP_EOL;
				$retVal .= "return false; ".PHP_EOL;
				$retVal .= "}".PHP_EOL;
				$retVal .= "}); ".PHP_EOL;
				$retVal .= "}); ".PHP_EOL;
				$retVal .= "</script>";								
				return $retVal;
		}
		public function BudgetTableJavaScripts()
		{
	
			global $irrigationScheduler;
			$BudgetTableJavaScripts = "<script type='text/javascript' language=\"javascript\"> ";
			$BudgetTableJavaScripts .= "function toggleIt(ele) {";
			$BudgetTableJavaScripts .= "if(ele.style.display == \"block\") {";
			$BudgetTableJavaScripts .= "ele.style.display = \"none\";";
			$BudgetTableJavaScripts .= "}";
			$BudgetTableJavaScripts .= "else {";
			$BudgetTableJavaScripts .= "ele.style.display = \"block\";";
			$BudgetTableJavaScripts .= "}";
			$BudgetTableJavaScripts .= "} ";
			$BudgetTableJavaScripts .= "function ajaxSaveBudgetEdit(thisForm)".PHP_EOL;
			$BudgetTableJavaScripts .= "{  ";
			$BudgetTableJavaScripts .= "$.ajax({ ".PHP_EOL;
			$BudgetTableJavaScripts .= "data: $('#' + thisForm.id).serialize() ";
			$BudgetTableJavaScripts .= ", ";
			$BudgetTableJavaScripts .= "type: 'POST',";
			$BudgetTableJavaScripts .= "url: '".$irrigationScheduler->session->processPath."?whichday=123',";
			
			$BudgetTableJavaScripts .= "dataType: 'text' ";
			if($irrigationScheduler->session->isMobileDevice > 0)
			{
				$BudgetTableJavaScripts .= ", success: function MySuccess(data, textStatus, jqXHR) { parent.location.href = '".$irrigationScheduler->session->basepath."index.php?m=1&action=daily-budget-existing-fields&BUDGETSTARTROW=' + thisForm['BUDGETSTARTROW'].value; } ";
				$BudgetTableJavaScripts .= ", error: function MyFailure(data, textStatus, jqXHR) {  parent.location.href = '".$irrigationScheduler->session->basepath."index.php?m=1&action=daily-budget-existing-fields&BUDGETSTARTROW=' + thisForm['BUDGETSTARTROW'].value; } ";
			}
			else
			{
				$BudgetTableJavaScripts .= ", success: function MySuccess(data, textStatus, jqXHR) { ";
				$BudgetTableJavaScripts .= " parent.location.href = '/awn.php?page=irrigation-scheduler&BUDGETSTARTROW=' + thisForm['BUDGETSTARTROW'].value; ";
				$BudgetTableJavaScripts .= " } ";
				$BudgetTableJavaScripts .= ", error: function MyFailure(data, textStatus, jqXHR) { parent.location.href = '/awn.php?page=irrigation-scheduler&BUDGETSTARTROW=' + thisForm['BUDGETSTARTROW'].value; } ";
			}
			$BudgetTableJavaScripts .= "})";
			$BudgetTableJavaScripts .= "}";
			$BudgetTableJavaScripts .= "</script>";
	
			return $BudgetTableJavaScripts;
		}
	}

?>