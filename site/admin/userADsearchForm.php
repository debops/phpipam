<?php

/**
 * Script to display usermod result
 *************************************/
 
/* required functions */
require_once('../../functions/functions.php'); 

/* verify that user is admin */
checkAdmin();
?>


<!-- header -->
<div class="pHeader"><?php print _('Search user in AD'); ?></div>


<!-- content -->
<div class="pContent">

	<div class='input-append'>
		<input type="text" id='dusername' name="dusername" placeholder="<?php print _('Username'); ?>">
		<button class='btn' id="adsearchusersubmit"><?php print _('Search'); ?></button>
	</div>
	
	<div id="adsearchuserresult" style='margin-bottom:10px;'></div>

</div>



<!-- footer -->
<div class="pFooter">
	<div class="btn-group">
		<button class="btn btn-small hidePopup2"><?php print _('Cancel'); ?></button>
	</div>
</div>