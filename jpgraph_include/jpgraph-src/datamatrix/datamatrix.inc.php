<?php
/*=======================================================================
// File: 	DATAMATRIX.INC.PHP
// Description:	Main Datamatrix encoding class
// Created: 	2006-08-29
// Ver:		$Id: datamatrix.inc.php 1070 2008-09-07 08:56:03Z ljp $
//
// Copyright (c) 2006 Asial Corporation. All rights reserved.
//========================================================================
*/

require_once('dmexception.inc.php');
require_once('dm-utils.inc.php');
require_once('datamatrix-200.inc.php');
require_once('printspec.inc.php');
require_once('backend.inc.php');

// Which of the two basic types of Datmatrix
DEFINE('DATAMATRIX_TYPE140',1);
DEFINE('DATAMATRIX_TYPE200',2);

// Sizes for type ECC 200 datamatrix codes (all even number)
DEFINE('DMAT_AUTO',-1);
DEFINE('DMAT_10x10',0);
DEFINE('DMAT_12x12',1);
DEFINE('DMAT_14x14',2);
DEFINE('DMAT_16x16',3);
DEFINE('DMAT_18x18',4);
DEFINE('DMAT_20x20',5);
DEFINE('DMAT_22x22',6);
DEFINE('DMAT_24x24',7);
DEFINE('DMAT_26x26',8);
DEFINE('DMAT_32x32',9);
DEFINE('DMAT_36x36',10);
DEFINE('DMAT_40x40',11);
DEFINE('DMAT_44x44',12);
DEFINE('DMAT_48x48',13);
DEFINE('DMAT_52x52',14);
DEFINE('DMAT_64x64',15);
DEFINE('DMAT_72x72',16);
DEFINE('DMAT_80x80',17);
DEFINE('DMAT_88x88',18);
DEFINE('DMAT_96x96',19);
DEFINE('DMAT_104x104',20);
DEFINE('DMAT_120x120',21);
DEFINE('DMAT_132x132',22);
DEFINE('DMAT_144x144',23);
DEFINE('DMAT_8x18',24);
DEFINE('DMAT_8x32',25);
DEFINE('DMAT_12x26',26);
DEFINE('DMAT_12x36',27);
DEFINE('DMAT_16x36',28);
DEFINE('DMAT_16x48',29);

// Sizes for ECC140 type matrices. Use an offset of 50 to differentiate 
// the numeric value from the ECC200 to discover user mistakes of trying
// to specify the wrong size.
DEFINE('DMAT140_AUTO',-1);
DEFINE('DMAT140_9x9',0+50);
DEFINE('DMAT140_11x11',1+50);
DEFINE('DMAT140_13x13',2+50);
DEFINE('DMAT140_15x15',3+50);
DEFINE('DMAT140_17x17',4+50);
DEFINE('DMAT140_19x19',5+50);
DEFINE('DMAT140_21x21',6+50);
DEFINE('DMAT140_23x23',7+50);
DEFINE('DMAT140_25x25',8+50);
DEFINE('DMAT140_27x27',9+50);
DEFINE('DMAT140_29x29',10+50);
DEFINE('DMAT140_31x31',11+50);
DEFINE('DMAT140_33x33',12+50);
DEFINE('DMAT140_35x35',13+50);
DEFINE('DMAT140_37x37',14+50);
DEFINE('DMAT140_39x39',15+50);
DEFINE('DMAT140_41x41',16+50);
DEFINE('DMAT140_43x43',17+50);
DEFINE('DMAT140_45x45',18+50);
DEFINE('DMAT140_47x47',19+50);
DEFINE('DMAT140_49x49',20+50);

// Encoding types for ECC 200
DEFINE('ENCODING_C40',0);
DEFINE('ENCODING_TEXT',1);
DEFINE('ENCODING_X12',2);
DEFINE('ENCODING_EDIFACT',3);
DEFINE('ENCODING_ASCII',4);
DEFINE('ENCODING_BASE256',5);
DEFINE('ENCODING_AUTO',6);

// Encoding types for ECC 140
DEFINE('ENCODING_BASE11',0);
DEFINE('ENCODING_BASE27',1);
DEFINE('ENCODING_BASE37',2);
DEFINE('ENCODING_BASE41',3);
// DEFINE('ENCODING_ASCII',4); We reuse the define for ECC-200
DEFINE('ENCODING_BYTE',5);


class DatamatrixFactory {
    static function Create($aSize=-1,$aType=DATAMATRIX_TYPE200,$aDebug=false) {
	switch( $aType ) {
	    case DATAMATRIX_TYPE140:
		return new Datamatrix_140($aSize,$aDebug);
	    break;
	    
	    case DATAMATRIX_TYPE200:
		return new Datamatrix($aSize,$aDebug);
	    break;
	    
	    default:
		return false;

	}
    }
}

?>
