<?php
	require_once 'jpgraph.php';
	require_once 'jpgraph_line.php';
	require_once 'jpgraph_date.php';
	require_once 'jpgraph_utils.inc.php';
	require_once 'jpgraph_scatter.php';
	require_once 'jpgraph_bar.php';
	require_once "/var/www/html/ism/ism.constants.php";
	include_once  '/var/www/html/ism/irrigationScheduler.class.php';
	include_once  '/var/www/html/ism/databaseManager.class.php';
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
if (!function_exists("dayofyear2date")) {	
	function dayofyear2date( $tDay, $tFormat = 'd-m-Y', $year ) { 
	    $day = intval( $tDay ); 
	    $day = ( $day == 0 ) ? $day : $day - 1; 
	    $offset = intval( intval( $tDay ) * 86400 ); 
	    $str = date( $tFormat, strtotime( 'Jan 1, ' . $year ) + $offset ); 
	    return( $str ); 
	} 
}

if(!isset($_SESSION['exportName']))
{
	$soilWaterChart = new SoilWaterChart();
}

class SoilWaterChart
{
	private $exportName;
	function __construct()
	{
		$this->stroke();
	}
	public function stroke()
	{
		global $database;
		global $irrigationScheduler;
		if(isset($_SESSION['maxy']))
			$maxY = $_SESSION['maxy'];
		else
			$maxY = 20;
		$maxY2 = 1;
		
		$colname_FieldInfo = "-1";
		if (isset($_SESSION['field'])) {
		  $colname_FieldInfo = $_SESSION['field'];
		  //echo "YES";
		}
		
		$colname_AdvancedFieldInfo = "-1";
		if (isset($_SESSION['field'])) {
		  $colname_AdvancedFieldInfo = $_SESSION['field'];
		}
		//die("FI: ".$colname_FieldInfo."<br/>");
		//echo date("H:i:s");
		$query_FieldInfo = sprintf("SELECT * FROM irrigation.tblfield WHERE fieldID = %s", GetSQLValueString($colname_FieldInfo, "int"));
		//echo date("H:i:s");

		$FieldInfo = $database->query($query_FieldInfo);
		$row_FieldInfo = mysql_fetch_assoc($FieldInfo);
		$plantDate = $row_FieldInfo['plantDate'];
		$seasonEndDate = $row_FieldInfo['seasonEndDate'];
		
		$query_AdvancedFieldInfo = sprintf("SELECT * FROM irrigation.tblindividfield WHERE fieldID = %s and status = 0  and DOY >= $plantDate  and doy <= $seasonEndDate ORDER BY doy ", GetSQLValueString($colname_AdvancedFieldInfo, "int"));
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
		
		$forageArray = array();
		$forageCut = 0;
		$debug = false;
		for($cnt = $startCnt; $cnt < $endCount; $cnt++)
		{
			$thisRow = mysql_fetch_assoc($AdvancedFieldInfo);
			//if($thisRow['foragecutting'] == 1)
			//{
				//$tL = new PlotLine(VERTICAL,$cnt, 'black', 1);
				//$forageArray[] = $tL;
				//unset ($tL);
			//}
			if(($thisRow['doy'] >= $chartStartDate) && (dayofyear2date($thisRow['doy'],"Y-m-d",$row_FieldInfo['year']) < date("Y-m-d")) )
			{
				$labels[$tCnt] = date("m/d",strtotime(dayofyear2date($thisRow['doy'],'M d',$row_FieldInfo['year']).", ".$row_FieldInfo['year']));
				$fcWater[$tCnt] = $thisRow['waterStorageAtFieldCapacity'];
				$swc[$tCnt] = $thisRow['currentSoilProfileWaterStorage'];
				//echo $swc[$tCnt]."<br/>";
				$forecastfcWater[$tCnt] = "";
				$forecastswc[$tCnt] = "";
				if($debug)
					echo "currentSoilProfileWaterStorage: ".$swc[$tCnt]."<BR>";
				//Don't show rain or irrigation if it is 0
				if($thisRow['rain'] == 0)
				{
					$rain[$tCnt] = "";
					$forecastrain[$tCnt] = "";
				}
				else
				{
					while($thisRow['rain'] > $maxY2)
						$maxY2 += 1;
					$rain[$tCnt] = $thisRow['rain'];
					$forecastrain[$tCnt] = "";
				}
				if($thisRow['irrig'] == 0)
				{
					$irrig[$tCnt] = "";
					$forecastirrig[$tCnt] = "";
				}
				else
				{
					while($thisRow['irrig'] > $maxY2)
						$maxY2 += 1;
					$irrig[$tCnt] = $thisRow['irrig'];
					$forecastirrig[$tCnt] = "";
				}
				if($thisRow['foragecutting'] == 0)
				{
					$forageArray[$tCnt] = "";
				}
				else
				{
					$forageCut++;
					$forageArray[$tCnt] = $thisRow['waterStorageAtFieldCapacity'];
				}
				$mad[$tCnt] = $thisRow['waterStorageAtMad'];
				$wpWater[$tCnt] = $thisRow['waterStorageAtPermanentWiltingPoint'];
				$forecastmad[$tCnt] = "";
				$forecastwpWater[$tCnt] = "";
				$tCnt ++;
			}
			elseif($row_FieldInfo['useNDFDforecast'] == 1 && ($thisRow['doy'] >= $chartStartDate) && (dayofyear2date($thisRow['doy'],"Y-m-d",$row_FieldInfo['year']) <= date("Y-m-d",strtotime("+6 days") )) )
			{
				$labels[$tCnt] = date("m/d",strtotime(dayofyear2date($thisRow['doy'],'M d',$row_FieldInfo['year']).", ".$row_FieldInfo['year']));
				$forecastfcWater[$tCnt] = $thisRow['waterStorageAtFieldCapacity'];
				$forecastswc[$tCnt] = $thisRow['currentSoilProfileWaterStorage'];
				if(dayofyear2date($thisRow['doy'],"Y-m-d",$row_FieldInfo['year']) == date("Y-m-d"))
				{
					$fcWater[$tCnt] = $thisRow['waterStorageAtFieldCapacity'];
					$swc[$tCnt] = $thisRow['currentSoilProfileWaterStorage'];
				}
				else
				{
					$fcWater[$tCnt] = "";
					$swc[$tCnt] = "";
				}
				if($debug)
					echo "currentSoilProfileWaterStorage: ".$swc[$tCnt]."<BR>";
				//Don't show rain or irrigation if it is 0
				if($thisRow['rain'] == 0)
				{
					$forecastrain[$tCnt] = "";
					$rain[$tCnt] = "";
				}
				else
				{
					$forecastrain[$tCnt] = $thisRow['rain'];
					$rain[$tCnt] = "";
				}
				if($thisRow['irrig'] == 0)
				{
					$forecastirrig[$tCnt] = "";
					$irrig[$tCnt] = "";
				}
				else
				{
					$forecastirrig[$tCnt] = $thisRow['irrig'];
					$irrig[$tCnt] = "";
				}
				$forecastmad[$tCnt] = $thisRow['waterStorageAtMad'];
				$forecastwpWater[$tCnt] = $thisRow['waterStorageAtPermanentWiltingPoint'];
				if(dayofyear2date($thisRow['doy'],"Y-m-d",$row_FieldInfo['year']) == date("Y-m-d"))
				{
					$mad[$tCnt] = $thisRow['waterStorageAtMad'];
					$wpWater[$tCnt] = $thisRow['waterStorageAtPermanentWiltingPoint'];
				}
				else
				{
					$mad[$tCnt] = "";
					$wpWater[$tCnt] = "";
				}
				$tCnt ++;
			}
			elseif(false)
			{
				$labels[$tCnt] = date("m/d",strtotime(dayofyear2date($thisRow['doy'],'M d',$row_FieldInfo['year']).", ".$row_FieldInfo['year']));
				$forecastfcWater[$tCnt] = "";
				$forecastswc[$tCnt] = "";
				$fcWater[$tCnt] = "";
				$swc[$tCnt] = "";
				if($debug)
					echo "currentSoilProfileWaterStorage: ".$swc[$tCnt]."<BR>";
				//Don't show rain or irrigation if it is 0
					$forecastrain[$tCnt] = "";
					$rain[$tCnt] = "";
					$forecastirrig[$tCnt] = "";
					$irrig[$tCnt] = "";
				$forecastmad[$tCnt] = "";
				$forecastwpWater[$tCnt] = "";
				$mad[$tCnt] = "";
				$wpWater[$tCnt] = "";
				$tCnt ++;
			}	
		}
		
		
		
		if(
		isset($_SESSION['exportName'])
		|| $irrigationScheduler->session->isMobileDevice > 0  )
		{
			if(isset($_SESSION['viewportwidth']) && $_SESSION['viewportwidth'] <= 400 && $_SESSION['viewportwidth'] > 312)
			{
				$xsize= $_SESSION['viewportwidth']-8;
				$ysize= $_SESSION['viewportwidth']-8;
			}
			else
			{
				$xsize=312;
				$ysize=312;
			}
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
		//For second Y axis
		//$graph->SetYScale(0,'int');
		//$graph->ynaxis[0]->scale->SetAutoMax($maxY2);
		$graph->SetFrame(true);
		$graph->SetBox(true);
		$graph->SetMarginColor('#e7e9ea');
		
		$graph->title->SetFont ( FF_FONT1, FS_BOLD );
		$graph->yaxis->title->SetFont(FF_FONT1,FS_BOLD); 
		$graph->xaxis->title->SetFont(FF_FONT1,FS_BOLD); 
		//	$irrigationScheduler = new irrigationScheduler( $_SESSION['field']);

		$graph->title->Set(" Field Soil Water Content, Rain & Irrigation");
		if(isset($_SESSION['exportName']) || $irrigationScheduler->session->isMobileDevice >0 )
		{
			$graph->img->SetMargin (35,20,0,75 );
		}
		else
		{
			$graph->img->SetMargin (45,20,0,75 );
			//$graph->xaxis->SetTitle(  'Date','center');
			//$graph->xaxis->SetTitlemargin ( 20 );
		}
		$graph->img->SetAntiAliasing(false);
		
		$graph->xgrid->Show();
		$graph->xgrid->SetLineStyle("solid");
		$graph->xgrid->SetColor('#E3E3E3');
		
		$graph->xaxis->SetPos ( "min" );
		
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
		
		
		$graph->yaxis->SetTitleMargin(20);
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
		
		
		
		$p1 = new LinePlot($mad);
		$graph->Add($p1);
		$p1->SetWeight(1); 
		$p1->SetColor("#981e32");
		//$p1->SetFillGradient('#981e32@0.9','#981e32');
		$p1->SetFillColor("#981e32@0.9");
		$p1->SetStyle($whichStyle);
		unset($p1);
		
		if($row_FieldInfo['useNDFDforecast'] == 1)
		{
			$p1 = new LinePlot($forecastmad);
			$graph->Add($p1);
			$p1->SetWeight(1); 
			$p1->SetColor("#981e32");
			//$p1->SetFillGradient('#981e32@0.9','#981e32');
			$p1->SetFillColor("#981e32@0.9");
			$p1->SetStyle("dotted");
			unset($p1);
		}
		
		$p1 = new LinePlot($swc);
		$graph->Add($p1);
		$p1->SetWeight(1); 
		$p1->SetColor("#3cb6ce");
		$p1->SetFillColor("#3cb6ce@0.9");
		//$p1->SetLegend("Soil Water");
		unset($p1);
		
		if($row_FieldInfo['useNDFDforecast'] == 1)
		{
			$p1 = new LinePlot($forecastswc);
			$graph->Add($p1);
			$p1->SetWeight(1); 
			$p1->SetColor("#3cb6ce");
			$p1->SetFillColor("#3cb6ce@0.9");
			$p1->SetStyle("dotted");
			unset($p1);
		}
		
		$p1 = new LinePlot($fcWater);
		$graph->Add($p1);
		$p1->SetWeight(1); 
		$p1->SetColor("#b6bf00");
		$p1->SetFillColor("#b6bf00@0.9");
		//$p1->SetLegend("Field Capacity");
		unset($p1);
		
		if($row_FieldInfo['useNDFDforecast'] == 1)
		{
			$p1 = new LinePlot($forecastfcWater);
			$graph->Add($p1);
			$p1->SetWeight(1); 
			$p1->SetColor("#b6bf00");
			$p1->SetFillColor("#b6bf00@0.9");
			$p1->SetStyle("dotted");
			unset($p1);
		}
		
		$p1 = new LinePlot($fcWater);
		$graph->Add($p1);
		$p1->SetWeight(1); 
		$p1->SetColor("#b6bf00");
		//$p1->SetFillColor("#b6bf00@0.9");
		$p1->SetLegend("Full");
		unset($p1);
		
		$p1 = new LinePlot($swc);
		$graph->Add($p1);
		$p1->SetWeight(1); 
		$p1->SetColor("#3cb6ce");
		//$p1->SetFillColor("#3cb6ce@0.9");
		$p1->SetLegend("Soil Water");
		unset($p1);
		
		
		
		$p1 = new LinePlot($mad);
		$graph->Add($p1);
		$p1->SetWeight(1); 
		$p1->SetColor("#981e32");
		//$p1->SetStyle($whichStyle);
		$p1->SetLegend("First Stress");
		unset($p1);
		
		
		$p1 = new LinePlot($wpWater);
		$graph->Add($p1);
		$p1->SetWeight(1); 
		$p1->SetColor("#452325");
		$p1->SetFillColor("#452325@0.9");
		//$p1->SetLegend("Wilting Point");
		//$p1->SetStyle("dotted");
		unset($p1);
		
		
		if($row_FieldInfo['useNDFDforecast'] == 1)
		{
			$p1 = new LinePlot($forecastwpWater);
			$graph->Add($p1);
			$p1->SetWeight(1); 
			$p1->SetColor("#452325");
			//$p1->SetFillGradient('#981e32@0.9','#981e32');
			$p1->SetFillColor("#452325@0.9");
			$p1->SetStyle("dotted");
			unset($p1);
		}
		
		$p1 = new LinePlot($wpWater);
		$graph->Add($p1);
		$p1->SetWeight(1); 
		$p1->SetColor("#452325");
		//$p1->SetFillColor("#452325@0.9");
		$p1->SetLegend("Empty/Dead");
		//$p1->SetStyle("dotted");
		unset($p1);
		
		
		$s1 = new ScatterPlot($irrig);
		
		//$graph->AddY(0,$s1);
		$graph->Add($s1);
		$s1->SetWeight(1); 
		$s1->mark->SetFillColor("green");
		$s1->SetLegend("Irrigation");
		unset($s1);
		
		
		if($row_FieldInfo['useNDFDforecast'] == 1)
		{
			$s1 = new ScatterPlot($forecastirrig);
			
			$graph->Add($s1);
			$s1->SetWeight(1); 
			$s1->mark->SetFillColor("green");
			//$s1->SetLegend("Irrigation");
			unset($s1);
		}
		
		$s2 = new ScatterPlot($rain);
		//$graph->AddY(0,$s2);
		$graph->Add($s2);
		$s2->SetWeight(1); 
		$s2->mark->SetFillColor("blue");
		$s2->SetLegend("Rain");
		unset($s2);
		
		if($row_FieldInfo['useNDFDforecast'] == 1)
		{
			$s2 = new ScatterPlot($forecastrain);
			$graph->Add($s2);
			$s2->SetWeight(1); 
			$s2->mark->SetFillColor("blue");
			//$s2->SetLegend("Forecast Rain");
			unset($s2);
		}
		
		if($forageCut > 0)
		{
			$s1 = new ScatterPlot($forageArray);
			
			$graph->Add($s1);
			$s1->mark->SetType(MARK_FLASH);
			//$s1->mark->SetType(MARK_DIAMOND);
			$s1->mark->SetFillColor("black");
			$s1->mark->SetWidth(10);
			//$s1->SetLegend("Forage Cut");
			unset($s1);

/*
			//  Could add text to the chart
			$txt = new Text();
			//$txt->SetFont(FF_ARIAL,FS_NORMAL,10);
			$txt->Set("Diamond is station installation date.\nNo data before that.");
			$txt->SetParagraphAlign('left');
			$txt->SetPos(0.75,0.08,'right');
			//$txt->SetBox('lightyellow');
			//$txt->SetShadow();
			$graph->Add($txt); 
			
			$s1 = new ScatterPlot($forageArray);
			$graph->Add($s1);
			$s1->mark->SetType(MARK_DIAMOND);
			$s1->mark->SetFillColor("black");
			$s1->mark->SetWidth(10);
			unset($s1);
			*/

		}
		
		/*
		$p1 = new LinePlot($forecastwpWater);
		$graph->Add($p1);
		$p1->SetWeight(1); 
		$p1->SetColor("#452325");
		$p1->SetFillColor("#452325@0.9");
		$p1->SetLegend("Forecast WP");
		$p1->SetStyle("dotted");
		unset($p1);
		*/
		$graph->yaxis->scale->SetAutoMax($maxY);
		//$graph->yaxis->scale->SetAutoMin(0);
		
		
		$graph->legend->SetFrameWeight(0);
		//$graph->legend->SetLayout(LEGEND_HOR); 
		if(isset($_SESSION['exportName']) || $irrigationScheduler->session->isMobileDevice >0)
		{
		$graph->legend->SetColumns(3);
		}
		else
		{
		$graph->legend->SetColumns(7);
		}
		$graph->legend->Pos(0.5,0.95,"center","bottom");
		$graph->legend->SetFillColor('#e7e9ea');
		$graph->legend->SetShadow(false);
		
		$graph->footer->left->Set("Dotted lines indicate forecast values."); //Source: WSU AgWeatherNet (weather.wsu.edu)"); //.PHP_EOL.date("D M j, Y")." at ".date("g:i a"));
		$graph->footer->left->SetColor("#981e32"); 
		
		if(isset($_SESSION['exportName']))
		{
			$graph->Stroke ($_SESSION['exportName']);
		}
		else
		{
			$graph->Stroke ();
		}
		return (True);
	}
	
}


?>


