<?php

/**
 *	Script that checks if IP is alive
 */


/* include required scripts */
require_once('../../functions/functions.php');

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
$pingRes = pingHostPear($ip['ip_addr'], 1);

//update last seen if success
if($pingRes['code']==0) { @updateLastSeen($_POST['id']); }
?>

<!-- header -->
<div class="pHeader"><?php print _('Ping check result'); ?></div>

<!-- content -->
<div class="pContent">
	<?php if($pingRes['code']==2) { ?>
		<div class="alert alert-error"><?php print _("Error").": $pingRes[text]"; ?></div>
	<?php } elseif($pingRes['code']==0) { ?>
		<div class="alert alert-success"><?php print _("IP address")." ".$ip['ip_addr']." "._("is alive"); ?><hr><?php print $pingRes['text']; ?></div>
	<?php } elseif($pingRes['code']==1) { ?>
		<div class="alert alert-error"><?php print _("IP address")." ".$ip['ip_addr']." "._("is not alive"); ?></div>
	<?php } elseif($pingRes['code']==3) { ?>
		<div class="alert alert-error"><?php print _("Error")." $pingRes[text]"; ?></div>
	<?php } ?>
</div>

<!-- footer -->
<div class="pFooter">
	<button class="btn btn-small hidePopups"><?php print _('Close window'); ?></button>
</div>