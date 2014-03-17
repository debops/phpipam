<?php

/**
 * Script to display devices
 *
 */


/* verify that user is authenticated! */
isUserAuthenticated ();

/* check if admin */
if(checkAdmin(false))	{ $admin = true; }

# title
print "<h4>"._('List of network devices')."</h4>";
print "<hr>";

# print link to manage
if(isset($_GET['deviceid'])) {
	print "<a class='btn btn-sm btn-default' href='tools/devices/' data-action='add'  data-switchid='' style='margin-bottom:10px;'><i class='fa fa-chevron-left'></i> ". _('Back')."</a>";
}
elseif($admin) {
	print "<a class='btn btn-sm btn-default' href='administration/manageDevices/' data-action='add'  data-switchid='' style='margin-bottom:10px;'><i class='fa fa-pencil'></i> ". _('Manage')."</a>";
}


/* print hosts? */
if(isset($_GET['deviceid'])) {
	include('devicesHosts.php');
	
} else {
	/* Print them out */
	print "<div class='devicePrintHolder'>";
	include('devicesPrint.php');
	print "</div>";	
}

?>