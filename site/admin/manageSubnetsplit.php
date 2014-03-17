<?php

/*
 * Print resize split
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

# check if it has slaves - if yes it cannot be splitted!
$slaves = getAllSlaveSubnetsBySubnetId ($_POST['subnetId']);
if(sizeof($slaves) > 0) { die("<div class='alert alert-warning'>".('Only subnets that have no nested subnets can be splitted')."!</div>"); }

# calculate max split
$type = IdentifyAddress( transform2long($subnet['subnet']) );	# type for max resize

if($type == "IPv4")	{ $maxMask = "30";  $type = 0; }
else				{ $maxMask = "126"; $type = 1; }

$n = 2;		# step
$m = 0;		# array id
				
for($mask=($subnet['mask']+1); $mask<=$maxMask; $mask++) {
	# set vars
	$opts[$m]['mask']   = $mask;
	$opts[$m]['number'] = $n;
	$opts[$m]['max']    = MaxHosts( $mask, $type ); 
	
	# next
	$m++;
	$n = $n * 2;
	
	# max number = 16!
	if($n > 256) {
		$mask = 1000;
	}
}
?>


<!-- header -->
<div class="pHeader"><?php print _('Split subnet'); ?></div>


<!-- content -->
<div class="pContent">

	<form id="subnetSplit">
	<table class="table table-noborder table-condensed">

    <!-- subnet -->
    <tr>
        <td class="middle"><?php print _('Subnet'); ?></td>
        <td><?php print transform2long($subnet['subnet']) . "/$subnet[mask] ($subnet[description])"; ?></td>
    </tr>

    <!-- number of new subnets -->
    <tr>
        <td class="middle"><?php print _('Number of subnets'); ?></td>
        <td style="vertical-align:middle">
	    	<select name="number" class="form-control input-sm input-w-auto">
	    	<?php
	    	foreach($opts as $line) {
		    	print "<option value='$line[number]'>$line[number]x /$line[mask] subnet ($line[number]x $line[max] hosts)</option>";
	    	}
	    	?>
	    	</select>
	    	<input type="hidden" name="subnetId" value="<?php print $subnet['id']; ?>">
        </td>
    </tr>

    <!-- Group under current -->
    <tr>
        <td class="middle"><?php print _('Group under current'); ?></td>
        <td>
	        <select name="group" class="form-control input-sm input-w-auto">
	        	<option value="no" ><?php print _('No'); ?></option>
	        	<option value="yes"><?php print _('Yes'); ?></option>
	        </select>
        </td>
    </tr>
    
    <!-- strict mode -->
    <tr>
    	<td><?php print _('Strict mode'); ?></td>
    	<td>
	    	<input type="checkbox" name="strict" value="yes" checked="checked">
    	</td>
    </tr>
        
    </table>
    </form> 

    <!-- warning -->
    <div class="alert alert-warning">
    <?php print _('You can split subnet to smaller subnets by specifying new subnets. Please note:'); ?>
    <ul>
    	<li><?php print _('Existing IP addresses will be assigned to new subnets'); ?></li>
    	<li><?php print _('Group under current will create new nested subnets under current one'); ?></li>
    	<li><?php print _('If existing IP will fall to subnet/broadcast of new subnets split will fail, except if strict mode is disabled'); ?></li>
    </ul>
    </div>

</div>


<!-- footer -->
<div class="pFooter">
	<div class="btn-group">
		<button class="btn btn-sm btn-default hidePopup2"><?php print _('Cancel'); ?></button>
		<button class="btn btn-sm btn-default btn-success" id="subnetSplitSubmit"><i class="fa fa-ok"></i> <?php print _('Split subnet'); ?></button>
	</div>

	<div class="subnetSplitResult"></div>
</div>