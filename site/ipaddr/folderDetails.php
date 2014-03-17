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
if(sizeof($SubnetDetails) == 0) { die('<div class="alert alert-danger">'._('Folder does not exist').'!</div>'); }

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

# set rowspan
$rowSpan = 10 + $customSubnetFieldsSize;

# permissions
$permission = checkSubnetPermission ($subnetId);

# section permissions
$permissionsSection = checkSectionPermission ($SubnetDetails['sectionId']);

# if 0 die
if($permission == "0")	{ die("<div class='alert alert-danger'>"._('You do not have permission to access this folder')."!</div>"); }
?>

<!-- content print! -->

<!-- for adding IP address! -->
<div id="subnetId" style="display:none"><?php print $subnetId; ?></div>

<!-- subnet details upper table -->
<h4><?php print _('Folder details'); ?></h4>
<hr>

<table class="ipaddress_subnet table-condensed table-full">

	<tr>
		<th><?php print _('Hierarchy'); ?></th>
		<td>
			<?php printBreadCrumbs($_REQUEST); ?>
		</td>
	</tr>
	<tr>
		<th><?php print _('Folder name'); ?></th>
		<td><?php print html_entity_decode($SubnetDetails['description']); ?></td>
	</tr>
	<tr>
		<th><?php print _('Permission'); ?></th>
		<td><?php print parsePermissions($permission); ?></td>
	</tr>
	
	<?php	
	/* print custom subnet fields if any */
	if(sizeof($customSubnetFieldsSize) > 0) {
		foreach($customSubnetFields as $key=>$field) {
			if(strlen($SubnetDetails[$key]) > 0) {
			print "<tr>";
			print "	<th>$key</th>";
			print "	<td>$SubnetDetails[$key]</td>";
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
		$sp['changelog'] = false;		//changelog view
	}
	else if ($permission == 2) { 
		$sp['editsubnet']= false;		//edit subnet
		$sp['editperm']  = false;		//edit permissions			
		$sp['changelog'] = true;		//changelog view
	}
	else if ($permission == 3) {
		$sp['editsubnet']= true;		//edit subnet
		$sp['editperm']  = true;		//edit permissions
		$sp['changelog'] = true;		//changelog view
	}


	# edit / permissions / nested / favourites / changelog
	print "<div class='btn-group'>";

		//warning
		if($permission == 1) 
		print "<button class='btn btn-xs btn-default btn-danger' 	data-container='body' rel='tooltip' title='"._('You do not have permissions to edit subnet or IP addresses')."'>																	<i class='fa fa-lock'></i></button> ";

		// edit subnet
		if($sp['editsubnet'])
		print "<a class='add_folder btn btn-xs btn-default' 		href='' rel='tooltip' data-container='body' title='"._('Edit folder')."' 		data-action='edit' data-subnetId='$SubnetDetails[id]' data-sectionId='$SubnetDetails[sectionId]'>	<i class='fa fa-pencil'></i></a>";		# edit subnet
		else
		print "<a class='btn btn-xs btn-default disabled' 			href='' rel='tooltip' data-container='body' title='"._('Edit folder')."' >																											<i class='fa fa-pencil'></i></a>";		# edit subnet

		//permissions
		if($sp['editperm']) 
		print "<a class='showSubnetPerm btn btn-xs btn-default' href='' rel='tooltip' data-container='body' title='"._('Manage folder permissions')."'	data-subnetId='$SubnetDetails[id]' data-sectionId='$SubnetDetails[sectionId]' data-action='show'>	<i class='fa fa-tasks'></i></a>";			# edit subnet
		else 
		print "<a class='btn btn-xs btn-default disabled' 		href='' rel='tooltip' data-container='body' title='"._('Manage folder permissions')."'>																										<i class='fa fa-tasks'></i></a>";			# edit subnet

		// add nested subnet
		if($permissionsSection == 3) {
		print "<a class='edit_subnet btn btn-xs btn-default '	href='' data-container='body' rel='tooltip' title='"._('Add new nested subnet')."' 		data-subnetId='$SubnetDetails[id]' data-action='add' data-id='' data-sectionId='$SubnetDetails[sectionId]'> <i class='fa fa-plus-circle'></i></a> ";
		print "<a class='add_folder btn btn-xs btn-default '	href='' rel='tooltip' data-container='body' title='"._('Add new nested folder')."' 		data-subnetId='$SubnetDetails[id]' data-action='add' data-id='' data-sectionId='$SubnetDetails[sectionId]'> <i class='fa fa-folder-close-o'></i></a> ";		# add new child subnet
		} else {
		print "<a class='btn btn-xs btn-default disabled' 		href=''> 																																															<i class='fa fa-plus-circle'></i></a> ";			
		print "<a class='btn btn-xs btn-default disabled'		href=''> 																																															<i class='fa fa-folder-close-o'></i></a> ";		# add new child subnet
		}	
	print "</div>";

	print "<div class='btn-group'>";
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


	print "	</div>";
		
	print "	</td>";
	print "</tr>";
	
	?>

</table>	<!-- end subnet table -->
<br>