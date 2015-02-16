<?php
/**
 * Display VLAN details
 ***********************************************************************/

/* verify that user is authenticated! */
isUserAuthenticated ();

# get VLAN details
$vlan = getVLANbyId($_GET['subnetId']);

# not existing
if(!$vlan) { die("<h4>"._('Error')."</h4><div class='alert alert-danger'>"._('Invalid VLAN id')."!</div>"); }

# get all site settings
$settings = getAllSettings();

# get custom VLAN fields
$customVLANFields = getCustomFields('vlans');

?>

<!-- content print! -->

<!-- for adding IP address! -->
<div id="subnetId" style="display:none"><?php print $subnetId; ?></div>

<?php
print "<a class='btn btn-sm btn-default' href='".create_link("tools","vlan")."' data-action='add'  data-switchid='' style='margin-bottom:10px;'><i class='fa fa-chevron-left'></i> ". _('Back')."</a>";	
?>

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
			$vlan[$key] = str_replace("\n", "<br>",$vlan[$key]);
			
			# fix for boolean
			if($field['type']=="tinyint(1)" || $field['type']=="boolean") {
				if($vlan[$key]==0)		{ $vlan[$key] = "false"; }
				elseif($vlan[$key]==1)	{ $vlan[$key] = "true"; }
				else					{ $vlan[$key] = ""; }
			}

			print "<tr>";
			print "	<th>$key</th>";
			print "	<td style='vertical-align:top;align:left;'>$vlan[$key]</td>";
			print "</tr>";
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

<?php

/**
 * Script to display all slave IP addresses and subnets in content div of subnets table!
 ***************************************************************************************/

/* get all slaves */
$slaves = getAllSubnetsInVlan ($_GET['subnetId']);

/* if none */
if(!$slaves) {
	print "<hr>";
	print "<h4>"._('VLAN')." $vlan[number] (".$vlan['description'].") "._('has no belonging subnets')."</h4>";
}
else {
	/* print title */
	$slaveNum = sizeof($slaves);
	print "<h4>"._('VLAN')." $vlan[number] (".$vlan['description'].") "._('has')." $slaveNum "._('belonging subnets').":</h4><hr><br>";
	
	/* print HTML table */
	print '<table class="slaves table table-striped table-condensed table-hover table-full table-top">'. "\n";
	
	/* headers */
	print "<tr>";
	print "	<th class='small description'>"._('Subnet')."</th>";
	print "	<th>"._('Subnet description')."</th>";
	print "	<th>"._('Section')."</th>";
	print "	<th class='small hidden-xs hidden-sm'>"._('Hosts check')."</th>";
	print "	<th class='hidden-xs hidden-sm'>"._('Requests')."</th>";
	print " <th></th>";
	print "</tr>";
	
	/* print each slave */
	$usedSum = 0;
	$allSum = 0;
	
	# for free space check
	$slaveSize = sizeof($slaves);
	$m = 0;
	
	foreach ($slaves as $slave) {
	
		# check permission
		$permission = checkSubnetPermission ($slave['id']);
	
		if($permission > 0) {
		
		   $section = getSectionDetailsById($slave['sectionId']);

			print "<tr>";
		    print "	<td class='small description'><a href='".create_link("subnets",$section['id'],$slave['id'])."'>".transform2long($slave['subnet'])."/$slave[mask]</a></td>";
		    print "	<td>$slave[description]</td>";
		    
		    # section
		    print "	<td><a href='".create_link("subnets",$section['id'])."'>".$section['name']."</a></td>";

			# host check
			if($slave['pingSubnet'] == 1) 				{ print '<td class="allowRequests small hidden-xs hidden-sm"><i class="fa fa-gray fa-check"></i></td>'; }
			else 										{ print '<td class="allowRequests small hidden-xs hidden-sm"></td>'; }	
		    
		    # count IP addresses
			$hasSlaves = getAllSlaveSubnetsBySubnetId ($slave['id']); 
		
			# slaves details are provided with ipaddressprintslaves script
			if(sizeof($hasSlaves)>0)	{ $ipCount = sizeof(getIpAddressesBySubnetIdSlavesSort ($slave['id'])); }	//ip count - slaves
			else 						{ $ipCount = countIpAddressesBySubnetId ($slave['id']);	}					//ip count - direct subnet  
			
			# allow requests
			if($slave['allowRequests'] == 1) 			{ print '<td class="allowRequests small hidden-xs hidden-sm"><i class="fa fa-gray fa-check"></i></td>'; }
			else 										{ print '<td class="allowRequests small hidden-xs hidden-sm"></td>'; }		
			
			# edit
			$subnetPerm = checkSubnetPermission ($slave['id']);
			if($subnetPerm == 3) {
				print "	<td class='actions'>";
				print "	<div class='btn-group'>";
				print "		<button class='btn btn-xs btn-default editSubnet'     data-action='edit'   data-subnetid='".$slave['id']."'  data-sectionid='".$slave['sectionId']."'><i class='fa fa-gray fa-pencil'></i></button>";
				print "		<button class='btn btn-xs btn-default showSubnetPerm' data-action='show'   data-subnetid='".$slave['id']."'  data-sectionid='".$slave['sectionId']."'><i class='fa fa-gray fa-tasks'></i></button>";
				print "		<button class='btn btn-xs btn-default editSubnet'     data-action='delete' data-subnetid='".$slave['id']."'  data-sectionid='".$slave['sectionId']."'><i class='fa fa-gray fa-times'></i></button>";
				print "	</div>";
				print " </td>";
			}
			else {
				print "	<td class='small actions'>";
				print "	<div class='btn-group'>";
				print "		<button class='btn btn-xs btn-default disabled'><i class='fa fa-gray fa-pencil'></i></button>";
				print "		<button class='btn btn-xs btn-default disabled'><i class='fa fa-gray fa-tasks'></i></button>";
				print "		<button class='btn btn-xs btn-default disabled'><i class='fa fa-gray fa-times'></i></button>";
				print "	</div>";
				print " </td>";		
			}
		
			print '</tr>' . "\n";
			
			# next - for free space check
			$m++;	
		}
	}
	
	print '</table>'. "\n";
	
}
?>