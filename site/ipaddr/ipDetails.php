<?php

/**
 * Script to display IP address info and history
 ***********************************************/

/* verify that user is authenticated! */
isUserAuthenticated ();

# get site settings
if(sizeof($settings) == 0) { $settings = getAllSettings(); }

# get IP address details
$ip      = getIpAddrDetailsById($_REQUEST['ipaddrid']);
$subnet  = getSubnetDetailsById($_REQUEST['subnetId']);
$section = getSectionDetailsById($_REQUEST['section']);

# get all selected fields for IP print
$setFieldsTemp = getSelectedIPaddrFields();
// format them to array!
$setFields = explode(";", $setFieldsTemp);

# get all custom fields
$myFields = getCustomFields('ipaddresses');

# set ping statuses
$statuses = explode(";", $settings['pingStatus']);

# permissions
$permission = checkSubnetPermission ($_REQUEST['subnetId']);
# section permissions
$permissionsSection = checkSectionPermission ($_REQUEST['section']);

# if 0 die
if($permission == "0")	{ die("<div class='alert alert-danger'>"._('You do not have permission to access this network')."!</div>"); }

# reformat empty fields
foreach($ip as $k=>$i) {
	if(strlen($i)==0) {
		$ip[$k] = "/";
	}
}

#header
print "<h4>"._('IP address details')."</h4><hr>";

# back
print "<a class='btn btn-default btn-sm btn-default' href='subnets/$_REQUEST[section]/$_REQUEST[subnetId]/'><i class='fa fa-chevron-left'></i> "._('Back to subnet')."</a>";

# check if it exists, otherwise print error
if(sizeof($ip)>1) {
	
	# table - details
	print "<table class='ipaddress_subnet table table-noborder table-condensed' style='margin-top:10px;'>";
	
	//ip
	print "<tr>";
	print "	<th>"._('IP address')."</th>";
	print "	<td><strong>$ip[ip_addr]</strong></td>";
	print "</tr>";
	
	//description
	print "<tr>";
	print "	<th>"._('Description')."</th>";
	print "	<td>$ip[description]</td>";
	print "</tr>";
	
	//hierarchy
	print "<tr>";
	print "	<th>"._('Hierarchy')."</th>";
	print "	<td>";
	printBreadCrumbs($_REQUEST);
	print "</td>";
	print "</tr>";
	
	//subnet
	print "<tr>";
	print "	<th>"._('Subnet')."</th>";
	print "	<td>".Transform2long($subnet['subnet'])."/$subnet[mask] ($subnet[description])</td>";
	print "</tr>";
	
	//state
	print "<tr>";
	print "	<th>"._('IP status')."</th>";
	print "	<td>";
	
	if ($ip['state'] == "0") 	  { $stateClass = _("Offline"); }
	else if ($ip['state'] == "2") { $stateClass = _("Reserved"); }
	else if ($ip['state'] == "3") { $stateClass = _("DHCP"); }
	else						  { $stateClass = _("Online"); }
	
	print $stateClass;
	print reformatIPState($ip['state'], true, false);
	
	print "	</td>";
	print "</tr>";
	
	//hostname
	print "<tr>";
	print "	<th>"._('Hostname')."</th>";
	print "	<td>$ip[dns_name]</td>";
	print "</tr>";
	
	//mac
	if(in_array('owner', $setFields)) {
	print "<tr>";
	print "	<th>"._('Owner')."</th>";
	print "	<td>$ip[owner]</td>";
	print "</tr>";
	}
	
	//mac
	if(in_array('mac', $setFields)) {
	print "<tr>";
	print "	<th>"._('MAC address')."</th>";
	print "	<td>$ip[mac]</td>";
	print "</tr>";
	}
	
	//note
	if(in_array('note', $setFields)) {
	print "<tr>";
	print "	<th>"._('Note')."</th>";
	print "	<td></td>";
	print "</tr>";
	}
	
	//switch
	if(in_array('switch', $setFields)) {
	print "<tr>";
	print "	<th>"._('Switch')."</th>";
	if(strlen($ip['switch'])>0) {
		# get switch
		$switch = getDeviceById ($ip['switch']);
		if(strlen($switch['description'])==0) $switch['description'] = "";
		else								  $switch['description'] = "($switch[description])";
		print "	<td>$switch[hostname] $switch[description]</td>";	
	} else {
		print "	<td>$ip[switch]</td>";	
	}
	print "</tr>";
	}
	
	//port
	if(in_array('port', $setFields)) {
	print "<tr>";
	print "	<th>"._('Port')."</th>";
	print "	<td>$ip[port]</td>";
	print "</tr>";
	}
	
	//last edited
	print "<tr>";
	print "	<th>"._('Last edited')."</th>";
	if(strlen($ip['editDate'])>1) {
		print "	<td>$ip[editDate]</td>";
	} else {
		print "	<td>"._('Never')."</td>";
	}
	print "</tr>";
	
	
	# avalibility
	
	print "<tr>";
	print "	<td colspan='2'><hr></td>";
	print "</tr>";
	print "<tr>";
	
	//calculate
	$tDiff = time() - strtotime($ip['lastSeen']);
	if($ip['excludePing']=="1" ) 						{ $hStatus = ""; 			$hTooltip = ""; }
	elseif($tDiff < $statuses[0])						{ $hStatus = "success";		$hTooltip = _("Device is alive")."<br>"._("Last seen").": ".$ip['lastSeen']; }
	elseif($tDiff < $statuses[1])						{ $hStatus = "warning"; 	$hTooltip = _("Device warning")."<br>"._("Last seen").": ".$ip['lastSeen']; }
	elseif($tDiff < 2592000)							{ $hStatus = "error"; 		$hTooltip = _("Device is offline")."<br>"._("Last seen").": ".$ip['lastSeen'];}
	elseif($ip['lastSeen'] == "0000-00-00 00:00:00") 	{ $hStatus = "neutral"; 	$hTooltip = _("Device is offline")."<br>"._("Last seen").": "._("Never");}
	else												{ $hStatus = "neutral"; 	$hTooltip = _("Device status unknown");}		    
	
	print "	<th>"._('Avalibility')."<br><span class='status status-ip status-$hStatus' style='pull-right'></span></th>";
	print "	<td>";
	print "$hTooltip";
	
	print "	</td>";
	print "</tr>";
	
	# custom fields
	if(sizeof($myFields)>0) {
	
	//divider
	print "<tr>";
	print "	<td colspan='2'><hr></td>";
	print "</tr>";
	
	//customs
	foreach($myFields as $k=>$f) {
		print "<tr>";
		print "	<th>$f[name]</th>";
		print "	<td>".$ip[$f['name']]."</td>";
		print "</tr>";	
	}
	
	}
	
	# actions
	print "<tr>";
	print "	<td colspan='2'><hr></td>";
	print "</tr>";
	print "<tr>";
	print "	<th>"._('Actions')."</th>";
	
	print "<td class='btn-actions'>";
	print "	<div class='btn-toolbar'>";
	print "	<div class='btn-group'>";
	# write permitted
	if( $permission > 1) {
		if($ip['class']=="range-dhcp") 
		{
			print "		<a class='edit_ipaddress   btn btn-default btn-xs modIPaddr' data-action='edit'   data-subnetId='".$ip['subnetId']."' data-id='".$ip['id']."' data-stopIP='".$ip['stopIP']."' href='#' 		   rel='tooltip' data-container='body' title='"._('Edit IP address details')."'>	<i class='fa fa-gray fa-pencil'>  </i></a>";
			print "		<a class='				   btn btn-default btn-xs disabled' href='#'>																																													<i class='fa fa-gray fa-exchange'> </i></a>"; 
			print "		<a class='				   btn btn-default btn-xs disabled' href='#'>																																													<i class='fa fa-gray fa-search'></i></a>";
			print "		<a class='				   btn btn-default btn-xs disabled' href='#'>																																													<i class='fa fa-gray fa-envelope-o'></i></a>";
			print "		<a class='delete_ipaddress btn btn-default btn-xs modIPaddr' data-action='delete' data-subnetId='".$ip['subnetId']."' data-id='".$ip['id']."' href='#' id2='".Transform2long($ip['ip_addr'])."' rel='tooltip' data-container='body' title='"._('Delete IP address')."'>		<i class='fa fa-gray fa-times'>  </i></a>";											
		} 
		else 
		{
			print "		<a class='edit_ipaddress   btn btn-default btn-xs modIPaddr' data-action='edit'   data-subnetId='".$ip['subnetId']."' data-id='".$ip['id']."' href='#' 											   rel='tooltip' data-container='body' title='"._('Edit IP address details')."'>												<i class='fa fa-gray fa-pencil'></i></a>";
			print "		<a class='ping_ipaddress   btn btn-default btn-xs' data-subnetId='".$ip['subnetId']."' data-id='".$ip['id']."' href='#' 						   													rel='tooltip' data-container='body' title='"._('Check avalibility')."'>													<i class='fa fa-gray fa-exchange'></i></a>"; 
			print "		<a class='search_ipaddress btn btn-default btn-xs         "; if(strlen($dnsResolved['name']) == 0) { print "disabled"; } print "' href='tools/search/$dnsResolved[name]' "; if(strlen($dnsResolved['name']) != 0)   { print "rel='tooltip' data-container='body' title='"._('Search same hostnames in db')."'"; } print ">	<i class='fa fa-gray fa-search'></i></a>";
			print "		<a class='mail_ipaddress   btn btn-default btn-xs          ' href='#' data-id='".$ip['id']."' rel='tooltip' data-container='body' title='"._('Send mail notification')."'>																																					<i class='fa fa-gray fa-envelope-o'></i></a>";
			print "		<a class='delete_ipaddress btn btn-default btn-xs modIPaddr' data-action='delete' data-subnetId='".$ip['subnetId']."' data-id='".$ip['id']."' href='#' id2='".Transform2long($ip['ip_addr'])."' rel='tooltip' data-container='body' title='"._('Delete IP address')."'>														<i class='fa fa-gray fa-times'></i></a>";											
		}
	}
	# write not permitted
	else {
		if($ip['class']=="range-dhcp") 
		{
			print "		<a class='edit_ipaddress   btn btn-default btn-xs disabled' rel='tooltip' data-container='body' title='"._('Edit IP address details (disabled)')."'>	<i class='fa fa-gray fa-pencil'>  </i></a>";
			print "		<a class='				   btn btn-default btn-xs disabled' href='#'>																<i class='fa fa-gray fa-retweet'> </i></a>"; 
			print "		<a class='				   btn btn-default btn-xs disabled' href='#'>																<i class='fa fa-gray fa-search'></i></a>";
			print "		<a class='				   btn btn-default btn-xs disabled' href='#'>																<i class='fa fa-gray fa-envelope'></i></a>";
			print "		<a class='delete_ipaddress btn btn-default btn-xs disabled' rel='tooltip' data-container='body' title='"._('Delete IP address (disabled)')."'>			<i class='fa fa-gray fa-times'>  </i></a>";				
		}
		else 
		{
			print "		<a class='edit_ipaddress   btn btn-default btn-xs disabled' rel='tooltip' data-container='body' title='"._('Edit IP address details (disabled)')."'>							<i class='fa fa-gray fa-pencil'>  </i></a>";
			print "		<a class='				   btn btn-default btn-xs disabled'  data-id='".$ip['id']."' href='#' rel='tooltip' data-container='body' title='"._('Check avalibility')."'>		<i class='fa fa-gray fa-retweet'>  </i></a>";
			print "		<a class='search_ipaddress btn btn-default btn-xs         "; if(strlen($dnsResolved['name']) == 0) { print "disabled"; } print "' href='tools/search/$dnsResolved[name]' "; if(strlen($dnsResolved['name']) != 0) { print "rel='tooltip' data-container='body' title='"._('Search same hostnames in db')."'"; } print ">	<i class='fa fa-gray fa-search'></i></a>";
			print "		<a class='mail_ipaddress   btn btn-default btn-xs          ' href='#' data-id='".$ip['id']."' rel='tooltip' data-container='body' title='"._('Send mail notification')."'>		<i class='fa fa-gray fa-envelope'></i></a>";
			print "		<a class='delete_ipaddress btn btn-default btn-xs disabled' rel='tooltip' data-container='body' title='"._('Delete IP address (disabled)')."'>				<i class='fa fa-gray fa-times'>  </i></a>";				
		}
	}
	print "	</div>";
	print "	</div>";
	print "</td>";		
	
	print "</tr>";
	
	
	print "</table>";

}
# not exisitng
else {
	print "<div class='alert alert-danger'>"._('IP address not existing in database')."!</div>";
}
?>