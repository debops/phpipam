<?php 

/**
 * Script to edit VRF
 ***************************/

/* required functions */
require_once('../../functions/functions.php'); 

/* verify that user is admin */
if (!checkAdmin()) die('');

/* filter input */
$_POST = filter_user_input($_POST, true, true, false);
$_POST['action'] = filter_user_input($_POST['action'], false, false, true);

/* get modified details */
$vrf = $_POST;

/* Hostname must be present! */
if($vrf['name'] == "") { die('<div class="alert alert-danger">'._('Name is mandatory').'!</div>'); }

/* update details */
if(!updateVRFDetails($vrf)) { print('<div class="alert alert-danger">'._("Failed to $vrf[action] VRF").'!</div>'); }
else 						{ print('<div class="alert alert-success">'._("VRF $vrf[action] successfull").'!</div>'); }

?>