<?php

/**
 * Script to display available VLANs
 */

/* verify that user is authenticated! */
isUserAuthenticated ();

/* check if admin */
if(checkAdmin(false))	{ $admin = true; }

/* all or details */
if(isset($_GET['subnetId']))	{
	include('vlanPrintDetails.php');
}
else {
	include('vlanPrint.php');
}
?>
