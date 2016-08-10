<?php
	class addDeleteFields
	{
		public function toHTML()
		{
			global $irrigationScheduler;
      if($irrigationScheduler->session->isMobileDevice > 0)
          $MobileSpace = "<p>&nbsp;</p>";
      else
          $MobileSpace = "";

			$myFields .= "<h2><a name=\"top\"></a>Manage Fields Help</h2>";
			$myFields .= "<p><b>Delete Selected Field</b> - Permanently removes the selected field and all of its settings and associated data.</p>";
			$myFields .= "<p><b>Add New Field</b> - Use this to add a new field.</p>".$MobileSpace;
	
			if($irrigationScheduler->session->isMobileDevice > 0)
				$myFields .= "<div style=\"clear:both;\"><center><a href='".$irrigationScheduler->session->basepath."&amp;action=my-fields'>Back</a></center></div>";
			else
				$myFields .= "<div style=\"clear:both;\"><center><a href='javascript:void(0)' onclick='$.fancybox.close();'>Close</a></center></div>";
	     		
	 		return $myFields;
		}
	}

?>