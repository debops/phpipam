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

/* save widgets */
if (!setUserDashWidgets ($user_old['id'], $_POST['widgets'])) 	{ die('<div class="alert alert-danger alert-absolute">'._('Error updating').'!</div>'); }
else 															{ print '<div class="alert alert-success alert-absolute">'._('Account updated successfully').'!</div>'; }

?>