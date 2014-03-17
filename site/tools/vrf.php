<?php

/**
 * Script to display all VRFs
 *
 */


/* verify that user is authenticated! */
isUserAuthenticated ();

/* check if admin */
if(checkAdmin(false))	{ $admin = true; }

/* get all VLANs and subnet descriptions */
$vrfs = getAllVRFs ();


/* title */
print "<h4>"._('Available VRFs and belonging subnets')."</h4>";
print "<hr>";
if($admin) {
	print "<a class='btn btn-sm btn-default' href='administration/manageVRF/' data-action='add'  data-switchid=''><i class='fa fa-pencil'></i> ". _('Manage')."</a>";
}

/* for each VRF check which subnet has it configured */
if(!$vrfs) { print "<div class='alert alert-info'>"._('No VRFs configured')."!</div>"; }
else {
	# print table
	print "<table id='vrf' class='table table-striped table-condensed table-top'>";
	
	foreach ($vrfs as $vrf) {

	# print table body
	print "<tbody>";

	# section names
	print "<tr class='vrf-title'>";
    print "	<th colspan='8'><h4>$vrf[name]</h4></th>";
	print "</tr>";	
	
	# fetch subnets in vrf
	$subnets = getAllSubnetsInVRF($vrf['vrfId']);
	
	# headers
	print "	<tr>";
	print "	<th>"._('VLAN')."</th>";	
	print "	<th>"._('Description')."</td>";
	print "	<th>"._('Subnet')."</td>";
	print "	<th>"._('Master Subnet')."</td>";
	print "	<th class='hidden-xs hidden-sm'>"._('Used')."</td>";
	print "	<th class='hidden-xs hidden-sm'>"._('Free')." [%]</td>";
	print "	<th class='hidden-xs hidden-sm'>"._('Requests')."</td>";
	print "</tr>";	

	# subnets
	if($subnets) {
		foreach ($subnets as $subnet) {
	
			# check permission
			$permission = checkSubnetPermission ($subnet['id']);
		
			if($permission != "0") {
	
				# check if it is master
				if( ($subnet['masterSubnetId'] == 0) || (empty($subnet['masterSubnetId'])) ) 	{ $masterSubnet = true; }
				else 																			{ $masterSubnet = false; }
	
				print "<tr>";
	
				# get VLAN details
				$subnet['VLAN'] = subnetGetVLANdetailsById($subnet['vlanId']);
				$subnet['VLAN'] = $subnet['VLAN']['number'];
	
				# reformat empty VLAN
				if(empty($subnet['VLAN']) || $subnet['VLAN'] == 0) { $subnet['VLAN'] = ""; }
				
				# get section name
				$section = getSectionDetailsById($subnet['sectionId']);
	
				print "	<td>$subnet[VLAN]</td>";
				print "	<td>$subnet[description]</td>";
				print "	<td><a href='subnets/$section[id]/$subnet[id]/'>".transform2long($subnet['subnet'])."/$subnet[mask]</a></td>";    
	    
				if($masterSubnet) { 
					print '	<td>/</td>' . "\n"; 
				}
				else {
					$master = getSubnetDetailsById ($subnet['masterSubnetId']);
					# orphaned
					if(strlen($master['subnet']) == 0)	{ print "	<td><div class='alert alert-warning'>"._('Master subnet does not exist')."!</div></td>";}
					else 								{ print "	<td><a href='subnets/$subnet[sectionId]/$subnet[masterSubnetId]/'>".transform2long($master['subnet'])."/$master[mask] ($master[description])</a></td>"; }
				}
	
				# details
				if( (!$masterSubnet) || (!subnetContainsSlaves($subnet['id']))) {
					$ipCount = countIpAddressesBySubnetId ($subnet['id']);
					$calculate = calculateSubnetDetails ( gmp_strval($ipCount), $subnet['mask'], $subnet['subnet'] );

					print ' <td class="used hidden-xs hidden-sm">'. reformatNumber($calculate['used']) .'/'. reformatNumber($calculate['maxhosts']) .'</td>'. "\n";
					print '	<td class="free hidden-xs hidden-sm">'. reformatNumber($calculate['freehosts_percent']) .' %</td>';
				}
				else {
					print '<td class="hidden-xs hidden-sm"></td>'. "\n";
					print '<td class="hidden-xs hidden-sm"></td>'. "\n";
				}
	
				# allow requests
				if($subnet['allowRequests'] == 1) 	{ print '<td class="allowRequests requests hidden-xs hidden-sm">'._('enabled').'</td>'; }
				else 								{ print '<td class="allowRequests hidden-xs hidden-sm"></td>'; }
    
				print '</tr>' . "\n";
			}
		}
	}
	# no subnets!
	else {
		print '<tr>'. "\n";
		print '<td colspan="8"><div class="alert alert-info">'._('No subnets belonging to this VRF').'!</div></td>'. "\n";
		print '</tr>'. "\n";
	}
	
	/* end */
	print '</tbody>';
}
}
print "</table>";

?>