<?php

/**
 *	Mail settings
 **************************/

/* required functions */
require_once('../../functions/functions.php'); 

/* verify that user is admin */
checkAdmin(false);

/* fetch all settings */
$settings = $_POST;

/* Update mail settings */
if(!updateMailSettings($settings)) 	{ die('<div class="alert alert alert-danger">'._('Cannot update settings').'!</div>'); }
else 								{ print '<div class="alert alert-success">'._('Settings updated successfully').'!</div>';}
?>