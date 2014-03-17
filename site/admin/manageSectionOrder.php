<?php

/*
 * Section ordering
 *************************************************/

/* required functions */
require_once('../../functions/functions.php'); 

/* verify that user is admin */
checkAdmin();

/* verify post */
CheckReferrer();

/**
 * Fetch section info
 */
$sections = fetchSections();

$size =sizeof($sections);
?>

<script type="text/javascript" src="js/jquery-ui-1.10.3.custom.min.js"></script>
<script>
$(document).ready(function() {
	// initialize sortable
	$( "#sortableSec" ).sortable({
		start: function( event, ui ) {
			var iid = $(ui.item).attr('id');
			$('li#'+ iid).addClass('alert alert-success');
		},
		stop: function( event, ui ) {
			var iid = $(ui.item).attr('id');
			$('li#'+ iid).removeClass('alert alert-success');
		}		
	});
});
</script>


<!-- header -->
<div class="pHeader"><?php print _('Section order'); ?></div>


<!-- content -->
<div class="pContent">

	<!-- Order note -->
	<p class="muted"><?php print _('You can manually set order in which sections are displayed in. Default is creation date.'); ?></p>
	
	<!-- list -->
	<ul id='sortableSec' class='sortable'>
	<?php
	foreach($sections as $s) {
		print "<li id='$s[id]'><i class='fa fa-arrows'></i> <strong>$s[name]</strong> <span class='info2'>( $s[description] )</span></li>";	
	}
	?>
	</ul>
</div>


<!-- footer -->
<div class="pFooter">
	<div class="btn-group">
		<button class="btn btn-sm btn-default hidePopups"><?php print _('Cancel'); ?></button>
		<button class="btn btn-sm btn-default btn-success" id="sectionOrderSubmit"><i class="fa fa-check"></i> <?php print _('Save'); ?></button>
	</div>
	<!-- result holder -->
	<div class="sectionOrderResult"></div>
</div>	
		