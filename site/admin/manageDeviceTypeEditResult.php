<?php 

/**
 * Edit switch result
 ***************************/

/* required functions */
require_once('../../functions/functions.php'); 

/* verify that user is admin */
if (!checkAdmin()) die('');

/* Hostname must be present! */
if($_POST['tname'] == "") {
	die('<div class="alert alert alert-danger">'._('Name is mandatory').'!</div>');
}

/* update details */
if(!updateDevicetypeDetails($_POST)) {
	print('<div class="alert alert alert-danger">'._("Failed to $_POST[action] device type").'!</div>');
}
else {
	print('<div class="alert alert-success">'._("Device type $_POST[action] successfull").'!</div>');
}

?>