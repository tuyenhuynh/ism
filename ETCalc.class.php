<?php

/**
 * This function will calculate Evapotranspiration reference (etr) and ET grass (eto)
 * Modified by Troy Peters and Brent Etzel August, 2007 to the ASCE Stdzd Penman Monteith Equation
 */
function ETCalc($tmax, $tmin, $tdew, $rs, $wr, $jd, $lat, $elv, $AnemomH, &$etr, &$eto) {
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
	$ED = CalcSVP ( $tdew );
	
	//     $ALPHA = 0.29 + 0.06 * sin(($J + 96) / 57.3);
	

	// net radiation
	$Rnl = 0.0000000049 * (pow ( $TMAXK, 4 ) + pow ( $TMINK, 4 )) / 2 * (0.34 - 0.14 * sqrt ( $ED )) * $Fcd;
	$RN = $Rns - $Rnl;
	
	// grass ref et
	$RNG = 0.75 * $rs - 0.000000004903 * (pow ( $TMEANK, 4 ) * (0.34 - 0.139 * pow ( $ED, .5 )) * (1.35 * $rs / $RSO - 0.35));
	
	// soil heat flux
	$G = 0;
	// sat VP @ TA
	$ETAV = CalcSVP ( $TA );
	
	// patent heat of vaporiation
	$LHVP = 2.5002 - 0.002361 * $TA;
	
	// pychromatric constant (kPa C-1)
	$GAMMA = 0.000665 * $P;
	
	// slop of sat VP
	$DELTA = 4099 * (0.6108 * exp ( 17.27 * $TA / ($TA + 237.3) )) / (pow ( ($TA + 237.3), 2 ));
	
	// sat VP at tmax
	$ETX = CalcSVP ( $tmax );
	
	// sat VP at tmin
	$ETN = CalcSVP ( $tmin );
	
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
	// make sure they are not negative
	if ($ETR < 0)
		$ETR = 0;
	$etr = $ETR / 25.4; // convert to inches from mm
	

	if ($ETO < 0)
		$ETO = 0;
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



function ETHarg($jd, $tmax, $tmin, $lat) {
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
function CalcSVP($airTemp) {
	$svp = 0;
	$svp = .6108 * exp ( (17.27 * $airTemp) / ($airTemp + 237.3) );
	return $svp;

} // eof CalcSVP

//ETCalc( 15.4, 11.3, 11.9, 3.9744, 0.7, 25, 38.5, 18.5, 2, $etr, $eto);

