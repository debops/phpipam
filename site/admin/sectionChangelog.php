<?php

/**
 * Script to display IP address info and history
 ***********************************************/

/* verify that user is authenticated! */
isUserAuthenticated ();

# get clog entries
$clogs = getChangelogEntries("section", $_REQUEST['sectionId']);

/* verify that user is admin */
if (!checkAdmin()) die('');

# header
print "<h4>"._('Section')." - "._('Changelog')."</h4><hr>";

# back
print "<a class='btn btn-sm btn-default' href='administration/manageSection/'><i class='fa fa-angle-left'></i> "._('Back to section')."</a>";


# empty
if(sizeof($clogs)==0) {
	print "<blockquote style='margin-top:20px;margin-left:20px;'>";
	print "<p>"._("No changelogs available")."</p>";
	print "<small>"._("No changelog entries are available for this section")."</small>";
	print "</blockquote>";
}
# result
else {
	# printout
	print "<table class='table table-striped table-top table-condensed' style='margin-top:30px;'>";

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