<?php
	class fieldSettings
	{
		public function toHTML()
		{
			global $irrigationScheduler;
			global $iform, $validation, $database;


			$query_UpdateField = sprintf("SELECT * FROM irrigation.tblfield WHERE fieldID = %s", GetSQLValueString($irrigationScheduler->htmlFactory->FieldID, "int"));
			$UpdateField = $database->query($query_UpdateField);
			$row_UpdateField = mysql_fetch_assoc($UpdateField);
			$totalRows_UpdateField = mysql_num_rows($UpdateField);
			
			if($totalRows_UpdateField == 1)
			{
				$EmergenceDate = strtotime(dayofyear2date($row_UpdateField['plantDate'],'M d',$row_UpdateField['year']).", ".$row_UpdateField['year']);
				$EmergenceMonth = date("m",$EmergenceDate);
				$EmergenceDay =  date("d",$EmergenceDate);
			
				$WaterBudgetDate = strtotime(dayofyear2date($row_UpdateField['growth10PcntDate'],'M d',$row_UpdateField['year']).", ".$row_UpdateField['year']);
				$WaterBudgetMonth = date("m",$WaterBudgetDate);
				$WaterBudgetDay =  date("d",$WaterBudgetDate);
	
				$CanopyCover10Date = strtotime(dayofyear2date($row_UpdateField['growthMaxDate'],'M d',$row_UpdateField['year']).", ".$row_UpdateField['year']);
				$CanopyCover10Month = date("m",$CanopyCover10Date);
				$CanopyCover10Day =  date("d",$CanopyCover10Date);
			
				$CanopyCover70Date = strtotime(dayofyear2date($row_UpdateField['growthDeclineDate'],'M d',$row_UpdateField['year']).", ".$row_UpdateField['year']);
				$CanopyCover70Month = date("m",$CanopyCover70Date);
				$CanopyCover70Day =  date("d",$CanopyCover70Date);
			
				$CropMaturationDate = strtotime(dayofyear2date($row_UpdateField['growthEndDate'],'M d',$row_UpdateField['year']).", ".$row_UpdateField['year']);
				$CropMaturationMonth = date("m",$CropMaturationDate);
				$CropMaturationDay =  date("d",$CropMaturationDate);
				
				//seasonEndDate
				if($row_UpdateField['seasonEndDate'] > 0)
				{
					$EndSeasonDate = strtotime(dayofyear2date($row_UpdateField['seasonEndDate'],'M d',$row_UpdateField['year']).", ".$row_UpdateField['year']);
					$EndSeasonMonth = date("m",$EndSeasonDate);
					$EndSeasonDay =  date("d",$EndSeasonDate);
				}
				else
				{
					$EndSeasonDate = strtotime("+5 days",$CropMaturationDate);
					$EndSeasonMonth = date("m",$EndSeasonDate);
					$EndSeasonDay =  date("d",$EndSeasonDate);
				}
			}
			else
			{
				$EmergenceDate = "";
				$EmergenceMonth	= "";
				$EmergenceDay = "";
			
				$WaterBudgetDate = "";
				$WaterBudgetMonth = "";
				$WaterBudgetDay =  "";
			
				$CanopyCover10Date = "";
				$CanopyCover10Month = "";
				$CanopyCover10Day =  "";
			
				$CanopyCover70Date = "";
				$CanopyCover70Month = "";
				$CanopyCover70Day =  "";
			
				$CropMaturationDate = "";
				$CropMaturationMonth = "";
				$CropMaturationDay =  "";
			
				$EndSeasonDate = "";
				$EndSeasonMonth = "";
				$EndSeasonDay =  "";
			}
	
			$advancedUpdateField = "";
	 		$advancedUpdateField .= $this->AdvancedIrrigationJavaScripts();
			if($irrigationScheduler->session->isMobileDevice > 0)
			{
				$advancedUpdateField .= "<h2><a name=\"top\"></a>Advancaed Field Information</h2>";
		 		$advancedUpdateField .= "<form action=\"".$irrigationScheduler->session->basepath."&amp;action=advanced-update-field\" method=\"post\" name=\"advancedUpdateFieldForm\">";
		
				$advancedUpdateField .= $irrigationScheduler->htmlFactory->selectField(true,"advancedUpdateFieldForm");
				$advancedUpdateField .= "</form>";		
				$advancedUpdateField .= "<div style=\"clear:both;\"><center><a href='".$irrigationScheduler->session->basepath."&amp;help=advanced-update-field'>Help</a></center></div>";
			}
		 	else
			{
				$advancedUpdateField .= "<div style=\"clear:both;\"><center><a id='advancedupdateFieldHelp' class='helplink' href='/is/ajaxPage.php?help=advanced-update-field'>Help</a></center></div>";
				$advancedUpdateField .= "<script type=\"text/javascript\">";
				$advancedUpdateField .= "$(document).ready(function(){";
				$advancedUpdateField .= "$('#advancedupdateFieldHelp').fancybox({ 'width':725, 'autoDimensions': false });".PHP_EOL;
				$advancedUpdateField .= "})";
				$advancedUpdateField .= "</script>";
			}
			$advancedUpdateField .= "<script type=\"text/javascript\">";
			$advancedUpdateField .= "function chkAppRt(cBox){ ";
			$advancedUpdateField .= "if (cBox.checked){ ";
			$advancedUpdateField .= "document.getElementById('AppRate').style.display='block'; ";
			$advancedUpdateField .= "} ";
			$advancedUpdateField .= "else{ ";
			$advancedUpdateField .= "document.getElementById('AppRate').style.display='none'; ";
			$advancedUpdateField .= "document.getElementById('IrrApRt').value ='1'; ";
			$advancedUpdateField .= "} ";
			$advancedUpdateField .= "}		 ";
	
			$advancedUpdateField .= "function helpRt(aLink){ ";
			$advancedUpdateField .= "if (document.getElementById('HelpRate').style.display == 'none'){ ";
			$advancedUpdateField .= "document.getElementById('HelpRate').style.display='block'; ";
	 		$advancedUpdateField .= "document.getElementById('helpmelink').innerHTML='I know my Application Rate'; ";
			$advancedUpdateField .= "} ";
			$advancedUpdateField .= "else{ ";
			$advancedUpdateField .= "document.getElementById('HelpRate').style.display='none'; ";
	 		$advancedUpdateField .= "document.getElementById('helpmelink').innerHTML='Help Calculate My Application Rate'; ";
			$advancedUpdateField .= "} ";
			$advancedUpdateField .= "}		 ";
	
			$advancedUpdateField .= "function selectIrrigationType(aOption) { ";
			$advancedUpdateField .= " document.getElementById('DripRate').style.display='none'; ";
			$advancedUpdateField .= " document.getElementById('SprinklerRate').style.display='none'; ";
			$advancedUpdateField .= " document.getElementById('GeneralRate').style.display='none'; ";
	 		$advancedUpdateField .= " document.getElementById('calcButton').style.display='none'; ";
			$advancedUpdateField .= " switch(aOption.value) { ";
			$advancedUpdateField .= " case '1':  ";
			$advancedUpdateField .= " document.getElementById('calcButton').style.display='block'; ";
			$advancedUpdateField .= " document.getElementById('DripRate').style.display='block'; ";
			$advancedUpdateField .= " break; ";
			$advancedUpdateField .= " case '2':  ";
			$advancedUpdateField .= " document.getElementById('calcButton').style.display='block'; ";
			$advancedUpdateField .= " document.getElementById('SprinklerRate').style.display='block'; ";
			$advancedUpdateField .= " break; ";
			$advancedUpdateField .= " case '3':  ";
			$advancedUpdateField .= " document.getElementById('calcButton').style.display='block'; ";
			$advancedUpdateField .= " document.getElementById('GeneralRate').style.display='block'; ";
			$advancedUpdateField .= " break; ";
			$advancedUpdateField .= " } ";
			$advancedUpdateField .= " } ";
			$advancedUpdateField .= "function calc_dripRate(form){
		emitterFlowUnits=parseFloat(form.emitterFlowUnits.value);
		spacingUnits=parseFloat(form.spacingUnits.value);
		distanceUnits=parseFloat(form.distanceUnits.value);
		DripEff=parseFloat(form.DripEff.value);
		emitterFlow=parseFloat(form.emitterFlow.value)*emitterFlowUnits;
		spacing=parseFloat(form.spacing.value)*spacingUnits;
		distance=parseFloat(form.distance.value)*distanceUnits;
		appRate=spacing/2.54;
		appRate=appRate*(distance/2.54)*DripEff/100;
		appRate=(231*emitterFlow*60)/appRate;
		form.IrrApRt.value=appRate.toFixed(3);
	}";
	
			$advancedUpdateField .= "function validateNotifypercent(ele) { ";
			$advancedUpdateField .= "var MyDiv = document.getElementById('notifymeerror');";
			$advancedUpdateField .= "MyDiv.style.display = 'none'; ";
			$advancedUpdateField .= "if((document.forms[\"advanced-field-info\"].elements[\"notifypercent\"].value < 10) || (document.forms[\"advanced-field-info\"].elements[\"notifypercent\"].value > 100))";
			$advancedUpdateField .= "{";
			$advancedUpdateField .= " MyDiv.innerHTML = \"The percent of wetted soil is normally >= 10% and <= 100%\"; ";
			$advancedUpdateField .= " MyDiv.style.display = 'block'; ";
			$advancedUpdateField .= "}";
			$advancedUpdateField .= "}";
	
			$advancedUpdateField .= "function calc_precipitation(form){
		diameterUnits=parseFloat(form.diameterUnits.value);
		pressureUnits=parseFloat(form.pressureUnits.value);
		headSpacingUnits=parseFloat(form.headSpacingUnits.value);
		lineSpacingUnits=parseFloat(form.lineSpacingUnits.value);
		nozzleFlowRateUnits=parseFloat(form.nozzleFlowRateUnits.value);
		diameter=parseFloat(form.diameter.value)*diameterUnits;
		pressure=parseFloat(form.pressure.value)/pressureUnits;
		headSpacing=parseFloat(form.headSpacing.value)/headSpacingUnits;
		lineSpacing=parseFloat(form.lineSpacing.value)/lineSpacingUnits;
		efficiency=parseFloat(form.efficiency.value);
		nozzleFlowRate=diameter/2.54;
		nozzleFlowRate=28.925*Math.sqrt(pressure)*Math.pow(nozzleFlowRate,2);
		nozzleFlowRate=nozzleFlowRate/nozzleFlowRateUnits;
		switch(nozzleFlowRateUnits){
			case 1:precisionF=2;
			break;
			case.016666666666667:precisionF=1;
			break;
			case 448.8311688:precisionF=4;
			break;
			case 18.85714:precisionF=3;
			break;
			case 452.5714:precisionF=4;
			break;
			case 226.2857:precisionF=4;
			break;
			case 15.85032:precisionF=3;
			break;
			case.264172051:precisionF=2;
			break;
			case.004402868:precisionF=0;
			break;
			default:precisionF=2;
		}
		form.nozzleFlowRate.value=nozzleFlowRate.toFixed(precisionF);
		nozzleAppRate=diameter/2.54;nozzleAppRate=Math.pow(nozzleAppRate,2);
		nozzleAppRate=28.925*Math.sqrt(pressure)*nozzleAppRate;
		nozzleAppRate=nozzleAppRate/((lineSpacing*headSpacing)/efficiency*100);
		nozzleAppRate=parseFloat(96.25025*nozzleAppRate);
		form.IrrApRt.value=nozzleAppRate.toFixed(3);
	}";
	
			$advancedUpdateField .= "function calc_applicationRate(form){
		flowRateUnits=parseFloat(form.flowRateUnits.value);
		areaUnits=parseFloat(form.areaUnits.value);
		efficiency=parseFloat(form.Efficiency.value);
		flowRate=parseFloat(form.flowRate.value)*flowRateUnits;
		area=parseFloat(form.area.value)/areaUnits;
		applicationRate=flowRate/area*efficiency/100;
		applicationRate=applicationRate/452.57;
		form.IrrApRt.value=applicationRate.toFixed(3);
	}";
	
			$advancedUpdateField .= " function validatephone() { } function changeServiceProvider() { } function changeAlertMethod(thisValue, thisText)
			{
				if(thisValue == 0)
				{
					var showDiv = document.getElementById('emailDiv');
					var hideDiv = document.getElementById('txtDiv');
				}
				else
				{
					var showDiv = document.getElementById('txtDiv');
					var hideDiv = document.getElementById('emailDiv');
				}
				showDiv.style.display = 'block';
				hideDiv.style.display = 'none';
			}".PHP_EOL;	
			$advancedUpdateField .= "</script>";
	
			$advancedUpdateField .= "<form action=\"".$irrigationScheduler->session->processPath."\" method=\"POST\" name=\"advanced-field-info\" id=\"advanced-field-info\">";
			$advancedUpdateField .= "<input type=hidden name='advanced-field-update' value='1'>";
	
			$isChecked = "";
			if(isset($row_UpdateField['useNDFDforecast']) && $row_UpdateField['useNDFDforecast'] == 1)
			{
				$isChecked = " checked=checked ";
			}
	
			$advancedUpdateField .= "<div  style=\"clear:both; border-width: 1px; border-top-style: solid; border-color: #e7e9ea;\"><center><input name=\"update-field\" type=\"submit\" value=\"Update Field\" /></center></div>";

	 		$advancedUpdateField .= "<div style='clear:both;border-width: 1px; border-top-style: solid; border-color: #e7e9ea;'>";
	 		$advancedUpdateField .= "<div style='width:300px;margin-left: auto; margin-right:auto; text-align:left;'><label><input type='checkbox' name='chkUseNDFD' $isChecked/> Show Forecast Values</label></div>";
	 		$advancedUpdateField .= "</div>";
	
      //If Surface Irrigation is checked then hide "Use Hours Instead of Inches" Checkbox. Default to inches.
	                
			$isChecked = "";
			$thisStyle = " display:none;";
			if(isset($row_UpdateField['notifygrower']) && $row_UpdateField['notifygrower'] > 0)
			{
				$isChecked = " checked=checked ";
				$thisStyle = " display:block;";
			}
	 		$advancedUpdateField .= "<div style='clear:both;border-width: 1px; border-top-style: solid; border-color: #e7e9ea;'>";
	 		$advancedUpdateField .= "<div style='width:300px;margin-left: auto; margin-right:auto; text-align:left;'><label><input type='checkbox' name='chkNotifyMe' $isChecked onchange='var tDiv = document.getElementById(\"notifybigdiv\"); if(this.checked){ tDiv.style.display=\"block\"; } else {tDiv.style.display=\"none\"; }'/> Send Me Notifications By </label>";
			
			$options = "";
			$deliveryOptions = array("0" => "Email", "1" => "Texts");
			$advancedUpdateField .= "<select style='' id='alertmethod' name='alertmethod'  onchange='changeAlertMethod(this.value, this.text);'>";
			foreach($deliveryOptions as $value => $text)
			{
				$selected = "";
				if($value == $row_UpdateField['notificationtype'])
					$selected = " selected=SELECTED ";
				$options .= "<option $selected value='$value'>$text</option>";
			}
			$advancedUpdateField .= $options;
			$advancedUpdateField .= "</select>";
	 		$advancedUpdateField .= "</div>";
	
			$isChecked = "";
			
			$notifyAmount = 100 - $row_UpdateField['mad'];
			$notifyAddress = $database->getGrowerEmail($irrigationScheduler->session->username);
			$notifyPhone = $row_UpdateField['notifynumber'];
			$txtProvider = $row_UpdateField['txtprovider'];
			if(isset($row_UpdateField['notifygrower']) && $row_UpdateField['notifygrower'] > 0)
			{
				$notifyAddress = $row_UpdateField['notifyaddress'];
				$thisStyle = 'display:block';
				if($row_UpdateField['notificationtype'] == 2)
					$isChecked = " checked=checked ";
				$notifyAmount = $row_UpdateField['notifypercent'];
			}
	 		$advancedUpdateField .= "<div id='notifybigdiv' style='clear:both;background-color:#e2f4f8;$thisStyle'>";
			if($row_UpdateField['notificationtype'] == 0)
			{
				$emailDivStyle = "display:block;";
				$txtDivStyle = "display:none;";
			}
			else
			{
				$emailDivStyle = "display:none;";
				$txtDivStyle = "display:block;";
			}
			$advancedUpdateField .= "<div id='emailDiv' style='$emailDivStyle'>";		
			$advancedUpdateField .= "<div style='clear:both; width:52%; float:left;text-align:right;'><label>Email Address:</label></div>";
			$advancedUpdateField .= "<div style=' width:47%; float:right;text-align:left;'><input onchange='validatenotifyaddress(this);' style='text-align:center' name=\"notifyaddress\" type=\"text\"  size=18 maxlength=\"255\" value=\"".$notifyAddress."\" /></div>";
	 		$advancedUpdateField .= "<div name='notifyaddresserror' id='notifyaddresserror' style='display:none;color:red;clear:both;'></div>";
	 		$advancedUpdateField .= "</div>";
	
			$advancedUpdateField .= "<div id='txtDiv' style='$txtDivStyle'>";		
			$advancedUpdateField .= "<div style='clear:both; width:52%; float:left;text-align:right;'><label>Phone Number:</label></div>";
			$advancedUpdateField .= "<div style=' width:47%; float:right;text-align:left;'><input onchange='validatephone(this);' style='text-align:center' name=\"notifyphone\" type=\"text\"  size=18 maxlength=\"255\" value=\"".$notifyPhone."\" /></div>";
			$advancedUpdateField .= "<div style='clear:both; width:52%; float:left;text-align:right;'><label>Service Provider:</label></div>";
			$advancedUpdateField .= "<div style=' width:47%; float:right;text-align:left;'>";
			$providerQuery = "SELECT objid, name from irrigation.table_cell_provider where status = 1 order by name";
			$providerResult = $database->query($providerQuery);
			$providerOptions = array("-1" => "Select Provider");
			while($providerRow = mysql_fetch_assoc($providerResult))
			{
				$providerOptions[$providerRow['objid']] = $providerRow['name'];
			}
			$options = "";
			$advancedUpdateField .= "<select style='' id='serviceprovider' name='serviceprovider'  onchange='changeServiceProvider(this.value, this.text);'>";
			foreach($providerOptions as $value => $text)
			{
				$selected = "";
				if($value == $row_UpdateField['txtprovider'] && !is_null($row_UpdateField['txtprovider']))
				{
					$selected = " selected=SELECTED ";
				}
				$options .= "<option $selected value='$value'>$text</option>";
			}
			$advancedUpdateField .= $options;
			$advancedUpdateField .= "</select>";
			
			$advancedUpdateField .= "</div>";
	 		$advancedUpdateField .= "<div name='notifyaddresserror' id='notifyaddresserror' style='display:none;color:red;clear:both;'></div>";
	 		$advancedUpdateField .= "</div>";
	//
			$advancedUpdateField .= "<div style='clear:both; width:52%; float:left;text-align:right;'><label>Each day at:</label></div>";
			$advancedUpdateField .= "<div style=' width:47%; float:right;text-align:left;'>";
			for($hour = 0; $hour <= 23; $hour++)
			{
				$hours[$hour] = str_pad($hour, 2, "0", STR_PAD_LEFT).":10";  
			}
			$notifyHour = 8;
			if(isset($row_UpdateField['notifyhour']))
			{
				$notifyHour = $row_UpdateField['notifyhour'];
			}
			$options = "";
			foreach($hours as $value => $display)
			{
				$selected = "";
				if($value == $notifyHour)
					$selected = " selected=SELECTED ";
				$options .= "<option $selected value='$value'>$display</option>";
			}
			$advancedUpdateField .= "<select name='notifyhour'>";
			$advancedUpdateField .= $options;
			$advancedUpdateField .= "</select>";
			$advancedUpdateField .= "</div>";
	
	//
			$isChecked = "";
			$thisStyle = "display:none;";
	 		if($notifyAmount < 100)
	 		{
	 			$isChecked = " checked=CHECKED ";
				$thisStyle = "display:block;";
	 		}
	 		$advancedUpdateField .= "<div style='clear:both;width:300px;margin-left: auto; margin-right:auto; text-align:left;'><label><input onchange='' type=checkbox name='notifymecheck' $isChecked id='notifymecheck'>&nbsp;Only If % Avail. Water Is &lt; </label><input onchange='validateNotifypercent(this);' name=\"notifypercent\" id=\"notifypercent\" type=\"text\"  size=5 maxlength=\"10\" value=\"".($notifyAmount)."\" />%</div>";
	 		$advancedUpdateField .= "<div name='notifymeerror' id='notifymeerror' style='display:none;color:red;clear:both;'></div>";
	
	 		$advancedUpdateField .= "</div>";
	 		$advancedUpdateField .= "</div>";
	
			$irrApRate = 1;
			$thisStyle = 'display:none';
			
      $isChecked = "";            
			if(isset($row_UpdateField['DispVWC']) && $row_UpdateField['DispVWC'] == 1)
			{
				$isChecked = " checked=checked ";
			}
	 		$advancedUpdateField .= "<div style='clear:both;border-width: 1px; border-top-style: solid; border-color: #e7e9ea;'>";
	 		$advancedUpdateField .= "<div style='width:300px;margin-left: auto; margin-right:auto; text-align:left;'><label><input type='checkbox' name='chkDispVWC' $isChecked/> Use Volumetric Soil Water Content</label></div>";
	 		$advancedUpdateField .= "</div>";
	                
			$thisStyle = 'display:none';
			$isChecked = "";
			if(isset($row_UpdateField['applicationrate']) && $row_UpdateField['applicationrate'] <> 1)
			{
				$irrApRate = $row_UpdateField['applicationrate'];
				$thisStyle = 'display:block';
				$isChecked = " checked=checked ";
			}
	 		$advancedUpdateField .= "<div style='clear:both;border-width: 1px; border-top-style: solid; border-color: #e7e9ea;'>";
	 		$advancedUpdateField .= "<div style='width:300px;margin-left: auto; margin-right:auto; text-align:left;'><label><input type='checkbox' name='chkInIrr' $isChecked onclick='chkAppRt(this)'/> Use Hrs Irrigation Instead of Inches</label></div>";
	 		$advancedUpdateField .= "<div name='icheckerror' id='icheckerror' style='display:none;color:red;clear:both;'></div>";
	 		$advancedUpdateField .= "</div>";
	
      $advancedUpdateField .= "<div style='clear:both;border-width: 1px; border-top-style: solid; border-color: #e7e9ea;'>";
	 		$advancedUpdateField .= "<div id='AppRate' style='".$thisStyle."'>";
	 		$advancedUpdateField .= "<div style='width:52%; text-align:right; float:left;'><label>Irrig. Application Rate:</label></div>";
	 		$advancedUpdateField .= "<div style='width:47%; float:right;'><input type='text' name='IrrApRt' size=5 maxlength =10 id='IrrApRt' value='".$irrApRate."'> in/hr</div>";
	 		$advancedUpdateField .= "<div style='width:99%; float:left;'><center><a id='helpmelink' href='javascript:void(0);' onclick='helpRt(this);'>Help Calculate My Application Rate</a></center></div>";
	
			$advancedUpdateField .= "<div id='HelpRate' style='display:none;background-color:#e2f4f8;'>";
	 		$advancedUpdateField .= "<div style='background-color:#e2f4f8; width:52%; text-align:right; float:left;'><label>Irrigation Type:</label></div>";
	 		$typeSelect = "<select onchange='selectIrrigationType(this.options[this.selectedIndex]);' name='irrigationtype' id='irrigationtype'><option value='0'>Select Type</option><option value='1'>Drip</option><option value='2'>Sprinkler</option><option value='3'>General</option></select>";
	 		$advancedUpdateField .= "<div style='background-color:#e2f4f8; width:47%; float:right;'>".$typeSelect."</div>";
	
	//drip rate
	 		$advancedUpdateField .= "<div id='DripRate' style='display:none'>";
	
	 		$advancedUpdateField .= "<div style='clear:both'><div style='background-color:#e2f4f8; width:52%; text-align:right; float:left;'><label>Emitter flow rate:</label></div>";
	 		$advancedUpdateField .= "<div style='background-color:#e2f4f8; width:47%; float:right;'><input type='text' name='emitterFlow' size=5 maxlength=10 onchange='calc_dripRate( this.form )'/><select name='emitterFlowUnits' onchange='calc_dripRate(this.form)'><option value='.016666666666667' selected='selected'>gph</option><option value='.004402867539'>lph</option><option value='1'>gpm</option><option value='15.85032314'>lps</option></select></div></div>";
	
	 		$advancedUpdateField .= "<div style='clear:both'><div style='background-color:#e2f4f8; width:52%; text-align:right; float:left;'><label>Emitter line spacing:</label></div>";
	 		$advancedUpdateField .= "<div style='background-color:#e2f4f8; width:47%; float:right;'>        <input type='text' size=5 maxlength=10 name='spacing'  onchange='calc_dripRate( this.form )'/><select name='spacingUnits' onchange='calc_dripRate( this.form )'><option value='2.54' selected='selected'>in</option><option value='30.48'>ft</option><option value='1'>cm</option><option value='100'>m</option></select></div></div>";
	
	 		$advancedUpdateField .= "<div style='clear:both'><div style='background-color:#e2f4f8; width:52%; text-align:right; float:left;'><label>Distance between drip lines:</label></div>";
	 		$advancedUpdateField .= "<div style='background-color:#e2f4f8; width:47%; float:right;'><br/><input size=5 maxlength=10 type='text' name='distance'  onchange='calc_dripRate( this.form )'/><select name='distanceUnits' onchange='calc_dripRate( this.form )'><option value='2.54'>in</option><option value='30.48' selected='selected'>ft</option><option value='1'>cm</option><option value='100'>m</option></select></div></div>";
	
	 		$advancedUpdateField .= "<div style='clear:both'><div style='background-color:#e2f4f8; width:52%; text-align:right; float:left;'><label>Application Efficiency:</label></div>";
	 		$advancedUpdateField .= "<div style='background-color:#e2f4f8; width:47%; float:right;'><input type='text' size=5 maxlength=10 name='DripEff'  onchange='calc_dripRate( this.form )' value='95'/> %</div></div>";
	 		
	 		$advancedUpdateField .= "</div>";
	//drip rate
	
	//sprinkler rate
	 		$advancedUpdateField .= "<div id='SprinklerRate' style='display:none'>";
	
	 		$advancedUpdateField .= "<div style='clear:both'><div style='background-color:#e2f4f8; width:52%; text-align:right; float:left;'><label>Nozzle Diameter:</label></div>";
	 		$advancedUpdateField .= "<div style='background-color:#e2f4f8; width:47%; float:right;'><input size=3 maxlength=10 type='text' name='diameter'  onchange='calc_precipitation( this.form )'/><select name='diameterUnits' onchange='calc_precipitation( this.form )'><option value='.01984375'>128ths in</option><option value='.0396875'>64ths in</option><option value='.079375'>32nds in</option><option value='.15875'>16ths in</option><option value='.3175'>8ths in</option><option value='2.54' selected='selected'>in</option><option value='1'>cm</option><option value='.1'>mm</option></select></div></div>";
	
	 		$advancedUpdateField .= "<div style='clear:both'><div style='background-color:#e2f4f8; width:52%; text-align:right; float:left;'><label>Pressure:</label></div>";
	 		$advancedUpdateField .= "<div style='background-color:#e2f4f8; width:47%; float:right;'><input size=4 maxlength=10 type='text' name='pressure'  onchange='calc_precipitation( this.form )'/><select name='pressureUnits' onchange='calc_precipitation( this.form )'><option value='1' selected='selected'>psi</option><option value='0.068947573'>bar</option><option value='6.8947625'>kPa</option><option value='2.306661407'>ft</option><option value='.703249615'>m</option></select></div></div>";
	
	 		$advancedUpdateField .= "<div style='clear:both'><div style='background-color:#e2f4f8; width:52%; text-align:right; float:left;'><label>Head Spacing:</label></div>";
	 		$advancedUpdateField .= "<div style='background-color:#e2f4f8; width:47%; float:right;'><input size=4 maxlength=10 type='text' name='headSpacing'   onchange='calc_precipitation( this.form )'/><select name='headSpacingUnits' onchange='calc_precipitation( this.form )'><option value='1' selected='selected'>ft</option><option value='.3048'>m</option></select></div></div>";
	
	 		$advancedUpdateField .= "<div style='clear:both'><div style='background-color:#e2f4f8; width:52%; text-align:right; float:left;'><label>Line Spacing:</label></div>";
	 		$advancedUpdateField .= "<div style='background-color:#e2f4f8; width:47%; float:right;'><input size=4 maxlength=10 type='text' name='lineSpacing'  onchange='calc_precipitation( this.form )'/><select name='lineSpacingUnits' onchange='calc_precipitation( this.form )'><option value='1' selected='selected'>ft</option><option value='.3048'>m</option></select></div></div>";
	
	 		$advancedUpdateField .= "<div style='clear:both'><div style='background-color:#e2f4f8; width:52%; text-align:right; float:left;'><label>Sprinkler Efficiency:</label></div>";
	 		$advancedUpdateField .= "<div style='background-color:#e2f4f8; width:47%; float:right;'><input size=4 maxlength=10 type='text' onfocus='clearText(this)' onchange='calc_precipitation( this.form )' name='efficiency' value='70'/></div></div>";
	
			$advancedUpdateField .= "<div style='display:none'>";
	 		$advancedUpdateField .= "<div style='width:52%; text-align:right; float:left;'><label>Nozzle flow rate:</label></div>";
	 		$advancedUpdateField .= "<div style='width:47%; float:right;'><input size=4 maxlength=10 type='text' name='nozzleFlowRate' /><select style='width:50%' name='nozzleFlowRateUnits' onchange='calc_precipitation( this.form )'><option value='1' selected='selected'>gpm</option><option value='18.85714'>acre-in/day</option><option value='452.5714'>acre-in/hr</option><option value='226.2857'>acre-ft/day</option><option value='15.85032'>lps</option><option value='.264172051'>lpm</option><option value='.004402868'>lph</option></select></div>";
	 		$advancedUpdateField .= "</div>";
	
	 		$advancedUpdateField .= "</div>";
	//sprinkler rate
	
	//general rate
	 		$advancedUpdateField .= "<div id='GeneralRate' style='display:none'>";
	
	 		$advancedUpdateField .= "<div style='clear:both'><div style='background-color:#e2f4f8; width:52%; text-align:right; float:left;'><label>Area:</label></div>";
	 		$advancedUpdateField .= "<div style='background-color:#e2f4f8; width:47%; float:right;'><input size=2 maxlength=10 type='text' name='area'   onchange='calc_applicationRate( this.form )'/><select name='areaUnits' onchange='calc_applicationRate( this.form )'><option value='1' selected='selected'>acre</option><option value='6272640'>sq. in.</option><option value='43560'>sq. ft.</option><option value='.404685658'>hectare</option><option value='40468564.224 '>sq. cm.</option><option value='4046.856579'>sq. meter</option><option value='4840'>sq. yd</option><option value='0.004046856422'>sq. km</option><option value='0.0015625'>sq. mile</option></select></div></div>";
	
	 		$advancedUpdateField .= "<div style='clear:both'><div style='background-color:#e2f4f8; width:52%; text-align:right; float:left;'><label>Flow Rate:</label></div>";
	 		$advancedUpdateField .= "<div style='background-color:#e2f4f8; width:47%; float:right;'><input size=2 maxlength=10  type='text' name='flowRate'   onchange='calc_applicationRate( this.form )'/><select style='width:50%' name='flowRateUnits' onchange='calc_applicationRate( this.form )'><option value='1' selected='selected'>gpm</option><option value='.016666666666667'>gph</option><option value='.000694444'>gpd</option><option value='448.8311688'>cfs</option><option value='18.85714286'>acre-in/day</option><option value='452.5714286'>acre-in/hr</option><option value='226.2857143'>acre-ft/day</option><option value='15.85032314'>lps</option><option value='.004402867539'>lph</option><option value='.000183453'>lpd</option><option value='15850.32314'>cms</option><option value='4.402867521'>cu. m/hr</option></select></div></div>";
	
	 		$advancedUpdateField .= "<div style='clear:both'><div style='background-color:#e2f4f8; width:52%; text-align:right; float:left;'><label>Application Efficiency:</label></div>";
	 		$advancedUpdateField .= "<div style='background-color:#e2f4f8; width:47%; float:right;'><input size=2 maxlength=10 type='text' name='Efficiency'  onchange='calc_applicationRate( this.form )' value='80'/> %</div></div>";
	
	 		$advancedUpdateField .= "</div>";
	//general rate
	 		
			$advancedUpdateField .= "<div id='calcButton' style='display:none;clear:both;'><center><input type=button name='calculatebutton' value='Calculate Rate'></center></div>";
	
	 		$advancedUpdateField .= "</div>";
	 		
	 		$advancedUpdateField .= "</div>";
	 		
			$advancedUpdateField .= "<div name='irateerror' id='irateerror' style='display:none;color:red;clear:both;'></div>";
	 		$advancedUpdateField .= "</div>";
	
	 		$advancedUpdateField .= "<div style='clear:both;border-width: 1px; border-top-style: solid; border-color: #e7e9ea;'>";
	 		$advancedUpdateField .= "<div style='width:52%; text-align:right; float:left;'> <label>For Drip/Micro<br/>% of Soil Wetted:</label></div>";
	 		$advancedUpdateField .= "<div style='width:47%; float:right;'><br/><input onchange='validateGroundwetted(this);' type='text' id='groundwetted' size=5 name='groundwetted' value='".sprintf('%3.0f',$row_UpdateField['groundWetted'])."'> %</div>";
	 		$advancedUpdateField .= "<div name='groundwettederror' id='groundwettederror' style='display:none;color:red;clear:both;'></div>";
	 		$advancedUpdateField .= "</div>";
	
	 		$advancedUpdateField .= "<div style='clear:both;border-width: 1px; border-top-style: solid; border-color: #e7e9ea;'>";
	 		$advancedUpdateField .= "<div style='width:52%; text-align:right; float:left;'><label>Soil Water Content at Field Capacity:</label></div>";
	 		$advancedUpdateField .= "<div style='width:47%; float:right;'><br/><input onchange='validateFieldCapacity(this);' id =\"field-capacity\" name=\"field-capacity\" type=\"text\" size=5 maxlength=\"10\"  value=\"".$row_UpdateField['soilFC']."\"/> in/ft</div>";
	 		$advancedUpdateField .= "<div name='fieldcapacityerror' id='fieldcapacityerror' style='display:none;color:red;clear:both;'></div>";
	 		$advancedUpdateField .= "</div>";
	
	 		$advancedUpdateField .= "<div style='clear:both;border-width: 1px; border-top-style: solid; border-color: #e7e9ea;'>";
	 		$advancedUpdateField .= "<div style='width:52%; text-align:right; float:left;'> <label>Soil Available Water Holding Capacity:</label></div>";
	 		$advancedUpdateField .= "<div style='width:47%; float:right;'><br/><input onchange='validateFieldCapacity(this);' id=\"sawhc\" name=\"sawhc\" type=\"text\"  size=5 maxlength=\"10\"  value=\"".$row_UpdateField['soilAWC']."\"/> in/ft</div>";
	 		$advancedUpdateField .= "<div name='sawhcerror' id='sawhcerror' style='display:none;color:red;clear:both;'></div>";
	 		$advancedUpdateField .= "</div>";
	 		
	 		$advancedUpdateField .= "<div style='clear:both;border-width: 1px; border-top-style: solid; border-color: #e7e9ea;'>";
			$advancedUpdateField .= "<div style='width:52%; text-align:right; float:left;'><label>Management Allowable Depletion:</label></div>";
			$advancedUpdateField .= "<div style='width:47%; float:right;'><br/><input onchange='validateMad(this);' name=\"mad\" type=\"text\"  size=5 maxlength=\"10\" value=\"".$row_UpdateField['mad']."\" /> %</div>";
	 		$advancedUpdateField .= "<div name='maderror' id='maderror' style='display:none;color:red;clear:both;'></div>";
	 		$advancedUpdateField .= "</div>";
	 		
	 		$advancedUpdateField .= "<div style='clear:both;border-width: 1px; border-top-style: solid; border-color: #e7e9ea;'>";
	 		$advancedUpdateField .= "<div style='width:52%; text-align:right; float:left;'> <label>Emergence<br/>Date:</label></div>";
	 		$advancedUpdateField .= "<div style='width:47%; float:right;'><br/><input onchange='validateFieldDates(this);' type='text' id='emergence-date' size=12 name='emergence-date' value='".date("M d, Y",$EmergenceDate)."'> </div>";
	 		$advancedUpdateField .= "<div name='emergencedateerror' id='emergencedateerror' style='display:none;color:red;clear:both;'></div>";
	 		$advancedUpdateField .= "</div>";
	
			//No longer user water budget start date, so simple fix is to hide it for the moment. 		
			$advancedUpdateField .= "<input type='hidden' id='water-budget-date' size=12  name='water-budget-date' value='".date("M d, Y",$WaterBudgetDate)."'>";
	
	 		$advancedUpdateField .= "<div style='clear:both;border-width: 1px; border-top-style: solid; border-color: #e7e9ea;'>";
			$advancedUpdateField .= "<div style='width:52%; text-align:right; float:left;'> <label>Canopy Cover<br/>exceeds 10% of Field:</label></div>";
			$advancedUpdateField .= "<div style='width:47%; float:right;'><br/><input onchange='validateFieldDates(this);'  type='text' id='ccc-10'  size=12 name='ccc-10' value='".date("M d, Y",$CanopyCover10Date)."'> </div>";
	 		$advancedUpdateField .= "<div name='ccc10dateerror' id='ccc10dateerror' style='display:none;color:red;clear:both;'></div>";
	 		$advancedUpdateField .= "</div>";
	
	 		$advancedUpdateField .= "<div style='clear:both;border-width: 1px; border-top-style: solid; border-color: #e7e9ea;'>";
			$advancedUpdateField .= "<div style='width:52%; text-align:right; float:left;'><label>Canopy Cover exceeds 70% of Field:</label></div>";
			$advancedUpdateField .= "<div style='width:47%; float:right;'><br/><input onchange='validateFieldDates(this);'  type='text' id='ccc-70' size=12  name='ccc-70' value='".date("M d, Y",$CanopyCover70Date)."'> </div>";
	 		$advancedUpdateField .= "<div name='ccc70dateerror' id='ccc70dateerror' style='display:none;color:red;clear:both;'></div>";
	 		$advancedUpdateField .= "</div>";
	
	 		$advancedUpdateField .= "<div style='clear:both;border-width: 1px; border-top-style: solid; border-color: #e7e9ea;'>";
			$advancedUpdateField .= "<div style='width:52%; text-align:right; float:left;'> <label>Crop Initial<br/>Maturation Date:</label></div>";
			$advancedUpdateField .= "<div style='width:47%; float:right;'><br/><input onchange='validateFieldDates(this);' type='text' id='crop-maturation' size=12  name='crop-maturation' value='".date("M d, Y",$CropMaturationDate)."'> </div>";
	 		$advancedUpdateField .= "<div name='cropmaturationdateerror' id='cropmaturationdateerror' style='display:none;color:red;clear:both;'></div>";
	 		$advancedUpdateField .= "</div>";
	
	 		$advancedUpdateField .= "<div style='clear:both;border-width: 1px; border-top-style: solid; border-color: #e7e9ea;'>";
			$advancedUpdateField .= "<div style='width:52%; text-align:right; float:left;'> <label>End of Growing Season Date:</label></div>";
			$advancedUpdateField .= "<div style='width:47%; float:right;'><br/><input onchange='validateFieldDates(this);'  type='text' id='seasonend' size=12  name='seasonend' value='".date("M d, Y",$EndSeasonDate)."'> </div>";
	 		$advancedUpdateField .= "<div name='endseasondateerror' id='endseasondateerror' style='display:none;color:red;clear:both;'></div>";
	 		$advancedUpdateField .= "</div>";
	
	 		$advancedUpdateField .= "<div style='clear:both;border-width: 1px; border-top-style: solid; border-color: #e7e9ea;'>";
			$advancedUpdateField .= "<div style='width:52%; text-align:right; float:left;'><label>Root Depth on<br/>Start Date:</label></div>";
			$advancedUpdateField .= "<div style='width:47%; float:right;'><br/><input onchange='validateRootDepths(this);'   name=\"root-depth\" type=\"text\"  size=5 maxlength=\"10\"  value=\"".($row_UpdateField['rz_val1'])."\" /> in</div>";
	 		$advancedUpdateField .= "<div name='rootdeptherror' id='rootdeptherror' style='display:none;color:red;clear:both;'></div>";
	 		$advancedUpdateField .= "</div>";
	
	 		$advancedUpdateField .= "<div style='clear:both;border-width: 1px; border-top-style: solid; border-color: #e7e9ea;'>";
			$advancedUpdateField .= "<div style='width:52%; text-align:right; float:left;'><label>Maximum Managed Root Zone Depth:</label></div>";
			$advancedUpdateField .= "<div style='width:47%; float:right;'><br/><input onchange='validateRootDepths(this);'  name=\"mmrzd\" type=\"text\"  size=5 maxlength=\"10\"  value=\"".($row_UpdateField['rz_val2'])."\" /> in</div>";
	 		$advancedUpdateField .= "<div name='maximumrootdeptherror' id='maximumrootdeptherror' style='display:none;color:red;clear:both;'></div>";
	 		$advancedUpdateField .= "</div>";
	
	 		$advancedUpdateField .= "<div style='clear:both;border-width: 1px; border-top-style: solid; border-color: #e7e9ea; '>";
			$advancedUpdateField .= "<div style='width:52%; text-align:right; float:left;'><label>Initial Crop Coefficient:</label></div>";
			$advancedUpdateField .= "<div style='width:47%; float:right;'><br/><input onchange='validateCropCoefficients(this);'  name=\"icc\" type=\"text\"  size=5 maxlength=\"10\" value=\"".$row_UpdateField['kc1']."\" /></div>";
	 		$advancedUpdateField .= "<div name='iccerror' id='iccerror' style='display:none;color:red;clear:both;'></div>";
	 		$advancedUpdateField .= "</div>";
	
	 		$advancedUpdateField .= "<div style='clear:both;border-width: 1px; border-top-style: solid; border-color: #e7e9ea;'>";
			$advancedUpdateField .= "<div style='width:52%; text-align:right; float:left;'><label>Full Cover Crop Coefficient:</label></div>";
			$advancedUpdateField .= "<div style='width:47%; float:right;'><br/><input onchange='validateCropCoefficients(this);'   name=\"fccc\" type=\"text\"  size=5 maxlength=\"10\" value=\"".$row_UpdateField['kc2']."\" /></div>";
	 		$advancedUpdateField .= "<div name='fcccerror' id='fcccerror' style='display:none;color:red;clear:both;'></div>";
	 		$advancedUpdateField .= "</div>";
	
	 		$advancedUpdateField .= "<div style='clear:both; border-width: 1px; border-top-style: solid; border-color: #e7e9ea; '>";
			$advancedUpdateField .= "<div style='width:52%; text-align:right; float:left;'><label>Final Crop<br/>Coefficient:</label></div>";
			$advancedUpdateField .= "<div style='width:47%; float:right;'><br/><input onchange='validateCropCoefficients(this);'   name=\"fcc\" type=\"text\"  size=5 maxlength=\"10\" value=\"".$row_UpdateField['kc3']."\" /></div>";
	 		$advancedUpdateField .= "<div name='fccerror' id='fccerror' style='display:none;color:red;clear:both;'></div>";
	 		$advancedUpdateField .= "</div>";
	
			//If it is a forage crop
			$thisStyle = "display:none;";
			if($irrigationScheduler->row_CropInfo['postCuttingFlatDays'] > 0 && $irrigationScheduler->row_CropInfo['postCuttingRecoveryDays'] > 0)
			{
				$thisStyle = "display:block;";
			}
	 		$advancedUpdateField .= "<div style='clear:both;$thisStyle border-width: 1px; border-top-style: solid; border-color: #e7e9ea; '>";
			$advancedUpdateField .= "<div style='width:52%; text-align:right; float:left;'><label>Post-Cutting Kc<br/>Flat Days:</label></div>";
			$advancedUpdateField .= "<div style='width:47%; float:right;'><br/><input onchange=''   name=\"pckfd\" type=\"text\"  size=5 maxlength=\"10\" value=\"".$irrigationScheduler->partA."\" /></div>";
	 		$advancedUpdateField .= "<div name='pckfderror' id='pckfderror' style='display:none;color:red;clear:both;'></div>";
	 		$advancedUpdateField .= "</div>";
	
	 		$advancedUpdateField .= "<div style='clear:both;$thisStyle border-width: 1px; border-top-style: solid; border-color: #e7e9ea; '>";
			$advancedUpdateField .= "<div style='width:52%; text-align:right; float:left;'><label>Post Cutting Kc Recovery Days:</label></div>";

			$advancedUpdateField .= "<div style='width:47%; float:right;'><br/><input onchange=''   name=\"pckrd\" type=\"text\"  size=5 maxlength=\"10\" value=\"";
			$advancedUpdateField .= $irrigationScheduler->partB."\" /></div>";
	 		$advancedUpdateField .= "<div name='pckrderror' id='pckrderror' style='display:none;color:red;clear:both;'></div>";
	 		$advancedUpdateField .= "</div>";
	 		//end forage crop
	
			$advancedUpdateField .= "<div  style=\"clear:both; border-width: 1px; border-top-style: solid; border-color: #e7e9ea;\"><center><input name=\"update-field\" type=\"submit\" value=\"Update Field\" /></center></div>";
			$advancedUpdateField .= "<input type=\"hidden\" name=\"MM_update\" value=\"field-info\">";
			$advancedUpdateField .= "</form>";
	 	
		 	$advancedUpdateField .= "<script type='text/javascript'> ".PHP_EOL;
			$advancedUpdateField .= "$(document).ready(function(){  ".PHP_EOL;
			$advancedUpdateField .= "$('#emergence-date').datepicker({  ".PHP_EOL;
			$advancedUpdateField .= "yearRange: '".(date("Y")-4).":".(date("Y")+1)."', ".PHP_EOL;
			$advancedUpdateField .= "dateFormat: 'M dd, yy', ".PHP_EOL;
			$advancedUpdateField .= "changeMonth: true, ".PHP_EOL;
			$advancedUpdateField .= "changeYear: true ".PHP_EOL;
			$advancedUpdateField .= "}); ".PHP_EOL;
			$advancedUpdateField .= "$('#water-budget-date').datepicker({  ".PHP_EOL;
			$advancedUpdateField .= "yearRange: '".(date("Y")-4).":".(date("Y")+1)."', ".PHP_EOL;
			$advancedUpdateField .= "dateFormat: 'M dd, yy', ".PHP_EOL;
			$advancedUpdateField .= "changeMonth: true, ".PHP_EOL;
			$advancedUpdateField .= "changeYear: true ".PHP_EOL;
			$advancedUpdateField .= "}); ".PHP_EOL;
			$advancedUpdateField .= "$('#ccc-10').datepicker({  ".PHP_EOL;
			$advancedUpdateField .= "yearRange: '".(date("Y")-4).":".(date("Y")+1)."', ".PHP_EOL;
			$advancedUpdateField .= "dateFormat: 'M dd, yy', ".PHP_EOL;
			$advancedUpdateField .= "changeMonth: true, ".PHP_EOL;
			$advancedUpdateField .= "changeYear: true ".PHP_EOL;
			$advancedUpdateField .= "}); ".PHP_EOL;
			$advancedUpdateField .= "$('#ccc-70').datepicker({  ".PHP_EOL;
			$advancedUpdateField .= "yearRange: '".(date("Y")-4).":".(date("Y")+1)."', ".PHP_EOL;
			$advancedUpdateField .= "dateFormat: 'M dd, yy', ".PHP_EOL;
			$advancedUpdateField .= "changeMonth: true, ".PHP_EOL;
			$advancedUpdateField .= "changeYear: true ".PHP_EOL;
			$advancedUpdateField .= "}); ".PHP_EOL;
			$advancedUpdateField .= "$('#crop-maturation').datepicker({  ".PHP_EOL;
			$advancedUpdateField .= "yearRange: '".(date("Y")-4).":".(date("Y")+1)."', ".PHP_EOL;
			$advancedUpdateField .= "dateFormat: 'M dd, yy', ".PHP_EOL;
			$advancedUpdateField .= "changeMonth: true, ".PHP_EOL;
			$advancedUpdateField .= "changeYear: true ".PHP_EOL;
			$advancedUpdateField .= "}); ".PHP_EOL;
			$advancedUpdateField .= "$('#seasonend').datepicker({  ".PHP_EOL;
			$advancedUpdateField .= "yearRange: '".(date("Y")-4).":".(date("Y")+1)."', ".PHP_EOL;
			$advancedUpdateField .= "dateFormat: 'M dd, yy', ".PHP_EOL;
			$advancedUpdateField .= "changeMonth: true, ".PHP_EOL;
			$advancedUpdateField .= "changeYear: true ".PHP_EOL;
			$advancedUpdateField .= "}); ".PHP_EOL;
			$advancedUpdateField .= "}); ".PHP_EOL;
		 	$advancedUpdateField .= "</script>".PHP_EOL;
	
			return $advancedUpdateField;
		}


 	public function AdvancedIrrigationJavaScripts()
 	{
 		$AdvancedIrrigationJavaScripts = "";
		$AdvancedIrrigationJavaScripts .= "<script type='text/javascript' language=\"javascript\"> ";
                
		//Validate Root depth values
		$AdvancedIrrigationJavaScripts .= "function validateRootDepths(ele) {";
		$AdvancedIrrigationJavaScripts .= "var MyDiv = document.getElementById('rootdeptherror');";
		$AdvancedIrrigationJavaScripts .= "MyDiv.style.display = 'none'; ";
		$AdvancedIrrigationJavaScripts .= "if((document.forms[\"advanced-field-info\"].elements[\"root-depth\"].value < 1) || (document.forms[\"advanced-field-info\"].elements[\"root-depth\"].value > 120))";
		$AdvancedIrrigationJavaScripts .= "{";
		$AdvancedIrrigationJavaScripts .= " MyDiv.innerHTML = \"Root Depth on Start Date is normally >= 1 and <= 120\"; ";
		$AdvancedIrrigationJavaScripts .= " MyDiv.style.display = 'block'; ";
		$AdvancedIrrigationJavaScripts .= "}";
		$AdvancedIrrigationJavaScripts .= "var MyDiv = document.getElementById('maximumrootdeptherror');";
		$AdvancedIrrigationJavaScripts .= "MyDiv.style.display = 'none'; ";
		$AdvancedIrrigationJavaScripts .= "if(parseInt(document.forms[\"advanced-field-info\"].elements[\"mmrzd\"].value) < parseInt(document.forms[\"advanced-field-info\"].elements[\"root-depth\"].value))";
		$AdvancedIrrigationJavaScripts .= "{";
		$AdvancedIrrigationJavaScripts .= " MyDiv.innerHTML = \"Maximum Root Depth is normally greater than the Root Depth on Start Date\"; ";
		$AdvancedIrrigationJavaScripts .= " MyDiv.style.display = 'block'; ";
		$AdvancedIrrigationJavaScripts .= "}  ";
		$AdvancedIrrigationJavaScripts .= "if((document.forms[\"advanced-field-info\"].elements[\"mmrzd\"].value < 1) || (document.forms[\"advanced-field-info\"].elements[\"mmrzd\"].value > 120))";
		$AdvancedIrrigationJavaScripts .= "{";
		$AdvancedIrrigationJavaScripts .= " MyDiv.innerHTML = \"Maximum Managed Root Zone Depth is normally >= 1 and <= 120\"; ";
		$AdvancedIrrigationJavaScripts .= " MyDiv.style.display = 'block'; ";
		$AdvancedIrrigationJavaScripts .= "}";
		$AdvancedIrrigationJavaScripts .= "}";
		//End validate root depth values
		
		//Validate Crop Coefficient values
		$AdvancedIrrigationJavaScripts .= "function validateCropCoefficients(ele) {";
		$AdvancedIrrigationJavaScripts .= "var MyDiv = document.getElementById('iccerror');";
		$AdvancedIrrigationJavaScripts .= "MyDiv.style.display = 'none'; ";
		$AdvancedIrrigationJavaScripts .= "if((document.forms[\"advanced-field-info\"].elements[\"icc\"].value < 0) || (document.forms[\"advanced-field-info\"].elements[\"icc\"].value > 1.2))";
		$AdvancedIrrigationJavaScripts .= "{";
		$AdvancedIrrigationJavaScripts .= " MyDiv.innerHTML = \"Initial Crop Coefficient is normally > 0  and < 1.2\"; ";
		$AdvancedIrrigationJavaScripts .= " MyDiv.style.display = 'block'; ";
		$AdvancedIrrigationJavaScripts .= "}";
		$AdvancedIrrigationJavaScripts .= "var MyDiv = document.getElementById('fcccerror');";
		$AdvancedIrrigationJavaScripts .= "MyDiv.style.display = 'none'; ";
		$AdvancedIrrigationJavaScripts .= "if((document.forms[\"advanced-field-info\"].elements[\"fccc\"].value < 0) || (document.forms[\"advanced-field-info\"].elements[\"fccc\"].value > 1.2))";
		$AdvancedIrrigationJavaScripts .= "{";
		$AdvancedIrrigationJavaScripts .= " MyDiv.innerHTML = \"Full Cover Crop Coefficient is normally > 0  and < 1.2\"; ";
		$AdvancedIrrigationJavaScripts .= " MyDiv.style.display = 'block'; ";
		$AdvancedIrrigationJavaScripts .= "}";

		$AdvancedIrrigationJavaScripts .= "var MyDiv = document.getElementById('fccerror');";
		$AdvancedIrrigationJavaScripts .= "MyDiv.style.display = 'none'; ";
		$AdvancedIrrigationJavaScripts .= "if((document.forms[\"advanced-field-info\"].elements[\"fcc\"].value < 0) || (document.forms[\"advanced-field-info\"].elements[\"fcc\"].value > 1.2))";
		$AdvancedIrrigationJavaScripts .= "{";
		$AdvancedIrrigationJavaScripts .= " MyDiv.innerHTML = \"Final Crop Coefficient is normally > 0  and < 1.2\"; ";
		$AdvancedIrrigationJavaScripts .= " MyDiv.style.display = 'block'; ";
		$AdvancedIrrigationJavaScripts .= "}";

		$AdvancedIrrigationJavaScripts .= "}";
		//End validate Crop Coefficient values		
                
                
                
    //Phone number validation? Troy  will check on that more
                
		//Validate Field Capacities
		$AdvancedIrrigationJavaScripts .= "function validateFieldCapacity(ele) {";
		$AdvancedIrrigationJavaScripts .= "var MyDiv = document.getElementById('fieldcapacityerror');";
		$AdvancedIrrigationJavaScripts .= "MyDiv.style.display = 'none'; ";
		$AdvancedIrrigationJavaScripts .= "if((document.forms[\"advanced-field-info\"].elements[\"field-capacity\"].value < 0.5) || (document.forms[\"advanced-field-info\"].elements[\"field-capacity\"].value > 6))";
		$AdvancedIrrigationJavaScripts .= "{";
		$AdvancedIrrigationJavaScripts .= " MyDiv.innerHTML = \"Field Capacity is normally >= 0.5 and <= 6\"; ";
		$AdvancedIrrigationJavaScripts .= " MyDiv.style.display = 'block'; ";
		$AdvancedIrrigationJavaScripts .= "}";
		$AdvancedIrrigationJavaScripts .= "var MyDiv = document.getElementById('sawhcerror');";
		$AdvancedIrrigationJavaScripts .= "MyDiv.style.display = 'none'; ";
		$AdvancedIrrigationJavaScripts .= "if(document.forms[\"advanced-field-info\"].elements[\"sawhc\"].value >= document.forms[\"advanced-field-info\"].elements[\"field-capacity\"].value)";
		$AdvancedIrrigationJavaScripts .= "{";
		$AdvancedIrrigationJavaScripts .= " MyDiv.innerHTML = \"S.A.W.H.C. should be less than Field Capacity\"; ";
		$AdvancedIrrigationJavaScripts .= " MyDiv.style.display = 'block'; ";
		$AdvancedIrrigationJavaScripts .= "}";
		$AdvancedIrrigationJavaScripts .= "else if((document.forms[\"advanced-field-info\"].elements[\"sawhc\"].value < 0.2) || (document.forms[\"advanced-field-info\"].elements[\"sawhc\"].value > 3.5))";
		$AdvancedIrrigationJavaScripts .= "{";
		$AdvancedIrrigationJavaScripts .= " MyDiv.innerHTML = \"S.A.W.H.C. is normally >= 0.2 and <= 3.5\"; ";
		$AdvancedIrrigationJavaScripts .= " MyDiv.style.display = 'block'; ";
		$AdvancedIrrigationJavaScripts .= "}";
		$AdvancedIrrigationJavaScripts .= "}";
		//End Validate Field Capacities
		
		//Validated Field Dates
		$AdvancedIrrigationJavaScripts .= "function validateFieldDates(ele) {";
		$AdvancedIrrigationJavaScripts .= "var MyDiv = document.getElementById('emergencedateerror');";
		$AdvancedIrrigationJavaScripts .= "MyDiv.style.display = 'none'; ";
		$AdvancedIrrigationJavaScripts .= "if((new Date(document.forms[\"advanced-field-info\"].elements[\"emergence-date\"].value) - new Date(document.forms[\"advanced-field-info\"].elements[\"ccc-10\"].value)) > 0)";
		$AdvancedIrrigationJavaScripts .= "{";
		$AdvancedIrrigationJavaScripts .= " MyDiv.innerHTML = \"Emergence Date should be before Crop Canopy Cover 10% Date\"; ";
		$AdvancedIrrigationJavaScripts .= " MyDiv.style.display = 'block'; ";
		$AdvancedIrrigationJavaScripts .= "}";

		$AdvancedIrrigationJavaScripts .= "var MyDiv = document.getElementById('ccc10dateerror');";
		$AdvancedIrrigationJavaScripts .= "MyDiv.style.display = 'none'; ";
		$AdvancedIrrigationJavaScripts .= "if((new Date(document.forms[\"advanced-field-info\"].elements[\"ccc-10\"].value) - new Date(document.forms[\"advanced-field-info\"].elements[\"ccc-70\"].value)) > 0)";
		$AdvancedIrrigationJavaScripts .= "{";
		$AdvancedIrrigationJavaScripts .= " MyDiv.innerHTML = \"Crop Canopy Cover 10% Date should be before Full Cover Date\"; ";
		$AdvancedIrrigationJavaScripts .= " MyDiv.style.display = 'block'; ";
		$AdvancedIrrigationJavaScripts .= "}";
		
		$AdvancedIrrigationJavaScripts .= "var MyDiv = document.getElementById('ccc70dateerror');";
		$AdvancedIrrigationJavaScripts .= "MyDiv.style.display = 'none'; ";
		$AdvancedIrrigationJavaScripts .= "if((new Date(document.forms[\"advanced-field-info\"].elements[\"ccc-70\"].value) - new Date(document.forms[\"advanced-field-info\"].elements[\"crop-maturation\"].value)) > 0)";
		$AdvancedIrrigationJavaScripts .= "{";
		$AdvancedIrrigationJavaScripts .= " MyDiv.innerHTML = \"Crop Canopy Cover 70% Date should be before Crop Maturation Date\"; ";
		$AdvancedIrrigationJavaScripts .= " MyDiv.style.display = 'block'; ";
		$AdvancedIrrigationJavaScripts .= "}";

		$AdvancedIrrigationJavaScripts .= "var MyDiv = document.getElementById('cropmaturationdateerror');";
		$AdvancedIrrigationJavaScripts .= "MyDiv.style.display = 'none'; ";
		$AdvancedIrrigationJavaScripts .= "if((new Date(document.forms[\"advanced-field-info\"].elements[\"crop-maturation\"].value) - new Date(document.forms[\"advanced-field-info\"].elements[\"seasonend\"].value)) > 0)";
		$AdvancedIrrigationJavaScripts .= "{";
		$AdvancedIrrigationJavaScripts .= " MyDiv.innerHTML = \"Crop Maturation Date should be before Season End Date\"; ";
		$AdvancedIrrigationJavaScripts .= " MyDiv.style.display = 'block'; ";
		$AdvancedIrrigationJavaScripts .= "}";

		$AdvancedIrrigationJavaScripts .= "var MyDiv = document.getElementById('endseasondateerror');";
		$AdvancedIrrigationJavaScripts .= "MyDiv.style.display = 'none'; ";
		$AdvancedIrrigationJavaScripts .= "if((new Date(document.forms[\"advanced-field-info\"].elements[\"seasonend\"].value) - new Date(document.forms[\"advanced-field-info\"].elements[\"crop-maturation\"].value)) < 0)";
		$AdvancedIrrigationJavaScripts .= "{";
		$AdvancedIrrigationJavaScripts .= " MyDiv.innerHTML = \"End Season Date should be after Crop Maturation Date\"; ";
		$AdvancedIrrigationJavaScripts .= " MyDiv.style.display = 'block'; ";
		$AdvancedIrrigationJavaScripts .= "}";


		$AdvancedIrrigationJavaScripts .= "}";
		//End Validate Field Dates

		$AdvancedIrrigationJavaScripts .= "function validateMad(ele) {";
		$AdvancedIrrigationJavaScripts .= "var MyDiv = document.getElementById('maderror');";
		$AdvancedIrrigationJavaScripts .= "MyDiv.style.display = 'none'; ";
		$AdvancedIrrigationJavaScripts .= "if((document.forms[\"advanced-field-info\"].elements[\"mad\"].value < 10) || (document.forms[\"advanced-field-info\"].elements[\"mad\"].value > 90))";
		$AdvancedIrrigationJavaScripts .= "{";
		$AdvancedIrrigationJavaScripts .= " MyDiv.innerHTML = \"Management Allowable Deficit is normally >= 10% and <= 90%\"; ";
		$AdvancedIrrigationJavaScripts .= " MyDiv.style.display = 'block'; ";
		$AdvancedIrrigationJavaScripts .= "}";
		$AdvancedIrrigationJavaScripts .= "}";

		$AdvancedIrrigationJavaScripts .= "function validateGroundwetted(ele) {";
		$AdvancedIrrigationJavaScripts .= "var MyDiv = document.getElementById('groundwettederror');";
		$AdvancedIrrigationJavaScripts .= "MyDiv.style.display = 'none'; ";
		$AdvancedIrrigationJavaScripts .= "if((document.forms[\"advanced-field-info\"].elements[\"groundwetted\"].value < 10) || (document.forms[\"advanced-field-info\"].elements[\"groundwetted\"].value > 100))";
		$AdvancedIrrigationJavaScripts .= "{";
		$AdvancedIrrigationJavaScripts .= " MyDiv.innerHTML = \"Please enter a percent between 0 and 100.\"; ";
		$AdvancedIrrigationJavaScripts .= " MyDiv.style.display = 'block'; ";
		$AdvancedIrrigationJavaScripts .= "}";
		$AdvancedIrrigationJavaScripts .= "}";

		$AdvancedIrrigationJavaScripts .= "</script>";
 		return $AdvancedIrrigationJavaScripts;
 	}
 	
	}

?>