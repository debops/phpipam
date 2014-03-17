<?php

/**
 *	Script that checks if IP is alive
 */


/* include required scripts */
require_once('../../functions/functions.php');
require_once( dirname(__FILE__) . '/../../functions/scan/config-scan.php');

/* verify that user is logged in */
isUserAuthenticated(false);

// verify that user has write access
$subnetPerm = checkSubnetPermission ($_POST['subnetId']);
if($subnetPerm < 2) {
	echo _("error").":"._("Insufficient permissions");
	die();
}

//get IP address details
$ip = getIpAddrDetailsById ($_POST['id']);

//try to ping it
$pingRes = pingHost($ip['ip_addr'], 1, 1);

//update last seen if success
if($pingRes==0) { @updateLastSeen($_POST['id']); }
?>

<!-- header -->
<div class="pHeader"><?php print _('Ping check result'); ?></div>

<!-- content -->
<div class="pContent">

	<?php
	# online
	if($pingRes==0) { 
		print "<div class='alert alert-success'>"._("IP address")." $ip[ip_addr] "._("is alive")."</div>";
	}
	# offline
	elseif ($pingRes==1 || $pingRes==2) {
		print "<div class='alert alert-danger'  >"._("IP address")." $ip[ip_addr] "._("is not alive")."</div>";
	}
	# error
	else {
		//get error code
		$ecode = explainPingExit($pingRes);
		print "<div class='alert alert-danger'>"._("Error").": $ecode ($pingRes)</div>";		
	}
	
	?>
</div>

<!-- footer -->
<div class="pFooter">
	<button class="btn btn-sm btn-default hidePopups"><?php print _('Close window'); ?></button>
</div>