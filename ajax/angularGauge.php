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
	if(isset($irrigationScheduler->row_tblIndividField[$cnt]['calculatedSoilWaterAvailibility']))
		$currentValue = round($irrigationScheduler->row_tblIndividField[$cnt]['calculatedSoilWaterAvailibility']);
	else
		$currentValue = round($irrigationScheduler->row_tblIndividField[date("z")]['calculatedSoilWaterAvailibility']);
	
	$mad = 100-$irrigationScheduler->row_FieldInfo['mad']*.85;
	
	$chart = "";
	$chart .= "";
	$chart .= "<chart ";
	$chart .= " showGaugeBorder='0' ";
	$chart .= "bgAlpha='0,0' ";
	$chart .= "showBorder='0' ";
	$chart .= "lowerLimit='0' ";
	$chart .= "upperLimit='100' ";
	$chart .= "lowerLimitDisplay='Dead' ";
	$chart .= "upperLimitDisplay='Full' ";
	$chart .= "gaugeStartAngle='-90' ";
	$chart .= "gaugeEndAngle='90' ";
	$chart .= "palette='1' ";
	$chart .= "numberSuffix='%' ";
	$chart .= "autoAlignTickValues ='1' ";
	$chart .= "showValue='1' ";
	$chart .= "animation='0' ";
	$chart .= ">";
	
	
	$chart .= "<colorRange>";
	
	$chart .= "<color minValue='0' maxValue='".(1/50*$mad)."' code='000000'/>";
	$chart .= "<color minValue='".(1/50*$mad)."' maxValue='".(2/50*$mad)."' code='0C0701'/>";
	$chart .= "<color minValue='".(2/50*$mad)."' maxValue='".(3/50*$mad)."' code='180E03'/>";
	$chart .= "<color minValue='".(3/50*$mad)."' maxValue='".(4/50*$mad)."' code='241605'/>";
	$chart .= "<color minValue='".(4/50*$mad)."' maxValue='".(5/50*$mad)."' code='311D07'/>";
	$chart .= "<color minValue='".(5/50*$mad)."' maxValue='".(6/50*$mad)."' code='3D2509'/>";
	$chart .= "<color minValue='".(6/50*$mad)."' maxValue='".(7/50*$mad)."' code='492C0A'/>";
	$chart .= "<color minValue='".(7/50*$mad)."' maxValue='".(8/50*$mad)."' code='56330C'/>";
	$chart .= "<color minValue='".(8/50*$mad)."' maxValue='".(9/50*$mad)."' code='623B0E'/>";
	$chart .= "<color minValue='".(9/50*$mad)."' maxValue='".(10/50*$mad)."' code='6E4210'/>";
	$chart .= "<color minValue='".(10/50*$mad)."' maxValue='".(11/50*$mad)."' code='7B4A12'/>";
	$chart .= "<color minValue='".(11/50*$mad)."' maxValue='".(12/50*$mad)."' code='884210'/>";
	$chart .= "<color minValue='".(12/50*$mad)."' maxValue='".(13/50*$mad)."' code='953B0E'/>";
	$chart .= "<color minValue='".(13/50*$mad)."' maxValue='".(14/50*$mad)."' code='A2330C'/>";
	$chart .= "<color minValue='".(14/50*$mad)."' maxValue='".(15/50*$mad)."' code='AF2C0A'/>";
	$chart .= "<color minValue='".(15/50*$mad)."' maxValue='".(16/50*$mad)."' code='BD2509'/>";
	$chart .= "<color minValue='".(16/50*$mad)."' maxValue='".(17/50*$mad)."' code='CA1D07'/>";
	$chart .= "<color minValue='".(17/50*$mad)."' maxValue='".(18/50*$mad)."' code='D71605'/>";
	$chart .= "<color minValue='".(18/50*$mad)."' maxValue='".(19/50*$mad)."' code='E40E03'/>";
	$chart .= "<color minValue='".(19/50*$mad)."' maxValue='".(20/50*$mad)."' code='F10701'/>";
	$chart .= "<color minValue='".(20/50*$mad)."' maxValue='".(21/50*$mad)."' code='FF1000'/>";
	$chart .= "<color minValue='".(21/50*$mad)."' maxValue='".(22/50*$mad)."' code='FF2100'/>";
	$chart .= "<color minValue='".(22/50*$mad)."' maxValue='".(23/50*$mad)."' code='FF3100'/>";
	$chart .= "<color minValue='".(23/50*$mad)."' maxValue='".(24/50*$mad)."' code='FF4200'/>";
	$chart .= "<color minValue='".(24/50*$mad)."' maxValue='".(25/50*$mad)."' code='FF5200'/>";
	$chart .= "<color minValue='".(25/50*$mad)."' maxValue='".(26/50*$mad)."' code='FF6300'/>";
	$chart .= "<color minValue='".(26/50*$mad)."' maxValue='".(27/50*$mad)."' code='FF7300'/>";
	$chart .= "<color minValue='".(27/50*$mad)."' maxValue='".(28/50*$mad)."' code='FF8400'/>";
	$chart .= "<color minValue='".(28/50*$mad)."' maxValue='".(29/50*$mad)."' code='FF9400'/>";
	$chart .= "<color minValue='".(29/50*$mad)."' maxValue='".(30/50*$mad)."' code='FFA500'/>";

	$chart .= "<color minValue='".(30/50*$mad)."' maxValue='".(31/50*$mad)."' code='FFAE00'/>";
	$chart .= "<color minValue='".(31/50*$mad)."' maxValue='".(32/50*$mad)."' code='FFB700'/>";
	$chart .= "<color minValue='".(32/50*$mad)."' maxValue='".(33/50*$mad)."' code='FFC000'/>";
	$chart .= "<color minValue='".(33/50*$mad)."' maxValue='".(34/50*$mad)."' code='FFC900'/>";
	$chart .= "<color minValue='".(34/50*$mad)."' maxValue='".(35/50*$mad)."' code='FFD200'/>";
	$chart .= "<color minValue='".(35/50*$mad)."' maxValue='".(36/50*$mad)."' code='FFDB00'/>";
	$chart .= "<color minValue='".(36/50*$mad)."' maxValue='".(37/50*$mad)."' code='FFE400'/>";
	$chart .= "<color minValue='".(37/50*$mad)."' maxValue='".(38/50*$mad)."' code='FFED00'/>";
	$chart .= "<color minValue='".(38/50*$mad)."' maxValue='".(39/50*$mad)."' code='FFF600'/>";
	$chart .= "<color minValue='".(39/50*$mad)."' maxValue='".(40/50*$mad)."' code='FFFF00'/>";
	
	$chart .= "<color minValue='".(40/50*$mad)."' maxValue='".(41/50*$mad)."' code='E5F400'/>";
	$chart .= "<color minValue='".(41/50*$mad)."' maxValue='".(42/50*$mad)."' code='CCEA00'/>";
	$chart .= "<color minValue='".(42/50*$mad)."' maxValue='".(43/50*$mad)."' code='B2E000'/>";
	$chart .= "<color minValue='".(43/50*$mad)."' maxValue='".(44/50*$mad)."' code='99D600'/>";
	$chart .= "<color minValue='".(44/50*$mad)."' maxValue='".(45/50*$mad)."' code='7FCC00'/>";
	$chart .= "<color minValue='".(45/50*$mad)."' maxValue='".(46/50*$mad)."' code='66C100'/>";
	$chart .= "<color minValue='".(46/50*$mad)."' maxValue='".(47/50*$mad)."' code='4CB700'/>";
	$chart .= "<color minValue='".(47/50*$mad)."' maxValue='".(48/50*$mad)."' code='33AD00'/>";
	$chart .= "<color minValue='".(48/50*$mad)."' maxValue='".(49/50*$mad)."' code='19A300'/>";
	$chart .= "<color minValue='".(49/50*$mad)."' maxValue='".(50/50*$mad)."' code='009900'/>";

	$chart .= "<color minValue='$mad' maxValue='100' code='009900'/>";
	$chart .= "</colorRange>";
	$chart .= "<dials>";
	$chart .= "<dial value='".$currentValue."' rearExtension='0' ";
	if($currentValue <= 50)
	{
		$chart .= " valueY='90' ";
	}
	else
	{
		$chart .= " valueY='120' ";
	}
	$chart .= "/>";
	$chart .= "</dials>";

	
	$chart .= "</chart>";
	echo $chart; 
 ?>