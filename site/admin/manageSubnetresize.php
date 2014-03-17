<?php

/*
 * Print resize subnet
 *********************/

/* required functions */
require_once('../../functions/functions.php'); 

/* verify that user has write permissions for subnet */
$subnetPerm = checkSubnetPermission ($_REQUEST['subnetId']);
if($subnetPerm < 3) 	{ die('<div class="alert alert-danger">'._('You do not have permissions to resize subnet').'!</div>'); }


/* verify post */
CheckReferrer();

# get subnet details
$subnet = getSubnetDetailsById ($_POST['subnetId']);
?>


<!-- header -->
<div class="pHeader"><?php print _('Resize subnet'); ?></div>


<!-- content -->
<div class="pContent">

	<form id="subnetResize">
	<table class="table table-noborder table-condensed">

    <!-- subnet -->
    <tr>
        <td class="middle"><?php print _('Subnet'); ?></td>
        <td><?php print transform2long($subnet['subnet']) . " ($subnet[description])"; ?></td>
    </tr>

    <!-- Mask -->
    <tr>
        <td class="middle"><?php print _('Current mask'); ?></td>
        <td><?php print "/".$subnet['mask']; ?></td>
    </tr>

    <!-- new Mask -->
    <tr>
        <td class="middle"><?php print _('New mask'); ?></td>
        <td style="vertical-align:middle">
	        <span class="pull-left" style='margin-right:5px;'> / </span> <input type="text" class="form-control input-sm input-w-100" name="newMask">
	        <input type="hidden" name="subnetId" value="<?php print $_POST['subnetId']; ?>">
        </td>
    </tr>
        
    </table>
    </form> 

    <!-- warning -->
    <div class="alert alert-warning">
    <?php print _('You can change subnet size by specifying new mask (bigger or smaller). Please note'); ?>:
    <ul>
    	<li><?php print _('If subnet has hosts outside of resized subnet resizing will not be possible'); ?></li>
    	<li><?php print _('If strict mode is enabled check will be made to ensure it is still inside master subnet'); ?></li>
    </ul>
    </div>

</div>


<!-- footer -->
<div class="pFooter">
	<div class="btn-group">
		<button class="btn btn-sm btn-default hidePopup2"><?php print _('Cancel'); ?></button>
		<button class="btn btn-sm btn-default btn-success" id="subnetResizeSubmit"><i class="fa fa-check"></i> <?php print _('Resize subnet'); ?></button>
	</div>

	<div class="subnetResizeResult"></div>
</div>