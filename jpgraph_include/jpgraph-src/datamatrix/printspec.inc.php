<?php
/*=======================================================================
// File: 	PRINTSPEC.INC.PHP
// Description:	Define the common print specification
// Created: 	2006-08-23
// Ver:		$Id: printspec.inc.php 988 2008-03-25 02:50:13Z ljp $
//
// Copyright (c) 2006 Asial Corporation. All rights reserved.
//========================================================================
*/


DEFINE("DM_TYPE_140",1);
DEFINE("DM_TYPE_200",2);


class PrintSpecification {
    public $iMatrix = array();
    public $iType = -1;
    public $iData = array();
    public $iDataLen = 0;
    public $iSize = array();
    public $iErrLevel = 0 ;
    public $iEncoding = 0;

    function PrintSpecification($aType,$aData,$aMat,$aEncoding,$aErrLevel=-1) {
	$this->iType = $aType;
	$this->iData = $aData;
	$this->iDataLen = count($aData);
	$this->iMatrix = $aMat;
	$this->iSize[0] = count($aMat);
	$this->iSize[1] = count($aMat[0]);
	$this->iEncoding = $aEncoding;
	$this->iErrLevel = $aErrLevel;
    }
}

?>
