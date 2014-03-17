<?php
/**
 * Main script to display IP addresses in content div of subnets table!
 ***********************************************************************/

/* verify that user is authenticated! */
isUserAuthenticated ();

/* get posted subnet, die if it is not provided! */
if($_REQUEST['subnetId']) { $subnetId = $_REQUEST['subnetId']; }

/* get custom subnet fields */
$customSubnetFields = getCustomFields('subnets');
$customSubnetFieldsSize = sizeof($customSubnetFields);

/**
 * Get all ip addresses in subnet and subnet details!
 */
$ipaddresses   = getIpAddressesBySubnetId ($subnetId); 	# for stats only
$SubnetDetails = getSubnetDetailsById     ($subnetId);

# die if empty!
if(sizeof($SubnetDetails) == 0) { die('<div class="alert alert-danger">'._('Subnet does not exist').'!</div>'); }

# reset VLAN number!
$SubnetDetails['VLAN'] = subnetGetVLANdetailsById($SubnetDetails['vlanId']);

# get all site settings
$settings = getAllSettings();

/** 
 * Parse IP addresses
 *
 * We provide subnet and mask, all other is calculated based on it (subnet, broadcast,...)
 */
$SubnetParsed = parseIpAddress ( transform2long($SubnetDetails['subnet']), $SubnetDetails['mask']);

/* Calculate free / used etc */
$CalculateSubnetDetails = calculateSubnetDetails ( gmp_strval(sizeof($ipaddresses)), $SubnetDetails['mask'], $SubnetDetails['subnet'] );

# permissions
$permission = checkSubnetPermission ($subnetId);

# section permissions
$permissionsSection = checkSectionPermission ($SubnetDetails['sectionId']);

# if 0 die
if($permission == "0")	{ die("<div class='alert alert-danger'>"._('You do not have permission to access this network')."!</div>"); }
?>

<!-- content print! -->

<!-- for adding IP address! -->
<div class="row">

<div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">

	<div id="subnetId" style="display:none"><?php print $subnetId; ?></div>
	
	<!-- subnet details upper table -->
	<h4><?php print _('Subnet details'); ?></h4>
	<hr>
	
	<table class="ipaddress_subnet table-condensed table-full">
		<tr>
			<th><?php print _('Subnet details'); ?></th>
			<td><?php print '<b>'. transform2long($SubnetDetails['subnet']) ."/$SubnetDetails[mask]</b> ($SubnetParsed[netmask])"; ?></td>
		</tr>
		<tr>
			<th><?php print _('Hierarchy'); ?></th>
			<td>
				<?php printBreadCrumbs($_REQUEST); ?>
			</td>
		</tr>
		<tr>
			<th><?php print _('Subnet description'); ?></th>
			<td><?php print html_entity_decode($SubnetDetails['description']); ?></td>
		</tr>
		<tr>
			<th><?php print _('Permission'); ?></th>
			<td><?php print parsePermissions($permission); ?></td>
		</tr>
		<tr>
			<th><?php print _('Subnet Usage'); ?></th>
			<td>
				<?php print ''._('Used').':  '. reformatNumber ($CalculateSubnetDetails['used']) .' | 
							 '._('Free').':  '. reformatNumber ($CalculateSubnetDetails['freehosts']) .' ('. $CalculateSubnetDetails['freehosts_percent']  .'%) | 
							 '._('Total').': '. reformatNumber ($CalculateSubnetDetails['maxhosts']); 
				?>
			</td>
		</tr>
		<tr>
			<th><?php print _('VLAN'); ?></th>
			<td>
			<?php 
			if(empty($SubnetDetails['VLAN']['number']) || $SubnetDetails['VLAN']['number'] == 0) { $SubnetDetails['VLAN']['number'] = "/"; }	# Display fix for emprt VLAN
			print $SubnetDetails['VLAN']['number'];
			
			if(!empty($SubnetDetails['VLAN']['name'])) 		  { print ' - '.$SubnetDetails['VLAN']['name']; }									# Print name if provided
			if(!empty($SubnetDetails['VLAN']['description'])) { print ' ['. $SubnetDetails['VLAN']['description'] .']'; }						# Print description if provided
			?>
			</td>
		</tr>
		
		<?php
		if(!empty($SubnetDetails['vrfId'])) {
			# get vrf details
			$vrf = getVRFdetailsById($SubnetDetails['vrfId']);
			# set text
			$vrfText = $vrf['name'];
			if(!empty($vrf['description'])) { $vrfText .= " [$vrf[description]]";}
		
			print "<tr>";
			print "	<th>"._('VRF')."</th>";
			print "	<td>$vrfText</td>";
			print "</tr>";
		}
	
		/* Are IP requests allowed? */
		if ($settings['enableIPrequests'] == 1) {
			print "<tr>";
			print "	<th>"._('IP requests')."</th>";
			if($SubnetDetails['allowRequests'] == 1) 	{ print "	<td>"._('enabled')."</td>"; }		# yes
			else 										{ print "	<td>"._('disabled')."</td>";}		# no
			print "</tr>";
		}
	
		/* ping-check hosts inside subnet */
		if ($SubnetDetails['pingSubnet'] == 1) {
			print "<tr>";
			print "	<th>"._('Hosts check')."</th>";
			if($SubnetDetails['pingSubnet'] == 1) 		{ print "	<td>"._('enabled')."</td>"; }		# yes
			else 										{ print "	<td>"._('disabled')."</td>";}		# no
			print "</tr>";
		}
		
		/* print custom subnet fields if any */
		if(sizeof($customSubnetFieldsSize) > 0) {
			foreach($customSubnetFields as $key=>$field) {
				if(strlen($SubnetDetails[$key])>0) {
				$SubnetDetails[$key] = str_replace("\n", "<br>",$SubnetDetails[$key]);
				print "<tr>";
				print "	<th>$key</th>";
				print "	<td>";
				
				//booleans
				if($field['type']=="tinyint(1)")	{
					if($SubnetDetails[$key] == "0")		{ print _("No"); }
					elseif($SubnetDetails[$key] == "1")	{ print _("Yes"); }
				} 
				else {
					print $SubnetDetails[$key];
					
				}

				print "	</td>";
				print "</tr>";
				}
			}
		}
		
		
		/* action button groups */
		print "<tr>";
		print "	<th>"._('Actions')."</th>";
		print "	<td class='actions'>";
	
		print "	<div class='btn-toolbar'>";
		
		/* set values for permissions */
		if($permission == 1) {
			$sp['editsubnet']= false;		//edit subnet
			$sp['editperm']  = false;		//edit permissions
			
			$sp['addip'] 	 = false;		//add ip address
			$sp['scan']		 = false;		//scan subnet
			$sp['changelog'] = false;		//changelog view
			$sp['import'] 	 = false;			//import
		}
		else if ($permission == 2) { 
			$sp['editsubnet']= false;		//edit subnet
			$sp['editperm']  = false;		//edit permissions
			
			$sp['addip'] 	 = true;		//add ip address
			$sp['scan']		 = true;		//scan subnet
			$sp['changelog'] = true;		//changelog view
			$sp['import'] 	 = true;		//import
		}
		else if ($permission == 3) {
			$sp['editsubnet']= true;		//edit subnet
			$sp['editperm']  = true;		//edit permissions

			$sp['addip'] 	 = true;		//add ip address
			$sp['scan']		 = true;		//scan subnet
			$sp['changelog'] = true;		//changelog view
			$sp['import'] 	 = true;		//import
		}


		# edit / permissions / nested / favourites / changelog
		print "<div class='btn-group'>";

			//warning
			if($permission == 1) 
			print "<button class='btn btn-xs btn-default btn-danger' 	data-container='body' rel='tooltip' title='"._('You do not have permissions to edit subnet or IP addresses')."'>																	<i class='fa fa-lock'></i></button> ";

			// edit subnet
			if($sp['editsubnet'])
			print "<a class='edit_subnet btn btn-xs btn-default' 	href='' data-container='body' rel='tooltip' title='"._('Edit subnet properties')."'	data-subnetId='$SubnetDetails[id]' data-sectionId='$SubnetDetails[sectionId]' data-action='edit'>	<i class='fa fa-pencil'></i></a>";			
			else
			print "<a class='btn btn-xs btn-default disabled' 		href='' data-container='body' rel='tooltip' title='"._('Edit subnet properties')."'>																									<i class='fa fa-pencil'></i></a>";			

			//permissions
			if($sp['editperm']) 
			print "<a class='showSubnetPerm btn btn-xs btn-default' href='' data-container='body' rel='tooltip' title='"._('Manage subnet permissions')."'	data-subnetId='$SubnetDetails[id]' data-sectionId='$SubnetDetails[sectionId]' data-action='show'>	<i class='fa fa-tasks'></i></a>";			
			else 
			print "<a class='btn btn-xs btn-default disabled' 		href='' data-container='body' rel='tooltip' title='"._('Manage subnet permissions')."'>																										<i class='fa fa-tasks'></i></a>";			

			// add nested subnet
			if($permissionsSection == 3) {
			print "<a class='edit_subnet btn btn-xs btn-default '	href='' data-container='body' rel='tooltip' title='"._('Add new nested subnet')."' 		data-subnetId='$SubnetDetails[id]' data-action='add' data-id='' data-sectionId='$SubnetDetails[sectionId]'> <i class='fa fa-plus-circle'></i></a> ";
			} else {
			print "<a class='btn btn-xs btn-default disabled' 		href=''> 																																															<i class='fa fa-plus-circle'></i></a> ";			
			}	

			//favourite
			if(isSubnetFavourite($SubnetDetails['id'])) 
			print "<a class='btn btn-xs btn-default btn-info editFavourite favourite-$SubnetDetails[id]' href='' data-container='body' rel='tooltip' title='"._('Click to remove from favourites')."' data-subnetId='$SubnetDetails[id]' data-action='remove'>				<i class='fa fa-star'></i></a> ";	
			else 
			print "<a class='btn btn-xs btn-default editFavourite favourite-$SubnetDetails[id]' 		 href='' data-container='body' rel='tooltip' title='"._('Click to add to favourites')."' data-subnetId='$SubnetDetails[id]' data-action='add'>						<i class='fa fa-star fa-star-o' ></i></a> ";				
			// changelog
			if($settings['enableChangelog']==1) {
			if($sp['changelog']) 
			print "<a class='sChangelog btn btn-xs btn-default'     									 href='subnets/$SubnetDetails[sectionId]/$SubnetDetails[id]/changelog/' data-container='body' rel='tooltip' title='"._('Changelog')."'>								<i class='fa fa-clock-o'></i></a>";				
			else 
			print "<a class='btn btn-xs btn-default disabled'     									 	 href='' 																data-container='body' rel='tooltip' title='"._('Changelog')."'>								<i class='fa fa-clock-o'></i></a>";									
			}
		
		
		print "</div>";
		
		# add / requests / scan
		print "<div class='btn-group'>";

			// if full prevent new
			if(reformatNumber ($CalculateSubnetDetails['freehosts']) == "0" || !$sp['addip']) 
			print "<a class='btn btn-xs btn-default btn-success disabled' 	href='' data-container='body' rel='tooltip' title='"._('Add new IP address')."'>																					<i class='fa fa-plus'></i></a> ";
			else 
			print "<a class='modIPaddr btn btn-xs btn-default btn-success' 	href='' data-container='body' rel='tooltip' title='"._('Add new IP address')."' data-subnetId='$SubnetDetails[id]' data-action='add' data-id=''>					<i class='fa fa-plus'></i></a> ";	
			//requests
			if($SubnetDetails['allowRequests'] == 1 && $permission == 1)  {
			print "<a class='request_ipaddress btn btn-xs btn-default btn-success' 	href='' data-container='body' rel='tooltip' title='"._('Request new IP address')."' data-subnetId='$SubnetDetails[id]'>										<i class='fa fa-plus-circle'>  </i></a>";				
			}
			// subnet scan
			if($sp['scan']) 
			print "<a class='scan_subnet btn btn-xs btn-default'			href='' data-container='body' rel='tooltip' title='"._('Scan subnet for new hosts')."' 	data-subnetId='$SubnetDetails[id]'> 										<i class='fa fa-cogs'></i></a> ";					
			else 
			print "<a class='btn btn-xs btn-default disabled'				href='' data-container='body' rel='tooltip' title='"._('Scan subnet for new hosts')."'> 																			<i class='fa fa-cogs'></i></a> ";							
		print "</div>";

		# export / import
		print "<div class='btn-group'>";
			//import
			if($sp['import']) 
			print "<a class='csvImport btn btn-xs btn-default'  href='' data-container='body' rel='tooltip' title='"._('Import IP addresses')."' data-subnetId='$SubnetDetails[id]'>		<i class='fa fa-upload'></i></a>";			
			else
			print "<a class='btn btn-xs btn-default disabled'  	href='' data-container='body' rel='tooltip' title='"._('Import IP addresses')."'>											<i class='fa fa-upload'></i></a>";			
			//export
			print "<a class='csvExport btn btn-xs btn-default'  href='' data-container='body' rel='tooltip' title='"._('Export IP addresses')."' data-subnetId='$SubnetDetails[id]'>		<i class='fa fa-download'></i></a>";					
		print "</div>";		
		
			
		print "	</div>";
		
		print "	</td>";
		print "</tr>";
		
		?>
	
	</table>	<!-- end subnet table -->
</div>

<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
	<h4><?php print _('Usage chart'); ?></h4>
	<hr>
	<div id="pieChart" style="height:220px;width:100%;"></div>
	<?php include('subnetDetailsGraph.php'); ?>
</div>

</div>		<!-- end row -->
<br>