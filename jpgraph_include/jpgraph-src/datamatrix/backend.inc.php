<?php
/*=======================================================================
// File:        BACKEND.INC.PHP
// Description: All various output backends available
// Created:     2006-08-23
// Ver:         $Id: backend.inc.php 1481 2009-07-05 21:44:04Z ljp $
//
// Copyright (c) 2006 Asial Corporation. All rights reserved.
//========================================================================
*/

DEFINE('BACKEND_ASCII',0);
DEFINE('BACKEND_IMAGE',1);
DEFINE('BACKEND_PS',2);
DEFINE('BACKEND_EPS',3);

class BackendMatrix {
    protected $iDM = NULL;
    protected $iModWidth=2;
    protected $iInv=false;
    protected $iQuietZone = 0 ;
    protected $iError = 0 ;

    function BackendMatrix(&$aDataMatrixSpec) {
        $this->iDM = $aDataMatrixSpec;
    }

    function isCmdLine() {
        $s=php_sapi_name();
        return substr($s, 0, 3) == 'cli';
    }

    function fmtInfo($aS) {

        if ( !$this->isCmdLine() ) {
            return '<pre>' . $aS . '<pre>';
        }
        else return $aS;
    }

    function Stroke($aData, $aFileName='', $aDebug=false) {
    }

    function SetModuleWidth($aW) {
        $this->iModWidth = $aW;
    }

    function SetQuietZone($aW) {
        $this->iQuietZone = $aW;
    }

    function SetTilde($aFlg=true) {
        $this->iDM->SetTilde($aFlg);
    }

    function SetInvert($aFlg=true) {
        $this->iInv = $aFlg;
    }

    function GetError() {
        return $this->iError;
    }

    function SetColor($aOne,$aZero,$aBackground=array(255,255,255)) {
        return false;
    }
}

require_once('rgb_colors.inc.php');

class BackendMatrix_IMAGE extends BackendMatrix {
    private $iColor = array(array(0,0,0),array(255,255,255),array(255,255,255));
    private $iRGB = null;
    private $iImgFormat = 'png',$iQualityJPEG=75;

    function BackendMatrix_IMAGE(&$aDataMatrixSpec) {
        parent::BackendMatrix($aDataMatrixSpec);
        $this->iRGB = new Datamatrix_RGB();
    }

    function SetSize($aShapeIdx) {
        $this->iDM->SetSize($aShapeIdx);
    }

    function SetColor($aOne,$aZero,$aBackground=array(255,255,255)) {
        $this->iColor[0] = $aOne;
        $this->iColor[1] = $aZero;
        $this->iColor[2] = $aBackground;
    }

    // Specify image format. Note depending on your installation
    // of PHP not all formats may be supported.
    function SetImgFormat($aFormat,$aQuality=75) {
        $this->iQualityJPEG = $aQuality;
        $this->iImgFormat = $aFormat;
    }

    function PrepareImgFormat() {
        $format = strtolower($this->iImgFormat);
        if( $format == 'jpg' ) {
            $format = 'jpeg';
        }
        $tst = true;
        $supported = imagetypes();
        if( $format=="auto" ) {
            if( $supported & IMG_PNG )
                $this->iImgFormat="png";
            elseif( $supported & IMG_JPG )
                $this->iImgFormat="jpeg";
            elseif( $supported & IMG_GIF )
                $this->iImgFormat="gif";
            elseif( $supported & IMG_WBMP )
                $this->iImgFormat="wbmp";
            else {
                $this->iError = -15;
                return false;
            }
        }
        else {
            if( $format=="jpeg" || $format=="png" || $format=="gif" ) {
                if( $format=="jpeg" && !($supported & IMG_JPG) )
                    $tst=false;
                elseif( $format=="png" && !($supported & IMG_PNG) )
                    $tst=false;
                elseif( $format=="gif" && !($supported & IMG_GIF) )
                    $tst=false;
                elseif( $format=="wbmp" && !($supported & IMG_WBMP) )
                    $tst=false;
                else {
                    $this->iImgFormat=$format;
                }
            }
            else
                $tst=false;
            if( !$tst ) {
                $this->iError = -15;
                return false;
            }
        }
        return true;
    }

    function Stroke($aData,$aFileName='',$aDebug=false) {

        // Check the chosen graphic format
        if( !$this->PrepareImgFormat() ) {
            throw new DMExceptionL($this->iError);
        }

        $pspec = $this->iDM->Enc($aData);
        if( $pspec === false ) {
            $this->iError = $this->iDM->iError;
            throw new DMExceptionL($this->iError);
        }

        $mat = $pspec->iMatrix;
        $m = $this->iModWidth;

        $h = $pspec->iSize[0]*$m + 2*$this->iQuietZone;
        $w = $pspec->iSize[1]*$m + 2*$this->iQuietZone;

        $img = @imagecreatetruecolor($w,$h);
        if( !$img ) {
            $this->iError = -12;
            throw new DMExceptionL($this->iError);
        }

        $canvas_color  = $this->iRGB->Allocate($img,'white');
        $one_color  = $this->iRGB->Allocate($img,$this->iColor[0]);
        $zero_color = $this->iRGB->Allocate($img,$this->iColor[1]);
        $bkg_color  = $this->iRGB->Allocate($img,$this->iColor[2]);

        if( $canvas_color === false || $one_color === false || $zero_color === false || $bkg_color === false ) {
            $this->iError = -13;
            throw new DMExceptionL($this->iError);
        }

        imagefilledrectangle($img,0,0,$w-1,$h-1,$canvas_color);
        imagefilledrectangle($img,0,0,$w-1,$h-1,$bkg_color);

        for($i=0; $i < $pspec->iSize[0]-1; ++$i ) {
            for($j=1; $j < $pspec->iSize[1]; ++$j ) {
                $bit = $mat[$i][$j] == 1 ? $one_color : $zero_color;
                if( $m == 1 ) {
                    imagesetpixel($img,$j+$this->iQuietZone,$i+$this->iQuietZone,$bit);
                }
                else {
                    imagefilledrectangle($img,$j*$m+$this->iQuietZone,$i*$m+$this->iQuietZone,
                                         $j*$m+$m-1+$this->iQuietZone,$i*$m+$m-1+$this->iQuietZone,$bit);
                }
            }
        }

        // Left alignment line
        imagefilledrectangle($img,$this->iQuietZone,$this->iQuietZone,$this->iQuietZone+$m-1,$h-$this->iQuietZone-1,$one_color);

        // Bottom alignment line
        imagefilledrectangle($img,$this->iQuietZone,$h-$this->iQuietZone-$m,$w-$this->iQuietZone-1,$h-$this->iQuietZone-1,$one_color);

        if( headers_sent($file,$lineno) ) {
            $this->iError = 100;
            throw new DMExceptionL($this->iError,$file,$lineno);
        }

        if( $aFileName == '' ) {
            header("Content-type: image/$this->iImgFormat");
            switch( $this->iImgFormat ) {
            case 'png':
                $res = @imagepng($img);
                break;
            case 'jpeg':
                $res = @imagejpeg($img,NULL,$this->iQualityJPEG);
                break;
            case 'gif':
                $res = @imagegif($img);
                break;
            case 'wbmp':
                $res = @imagewbmp($img);
                break;
            }
        }
        else {
            switch( $this->iImgFormat ) {
            case 'png':
                $res = @imagepng($img,$aFileName);
                break;
            case 'jpeg':
                $res = @imagejpeg($img,$aFileName,$this->iQualityJPEG);
                break;
            case 'gif':
                $res = @imagegif($img,$aFileName);
                break;
            case 'wbmp':
                $res = @imagewbmp($img,$aFileName);
                break;
            }
        }
        return $res ;
    }
}

//--------------------------------------------------------------------------------
//       Class: BarcodeBackend_PS
// Description: Backend to generate postscript (or EPS) representation of the barcode
//--------------------------------------------------------------------------------

class BackendMatrix_PS extends BackendMatrix {

    private $iEPS = false;

    function __construct($aBarcodeEncoder) {
        parent::__construct($aBarcodeEncoder);
    }

    function SetEPS($aFlg=true) {
        $this->iEPS = $aFlg;
    }

    function Stroke($aData, $aFileName = '', $aDebug = false ) {

        $pspec = $this->iDM->Enc($aData);
        if( $pspec === false ) {
            $this->iError = $this->iDM->iError;
            throw new DMExceptionL($this->iError);
        }

        $m = $this->iModWidth;
        $nx = $pspec->iSize[1];
        $ny = $pspec->iSize[0];

        $h = $ny*$m + 2*$this->iQuietZone;
        $w = $nx*$m + 2*$this->iQuietZone;

        $ystart = $pspec->iSize[0]*$m + $this->iQuietZone;
        $xstart = $this->iQuietZone ;

        $psbar  = "%Data for bars. Only black bars are defined.\n" ;
        $psbar .= "%The figures are for each row and in format: [xpos]\n";

        /*
         if( is_array($aData)) {
         $data = " (manual encodation schemas) \n";
         $m = count($aData);
         for($i=0; $i < $m; $i++) {
         $data .= "%% (" . $aData[$i][0] . " : " . $aData[$i][1] . ")\n" ;
         }
         $aData = $data;
         }
        */

        $psbar .= "%Data: $aData";

        $y = $ystart;
        $psbar .= "\n";
        $psbar .= ($m+0.05)." setlinewidth\n";
        for( $r=0; $r < $ny ; ++$r, $y -= $m ) {
            $psbar .= '[';
            $x = $xstart;
            for( $i=0; $i < $nx; ++$i, $x += $m ) {
                if( $pspec->iMatrix[$r][$i] ) {
                    $psbar .= "[$x]";
                }
            }
            $psbar .= "] {{} forall $y moveto 0 -".($m+0.05)." rlineto stroke} forall\n";
        }
        $psbar .= "\n";
        $y += 4*$m;

        $psbar .= "%End of Datamatrix Barcode \n\n";
        if( !$this->iEPS )
            $psbar .= "showpage \n\n";
        $psbar .= "%%Trailer\n\n";

        $errStr = array('L', 'M', 'Q', 'H');
        $ps = ($this->iEPS ? "%!PS-Adobe EPSF-3.0\n" : "%!PS-Adobe-3.0\n" ) .
            "%%Title: Datamatrix Barcode \n".
            "%%Creator: JpGraph Barcode http://jpgraph.net/\n".
            "%%CreationDate: ".date("D j M H:i:s Y",time())."\n";

        if( $this->iEPS ) {
            $ps .= "%%BoundingBox: 0 0 $w $h\n";
        }
        else {
            $ps .= "%%DocumentPaperSizes: A4\n";
        }

        $ps .=
            "%%EndComments\n".
            "%%BeginProlog\n".
            "%%EndProlog\n\n".
            "%%Page: 1 1\n\n".
            "%Module width: $this->iModWidth pt\n\n";

        /*
         if( $this->iScale != 1 ) {
         $ps .=
         "%%Scale barcode\n".
         "$this->iScale $this->iScale scale\n\n";
         }
        */

        $ps = $ps.$psbar;

        if( $aFileName !== '' ) {
            $fp = @fopen($aFileName,'wt');
            if( $fp === FALSE ) {
                $this->iError = -34;
                throw new DMExceptionL($this->iError,$aFileName);
            }
            if( fwrite($fp,$ps) === FALSE ) {
                $this->iError = -35;
                throw new DMExceptionL($this->iError,$aFileName);
            }
            return fclose($fp);
        }
        else {
            return $ps;
        }
    }
}


class BackendMatrix_ASCII extends BackendMatrix {

    function BackendMatrix_ASCII(&$aDataMatrixSpec) {
        parent::BackendMatrix($aDataMatrixSpec);
    }

    function PrintMatrix($mat,$inv=false,$width=1,$aOne='1',$aZero='0') {

        if( $width > 1 ) {
            $m = count($mat);
            $n = count($mat[0]);
            $newmat = array();
            for($i=0; $i < $m; ++$i )
                for($j=0; $j < $n; ++$j )
                    for($k=0; $k < $width; ++$k )
                        for($l=0; $l < $width; ++$l )
                            $newmat[$i*$width+$k][$j*$width+$l] = $mat[$i][$j];
            $mat = $newmat;
        }

        $m = count($mat);
        $n = count($mat[0]);
        $s='';
        for($i=0; $i < $m; ++$i ) {
            for($j=0; $j < $n; ++$j ) {
                if( !$inv ) {
                    $s .= $mat[$i][$j] ? $aOne : $aZero;
                }
                else {
                    if( $mat[$i][$j] )
                        $s .=  $aZero;
                    else
                        $s .=  $aOne;
                }
            }
            $s .=  "\n";
        }
        $s .=  "\n";
        return $s;
    }

    function PrintBits($bits,$chunk=4) {
        $n = count($bits);
        $cnt=0;$nibs=0;
        for( $i=0; $i < $n; ++$i ) {
            echo $bits[$i];
            $cnt ++;
            if( $cnt == $chunk ) {
                echo " ";
                $cnt = 0 ;
                $nibs++;
                if( $nibs == 22 ) {
                    $nibs=0;
                    echo "\n";
                }
            }
        }
    }

    function Stroke($aData, $aFileName = '', $aDebug = false) {
        $pspec = $this->iDM->Enc($aData);
        $s = '';
        if( $aDebug ) {
            $s .= "-------------- BACKEND ASCII ---------------\n";
            $s .= "Matrix size: {$pspec->iSize[0]}*{$pspec->iSize[1]}\n";
            if( $pspec->iType == DM_TYPE_140 ) {
                $errlevels = array('None','ECC_050','ECC_080','ECC_100','ECC_140');
                $s .=  "Error 000 - 140 level: {$errlevels[$pspec->iErrLevel]}\n";
            }
        }

        $s .= $this->PrintMatrix($pspec->iMatrix,$this->iInv,$this->iModWidth,'X','_');

        if( $aFileName !== '' ) {
            $fp = @fopen($aFileName,'wt');
            if( $fp === FALSE ) {
                $this->iError = -34;
                throw new DMExceptionL($this->iError,$aFileName);
            }
            if( fwrite($fp,$s) === FALSE ) {
                $this->iError = -35;
                throw new DMExceptionL($this->iError,$aFileName);
            }
            return fclose($fp);
        }
        else {
            return $this->fmtInfo($s);
        }
    }
}

class DatamatrixBackendFactory {
    static function Create(&$aDMSpec,$aBackend=BACKEND_IMAGE) {
        switch( $aBackend ) {
        case BACKEND_ASCII:
            return new BackendMatrix_ASCII($aDMSpec);
            break;
        case BACKEND_IMAGE:
            return new BackendMatrix_Image($aDMSpec);
            break;
        case BACKEND_PS:
            return new BackendMatrix_PS($aDMSpec);
            break;
        case BACKEND_EPS:
            $bps = new BackendMatrix_PS($aDMSpec);
            $bps->SetEPS();
            return $bps;
            break;
        default:
            return false;
        }
    }
}

?>
