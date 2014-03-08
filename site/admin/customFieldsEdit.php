<?php

/** 
 * Edit custom IP field
 ************************/


/*
	provided values are:
		table		= name of the table
		action		= action
		fieldName	= field name to edit
 */ 
 

/* required functions */
require_once('../../functions/functions.php'); 

/* verify that user is admin */
checkAdmin();

/* reset field name for add! */
if($_POST['action'] == "add") 	{ $_POST['fieldName'] = ""; }
else 							{ $_POST['oldname'] = $_POST['fieldName'];}


//old field val
$fieldval = getFullFieldData($_POST['table'], $_POST['fieldName']);
?>


<div class="pHeader"><?php print ucwords(_("$_POST[action]")); ?> <?php print _('custom field'); ?></div>


<div class="pContent">

	<form id="editCustomFields">
	<table id="editCustomFields" class="table table-noborder table-condensed">

	<!-- name -->
	<tr>
		<td><?php print _('Name'); ?></td>
		<td>	
			<input type="text" name="name" value="<?php print $_POST['fieldName']; ?>" placeholder="<?php print _('Select field name'); ?>" <?php if($_POST['action'] == "delete") { print 'readonly'; } ?>>
			
			<input type="hidden" name="oldname" value="<?php print $_POST['oldname']; ?>">
			<input type="hidden" name="action" value="<?php print $_POST['action']; ?>">
			<input type="hidden" name="table" value="<?php print $_POST['table']; ?>">
		</td>
	</tr>

	<!-- Description -->
	<tr>
		<td><?php print _('Description'); ?></td>
		<td>	
			<input type="text" name="Comment" value="<?php print @$fieldval['Comment']; ?>" placeholder="<?php print _('Enter comment for users'); ?>" <?php if($_POST['action'] == "delete") { print 'readonly'; } ?>>
		</td>
	</tr>
	
	<!-- required -->
	<tr>
		<td><?php print _('Required field'); ?></td>
		<td>
			<input name="NULL" type="checkbox" value="NO" <?php if(@$fieldval['Null']=="NO") print "checked"; ?>>
		</td>
	</tr>

	</table>
	</form>	
</div>


<!-- footer -->
<div class="pFooter">
	<div class="btn-group">
		<button class="btn btn-small hidePopups"><?php print _('Close'); ?></button>
		<button class="btn btn-small <?php if($_POST['action']=="delete") { print "btn-danger"; } else { print "btn-success";} ?>" id="editcustomSubmit"><i class="icon-white <?php if($_POST['action']=="add") { print "icon-plus"; } else if ($_POST['action']=="delete") { print "icon-trash"; } else { print "icon-ok"; } ?>"></i> <?php print ucwords(_($_POST['action'])); ?></button>
	</div>
	<!-- result -->
	<div class="customEditResult"></div>
</div>