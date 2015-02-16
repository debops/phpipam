<div class="ipaddresses">

<?php

/**
 *	Script to show nested subnets / hierarchy or print IP address list and subnet details
 */


/* filter input */
$_GET = filter_user_input($_GET, true, true, false);

/* must be numeric */
if(!is_numeric($_GET['subnetId']))	{ die('<div class="alert alert-danger">'._("Invalid ID").'</div>'); }
if(!is_numeric($_GET['section']))	{ die('<div class="alert alert-danger">'._("Invalid ID").'</div>'); }


# ip address details, subnets, vlans or vrfs

# ip address details
if($_GET['page'] == "subnets" && isset($_GET['ipaddrid'])) {
		# print ip address details
		print "<div class='subnetDetails'>";
		include_once("ipDetails.php");
		print "</div>";
		
		if($settings['enableChangelog'] == 1) {
			# Changelog
			print '<div class="ipaddresses_overlay">';
			include_once('ipDetailsChangelog.php');
			print '</div>';
		}
}
# subnets
elseif($_GET['page'] == "subnets") {
	# fetch subnet details
	$slaves = getAllSlaveSubnetsBySubnetId ($_GET['subnetId']);
	
	# print subnet and ip addresses
	if(sizeof($slaves) == 0) 	{ 
		# print subnets
		print "<div class='subnetDetails'>";
		include_once("subnetDetails.php");
		print "</div>";
		
		# IP address table  
		print '<div class="ipaddresses_overlay">';
		include_once('ipAddressPrintTable.php');
		print '</div>';
	}
	# print slaves
	else { 
		# subnet details for slaves
		print "<div class='subnetDetails'>";
		include_once("subnetDetailsSlaves.php");
		print "</div>";
		
		# subnet slaves print subnets
		print "<div class='subnetSlaves'>";
		include_once("ipAddressPrintTableSlaves.php"); 
		print "</div>";
	
		# IP address table  
		print '<div class="ipaddresses_overlay">';
		include_once('ipAddressPrintTable.php');
		print '</div>';
	
		# IP address table - orphaned slaves
		print '<div class="ipaddresses_overlay">';
		include_once('ipAddressPrintTableOrphaned.php');
		print '</div>';
	}
}
# VLANSs
elseif($_GET['page'] == "vlan") {
		# print VLAN details
		print "<div class='subnetDetails'>";
		include_once("vlanDetails.php");
		print "</div>";
		
		# Subnets in VLAN
		print '<div class="ipaddresses_overlay">';
		include_once('vlanSubnets.php');
		print '</div>';	
}
# VRFS
elseif($_GET['page'] == "vrf") {
		# print VRF details
		print "<div class='subnetDetails'>";
		include_once("vrfDetails.php");
		print "</div>";
		
		# Subnets in VRF
		print '<div class="ipaddresses_overlay">';
		include_once('vrfSubnets.php');
		print '</div>';		
}
# folders
elseif($_GET['page'] == "folder") {
		# print VRF details
		print "<div class='subnetDetails'>";
		include_once("folderDetails.php");
		print "</div>";
		
		# Subnets in VRF
		print '<div class="ipaddresses_overlay">';
		include_once('folderDetailsSubnets.php');
		print '</div>';		
}


?>

</div>