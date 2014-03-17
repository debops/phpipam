<?php

/**
 * Script to display devices
 *
 */


/* verify that user is authenticated! */
isUserAuthenticated ();

/* get hosts under device */
$device = getDeviceById($_GET['deviceid']);

/* Get all IP addresses belonging to switch */
$ipaddresses = getIPaddressesBySwitchName ( $device['id'] );

# title
print "<hr>";



# main table frame
print "<table id='switchMainTable' class='devices table table-striped table-top table-condensed'>";
	
	if(empty($device['hostname'])) 		{ 
		$device['hostname'] = _('Device not specified'); 
		$device['ip_addr']  = "";
	}
	else 										{ 
		$device['ip_addr'] = "($device[ip_addr])";
	}
	
	/* reformat if empty */
	if(empty($device['hostname'])) 				{ $device['hostname'] = "Unspecified";}
	
	# count size
	$size = sizeof($ipaddresses);
	
	# print name
	print "<tbody id='switch'>";
	print "<tr class='switch-title'>";
	print "	<th colspan='7'>";
	print "		<h4> $device[hostname] $device[ip_addr]</h4>";
	print "	</th>";
	print "</tr>";
	print "</tbody>";
	
	# collapsed div with details
	print "<tbody id='content-switch'>";
		
	# headers
	print "<tr>";
	print "	<th>"._('IP address')."</th>";
	print "	<th>"._('Port')."</th>";
	print "	<th>"._('Subnet')."</th>";
	print "	<th colspan='2'>"._('Description')."</th>";
	print "	<th class='hidden-xs'>"._('Hostname')."</th>";
	print "	<th class='hidden-xs hidden-sm'>"._('Owner')."</th>";
	print "</tr>";
	
	if(sizeof($ipaddresses) == 0) {
	print "<tr>";
	print '	<td colspan="8"><div class="alert alert-info">'._('No hosts belonging to this device').'!</div></td>'. "\n";
	print "</tr>";
	}
	
	# IP addresses
	foreach ($ipaddresses as $ip) {
	
		# check permission
		$permission = checkSubnetPermission ($ip['subnetId']);
		
		if($permission != "0") {
			# get subnet details for belonging IP
			$subnet = getSubnetDetails ($ip['subnetId']);
			# get section details
			$section = getSectionDetailsById ($subnet['sectionId']);
	
			# print
			print "<tr>";
			print "	<td class='ip'>".transform2long($ip['ip_addr'])."/$subnet[mask]</td>";
			print "	<td class='port'>$ip[port]</td>";
			print "	<td class='subnet'><a href='subnets/$section[id]/$subnet[id]/'>$subnet[description]</a></td>";
			print "	<td class='description'>$ip[description]</td>";

			# print info button for hover
			print "<td class='note'>";
			if(!empty($ip['note'])) {
				$ip['note'] = str_replace("\n", "<br>",$ip['note']);
				print "	<i class='fa fa-comment-o' rel='tooltip' title='$ip[note]'></i>";
			}
			print "</td>";
		
			print "	<td class='dns hidden-xs'>$ip[dns_name]</td>";
			print "	<td class='owner hidden-xs hidden-sm'>$ip[owner]</td>";
			print "</tr>";
		}
	
	}
	
	print "</tr>";
	print "</tbody>";

print "</table>";			# end major table

?>