<?php

/** 
 * Function to add / edit / delete section
 *************************************************/

/* required functions */
require_once('../../functions/functions.php'); 

/* filter input */
$_POST = filter_user_input($_POST, true, true, false);
$_POST['action'] = filter_user_input($_POST['action'], false, false, true);

/* verify that user is admin */
checkAdmin();

/* verify post */
CheckReferrer();


/* get permissions */
foreach($_POST as $key=>$val) {
	if(substr($key, 0,5) == "group") {
		if($val != "0") {
			$perm[substr($key,5)] = $val;
		}
	}
}
/* save to json */
$update['permissions'] = json_encode($perm);

/* get variables */
$update['action']      		= $_POST['action'];
$update['name']        		= $_POST['name'];
$update['description'] 		= $_POST['description'];
$update['id']          		= $_POST['id'];
$update['strictMode']  		= $_POST['strictMode'];
$update['subnetOrdering']  	= $_POST['subnetOrdering'];
$update['showVLAN']  		= $_POST['showVLAN'];
$update['showVRF']  		= $_POST['showVRF'];
$update['masterSection']	= $_POST['masterSection'];

if(isset($_POST['delegate'])) {
	if($_POST['delegate'] == 1) {
		$update['delegate'] = $_POST['delegate'];
	}
}

/* do action! */
if ($_POST['action']=="delete" && !isset($_POST['deleteconfirm'])) {
	# for ajax to prevent reload
	print "<div style='display:none'>alert alert-danger</div>";
	# result
	print "<div class='alert alert-warning'>";
	# print what will be deleted
	
	$subsections = getAllSubSections ($update['id']);
	# check also subsections
	if(sizeof($subsections)>0) {
		$subnets  = fetchSubnets ($update['id']);
		$subnetsc = sizeof($subnets);
		foreach($subnets as $s) {
			$out[] = $s;
		}
		foreach($subsections as $ss) {
			$subssubnet = fetchSubnets($ss['id']);
			foreach($subssubnet as $sss) {
				$out[] = $sss;
			}
			$subnetsc = $subnetsc + sizeof($subssubnet);
			$ipcnt   = countAllIPinSection($out);
		}
	}
	# no subsections
	else {
		$subnets  = fetchSubnets ($update['id']);
		$subnetsc = sizeof($subnets);
		$ipcnt   = countAllIPinSection($subnets);
	}
		
	print "<strong>"._("Warning")."</strong>: "._("I will delete").":<ul>";
	print "	<li>$subnetsc "._("subnets")."</li>";
	if($ipcnt>0) {
	print "	<li>$ipcnt "._("IP addresses")."</li>";
	}
	print "</ul>";
	
	print "<hr><div style='text-align:right'>";
	print _("Are you sure you want to delete above items?")." ";
	print "<div class='btn-group'>";
	print "	<a class='btn btn-sm btn-danger editSectionSubmitDelete' id='editSectionSubmitDelete'>"._("Confirm")."</a>";
	print "</div>";
	print "</div>";
	print "</div>";
}
else {
	# execute update
	if (UpdateSection ($update)) {
    	print '<div class="alert alert-success">'._("Section $update[action] successful").'!</div>';
    }
}

?>