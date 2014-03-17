<?php

/**
 * Script to print subnets
 ***************************/

/* verify that user is admin */
if (!checkAdmin()) die('');

/* print all sections with delete / edit button */
print '<h4>'._('Subnet management').'</h4>' . "\n";
print "<hr>";


/* first we need to fetch all sections */
$sections = fetchSections ();

/* get all site settings */
$settings = getAllSettings();

/* read cookie for showing subnets */
if(isset($_COOKIE['showSubnets'])) {
	if($_COOKIE['showSubnets'] == 1) {
		$display = "";
		$icon    = "fa-compress";	
		$iconchevron = "fa-angle-down";
	}
	else {
		$display = "display:none";
		$icon    = "fa-expand";	
		$iconchevron = "fa-angle-right";	
	}
}
else {
		$display = "display:none";
		$icon    = "fa-expand";
		$iconchevron = "fa-angle-right";	
}


/* Foreach section fetch subnets and print it! */
if(sizeof($sections) > 0) {

	# expand / collapse
	print "<button id='toggleAllSwitches' class='btn btn-sm btn-default pull-right' rel='tooltip' data-placement='left' title='"._('click to show/hide all subnets')."'><i class='fa $icon'></i></button>";
	
	# print  table structure
	print "<table id='manageSubnets' class='table table-striped table-condensed table-top table-absolute'>";
	
	$m = 0;	# for subnet id
	
	# print titles and content
	foreach($sections as $section)
	{
		# set colcount
		if($settings['enableVRF'] == 1)		{ $colCount = "8"; }
		else								{ $colCount = "7"; }
		
		# print name
		print "<tbody id='subnet-$m'>";
		print "<tr class='subnet-title'>";
		print "	<th colspan='$colCount'>";
		print "		<h4><button class='btn btn-xs btn-default' id='subnet-$m' rel='tooltip' title='"._('click to show/hide belonging subnets')."'><i class='fa $iconchevron'></i></button> $section[name] </h4>";
		print "	</th>";
		print "</tr>";
		print "</tbody>";
		
		# get all subnets in section
		$subnets = fetchSubnets($section['id']);
	
		# collapsed div with details
		print "<tbody id='content-subnet-$m' style='$display'>";
				
		# headers
		print "<tr>";
		print "	<th>"._('Subnet')."</th>";
		print "	<th>"._('Description')."</th>";
		print "	<th class='hidden-xs hidden-sm'>"._('VLAN')."</th>";
		if($settings['enableVRF'] == 1) {
		print "	<th class='hidden-xs hidden-sm'>"._('VRF')."</th>";
		}
		print "	<th class='hidden-xs hidden-sm hidden-md'>"._('Requests')."</th>";
		print "	<th class='hidden-xs hidden-sm hidden-md'>"._('Hosts check')."</th>";
		print "	<th class='actions'></th>";
		print "</tr>";

		# add new link
		print "<tr>";
		print "	<td colspan='$colCount'>";
		print "		<button class='btn btn-sm btn-default editSubnet' data-action='add' data-sectionid='$section[id]' rel='tooltip' data-placement='right' title='"._('Add new subnet to section')." $section[name]'><i class='fa fa-plus'></i> "._('Add subnet')."</button>";
		print "	</td>";
		print "	</tr>";

		# no subnets
		if(sizeof($subnets) == 0) {
			print "<tr><td colspan='$colCount'><div class='alert alert-info'>"._('Section has no subnets')."!</div></td></tr>";
		}	
		else {
			# subnets
			$subnets2 = printAdminSubnets($subnets, true, $settings['enableVRF']);
			print $subnets2;				
		}
		print "</tbody>";
		$m++;
	}
	
	# end master table
	print "</table>";
} 
?>