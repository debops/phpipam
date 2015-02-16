<?php

/**
 * Script to print add / edit / delete group
 *************************************************/

/* required functions */
require_once('../../functions/functions.php'); 

/* verify that user is admin */
checkAdmin();

/* get all settings */
$settings = getAllSettings();

/* filter input */
$_POST = filter_user_input($_POST, true, true, false);
$_POST['action'] = filter_user_input($_POST['action'], false, false, true);

?>


<!-- header -->
<div class="pHeader">
<?php
/**
 * If action is not set get it form post variable!
 */
if($_POST['action'] != "add") {
    
    //fetch all group details
    $group = getGroupById($_POST['id']);
    
    print ucwords($_POST['action']) .' '._('group').' '.$group['g_name'];
}
else {
	/* Set dummy data  */
	$group['g_name'] = '';
	$group['g_description']  = '';
	
	print _('Add new group');
}
?>
</div>


<!-- content -->
<div class="pContent">

	<form id="groupEdit" name="groupEdit">
	<table class="groupEdit table table-noborder table-condensed">

	<!-- name -->
	<tr>
	    <td><?php print _('Group name'); ?></td> 
	    <td><input type="text" name="g_name" class="form-control input-sm" value="<?php print $group['g_name']; ?>" <?php if($_POST['action'] == "delete") print "readonly"; ?>></td>
       	<td class="info2"><?php print _('Enter group name'); ?></td>
    </tr>

    <!-- description -->
    <tr>
    	<td><?php print _('Description'); ?></td> 
    	<td>
    		<input type="text" name="g_desc" class="form-control input-sm" value="<?php print $group['g_desc']; ?>" <?php if($_POST['action'] == "delete") print "readonly"; ?>>

    		<input type="hidden" name="g_id" value="<?php print $_POST['id']; ?>">
    		<input type="hidden" name="action" value="<?php print $_POST['action']; ?>">
    	</td>   
    	<td class="info2"><?php print _('Enter description'); ?></td>
    </tr>

</table>
</form>

</div>




<!-- footer -->
<div class="pFooter">
	<div class="btn-group">
		<button class="btn btn-sm btn-default hidePopups"><?php print _('Cancel'); ?></button>
		<button class="btn btn-sm btn-default <?php if($_POST['action']=="delete") { print "btn-danger"; } else { print "btn-success"; } ?>" id="editGroupSubmit"><i class="fa <?php if($_POST['action']=="add") { print "fa-plus"; } else if ($_POST['action']=="delete") { print "fa-trash-o"; } else { print "fa-check"; } ?>"></i> <?php print ucwords(_($_POST['action'])); ?></button>
	</div>

	<!-- Result -->
	<div class="groupEditResult"></div>
</div>
