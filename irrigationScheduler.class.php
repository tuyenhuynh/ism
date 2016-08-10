<?php
	require_once('sessionManager.class.php');
	require_once('html/htmlFactory.class.php');
	require_once("validation.class.php");
	require_once("iform.class.php");


	class irrigationScheduler
	{
		public $session;
		public $htmlFactory;
		public $thisID = "12345";
		public $row_tblIndividField = array();
		public $arrStationMetaData = array ();
		public $FieldInfo = array();
		public $AdvancedFieldInfo = array();
		public $row_FieldInfo = array();
		public $row_CropInfo = array();
		public $partA = 0;
		public $partB = 0;


		function __construct($fieldID=-1, $updateFlag = 0)
		{
			$this->selectVariables($fieldID, $updateFlag);
		}
		
		public function selectVariables($fieldID = -1, $updateFlag = 0)
		{
			global $database;
			global $iform;
			$this->session = new sessionManager();
			$this->htmlFactory= new htmlFactory();
			$iform = new iForm();		
			$_SESSION['maxy'] = 0;	
			if(intval($this->htmlFactory->FieldID) > 0)
			{	
				
				//Get general field information
				$this->row_FieldInfo = $database->getFieldInfo($this->htmlFactory->FieldID);
				//Get crop information
				$this->row_CropInfo = $database->getCropInfo($this->row_FieldInfo['cropID']);
	
				$this->row_tblIndividField = $database->getDayInfo($this->htmlFactory->FieldID, $this->row_FieldInfo['year'], $this->row_FieldInfo['plantDate']);
				if(count($this->row_tblIndividField) == 0)
					$updateFlag++;
				if(isset($_SESSION['lastUpdate']))
				{
					$lastUpdated = $_SESSION['lastUpdate'];
				}
				
				//Rather than pull from crop defaults pull from tblfield so that user can edit the number of recovery or flat days
				if($this->row_FieldInfo['postCuttingFlatDays'] >= 0 && $this->row_FieldInfo['postCuttingRecoveryDays'] >= 0)
				{
					$this->partA = $this->row_FieldInfo['postCuttingFlatDays'];
					$this->partB = $this->row_FieldInfo['postCuttingRecoveryDays'];
				}
				else
				{
					$this->partA = $this->row_CropInfo['postCuttingFlatDays'];
					$this->partB = $this->row_CropInfo['postCuttingRecoveryDays'];
				}
	

				//if an update is forced, then update the value
				if($updateFlag > 0 || date("Y-m-d H:i:s",strtotime($lastUpdated)) < date("Y-m-d H:i:s",strtotime("-2 hours")))
				{
					
					$this->updateValues($fieldID, $updateFlag);
				}
			}
		}
		
		public function toHTML()
		{
			$myaction = "";
			if(isset($_REQUEST['action']))
			{
				$myaction = $_REQUEST['action'];
			}

			$publicActions = array('register','forgotname','forgotpass','privacy','about-us','contact-us');
			if($this->session->logged_in || (isset($_REQUEST['action']) && in_array($_REQUEST['action'],$publicActions)))
			{
				$retVal = $this->htmlFactory->displayPage();
			}
			else
			{
				$retVal = $this->htmlFactory->loginForm();
			}
			return $retVal;
		}

	/**
	 * This function will calculate Evapotranspiration reference (etr) and ET grass (eto)
	 * Modified by Troy Peters and Brent Etzel August, 2007 to the ASCE Stdzd Penman Monteith Equation
	 */
		public function ETCalc($tmax, $tmin, $tdew, $rs, $wr, $jd, $lat, $elv, $AnemomH, &$etr, &$eto)
		{
			// check for valid number of arguments
			if (func_num_args () != 11) {
				print ( "<br /><br />One or more function arguments is missing!<br />Please contact the AWN systems people.<br />" );
				trigger_error ( "One or more function arguments is missing", E_USER_ERROR );
				return (False);
			}
			
			// the below definitions come from the ETCalculator.java module
			// GRASS FAO24
			// ALFALFA KIMBERLY-PENNMAN 82
			// tp3 -> average air tmp for previous three days
			// tmax -> max average air tmp (deg F)
			// tmin -> min average air tmp (deg F)
			// tdew -> average dewpoint (deg F)
			// rs -> total solar radiation (W/m^2/day)
			// wr -> wind run (miles/day)
			// jd -> julian day of year
			// lat -> latitude
			// elv -> elevation (ft)
			// AnemomH -> elevation of anemometer (m)
			// P -> pressure (kPa)
			// D -> declination (for Ra calculation) radians, eq 27 Allen et al., 1989
			// WS -> sunset hr angle, radians, eq 29/30: Allen et al 1989
			// RA -> daily values of extraterrestrial radiation
			// Gsc -> 0.08202 MJ m-2 min-1 = 1367 W m-2; Allen et al 1989 (solar constant)
			// Rso -> clear sky radiation MJ m-2 day-1
			// sigma -> Stephen-Boltsmann constant 4.903x10-9 MJ m-2 day-1 K-4
			// TA -> avg of Tmax, Tmin
			// TmaxK -> max air T in Kelvin
			// TminK -> min air T in Kelvin
			// TmeanK -> avg air T in Kelvin
			// ED -> saturation vapor pressure @dewpt T (kPa)
			// alpha -> albedo, allen et al, Wright (1982)
			// RN -> net radiation (MJ m-2 day-1)
			// RNG -> Grass ref ET, note differences
			// G -> soil heat flux
			// cs -> general heat conductance for soil surface cs = 0.38 MJ m-2 day-1 C-1 for silt loam
			// ETAV -> sat VP @TA
			// LHVP -> patent heat of vaporization Allen et al 1989[35], Harris (1963)
			// gamma -> psychromatric constant (kPa C-1) eq [34]
			// DELTA -> slope of sat VP eq [33]
			// ETX -> sat VP @ Tmax
			// ETN -> satVP @Tmin
			// EAV -> avg sat VP @ ETX,ETN
			// BW -> Wright, 1982; Allen et al 1989 eq [13]; wind function coeff
			// AW -> Allen et al 1989; eq[14] wind function coeff
			// EA -> [eq 10] general Penman form alfalfa : wind/vapor term Allen et al 1989
			// EAG -> [eq 10} Allen et al 1989; general Penman Grass, wind/vapor term
			// ETR -> reference ET, general Penman
			// ETO -> ref ET Grass
			// grass or alfalfa ET differ by the way sat VP is handled
			// grass is SVP@TA
			// alfalfa is SVP@meanSVP@Tmax, SVP@Tmin
			
		
			// check for valid data, if not return na
			if (($rs > 59999) || ($wr > 9999) || ($tmax > 999) || ($tmin > 999) || ($tdew > 999)) {
				// bad data, return NA
				$eto = 'NA';
				$etr = 'NA';
				return;
			}
			
			// convert the input values to metric, etc.
			$rs *= 0.0036; /* convert Watts/M2/Day to MJ/M2/Day */
			$wr /= 53.6865; // miles/day to m/s
			//$tp3 = ($tp3 - 32) / 1.8;     // degree F -> degree C
			$tmax = ($tmax - 32) / 1.8; // degree F -> degree C
			$tmin = ($tmin - 32) / 1.8; // degree F -> degree C
			$tdew = ($tdew - 32) / 1.8; // degree F -> degree C
			$elv *= 0.3048; // feet to meters
			
		
			$J = $jd;
			
			// initialize values
			$ETR = 0;
			$ETO = 0;
			
			// check if latitude and elevation are there
			if ($lat == 0)
				$lat = 46.7;
			
			if ($elv == 0)
				$elv = 250;
			
			$PI = M_PI; // php constant
			$PHI = $lat * $PI / 180;
			
			// pressure
			$P = 101.3 * pow ( (293 - .0065 * $elv) / 293, 5.26 );
			
			// declination
			$D = 0.409 * sin ( 2 * $PI * $J / 365 - 1.39 );
			
			//
			$DR = 1 + 0.033 * cos ( 2 * $PI * $J / 365 );
			
			// sunset angle
			$WS = atan ( - (- tan ( $PHI ) * tan ( $D )) / sqrt ( - (- tan ( $PHI ) * tan ( $D )) * (- tan ( $PHI ) * tan ( $D )) + 1 ) ) + 2 * atan ( 1 );
			
			// daily radiation
			$RA = 37.586 * $DR * ($WS * sin ( $PHI ) * sin ( $D ) + cos ( $PHI ) * cos ( $D ) * sin ( $WS ));
			
			// clear sky radiation
			$RSO = $RA * (0.75 + 0.00002 * $elv);
			$Rns = 0.77 * $rs;
			
			$RsRso = $rs / $RSO;
			if ($RsRso < .3) {
				$RsRso = .3;
			}
			if ($RsRso > 1) {
				$RsRso = 1;
			}
			
			$Fcd = 1.35 * $RsRso - .35;
			if ($Fcd < .05) {
				$Fcd = .05;
			}
			
			if ($Fcd > 1) {
				$Fcd = 1;
			}
			
			// convert max and min temps to degree kelvin
			$TMAXK = $tmax + 273.16;
			$TMINK = $tmin + 273.16;
			
			// avg of max and min temps
			$TA = ($tmax + $tmin) / 2;
			
			// avg air temp in degree kelvin
			$TMEANK = $TA + 273.16;
			
			// saturation vapor pressure at dewpoint
			$ED = $this->CalcSVP ( $tdew );
			
			//     $ALPHA = 0.29 + 0.06 * sin(($J + 96) / 57.3);
			
		
			// net radiation
			$Rnl = 0.0000000049 * (pow ( $TMAXK, 4 ) + pow ( $TMINK, 4 )) / 2 * (0.34 - 0.14 * sqrt ( $ED )) * $Fcd;
			$RN = $Rns - $Rnl;
			
			// grass ref et
			$RNG = 0.75 * $rs - 0.000000004903 * (pow ( $TMEANK, 4 ) * (0.34 - 0.139 * pow ( $ED, .5 )) * (1.35 * $rs / $RSO - 0.35));
			
			// soil heat flux
			$G = 0;
			// sat VP @ TA
			$ETAV = $this->CalcSVP ( $TA );
			
			// patent heat of vaporiation
			$LHVP = 2.5002 - 0.002361 * $TA;
			
			// pychromatric constant (kPa C-1)
			$GAMMA = 0.000665 * $P;
			
			// slop of sat VP
			$DELTA = 4099 * (0.6108 * exp ( 17.27 * $TA / ($TA + 237.3) )) / (pow ( ($TA + 237.3), 2 ));
			
			// sat VP at tmax
			$ETX = $this->CalcSVP ( $tmax );
			
			// sat VP at tmin
			$ETN = $this->CalcSVP ( $tmin );
			
			// avg sta VP @ ETX, ETN
			$EAV = ($ETX + $ETN) / 2;
			
			$U2 = $wr * 4.87 / (log ( 67.8 * $AnemomH - 5.42 ));
			
			//Alfalfa Reference ET (ETr) Cn = 1600, Cd = 0.38
			$Numerator = 0.408 * $DELTA * ($RN - $G) + $GAMMA * 1600 / ($TA + 273) * $U2 * ($EAV - $ED);
			$Denominator = $DELTA + $GAMMA * (1 + 0.38 * $U2);
			$ETR = $Numerator / $Denominator;
			
			//Grass Reference ET (Eto) Cn = 900, Cd = 0.34
			$Numerator = 0.408 * $DELTA * ($RN - $G) + $GAMMA * 900 / ($TA + 273) * $U2 * ($EAV - $ED);
			$Denominator = $DELTA + $GAMMA * (1 + 0.34 * $U2);
			$ETO = $Numerator / $Denominator;
			/*   print( "\n"
			. "</pre>Numerator = $Numerator\n"
			. "</pre>Denominator = $Denominator\n"
			. "</pre>ETO = $ETO\n" );
			*/
			// make sure they are not negative
			//echo "This is a test $ETR<br/>";
			if ($ETR < 0)
				$ETR = 0;
			//else
				$etr = $ETR / 25.4; // convert to inches from mm
			
		
			if ($ETO < 0)
				$ETO = 0;
			//else
				$eto = $ETO / 25.4; // convert to inches from mm
		
		
		// *****************************
		// print out values for debugging
		// *****************************
		
		
		/*  print( "<pre>\n" );
			print( "*** INPUT VALUES (METRIC) ***\n"
			. "tmax = $tmax\n"
			. "tmin = $tmin\n"
			. "tdew = $tdew\n"
			. "rs = $rs\n"
			. "wr = $wr\n"
			. "jd = $jd\n"
			. "lat = $lat\n"
			. "elv = $elv\n"
			. "AnemomH = $AnemomH\n" );
		
		
			print( "\n*** CALC VALUES ***\n"
			. "PI = $PI\n"
			. "PHI = $PHI\n"
			. "P = $P\n"
			. "D = $D\n"
			. "DR = $DR\n"
			. "WS = $WS\n"
			. "RA = $RA\n"
			. "RSO = $RSO\n"
			. "TMAXK = $TMAXK\n"
			. "TMINK = $TMINK\n"
			. "TA = $TA\n"
			. "TMEANK = $TMEANK\n"
			. "ED = $ED\n"
			. "ALPHA = $ALPHA\n"
			. "RN = $RN\n"
			. "RNG = $RNG\n"
			. "G = $G\n"
			. "ETAV = $ETAV\n"
			. "LHVP = $LHVP\n"
			. "GAMMA = $GAMMA\n"
			. "DELTA = $DELTA\n"
			. "ETX = $ETX\n"
			. "ETN = $ETN\n"
			. "EAV = $EAV\n"
			. "EA = $EA\n"
			. "EAG = $EAG\n"
			. "ETR = $ETR\n"
			. "ETO = $ETO\n"
			. "eto final output = $eto\n"
			. "etr final output = $etr\n" );
			*/
		//  print( "</pre>\n" );
		//  $TempEtr = sprintf( "%2.2f", $etr );
		//  $TempEto = sprintf ("%2.2f", $eto );
		//  $RtrmString = $TempEtr . "," . $TempEto;
		//   return ($RtrmString);
		//  return (True);
		}

		public function ETHarg($jd, $tmax, $tmin, $lat) {
		//Written by Troy Peters March, 2013
		//Calculates grass reference ETo in mm using the Hargreaves temperature method then converts to alfala reference ETr in inches.
		//inputs are: jd is Day of Year. tmax, and tmin are in maximum and minimum temperature delivered in deg F.
		//latitude is in decimal degrees.
		
		    $tmax = ($tmax - 32) / 1.8;	//convert to deg C
		    $tmin = ($tmin - 32) / 1.8;
		    $tavg = ($tmax + $tmin) / 2;
		    $LAMBDA = 2.5 - 0.002361 * $tavg;
		    $lat = $lat * M_PI / 180;    //Convert to radians
		    $SmallDelta = 0.409 * sin (2 * M_PI / 365 * $jd - 1.39);
		    $omega = acos(-tan($lat) * tan($SmallDelta));
		    $dr = 1 + 0.033 * cos(2 * M_PI * $jd / 365);
		    $Ra = 37.586 * $dr * ($omega * sin ($lat) * sin ($SmallDelta) + cos ($lat) * cos ($SmallDelta) * sin ($omega));
		    $eto = 0.0023 * ($tavg + 17.8) * sqrt($tmax - $tmin) * $Ra / $LAMBDA;
		    $eto /= 25.4;	//convert back to inches
		    $etr = $eto * 1.2;	//use a constant of 1.2 for converting ETo to ETr.
		
			// *****************************
			// print out values for debugging
			// *****************************
		
		/*  print( "<pre>\n" );
			print( "*** INPUT VALUES (METRIC) ***\n"
			. "jd = $jd\n"
			. "tmax = $tmax\n"
			. "tmin = $tmin\n"
			. "tavg = $tavg\n"
			. "lat = $lat\n");
		
			print( "\n*** CALC VALUES ***\n"
			. "LAMBDA = $LAMBDA\n"
			. "SmallDelta = $SmallDelta\n"
			. "omega = $omega\n"
			. "dr = $dr\n"
			. "Ra = $Ra\n"
			. "ETO = $ETO\n"
			. "ETR final output = $ETR\n" );
				*/
			return $etr;
		}
		
		
		/**************************************
		 *    function CalcSVP( airTemp )
		 **************************************/
		public function CalcSVP($airTemp) {
			$svp = 0;
			$svp = .6108 * exp ( (17.27 * $airTemp) / ($airTemp + 237.3) );
			return $svp;
		
		} // eof CalcSVP


	public function updateValues($fieldID, $updateFlag)
	{
		global $database;
		$maxY = 0;
		$_SESSION['maxy'] = $maxY;
		$todaysDate = date("M d, Y");

		$groundWetted = $this->row_FieldInfo['groundWetted']/100;
		$numDays =  $this->row_FieldInfo['growthDeclineDate'] - $this->row_FieldInfo['growthMaxDate'];
		$rootNumDays = $this->row_FieldInfo['growthDeclineDate'] - $this->row_FieldInfo['plantDate'];
		$rootGrowthPerDay = ($this->row_FieldInfo['rz_val2'] - $this->row_FieldInfo['rz_val1'] ) / $rootNumDays;
		$rootDepth = $this->row_FieldInfo['rz_val1'];
		$kc1 = $this->row_FieldInfo['kc1'];
		$kc2 = $this->row_FieldInfo['kc2'];
		$kc3 = $this->row_FieldInfo['kc3'];
		//multipy by the percent of the ground wetted
		$soilCapacityField = $this->row_FieldInfo['soilFC'] * $groundWetted;
		$soilAvailableWaterContent = $this->row_FieldInfo['soilAWC'] * $groundWetted;
		$crop = $this->row_FieldInfo['cropID'];
		$mad = $this->row_FieldInfo['mad']/100;			
		$fieldYear = 	$this->row_FieldInfo['year'];
		$plantDate = $this->row_FieldInfo['plantDate']; //emergence
		$seasonEndDate = $this->row_FieldInfo['seasonEndDate']; //emergence
		
		$kcVal = $kc1;
	
		$etrArray = $this->getWeatherData($updateFlag);
		

		$lastForage = -1;


		// Main Loop.  Once for every day       //Remove <= to prevent adding of additional final day--find and fix this                  
		for($cnt = $plantDate; $cnt < $seasonEndDate; $cnt++)
		{
			
			if(isset($etrArray[$cnt]))
			{
				$currentDayID = "";
				if(isset($this->row_tblIndividField[$cnt]['individFieldID']))
					$currentDayID = $this->row_tblIndividField[$cnt]['individFieldID'];
        // load in background data
				$modifiedCurrentDay = 0;
				$msdPcnt = null;
				if(isset($this->row_tblIndividField[$cnt]['measdPcntAvail']))
					$msdPcnt = $this->row_tblIndividField[$cnt]['measdPcntAvail'];	
				$deepPercolationCurrentDay = 0;
				
				$fieldDate = date("Y-m-d",strtotime(dayofyear2date($cnt,'M d, Y',$fieldYear)));					
				$displayCurrentDay = date("M d, Y",strtotime(dayofyear2date($cnt,'M d, Y',$fieldYear)));		
				$irrigationAppliedCurrentDay = 0;
				if(isset($this->row_tblIndividField[$cnt]['irrig']))
					$irrigationAppliedCurrentDay = $this->row_tblIndividField[$cnt]['irrig'];
				$forageCutCurrentDay = 0;
				if(isset($this->row_tblIndividField[$cnt]['foragecutting']))
					$forageCutCurrentDay = $this->row_tblIndividField[$cnt]['foragecutting'] ;
				if($forageCutCurrentDay == 1)
				{
					$lastForage = $cnt; //
				}
				
				$waterStorageAtFieldCapacityCurrentDay = $soilCapacityField * ($rootDepth/12);
				
				$waterStorageAtPermanentWiltingPoint = $waterStorageAtFieldCapacityCurrentDay - ($soilAvailableWaterContent * ($rootDepth/12));
				$availableWaterAtFieldCapacity = $waterStorageAtFieldCapacityCurrentDay - $waterStorageAtPermanentWiltingPoint;
				$waterStorageAtMad = (1-$mad)*$availableWaterAtFieldCapacity+$waterStorageAtPermanentWiltingPoint;

				$kcVal = $this->calculateKC($cnt, $kcVal, $kc1, $kc2, $kc3, $plantDate, $this->row_FieldInfo['growthMaxDate'], $this->row_FieldInfo['growthDeclineDate'], $this->row_FieldInfo['growthEndDate'], $seasonEndDate, $lastForage, $this->partA, $this->partB);
 				if(isset($currentSoilProfileWaterStoragePreviousDay) && $currentSoilProfileWaterStoragePreviousDay < $waterStorageAtMadPreviousDay)
 				{
					$Ks = ($currentSoilProfileWaterStorage - $waterStorageAtPermanentWiltingPoint) / ($waterStorageAtMad - $waterStorageAtPermanentWiltingPoint);
					if($Ks > 1) 
					{
						$Ks = 1;
					}
				}
				else
				{
					$Ks = 1;
				}
				if($cnt > $plantDate)
				{
					$currentSoilProfileWaterStoragePreviousDay =	$this->row_tblIndividField[$cnt-1]['currentSoilProfileWaterStorage'];
					$waterStorageAtMadPreviousDay = $this->row_tblIndividField[$cnt-1]['waterStorageAtMad'];
				}

				$sumPrecipCurrentDay = $etrArray[$cnt]['precip'];
				$ET = $etrArray[$cnt]['et'] * $kcVal;
				if($ET < 0) 
					$ET = 0;						
				$ET = $Ks * $ET;

				$modifiedCurrentDay = $this->calculateCurrentDay($cnt,$plantDate,$availableSoilWaterContentAbovePWP,$calculatedSoilWaterAvailability,$currentSoilProfileWaterStorage,$waterStorageAtFieldCapacityCurrentDay,$msdPcnt, $sumPrecipCurrentDay, $ET, $deepPercolationCurrentDay, $waterStorageAtPermanentWiltingPoint,$availableWaterAtFieldCapacity);									
				$availableSoilWaterContentAbovePWP = $this->calculateAvailableSoilWaterContentAbovePWP($calculatedSoilWaterAvailability,$availableWaterAtFieldCapacity);													
				$rootZoneWaterDeficit = $waterStorageAtFieldCapacityCurrentDay - $currentSoilProfileWaterStorage;
				$this->row_tblIndividField[$cnt]['doy'] = $cnt;					
				$this->row_tblIndividField[$cnt]['fieldYear'] = $fieldYear;					
				$this->row_tblIndividField[$cnt]['fieldDate'] = $fieldDate;					
				$this->row_tblIndividField[$cnt]['individFieldID'] = $currentDayID;
				$this->row_tblIndividField[$cnt]['fcWater'] = $waterStorageAtFieldCapacityCurrentDay;
				$this->row_tblIndividField[$cnt]['wpWater'] = $availableWaterAtFieldCapacity;
				$this->row_tblIndividField[$cnt]['madWater'] = $mad; ///100 * $waterStorageAtFieldCapacityCurrentDay; ;
				$this->row_tblIndividField[$cnt]['kc'] = $kcVal;
				$this->row_tblIndividField[$cnt]['et'] = round($etrArray[$cnt]['et'],3);
				$this->row_tblIndividField[$cnt]['etc'] = round($ET,3);
				$this->row_tblIndividField[$cnt]['rain'] = round($sumPrecipCurrentDay,2);
				$this->row_tblIndividField[$cnt]['irrig'] = round($irrigationAppliedCurrentDay,2);
				$this->row_tblIndividField[$cnt]['measdPcntAvail'] = $msdPcnt;
				$this->row_tblIndividField[$cnt]['modified'] = $modifiedCurrentDay;
				$this->row_tblIndividField[$cnt]['rootDepth'] = $rootDepth;
				$this->row_tblIndividField[$cnt]['waterStorageAtFieldCapacity'] = $waterStorageAtFieldCapacityCurrentDay;
				$this->row_tblIndividField[$cnt]['waterStorageAtMad'] = $waterStorageAtMad;
				$this->row_tblIndividField[$cnt]['availableSoilWaterContentAbovePWP'] = $availableSoilWaterContentAbovePWP;
				$this->row_tblIndividField[$cnt]['waterStorageAtPermanentWiltingPoint'] = $waterStorageAtPermanentWiltingPoint;
				$this->row_tblIndividField[$cnt]['rootZoneWaterDeficit'] = $rootZoneWaterDeficit;
				$this->row_tblIndividField[$cnt]['calculatedSoilWaterAvailability'] = $calculatedSoilWaterAvailability;
				$this->row_tblIndividField[$cnt]['deepPercolation'] = $deepPercolationCurrentDay;
				$this->row_tblIndividField[$cnt]['Ks'] = $Ks;
				if($waterStorageAtFieldCapacityCurrentDay > $_SESSION['maxy'])
				{
					$_SESSION['maxy'] = $waterStorageAtFieldCapacityCurrentDay+0.5;
				}

				if(isset($this->row_tblIndividField[$cnt]['individFieldID']) && $this->row_tblIndividField[$cnt]['individFieldID'] > 0)
				{
					if($updateFlag || $this->row_tblIndividField[$cnt]['currentSoilProfileWaterStorage'] <> round($this->row_tblIndividField[$cnt]['currentSoilProfileWaterStorage'],4) || $this->row_tblIndividField[$cnt]['availableSoilWaterContentAbovePWP'] <> round($currentSoilProfileWaterStorage,5))
					{
						$this->row_tblIndividField[$cnt]['currentSoilProfileWaterStorage'] = $currentSoilProfileWaterStorage;						
						$database->updateDay($cnt, $this);		
						$updateFlag = true;
					}
				}
				else
				{
					$this->row_tblIndividField[$cnt]['currentSoilProfileWaterStorage'] = $currentSoilProfileWaterStorage;						
					$database->insertDay($cnt,$this);
				}

			}
			elseif(isset($etrArray[$cnt-1]))
			{
				$currentDayID = ""; //$this->row_tblIndividField[$cnt]['individFieldID'];
				$modifiedCurrentDay = 0;
				$msdPcnt = ""; //$this->row_tblIndividField[$cnt]['measdPcntAvail'];	
				$deepPercolationCurrentDay = 0;
				
				$fieldDate = date("Y-m-d",strtotime(dayofyear2date($cnt,'M d, Y',$fieldYear)));					
				$displayCurrentDay = date("M d, Y",strtotime(dayofyear2date($cnt,'M d, Y',$fieldYear)));					
				$irrigationAppliedCurrentDay = 0;
				if(isset($this->row_tblIndividField[$cnt]['irrig']))
					$irrigationAppliedCurrentDay = $this->row_tblIndividField[$cnt]['irrig'];
				$forageCutCurrentDay = ""; //$this->row_tblIndividField[$cnt]['foragecutting'] ;
				if($forageCutCurrentDay == 1)
				{
					$lastForage = $cnt; //
				}
				
				$waterStorageAtFieldCapacityCurrentDay = $soilCapacityField * ($rootDepth/12);
				
				$waterStorageAtPermanentWiltingPoint = $waterStorageAtFieldCapacityCurrentDay - ($soilAvailableWaterContent * ($rootDepth/12));
				$availableWaterAtFieldCapacity = $waterStorageAtFieldCapacityCurrentDay - $waterStorageAtPermanentWiltingPoint;
				$waterStorageAtMad = (1-$mad)*$availableWaterAtFieldCapacity+$waterStorageAtPermanentWiltingPoint;

				$kcVal = $this->calculateKC($cnt, $kcVal, $kc1, $kc2, $kc3, $plantDate, $this->row_FieldInfo['growthMaxDate'], $this->row_FieldInfo['growthDeclineDate'], $this->row_FieldInfo['growthEndDate'], $seasonEndDate, $lastForage, $this->partA, $this->partB);
 				if(isset($currentSoilProfileWaterStoragePreviousDay) && $currentSoilProfileWaterStoragePreviousDay < $waterStorageAtMadPreviousDay)
 				{
					$Ks = ($currentSoilProfileWaterStorage - $waterStorageAtPermanentWiltingPoint) / ($waterStorageAtMad - $waterStorageAtPermanentWiltingPoint);
					if($Ks > 1) 
					{
						$Ks = 1;
					}
				}
				else
				{
					$Ks = 1;
				}
				if($cnt > $plantDate)
				{
					$currentSoilProfileWaterStoragePreviousDay =	$this->row_tblIndividField[$cnt-1]['currentSoilProfileWaterStorage'];
					$waterStorageAtMadPreviousDay = $this->row_tblIndividField[$cnt-1]['waterStorageAtMad'];
				}

				$sumPrecipCurrentDay = 0; //$etrArray[$cnt]['precip'];
				$ET = $etrArray[$cnt-1]['et'] * $kcVal;
				if($ET < 0) 
					$ET = 0;						
				$ET = $Ks * $ET;

				$modifiedCurrentDay = $this->calculateCurrentDay($cnt,$plantDate,$availableSoilWaterContentAbovePWP,$calculatedSoilWaterAvailability,$currentSoilProfileWaterStorage,$waterStorageAtFieldCapacityCurrentDay,$msdPcnt, $sumPrecipCurrentDay, $ET, $deepPercolationCurrentDay, $waterStorageAtPermanentWiltingPoint,$availableWaterAtFieldCapacity);									
				$availableSoilWaterContentAbovePWP = $this->calculateAvailableSoilWaterContentAbovePWP($calculatedSoilWaterAvailability,$availableWaterAtFieldCapacity);													
				$rootZoneWaterDeficit = $waterStorageAtFieldCapacityCurrentDay - $currentSoilProfileWaterStorage;
				$this->row_tblIndividField[$cnt]['doy'] = $cnt;					
				$this->row_tblIndividField[$cnt]['fieldYear'] = $fieldYear;					
				$this->row_tblIndividField[$cnt]['fieldDate'] = $fieldDate;					
				$this->row_tblIndividField[$cnt]['individFieldID'] = $currentDayID;
				$this->row_tblIndividField[$cnt]['fcWater'] = $waterStorageAtFieldCapacityCurrentDay;
				$this->row_tblIndividField[$cnt]['wpWater'] = $availableWaterAtFieldCapacity;
				$this->row_tblIndividField[$cnt]['madWater'] = $mad; ///100 * $waterStorageAtFieldCapacityCurrentDay; ;
				$this->row_tblIndividField[$cnt]['kc'] = $kcVal;
				if(isset($etrArray[$cnt]))
					$this->row_tblIndividField[$cnt]['et'] = round($etrArray[$cnt]['et'],3);
				else
					$this->row_tblIndividField[$cnt]['et'] = 0;
				$this->row_tblIndividField[$cnt]['etc'] = round($ET,3);
				$this->row_tblIndividField[$cnt]['rain'] = round($sumPrecipCurrentDay,2);
				$this->row_tblIndividField[$cnt]['irrig'] = round($irrigationAppliedCurrentDay,2);
				$this->row_tblIndividField[$cnt]['measdPcntAvail'] = $msdPcnt;
				$this->row_tblIndividField[$cnt]['modified'] = $modifiedCurrentDay;
				$this->row_tblIndividField[$cnt]['rootDepth'] = $rootDepth;
				$this->row_tblIndividField[$cnt]['waterStorageAtFieldCapacity'] = $waterStorageAtFieldCapacityCurrentDay;
				$this->row_tblIndividField[$cnt]['waterStorageAtMad'] = $waterStorageAtMad;
				$this->row_tblIndividField[$cnt]['availableSoilWaterContentAbovePWP'] = $availableSoilWaterContentAbovePWP;
				$this->row_tblIndividField[$cnt]['waterStorageAtPermanentWiltingPoint'] = $waterStorageAtPermanentWiltingPoint;
				$this->row_tblIndividField[$cnt]['rootZoneWaterDeficit'] = $rootZoneWaterDeficit;
				$this->row_tblIndividField[$cnt]['calculatedSoilWaterAvailability'] = $calculatedSoilWaterAvailability;
				$this->row_tblIndividField[$cnt]['deepPercolation'] = $deepPercolationCurrentDay;
				$this->row_tblIndividField[$cnt]['Ks'] = $Ks;
				if($waterStorageAtFieldCapacityCurrentDay > $_SESSION['maxy'])
				{
					$_SESSION['maxy'] = $waterStorageAtFieldCapacityCurrentDay+0.5;
				}

				if(isset($this->row_tblIndividField[$cnt]['individFieldID']) && $this->row_tblIndividField[$cnt]['individFieldID'] > 0)
				{
					if($updateFlag || $this->row_tblIndividField[$cnt]['currentSoilProfileWaterStorage'] <> round($this->row_tblIndividField[$cnt]['currentSoilProfileWaterStorage'],4) || $this->row_tblIndividField[$cnt]['availableSoilWaterContentAbovePWP'] <> round($currentSoilProfileWaterStorage,5))
					{
						$this->row_tblIndividField[$cnt]['currentSoilProfileWaterStorage'] = $currentSoilProfileWaterStorage;						
						$database->updateDay($cnt, $this);							
						$updateFlag = true;
					}
				}
				elseif( (date("z") ) <> $cnt)  //There is a problem here between the boundaries of observed data and forecast data when a particular day is missing.
				{
					//////error_log("No Insert Day?");
					//die("This should not insert $cnt".date("z"));
					//$this->row_tblIndividField[$cnt]['currentSoilProfileWaterStorage'] = $currentSoilProfileWaterStorage;						
					//$database->insertDay($cnt,$this);
				}
				/*
				*/
			}
			//roots grow at end of day
			if($cnt < $this->row_FieldInfo['growthDeclineDate'])
				$rootDepth += $rootGrowthPerDay;
		} // end of daily $cnt loop
	} // eof updateValues

	function calculateCurrentDay($cnt,$plantDate,&$availableSoilWaterContentAbovePWP,&$calculatedSoilWaterAvailability,&$currentSoilProfileWaterStorage,&$waterStorageAtFieldCapacityCurrentDay,&$msdPcnt, &$sumPrecipCurrentDay, &$ET, &$deepPercolationCurrentDay, &$waterStorageAtPermanentWiltingPoint, &$availableWaterAtFieldCapacity)
	{				
		$modifiedCurrentDay = 0;
		//On the first day of the season, assume 100 percent available water unless there is a measured percent
		if($cnt == $plantDate && is_null($msdPcnt) )
		{
			$availableSoilWaterContentAbovePWP = 100;
			$calculatedSoilWaterAvailability = 100;
			$currentSoilProfileWaterStorage = $waterStorageAtFieldCapacityCurrentDay;
		}
		elseif(is_null($msdPcnt) )  //it is not the first day, and we don't use a measured percent
		{
			// yesterdays water storage 
			// + todays rain 
			// + todays irrigation 
			// - todays evapotranspiration 
			// + the water storage at field capacity 
			// - yesterdays water storage at field capacity = 
			// potential current soil profile water storage???
			////////error_log("Calculated is null msdPcnt for $cnt");
			$v1 = 0;
			if(isset($this->row_tblIndividField[$cnt-1]['currentSoilProfileWaterStorage']))
				$v1 = $this->row_tblIndividField[$cnt-1]['currentSoilProfileWaterStorage'];
			$v2 = 0;
			if(isset($this->row_tblIndividField[$cnt]['irrig']))
				$v2 = $this->row_tblIndividField[$cnt]['irrig'];
			$v3 = 0;
			if(isset($this->row_tblIndividField[$cnt-1]['waterStorageAtFieldCapacity']))
				$v3 = $this->row_tblIndividField[$cnt-1]['waterStorageAtFieldCapacity'];
			$sumValue = $v1 
									+ $sumPrecipCurrentDay 
									+ $v2
									- $ET
									+ $waterStorageAtFieldCapacityCurrentDay
									- $v3;
			if($sumValue < 0)  //if that is negative, then 0
			{
				$sumValue = 0;
			}
			if($waterStorageAtFieldCapacityCurrentDay > $sumValue) //if todays water stoarage is less than the max
			{
				$currentSoilProfileWaterStorage = $sumValue; //then currentSoilProfileWaterStorage = the calculate value
			}
			else
			{
				//If there was more water than the field could hold
				//The extra water is the calculated value - the total field capacity
				$deepPercolationCurrentDay = $sumValue - $waterStorageAtFieldCapacityCurrentDay;
				//and currentSoilProfileWaterStorage = the field capacity
				$currentSoilProfileWaterStorage = $waterStorageAtFieldCapacityCurrentDay;
			}							
			if( ($currentSoilProfileWaterStorage-$waterStorageAtPermanentWiltingPoint)/($waterStorageAtFieldCapacityCurrentDay-$waterStorageAtPermanentWiltingPoint) > 1)
			{
				$calculatedSoilWaterAvailability = 100;
			}
			elseif(($currentSoilProfileWaterStorage-$waterStorageAtPermanentWiltingPoint)/($waterStorageAtFieldCapacityCurrentDay-$waterStorageAtPermanentWiltingPoint) > 0)
			{
				$calculatedSoilWaterAvailability = ($currentSoilProfileWaterStorage-$waterStorageAtPermanentWiltingPoint)/($waterStorageAtFieldCapacityCurrentDay-$waterStorageAtPermanentWiltingPoint) * 100;
			}
			else
			{
				$calculatedSoilWaterAvailability = 0;
			}
		}
		else
		{
			$currentSoilProfileWaterStorage = $msdPcnt/100 * $availableWaterAtFieldCapacity + $waterStorageAtPermanentWiltingPoint;
			$calculatedSoilWaterAvailability = $msdPcnt; //$this->row_tblIndividField[$cnt]['measdPcntAvail'];
			$modifiedCurrentDay = 1;
		}
		return $modifiedCurrentDay;
	}
	function calculateAvailableSoilWaterContentAbovePWP($calculatedSoilWaterAvailability,$availableWaterAtFieldCapacity)
	{
		if((($calculatedSoilWaterAvailability/100) * $availableWaterAtFieldCapacity) < 0)
		{
			$availableSoilWaterContentAbovePWP = 0;
		}
		else
		{
			$availableSoilWaterContentAbovePWP = ($calculatedSoilWaterAvailability/100) * $availableWaterAtFieldCapacity;
		}
		return $availableSoilWaterContentAbovePWP;
	}
	function getWeatherData($updateFlag)
	{
		global $database;
		if(isset($_SESSION['lastUpdate']))
		{
			$lastUpdated = $_SESSION['lastUpdate'];
		}
		if($updateFlag == 0 && date("Y-m-d H:i:s",strtotime($lastUpdated)) > date("Y-m-d H:i:s",strtotime("-2 hours")))
		{
			//If the last time it tblindividfield was updated is less than an hour ago, we will use data from local AWN database
			//including the forecast
			$query = "SELECT doy, etr, rain from irrigation.tblindividfield where status = 0 and fieldID = ".$this->row_FieldInfo['fieldID']." order by DOY ";
			$result = $database->query($query);
			while($row = mysql_fetch_assoc($result))
			{
				if($row['rain'] < 0)
					$row['rain'] = 0;
				$etrArray[$row['doy']]['et'] = $row['etr'];
				$etrArray[$row['doy']]['precip'] = $row['rain'];
			}
		}
		else
		{
			$StartDate = date("Y-m-d",strtotime(dayofyear2date($this->row_FieldInfo['plantDate'],'M d, Y',$this->row_FieldInfo['year'])));
			$EndDate = date("Y-m-d",strtotime(dayofyear2date($this->row_FieldInfo['seasonEndDate'],'M d, Y',$this->row_FieldInfo['year'])));
			//Get the data for AWN or AgriMet
			switch($this->row_FieldInfo['weather_network'])
			{
				case 1:
				case 20:
					//get specifics of the weather station
					$this->arrStationMetaData = $database->GetStationMetaData ( $this->row_FieldInfo['weatherStnID'] );
					$lat = $this->arrStationMetaData['STATION_LATDEG'];
					$lng = -1 * $this->arrStationMetaData['STATION_LNGDEG'];
	
					$etrArray = $database->awnET($this->row_FieldInfo['weatherStnID'], $StartDate, $EndDate);
				break;
				case 2:
				case 120:
					//get specifics of the weather station
					$this->arrStationMetaData = $database->GetAgriMetStationMetaData ( $this->row_FieldInfo['weatherStnID'] );
					$lat = $this->arrStationMetaData['Latitude'];
					$lng = $this->arrStationMetaData['Longitude'];
					$etrArray = $this->getAgriMetdata($this->row_FieldInfo['weatherStnID'],$StartDate,$EndDate);
				break;
				case 3:
				case 220:
					//get specifics of the weather station
					$this->arrStationMetaData = $database->GetCoAgMetStationMetaData ( $this->row_FieldInfo['weatherStnID'] );
					$lat = $this->arrStationMetaData[$this->row_FieldInfo['weatherStnID']]['LAT'];
					$lng = $this->arrStationMetaData[$this->row_FieldInfo['weatherStnID']]['LNG'] * -1;
					$totalDays = date("z",strtotime($EndDate))-date("z",strtotime($StartDate));
					$etrArray = $this->getCoAgMetData($this->row_FieldInfo['weatherStnID'],$StartDate,$EndDate, $totalDays);
	   				break;
				case 320:
					//get specifics of the weather station
					$this->arrStationMetaData = $database->GetAzmetStationMetaData ( $this->row_FieldInfo['weatherStnID'] );
					$lat = $this->arrStationMetaData['station_latdeg'];
					$lng = $this->arrStationMetaData['station_lngdeg'];
	
					$etrArray = $database->azmetET($this->row_FieldInfo['weatherStnID'], $StartDate, $EndDate);
	   				break;
				case 420:
					//get specifics of the weather station
					$this->arrStationMetaData = $database->GetNDAWNStationMetaData ( $this->row_FieldInfo['weatherStnID'] );
					$lat = $this->arrStationMetaData['station_latdeg'];
					$lng = $this->arrStationMetaData['station_lngdeg'];
	
					$etrArray = $database->NDAWNET($this->row_FieldInfo['weatherStnID'], $StartDate, $EndDate);
	   				break;
				case 520:
					//get specifics of the weather station
					$this->arrStationMetaData = $database->GetAWDNStationMetaData ( $this->row_FieldInfo['weatherStnID'] );
					$lat = $this->arrStationMetaData['station_latdeg'];
					$lng = $this->arrStationMetaData['station_lngdeg'];
	
					$etrArray = $database->AWDNET($this->row_FieldInfo['weatherStnID'], $StartDate, $EndDate);
	   				break;
				case 620:
					//get specifics of the weather station
					$this->arrStationMetaData = $database->GetCIMISStationMetaData ( $this->row_FieldInfo['weatherStnID'] );
					$lat = $this->arrStationMetaData['station_latdeg'];
					$lng = $this->arrStationMetaData['station_lngdeg'];
					$etrArray = $database->CIMISET($this->row_FieldInfo['weatherStnID'], $StartDate, $EndDate);
	   				break;
				case 720:
					//get specifics of the weather station
					$this->arrStationMetaData = $database->GetMTAgriMetStationMetaData ( $this->row_FieldInfo['weatherStnID'] );
					$lat = $this->arrStationMetaData['Latitude'];
					$lng = $this->arrStationMetaData['Longitude'];
					$etrArray = $this->getMTAgriMetdata($this->row_FieldInfo['weatherStnID'],$StartDate,$EndDate);
				break;
				default:
					break;
			}					
	
			if($this->row_FieldInfo['useNDFDforecast'] == 1)
			{
				//Forecasting...;
				if(! $this->doForecast($etrArray, $lat, $lng) )
					echo "Failed to retrieve forecast information, forecast unavailable.";
				//Forecast complete $lat $lng
			}
		}
		return $etrArray;
	}

	function calculateKC($doy, $kcVal, $kc1, $kc2, $kc3, $plantDate, $maxDate, $declineDate, $endGrowthDate, $seasonEndDate, $lastForage, $partA, $partB)
	{	
		if($doy < $maxDate)
		{
			$kcVal = $kc1;
			$maxBeforeMax = $kcVal;
			$kc2rate = 0;
		}
		if($doy >= $maxDate)
		{
			$mult = $doy - $maxDate;					
			$numDays =  $declineDate - $maxDate;
			$rate = 	($kc2-$kc1)/$numDays;
			$increment = $rate * $mult;
			$kcVal = $kc1 + $increment;
			$maxBeforeDecline = $kcVal;
			$kc2rate = 0;
		}
		if($doy >= $declineDate && $doy <= $endGrowthDate)
		{
			$kcVal = $kc2;
			$maxBeforeEnd = $kcVal;
			$kc2rate = 0;
		}
		if($doy > $endGrowthDate)
		{
			$mult = $doy - $endGrowthDate;
			$kc3days = $seasonEndDate - $endGrowthDate ;
			$rate = ($kc3 - $kc2)/$kc3days;
			$increment = $rate * $mult;
			$kcVal = $kc2 + $increment;
			$maxAfterEnd = $kcVal;
			$kc2rate = 0;
		}
		if($this->partA > 0 && $this->partB > 0 && $doy >= $lastForage && $doy <= ($lastForage + $this->partA + $this->partB) )
		{
			if($doy <= $lastForage + $this->partA)
			{
				$kcVal = $kc1;
				$kc2rate = 0;
			}
			else
			{
				$mult = $doy - ($lastForage + $this->partA);
				$rate = ($kc2 - $kc1)/$this->partB;
				$increment = $rate * $mult;
				$kcVal = $kc1 + $increment;
				$kc2rate = 0;
			}
			if($doy < $maxDate && $kcVal > $maxBeforeMax)
			{
				$kcVal = $maxBeforeMax;
			}
			if($doy >= $maxDate && $doy < $declineDate && $kcVal > $maxBeforeDecline)
			{
				$kcVal = $maxBeforeDecline;
			}
			if($doy >= $declineDate && $doy < $endGrowthDate && $kcVal > $maxBeforeEnd)
			{
				$kcVal = $maxBeforeEnd;
			}
			if($doy > $endGrowthDate && $kcVal > $maxAfterEnd)
			{
				$kcVal = $maxAfterEnd;
			}
		}
		return $kcVal;
	}
		
	function doForecast(&$etrArray,$lat =42,$lng =-120, $ele=0)
	{
		require_once 'ETCalc.class.php';
		$etr = "";
		$eto = "";
		$AnemomH = 2;		
		$retVal = false;
		$tempArrays = $this->generateFcst($lat,$lng);
		foreach($tempArrays as $key=>$value)
		{
      $etr = ETHarg( date("z",strtotime($key))+1,  $value['max'],  $value['min'], $lat) ;
      if(isset($etrArray[date("z",strtotime("-1 days",strtotime($key)))]['et']))
      {
				if($etr == 0)
				{
					$etr = $etrArray[date("z",strtotime("-1 days",strtotime($key)))]['et'];
				}
			}
			$etrArray[date("z",strtotime($key))]['et'] = $etr;
			$etrArray[date("z",strtotime($key))]['precip'] = 0;
		}
		$retVal = true;
		return $retVal;
	}

	function GetMTAgriMetData($stnName,$StrtDate,$EndDate) {
		require_once 'ETCalc.class.php';
		global $database;
		if($EndDate > date("Y-m-d"))
			$EndDate = date("Y-m-d");
			
      $resultArray = array();

      //Copied from GetAWNData.  Get the latitude and elevation from the station table.
			$metaQuery = "SELECT * from irrigation.mtagrimetstations where StnID = '".$this->row_FieldInfo['weatherStnID']."'";
			if($metaResult = $database->query($metaQuery))
		  {
	      $metaRow = mysql_fetch_assoc($metaResult);
	      $lat = $metaRow['Latitude'];
	      $ele = $metaRow['Elevation'];
      }                      
      else
      {
              die("Could not get weather station info");
      }
      
      $strtDy = "&syer=".date("Y",strtotime($StrtDate))."&smnth=".date("m",strtotime($StrtDate))."&sdy=".date("d",strtotime($StrtDate));
      $endDy = "&eyer=".date("Y",strtotime($EndDate))."&emnth=".date("m",strtotime($EndDate))."&edy=".date("d",strtotime($EndDate));
      $URLFile = 'http://www.usbr.gov/gp-bin/webarccsv.pl?parameter='.$stnName.'%20MX,'.$stnName.'%20MN,'.$stnName.'%20SR,'.$stnName.'%20WR,'.$stnName.'%20YM,'.$stnName.'%20PP';
      $URLFile .= $strtDy . $endDy . '&format=1';
      $lines = file ( $URLFile ); //This takes the data and puts it into a variable
      //This checks how many lines total are in the results
      $numLine = count ( $lines )-5; //-5 because there is garbage at the end of the file.
 

      //The initial value for the $i variable is set so that we skip over any "header" type of information returned by the URL
      for($i = 20; $i < $numLine; $i ++) {
	      list ( $Date, $Tmax, $Tmin, $Rs, $WR, $Tdew, $PP ) = preg_split ( "/[\s]+/", trim ( $lines [$i] ) );
	      list ( $month, $day, $year ) = explode ( "/", $Date );
				//We have to multiple by 11.622 because ETCalc.php is got a bad function we need to replace
				//converts to Watts/hour/m2 from langleys 
	      $Rs = $Rs * 11.622;
	      //Calculate Day of Year (JulDay)
	      $DOY = $day-32+floor(275*$month/9)+2*floor(3/($month+1))+floor($month/100-($year%4)/4+0.975);
	      $AnemomH = 2;
	      ETCalc ($Tmax, 
	      				$Tmin, 
	      				$Tdew, 
	      				$Rs, 
	      				$WR, 
	      				$DOY, 
	      				$lat, 
	      				$ele, 
	      				$AnemomH, 
	      				$etr, 
	      				$eto);         
 
	      $resultArray[date("z",strtotime($Date))]['et'] = $etr;
	      $resultArray[date("z",strtotime($Date))]['precip'] = $PP;
      }
      
		return $resultArray;  
	}
	function checkPrecip($PP)
	{
		$retVal = $PP;
		if($PP < 0)
			$retVal = 0;
		return $retVal;
	}
  function GetAgriMetData($stnName,$StrtDate,$EndDate) {
  		if($EndDate > date("Y-m-d"))
  			$EndDate = date("Y-m-d");
      $resultArray = array();
      $strtDate = "&year=".date("Y",strtotime($StrtDate))."&month=".date("m",strtotime($StrtDate))."&day=".date("d",strtotime($StrtDate));
      $endDate = "&year=".date("Y",strtotime($EndDate))."&month=".date("m",strtotime($EndDate))."&day=".date("d",strtotime($EndDate));
      $URLFile = 'http://www.usbr.gov/pn-bin/webarccsv.pl?station=' . $stnName . $strtDate . $endDate . '&pcode=ETRS&pcode=PP';
      $lines = file ( $URLFile ); //This takes the data and puts it into a variable
      //This checks how many lines total are in the results
      $numLine = count ( $lines )-6; //-6 because there is garbage at the end of the file.
      //The initial value for the $i variable is set so that we skip over any "header" type of information returned by the URL
      for($i = 21; $i < $numLine; $i ++) {
              //This takes the variables $lines[$i] and splits the values into multiple variables that can be used individually.  The preg_split function looks for \s as the split, for comma separated there would need to be something similar to "/[,]/" but I haven't tested that, so no promise as to the exact syntax
              list ( $Date, $ETrs, $PP ) = preg_split ( "/[\s]+/", trim ( $lines [$i] ) );
              $PP = $this->checkPrecip($PP);
              //This takes the $Date variable from the previous line and splits it into month, day, and year (they are separated by /)
              list ( $month, $day, $year ) = explode ( "/", $Date );
        			$resultArray[date("z",strtotime($Date))]['et'] = $ETrs;
        			$resultArray[date("z",strtotime($Date))]['precip'] = $PP;
      }
      return $resultArray;   
  }
  
  
  function GetCoAgMetData($stnName,$StrtDate,$EndDate, $numDays) {
		global $isession;
    $resultArray = array();
		$strtYear = date("Y",strtotime($StrtDate));
		$strtMonth = date("m",strtotime($StrtDate));
		$strtDay = date("d",strtotime($StrtDate));
		$numdays = $numDays;
		$URL=  "http://ccc.atmos.colostate.edu/cgi-bin/crop_et.pl?start=start&year=$strtYear&month=$strtMonth&day=$strtDay&ndo=$numdays&station=$stnName&Alfalfa_mon=$strtMonth&Alfalfa_day=24&model=etr_asce";
		$dom = new DOMDocument;
		if(@$dom->loadHTMLFile($URL))
		{
			$tables = $dom->getElementsByTagName('table');
			foreach($tables as $table)
			{
				if($table->childNodes->item(0)->nodeValue == 'Crop Evapotranspiration in Inches')
				{
					foreach($table->childNodes as $childNode)
					{
						$someArray = explode (" ", preg_replace( '/\s+/', ' ', $childNode->nodeValue ));
						if(is_date($someArray[0]))
						{
							$results[$someArray[0]]['ET'] = $someArray[1];
							$results[$someArray[0]]['precip'] = $someArray[2];
						}
					}
				}
			}
			foreach($results as $key=>$value)
			{
				$resultArray[date("z",strtotime($key))]['et'] = $value['ET'];
				$resultArray[date("z",strtotime($key))]['precip'] = $value['precip'];
			}
		}
		//
    return $resultArray;
  }

    
	function stationSelect()
	{
    global $database;
    $Station = $database->getAllStations();
    $row_Station = mysql_fetch_assoc($Station);
    $totalRows_Station = mysql_num_rows($Station);	
	}

	function generateFcst($lat, $long, $stationName = "Unspecified Station", $unitID="Unspecified Unit") {
		$aryEndValidTime = array ();
		$aryStartValidTime = array ();
		$aryMaxT = array ();
		$aryMinT = array ();

		// ndfdXML.wsdl was downloaded from www.weather.gov/xml
		$soapclient = new SoapClient ( '/var/www/html/irrigation-scheduler/ndfdXML.wsdl' ); //creates a PHP SOAP object using ndfdXML.wsdl

		$stdate = date ( 'Y-m-d' );
		try{
			$forecast = $soapclient->NDFDgenByDay ( $lat, $long, $stdate, 10,'e', '24 hourly' ); // request XML forecast from NOAA
			if (!$forecast) {
				echo "<h3>Error: no forecast!</h3>\n";
			}
			
			//Here's our Simple XML parser!
			$xmlData = new SimpleXMLElement ( $forecast );	
			foreach ( $xmlData->data->parameters->temperature as $temperature ) {
				$layoutKey = $temperature ["time-layout"];
				switch ($temperature ["type"]) {
					case 'maximum' :
						$i = 0;
						foreach ( $temperature->value as $maxT ) {
							//echo "Max".$maxT.PHP_EOL;
							$aryMaxT [$i] = "$maxT";
							$i ++;
						}
						break;
					
					case 'minimum' :
						$i = 0;
						foreach ( $temperature->value as $minT ) {
							$aryMinT [$i] = "$minT";
							$i ++;
						}
						break;			
				}
			}
			foreach ( $xmlData->data->{'time-layout'} as $timeLayout ) {
				$tLayoutKey = $timeLayout->{'layout-key'};
				if (strcasecmp ($layoutKey,$tLayoutKey)==0) 
				{
					$layoutKey2 = $timeLayout->{'layout-key'};
					$i = 0;
					foreach ( $timeLayout->{'start-valid-time'} as $startValidTime ) {
						$aryStartValidTime [$i] = "$startValidTime";
						$i ++;
					}
					$i = 0;
					foreach ( $timeLayout->{'end-valid-time'} as $endValidTime ) {
						$aryEndValidTime [$i] = "$endValidTime";
						$i ++;
					}
				}
			}
			
			$retArray = array();
			$numberDays = count ( $aryStartValidTime );
			for($i = 0; $i < $numberDays; $i ++) {
				$validStartTime = str_replace ( 'T', ' ', substr ( $aryStartValidTime [$i], 0, 19 ) );
				
				if (! empty ( $aryMaxT [$i] )) {
					$maxT = $aryMaxT [$i];
				} else {
					$maxT = 99999;
				}
	
				if (! empty ( $aryMinT [$i] )) {
					$minT = $aryMinT [$i];
				} else {
					$minT = $maxT;
				}
				$retArray[date("Y-m-d",strtotime($validStartTime))]['max'] = $maxT;
				$retArray[date("Y-m-d",strtotime($validStartTime))]['min'] = $minT;
			}
		}
		catch(Exception $e)
		{
			//echo "Forecast Failed...";
			$retArray = array();
		}
		return $retArray;
	} // end generateFcst	
	
	}
?>