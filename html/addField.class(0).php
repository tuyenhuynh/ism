<?php
	class addField
	{
		public function toHTML()
		{
			global $irrigationScheduler;
			global $iform, $validation, $database;
	 		if(isset($_SESSION['value_array']['field-name']))
	 		{
	 			$FieldName = $_SESSION['value_array']['field-name'];
	 		}
	 		else
	 		{
	 			$FieldName = "";
	 		}
	 		if(isset($_SESSION['value_array']['year']))
	 		{
	 			$FieldYear = $_SESSION['value_array']['year'];
	 		}
	 		else
	 		{
	 			$FieldYear = "";
	 		} 		
	 		if(isset($_SESSION['value_array']['station']))
	 		{
	 			$FieldStation = $_SESSION['value_array']['station'];
	 		}
	 		else
	 		{
	 			$FieldStation = "";
	 		} 	
	
	 		if(isset($_SESSION['value_array']['network']))
	 		{
	 			$FieldNetwork = $_SESSION['value_array']['network'];
	 		}
	 		else
	 		{
	 			$FieldNetwork = "";
	 		} 	
	
	 		if(isset($_SESSION['value_array']['crop']))
	 		{
	 			$FieldCrop = $_SESSION['value_array']['crop'];
	 		}
	 		else
	 		{
	 			$FieldCrop = "";
	 		} 	 		
	 		if(isset($_SESSION['value_array']['soil-type']))
	 		{
	 			$FieldSoil = $_SESSION['value_array']['soil-type'];
	 		}
	 		else
	 		{
	 			$FieldSoil = "";
	 		} 	 		 		
	 			
	 		$addField = "";
	 		$addField .= $this->addFieldJavaScripts();
	 		$addField .= "<div class=\"content\">";
			$addField .= "<h2><a name='top'></a>Add New Field</h2>";
			$addField .= "<script type=\"text/javascript\">";
			$addField .= "function fillCrops(stationID, cropID){  ";
			$addField .= "$.post( '/ism/ajax/getStationCrops.php', $('#addFieldForm').serialize(), function (j) { ".PHP_EOL;
			$addField .= "var crops = document.getElementById('crop'); var cCtl = document.getElementById('crop'); ";
			$addField .= "var options = ''; ";
			
			$addField .= " if(j.length > 1) { cCtl.disabled=false; } else { cCtl.disabled=true; }      ".PHP_EOL;
			$addField .= "for (var i = 0; i < j.length; i++) ".PHP_EOL;
			$addField .= "{  ";
			$addField .= "if(j[i].optionValue == cropID) { ";
			$addField .= "options += '<option selected=selected value=\"' + j[i].optionValue + '\">' + j[i].optionDisplay + '</option>';  ".PHP_EOL;
			$addField .= " } else {       ";
			$addField .= "options += '<option value=\"' + j[i].optionValue + '\">' + j[i].optionDisplay + '</option>';  ".PHP_EOL;
			$addField .= "}        ";
			$addField .= "}        ";
			$addField .= "$('select#crop').html(options); ";
	
	
			$addField .= "}, 'json' ); ".PHP_EOL;	
			$addField .= "}";
			$addField .= "</script>";
			if($irrigationScheduler->session->isMobileDevice > 0)
			{
				$addField .= "<div style=\"clear:both;\"><center><a href='".$irrigationScheduler->session->basepath."&amp;help=add-a-field'>Help</a></center></div>";
			}
		 	else
			{
				$addField .= "<div style=\"clear:both;\"><center><a id='addfieldHelp' class='helplink' href='/ism/ajaxPage.php?help=add-a-field'>Help</a></center></div>";
				$addField .= "<script type=\"text/javascript\">";
				$addField .= "$(document).ready(function(){";
				$addField .= "$('#addfieldHelp').fancybox({ 'width':725, 'autoDimensions': false });".PHP_EOL;
				$addField .= "})";
				$addField .= "</script>";
			}
			if(isset($_SESSION['fbversion']) && $_SESSION['fbversion'])
			{
	    	$addField .= "<form action='".$irrigationScheduler->session->processPath."' method='POST' name='addFieldForm' id='addFieldForm'><input type=hidden name='add-field' value='1'>";
			}
			else
			{
	    	$addField .= "<form action='".$irrigationScheduler->session->processPath."' method='POST' name='addFieldForm' id='addFieldForm'><input type=hidden name='add-field' value='1'>";
			}
			$addField .= $this->fieldBasedOn($irrigationScheduler->htmlFactory->FieldID);
			$addField .= $irrigationScheduler->htmlFactory->fieldName($FieldName);
			$addField .= $irrigationScheduler->htmlFactory->fieldYear($FieldYear);
			$addField .= $irrigationScheduler->htmlFactory->selectWeatherNetwork($FieldNetwork);
			$addField .= $irrigationScheduler->htmlFactory->selectStation($FieldStation,"addFieldForm"," fillCrops(this.value); ");
			$addField .= $irrigationScheduler->htmlFactory->selectCrop($FieldCrop,false,true);
			$addField .= $irrigationScheduler->htmlFactory->selectSoil($FieldSoil);
			if(isset($_SESSION['error_array']) && count($_SESSION['error_array']) > 0)
			{
				$addField .=  "<div style=\"clear:both\" name='errorPHP' id='errorPHP' class='errorPHP'>Please fix the following errors and try again:<br/><label>";
				foreach($_SESSION['error_array'] as $key => $value)
				{
					$addField .= $value."<br/>";
				}
				$addField .= "</label></div>";
			}
			$addField .= "<div  style=\"color:red;clear:both;display:none;\" id='addFieldErrorCodes' name='addFieldErrorCodes'></div>";
			$addField .= "<div style=\"clear:both;\"><br/><center><input name='save' type='button' onclick='if(validateAddField()){ document.addFieldForm.submit(); return false; }' value='Add Field' /></center></div>";	
	
			$addField .= "<script type=\"text/javascript\">";
			$addField .= "$(document).ready(function(){";
			$addField .= " fillStationList(); ";
			$addField .= "})";
			$addField .= "</script>";
	
	
			$addField .= "</form>";
	
	     $addField .="</div>";
	
	
	 		return $addField;
		}


 	
 	public function fieldBasedOn($fieldID)
 	{
 		
 		global $database;
 		global $irrigationScheduler;
		$selectField = "";
		if($irrigationScheduler->session->username == 'sehill')
		{
 			$query_SelectField = sprintf("SELECT distinct a.fieldID as fieldID, a.fieldName as fieldName, a.year as year, b.cropName as cropName FROM irrigation.tblfield a, irrigation.tblcropdefaults b WHERE b.cropDefaultsID = a.cropid ORDER BY a.fieldid, a.fieldName, a.year ASC ");
 		}
		else
 			$query_SelectField = sprintf("SELECT distinct a.fieldID as fieldID, a.fieldName as fieldName, a.year as year, b.cropName as cropName FROM irrigation.tblfield a, irrigation.tblcropdefaults b WHERE growerID = %s and b.cropDefaultsID = a.cropid ORDER BY a.fieldName, a.year ASC ", GetSQLValueString($irrigationScheduler->htmlFactory->GrowerID, "int"));
		$SelectField = $database->query($query_SelectField);
		$selectField .= "<select style='width:236px;'  onchange='fieldBasedOnChanged(this.form, this);' name=\"basedonfield\">";
		$selectField .= "<option value=\"0\" selected=\"selected\">Select a Field</option>";
		while( $row_SelectField = mysql_fetch_assoc($SelectField) )
		{
			$selectField .= "<option value=\"".$row_SelectField['fieldID']."\" "; 
			$selectField .= "> ".$row_SelectField['fieldName'].", ".$row_SelectField['year']."; ".$row_SelectField['cropName'];
			$selectField .= "</option>";
		}
		$selectField .= "</select>";
	 		
 		$fieldName = "<div style='clear:both'> ";
 		$fieldName .= "<script type='text/javascript'>".PHP_EOL;
		$fieldName .= "function fieldBasedOnCheckChanged(theCheckBox) {";
 		$fieldName .= "var tDiv = document.getElementById('fieldbasedonselectdiv');";
 		$fieldName .= "if(theCheckBox.checked)";
 		$fieldName .= "{ tDiv.style.display='block'; }";
		$fieldName .= "else";
 		$fieldName .= "{ tDiv.style.display='none';  }";
 		$fieldName .= "}";


		$fieldName .= "function fieldBasedOnChanged(thisForm, theSelectList) {";
		$fieldName .= "if(theSelectList.value > 0) {  "; //a field is selected
		$fieldName .= "$.post( '/ism/ajax/getFieldDefaults.php', $('#addFieldForm').serialize(), ";
		$fieldName .= "function (j) { ";
		$fieldName .= "var dd = thisForm['weather_network'];  dd.value=j.weather_network;  ";
		$fieldName .= "fillStationList(j.weatherStnID, j.cropID);  ";
		$fieldName .= "var dd = thisForm['soil-type'];  dd.value=j.soilID;  ";
		$fieldName .= "var dd = thisForm['crop'];  dd.value=j.cropID;  ";
		$fieldName .= "}, "; //end function(j) {
		$fieldName .= "'json' ); ".PHP_EOL;	//end $.post(	
		$fieldName .= "} else { "; //theSelectList.Value <= 0
		$fieldName .= "  ";
		$fieldName .= "}";  //end if(theSelectList.value > 0) {
		
 		$fieldName .= "var tDiv = document.getElementById('weather_network');";
 		$fieldName .= "}";

 		$fieldName .= "</script>";
 		$fieldName .= "<input type='checkbox' name='fieldbasedonchk' id='fieldbasedonchk' onchange='fieldBasedOnCheckChanged(this);'>";
 		$fieldName .= "<label>Check box to start with existing field:</label>";
		$fieldName .= "<div name='fieldbasedonselectdiv' id='fieldbasedonselectdiv' style='display:none'>";
		$fieldName .= "<div style='clear:both;width:20%; float:left;'>&nbsp;</div><div style='float:left'>&nbsp;&nbsp;".$selectField."</div>";
		$fieldName .= "</div>";
 		$fieldName .= "</div>";
 		
 		return $fieldName;
 	}		
 	
	 	public function addFieldJavaScripts()
	 	{ 		 		
	 		$retVal = "<script type='text/javascript' language='javascript'>";
	 		$retVal .= "function validateAddField() { ";
			$retVal .= "var MySuccess = true; ";
			$retVal .= "var MyDiv = document.getElementById('addFieldErrorCodes');";
			$retVal .= "MyDiv.style.display = 'none'; ";
			$retVal .= "MyDiv.innerHTML = 'Please fix the following errors and then try again:<BR>'; ";
			$retVal .= " var sel = document.getElementById(\"field-name\"); ";
			$retVal .= "if(new String(sel.value).length < 1) ";
			$retVal .= "{";
			$retVal .= "MyDiv.innerHTML = MyDiv.innerHTML + \"Please enter a name for this field.<BR>\"; ";
			$retVal .= "MyDiv.style.display = 'block'; ";
			$retVal .= "MySuccess = false; ";
			$retVal .= "} ";
	
			$retVal .= " var sel = document.getElementById('year'); ";
			$retVal .= "if(new String(sel.value).length < 1) ";
			$retVal .= "{";
			$retVal .= "MyDiv.innerHTML =  MyDiv.innerHTML + \"Please enter a year for this field.<BR>\"; ";
			$retVal .= "MyDiv.style.display = 'block'; ";
			$retVal .= "MySuccess = false; ";
			$retVal .= "} ";
			$retVal .= "if( (parseInt(sel.value) >= 1989) && (parseInt(sel.value) <= ".date("Y").")) ";
			$retVal .= "{";
			$retVal .= " sel.value = parseInt(sel.value) ";
			$retVal .= "} else { ";
			$retVal .= "MyDiv.innerHTML =  MyDiv.innerHTML + \"Please enter a valid year for this field.<BR>\"; ";
			$retVal .= "MyDiv.style.display = 'block'; ";
			$retVal .= "MySuccess = false; ";
			$retVal .= "} ";
	
	
			$retVal .= " var sel = document.getElementById('station'); ";
			$retVal .= "if(sel.options[sel.selectedIndex].value < 1) ";
			$retVal .= "{";
			$retVal .= "MyDiv.innerHTML =  MyDiv.innerHTML + \"Please select a station for this field.<br>\"; ";
			$retVal .= "MyDiv.style.display = 'block'; ";
			$retVal .= "MySuccess = false; ";
			$retVal .= "} ";		
			
			$retVal .= " var sel = document.getElementById('crop');  ";
			$retVal .= "if(sel.options[sel.selectedIndex].value < 1) ";
			$retVal .= "{";
			$retVal .= "MyDiv.innerHTML =  MyDiv.innerHTML + \"Please select a Crop for this field.<br>\"; ";
			$retVal .= "MyDiv.style.display = 'block'; ";
			$retVal .= "MySuccess = false; ";
			$retVal .= "} ";
			
			$retVal .= " var sel = document.getElementById('soil-type'); ";
			$retVal .= "if(sel.options[sel.selectedIndex].value < 1) ";
			$retVal .= "{";
			$retVal .= "MyDiv.innerHTML =  MyDiv.innerHTML + \"Please select a Soil Type for this field.<br>\"; ";
			$retVal .= "MyDiv.style.display = 'block'; ";
			$retVal .= "MySuccess = false; ";
			$retVal .= "} ";
			
	 		$retVal .= "return MySuccess; ";
	 		$retVal .= "} " ;
	 		$retVal .= "</script>";
	 		return $retVal;
	 	}
	   	
	}

?>