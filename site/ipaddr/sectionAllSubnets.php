<?php

/**
 * Script to print subnets
 ***************************/

/* verify that user is authenticated! */
isUserAuthenticated ();

/* get custom fields */
$custom = getCustomFields('subnets');

/* get all site settings */
$settings = getAllSettings();


# title
print "<h4>"._('Available subnets')."</h4>";

# check permission
$permission = checkSectionPermission ($_REQUEST['section']);
if($permission != "0") {

	# print  table structure
	print "<table id='manageSubnets' class='table table-striped table-condensed table-top table-absolute'>";
	
		# set colcount
		if($settings['enableVRF'] == 1)		{ $colCount = 8; }
		else								{ $colCount = 7; }
		
		# get Available subnets in section
		$subnets = fetchSubnets($_REQUEST['section']);
		
		# remove custom fields if all empty! */
		foreach($custom as $field) {
			$sizeMyFields[$field['name']] = 0;				// default value
			# check against each IP address
			foreach($subnets as $subn) {
				if(strlen($subn[$field['name']]) > 0) {
					$sizeMyFields[$field['name']]++;		// +1
				}
			}	
			# unset if value == 0
			if($sizeMyFields[$field['name']] == 0) {
				unset($custom[$field['name']]);
			} 
			else {
				$colCount++;								// colspan
			}
		}
	
		# collapsed div with details
		print "<tbody>";
				
		# headers
		print "<tr>";
		print "	<th>"._('Subnet')."</th>";
		print "	<th>"._('Description')."</th>";
		print "	<th>"._('VLAN')."</th>";
		if($settings['enableVRF'] == 1) {
		print "	<th class='hidden-xs hidden-sm'>"._('VRF')."</th>";
		}
		print "	<th class='hidden-xs hidden-sm'>"._('Requests')."</th>";
		print "	<th class='hidden-xs hidden-sm'>"._('Hosts check')."</th>";
		if(sizeof($custom) > 0) {
			foreach($custom as $field) {
				print "	<th class='hidden-xs hidden-sm'>$field[name]</th>";
			}
		}
		print "	<th class='actions' style='width:140px;white-space:nowrap;'></th>";
		print "</tr>";
	
		# no subnets
		if(sizeof($subnets) == 0) {
			print "<tr><td colspan='$colCount'><div class='alert alert-info'>"._('Section has no subnets')."!</div></td></tr>";
				
			# check Available subnets for subsection
			$subsections = getAllSubSections($_REQUEST['section']);
			
			$colspan = 6 + sizeof($custom);
			if($settings['enableVRF'] == 1) { $colspan++; }
		}	
		else {
			# subnets
			$subnets2 = printSubnets($subnets, true, $settings['enableVRF'], $custom);
			print $subnets2;				
		}

		# subsection subnets
		if(sizeof($subsections)>0) {

			$colspan = 6 + sizeof($custom);
			if($settings['enableVRF'] == 1) { $colspan++; }
			
			# subnets
			foreach($subsections as $ss) {
				$slavesubnets = fetchSubnets($ss['id']);
				if(sizeof($slavesubnets)>0) {
					# headers
					print "<tr>";
					print "	<th colspan='$colspan'>"._('Available subnets in subsection')." $ss[name]:</th>";
					print "</tr>";
					
					# subnets
					$subnets3 = printSubnets($slavesubnets, true, $settings['enableVRF'], $custom);
					print $subnets3;
				}
				else {
					print "<tr>";
					print "	<th colspan='$colspan'>"._('Available subnets in subsection')." $ss[name]:</th>";
					print "</tr>";	
					
					print "<tr>";
					print "	<td colspan='$colspan'><div class='alert alert-info'>"._('Section has no subnets')."!</div></td>";
					print "</tr>";			
				}
			}				
		} 

		print "</tbody>";
		$m++;
	
	# end master table
	print "</table>";
}
else {
	print "<div class='alert alert-danger'>"._("You do not have permission to access this network")."!</div>";
}
?>