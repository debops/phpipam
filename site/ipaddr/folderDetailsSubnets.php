<script type="text/javascript">
/* fix for ajax-loading tooltips */
$('body').tooltip({ selector: '[rel=tooltip]' });
</script>
<?php

/**
 * Script to display all slave IP addresses and subnets in content div of subnets table!
 ***************************************************************************************/

/* get master subnet ID */
$subnetId = $_REQUEST['subnetId'];

/* get all slaves */
$slaves = getAllSlaveSubnetsBySubnetId ($subnetId);

/* get master details */
$master = getSubnetDetailsById($subnetId);

/* get section details */
$section = getSectionDetailsById($master['sectionId']);

/* divide subnets / folders */
foreach($slaves as $s) {
	//folders
	if($s['isFolder']=="1")	{
		$folders[] = $s;
	}
	//subnets
	else {
		$subnets[] = $s;
	}
}

/* first print belonging folders */
if(sizeof($folders)>0) 
{
	/* print title */
	$slaveNum = sizeof($folders);
	print "<h4>$master[description] "._('has')." $slaveNum "._('directly nested folders').":</h4><hr>";
	
	/* print HTML list */
	print "<ul style='margin-bottom:35px;list-style:none'>";
	foreach($folders as $f) {
		print "<li><a href='folder/$section[id]/$f[id]/'><i class='fa fa-folder fa-sfolder'></i> $f[description]</a></li>";
	}
	print "</ul>";
}
/* print title */
if(sizeof($subnets)>0) {
	$slaveNum = sizeof($subnets);
	print "<h4>$master[description] "._('has')." $slaveNum "._('directly nested subnets').":</h4><hr><br>";
	
	/* print HTML table */
	print '<table class="slaves table table-striped table-condensed table-hover table-full table-top">'. "\n";
	
	/* headers */
	print "<tr>";
	print "	<th class='small'>"._('VLAN')."</th>";
	print "	<th class='small description'>"._('Subnet description')."</th>";
	print "	<th>"._('Subnet')."</th>";
	print "	<th class='small hidden-xs hidden-sm'>"._('Used')."</th>";
	print "	<th class='small hidden-xs hidden-sm'>% "._('Free')."</th>";
	print "	<th class='small hidden-xs hidden-sm'>"._('Requests')."</th>";
	print " <th class='actions'></th>";
	print "</tr>";
	
	/* print each slave */
	$usedSum = 0;
	$allSum = 0;
	
	# for free space check
	$slaveSize = sizeof($subnets);
	$m = 0;
	
	foreach ($subnets as $slave) {
	
		
		# reformat empty VLAN
		if(empty($slave['VLAN']) || $slave['VLAN'] == 0 || strlen($slave['VLAN']) == 0) { $slave['VLAN'] = "/"; }
		
		# get VLAN details
		$slave['VLAN'] = subnetGetVLANdetailsById($slave['vlanId']);
		$slave['VLAN'] = $slave['VLAN']['number'];
		
		print "<tr>";
	    print "	<td class='small'>$slave[VLAN]</td>";
	    print "	<td class='small description'><a href='subnets/$section[id]/$slave[id]/'>$slave[description]</a></td>";
	    print "	<td><a href='subnets/$section[id]/$slave[id]/'>".transform2long($slave['subnet'])."/$slave[mask]</a></td>";
	    
	    # count IP addresses
		$hasSlaves = getAllSlaveSubnetsBySubnetId ($slave['id']); 
	
		# slaves details are provided with ipaddressprintslaves script
		if(sizeof($hasSlaves)>0)	{ $ipCount = sizeof(getIpAddressesBySubnetIdSlavesSort ($slave['id'])); }	//ip count - slaves
		else 						{ $ipCount = countIpAddressesBySubnetId ($slave['id']);	}					//ip count - direct subnet  
	
	    
		$calculate = calculateSubnetDetails ( gmp_strval($ipCount), $slave['mask'], $slave['subnet'] );
	    print ' <td class="small hidden-xs hidden-sm">'. $calculate['used'] .'/'. $calculate['maxhosts'] .'</td>'. "\n";
	    print '	<td class="small hidden-xs hidden-sm">'. $calculate['freehosts_percent'] .'</td>';
	    
	    # add to sum if IPv4
	    if ( IdentifyAddress( $slave['subnet'] ) == "IPv4") {
			$usedSum = $usedSum + $calculate['used'];
			$allSum  = $allSum  + $calculate['maxhosts'];    
	    }
		
		# allow requests
		if($slave['allowRequests'] == 1) 			{ print '<td class="allowRequests small hidden-xs hidden-sm"><i class="fa fa-gray fa-check"></i></td>'; }
		else 										{ print '<td class="allowRequests small hidden-xs hidden-sm"><i class="fa fa-gray fa-check"></i></td>'; }
		
		# edit
		$subnetPerm = checkSubnetPermission ($slave['id']);
		if($subnetPerm == 3) {
			print "	<td class='actions'>";
			print "	<div class='btn-group'>";
			print "		<button class='btn btn-xs btn-default editSubnet'     data-action='edit'   data-subnetid='".$slave['id']."'  data-sectionid='".$slave['sectionId']."'><i class='fa fa-gray fa fa-pencil'></i></button>";
			print "		<button class='btn btn-xs btn-default showSubnetPerm' data-action='show'   data-subnetid='".$slave['id']."'  data-sectionid='".$slave['sectionId']."'><i class='fa fa-gray fa fa-tasks'></i></button>";
			print "		<button class='btn btn-xs btn-default editSubnet'     data-action='delete' data-subnetid='".$slave['id']."'  data-sectionid='".$slave['sectionId']."'><i class='fa fa-gray fa fa-times'></i></button>";
			print "	</div>";
			print " </td>";
		}
		else {
			print "	<td class='actions'>";
			print "	<div class='btn-group'>";
			print "		<button class='btn btn-xs btn-default disabled'><i class='fa fa-gray fa fa-pencil'></i></button>";
			print "		<button class='btn btn-xs btn-default disabled'><i class='fa fa-gray fa fa-tasks'></i></button>";
			print "		<button class='btn btn-xs btn-default disabled'><i class='fa fa-gray fa fa-times'></i></button>";
			print "	</div>";
			print " </td>";		
		}
	
		print '</tr>' . "\n";
		
		# next - for free space check
		$m++;	
	}
	
	print '</table>'. "\n";
}

/* No slaves */
if(sizeof($slaves)==0) {
	print "<hr><div class='alert alert-info alert-absolute'>"._("Folder has no nested folders or belonging subnets")."!</div>";
}

?>