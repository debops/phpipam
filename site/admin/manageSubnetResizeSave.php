<?php

/*
 * Print resize subnet
 *********************/

/* required functions */
require_once('../../functions/functions.php'); 

/* filter input */
$_POST = filter_user_input($_POST, true, true, false);

/* must be numeric */
if(!is_numeric($_POST['subnetId']))	{ die('<div class="alert alert-danger">'._("Invalid ID").'</div>'); }


/* verify that user has write permissions for subnet */
$subnetPerm = checkSubnetPermission ($_POST['subnetId']);
if($subnetPerm < 3) 	{ die('<div class="alert alert-danger">'._('You do not have permissions to resize subnet').'!</div>'); }


/* verify post */
CheckReferrer();

# get old subnet details
$subnetOld = getSubnetDetailsById ($_POST['subnetId']);

# get all site settings
$settings = getAllSettings();

/* get section details */
$section = getSectionDetailsById($subnetOld['sectionId']);

/* 
 * now we need to check each host against new subnet
 */
$ipaddresses   = getIpAddressesBySubnetIdSort ($_POST['subnetId'], "ip_addr", "asc");		# get all IP addresses


# check if it possible
if (!$newSubnet = getSubnetNetworkAddress(transform2long($subnetOld['subnet']) . "/" . $_POST['newMask'])) {
	$errors[] = _("New subnet not possible") . "!";
}
 
foreach($ipaddresses as $ip) {
	# check against new subnet
	$error = VerifyIpAddress(transform2long($ip['ip_addr']), $newSubnet);
	
	if(!$error) {}	# ok - false returns if no error is found
	else {
		$errors[] = $error;
	} 
}
 
# Check if new subnet size doesn't overlap
if ($section['strictMode'] == 1) {
    if ($overlap = verifyResizedSubnetOverlapping($subnetOld, $newSubnet)) {
        $errors[] = $overlap;
    }
}

/* ask must be > 8 */
if($_POST['newMask'] < 8) { die('<div class="alert alert-danger">'._('New mask must be at least /8').'!</div>'); }


/* 
 * if strict mode is enabled check that is is still inside master subnet!
 */
if($section['strictMode'] == 1) {
    if ( (!$overlap = verifySubnetNesting($subnetOld['masterSubnetId'], transform2long($subnetOld['subnet'])."/".$_POST['newMask'])) && $subnetOld['masterSubnetId']!=0) {
    	# get master details
    	$master = getSubnetDetailsById($subnetOld['masterSubnetId']);
		$master = Transform2long($master['subnet']) . "/" . $master['mask']." - ".$master['description'];
    	$errors[] = _("New subnet not in master subnet")."!<br>($master)";
    }
}


/*
 * If subnet has slaves make sure all slaves are still inside!
 */
if($section['strictMode'] == 1) {
	$slaves = getAllSlaveSubnetsBySubnetId ($_POST['subnetId']);
	if(sizeof($slaves) > 0) {
		foreach($slaves as $slave) {
			if(!isSubnetInsideSubnet (transform2long($slave['subnet'])."/".$slave['mask'], transform2long($subnetOld['subnet'])."/".$_POST['newMask'])) {
				$errors[] = _("Nested subnet out of new subnet")."!<br>(".transform2long($slave['subnet'])."/$slave[mask] - $slave[description])";	
			}
		}
	}
}


/* if no errors edit! */
if(sizeof($errors) > 0) {
	print "<div class='alert alert-danger'><ul>";
	foreach($errors as $error) {
		print "<li>$error</li>";
	}
	print "</ul></div>";
}
# all good, edit subnet!
else {
	# failed
    if (!modifySubnetMask ($_POST['subnetId'], $_POST['newMask'])) 	{ print '<div class="alert alert-danger">'._('Error resizing subnet').'!</div>'; }
    # all good
    else 															{ print '<div class="alert alert-success">'._('Subnet resized successfully').'!</div>'; } 
}

?>