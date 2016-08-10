<?php
	
	require_once('../irrigationScheduler.class.php');
	if(isset($_SESSION['field']))
	{
		$irrigationScheduler = new irrigationScheduler( $_SESSION['field']);
	}
	else
	{
		exit();
	}
	
	$cnt = date("z")+1;
	$currentValue = round($irrigationScheduler->row_tblIndividField[$cnt]['calculatedSoilWaterAvailibility']);
	$mad = $irrigationScheduler->row_FieldInfo['mad'];
	$chart = "";
	$chart .= "";
	$chart .= "<chart ";
	$chart .= "bgAlpha='0,0' ";
	$chart .= "showBorder='0' ";
	$chart .= "lowerLimit='0' ";
	$chart .= "upperLimit='100' ";
	$chart .= "lowerLimitDisplay='Dead' ";
	$chart .= "upperLimitDisplay='Full' ";
	$chart .= "gaugeFillColor='003c69'  ";
	$chart .= "numberSuffix='%' ";
	$chart .= "tickValueDistance='2' ";
	$chart .= "showValue='1'";
	$chart .= ">";
	$chart .= "<value>$currentValue</value>";
	$chart .= "</chart>";
	echo $chart; 
 ?>