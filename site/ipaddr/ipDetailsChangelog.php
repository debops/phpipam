<?php

/**
 * Script to display IP address info and history
 ***********************************************/

/* verify that user is authenticated! */
isUserAuthenticated ();

# get clog entries
$clogs = getChangelogEntries("ip_addr", $_REQUEST['ipaddrid']);

# permissions
$permission = checkSubnetPermission ($_REQUEST['subnetId']);
if($permission == "0")	{ die("<div class='alert alert-danger'>"._('You do not have permission to access this network')."!</div>"); }

# header
print "<h4 style='margin-top:30px;'>"._('Changelog')."</h4><hr>";

# empty
if(sizeof($clogs)==0) {
	print "<blockquote style='margin-top:20px;margin-left:20px;'>";
	print "<p>"._("No changelogs available")."</p>";
	print "<small>"._("No changelog entries are available for this host")."</small>";
	print "</blockquote>";
}
# result
else {
	# printout
	print "<table class='table table-striped table-top table-condensed'>";

	# headers
	print "<tr>";
	print "	<th>"._('User')."</th>";
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
		print "	<td>"._("$l[caction]")."</td>";
		print "	<td>"._("$l[cresult]")."</td>";
		print "	<td>$l[cdate]</td>";
		print "	<td>$l[cdiff]</td>";
		print "</tr>";		

	}
	
	print "</table>";
}
?>