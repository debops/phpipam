<?php

/** 
 * Function to add / edit / delete section
 ********************************************/

/* required functions */
require_once('../../functions/functions.php');

/* verify that user is logged in */
isUserAuthenticated(true);

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
if (!modifySubnetDetails ($_POST)) 		{ print '<div class="alert alert alert-danger">'._('Error adding new folder').'!</div>'; }
# all good
else {
	if($_POST['action'] == "delete") 	{ print '<div class="alert alert-success">'._('Folder, IP addresses and all belonging subnets deleted successfully').'!</div>'; } 
	else 								{ print '<div class="alert alert-success">'._("Folder $_POST[action] successfull").'!</div>';  }
}

?>