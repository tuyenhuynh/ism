<?php
	class addField
	{
		public function toHTML()
		{
			global $irrigationScheduler;
      if($irrigationScheduler->session->isMobileDevice > 0)
          $MobileSpace = "<p>&nbsp;</p>";
      else
          $MobileSpace = "";

	 		$addField = "";
			$addField .= "<h2><a name='top'></a>Add New Field</h2>";
	 		$addField .= "<p>Clicking the update field button will overwrite any changes you made in the \"Field Settings\" for this field.  If you don't wish to save any changes, hit the browser's back button instead of clicking the \"Update Field\" button.</p>".$MobileSpace;
	 		$addField .= "<p><b>Field Name</b> - Use this to name/rename the field.</p>".$MobileSpace;
	 		$addField .= "<p><b>Field Year</b> - This is the growing year.  If a previous year is selected, then that previous year's weather data will be used in the water budget.</p>".$MobileSpace;
	 		$addField .= "<p><b>Field Crop</b> - Based on the selected crop, default growing season dates, crop coefficients, management allowable deficit (MAD) rates, and rooting depths are chosen.  These crop parameters can be later edited in the \"Field Settings\" Page.</p>".$MobileSpace;
	 		$addField .= "<p><b>Field Soil</b> - Based on the soil texture chosen, default field capacity, wilting point, and water holding capacity values are chosen.  These soil parameters can be later edited in the \"Field Settings\" Page.</p>".$MobileSpace;
	 		$addField .= "<p><b>Station</b> - Choose the weather station that is nearest, or whose weather conditions are closest to your field.  Daily reference evapotranspiration (ET) rates and rainfall data from this station will be used in the soil water balance.</p>".$MobileSpace;
			
			if($irrigationScheduler->session->isMobileDevice > 0)
				$addField .= "<div style=\"clear:both;\"><center><a href='".$irrigationScheduler->session->basepath."&amp;action=add-a-field'>Back</a></center></div>";
			else
				$addField .= "<div style=\"clear:both;\"><center><a href='javascript:void(0)' onclick='$.fancybox.close();'>Close</a></center></div>";
	    
	 		return $addField;
	 	}
	   	
	}

?>