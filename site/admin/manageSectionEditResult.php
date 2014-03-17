<?php

/** 
 * Function to add / edit / delete section
 *************************************************/

/* required functions */
require_once('../../functions/functions.php'); 

/* verify that user is admin */
if (!checkAdmin()) die('');

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
if (UpdateSection ($update)) {
    print '<div class="alert alert-success">'._("Section $update[action] successful").'!</div>';
}

?>