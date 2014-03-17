<?php

/*
 * Print edit folder
 *********************/

/* required functions */
require_once('../../functions/functions.php'); 

/* verify that user is logged in */
isUserAuthenticated(false);

/* verify that user has permissions if add */
if($_POST['action'] == "add") {
	$sectionPerm = checkSectionPermission ($_POST['sectionId']);
	if($sectionPerm != 3) {
		die("<div class='pHeader'>"._('Error')."</div><div class='pContent'><div class='alert alert-danger'>"._('You do not have permissions to add new folder in this section')."!</div></div><div class='pFooter'><button class='btn btn-sm btn-default hidePopups'>"._('Close')."</button>");
	}
}
/* otherwise check subnet permission */
else {
	$subnetPerm = checkSubnetPermission ($_POST['subnetId']);
	if($subnetPerm != 3) {
		die("<div class='pHeader'>"._('Error')."</div><div class='pContent'><div class='alert alert-danger'>"._('You do not have permissions to add edit/delete this folder')."!</div></div><div class='pFooter'><button class='btn btn-sm btn-default hidePopups'>"._('Close')."</button>");
	}
}

/* verify post */
CheckReferrer();

/* get all site settings */
$settings = getAllSettings();

# we are editing or deleting existing subnet, get old details
if ($_POST['action'] != "add") {
    $subnetDataOld = getSubnetDetailsById ($_POST['subnetId']);
}
# we are adding new subnet - get section details
else {
	# for selecting master subnet if added from subnet details!
	if(strlen($_REQUEST['subnetId']) > 0) {
    	$tempData = getSubnetDetailsById ($_POST['subnetId']);	
    	$subnetDataOld['masterSubnetId'] = $tempData['id'];			// same master subnet ID for nested
    	$subnetDataOld['vlanId'] 		 = $tempData['vlanId'];		// same default vlan for nested
    	$subnetDataOld['vrfId'] 		 = $tempData['vrfId'];		// same default vrf for nested
	}
	$sectionName = getSectionDetailsById ($_POST['sectionId']);
}

/* get custom subnet fields */
$customSubnetFields = getCustomFields('subnets');


# set readonly flag
if($_POST['action'] == "edit" || $_POST['action'] == "delete")	{ $readonly = true; }
else															{ $readonly = false; }
?>



<!-- header -->
<div class="pHeader"><?php print ucwords(_("$_POST[action]")); ?> <?php print _('folder'); ?></div>


<!-- content -->
<div class="pContent">

	<form id="editFolderDetails">
	<table class="editSubnetDetails table table-noborder table-condensed">

    <!-- name -->
    <tr>
        <td class="middle"><?php print _('Name'); ?></td>
        <td>
            <input type="text" class="form-control input-sm input-w-250" id="field-description" name="description" value="<?php print @$subnetDataOld['description']; ?>">
        </td>
        <td class="info2"><?php print _('Enter folder name'); ?></td>
    </tr>  

    <?php if($_POST['action'] != "add") { ?>
    <!-- section -->
    <tr>
        <td class="middle"><?php print _('Section'); ?></td>
        <td>
        	<select name="sectionIdNew" class="form-control input-sm input-w-auto">
            	<?php
           		$sections = fetchSections();
            
            	foreach($sections as $section) {
            		/* selected? */
            		if($_POST['sectionId'] == $section['id']) { print '<option value="'. $section['id'] .'" selected>'. $section['name'] .'</option>'. "\n"; }
            		else 									  { print '<option value="'. $section['id'] .'">'. $section['name'] .'</option>'. "\n"; }
            	}
            ?>
            </select>
        	
        	</select>
        </td>
        <td class="info2"><?php print _('Move to different section'); ?></td>
    </tr>  
    <?php } ?>
    
    <!-- Master subnet -->
    <tr>
        <td><?php print _('Master Folder'); ?></td>
        <td>
        	<?php printDropdownMenuBySectionFolders($_POST['sectionId'], $subnetDataOld['masterSubnetId']); ?>
        </td>
        <td class="info2"><?php print _('Enter master folder if you want to nest it under existing folder, or select root to create root folder'); ?>!</td>
    </tr>
    
    <!-- hidden values -->
    <input type="hidden" name="sectionId"       value="<?php print $_POST['sectionId'];    ?>">
    <input type="hidden" name="subnetId"        value="<?php print $_POST['subnetId'];     ?>">       
    <input type="hidden" name="action"    		value="<?php print $_POST['action']; ?>">
	<input type="hidden" name="vlanId" 			value="0">
	<input type="hidden" name="vrfId" 			value="0">


    <?php
	# custom Subnet fields
    if(sizeof($customSubnetFields) > 0) {
    	print "<tr>";
    	print "	<td colspan='3' class='hr'><hr></td>";
    	print "</tr>";
	    foreach($customSubnetFields as $field) {
	    	# replace spaces
	    	$field['nameNew'] = str_replace(" ", "___", $field['name']);
	    	# retain newlines
	    	$subnetDataOld[$field['name']] = str_replace("\n", "\\n", $subnetDataOld[$field['name']]);
	    	
		    print "<tr>";
		    print "	<td class='middle'>$field[name]</td>";
		    print "	<td colspan='2'>";
		    print "	<input type='text' class='form-control input-sm' id='field-$field[nameNew]' name='$field[nameNew]' value='".$subnetDataOld[$field['name']]."' placeholder='".$subnetDataOld[$field['name']]."'>";
		    print " </td>";
		    print "</tr>";
	    }
    }
    
    # divider
    print "<tr>";
    print "	<td colspan='3' class='hr'><hr></td>";
    print "</tr>";
    ?>
    
    </table>
    </form> 
    
    <?php
    # warning if delete
    if($_POST['action'] == "delete") {
	    print "<div class='alert alert-warning' style='margin-top:0px;'><strong>"._('Warning')."</strong><br>"._('Removing subnets will delete ALL underlaying subnets and belonging IP addresses')."!</div>";
    }
    ?>


</div>


<!-- footer -->
<div class="pFooter">
	<div class="btn-group">
		<button class="btn btn-sm btn-default hidePopups"><?php print _('Cancel'); ?></button>
		<?php
		//if action == edit and location = IPaddresses print also delete form
		if(($_POST['action'] == "edit") && ($_POST['location'] == "IPaddresses") ) {
			print "<button class='btn btn-sm btn-default btn-danger editFolderSubmitDelete' data-action='delete' data-subnetId='$subnetDataOld[id]'><i class='fa fa-trash-o'></i> "._('Delete folder')."</button>";
		}
		?>
		<button class="btn btn-sm btn-default editFolderSubmit <?php if($_POST['action']=="delete") print "btn-danger"; else print "btn-success"; ?>"><i class="<?php if($_POST['action']=="add") { print "fa fa-plus"; } else if ($_POST['action']=="delete") { print "fa fa-trash-o"; } else { print "fa fa-check"; } ?>"></i> <?php print ucwords(_($_POST['action'])); ?></button>
	</div>
	
	<div class="manageFolderEditResult"></div>
</div>