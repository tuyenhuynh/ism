<?php
	class addDeleteFields
	{
		public function toHTML()
		{
			global $irrigationScheduler;
			global $iform, $validation, $database;
			$myFields = "";
	
	 		$myFields .= "<div class=\"content\">";
	 
	 		if($irrigationScheduler->session->isMobileDevice > 0)
			{
				$myFields .= "<h2><a name=\"top\"></a>Manage Fields</h2>";
				$myFields .= "<form action=\"".$irrigationScheduler->session->basepath."&amp;action=my-fields\" method=\"post\" name=\"MyFieldsForm\">";
		 		$myFields .= "<input type='hidden' name='delete-field' id='delete-field'  value=0>";
		 		$myFields .= "<input type='hidden' name='action' id='action' value='my-fields'>";
		 		$myFields .=  $irrigationScheduler->htmlFactory->selectField(true,"MyFieldsForm");
		 		$myFields .= "</form>"; 	
		 	}
	
	 		if($irrigationScheduler->session->isMobileDevice > 0)
			{
				$openCenter = "<center>";
				$closeCenter = "</center>";
			}
			else
			{
				$openCenter = "";
				$closeCenter = "";
			}
			$myFields .= "<div style=\"clear:both;\">$openCenter<a href='".$irrigationScheduler->session->basepath."&amp;action=add-a-field'>Add New Field</a>$closeCenter</div>";
	
	 		if($irrigationScheduler->htmlFactory->FieldID > 0) 
	 		{	
	 			$myFields .= "<div style=\"clear:both;\">$openCenter<a onclick=\"if( confirm('Are you sure you want to delete this field?  This will delete all of the field settings and data.')){ var dd = document.getElementById('action'); dd.value = 'delete-field'; document.MyFieldsForm.action='".$irrigationScheduler->session->processPath."';document.MyFieldsForm.submit();return false;}\"  href='javascript:void(0)'>Delete Selected Field</a>$closeCenter</div>";
	 		}
			$myFields .= "<div style=\"clear:both;\">$openCenter<a href='".$irrigationScheduler->session->basepath."&amp;help=my-fields'>Help</a>$closeCenter</div>";
	    $myFields .="</div>";
	
	 		return $myFields;
		}
	}

?>