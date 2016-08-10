<?php
	require_once 'jpgraph.php';
	require_once 'jpgraph_line.php';
	require_once 'jpgraph_date.php';
	require_once 'jpgraph_utils.inc.php';
	require_once 'jpgraph_scatter.php';
	require_once 'jpgraph_bar.php';
	require_once "../ism.constants.php";
	include_once  '../irrigationScheduler.class.php';
	include_once  '../databaseManager.class.php';
	$irrigationScheduler = new irrigationScheduler( $_SESSION['field']);
	$database = new databaseManager();


if (!function_exists("GetSQLValueString")) {
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  if (PHP_VERSION < 6) {
    $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
  }

  $theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? doubleval($theValue) : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}
}

function dayofyear2date( $tDay, $tFormat = 'd-m-Y', $year ) { 
    $day = intval( $tDay ); 
    $day = ( $day == 0 ) ? $day : $day - 1; 
    $offset = intval( intval( $tDay ) * 86400 ); 
    $str = date( $tFormat, strtotime( 'Jan 1, ' . $year ) + $offset ); 
    return( $str ); 
} 

//global $irrigationScheduler->session;

if(isset($_SESSION['maxy']))
	$maxY = $_SESSION['maxy'];
else
	$maxY = 20;

$colname_FieldInfo = "-1";
if (isset($_SESSION['field'])) {
  $colname_FieldInfo = $_SESSION['field'];
  //echo "YES";
}
else
{
	//echo "Is Not Set";
}

$colname_AdvancedFieldInfo = "-1";
if (isset($_SESSION['field'])) {
  $colname_AdvancedFieldInfo = $_SESSION['field'];
}
//die("FI: ".$colname_FieldInfo."<br/>");
$query_FieldInfo = sprintf("SELECT * FROM irrigation.tblfield WHERE fieldID = %s", GetSQLValueString($colname_FieldInfo, "int"));
//die("Text " . $query_FieldInfo);
$FieldInfo = $database->query($query_FieldInfo);
$row_FieldInfo = mysql_fetch_assoc($FieldInfo);
$plantDate = $row_FieldInfo['plantDate'];
		$seasonEndDate = $row_FieldInfo['seasonEndDate'];

$query_AdvancedFieldInfo = sprintf("SELECT * FROM irrigation.tblindividfield WHERE fieldID = %s and status = 0 and DOY >= $plantDate  and doy <= $seasonEndDate  ORDER BY doy ", GetSQLValueString($colname_AdvancedFieldInfo, "int"));
$AdvancedFieldInfo = $database->query($query_AdvancedFieldInfo);
$totalRows_AdvancedFieldInfo = mysql_num_rows($AdvancedFieldInfo);

$startCnt = 0;
$endCount = $totalRows_AdvancedFieldInfo;
$tCnt = 0;
if(isset($_SESSION['CHARTSTARTDATE']))
{
	$chartStartDate = $_SESSION['CHARTSTARTDATE'];
}
else
{
	$chartStartDate = $AdvancedFieldInfo['plantDate'];
}
if(isset($_SESSION['CHARTENDDATE']))
{
	$chartEndDate =  $_SESSION['CHARTENDDATE'];
}
else
{
	$chartEndDate = $AdvancedFieldInfo['seasonEndDate'];
}

$debug = false;
for($cnt = $startCnt; $cnt < $endCount; $cnt++)
{
	$thisRow = mysql_fetch_assoc($AdvancedFieldInfo);
	if(($thisRow['doy'] >= $chartStartDate) && (dayofyear2date($thisRow['doy'],"Y-m-d",$row_FieldInfo['year']) <= date("Y-m-d")) )
	{
		$labels[$tCnt] = date("m/d",strtotime(dayofyear2date($thisRow['doy'],'M d',$row_FieldInfo['year']).", ".$row_FieldInfo['year']));
		$fcWater[$tCnt] = $thisRow['waterStorageAtFieldCapacity'];
		$swc[$tCnt] = $thisRow['currentSoilProfileWaterStorage'];
		if($debug)
			echo "currentSoilProfileWaterStorage: ".$swc[$tCnt]."<BR>";
		//Don't show rain or irrigation if it is 0
		if($thisRow['etc'] == 0)
		{
			$etc[$tCnt] = 0;
		}
		else
		{
			$etc[$tCnt] = $thisRow['etc'];
		}
		if($thisRow['irrig'] == 0)
		{
			$irrig[$tCnt] = "";
		}
		else
		{
			$irrig[$tCnt] = $thisRow['irrig'];
		}
		$mad[$tCnt] = $thisRow['waterStorageAtMad'];
		$wpWater[$tCnt] = $thisRow['waterStorageAtPermanentWiltingPoint'];
		$tCnt ++;
	}
}





if($irrigationScheduler->session->isMobileDevice > 0)
{
	$xsize=312;
	$ysize=312;
	$whichStyle = "solid";
}
else
{
	$xsize=700;
	$ysize=420;
	$whichStyle = "dotted";
}
$graph = new Graph ( $xsize, $ysize);


$graph->SetScale('textlin'); //,0,0,$xmin,$xmax);
$graph->SetFrame(true);
$graph->SetBox(true);
$graph->SetMarginColor('#e7e9ea');

$graph->title->SetFont ( FF_FONT1, FS_BOLD );
$graph->yaxis->title->SetFont(FF_FONT1,FS_BOLD); 
$graph->xaxis->title->SetFont(FF_FONT1,FS_BOLD); 

$graph->title->Set("Daily Crop Water Use");
$graph->img->SetMargin (45,20,0,50 );
$graph->img->SetAntiAliasing(false);

$graph->xgrid->Show();
$graph->xgrid->SetLineStyle("solid");
$graph->xgrid->SetColor('#E3E3E3');

$graph->xaxis->SetPos ( "min" );
//$graph->xaxis->SetTitlemargin ( 75 );
//$graph->xaxis->SetTitle(  'Date','center');
$graph->xaxis->SetTickLabels ( $labels );
if(round(count($labels)/7) > 0)
{
	$graph->xaxis->SetTextTickInterval( round(count($labels)/7) ); 
}
else
{
	$graph->xaxis->SetTextTickInterval( 1 ); 
}
$graph->xaxis->SetLabelAngle ( 0 );


$graph->yaxis->SetTitleMargin(32);
$graph->yaxis->title->Set('Inches');
$graph->yaxis->SetLabelAngle ( 0 );

$graph->ygrid->SetFill(false);

//Setup y axis
$graph->yaxis->SetColor("black"); 
$graph->yaxis->SetWeight(1) ;
$graph->yaxis->HideZeroLabel();
$graph->yaxis->HideLine(false);
$graph->yaxis->HideTicks(false,false);

//Setup x axis
$graph->xaxis->SetColor("black"); 
$graph->xaxis->SetWeight(1) ;
/*
$p1 = new LinePlot($fcWater);
$graph->Add($p1);
$p1->SetWeight(1); 
$p1->SetColor("black");
$p1->SetLegend("Field Capacity");
unset($p1);


$p1 = new LinePlot($swc);
$graph->Add($p1);
$p1->SetWeight(1); 
$p1->SetColor("orange");
$p1->SetLegend("Soil Water");
unset($p1);


$p1 = new LinePlot($mad);
$graph->Add($p1);
$p1->SetWeight(1); 
$p1->SetColor("red");
$p1->SetLegend("MAD");
$p1->SetStyle($whichStyle);
unset($p1);


$p1 = new LinePlot($wpWater);
$graph->Add($p1);
$p1->SetWeight(1); 
$p1->SetColor("brown");
$p1->SetLegend("Wilting Point");
//$p1->SetStyle("dotted");
unset($p1);


$s1 = new ScatterPlot($irrig);

$graph->Add($s1);
$s1->SetWeight(1); 
$s1->mark->SetFillColor("green");
$s1->SetLegend("Irrigation");
unset($s1);
*/

$s2 = new LinePlot($etc);
$graph->Add($s2);
$s2->SetWeight(1); 
$s2->mark->SetFillColor("blue");
//$s2->SetLegend("ETc");
unset($s2);


//$graph->yaxis->scale->SetAutoMax($maxY);
$graph->yaxis->SetLabelFormatString("%3.2f");

$graph->legend->SetFrameWeight(0);
//$graph->legend->SetLayout(LEGEND_HOR); 
$graph->legend->Pos(0.5,0.95,"center","bottom");
$graph->legend->SetFillColor('#e7e9ea');
//$graph->legend->SetShadow();

$graph->footer->left->Set("Source: WSU AgWeatherNet (weather.wsu.edu)"); //.PHP_EOL.date("D M j, Y")." at ".date("g:i a"));
$graph->footer->left->SetColor("#981e32"); 

if(!$debug)
	$graph->Stroke ();
return (True);

?>


