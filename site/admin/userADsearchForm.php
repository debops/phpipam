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

	<div class='input-group'>
		<input type="text" class="form-control input-sm" id='dusername' name="dusername" placeholder="<?php print _('Username'); ?>">
		<span class="input-group-btn">
			<button class='btn btn-sm btn-default' class="form-control input-sm" id="adsearchusersubmit"><?php print _('Search'); ?></button>
		</span>
	</div>
	
	<div id="adsearchuserresult" style='margin-bottom:10px;margin-top:10px;'></div>

</div>



<!-- footer -->
<div class="pFooter">
	<div class="btn-group">
		<button class="btn btn-sm btn-default hidePopup2"><?php print _('Cancel'); ?></button>
	</div>
</div>