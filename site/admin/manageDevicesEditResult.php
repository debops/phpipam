<?php 

/**
 * Edit switch result
 ***************************/

/* required functions */
require_once('../../functions/functions.php'); 

/* verify that user is admin */
if (!checkAdmin()) die('');

/* get modified details */
$device = $_POST;

/* sanitize post! */
$device['hostname'] 	= htmlentities($device['hostname'], ENT_COMPAT | ENT_HTML401, "UTF-8");		# prevent XSS
$device['ip_addr'] 		= htmlentities($device['ip_addr'], ENT_COMPAT | ENT_HTML401, "UTF-8");		# prevent XSS
$device['vendor'] 		= htmlentities($device['vendor'], ENT_COMPAT | ENT_HTML401, "UTF-8");		# prevent XSS
$device['model'] 		= htmlentities($device['model'], ENT_COMPAT | ENT_HTML401, "UTF-8");		# prevent XSS
$device['version'] 		= htmlentities($device['version'], ENT_COMPAT | ENT_HTML401, "UTF-8");		# prevent XSS
$device['description'] 	= htmlentities($device['description'], ENT_COMPAT | ENT_HTML401, "UTF-8");	# prevent XSS


/* available devices */
foreach($device as $key=>$line) {
	if (strlen(strstr($key,"section-"))>0) {
		$key2 = str_replace("section-", "", $key);
		$temp[] = $key2;
		
		unset($device[$key]);
	}
}
/* glue sections together */
if(sizeof($temp) > 0) {
	$device['sections'] = implode(";", $temp);
}

/* Hostname must be present! */
if($device['hostname'] == "") {
	die('<div class="alert alert alert-danger">'._('Hostname is mandatory').'!</div>');
}

# we need old hostname
if(($device['action'] == "edit") || ($device['action'] == "delete") ) {
	
	# get old switch name
	$oldHostname = getDeviceDetailsById($device['switchId']);
	$oldHostname = $oldHostname['hostname'];

	# if delete new hostname = ""
	if(($device['action'] == "delete")) {
		$device['hostname'] = "";
	}
}

//custom
$myFields = getCustomFields('devices');
if(sizeof($myFields) > 0) {
	foreach($myFields as $myField) {
		# replace possible ___ back to spaces!
		$myField['nameTest']      = str_replace(" ", "___", $myField['name']);
		
		if(isset($_POST[$myField['nameTest']])) { $device[$myField['name']] = $device[$myField['nameTest']];}

		//booleans can be only 0 and 1!
		if($myField['type']=="tinyint(1)") {
			if($device[$myField['name']]>1) {
				$device[$myField['name']] = "";
			}
		}
				
		//not null!
		if($myField['Null']=="NO" && strlen($device[$myField['name']])==0 && !checkAdmin(false,false)) {
			die('<div class="alert alert-danger">"'.$myField['name'].'" can not be empty!</div>');
		}

	}
}

/* update details */
if(!updateDeviceDetails($device)) {
	print('<div class="alert alert alert-danger">'._("Failed to $device[action] device").'!</div>');
}
else {
	print('<div class="alert alert-success">'._("Device $device[action] successfull").'!</div>');
}

?>