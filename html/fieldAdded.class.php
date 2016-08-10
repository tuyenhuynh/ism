<?php
	class fieldAdded
	{
		public function toHTML()
		{
	 		global $irrigationScheduler;
	 		$fieldAdded = "";
			$fieldAdded .= "<h2><a name='top'></a>Add New Field</h2>";
	    $fieldAdded .= "<form action='".$irrigationScheduler->session->processPath."' method='POST' name='added-field-form'><input type=hidden name='add-field' value='1'>";
			$fieldAdded .= "<div style=\"clear:both;\">A new field has been added to the database.<br/><br/>Add irrigation events in the 'Daily Budget Table' using the 'Edit' button for that date.<br/><br/>The 'Soil Water Chart' shows your soil water content over time.<br/><br/>You can make changes to the default values using the 'Field Settings' button below.</div>";
			$fieldAdded .= "</form>";
	 		$fieldAdded .= "<center>";
 
	 		return $fieldAdded;

		}
	   	
	}

?>