<?php

/**
 * Script to display devices
 *
 */


/* verify that user is authenticated! */
isUserAuthenticated ();

/* check if admin */
if(checkAdmin(false))	{ $admin = true; }


# print link to manage
print "<div class='btn-group'>";

if(isset($_GET['sPage'])) {
	print "<a class='btn btn-sm btn-default' href='".create_link("tools","devices")."' data-action='add'  data-switchid='' style='margin-bottom:10px;'><i class='fa fa-chevron-left'></i> ". _('Back')."</a>";
}
elseif($admin) {
	print "<a class='btn btn-sm btn-default' href='".create_link("administration","manageDevices")."' data-action='add'  data-switchid='' style='margin-bottom:10px;'><i class='fa fa-pencil'></i> ". _('Manage')."</a>";
}
print "</div>";


/* print hosts? */
if(isset($_GET['subnetId'])) {
	include('devicesHosts.php');
	
} else {
	/* Print them out */
	print "<div class='devicePrintHolder'>";
	include('devicesPrint.php');
	print "</div>";	
}

?>