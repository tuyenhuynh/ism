<?php

	require_once('irrigationScheduler.class.php');
	if(isset($_SESSION['field']))
	{
		$irrigationScheduler = new irrigationScheduler( $_SESSION['field']);
	}
	else
	{
		$irrigationScheduler = new irrigationScheduler( );
	}
	
	$page = $irrigationScheduler->toHTML();
	echo $page;
?>