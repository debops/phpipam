<?php

/**
 * 
 * User selfMod check end execute
 *
 */

/* include required scripts */
require_once('../../functions/functions.php');

/* verify posted data */
CheckReferrer();

/* get old details */
$user_old = getActiveUserDetails();

/* get changed details */
$modData = $_POST;


/* widgets */
foreach($modData as $k=>$md) {
	if(substr($k, 0,7)=="widget-") {
		$modData['widgets'] .= substr($k, 7).";";
		unset($modData[$k]);
	}
}

/* verify email */
if (!checkEmail($modData['email'])) 											{ $error = _('Email not valid!'); }

/* verify password if changed (not empty) */
if (strlen($modData['password1']) != 0) {
	
	/* Hash passwords */
	$modData['password1'] = md5($modData['password1']);
	$modData['password2'] = md5($modData['password2']);

	if ( (strlen($_POST['password1']) < 8) && (!empty($_POST['password1'])) ) 	{ $error = _('Password must be at least 8 characters long!'); }
	else if ($modData['password1'] != $modData['password2']) 					{ $error = _('Passwords do not match!'); }
}


/* Print errors if present and die, else update */
if ($error) { die('<div class="alert alert-error alert-absolute">'._('Please fix the following error').': <strong>'. $error .'<strong></div>'); }
else {
    if (!selfUpdateUser ($modData)) 		{ die('<div class="alert alert-error alert-absolute">'._('Error updating').'!</div>'); }
    else 									{ print '<div class="alert alert-success alert-absolute">'._('Account updated successfully').'!</div>'; }
    
    # check if language has changed
    if($user_old['lang']!=$modData['lang'])	{ print '<div class="alert alert-info alert-absolute" style="margin-top:50px;">'._("To apply language change please log in again").'!</div>'; }
    
}

?>