<?php
/**
 * Display VLAN details
 ***********************************************************************/

/* verify that user is authenticated! */
isUserAuthenticated ();

# get VLAN details
$vrf = getVRFDetailsById ($_REQUEST['vrfId']);

# not existing
if(!$vrf) {
	die("<div class='alert alert-error'>"._('Invalid VRF id')."!</div>");
}

# get all site settings
$settings = getAllSettings();
?>

<!-- content print! -->

<!-- for adding IP address! -->
<div id="subnetId" style="display:none"><?php print $subnetId; ?></div>

<!-- subnet details upper table -->
<h4><?php print _('VRF details'); ?></h4>
<hr>

<table class="ipaddress_subnet table-condensed table-full">
	<tr>
		<th><?php print _('RD'); ?></th>
		<td><?php print $vrf['rd']; ?></td>
	</tr>
	<tr>
		<th><?php print _('Name'); ?></th>
		<td>
			<?php print $vrf['name']; ?>
		</td>
	</tr>
	<tr>
		<th><?php print _('Description'); ?></th>
		<td><?php print html_entity_decode($vrf['description']); ?></td>
	</tr>

	<?php
	
	/* action button groups */
	print "<tr>";
	print "	<th style='vertical-align:bottom;align:left;'>"._('Actions')."</th>";
	print "	<td style='vertical-align:bottom;align:left;'>";

	print "	<div class='btn-toolbar' style='margin-bottom:0px'>";
	print "	<div class='btn-group'>";
	
	# permissions
	if(checkAdmin (false)) {
		print "		<button class='btn btn-small vrfManagement' data-action='edit'   data-vrfId='$vrf[vrfId]'><i class='icon-gray icon-pencil'></i></button>";
		print "		<button class='btn btn-small vrfManagement' data-action='delete' data-vrfId='$vrf[vrfId]'><i class='icon-gray icon-remove'></i></button>";
	}
		
	print "	</div>";
	print "	</div>";
	
	print "	</td>";
	print "</tr>";
	
	?>

</table>	<!-- end subnet table -->
<br>