<?php

/**
 *	Edit switch details
 ************************/

/* required functions */
require_once('../../functions/functions.php'); 

/* verify that user is admin */
if (!checkAdmin()) die('');

/* get switch detaild by Id! */
if( ($_POST['action'] == "edit") || ($_POST['action'] == "delete") ) {
	$device = getTypeDetailsById($_POST['tid']);
}

if ($_POST['action'] == "delete") 	{ $readonly = "readonly"; }
else 								{ $readonly = ""; }
?>


<!-- header -->
<div class="pHeader"><?php print ucwords(_("$_POST[action]")); ?> <?php print _('device type'); ?></div>


<!-- content -->
<div class="pContent">

	<form id="devTypeEdit">
	<table class="table table-noborder table-condensed">

	<!-- hostname  -->
	<tr>
		<td><?php print _('Name'); ?></td>
		<td>
			<input type="text" name="tname" class="form-control input-sm" placeholder="<?php print _('Name'); ?>" value="<?php print @$device['tname']; ?>" <?php print $readonly; ?>>
			<input type="hidden" name="action" value="<?php print $_POST['action']; ?>">
			<?php
			if( ($_POST['action'] == "edit") || ($_POST['action'] == "delete") ) {
				print '<input type="hidden" name="tid" value="'. $_POST['tid'] .'">'. "\n";
			} 
			?>
		</td>
	</tr>

	<!-- IP address -->
	<tr>
		<td><?php print _('Description'); ?></td>
		<td>
			<input type="text" name="tdescription" class="form-control input-sm" placeholder="<?php print _('Description'); ?>" value="<?php print @$device['tdescription']; ?>" <?php print $readonly; ?>>
		</td>
	</tr>

	</table>
	</form>
</div>


<!-- footer -->
<div class="pFooter">
	<div class="btn-group">
		<button class="btn btn-sm btn-default hidePopups"><?php print _('Cancel'); ?></button>
		<button class="btn btn-sm btn-default <?php if($_POST['action']=="delete") { print "btn-danger"; } else { print "btn-success"; } ?>" id="editDevTypeSubmit"><i class="fa <?php if($_POST['action']=="add") { print "fa-plus"; } else if ($_POST['action']=="delete") { print "fa-trash-o"; } else { print "fa-check"; } ?>"></i> <?php print ucwords(_($_POST['action'])); ?></button>
	</div>

	<!-- result -->
	<div class="devTypeEditResult"></div>
</div>