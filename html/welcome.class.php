<?php
	class welcome
	{
		public function toHTML()
		{
			global $irrigationScheduler;
			global $iform, $validation;

	 		$retVal = "<div style='clear:both'>";
			$retVal .= "<h2>Welcome ".$irrigationScheduler->session->username."</h2>";
	 		
	 		if($irrigationScheduler->htmlFactory->fCount > 0)  
	 		{
				$retVal .= "<form action=\"".$irrigationScheduler->session->basepath."&amp;action=soil-water-chart\" method=\"post\" name=\"WelcomeForm\">";
		 		$retVal .=  $irrigationScheduler->htmlFactory->selectField(true,"WelcomeForm");
		 		$retVal .= "</form>"; 				
		 	}
 			if($irrigationScheduler->htmlFactory->fCount == 0)  
	 		{
		 		$retVal .= "<div style=\"clear:both\">";
		 		$retVal .= "<p>The Irrigation Scheduler lets you ";
		 		$retVal .= "view, store, and analyze your irrigation water usage ";
		 		$retVal .= "based on crop type, soil type, and historical water use ";
		 		$retVal .= "data from a chosen weather network.  This can help you make informed decisions on ";
		 		$retVal .= "when, and how much water to apply for maximum ";
		 		$retVal .= "crop yields and quality.</p>";
	  		$retVal .= "<p>&nbsp;</p>";
		 		$retVal .= "<h2>Get Started!</h2><p>To get started, click <a href='".$irrigationScheduler->session->basepath."&amp;action=add-a-field'>Add New Field</a>, and then select your crop type, soil type and ";
		 		$retVal .= "closest weather station.  We'll do the rest by filling in ";
		 		$retVal .= "information based on your choices.  Later, if you wish, ";
		 		$retVal .= "you can use 'Field Settings' to personalize or ";
		 		$retVal .= "refine the default field settings for the selected crop and ";
		 		$retVal .= "soil type.</p>";
				$retVal .= "<div style=\"clear:both;\"><center><a href='".$irrigationScheduler->session->basepath."&amp;action=add-a-field'>Add New Field</a></center></div></div>";
		 	}
	 		$retVal .= "</div>";
	 		return $retVal;
		}
	}

?>