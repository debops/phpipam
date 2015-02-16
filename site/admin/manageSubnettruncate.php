<?php

/*
 * Print truncate subnet
 *********************/

/* required functions */
require_once('../../functions/functions.php');

/* filter input */
$_POST = filter_user_input($_POST, true, true, false);
$_POST['action'] = filter_user_input($_POST['action'], false, false, true);

/* must be numeric */
if(!is_numeric($_POST['subnetId']))	{ die('<div class="alert alert-danger">'._("Invalid ID").'</div>'); }

/* verify that user has write permissions for subnet */
$subnetPerm = checkSubnetPermission ($_POST['subnetId']);
if($subnetPerm < 2) 	{ die('<div class="alert alert-danger">'._('You do not have permissions to truncate subnet').'!</div>'); }


/* verify post */
CheckReferrer();

# get subnet details
$subnet = getSubnetDetailsById ($_POST['subnetId']);

# get all IP addresses
$ip_addr = getIpAddressesBySubnetId ($_POST['subnetId']) ;
?>


<!-- header -->
<div class="pHeader"><?php print _('Truncate subnet'); ?></div>


<!-- content -->
<div class="pContent">

	<table class="table table-noborder table-condensed">

    <!-- subnet -->
    <tr>
        <td class="middle"><?php print _('Subnet'); ?></td>
        <td><?php print transform2long($subnet['subnet'])."/$subnet[mask] ($subnet[description])"; ?></td>
    </tr>

    <!-- Mask -->
    <tr>
        <td class="middle"><?php print _('Number of IP addresses'); ?></td>
        <td><?php print sizeof($ip_addr); ?></td>
    </tr>
        
    </table>

    <!-- warning -->
    <div class="alert alert-warning">
    <?php print _('Truncating network will remove all IP addresses, that belong to selected subnet!'); ?>
    </div>

</div>


<!-- footer -->
<div class="pFooter">
	<div class="btn-group">
		<button class="btn btn-sm btn-default hidePopup2"><?php print _('Cancel'); ?></button>
		<button class="btn btn-sm btn-default btn-danger" id="subnetTruncateSubmit" data-subnetId='<?php print $_POST['subnetId']; ?>'><i class="fa fa-trash-o"></i> <?php print _('Truncate subnet'); ?></button>
	</div>

	<div class="subnetTruncateResult"></div>
</div>