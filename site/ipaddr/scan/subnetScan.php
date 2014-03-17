<?php

/*
 * Scan subnet for new hosts
 ***************************/

/* required functions */
require_once('../../../functions/functions.php'); 

/* verify that user is logged in */
isUserAuthenticated(false);

/* verify that user has write permissions for subnet */
$subnetPerm = checkSubnetPermission ($_REQUEST['subnetId']);
if($subnetPerm < 2) 								{ die('<div class="pHeader">Error</div><div class="alert alert-danger">'._('You do not have permissions to modify hosts in this subnet').'!</div><div class="pFooter"><button class="btn btn-sm btn-default hidePopups">'._('Cancel').'</button></div>'); }

/* verify post */
CheckReferrer();

# get subnet details
$subnet = getSubnetDetailsById ($_POST['subnetId']);

# IPv6 is not supported
if ( IdentifyAddress( $subnet['subnet'] ) == "IPv6") { die('<div class="pHeader">Error</div><div class="alert alert-danger">'._('IPv6 scanning is not supported').'!</div><div class="pFooter"><button class="btn btn-sm btn-default hidePopups">'._('Cancel').'</button></div>'); }

# get all IP addresses
$ip_addr = getIpAddressesBySubnetId ($_POST['subnetId']);
?>


<!-- header -->
<div class="pHeader"><?php print _('Scan subnet'); ?></div>


<!-- content -->
<div class="pContent">

	<table class="table table-noborder table-condensed">

    <!-- subnet -->
    <tr>
        <td class="middle"><?php print _('Subnet'); ?></td>
        <td><?php print transform2long($subnet['subnet'])."/$subnet[mask] ($subnet[description])"; ?></td>
    </tr>
    
    <!-- Scan type -->
    <tr>
    	<td><?php print _('Select Scan type'); ?></td>
    	<td>
    		<select name="scanType" id="scanType" class="form-control input-sm input-w-auto">
    			<!-- Discovery scans -->
	    		<optgroup label="<?php print _('Discovery scans');?>">
		    		<option value="DiscoveryPing">Ping <?php print _('scan');?></option>
		    		<option value="DiscoveryTelnet">Telnet <?php print _('scan');?></option>
<!-- 		    		<option value="DiscoveryNmap">NMap <?php print _('scan');?></option> -->
	    		</optgroup>
    			<!-- Status update scans -->
	    		<optgroup label="<?php print _('Status update scans');?>">
		    		<option value="UpdatePing">Ping <?php print _('scan');?></option>
<!-- 		    		<option value="UpdateNmap">NMap <?php print _('scan');?></option> -->
	    		</optgroup>

			</select>
    	</td>
    </tr>
    
    <!-- telnet ports -->
    <tbody id="telnetPorts" style="border-top:0px;display:none;">
    <tr>
    	<td><?php print _('Ports'); ?></td>
    	<td>
	    	<input type="text" name="telnetports" class="form-control input-sm input-w-200" placeholder="<?php print _("Separate multiple ports with ;"); ?>">
    	</td>
    </tr>
    </tbody>
    
    <tbody style="border:0px;">
    <tr>
    	<td><?php print _('Debug');?></td>	
    	<td>
    		<input type="checkbox" name="debug">
    	</td>
    </tr>
    </tbody>
        
    </table>

    <!-- warning -->
    <div class="alert alert-warning alert-block" id="alert-scan">
    &middot; <?php print _('Discovery scans discover new hosts');?><br>
    &middot; <?php print _('Status update scans update alive status for whole subnet');?><br>
    </div>
    
    <!-- result -->
	<div id="subnetScanResult"></div>

</div>


<!-- footer -->
<div class="pFooter">
	<div class="btn-group">
		<button class="btn btn-sm btn-default hidePopups"><?php print _('Cancel'); ?></button>
		<button class="btn btn-sm btn-default btn-success" id="subnetScanSubmit" data-subnetId='<?php print $_POST['subnetId']; ?>'><i class="fa fa-gears"></i> <?php print _('Scan subnet'); ?></button>
	</div>

	<div class="subnetTruncateResult"></div>
</div>