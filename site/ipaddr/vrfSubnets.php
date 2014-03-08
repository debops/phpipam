<?php

/**
 * Script to display all slave IP addresses and subnets in content div of subnets table!
 ***************************************************************************************/

/* get all slaves */
$slaves = getAllSubnetsInSectionVRF ($_REQUEST['vrfId'], $_REQUEST['section']);


/* if none */
if(!$slaves) {
	print "<hr>";
	print "<h4>"._('VLAN')." $vrf[number] (".$vrf['description'].") "._('has no belonging subnets')."</h4>";
}
else {
	/* print title */
	$slaveNum = sizeof($slaves);
	print "<h4>"._('VLAN')." $vrf[number] (".$vrf['description'].") "._('has')." $slaveNum "._('belonging subnets').":</h4><hr><br>";
	
	/* print HTML table */
	print '<table class="slaves table table-striped table-condensed table-hover table-full table-top">'. "\n";
	
	/* headers */
	print "<tr>";
	print "	<th class='small description'>"._('Subnet description')."</th>";
	print "	<th>"._('Subnet')."</th>";
	print "	<th class='small'>"._('Hosts check')."</th>";
	print "	<th class='small'>"._('Used')."</th>";
	print "	<th class='small'>% "._('Free')."</th>";
	print "	<th class='small'>"._('Requests')."</th>";
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
		
			print "<tr>";
		    print "	<td class='small description'><a href='subnets/$_REQUEST[section]/$slave[id]/'>$slave[description]</a></td>";
		    print "	<td><a href='subnets/$_REQUEST[section]/$slave[id]/'>".transform2long($slave['subnet'])."/$slave[mask]</a></td>";

			# host check
			if($slave['pingSubnet'] == 1) 				{ print '<td class="allowRequests small">'._('enabled').'</td>'; }
			else 										{ print '<td class="allowRequests small"></td>'; }	
		    
		    # count IP addresses
			$hasSlaves = getAllSlaveSubnetsBySubnetId ($slave['id']); 
		
			# slaves details are provided with ipaddressprintslaves script
			if(sizeof($hasSlaves)>0)	{ $ipCount = sizeof(getIpAddressesBySubnetIdSlavesSort ($slave['id'])); }	//ip count - slaves
			else 						{ $ipCount = countIpAddressesBySubnetId ($slave['id']);	}					//ip count - direct subnet  
		
		    
			$calculate = calculateSubnetDetails ( gmp_strval($ipCount), $slave['mask'], $slave['subnet'] );
		    print ' <td class="small">'. $calculate['used'] .'/'. $calculate['maxhosts'] .'</td>'. "\n";
		    print '	<td class="small">'. $calculate['freehosts_percent'] .'</td>';
		    
		    # add to sum if IPv4
		    if ( IdentifyAddress( $slave['subnet'] ) == "IPv4") {
				$usedSum = $usedSum + $calculate['used'];
				$allSum  = $allSum  + $calculate['maxhosts'];    
		    }
			
			# allow requests
			if($slave['allowRequests'] == 1) 			{ print '<td class="allowRequests small">'._('enabled').'</td>'; }
			else 										{ print '<td class="allowRequests small"></td>'; }		
			
			# edit
			$subnetPerm = checkSubnetPermission ($slave['id']);
			if($subnetPerm == 3) {
				print "	<td class='small'>";
				print "	<div class='btn-group'>";
				print "		<button class='btn btn-mini editSubnet'     data-action='edit'   data-subnetid='".$slave['id']."'  data-sectionid='".$slave['sectionId']."'><i class='icon-gray icon-pencil'></i></button>";
				print "		<button class='btn btn-mini showSubnetPerm' data-action='show'   data-subnetid='".$slave['id']."'  data-sectionid='".$slave['sectionId']."'><i class='icon-gray icon-tasks'></i></button>";
				print "		<button class='btn btn-mini editSubnet'     data-action='delete' data-subnetid='".$slave['id']."'  data-sectionid='".$slave['sectionId']."'><i class='icon-gray icon-remove'></i></button>";
				print "	</div>";
				print " </td>";
			}
			else {
				print "	<td class='small'>";
				print "	<div class='btn-group'>";
				print "		<button class='btn btn-mini disabled'><i class='icon-gray icon-pencil'></i></button>";
				print "		<button class='btn btn-mini disabled'><i class='icon-gray icon-tasks'></i></button>";
				print "		<button class='btn btn-mini disabled'><i class='icon-gray icon-remove'></i></button>";
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