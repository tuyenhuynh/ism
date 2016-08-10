<?php
include_once ("../irrigationScheduler.class.php");
$irrigationScheduler = new irrigationScheduler();
error_reporting(0);

unset($_SESSION['viewportwidth']);

$MyViewPort = 320;
if (isset ( $_REQUEST['viewportwidth'] )) {
	$MyViewPort = mysql_real_escape_string($_REQUEST['viewportwidth']);
}
$_SESSION['viewportwidth'] = $MyViewPort;
echo $MyViewPort;

?>