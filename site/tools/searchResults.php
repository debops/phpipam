<?php

/*
 * Script to display search results
 **********************************/

/* if method is post get query, otherwise use $serachTerm */
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	if(isset($_POST['ip'])) {
		$searchTerm = $_REQUEST['ip'];
		//remove default
		if($searchTerm == "search") { $searchTerm = ""; }
	}
	require_once( dirname(__FILE__) . '/../../functions/functions.php' );

}


/* hide errors! */
ini_set('display_errors', 0);


/* change * to % for database wildchar */
$searchTerm = str_replace("*", "%", $searchTerm);
// sanitize


/* check if mac address */
if(strlen($searchTerm) == 17) {
	//count : -> must be 5
	if(substr_count($searchTerm, ":") == 5) 												{ $type = "mac"; }
}
else if(strlen($searchTerm) == 12) {
	//no dots or : -> mac without :
	if( (substr_count($searchTerm, ":") == 0) && (substr_count($searchTerm, ".") == 0) ) 	{ $type = "mac"; }	
}
/* ok, not MAC! */
else 																						{ $type = IdentifyAddress( $searchTerm ); }		# identify address type


# reformat
if ($type == "IPv4") 		{ $searchTermEdited = reformatIPv4forSearch ($searchTerm);}		# reformat the IPv4 address!
else if ($type == "mac") 	{  }
else 						{ $searchTermEdited = reformatIPv6forSearch ($searchTerm); }	# reformat the IPv4 address!

# check also subnets! 
$subnets = searchSubnets ($searchTerm, $searchTermEdited);

# check also VLANS!
$vlans = searchVLANs ($searchTerm);

# get all custom fields 
$myFields = getCustomFields('ipaddresses');


/* set the query */
$query  = 'select * from ipaddresses where ';
/* $query .= 'ip_addr like "' . $searchTerm . '%" '; */					//ip address in decimal
$query .= '`ip_addr` between "'. $searchTermEdited['low'] .'" and "'. $searchTermEdited['high'] .'" ';	//ip range
$query .= 'or `dns_name` like "%' . $searchTerm . '%" ';					//hostname
$query .= 'or `owner` like "%' . $searchTerm . '%" ';						//owner
# custom!
# custom fields
if(sizeof($myFields) > 0) {
	foreach($myFields as $myField) {
		$query .= 'or `'. $myField['name'] .'` like "%' . $searchTerm . '%" ';
	}
}
$query .= 'or `switch` like "%' . $searchTerm . '%" ';
$query .= 'or `port` like "%' . $searchTerm . '%" ';						//port search
$query .= 'or `description` like "%' . $searchTerm . '%" ';					//descriptions
$query .= 'or `note` like "%' . $searchTerm . '%" ';						//note
$query .= 'or `mac` like "%' . $searchTerm . '%" ';							//mac
$query .= 'order by `ip_addr` asc;';


/* get result */
$result = searchAddresses ($query);

/* get all selected fields for IP print */
$setFieldsTemp = getSelectedIPaddrFields();
/* format them to array! */
$setFields = explode(";", $setFieldsTemp);

/* get all selected fields */
$myFields = getCustomFields('ipaddresses');

# set col size
$fieldSize 	= sizeof($setFields);
$mySize 	= sizeof($myFields);
$colSpan 	= $fieldSize + $mySize + 4;
?>

<h4> <?php print _('Search results (IP address list)');?>: <?php if(sizeof($result) != 0) { print('<a href="" id="exportSearch" rel="tooltip" title="'._('Export All results to XLS').'"><button class="btn btn-xs btn-default"><i class="fa fa-download"></i></button></a>');} ?></h4>
<hr>

<!-- export holder -->
<div class="exportDIVSearch"></div>

<!-- search result table -->
<table class="searchTable table table-striped table-condensed table-top">

<!-- headers -->
<tr id="searchHeader">
<?php

	print '<th>'._('IP address').'</th>'. "\n";
	print '<th>'._('VLAN').'</th>'. "\n";
	# description
	print '<th>'._('Description').'</th>'. "\n";
	print '<th>'._('Hostname').'</th>'. "\n";
	# mac
	if(in_array('mac', $setFields)) 										{ print '<th></th>'. "\n"; }
	# switch
	if(in_array('switch', $setFields))										{ print '<th class="hidden-sm hidden-xs">'._('Device').'</th>'. "\n"; }
	# port
	if(in_array('port', $setFields)) 										{ print '<th>'._('Port').'</th>'. "\n"; }
	# owner and note
	if( (in_array('owner', $setFields)) && (in_array('note', $setFields)) ) { print '<th colspan="2" class="hidden-sm hidden-xs">'._('Owner').'</th>'. "\n"; }
	else if (in_array('owner', $setFields)) 								{ print '<th class="hidden-sm hidden-xs">'._('Owner').'</th>'. "\n";	}
	else if (in_array('note', $setFields)) 									{ print '<th></th>'. "\n"; }
	
	# custom fields
	if(sizeof($myFields) > 0) {
		foreach($myFields as $myField) 										{ print '<th class="hidden-sm hidden-xs hidden-md">'. $myField['name'] .'</th>'. "\n"; }
	}
	
	# actions
	print '<th class="actions"></th>';
?>
</tr>

<!-- IP addresses -->
<?php

/* if no result print nothing found */
if(sizeof($result) > 0) {

	$m = 0;		//for section change
	/* print content */
	foreach ($result as $line) {
	
		# check permission
		$permission = checkSubnetPermission ($line['subnetId']);
		if($permission != "0") {

			//get the Subnet details
			$subnet = getSubnetDetailsById ($line['subnetId']);
			//get vlan number
			$vlan   = subnetGetVLANDetailsById($subnet['vlanId']);
			//get section
			$section = getSectionDetailsById ($subnet['sectionId']);
	
			//detect section change and print headers
			if ($result[$m]['subnetId'] != $result[$m-1]['subnetId']) {
				print '<tr>' . "\n";
				print '	<th colspan="'. $colSpan .'">'. $section['name'] . ' :: <a href="subnets/'.$subnet['sectionId'].'/'.$subnet['id'].'/" style="font-weight:300">' . $subnet['description'] .' ('. transform2long($subnet['subnet']) .'/'. $subnet['mask'] .')</a></th>' . "\n";
				print '</tr>';
			}
			$m++;
		
			$stateClass = "";
			if(in_array('state', $setFields)) {
			    if ($line['state'] == "0") 	 	{ $stateClass = "offline"; }
			    else if ($line['state'] == "2") { $stateClass = "reserved"; }
			    else if ($line['state'] == "3") { $stateClass = "DHCP"; }
			}
	
			//print table
			print '<tr class="ipSearch '.$stateClass.'" id="'. $line['id'] .'" subnetId="'. $line['subnetId'] .'" sectionId="'. $subnet['sectionId'] .'" link="'. $section['name'] .'|'. $subnet['id'] .'">'. "\n";
	
			print ' <td><a href="subnets/'.$subnet['sectionId'].'/'.$subnet['id'].'/ipdetails/'.$line['id'].'/">'. transform2long($line['ip_addr'])."</a>";
			if(in_array('state', $setFields)) 				{ print reformatIPState($line['state']); }	
			print ' </td>' . "\n";
			print ' <td>'. $vlan['number']  .'</td>' . "\n";
			print ' <td>'. ShortenText($line['description'], $chars = 50) .'</td>' . "\n";
	
			print ' <td>'. $line['dns_name']  .'</td>' . "\n";
		
			# mac
			if(in_array('mac', $setFields)) {
				print '	<td>'. "\n";
				if(strlen($line['mac']) > 0) {
					print '<i class="fa fa-sitemap fa-gray" rel="tooltip" title="MAC: '. $line['mac'] .'"></i>'. "\n";
				}
				print '	</td>'. "\n";
			}
		
			# switch
			if(in_array('switch', $setFields)) 										{ 
				if(strlen($line['switch'])>0 && $line['switch']!=0) {
					# get switch
					$switch = getDeviceDetailsById($line['switch']);
					$line['switch'] = $switch['hostname'];
				}
				
				print ' <td class="hidden-sm hidden-xs">'. $line['switch']  .'</td>' . "\n"; 
			}
			# port
			if(in_array('port', $setFields)) 										{ print ' <td>'. $line['port']  .'</td>' . "\n"; }
			# owner and note
			if((in_array('owner', $setFields)) && (in_array('note', $setFields)) ) {
				print ' <td class="hidden-sm hidden-xs">'. $line['owner']  .'</td>' . "\n";
				print ' <td class="note hidden-sm hidden-xs">' . "\n";
				if(!empty($line['note'])) {
					$line['note'] = str_replace("\n", "<br>",$line['note']);
					print '<i class="fa fa-gray fa fa-comment-o" rel="tooltip" title="'. $line['note']. '"></i>' . "\n";
				}
				print '</td>'. "\n";
			}
			# owner only
			else if (in_array('owner', $setFields)) 								{ print ' <td class="hidden-sm hidden-xs">'. $line['owner']  .'</td>' . "\n";	}
			# note only
			else if (in_array('note', $setFields)) {
				print '<td class="note">' . "\n";
				if(!empty($line['note'])) {
					$line['note'] = str_replace("\n", "<br>",$line['note']);
					print '	<i class="fa fa-gray fa fa-comment-o" rel="tooltip" title="'. $line['note']. '"></i>' . "\n";
				}
				print '</td>'. "\n";
			}
			# custom
			if(sizeof($myFields) > 0) {
				foreach($myFields as $myField) 										{ print '<td class="customField hidden-sm hidden-xs hidden-md">'. $line[$myField['name']] .'</td>'. "\n"; }
			}
		
			# print action links if user can edit 	
			print "<td class='actions'>";
			print "	<div class='btn-group'>";

			if($permission > 1) {
				print "		<a class='edit_ipaddress   btn btn-xs btn-default modIPaddr' data-action='edit'   data-subnetId='$subnet[id]' data-id='".$line['id']."' href='#' 	rel='tooltip' data-container='body'  title='"._('Edit IP address details')."'>		<i class='fa fa-gray fa fa-pencil'>  </i></a>";
				print "		<a class='mail_ipaddress   btn btn-xs btn-default          ' href='#' data-id='".$line['id']."' rel='tooltip' data-container='body'  title='"._('Send mail notification')."'>														<i class='fa fa-gray fa fa-envelope-o'></i></a>";
				print "		<a class='delete_ipaddress btn btn-xs btn-default modIPaddr' data-action='delete' data-subnetId='$subnet[id]' data-id='".$line['id']."' href='#'  rel='tooltip' data-container='body'  title='"._('Delete IP address')."'>			<i class='fa fa-gray fa fa-times'>  </i></a>";
			}
			# unlocked
			else {
				print "		<a class='edit_ipaddress   btn btn-xs btn-default disabled' rel='tooltip' data-container='body'  title='"._('Edit IP address details (disabled)')."'>										<i class='fa fa-gray fa fa-pencil'>  </i></a>";
				print "		<a class='mail_ipaddress   btn btn-xs btn-default          ' href='#' data-id='".$line['id']."' rel='tooltip' data-container='body'  title='"._('Send mail notification')."'>				<i class='fa fa-gray fa fa-envelope'></i></a>";
				print "		<a class='delete_ipaddress btn btn-xs btn-default disabled' rel='tooltip' data-container='body'  title='"._('Delete IP address (disabled)')."'>												<i class='fa fa-gray fa fa-times'>  </i></a>";
			}
			print "	</div>";
			print "</td>";	
		
		print '</tr>' . "\n";
	}
	}
}
?>
</table>



<!-- search result table -->
<br>
<h4><?php print _('Search results (Subnet list)');?>:</h4>
<hr>

<table class="searchTable table table-striped table-condensed table-top">

<!-- headers -->
<tr id="searchHeader">
	<th><?php print _('Section');?></th>
	<th><?php print _('Subnet');?></th>
	<th><?php print _('Description');?></th>
	<th><?php print _('Master subnet');?></th>
	<th><?php print _('VLAN');?></th>
	<th><?php print _('Requests');?></th>
	<th style="width:5px;"></th>
</tr>


<?php
if(sizeof($subnets) > 0) {

	/* each query result */
	foreach($subnets as $subn) {
		
		foreach($subn as $line) {
	
			# check permission
			$permission = checkSubnetPermission ($line['id']);
			if($permission != "0") {
			
				//get section details 
				$section = getSectionDetailsById ($line['sectionId']);
				//get vlan number
				$vlan   = subnetGetVLANDetailsById($line['vlanId']);
		
				//format requests
				if($line['allowRequests'] == 1) { $line['allowRequests'] = "enabled"; }
				else 							{ $line['allowRequests'] = "disabled"; }
		
				//format master subnet
				if($line['masterSubnetId'] == 0) { $line['masterSubnetId'] = "/"; }
				else {
					$line['masterSubnetId'] = getSubnetDetailsById ($line['masterSubnetId']);
					# folder?
					if($line['isFolder']==1) {
						$line['masterSubnetId'] = "<i class='fa fa-folder-o fa fa-gray'></i> $line[description]";						
					} else {
						$line['masterSubnetId'] = transform2long($line['masterSubnetId']['subnet']) .'/'. $line['masterSubnetId']['mask'];					
					}
				}
			
				print '<tr class="subnetSearch" subnetId="'. $line['id'] .'" sectionName="'. $section['name'] .'" sectionId="'. $section['id'] .'" link="'. $section['name'] .'|'. $line['id'] .'">'. "\n";
	
				print '	<td>'. $section['name'] . '</td>'. "\n"; 
				//folder?
				if($line['isFolder']==1) {
				print '	<td><a href="subnets/'.$line['sectionId'].'/'.$line['id'].'/"><i class="fa fa-folder-o fa fa-gray"></i> '.$line['description'].'</a></td>'. "\n"; 
				} else {
				print '	<td><a href="subnets/'.$line['sectionId'].'/'.$line['id'].'/">'. transform2long($line['subnet']) . '/'.$line['mask'].'</a></td>'. "\n"; 					
				}
				print ' <td><a href="subnets/'.$line['sectionId'].'/'.$line['id'].'/">'. $line['description'] .'</a></td>' . "\n";
				print ' <td>'. $line['masterSubnetId'] .'</td>' . "\n";
				print ' <td>'. $vlan['number'] .'</td>' . "\n";
				print ' <td>'. _($line['allowRequests']) .'</td>' . "\n";
			
				#locked for writing
				if($permission > 1) {
					print "	<td><button class='btn btn-xs btn-default edit_subnet' data-action='edit'   data-subnetId='$line[id]' data-sectionId='$line[sectionId]' href='#' rel='tooltip' data-container='body'  title='"._('Edit subnet details')."'>		<i class='fa fa-gray fa fa-pencil'>  </i></a>";
				}
				else {
					print "	<td><button class='btn btn-xs btn-default disabled' rel='tooltip' data-container='body'  title='"._('Edit subnet (disabled)')."'>	<i class='fa fa-gray fa fa-pencil'>  </i></button>";
				}
				print '</tr>'. "\n";
			}
		}
	}
}
?>

</table>




<!-- search result table -->
<br>
<h4><?php print _('Search results (VLANs)');?>:</h4>
<hr>

<table class="vlanSearch table table-striped table-condensed table-top">

<!-- headers -->
<tr id="searchHeader">
	<th><?php print _('Name');?></th>
	<th><?php print _('Number');?></th>
	<th><?php print _('Description');?></th>
	<th><?php print _('Belonging subnets');?></th>
	<th><?php print _('Section');?></th>
</tr>


<?php
if(sizeof($vlans) == 0) {
}
else {

	foreach($vlans as $vlan) {

		/* get all subnets in VLAN */
		$subnets = getSubnetsByVLANid ($vlan['vlanId']);
		
		/* no belonging subnets! */
		if(sizeof($subnets) == 0) {
			print '<tr class="nolink">' . "\n";
			print ' <td><dd>'. $vlan['name']      .'</dd></td>' . "\n";
			print ' <td><dd>'. $vlan['number']        .'</dd></td>' . "\n";
			print ' <td><dd>'. $vlan['description'] .'</dd></td>' . "\n";				
			print ' <td>----</td>' . "\n";
			print ' <td>----</td>' . "\n";
			print '</tr>'. "\n";
		}
		
		/* for each subnet print tr */
		foreach($subnets as $subnet)
		{
			# check permission
			$permission = checkSubnetPermission ($subnet['id']);
			if($permission != "0") {
			
				/* get section details */
				$section = getSectionDetailsById ($subnet['sectionId']);	

				# detect change
				$vlanNew = $subnet['vlanId'];
				if($vlanNew == $vlanOld) { $change = ''; }
				else 					 { $change = 'style="border-top:1px dashed white"'; $vlanOld = $vlanNew; }

				print '<tr class="link vlanSearch" '. $change .' sectionId="'. $section['id'] .'" subnetId="'. $subnet['id'] .'" link="'. $section['name'] .'|'. $subnet['id'] .'">' . "\n";

				/* print first 3 only if change happened! */
				if(strlen($change) > 0) {
					print ' <td><dd>'. $vlan['name']         .'</dd></td>' . "\n";
					print ' <td><dd>'. $vlan['number']           .'</dd></td>' . "\n";
					print ' <td><dd>'. $vlan['description'] .'</dd></td>' . "\n";			
				}
				else {
					print '<td></td>';
					print '<td></td>';
					print '<td></td>';	
				} 

				if ($subnet['id'] != null) {
					# subnet
					print ' <td>'. transform2long($subnet['subnet']) .'/'. $subnet['mask'] .'</td>' . "\n";
					# section
					print ' <td>'. $section['name'] .'</td>'. "\n";
				}
				else {
    		    	print '<td>---</td>'. "\n";
    		    	print '<td>---</td>'. "\n";
    		    }
    		    print '</tr>' . "\n";
    		}
    	}

    }
}
?>

</table>