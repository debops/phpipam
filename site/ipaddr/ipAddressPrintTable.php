<script type="text/javascript">
/* fix for ajax-loading tooltips */
$('body').tooltip({ selector: '[rel=tooltip]' });
</script>

<?php

/**
 * Print sorted IP addresses
 ***********************************************************************/
 
/* get posted subnet, die if it is not provided! */
if($_REQUEST['subnetId']) { $subnetId = $_REQUEST['subnetId']; }

/* direct call */
if(!isset($_POST['direction'])) {
	$sort['direction'] = 'asc';
	$sort['field']	   = 'ip_addr';
	
	$sort['directionNext'] = "desc";
}
else {
	/* use required functions */
	require_once('../../functions/functions.php');
	
	/* format posted values! */
	$tmp = explode("|", $_POST['direction']);

	$sort['field'] 	   = $tmp[0];
	$sort['direction'] = $tmp[1];	

	if($sort['direction'] == "asc") { $sort['directionNext'] = "desc"; }
	else 							{ $sort['directionNext'] = "asc"; }	
	
	/** 
	* Parse IP addresses
	*
	* We provide subnet and mask, all other is calculated based on it (subnet, broadcast,...)
	*/
	$SubnetParsed = parseIpAddress ( transform2long($SubnetDetails['subnet']), $SubnetDetails['mask']);
}

/* verify that user is authenticated! */
isUserAuthenticated ();

/* get all selected fields for IP print */
$setFieldsTemp = getSelectedIPaddrFields();
/* format them to array! */
$setFields = explode(";", $setFieldsTemp);

/**
 * Get all ip addresses in subnet and subnet details!
 */
$title = _("IP addresses in subnet ");	# prefix for multiple subnets
if(sizeof($slaves) == 0) { $ipaddresses   = getIpAddressesBySubnetIdSort ($subnetId, $sort['field'], $sort['direction']);  $slavePing = false; }
else					 { $ipaddresses   = getIpAddressesBySubnetIdSlavesSort ($subnetId, $sort['field'], $sort['direction']);	$title = _("All IP addresses belonging to ALL nested subnets"); $slavePing = true; }
$SubnetDetails = getSubnetDetailsById     ($subnetId);

/* die if empty! */
if(sizeof($SubnetDetails) == 0) { die('<div class="alert alert-error">'._('Subnet does not exist').'!</div>');}

/* get all selected fields */
$myFields = getCustomFields('ipaddresses');
$myFieldsSize = sizeof($myFields);
	
/* set colspan */
$colspan['unused'] = sizeof($setFields) + $myFieldsSize;
$colspan['ipaddr'] = sizeof($setFields) + $myFieldsSize + 4;
$colspan['dhcp']   = sizeof($setFields) + $myFieldsSize - 3;

/* 
if result not empty use first IP address in subnet to identify type 
else use subnet
*/
$type = IdentifyAddress( $SubnetDetails['subnet'] );

/* remove myFields if all empty! */
foreach($myFields as $field) {
	$sizeMyFields[$field['name']] = 0;				// default value
	# check against each IP address
	foreach($ipaddresses as $ip) {
		if(strlen($ip[$field['name']]) > 0) {
			$sizeMyFields[$field['name']]++;		// +1
		}
	}	
	# unset if value == 0
	if($sizeMyFields[$field['name']] == 0) {
		unset($myFields[$field['name']]);
		
		$colspan['dhcp']--;							//dhcp span -1
		$colspan['unused']--;						//unused  span -1
	}
}


/* If compress is set in settings compress DHCP ranges! */
if($settings['dhcpCompress']==1) {
	// compress DHCP ranges
	$ipaddresses = compressDHCPranges ($ipaddresses);
}


/* For page repeats */
$m = 1;
# how many per page
if(sizeof($settings) == 0) { $settings = getAllSettings(); }
$pageLimit = $settings['printLimit'];

if($pageLimit == "0")		{ $pageLimit = "100000000"; }
else if(empty($pageLimit)) 	{ $pageLimit = "10"; }

$sizeIP = sizeof($ipaddresses);					// number of all
$repeats   = ceil($sizeIP / $pageLimit); 		// times to repeat body

# set page number from post
$maxPages = round($sizeIP/$pageLimit,0);																								// set max number of pages
if($_REQUEST['sPage']>$maxPages || !isset($_REQUEST['sPage']))	{ $_REQUEST['sPage'] = 1; }												// reset to 1 if number too big
elseif(!is_numeric($_REQUEST['sPage']))							{ $_REQUEST['sPage'] = str_replace("page", "", $_REQUEST['sPage']); }	// remove p from page

?>
<br>
<h4><?php print $title; ?>
<?php if($sizeIP  > $pageLimit) { print " (<span class='stran'>"._('Page')." $_REQUEST[sPage]/$repeats</span>)"; }  ?>
<?php
# next / previous
$colspanStran['unused'] = $colspan['unused']+1;
if($sizeIP  > $pageLimit) { ?>
<div class='btn-toolbar pull-right'>
	<div class="btn-group">
		<?php
		//prev page
		if($_REQUEST['sPage']==1) 			{ print "<a href='subnets/$_REQUEST[section]/$_REQUEST[subnetId]/page".($_REQUEST['sPage']-1)."/' class='btn btn-mini disabled'><i class='icon-gray icon-chevron-left'></i></a>"; }
		else								{ print "<a href='subnets/$_REQUEST[section]/$_REQUEST[subnetId]/page".($_REQUEST['sPage']-1)."/' class='btn btn-mini' rel='tooltip' title='". _('Previous page')."'><i class='icon-gray icon-chevron-left'></i></a>"; }
		//next page
		if($_REQUEST['sPage']==$maxPages) 	{ print "<a href='subnets/$_REQUEST[section]/$_REQUEST[subnetId]/page".($_REQUEST['sPage']+1)."/' class='btn btn-mini disabled'><i class='icon-gray icon-chevron-right'></i></a>"; }
		else								{ print "<a href='subnets/$_REQUEST[section]/$_REQUEST[subnetId]/page".($_REQUEST['sPage']+1)."/' class='btn btn-mini' rel='tooltip' title='". _('Next page')."'><i class='icon-gray icon-chevron-right'></i></a>"; }
	
		?>
	</div>
</div>
<?php } ?>

<?php
# jump to page
if($sizeIP  > $pageLimit) { 
	print "<div class='pull-right'>";
	print "<select name='jumptoPage' class='jumptoPage' style='width:auto;'>";
	for($m=1; $m<=$maxPages; $m++) {
		if($m==$_REQUEST['sPage'])		{ print "<option value='page$m' data-sectionId='$_REQUEST[section]' data-subnetId='$_REQUEST[subnetId]' selected='selected'>"._('Page')." $m</option>"; }
		else 							{ print "<option value='page$m' data-sectionId='$_REQUEST[section]' data-subnetId='$_REQUEST[subnetId]'>"._('Page')." $m</option>"; }
	}
	print "</select>";
	print "</div>";
}
?>
</h4>

<table class="ipaddresses normalTable table table-striped table-condensed table-hover table-full table-top">

<!-- headers -->
<tbody>
<tr class="th">

	<?php
	# set sort icon!
	if($sort['direction'] == 'asc') 	{ $icon = "<i class='icon-gray icon-chevron-down'></i> "; }
	else								{ $icon = "<i class='icon-gray icon-chevron-up'></i> "; }

	# IP address - mandatory
										  print "<th class='s_ipaddr'><a href='' data-id='ip_addr|$sort[directionNext]' class='sort' data-subnetId='$SubnetDetails[id]' rel='tooltip' title='"._('Sort by IP address')."'>"._('IP address')." "; if($sort['field'] == "ip_addr") 	print $icon;  print "</a></th>";
	# hostname - mandatory
										  print "<th><a href='' data-id='dns_name|$sort[directionNext]' class='sort' data-subnetId='$SubnetDetails[id]' rel='tooltip'  title='"._('Sort by hostname')."'				>"._('Hostname')." "; 	if($sort['field'] == "dns_name") 	print $icon;  print "</a></th>";
	# MAC address	
	if(in_array('mac', $setFields)) 	{ print "<th></th>"; }
	# Description - mandatory
										  print "<th><a href='' data-id='description|$sort[directionNext]' class='sort' data-subnetId='$SubnetDetails[id]' rel='tooltip'  title='"._('Sort by description')."'			>"._('Description')." "; if($sort['field'] == "description") print $icon;  print "</a></th>";
	# note
	if(in_array('note', $setFields)) 	{ print "<th></th>"; }	
	# switch
	if(in_array('switch', $setFields)) 	{ print "<th><a href='' data-id='switch|$sort[directionNext]' class='sort' data-subnetId='$SubnetDetails[id]' rel='tooltip'  title='"._('Sort by device')."'					>"._('Device')." "; 	if($sort['field'] == "switch") 		print $icon;  print "</a></th>"; }	
	# port
	if(in_array('port', $setFields)) 	{ print "<th><a href='' data-id='port|$sort[directionNext]'   class='sort' data-subnetId='$SubnetDetails[id]' rel='tooltip'  title='"._('Sort by port')."'  					>"._('Port')." "; 		if($sort['field'] == "port") 		print $icon;  print "</a></th>"; }
	# owner
	if(in_array('owner', $setFields)) 	{ print "<th><a href='' data-id='owner|$sort[directionNext]'  class='sort' data-subnetId='$SubnetDetails[id]' rel='tooltip'  title='"._('Sort by owner')."' 					>"._('Owner')." "; 		if($sort['field'] == "owner") 		print $icon;  print "</a></th>"; }
	
	# custom fields
	if(sizeof($myFields) > 0) {
		foreach($myFields as $myField) 	{ print "<th><a href='' data-id='$myField[name]|$sort[directionNext]' class='sort' data-subnetId='$SubnetDetails[id]' rel='tooltip' title='"._('Sort by')." $myField[name]'	>$myField[name] ";  if($sort['field'] == $myField['name']) print $icon;  print "</a></th>"; }
	}
	?>

	<!-- actions -->
	<th class="actions" width="10px"></th>

</tr>
</tbody>


<?php
/* content */
$n = 0;
$m = $CalculateSubnetDetails['used'] -1;

# set ping statuses
$statuses = explode(";", $settings['pingStatus']);

# if no IP is configured only display free subnet!
if (sizeof($ipaddresses) == 0) {
    $unused = FindUnusedIpAddresses ( Transform2decimal($SubnetParsed['network']), Transform2decimal($SubnetParsed['broadcast']), $type, 1, "networkempty", $SubnetDetails['mask'] );
    print '<tr class="th"><td></td><td colspan="'. $colspan['unused'] .'" class="unused">'. $unused['ip'] . ' (' . reformatNumber ($unused['hosts']) .')</td><td colspan=2></td></tr>'. "\n";
}
# print IP address
else {

	$ipaddress = $ipaddresses;
    # break into arrays
	$ipaddressesChunk = (array_chunk($ipaddresses, $pageLimit, true));

	$c = 1;		# count for print for pages - $c++ per page
	$n = 0;		# count for IP addresses - $n++ per IP address
	$g = 0;		# count for compress consecutive class
	
	foreach($ipaddressesChunk as $ipaddresses2) {
	
		if($c == $_REQUEST['sPage']) 	{ $show = true;  $display = "display:block;";}
		else 							{ $show = false; $display = "display:none";  }

		foreach($ipaddresses2 as $ipaddress2)  
		{
			# display?
			if($show) {
		       	
		       	#
		       	# first check for gaps
		       	#
		       	
		       	// check gap between network address and first IP address
		       	if ( $n == 0 ) 																	{ $unused = FindUnusedIpAddresses ( Transform2decimal($SubnetParsed['network']), $ipaddresses[$n]['ip_addr'], $type, 0, "network", $SubnetDetails['mask']  ); }
		       	// check unused space between IP addresses
		       	else { 
		       		// compressed and dhcp?
		       		if($settings['dhcpCompress'] && $ipaddresses[$n-1]['class']=="range-dhcp") 	{ $unused = FindUnusedIpAddresses ( $ipaddresses[$n-1]['stopIP'], $ipaddresses[$n]['ip_addr'], $type, 0, "", $SubnetDetails['mask'] );  }
		       		//uncompressed
		       		else 																		{ $unused = FindUnusedIpAddresses ( $ipaddresses[$n-1]['ip_addr'], $ipaddresses[$n]['ip_addr'], $type, 0, "", $SubnetDetails['mask'] );  }
		       	}
		       	
		       	/* if there is some result for unused print it - if sort == ip_addr */
			    if ( $unused && ($sort['field'] == 'ip_addr') && $sort['direction'] == "asc" ) { 
	        		print "<tr class='th'>";
	        		print "	<td></td>";
	        		print "	<td colspan='$colspan[ipaddr]' class='unused'>$unused[ip] ($unused[hosts])</td>";
	        		print "</tr>"; 
	        	}
	
	
				#
			    # print IP address
			    # 
			    
			    # ip - range
			    if($ipaddress[$n]['class']=="range-dhcp") 
			    {
			    	print "<tr class='dhcp'>"; 
				    print "	<td>";
				    # status icon
				    if($SubnetDetails['pingSubnet']=="1") {
				    print "		<span class='status status-padded'></span>";
					} 
				    print 		Transform2long( $ipaddress[$n]['ip_addr']).' - '.Transform2long( $ipaddress[$n]['stopIP'])." (".$ipaddress[$n]['numHosts'].")";
				    print 		reformatIPState($ipaddress[$n]['state']);
				    print "	</td>";
	        		print "	<td>"._("DHCP range")."</td>";
	        		print "	<td></td>";
	        		print "	<td>".$ipaddress[$n]['description']."</td>";
	        		print "	<td colspan='$colspan[dhcp]' class='unused'></td>";
				    // tr ends after!

			    }
			    # ip - normal
			    else 
			    {
			        /*	set class for reserved and offline - if set! */
				    $stateClass = "";
			        if(in_array('state', $setFields)) {
				        if ($ipaddress[$n]['state'] == "0") 	 { $stateClass = _("Offline"); }
				        else if ($ipaddress[$n]['state'] == "2") { $stateClass = _("Reserved"); }
				        else if ($ipaddress[$n]['state'] == "3") { $stateClass = _("DHCP"); }
				    }			    
			    	
			 		print "<tr class='$stateClass'>";
			    
				    # status icon
				    if($SubnetDetails['pingSubnet']=="1") {
					    //calculate
					    $tDiff = time() - strtotime($ipaddress[$n]['lastSeen']);
					    if($ipaddress[$n]['excludePing']=="1" ) { $hStatus = "padded"; $hTooltip = ""; }
					    elseif($tDiff < $statuses[0])	{ $hStatus = "success";	$hTooltip = "rel='tooltip' data-html='true' data-placement='left' title='"._("Device is alive")."<hr>"._("Last seen").": ".$ipaddress[$n]['lastSeen']."'"; }
					    elseif($tDiff < $statuses[1])	{ $hStatus = "warning"; $hTooltip = "rel='tooltip' data-html='true' data-placement='left' title='"._("Device warning")."<hr>"._("Last seen").": ".$ipaddress[$n]['lastSeen']."'"; }
					    elseif($tDiff < 2592000)		{ $hStatus = "error"; 	$hTooltip = "rel='tooltip' data-html='true' data-placement='left' title='"._("Device is offline")."<hr>"._("Last seen").": ".$ipaddress[$n]['lastSeen']."'";}
					    elseif($ipaddress[$n]['lastSeen'] == "0000-00-00 00:00:00") { $hStatus = "neutral"; 	$hTooltip = "rel='tooltip' data-html='true' data-placement='left' title='"._("Device is offline")."<hr>"._("Last seen").": "._("Never")."'";}
					    else							{ $hStatus = "neutral"; $hTooltip = "rel='tooltip' data-html='true' data-placement='left' title='"._("Device status unknown")."'";}		    
				    }
				    else {
					    $hStatus = "hidden";
					    $hTooltip = "";
				    }   
			    
				    print "	<td class='ipaddress'><span class='status status-$hStatus' $hTooltip></span>".Transform2long( $ipaddress[$n]['ip_addr']);
				    if(in_array('state', $setFields)) 				{ print reformatIPState($ipaddress[$n]['state']); }	
				    print "</td>";
		
				    # resolve dns name if not provided, else print it - IPv4 only!
				    if ( (empty($ipaddress[$n]['dns_name'])) and ($settings['enableDNSresolving'] == 1) and (IdentifyAddress($ipaddress[$n]['ip_addr']) == "IPv4") ) {
					    $dnsResolved = ResolveDnsName ( $ipaddress[$n]['ip_addr'] );
					}
					else {
						$dnsResolved['class'] = "";
						$dnsResolved['name']  = $ipaddress[$n]['dns_name'];
					}														  print "<td class='$dnsResolved[class] hostname'>$dnsResolved[name]</td>";  		
		
					# Print mac address icon!
					if(in_array('mac', $setFields)) {
						if(!empty($ipaddress[$n]['mac'])) 					{ print "<td class='mac'><img class='info mac' src='css/images/lan.png' rel='tooltip' title='"._('MAC').": ".$ipaddress[$n]['mac']."'></td>"; }
						else 												{ print "<td class='mac'></td>"; }
					}
				
					# print description - mandatory
		        													  		  print "<td class='description'>".$ipaddress[$n]['description']."</td>";	
				
		       		# print info button for hover
		       		if(in_array('note', $setFields)) {
		        		if(!empty($ipaddress[$n]['note'])) 					{ print "<td><i class='icon-gray icon-comment' rel='tooltip' data-html='true' title='".str_replace("\n", "<br>",$ipaddress[$n]['note'])."'></td>"; }
		        		else 												{ print "<td></td>"; }
		        	}
			
		        	# print switch
		        	if(in_array('switch', $setFields)) 					{ 
			        	# get switch details
			        	$switch = getSwitchById ($ipaddress[$n]['switch']);
																		  print "<td>".$switch['hostname']."</td>";
																		}
				
					# print port
					if(in_array('port', $setFields)) 					{ print "<td>".$ipaddress[$n]['port']."</td>"; }
				
					# print owner
					if(in_array('owner', $setFields)) 					{ print "<td>".$ipaddress[$n]['owner']."</td>"; }
				
					# print custom fields 
					if(sizeof($myFields) > 0) {
						foreach($myFields as $myField) 					{ print "<td class='customField'>".$ipaddress[$n][$myField['name']]."</td>"; }
					}				    
			    }
			    
				# print action links if user can edit 	
				print "<td class='btn-actions'>";
				print "	<div class='btn-toolbar'>";
				print "	<div class='btn-group'>";
				# write permitted
				if( $permission > 1) {
					if($ipaddress[$n]['class']=="range-dhcp") 
					{
						print "		<a class='edit_ipaddress   btn btn-mini modIPaddr' data-action='edit'   data-subnetId='".$ipaddress[$n]['subnetId']."' data-id='".$ipaddress[$n]['id']."' data-stopIP='".$ipaddress[$n]['stopIP']."' href='#' 		   rel='tooltip' title='"._('Edit IP address details')."'>						<i class='icon-gray icon-pencil'>  </i></a>";
						print "		<a class='				   btn btn-mini disabled' href='#'>																<i class='icon-gray icon-retweet'> </i></a>"; 
						print "		<a class='				   btn btn-mini disabled' href='#'>																<i class='icon-gray icon-search'></i></a>";
						print "		<a class='				   btn btn-mini disabled' href='#'>																<i class='icon-gray icon-envelope'></i></a>";
						print "		<a class='delete_ipaddress btn btn-mini modIPaddr' data-action='delete' data-subnetId='".$ipaddress[$n]['subnetId']."' data-id='".$ipaddress[$n]['id']."' href='#' id2='".Transform2long($ipaddress[$n]['ip_addr'])."' rel='tooltip' title='"._('Delete IP address')."'>					<i class='icon-gray icon-remove'>  </i></a>";											
					} 
					else 
					{
						print "		<a class='edit_ipaddress   btn btn-mini modIPaddr' data-action='edit'   data-subnetId='".$ipaddress[$n]['subnetId']."' data-id='".$ipaddress[$n]['id']."' href='#' 											   rel='tooltip' title='"._('Edit IP address details')."'>						<i class='icon-gray icon-pencil'>  </i></a>";
						print "		<a class='ping_ipaddress   btn btn-mini' data-subnetId='".$ipaddress[$n]['subnetId']."' data-id='".$ipaddress[$n]['id']."' href='#' 						   													rel='tooltip' title='"._('Check avalibility')."'>							<i class='icon-gray icon-retweet'>  </i></a>"; 
						print "		<a class='search_ipaddress btn btn-mini         "; if(strlen($dnsResolved['name']) == 0) { print "disabled"; } print "' href='tools/search/$dnsResolved[name]' "; if(strlen($dnsResolved['name']) != 0)   { print "rel='tooltip' title='"._('Search same hostnames in db')."'"; } print ">	<i class='icon-gray icon-search'></i></a>";
						print "		<a class='mail_ipaddress   btn btn-mini          ' href='#' data-id='".$ipaddress[$n]['id']."' rel='tooltip' title='"._('Send mail notification')."'>																																		<i class='icon-gray icon-envelope'></i></a>";
						print "		<a class='delete_ipaddress btn btn-mini modIPaddr' data-action='delete' data-subnetId='".$ipaddress[$n]['subnetId']."' data-id='".$ipaddress[$n]['id']."' href='#' id2='".Transform2long($ipaddress[$n]['ip_addr'])."' rel='tooltip' title='"._('Delete IP address')."'>					<i class='icon-gray icon-remove'>  </i></a>";											
					}
				}
				# write not permitted
				else {
					if($ipaddress[$n]['class']=="range-dhcp") 
					{
						print "		<a class='edit_ipaddress   btn btn-mini disabled' rel='tooltip' title='"._('Edit IP address details (disabled)')."'>	<i class='icon-gray icon-pencil'>  </i></a>";
						print "		<a class='				   btn btn-mini disabled' href='#'>																<i class='icon-gray icon-retweet'> </i></a>"; 
						print "		<a class='				   btn btn-mini disabled' href='#'>																<i class='icon-gray icon-search'></i></a>";
						print "		<a class='				   btn btn-mini disabled' href='#'>																<i class='icon-gray icon-envelope'></i></a>";
						print "		<a class='delete_ipaddress btn btn-mini disabled' rel='tooltip' title='"._('Delete IP address (disabled)')."'>			<i class='icon-gray icon-remove'>  </i></a>";				
					}
					else 
					{
						print "		<a class='edit_ipaddress   btn btn-mini disabled' rel='tooltip' title='"._('Edit IP address details (disabled)')."'>							<i class='icon-gray icon-pencil'>  </i></a>";
						print "		<a class='				   btn btn-mini disabled'  data-id='".$ipaddress[$n]['id']."' href='#' rel='tooltip' title='"._('Check avalibility')."'>		<i class='icon-gray icon-retweet'>  </i></a>";
						print "		<a class='search_ipaddress btn btn-mini         "; if(strlen($dnsResolved['name']) == 0) { print "disabled"; } print "' href='tools/search/$dnsResolved[name]' "; if(strlen($dnsResolved['name']) != 0) { print "rel='tooltip' title='"._('Search same hostnames in db')."'"; } print ">	<i class='icon-gray icon-search'></i></a>";
						print "		<a class='mail_ipaddress   btn btn-mini          ' href='#' data-id='".$ipaddress[$n]['id']."' rel='tooltip' title='"._('Send mail notification')."'>		<i class='icon-gray icon-envelope'></i></a>";
						print "		<a class='delete_ipaddress btn btn-mini disabled' rel='tooltip' title='"._('Delete IP address (disabled)')."'>				<i class='icon-gray icon-remove'>  </i></a>";				
					}
				}
				print "	</div>";
				print "	</div>";
				print "</td>";		
			
				print '</tr>'. "\n";
		            
				/*	if last one return ip address and broadcast IP 
				****************************************************/
				if ( $n == $m ) 
				{   
	            	$unused = FindUnusedIpAddresses ( $ipaddresses[$n]['ip_addr'], Transform2decimal($SubnetParsed['broadcast']), $type, 1, "broadcast", $SubnetDetails['mask'] );
	            	if ( $unused  ) {
	            	    print '<tr class="th"><td></td><td colspan="'. $colspan['unused'] .'" class="unused">'. $unused['ip'] . ' (' . $unused['hosts'] .')</td><td colspan=2></td></tr>'. "\n";
	            	}    
	            }	
            }   
            
            /* next IP address for free check */
	        $n++;         
        }
        
		$c++;
	}	
}
?>

</table>	<!-- end IP address table -->

<?php
# next / previous
$colspanStran['unused'] = $colspan['unused']+1;
if($sizeIP  > $pageLimit) { ?>
<hr>
<div class='btn-toolbar pull-right'>
	<div class="btn-group">
		<?php
		//prev page
		if($_REQUEST['sPage']==1) 			{ print "<a href='subnets/$_REQUEST[section]/$_REQUEST[subnetId]/page".($_REQUEST['sPage']-1)."/' class='btn btn-mini disabled'><i class='icon-gray icon-chevron-left'></i></a>"; }
		else								{ print "<a href='subnets/$_REQUEST[section]/$_REQUEST[subnetId]/page".($_REQUEST['sPage']-1)."/' class='btn btn-mini' rel='tooltip' title='". _('Previous page')."'><i class='icon-gray icon-chevron-left'></i></a>"; }
		//next page
		if($_REQUEST['sPage']==$maxPages) 	{ print "<a href='subnets/$_REQUEST[section]/$_REQUEST[subnetId]/page".($_REQUEST['sPage']+1)."/' class='btn btn-mini disabled'><i class='icon-gray icon-chevron-right'></i></a>"; }
		else								{ print "<a href='subnets/$_REQUEST[section]/$_REQUEST[subnetId]/page".($_REQUEST['sPage']+1)."/' class='btn btn-mini' rel='tooltip' title='". _('Next page')."'><i class='icon-gray icon-chevron-right'></i></a>"; }
	
		?>
	</div>
</div>
<?php } ?>


<?php
# visual display of used IP addresses
if($type == "IPv4") {
	if($settings['visualLimit'] > 0) {
		if($settings['visualLimit'] <= $SubnetDetails['mask']) {
			include_once('ipAddressPrintVisual.php');
		}
	}
}
?>