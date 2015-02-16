<?php

/** 
 * Function to add / edit / delete section
 ********************************************/

/* required functions */
require_once('../../functions/functions.php');

/* verify that user is logged in */
isUserAuthenticated(true);

/* filter input */
$_POST = filter_user_input($_POST, true, true, false);
$_POST['action'] = filter_user_input($_POST['action'], false, false, true);

/* must be numeric */
if($_POST['action']!="add") {
	if(!is_numeric($_POST['subnetId']))		{ die('<div class="alert alert-danger">'._("Invalid ID").'</div>'); }
}

/* verify that user has permissions if add */
if($_POST['action'] == "add") {
	$sectionPerm = checkSectionPermission ($_POST['sectionId']);
	if($sectionPerm != 3) {
		die("<div class='alert alert alert-danger'>"._('You do not have permissions to add new subnet in this section')."!</div>");
	}
}
/* otherwise check subnet permission */
else {
	$subnetPerm = checkSubnetPermission ($_POST['subnetId']);
	if($subnetPerm != 3) {
		die("<div class='alert alert alert-danger'>"._('You do not have permissions to add edit/delete this subnet')."!</div>");
	}	
}

/* verify post */
CheckReferrer();

/* get all settings */
$settings = getAllSettings();

/* get section details */
$section = getSectionDetailsById($_POST['sectionId']);

//custom
$myFields = getCustomFields('subnets');
if(sizeof($myFields) > 0) {
	foreach($myFields as $myField) {
		# replace possible ___ back to spaces!
		$myField['nameTest']      = str_replace(" ", "___", $myField['name']);
		
		if(isset($_POST[$myField['nameTest']])) { $_POST[$myField['name']] = $_POST[$myField['nameTest']];}
	}
}


//we need old values for mailing
if($_POST['action']=="edit" || $_POST['action']=="delete") {
	$old = getSubnetDetailsById($_POST['subnetId']);
}
$new = $_POST;
unset ($new['subnet'],$new['allowRequests'],$new['showName'],$new['pingSubnet'],$new['discoverSubnet']);
unset ($old['subnet'],$old['allowRequests'],$old['showName'],$old['pingSubnet'],$old['discoverSubnet']);


/* sanitize description */
$_POST['description'] = htmlentities($_POST['description'], ENT_COMPAT | ENT_HTML401, "UTF-8");	//prevent XSS

/* Set permissions for add! */
if($_POST['action'] == "add") {
	# root
	if($_POST['masterSubnetId'] == 0) {
		$_POST['permissions'] = $section['permissions'];
	}
	# nested - inherit parent permissions
	else {
		# get parent
		$parent = getSubnetDetailsById($_POST['masterSubnetId']);
		$_POST['permissions'] = $parent['permissions'];
	}
}

# check for name length - 2 is minimum!
if(strlen($_POST['description'])<2 && $_POST['action']!="delete") {
	die("<div class='alert alert alert-danger'>"._('Folder name must have at least 2 characters')."!</div>");
}

# set folder flag!
$_POST['isFolder'] = true;


# failed
if ($_POST['action']=="delete" && !isset($_POST['deleteconfirm'])) {
	# for ajax to prevent reload
	print "<div style='display:none'>alert alert-danger</div>";
	# result
	print "<div class='alert alert-warning'>";
	# print what will be deleted
	getAllSlaves($_POST['subnetId'], false);
	$removeSlaves = array_unique($removeSlaves);
	# check if folder?
	$foldercnt = 0;
	$subnetcnt = 0;
	foreach($removeSlaves as $s) {
		$f=getSubnetDetailsById($s);
		if($f['isFolder']==1)	$foldercnt++;
		else					$subnetcnt++;
	}
	$ipcnt  = countAllSlaveIPAddresses($_POST['subnetId']);
	print "<strong>"._("Warning")."</strong>: "._("I will delete").":<ul>";
	print "	<li>$foldercnt "._("folders")."</li>";
	if($subnetcnt>0) {
	print "	<li>$subnetcnt "._("subnets")."</li>";
	}
	if($ipcnt>0) {
	print "	<li>$ipcnt "._("IP addresses")."</li>";
	}
	print "</ul>";
	
	print "<hr><div style='text-align:right'>";
	print _("Are you sure you want to delete above items?")." ";
	print "<div class='btn-group'>";
	print "	<a class='btn btn-sm btn-danger editFolderSubmitDelete' id='editFolderSubmitDelete' data-subnetId='".$_POST['subnetId']."'>"._("Confirm")."</a>";
	print "</div>";
	print "</div>";
	print "</div>";
}
else {
		if (!modifySubnetDetails ($_POST)) 		{ print '<div class="alert alert alert-danger">'._('Error adding new folder').'!</div>'; }
	# all good
	else {

    	/* @mail functions ------------------- */
		include_once('../../functions/functions-mail.php');
		sendObjectUpdateMails("folder", $_POST['action'], $old, $new);
		
		if($_POST['action'] == "delete") 	{ print '<div class="alert alert-success">'._('Folder, IP addresses and all belonging subnets deleted successfully').'!</div>'; } 
		else 								{ print '<div class="alert alert-success">'._("Folder $_POST[action] successfull").'!</div>';  }
	}
}

?>