<?php
/*=======================================================================
// File: 	ENCODATION-140.INC.PHP
// Description:	Encodation schemas for ECC 000 140 Datamatrix variant
// Created: 	2006-08-19
// Ver:		$Id: encodation-140.inc.php 988 2008-03-25 02:50:13Z ljp $
//
// Copyright (c) 2006 Asial Corporation. All rights reserved.
//========================================================================
*/

class Encodation_140 {
    public $iError = 0 ;
    private $iN = 4, $iSelectSchema = -1;
    private $iT = 
    array(
	/* Base 11 */
	array(' ' => 0, '0' => 1, '1' => 2, '2' => 3,'3' => 4,'4' => 5,
	      '5' => 6,'6' => 7,'7' => 8,'8' => 9,'9' => 10),

	/* Base 27 */
	array(' ' => 0,
	      'A' => 1, 'B' => 2, 'C' => 3, 'D' => 4, 'E' => 5, 'F' => 6, 
	      'G' => 7, 'H' => 8, 'I' => 9, 'J' => 10, 'K' => 11, 'L' => 12, 
	      'M' => 13, 'N' => 14, 'O' => 15, 'P' => 16, 'Q' => 17, 'R' => 18, 
	      'S' => 19,'T' => 20, 'U' => 21, 'V' => 22, 'W' => 23, 
	      'X' => 24, 'Y' => 25, 'Z' => 26),

	/* Base 37 */
	array(' ' => 0,
	      'A' => 1, 'B' => 2, 'C' => 3, 'D' => 4, 'E' => 5, 'F' => 6, 
	      'G' => 7, 'H' => 8, 'I' => 9, 'J' => 10, 'K' => 11, 'L' => 12, 
	      'M' => 13, 'N' => 14, 'O' => 15, 'P' => 16, 'Q' => 17, 'R' => 18, 
	      'S' => 19,'T' => 20, 'U' => 21, 'V' => 22, 'W' => 23, 
	      'X' => 24, 'Y' => 25, 'Z' => 26,
	      '0' => 27, '1' => 28, '2' => 29, '3' => 30, '4' => 31, 
	      '5' => 32, '6' => 33, '7' => 34, '8' => 35, '9' => 36),

	/* Base 41 */
	array(' ' => 0,
	      'A' => 1, 'B' => 2, 'C' => 3, 'D' => 4, 'E' => 5, 'F' => 6, 
	      'G' => 7, 'H' => 8, 'I' => 9, 'J' => 10, 'K' => 11, 'L' => 12, 
	      'M' => 13, 'N' => 14, 'O' => 15, 'P' => 16, 'Q' => 17, 'R' => 18, 
	      'S' => 19,'T' => 20, 'U' => 21, 'V' => 22, 'W' => 23, 
	      'X' => 24, 'Y' => 25, 'Z' => 26,
	      '0' => 27, '1' => 28, '2' => 29, '3' => 30, '4' => 31, 
	      '5' => 32, '6' => 33, '7' => 34, '8' => 35, '9' => 36,
	      ',' => 38, '-' => 39, '.' => 37, '/' => 40)
	);

    function Encodation_140($aSchema=ENCODING_BASE11) {
	$this->iSelectSchema = $aSchema;
    }

    // Return the prefix that tells what encodation schema was used
    function GetPrefix() {
	$prefix = array(ENCODING_BASE11 => '00000', ENCODING_BASE27 => '00001', 
			ENCODING_BASE37 => '00011', ENCODING_BASE41 => '00010',
			ENCODING_ASCII => '00100', ENCODING_BYTE => '00101');

	return str_split($prefix[$this->iSelectSchema]);
    }

    function GetCRCPrefix() {
	$prefix = array(ENCODING_BASE11 => 1, ENCODING_BASE27 => 2, 
			ENCODING_BASE37 => 4, ENCODING_BASE41 => 3,
			ENCODING_ASCII => 5, ENCODING_BYTE => 6);

	return $prefix[$this->iSelectSchema];
    }

    function AutoSelect($aData) {
	// Find the smallest encoding schema that can handle all data
	$schema = -1;
	$found = false;
	$n = count($aData);
	while( $schema < $this->iN && !$found ) {
	    $schema++;
	    $found = true;
	    for($i=0; $i < $n && $found; ++$i ) {
		$found = array_key_exists($aData[$i],$this->iT[$schema]);
	    }
	}

	if( !$found ) {
	    // Check if ASCII encoding can be used
	    $maxval = 0 ;
	    for($i=0; $i < $n && $maxval < 128; ++$i ) {
		$maxval = max($maxval,ord($aData[$i]));
	    }	    
	    if( $maxval < 128 ) {
		$schema = ENCODING_ASCII;
	    }
	    elseif( $maxval > 127 && $maxval < 256) {
		// Must use 8-bit byte encoding
		$schema = ENCODING_BYTE;
	    }
	    else {
		$this->iError = -11;
		return false;
	    }
	}

	$this->iSelectSchema = $schema;
	return $schema;

    }

    function SetSchema($aSchema) {
	$this->iSelectSchema = $aSchema;
    }

    // $aN == Maximum number of coefficients
    // $aP == The actual coefficients
    // $aBitLen == Bits to use for 1,2,.. < $aN remaining symbols
    function _Encode($aData,$aN,$aP,$aBitLen,&$aSymbols) {
	$n = count($aData);
	$m = count($aBitLen);
	
	$i=0;$idx=0;
	$aSymbols = array();

	// Loop while we can complete full conversion for the chracters
	while( $n >= $aN ) {
	    $tmp = 0;
	    for( $j=0; $j < $aN; ++$j ) {
		$tmp += $aP[$j]*$this->iT[$this->iSelectSchema][$aData[$i+$j]];
	    }
	    Word2Bits($tmp,$aSymbols[$idx],$aBitLen[$m-1]);
	    ++$idx;
	    $n -= $aN;
	    $i += $aN;
	}

	// Now we have either processed all words or there are less than a 
	// full conversion length left. In that case the specifications will
	// tell us how many bits to use when there are X characters left.
	if( $n > 0 ) {
	    $tmp = 0;
	    for( $j=0; $j < $n; ++$j ) {
		$tmp += $aP[$j]*$this->iT[$this->iSelectSchema][$aData[$i+$j]];
	    }
	    Word2Bits($tmp,$aSymbols[$idx],$aBitLen[$n-1]);
	}
    }

    function Encode($aData, &$aSymbols) {
	$n = count($aData);
	// Base constants for the base XX -> 2 conversion
	// The first array is the exponentials evaluated and the
	// second array is the number of bits to use for a non complete word
	$p = array( 
	    /* Base 11 */
	    array(6,array(1, 11, 121, 1331, 14641, 161051),array(4, 7, 11, 14, 18, 21)),
	    
	    /* Base 27 */
	    array(5,array(1, 27, 729, 19683, 531441),array(5, 10, 15, 20, 24)),

	    /* Base 37 */
	    array(4,array(1, 37, 1369, 50653),array(6, 11, 16, 21)),

	    /* Base 41 */
	    array(4,array(1, 41, 1681, 68921),array(6, 11, 17, 22)) 
	    );

	if( $this->iSelectSchema < ENCODING_ASCII ) {
	    $this->_Encode($aData,
			   $p[$this->iSelectSchema][0],
			   $p[$this->iSelectSchema][1],
			   $p[$this->iSelectSchema][2],
			   $aSymbols);
	}
	else {
	    $aSymbols = $aData;
	}
    }
}

?>
