<?php
	class htmlFactory
	{
		private $Current_Indent;  //Current indent for HTML Formatting (disabled)
		public $GrowerID = "-1"; //Database ID of the Grower
		public $FieldID = "-1";  //Database ID of the Field
		private $NetworkID = "-1"; //Database ID of the Network
		private $CropID = "-1";  //Database ID of the Crop
		private $SoilID = "-1"; //Database ID of the Soil
		private $IndFieldID = "-1"; //Database ID of the individual field
		private $StationID = "-1"; //Database ID of the station
		private $previous = ""; 	//URL of the previously viewed page
		public $fCount = 0; //Count of the number of fields for current growerID

		function __construct() {
			$this->selectVariables();
		}
		
		public function selectVariables()
		{
			global $irrigationScheduler;
			global $database;
			
			if (isset($_SESSION['growerID'])) { //if successfully logged in the will be a grower ID
			  $this->GrowerID = $_SESSION['growerID'];
			  $this->fCount = $database->countFields($this->GrowerID);
			}

			if(isset($_POST['field'])){ //if we posted a new field ID, set the session value
				$_SESSION['field'] = GetSQLValueString($_POST['field'],"int");
			}
			if (isset($_SESSION['field']) && strlen($_SESSION['field']) > 0) {  //if we have a session field ID, then set it for use
				$database->setDefaultField($this->GrowerID,$_SESSION['field']);

			  $this->FieldID = $_SESSION['field'];
			}
	
			if(isset($_POST['weather_network'])) //if we posted a new weather_network ID, set the session value
				$_SESSION['weather_network'] = GetSQLValueString($_POST['weather_network'],"int");
			if (isset($_SESSION['weather_network'])) {  //if we have a session weather_network ID, then set it for use
			  $this->NetworkID = $_SESSION['weather_network'];
			}
			
			if(isset($_POST['crop'])) //if we posted a new crop ID, set the session value
				$_SESSION['crop'] = GetSQLValueString($_POST['crop'],"int");
			if (isset($_SESSION['crop'])) {  //if we have a session crop ID, then set it for use
			  $this->CropID = $_SESSION['crop'];
			}
	
			if(isset($_POST['soil-type'])) //if we posted a new soil-type ID, set the session value
				$_SESSION['soil-type'] = GetSQLValueString($_POST['soil-type'],"int");
			if (isset($_SESSION['soil-type'])) {  //if we have a session soil-type  ID, then set it for use
			  $this->SoilID = $_SESSION['soil-type'];
			}
		
			if(isset($_POST['station'])) //if we posted a new station ID, set the session value
				$_SESSION['station'] = GetSQLValueString($_POST['station'],"int");
			if (isset($_SESSION['station'])) {  //if we have a session station ID, then set it for use
			  $this->StationID = $_SESSION['station'];
			}
	
			//keep track of the previous page for "back" button purposes
			if(isset($_SESSION['previouspage']))
			{
				$this->previous = $_SESSION['previouspage'];
			}
			//Could be set for HTML formatting
			//$this->CurrentIndent = 0;
		}
		//Build HTML Header, include jquery scripts if needed
	 	private function createHeader($myAction = "", $title = "Irrigation Scheduler - Mobile")
	 	{
			global $irrigationScheduler;
			$jqueryActions = array('dashboard','daily-budget-existing-fields','advanced-update-field','add-a-field', 'advanced-fields-update');
			$fusionActions = array('dashboard');

			$createHeader = "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">";
			$createHeader .= "<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"en\" lang=\"en\">";
			$createHeader .= "<head>";
			$createHeader .= "<script type='text/javascript'>    var _gaq = _gaq || [];   _gaq.push(['_setAccount', 'UA-25237394-1']);   _gaq.push(['_trackPageview']);    (function() {     var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;      ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';     var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);   })();  </script> ";
			$createHeader .= "<meta http-equiv=\"content-type\" content=\"text/html; charset=UTF-8\" />";
			$createHeader .= "<meta name=\"viewport\" content=\"width=device-width, user-scalable=no\" />";
			$createHeader .= "<meta name=\"apple-mobile-web-app-capable\" content=\"yes\" />";
			$createHeader .= "<title>".$title."</title>";
			$createHeader .= "<link href=\"html/css/styles.php\" rel=\"stylesheet\" type=\"text/css\" />";
			$createHeader .= "<link rel='stylesheet' type='text/css' href='jquery-ui-1.8.17.custom/css/smoothness/jquery-ui-1.8.17.custom.css' />".PHP_EOL;
			$createHeader .= "<link rel='apple-touch-icon' href='ic_launcher-web.png' />";
			$createHeader .= "<link rel='shortcut icon' href='/images/favicon.ICO' />";

			//If the action requires jquery, or if it is full screen (i.e. we don't care about bandwidth too much)
			if(true) //in_array($myAction,$jqueryActions) || $irrigationScheduler->session->isMobileDevice <= 0)
			{
				$createHeader .= "<script type='text/javascript' src='jquery-ui-1.8.17.custom/js/jquery-1.7.1.min.js'></script>".PHP_EOL;
				$createHeader .= "<script type='text/javascript' src='jquery-ui-1.8.17.custom/js/jquery-ui-1.8.17.custom.min.js'></script>".PHP_EOL;	
			}
			if(in_array($myAction,$fusionActions) || $irrigationScheduler->session->isMobileDevice <= 0)
				$createHeader .= "<script type='text/javascript' src='/FusionWidgets/FusionCharts.js'>    </script>";
			$createHeader .= "</head>";
			return $createHeader;
		} //createHeader
		
		//Open the HTML Body, the container div, and create the img header
		private function openBody()
		{
			global $irrigationScheduler;
			$openBody = "<body>";
			$openBody .= "<div id='container' class=\"container\">";
				$openBody .= "<div class=\"header\">";
				$openBody .= "<center><a href=\"".$irrigationScheduler->session->basepath."\"><img src=\"images/error-404_02.gif\" alt=\"Irrigation Scheduler - Mobile\" width=\"320\" height=\"45\" id=\"Insert_logo\" style=\"background: #8090AB; display:block;\" /></a></center>";
				$openBody .= "</div>";
			return $openBody;
		}

		// insert the global navigation links, 
		private function closeBody($currentAction = "")
		{
			if(isset($_REQUEST['action']))
			{
				$currentAction = $_REQUEST['action'];
			}
			global $irrigationScheduler;

			$closeBody = "";
			//Include global navigation if they are logged in
			if($irrigationScheduler->session->logged_in)
			{
				$closeBody .= "<div style='position:relative;'><span style='padding:0px;margin:0px;font-size:1%'></span>";
				$closeBody .= $this->globalNavigation($currentAction);
				$closeBody .= "</div>";
			}
			
			//Include the footer no matter if they are logged in
			$closeBody .= "<div style='position:relative;clear:both;'>";
			$closeBody .= "<div class=\"footer\">";
			$closeBody .= $this->footer();
			$closeBody .= "</div>";
			$closeBody .= "</div>";

			//Close .container div 
			$closeBody .= "</div>";

			$closeBody .= "<script type='text/javascript'>".PHP_EOL;

			$closeBody .= "$(document).ready(function(){ ".PHP_EOL;
			$closeBody .= " $(window).on('click','a',function() { ";
			$closeBody .= " if(typeof($(this).attr('target')) == 'undefined') { ";
			$closeBody .= " if($(this).attr('href').indexOf('java') < 0) { ";
			$closeBody .= " document.location.href = $(this).attr('href'); ";
			$closeBody .= " return false; ";
			$closeBody .= " } ";
			$closeBody .= " } ";
			$closeBody .= "}); ";
			$closeBody .= "}); ";
			$closeBody .= "</script>";

			//Close HTML Body
			$closeBody .= "</body>";
			//Close HTML
			$closeBody .= "</html>".PHP_EOL;

			return $closeBody;
		}

		//Create global navigation links for logged in users, takes $currentAction ($_REQUEST['action']) to determine which links needs to be visible
	 	public function globalNavigation($currentAction = "")
	 	{
	 		global $irrigationScheduler;
	 		$globalNavigation = "";
	 		
	 		//If the user is on a mobile device or in mobile format
	 		if($irrigationScheduler->session->isMobileDevice > 0)
	 		{
	 			//Apply globalNavigation formatting to the globalNavigation div
		 		$globalNavigation .= "<div class='globalNavigation'>";
		 		//The links are in a list <UL> <LI>....</UL>
		 		$globalNavigation .= "<ul>";
				//If there is currently a field selected
		 		if($this->FieldID > 0)
		 		{
					//Allow the user to go to dashboard					
			 		$globalNavigation .= "<li class='mainNavigation'>";
			 		$globalNavigation .= "<a href='".$irrigationScheduler->session->basepath."&amp;action=dashboard'>";
			 		$globalNavigation .= "<div onmouseover='this.style.cursor=\"pointer\";'  onmouseout='this.style.cursor=\"default\";'  style='width:100%'><img height='18px' style='vertical-align:middle' src='ic_launcher-web.png'>";
			 		$globalNavigation .= "&nbsp;Dashboard";
			 		$globalNavigation .= "</div></a></li>";
					//Allow the user to go to daily budget table
			 		$globalNavigation .= "<li class=\"mainNavigation\">";
			 		$globalNavigation .= "<a href='".$irrigationScheduler->session->basepath."&amp;action=daily-budget-existing-fields'><div onmouseover='this.style.cursor=\"pointer\";'  onmouseout='this.style.cursor=\"default\";'  style='width:100%'><img height='18px' style='vertical-align:middle' src='/images/Correcttable.png'>&nbsp;Daily Budget Table</div></a></li>";
			 		//Allow the user to go to soil water chart
			 		$globalNavigation .= "<li class=\"chartNavigation\">";
			 		$globalNavigation .= "<a href='".$irrigationScheduler->session->basepath."&amp;action=soil-water-chart'><div onmouseover='this.style.cursor=\"pointer\";'  onmouseout='this.style.cursor=\"default\";'  style='width:100%'><img height='18px' style='vertical-align:middle' src='/images/stats1.gif'>&nbsp;Soil Water Chart</div></a></li>";
	
					//If they have not clicked on "more charts" then don't display all the charts
					$thisStyle = 'display:none;';
					$thisLabel = 'More Charts';
					//Use the chart+ image
					$thisIcon = "images/chart--plus.png";

					//If they have clicked on "more charts" then we need to change the label and display the links
					if(in_array($currentAction,array('ET-chart','cumulative-water-chart','crop-coefficient-chart','deep-percolation-chart','water-stress-coefficient-chart')))
					{
						$thisStyle = 'display:block;';
						$thisLabel = 'Less Charts';
						//Use the chart- image
						$thisIcon = "images/chart--minus.png";
					}

					//Show the link for "more charts" or "less charts" depending on user action
			 		$globalNavigation .= "<li class=\"globalNavigation\">";
			 		$globalNavigation .= "<a id='chartLink' href='javascript:void(0);' onclick='toggleCharts(this);'><div onmouseover='this.style.cursor=\"pointer\";'  onmouseout='this.style.cursor=\"default\";'  style='width:100%;'><img id='mcimg' height='18px'  style=\"vertical-align:middle\" src='$thisIcon'>&nbsp;".$thisLabel."</div></a></li>";
					
					//set up the toggleCharts javascript to toggle between "more chart" and "less charts"
					$globalNavigation .= "<script type='text/javascript'>".PHP_EOL;
					$globalNavigation .= "function toggleCharts(aLink){ ";
					//if the currently shown value is "more charts" (the charts are not displayed)
					$globalNavigation .= "if (document.getElementById('moreCharts').style.display == 'none'){ ";
					//then switch it and show all the chart links
					$globalNavigation .= "document.getElementById('moreCharts').style.display='block'; ";
					//and change the chartLink innerhtml to show "less charts"
					$globalNavigation .= "document.getElementById('chartLink').innerHTML='<div onmouseover=\"this.style.cursor=\'pointer\';\"  onmouseout=\"this.style.cursor=\'default\';\"  style=\"width:100%\"><img id=\"mcimg\" height=\"18px\" style=\"vertical-align:middle\" src=\"images/chart--minus.png\"> Less Charts</div>'; ";
					$globalNavigation .= "} else { ";
					//otherwise it currently shows "less charts"
					//so hide the charts
					$globalNavigation .= "document.getElementById('moreCharts').style.display='none'; ";
					//and change the label to "more charts"
					$globalNavigation .= "document.getElementById('chartLink').innerHTML='<div onmouseover=\"this.style.cursor=\'pointer\';\"  onmouseout=\"this.style.cursor=\'default\';\"  style=\"width:100%\"><img id=\"mcimg\" height=\"18px\" style=\"vertical-align:middle\"  src=\"images/chart--plus.png\"> More Charts</div>'; ";
					$globalNavigation .= "} ";
					$globalNavigation .= "}		 ";
					$globalNavigation .= "</script> ".PHP_EOL;
					//End of toggleCharts javascript
	
					//create the section where the rest of the chart links go, and apply the current style
			 		$globalNavigation .= "<div id='moreCharts' style='".$thisStyle."'>";
			 		//Daily water use chart
			 		$globalNavigation .= "<li class=\"globalNavigation\">";
			 		$globalNavigation .= "<a href='".$irrigationScheduler->session->basepath."&amp;action=ET-chart'><div onmouseover='this.style.cursor=\"pointer\";'  onmouseout='this.style.cursor=\"default\";'  style='width:100%'><img height='18px'  style=\"vertical-align:middle\" src='/images/irrigation/chart.png'>&nbsp;Daily Water Use Chart</div></a></li>";
					//Cumulative water use chart
			 		$globalNavigation .= "<li class=\"globalNavigation\">";
			 		$globalNavigation .= "<a href='".$irrigationScheduler->session->basepath."&amp;action=cumulative-water-chart'><div onmouseover='this.style.cursor=\"pointer\";'  onmouseout='this.style.cursor=\"default\";'  style='width:100%'><img height='18px'  style=\"vertical-align:middle\" src='/images/irrigation/chart.png'>&nbsp;Cumulative Water Chart</div></a></li>";
			 		//Crop Coefficient chart 
			 		$globalNavigation .= "<li class=\"globalNavigation\">";
			 		$globalNavigation .= "<a href='".$irrigationScheduler->session->basepath."&amp;action=crop-coefficient-chart'><div onmouseover='this.style.cursor=\"pointer\";'  onmouseout='this.style.cursor=\"default\";'  style='width:100%'><img height='18px'  style=\"vertical-align:middle\" src='/images/irrigation/chart.png'>&nbsp;Crop Coefficient Chart</div></a></li>";
			 		//Deep water loss chart
			 		$globalNavigation .= "<li class=\"globalNavigation\">";
			 		$globalNavigation .= "<a href='".$irrigationScheduler->session->basepath."&amp;action=deep-percolation-chart'><div onmouseover='this.style.cursor=\"pointer\";'  onmouseout='this.style.cursor=\"default\";'  style='width:100%'><img height='18px'  style=\"vertical-align:middle\" src='/images/irrigation/chart.png'>&nbsp;Deep Water Loss Chart</div></a></li>";
			 		//Water Stress Chart
			 		$globalNavigation .= "<li class=\"globalNavigation\">";
			 		$globalNavigation .= "<a href='".$irrigationScheduler->session->basepath."&amp;action=water-stress-coefficient-chart'><div onmouseover='this.style.cursor=\"pointer\";'  onmouseout='this.style.cursor=\"default\";'  style='width:100%'><img height='18px'  style=\"vertical-align:middle\" src='/images/irrigation/chart.png'>&nbsp;Water Stress Chart</div></a></li>";
			 		$globalNavigation .= "</div>";
			 		//End of expandable "more charts" section

		 			//Apply inputNavigation style to the field settings link
		 			$globalNavigation .= "<li class=\"inputNavigation\" >";
			 		$globalNavigation .= "<a href='".$irrigationScheduler->session->basepath."&amp;action=advanced-update-field'><div onmouseover='this.style.cursor=\"pointer\";'  onmouseout='this.style.cursor=\"default\";' style='width:100%;'><img height='18px' style='vertical-align:middle;' src='/images/irrigation/gear.png'>&nbsp;Field Settings</div></a></li>";
			 	}
				//Apply inputNavigation style to the "Add/Delete Fields" link
	 			$globalNavigation .= "<li class=\"inputNavigation\" >";
			 	$globalNavigation .= "<a href='".$irrigationScheduler->session->basepath."&amp;action=my-fields'><div onmouseover='this.style.cursor=\"pointer\";'  onmouseout='this.style.cursor=\"default\";' style='width:100%;'><img height='16px' style='vertical-align:middle;' src='/images/irrigation/plus.png'><img height='16px' style='vertical-align:middle;' src='/images/irrigation/minus.png'>&nbsp;Add/Delete Fields</div></a></li>";
			 	$globalNavigation .= "</ul> ";   
			 	//End of list of links
			 	$globalNavigation .= "</div>";
			 	//End of globalNavigation div
			}    		
			else
			{
				//if it is not mobile version, then do this
				if(isset($_SESSION['fbversion']) && $_SESSION['fbversion'])
				{
					$stationTabs = "<div style=\"width:100%;clear:both\"><form action=\"\" method=\"post\" name=\"StationTabsForm\">";
				}
				else
				{
					$stationTabs = "<div style=\"width:100%;clear:both\"><form action=\"".$irrigationScheduler->session->basepath."\" method=\"post\" name=\"StationTabsForm\">";
				}
		 		$stationTabs .=  $this->selectField(true,"StationTabsForm");
		 		$stationTabs .= "</form></div>";
		 		 	
		 		$stationTabs .= "<div id='example' class='ui-tabs' style='clear:both'>";
		 		$stationTabs .= "<ul>";
		 		if($irrigationScheduler->FieldID > 0)
		 		{
			 		$stationTabs .= "<li><a style='background-color:#3cb6cd' href=\"/is/ajaxPage.php?action=daily-budget-existing-fields\"><span><b><img height='18px' style='vertical-align:middle' src='/images/Correcttable.png'>&nbsp;Daily Budget Table</b></span></a></li>";
			 		$stationTabs .= "<li><a style='background-color:#33BB44' href=\"/is/ajaxPage.php?action=soil-water-chart\"><span><b><img height='18px' style='vertical-align:middle' src='/images/stats1.gif'>&nbsp;Soil Water Chart</b></span></a></li>";
			 		$stationTabs .= "<li><a style='background-color:#acd373' href=\"/is/ajaxPage.php?action=ET-chart\"><span><b><img height='18px'  style=\"vertical-align:middle\" src='/images/irrigation/chart.png'>&nbsp;Daily Water Use Chart</b></span></a></li>";
			 		$stationTabs .= "<li><a style='background-color:#acd373' href=\"/is/ajaxPage.php?action=cumulative-water-chart\"><span><b><img height='18px'  style=\"vertical-align:middle\" src='/images/irrigation/chart.png'>&nbsp;Cumulative Water Chart</b></span></a></li>";
			 		$stationTabs .= "<li><a style='background-color:#acd373' href=\"/is/ajaxPage.php?action=crop-coefficient-chart\"><span><b><img height='18px'  style=\"vertical-align:middle\" src='/images/irrigation/chart.png'>&nbsp;Crop Coefficient Chart</b></span></a></li>";
			 		$stationTabs .= "<li><a style='background-color:#acd373' href=\"/is/ajaxPage.php?action=deep-percolation-chart\"><span><b><img height='18px'  style=\"vertical-align:middle\" src='/images/irrigation/chart.png'>&nbsp;Deep Water Loss Chart</b></span></a></li>";
		 			$stationTabs .= "<li><a style='background-color:#acd373' href=\"/is/ajaxPage.php?action=water-stress-coefficient-chart\"><span><b><img height='18px'  style=\"vertical-align:middle\" src='/images/irrigation/chart.png'>&nbsp;Water Stress Chart</b></span></a></li>";
					$stationTabs .= "<li><a style='background-color:#3cb6cd' href=\"/is/ajaxPage.php?action=advanced-update-field\"><span><b><img height='18px' style='vertical-align:middle;' src='/images/irrigation/gear.png'>&nbsp;Field Settings</b></span></a></li>";
				}
		 		$stationTabs .= "<li><a style='background-color:#3cb6cd' href=\"/is/ajaxPage.php?action=add-a-field\"><span><b><img height='16px' style='vertical-align:middle;' src='/images/irrigation/plus.png'><img height='16px' style='vertical-align:middle;' src='/images/irrigation/minus.png'>&nbsp;Add/Delete Fields</b></span></a></li>";
		 		if($irrigationScheduler->FieldID > 0)
		 		{
	 				$stationTabs .= "<li><a  style='background-color:#3cb6cd'  href=\"/is/ajaxPage.php?action=activity\"><span><b>Field Activity</b></span></a></li>";
	 			}
		 		if(in_array($irrigationScheduler->session->username, array('sehill','tpeters') ))
		 		{
		 			$stationTabs .= "<li><a href=\"/is/ajaxPage.php?action=statistics\"><span>IS Statistics</span></a></li>";
		 		}
		 		$stationTabs .= "</ul>";
		 		$stationTabs .= "</div>";
				$stationTabs .= "<script type='text/javascript'>".PHP_EOL;
				$stationTabs .= "$(document).ready(function(){ ".PHP_EOL;
				$stationTabs .= "$(function() {".PHP_EOL;
				$stationTabs .= "$(this).ajaxStart(function() {".PHP_EOL;
				$stationTabs .= "$.fancybox.showActivity(); });".PHP_EOL;
				$stationTabs .= "$(this).ajaxStop(function() {".PHP_EOL;
				$stationTabs .= "$.fancybox.hideActivity(); });".PHP_EOL;
				$stationTabs .= "}); ".PHP_EOL;
				$stationTabs .= "$(function() {".PHP_EOL;
				$stationTabs .= "$( \"#example\" ).tabs({ });".PHP_EOL;
				$stationTabs .= "});".PHP_EOL;
				$stationTabs .= "$.fancybox.hideActivity();".PHP_EOL;
				$stationTabs .= "});".PHP_EOL;
				$stationTabs .= "</script> ".PHP_EOL;
	
				$globalNavigation .= $stationTabs;
			}
	 		return $globalNavigation;
	 	}
	 	public function footer()
	 	{
	 		global $irrigationScheduler;
	 		$footer = "";

			//Make the first 3 links in the footer
	 		$footer .= "<ul>";
				//Privacy link
		 		$footer .= "<li><a href=\"".$irrigationScheduler->session->basepath."&amp;action=privacy\">Privacy</a></li>";
		 		//About us link
		 		$footer .= "<li><a href=\"".$irrigationScheduler->session->basepath."&amp;action=about-us\">About Us</a>&nbsp;</li>";
		 		//Contact us link
		 		$footer .= "<li><a href=\"".$irrigationScheduler->session->basepath."&amp;action=contact-us\">Contact Us</a></li>";
	 		$footer .= "</ul>";
			//Make a second row of links
	 		$footer .= "<ul>";
			if($irrigationScheduler->session->logged_in)
			{
				//If they are logged in allow them to log out
		 		$footer .= "<li><a href=\"".$irrigationScheduler->session->processPath."\">Logout</a></li>";
			}
			else
			{
				//if they are not logged in, allow them to log in
		 		$footer .= "<li><a href=\"".$irrigationScheduler->session->basepath."\">Log In</a></li>"; 
			}
			//Desktop website link
 			$footer .= "<li><a target='_blank' href=\"http://weather.wsu.edu/awn.php?page=irrigation-scheduler\">Desktop Website</a></li>";
 			//ISMManualOptimized.pdf link 
 			$footer .= "<li><a target='_blank' href=\"http://weather.wsu.edu/ism/ISMManualOptimized.pdf\">Help</a></li>";
			$footer .= "</ul>";
			//End of footer links
	    
	 		return $footer;
	 	}

		//Depending on what "action" or "help" has been posted to the server, display the proper page
		public function displayPage()
		{				
	 		global $irrigationScheduler;
	 		global $database;
	 		$retVal = "";
			//create an array of all the possible "action" or "help" values
			//this array contains the filename and classname to be loaded when requested
			$pageInformation = array();
			$pageInformation['loginForm'] = array('file' => 'html/loginForm.class.php','class' => 'loginForm');
			$pageInformation['welcome'] = array('file' => 'html/welcome.class.php','class' => 'welcome');

			$pageInformation['daily-budget-existing-fields'] = array('file' => 'html/dailyBudgetTable.class.php','class' => 'dailyBudgetTable');
			$pageInformation['daily-budget-existing-fields-help'] = array('file' => 'html/dailyBudgetTable.help.class.php','class' => 'dailyBudgetTable');

			$pageInformation['soil-water-chart'] = array('file' => 'html/soilWaterChart.class.php','class' => 'soilWaterChart');
			$pageInformation['soil-water-chart-help'] = array('file' => 'html/soilWaterChart.help.class.php','class' => 'soilWaterChart');

			$pageInformation['ET-chart'] = array('file' => 'html/dailyWaterUseChart.class.php','class' => 'dailyWaterUseChart');
			$pageInformation['ET-chart-help'] = array('file' => 'html/dailyWaterUseChart.help.class.php','class' => 'dailyWaterUseChart');

			$pageInformation['cumulative-water-chart'] = array('file' => 'html/cumulativeWaterUseChart.class.php','class' => 'cumulativeWaterUseChart');
			$pageInformation['cumulative-water-chart-help'] = array('file' => 'html/cumulativeWaterUseChart.help.class.php','class' => 'cumulativeWaterUseChart');

			$pageInformation['crop-coefficient-chart'] = array('file' => 'html/cropCoefficientChart.class.php','class' => 'cropCoefficientChart');
			$pageInformation['crop-coefficient-chart-help'] = array('file' => 'html/cropCoefficientChart.help.class.php','class' => 'cropCoefficientChart');

			$pageInformation['deep-percolation-chart'] = array('file' => 'html/deepWaterLossChart.class.php','class' => 'deepWaterLossChart');
			$pageInformation['deep-percolation-chart-help'] = array('file' => 'html/deepWaterLossChart.help.class.php','class' => 'deepWaterLossChart');

			$pageInformation['water-stress-coefficient-chart'] = array('file' => 'html/waterStressChart.class.php','class' => 'waterStressChart');
			$pageInformation['water-stress-coefficient-chart-help'] = array('file' => 'html/waterStressChart.help.class.php','class' => 'waterStressChart');

			$pageInformation['advanced-update-field'] = array('file' => 'html/fieldSettings.class.php','class' => 'fieldSettings');
			$pageInformation['advanced-update-field-help'] = array('file' => 'html/fieldSettings.help.class.php','class' => 'fieldSettings');

			$pageInformation['my-fields'] = array('file' => 'html/addDeleteFields.class.php','class' => 'addDeleteFields');
			$pageInformation['my-fields-help'] = array('file' => 'html/addDeleteFields.help.class.php','class' => 'addDeleteFields');

			$pageInformation['add-a-field'] = array('file' => 'html/addField.class.php','class' => 'addField');
			$pageInformation['add-a-field-help'] = array('file' => 'html/addField.help.class.php','class' => 'addField');

			$pageInformation['delete-field'] = array('file' => 'html/addDeleteFields.class.php','class' => 'addDeleteFields');

			$pageInformation['field-added'] = array('file' => 'html/fieldAdded.class.php','class' => 'fieldAdded');
			$pageInformation['privacy'] = array('file' => 'html/privacy.class.php','class' => 'privacy');
			$pageInformation['about-us'] = array('file' => 'html/aboutUs.class.php','class' => 'aboutUs');
			$pageInformation['contact-us'] = array('file' => 'html/contactUs.class.php','class' => 'contactUs');
			
			$pageInformation['dashboard'] = array('file' => 'html/dashboard.class.php','class' => 'dashboard');
			$pageInformation['register'] = array('file' => 'html/register.class.php','class' => 'register');
			$pageInformation['forgotname'] = array('file' => 'html/forgotUsername.class.php','class' => 'forgotUsername');
			$pageInformation['forgotpass'] = array('file' => 'html/forgotPassword.class.php','class' => 'forgotPassword');

			
	 		if( isset($_REQUEST['action']) )  //First, we check for an "action" on the request
	 		{
	 			$thisAction = $_REQUEST['action'];  //Get the action of the request
				if(!isset($pageInformation[$thisAction])) //If thisAction which was sent on the request is not in the list of available actions, 
				{
					$thisAction = 'welcome'; //then we show the "welcome" action
				}
		 	}
		 	elseif( isset($_GET['help']) )  //if the is no action set, then if there is a "help" on the request
	 		{
	 			$thisAction = $_REQUEST['help']."-help"; //then set thisAction as the requested "help"
				if(!isset($pageInformation[$thisAction])) //If the requested help is not in the list of helps
				{
					$thisAction = 'welcome'; //then we show the "welcome" action"
				}
		 	}
		 	else //if neither "action" or "help" is on the request then
		 	{
		 		if(isset($_SESSION['field'])) //if there is a field selected for this session
		 		{
					if( $irrigationScheduler->row_FieldInfo['year'] <> date("Y") || (date("z") + 1) <= $irrigationScheduler->row_FieldInfo['plantDate'] || (date("z") + 1) > $irrigationScheduler->row_FieldInfo['seasonEndDate']  )
		 				$thisAction = 'soil-water-chart'; //show its soil water chart
		 			else
		 				$thisAction = 'dashboard';
		 		}
		 		else //if there is not a field selected for this session
		 		{
		 			$df = $database->getDefaultField($irrigationScheduler->session->username);
		 			if(isset($df['irrigation_field']) && strlen($df['irrigation_field'] > 0))
		 			{
		 				$_SESSION['field'] = $df['irrigation_field'];
						$this->selectVariables();
						$irrigationScheduler->selectVariables($_SESSION['field'],1);
						if( $irrigationScheduler->row_FieldInfo['year'] <> date("Y") || (date("z") + 1) <= $irrigationScheduler->row_FieldInfo['plantDate'] || (date("z") + 1) > $irrigationScheduler->row_FieldInfo['seasonEndDate']  )
			 				$thisAction = 'soil-water-chart'; //show its soil water chart
			 			else
			 				$thisAction = 'dashboard';
		 			}
		 			else
		 			{
			 			$thisAction = 'welcome'; //then we show the "welcome" action
		 			}
		 		}
		 	}

			$retVal = $this->createHeader($thisAction); //create the HTML header for "thisAction"
			$retVal .= $this->openBody();			 //open the HTML body for thisAction
			require_once($pageInformation[$thisAction]['file']); //include the required file for "thisAction"
			$page = new $pageInformation[$thisAction]['class']; //instantiate the class for "thisAction"
			$retVal .= $page->toHTML(); //tell the class to output its HTML
		  $retVal .= $this->closeBody(); //clean up by closing the HTML 
				
	 		$this->previous = $thisAction; //keep track of what this action was, in case we need it later
		 	$_SESSION['previouspage'] = $this->previous; //store it in a session variable
	 		return $retVal; //return the generated HTML
		}

		public function loginForm()
		{
			$retVal = $this->createHeader();
			$retVal .= $this->openBody();			
			require_once('html/loginForm.class.php');
			$page = new loginForm();
			$retVal .= $page->toHTML();
		  $retVal .= $this->closeBody();
		  return $retVal;
		}

	 	public function fieldName($thisName = "")
	 	{
	 		$fieldName = "<div style='clear:both'> ";
	 		$fieldName .= "<div  style='width:20%; text-align:right; float:left;'><label>Name:</label></div>";
	 		$fieldName .= "<div  style='width:79%; float:right;text-align:left;'>";
	 		$fieldName .= "<input  style='width:230px'  id=\"field-name\" name=\"field-name\" type=\"text\" maxlength=\"25\" value=\"".$thisName."\" />";
			$fieldName .= "</div>";
	 		$fieldName .= "<div name='fieldnameerror' id='fieldnameerror' style='display:none;color:red;clear:both;'>Testing</div>";
			$fieldName .= "</div>";
	
	 		return $fieldName;
	 	}
	 	public function fieldYear($thisYear = "" )
	 	{
	 		if(strlen($thisYear) == 0)
	 		{
	 			$thisYear = date("Y");
	 		}
	 		$fieldYear = "<div style='clear:both'>";
	 	 	$fieldYear .= "<div style='width:20%; text-align:right; float:left;'><label>Year:</label></div>";
	 		$fieldYear .= "<div style='width:79%; float:right;'>";
	 		$fieldYear .= "<input style='width:230px' onchange='fillStationList(document.getElementById(\"station\").value,document.getElementById(\"crop\").value,document.getElementById(\"year\").value);' id=\"year\" name=\"year\" type=\"text\" maxlength=\"25\" value=\"".$thisYear."\">";
			$fieldYear .= "</div>";
	 		$fieldYear .= "<div name='yearerror' id='yearerror' style='display:none;color:red;clear:both;'></div>";
			$fieldYear .= "</div>";
	 		
	 		return $fieldYear;
	 	}

	public function selectWeatherNetwork($thisNetwork = '-1', $formName = 'addFieldForm', $thisField = '-1' )
	{
			global $irrigationScheduler;
			global $database;
			$networkResults = $database->getAllWeatherNetworks();		
			$selectWeatherNetwork = "<script type='text/javascript' language='javascript'>";
			$selectWeatherNetwork .= "function fillStationList(stationID, cropID, theYear){  ";
			$selectWeatherNetwork .= " var sCtl = document.getElementById('station');  var jqxhr = $.post( '/ism/ajax/getStationList.php', $('#".$formName."').serialize(), function (j) {".PHP_EOL;
			$selectWeatherNetwork .= "var options = ''; if(j.length > 1) {  sCtl.disabled=false; } else { sCtl.disabled=true; }     ".PHP_EOL;
			$selectWeatherNetwork .= "for (var i = 0; i < j.length; i++) ".PHP_EOL;
			$selectWeatherNetwork .= "{     ";
			$selectWeatherNetwork .= "if(j[i].optionValue == stationID) { ";
			$selectWeatherNetwork .= "options += '<option selected=selected value=\"' + j[i].optionValue + '\">' + j[i].optionDisplay + '</option>';  ".PHP_EOL;
			$selectWeatherNetwork .= " } else {       ";
			$selectWeatherNetwork .= "options += '<option value=\"' + j[i].optionValue + '\">' + j[i].optionDisplay + '</option>';  ".PHP_EOL;
			$selectWeatherNetwork .= "}        ";
			$selectWeatherNetwork .= "}        ";
			$selectWeatherNetwork .= "$('select#station').html(options); ";
			$selectWeatherNetwork .= " fillCrops(stationID, cropID); ";
			$selectWeatherNetwork .= "}, 'json' ); ".PHP_EOL;			
			$selectWeatherNetwork .= "}";

			$selectWeatherNetwork .= "</script>";
			$selectWeatherNetwork .= "<div style='clear:both;'>";
			$selectWeatherNetwork .= "<div  style='width:20%; text-align:right; float:left;'><label>Network:</label></div>";
			$selectWeatherNetwork .= "<div   style='width:79%; float:right;'>";
			$selectWeatherNetwork .= "<select style='width:236px;'  onchange='fillStationList(null,null,document.getElementById(\"year\").value);' name=\"weather_network\" id=\"weather_network\">";
			if($this->NetworkID == -1)
				$selectWeatherNetwork .= "<option value=\"-1\" selected=\"selected\">Select Network</option>";
			else
				$selectWeatherNetwork .= "<option value=\"-1\">Select Weather Network</option>";
			
			$isSelected = false;
			while ($row_Station = mysql_fetch_assoc($networkResults))
			{
				$selectWeatherNetwork .= "<option value=\"".$row_Station['objid']."\"";
				if (!(strcmp($row_Station['objid'], $this->NetworkID)) || $row_Station['objid'] == $thisNetwork) 
				{
					$isSelected = true;
					$selectWeatherNetwork .= "selected=\"selected\"";
				}
				$selectWeatherNetwork .= ">".$row_Station['network_name']." (".$row_Station['states'].")</option>";
		  }
			$selectWeatherNetwork .= "</select>";
			if ($isSelected) 
			{
				$selectWeatherNetwork .= "<script type=\"text/javascript\">";
				$selectWeatherNetwork .= "$(document).ready(function(){  ";
				$selectWeatherNetwork .= "fillStationList('".$thisField."');".PHP_EOL;
				$selectWeatherNetwork .= "})";
				$selectWeatherNetwork .= "</script>";
			}
			$selectWeatherNetwork .= "</div>";
			$selectWeatherNetwork .= "<div name='weathernetworkerror' id='weathernetworkerror' style='display:none;color:red;clear:both;'></div>";
			$selectWeatherNetwork .= "</div>";
		
		return $selectWeatherNetwork; 		
	}
	public function selectStation($thisStation = "-1",$formName = '', $onchange="")
		{
			global $database;
			$selectStation = "<div style='clear:both;'>";
			$Station = $database->getAllStations();
			
			$selectStation .= "<div  style='width:20%; text-align:right; float:left;'><label>Station:</label></div>";
			$selectStation .= "<div   style='width:79%; float:right;'>";
			if($formName == 'eForm')
			{
				$selectStation .= "<script type=\"text/javascript\">";
				$selectStation .= "function selectRegionName(stationID){ ";
				$selectStation .= "$.post( '/ism/ajax/getStationRegion.php', $('#".$formName."').serialize(), function (j) { ".PHP_EOL;
				$selectStation .= "  var rN = ".$formName."['region'];  for(var tI = 0; tI < rN.length; tI++) { if(rN.options[tI].value == j.regionID) { rN.selectedIndex=tI; break; } }; ";
				$selectStation .= " var myX = document.getElementById('myNewObjid'); ".PHP_EOL;
				$selectStation .= " myX.value = j.objid; ".PHP_EOL;

				$selectStation .= "}, 'json' ); ".PHP_EOL;	
				$selectStation .= "}";
				$selectStation .= "</script>";
			}
			$selectStation .= "<select  style='width:236px;'   name=\"station\" id=\"station\" ";
			if(strlen($formName) > 0 )
			{
				if($formName == 'eForm')
				{
					$onclick = "onchange='selectRegionName(this.options[this.selectedIndex].value); $onchange';";
					$selectStation .= $onclick;
				}
				elseif($formName == 'addFieldForm')
				{
					$onclick = "onchange='$onchange'";
					$selectStation .= $onclick;
				}
			}
			$selectStation .= ">";
			$selectStation .= "<option value=\"-1\" selected=\"selected\">Select Network First</option>";
			$selectStation .= "</select>";
			$selectStation .= "</div>";
			$selectStation .= "<div name='stationerror' id='stationerror' style='display:none;color:red;clear:both;'></div>";
			$selectStation .= "</div>";
		
		return $selectStation; 		
	} 	
	
 	public function selectCrop($thisCrop = "-1", $ajax=false,$noItems = false)
 	{
 		global $database;
 		$selectCrop = "<div style='clear:both;'>";
		$Crop = $database->getAllCrops();
 		
		$selectCrop .= "<div style='width:20%; text-align:right; float:left;'><label>Crop:</label></div>";
		$selectCrop .= "<div style='width:79%; float:right;'>";
		$selectCrop .= "<select style='width:236px;'  name=\"crop\" id=\"crop\"";
		if($ajax)
		{
			$selectCrop .= " onchange='getCropDefaults(this.form, this.options[this.selectedIndex].value,this);' ";
		}
		$selectCrop .= ">";
		if($noItems)
		{
			$selectCrop .= "<option value=\"0\" selected=\"selected\">Select Station</option>";
		}
		else
		{
			if($ajax)
			{
				$selectCrop .= "<option value=\"0\" selected=\"selected\">New Crop</option>";
			}
			else
			{
				$selectCrop .= "<option value=\"0\" selected=\"selected\">Select Crop</option>";
			}
			while ($row_Crop = mysql_fetch_assoc($Crop))
			{
				$selectCrop .= "<option value=\"".$row_Crop['cropDefaultsID']."\" ";
	      if(!(strcmp($row_Crop['cropDefaultsID'], $this->CropID)) || $row_Crop['cropDefaultsID'] == $thisCrop)
					$selectCrop .= "selected=\"selected\"";	
	    		$selectCrop .= ">". $row_Crop['cropName']."</option>";
			}
		}
		$selectCrop .= "</select>";
		$selectCrop .= "</div>";
 		$selectCrop .= "<div name='croperror' id='croperror' style='float:none;display:none;color:red;clear:both;'></div>";
		$selectCrop .= "</div>";

		
		return $selectCrop;
 	}
 	
 	public function selectSoil($thisSoil = "-1")
 	{
 		global $database;
 		$selectSoil = "<div style='clear:both;'>";
 		
 		$Soil = $database->getAllSoils();
 		
 		$selectSoil .= "<div style='width:20%; text-align:right; float:left;'><label>Soil:</label></div>";
 		$selectSoil .= "<div style='width:79%; float:right;'>";
 		$selectSoil .= "<select style='width:236px;' name=\"soil-type\" id=\"soil-type\">";
 		$selectSoil .= "<option value=\"0\" selected=\"selected\">Select Soil Type</option>";
		
		while ($row_SelectSoil = mysql_fetch_assoc($Soil))
		{
			
 			$selectSoil .= "<option value=\"".$row_SelectSoil['soilID']."\"";
			if (!(strcmp($row_SelectSoil['soilID'], $this->SoilID)) || $row_SelectSoil['soilID'] == $thisSoil) 
 				$selectSoil .= " selected=\"selected\" ";
 			$selectSoil .= ">".$row_SelectSoil['soilTexture']."</option>";
	  }
		$selectSoil .= "</select>";
		$selectSoil .= "</div>";
 		$selectSoil .= "<div name='soilerror' id='soilerror' style='display:none;color:red;clear:both;'></div>";
		$selectSoil .= "</div>";

    
    return $selectSoil; 		
 	}
			
	 	public function selectField($submitsMyForm = false, $formName = "MyForm")
	 	{
	 		global $irrigationScheduler;
	 		global $database;
			$selectField = "";
 			$query_SelectField = sprintf("SELECT distinct a.fieldID as fieldID, a.fieldName as fieldName, a.year as year, b.cropName as cropName FROM irrigation.tblfield a, irrigation.tblcropdefaults b WHERE growerID = %s and b.cropDefaultsID = a.cropid ORDER BY a.year desc, a.fieldName ASC ", GetSQLValueString($this->GrowerID, "int"));
			$SelectField = $database->query($query_SelectField);
	
			$selectField .= "<div style=\"clear:both\">";
	 		if($irrigationScheduler->session->isMobileDevice > 0)
	 		{
				$selectField .= "<div style='width:99%;'>Field:&nbsp;";
			}
			else
			{
				$selectField .= "<div style='font-size:12pt;text-align:right; float:left;'>Field:&nbsp;</div>";
				$selectField .= "<div style='float:left;'>";
			}
			if($submitsMyForm)
			{
				$selectField .= "<select onchange='document.".$formName.".submit(); return false;' name=\"field\">";
			}
			else
			{
				$selectField .= "<select name=\"field\">";
			}
			$selectField .= "<option value=\"0\" selected=\"selected\">Select a Field</option>";
			while( $row_SelectField = mysql_fetch_assoc($SelectField) )
			{
				$selectField .= "<option value=\"".$row_SelectField['fieldID']."\" "; 
				if (!(strcmp($row_SelectField['fieldID'], $this->FieldID)))					  
					$selectField .= "selected=\"selected\"";
				$selectField .= "> ".$row_SelectField['fieldName'].", ".$row_SelectField['year']."; ".$row_SelectField['cropName'];
				$selectField .= "</option>";
			}
			$selectField .= "</select>";
			$selectField .= "</div>";
			$selectField .= "</div>";
	
			return $selectField;
	 	}
 	}


if (!function_exists("GetSQLValueString")) {
function GetSQLValueString($theValue, $theType = "", $theDefinedValue = "", $theNotDefinedValue = "") 
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
    $day = ( $day == 0 ) ? $day : $day - 1;  //This is original, 
    $offset = intval( intval( $day ) * 86400 ); 
    $str = date( $tFormat, strtotime( 'Jan 1, ' . $year ) + $offset ); 
    return( $str ); 
} 
}

	//*************************************************//
	// function dateDiff															 //
	// input: $start start date                        //
	// input: $end end date                            //
	// output: number of days difference               //
	//*************************************************//		
if (!function_exists("dateDiff")) {
	function dateDiff($start, $end) {   $start_ts = strtotime($start);   $end_ts = strtotime($end);   $diff = $end_ts - $start_ts;   return round($diff / 86400); } 
}
	
	//*************************************************//
	// function is_date 															 //
	// input: $str value to check                      //
	// output: true if a valid date, or false          //
	//*************************************************//		
if (!function_exists("is_date")) {
	function is_date( $str ) { $stamp = strtotime( $str ); return (is_numeric($stamp) && checkdate(date( 'm', $stamp ), date( 'd', $stamp ), date( 'Y', $stamp ))); } 
}

?>