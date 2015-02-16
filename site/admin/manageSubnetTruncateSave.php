<?php

/*
 * truncate subnet result
 *********************/

/* required functions */
require_once('../../functions/functions.php'); 

/* filter input */
$_POST = filter_user_input($_POST, true, true, false);

/* must be numeric */
if(!is_numeric($_POST['subnetId']))	{ die('<div class="alert alert-danger">'._("Invalid ID").'</div>'); }

/* verify that user has write permissions for subnet */
$subnetPerm = checkSubnetPermission ($_POST['subnetId']);
if($subnetPerm < 2) 	{ die('<div class="alert alert-danger">'._('You do not have permissions to truncate subnet').'!</div>'); }

/* verify post */
CheckReferrer();

# get all site settings
$settings = getAllSettings();

# truncate network
if(!truncateSubnet($_POST['subnetId'])) {}
else 									{ print "<div class='alert alert-success'>"._('Subnet truncated succesfully')."!</div>"; }

?>