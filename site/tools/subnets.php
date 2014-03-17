<?php

/**
 * Script to display available VLANs
 */

/* verify that user is authenticated! */
isUserAuthenticated ();

/* get all sections */
$sections = fetchSections ();

/* get custom fields */
$custom = getCustomFields('subnets');

# title
print "<h4>"._('Available subnets')."</h4>";
print "<hr>";

# table
print "<table id='manageSubnets' class='table table-striped table-condensed table-top table-absolute'>";

# print vlans in each section
foreach ($sections as $section) {

	# check permission
	$permission = checkSectionPermission ($section['id']);
	if($permission != "0") {

		# set colspan
		$colSpan = 9 + (sizeof($custom));

		# section names
		print "<tbody>";
		print "	<tr class='subnets-title'>";
		print "		<th colspan='$colSpan'><h4>$section[name] [$section[description]]</h4></th>";
		print "	</tr>";
		print "</tbody>";	

		# body
		print "<tbody>";

		# headers
		print "	<tr>";
		print "	<th>"._('Subnet')."</th>";
		print "	<th>"._('Description')."</th>";
		print "	<th>"._('VLAN')."</th>";	
		print "	<th>"._('Master Subnet')."</th>";
		print "	<th class='hidden-xs hidden-sm'>"._('Used')."</th>";
		print "	<th class='hidden-xs hidden-sm'>"._('Requests')."</th>";
		print "	<th class='hidden-xs hidden-sm'>"._('Hosts check')."</th>";
		if(sizeof($custom) > 0) {
			foreach($custom as $field) {
				print "	<th class='hidden-xs hidden-sm hidden-md'>$field[name]</th>";
			}
		}
		print "</tr>";
	
		# get all subnets in section
		$subnets = fetchSubnets ($section['id']);

		# no subnets
		if(sizeof($subnets) == 0) {
			print "<tr><td colspan='$colSpan'><div class='alert alert-info'>"._('Section has no subnets')."!</div></td></tr>";
		}	
		else {
			$subnetPrint = printToolsSubnets($subnets, $custom);
			print $subnetPrint;
		}

		print '</tbody>';
	
	}	# end permission check
}
?>

</table>