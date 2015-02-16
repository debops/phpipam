<?php

/*
 * Print edit subnet
 *********************/

/* required functions */
require_once('../../functions/functions.php'); 

/* verify that user is logged in */
isUserAuthenticated(false);

/* verify that user is admin */
if (!checkAdmin()) die('');

/* escape vars to prevent SQL injection */
$_POST = filter_user_input ($_POST, true, true);

/* must be numeric */
if(!is_numeric($_POST['subnetId']))	{ die('<div class="alert alert-danger">'._("Invalid ID").'</div>'); }


/* verify post */
CheckReferrer();

/* get all groups */
$groups = getAllGroups();

/* get subnet details */
$subnet = getSubnetDetailsById($_POST['subnetId']);
?>



<!-- header -->
<div class="pHeader">
	<?php 
	if($subnet['isFolder']==1)	{ print _('Manage folder permissions');  }
	else						{ print _('Manage subnet permissions');  }		
	?>
</div>

<!-- content -->
<div class="pContent">

	<?php 
	if($subnet['isFolder']==1)	{ print _('Manage permissions for folder')." $subnet[description]"; }
	else						{ print _('Manage permissions for subnet'); ?> <?php print transform2long($subnet['subnet'])."/".$subnet['mask']." ($subnet[description])"; }
	?>
	<hr>

	<form id="editSubnetPermissions">
	<table class="editSubnetPermissions table table-noborder table-condensed table-hover">

	<?php
	# parse permissions
	if(strlen($subnet['permissions'])>1) {
		$permissons = parseSectionPermissions($subnet['permissions']);
	}
	else {
		$permissons = "";
	}

	# print each group
	if($groups) {
	foreach($groups as $g) {
		print "<tr>";
		print "	<td>$g[g_name]</td>";
		print "	<td>";
			
		print "<span class='checkbox inline noborder'>";			

		print "	<input type='radio' name='group$g[g_id]' value='0' checked> na";
		if($permissons[$g['g_id']] == "1")	{ print " <input type='radio' name='group$g[g_id]' value='1' checked> ro"; }			
		else								{ print " <input type='radio' name='group$g[g_id]' value='1'> ro"; }	
		if($permissons[$g['g_id']] == "2")	{ print " <input type='radio' name='group$g[g_id]' value='2' checked> rw"; }			
		else								{ print " <input type='radio' name='group$g[g_id]' value='2'> rw"; }			
		if($permissons[$g['g_id']] == "3")	{ print " <input type='radio' name='group$g[g_id]' value='3' checked> rwa"; }			
		else								{ print " <input type='radio' name='group$g[g_id]' value='3'> rwa"; }
		print "</span>";

		# hidden
		print "<input type='hidden' name='subnetId' value='$_POST[subnetId]'>";
		
		print "	</td>";
		print "</tr>";
	}
	} else {
		print "<tr>";
		print "	<td colspan='2'><span class='alert alert-info'>"._('No groups available')."</span></td>";
		print "</tr>";		
	}
	?>
     
    </table>
    </form> 
    
    <?php
    # print warning if slaves exist
    if(subnetContainsSlaves($_POST['subnetId'])) { print "<div class='alert alert-warning'>"._('Permissions for all nested subnets will be overridden')."!</div>"; }
    ?>
    
</div>


<!-- footer -->
<div class="pFooter">
	<div class="btn-group">
		<button class="btn btn-sm btn-default hidePopups"><?php print _('Cancel'); ?></button>
		<button class="btn btn-sm btn-default btn-success editSubnetPermissionsSubmit"><i class="fa fa-check"></i> <?php print _('Set permissions'); ?></button>
	</div>

	<div class="editSubnetPermissionsResult"></div>
</div>