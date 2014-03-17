<?php
/**
 * Display VLAN details
 ***********************************************************************/

/* verify that user is authenticated! */
isUserAuthenticated ();

# get VLAN details
$vlan = getVLANbyId($_REQUEST['vlanId']);

# not existing
if(!$vlan) {
	die("<div class='alert alert-danger'>"._('Invalid VLAN id')."!</div>");
}

# get all site settings
$settings = getAllSettings();

# get custom VLAN fields
$customVLANFields = getCustomFields('vlans');
?>

<!-- content print! -->

<!-- for adding IP address! -->
<div id="subnetId" style="display:none"><?php print $subnetId; ?></div>

<!-- subnet details upper table -->
<h4><?php print _('VLAN details'); ?></h4>
<hr>

<table class="ipaddress_subnet table-condensed table-full">
	<tr>
		<th><?php print _('Number'); ?></th>
		<td><?php print '<b>'. $vlan['number']; ?></td>
	</tr>
	<tr>
		<th><?php print _('Name'); ?></th>
		<td>
			<?php print $vlan['name']; ?>
		</td>
	</tr>
	<tr>
		<th><?php print _('Description'); ?></th>
		<td><?php print html_entity_decode($vlan['description']); ?></td>
	</tr>

	<?php	
	/* print custom subnet fields if any */
	if(sizeof($customVLANFields) > 0) {
		foreach($customVLANFields as $key=>$field) {
			if(strlen($vlan[$key])>0) {
			$vlan[$key] = str_replace("\n", "<br>",$vlan[$key]);
			print "<tr>";
			print "	<th>$key</th>";
			print "	<td style='vertical-align:top;align:left;'>$vlan[$key]</td>";
			print "</tr>";
			}
		}
	}
	
	
	/* action button groups */
	print "<tr>";
	print "	<th style='vertical-align:bottom;align:left;'>"._('Actions')."</th>";
	print "	<td style='vertical-align:bottom;align:left;'>";

	print "	<div class='btn-toolbar' style='margin-bottom:0px'>";
	print "	<div class='btn-group'>";
	
	# permissions
	if(checkAdmin (false)) {
		print "		<button class='btn btn-xs btn-default editVLAN' data-action='edit'   data-vlanid='$vlan[vlanId]'><i class='fa fa-pencil'></i></button>";
		print "		<button class='btn btn-xs btn-default editVLAN' data-action='delete' data-vlanid='$vlan[vlanId]'><i class='fa fa-times'></i></button>";
	}
		
	print "	</div>";
	print "	</div>";
	
	print "	</td>";
	print "</tr>";
	
	?>

</table>	<!-- end subnet table -->
<br>