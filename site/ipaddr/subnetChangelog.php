<?php

/**
 * Script to display IP address info and history
 ***********************************************/

/* verify that user is authenticated! */
isUserAuthenticated ();

# get clog entries
$clogs = getChangelogEntries("subnet", $_REQUEST['subnetId']);

# changelog for allslaves
$clogsSlaves = getSubnetSlaveChangelogEntries($_REQUEST['subnetId']);

# changelog for each IP address, also in slave subnets
$clogsIP = getSubnetIPChangelogEntries($_REQUEST['subnetId']);

# get subnet
$subnetDetails = getSubnetDetailsById ($_REQUEST['subnetId']);


# permissions
$permission = checkSubnetPermission ($_REQUEST['subnetId']);
if($permission == "0")	{ die("<div class='alert alert-danger'>"._('You do not have permission to access this network')."!</div>"); }

# header
print "<h4>"._('Subnet')." - "._('Changelog')."</h4><hr>";

# back
if($subnetDetails['isFolder']==1) {
	print "<a class='btn btn-sm btn-default' href='folder/$_REQUEST[section]/$_REQUEST[subnetId]/'><i class='fa fa-gray fa-chevron-left'></i> "._('Back to subnet')."</a>";
} else {
	print "<a class='btn btn-sm btn-default' href='subnets/$_REQUEST[section]/$_REQUEST[subnetId]/'><i class='fa fa-gray fa-chevron-left'></i> "._('Back to subnet')."</a>";
}


/* Subnet changelog */

# empty
if(sizeof($clogs)==0) {
	print "<blockquote style='margin-top:20px;margin-left:20px;'>";
	print "<p>"._("No changelogs available")."</p>";
	print "<small>"._("No changelog entries are available for this subnet")."</small>";
	print "</blockquote>";
}
# result
else {
	# printout
	print "<table class='table table-striped table-top table-condensed' style='margin-top:30px;'>";

	# headers
	print "<tr>";
	print "	<th>"._('User')."</th>";
	print "	<th>"._('Subnet')."</th>";
	print "	<th>"._('Action')."</th>";
	print "	<th>"._('Result')."</th>";
	print "	<th>"._('Date')."</th>";
	print "	<th>"._('Change')."</th>";
	print "</tr>";
	
	# logs
	foreach($clogs as $l) {
	
		# format diff
		$l['cdiff'] = str_replace("\n", "<br>", $l['cdiff']);
	
		print "<tr>";
		print "	<td>$l[real_name]</td>";
		print "	<td><a href='subnets/$l[sectionId]/$l[id]/'>".transform2long($subnetDetails['subnet'])."/$subnetDetails[mask]</a></td>";
		print "	<td>"._("$l[caction]")."</td>";
		print "	<td>"._("$l[cresult]")."</td>";
		print "	<td>$l[cdate]</td>";
		print "	<td>$l[cdiff]</td>";
		print "</tr>";		

	}
	
	print "</table>";
}


/* Subnet slaves changelog */

# empty
if($clogsSlaves) {
	# header
	print "<h4 style='margin-top:30px;'>"._('Slave subnets')." "._('Changelog')."</h4><hr>";

	# printout
	print "<table class='table table-striped table-top table-condensed'>";

	# headers
	print "<tr>";
	print "	<th>"._('User')."</th>";
	print "	<th>"._('Subnet')."</th>";
	print "	<th>"._('Description')."</th>";
	print "	<th>"._('Action')."</th>";
	print "	<th>"._('Result')."</th>";
	print "	<th>"._('Date')."</th>";
	print "	<th>"._('Change')."</th>";
	print "</tr>";
	
	# logs
	foreach($clogsSlaves as $l) {
	
		# format diff
		$l['cdiff'] = str_replace("\n", "<br>", $l['cdiff']);
	
		print "<tr>";
		print "	<td>$l[real_name]</td>";
		print "	<td><a href='subnets/$l[sectionId]/$l[id]/'>".transform2long($l['subnet'])."/$l[mask]</a></td>";
		print "	<td>$l[description]</td>";
		print "	<td>"._("$l[caction]")."</td>";
		print "	<td>"._("$l[cresult]")."</td>";
		print "	<td>$l[cdate]</td>";
		print "	<td>$l[cdiff]</td>";
		print "</tr>";		

	}
	
	print "</table>";
}


/* IP changelog */

if($clogsIP) {
	# header
	print "<h4 style='margin-top:30px;'>"._('Underlying hosts')." "._('Changelog')."</h4><hr>";	

	# printout
	print "<table class='table table-striped table-top table-condensed'>";

	# headers
	print "<tr>";
	print "	<th>"._('User')."</th>";
	print "	<th>"._('IP')."</th>";
	print "	<th>"._('Action')."</th>";
	print "	<th>"._('Result')."</th>";
	print "	<th>"._('Date')."</th>";
	print "	<th>"._('Change')."</th>";
	print "</tr>";
	
	# logs
	foreach($clogsIP as $l) {
	
		# format diff
		$l['cdiff'] = str_replace("\n", "<br>", $l['cdiff']);
	
		print "<tr>";
		print "	<td>$l[real_name]</td>";
		print "	<td><a href='subnets/$_REQUEST[section]/$l[subnetId]/ipdetails/$l[id]/'>".transform2long($l['ip_addr'])."</a></td>";
		print "	<td>"._("$l[caction]")."</td>";
		print "	<td>"._("$l[cresult]")."</td>";
		print "	<td>$l[cdate]</td>";
		print "	<td>$l[cdiff]</td>";
		print "</tr>";		

	}
	
	print "</table>";
}


?>