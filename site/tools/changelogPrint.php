<?php

/**
 * Script to display available VLANs
 */

/* required functions */
if(!function_exists('getSubnetStatsDashboard')) {
require_once( dirname(__FILE__) . '/../../../functions/functions.php' );
}

/* verify that user is authenticated! */
isUserAuthenticated ();

# get clog entries
if(!isset($_REQUEST['cfilter'])) 	{ $clogs = getAllChangelogs(false, "", $_REQUEST['climit']); }
else								{ $clogs = getAllChangelogs(true, $_REQUEST['cfilter'], $_REQUEST['climit']); }

# empty
if(sizeof($clogs)==0) {
	print "<blockquote style='margin-top:20px;margin-left:20px;'>";
	print "<p>"._("No changelogs available")."</p>";
	print "<small>"._("No changelog entries are available")."</small>";
	print "</blockquote>";
}
# result
else {
	# if more that configured print it!
	if(sizeof($clogs)==$_REQUEST['climit']) {
		print "<div class='alert alert-warning alert-absolute'>"._("Output has been limited to last $_REQUEST[climit] lines")."!</div>";
	}
	
	# printout
	print "<table class='table table-striped table-top table-condensed'>";

	# headers
	print "<tr>";
	print "	<th>"._('User')."</th>";
	print "	<th>"._('Type')."</th>";
	print "	<th>"._('Object')."</th>";
	print "	<th>"._('Action')."</th>";
	print "	<th>"._('Result')."</th>";
	print "	<th>"._('Date')."</th>";
	print "	<th>"._('Change')."</th>";
	print "</tr>";
	
	# logs
	foreach($clogs as $l) {
	
		# permissions
		$permission = checkSubnetPermission ($l['subnetId']);

		# if 0 die
		if($permission != "0")	{ 	
			# format diff
			$l['cdiff'] = str_replace("\n", "<br>", $l['cdiff']);
			
			# format type
			switch($l['ctype']) {
				case "ip_addr":	$l['ctype'] = "IP address";	break;
				case "subnet":  if($l['isFolder']==1) 	{ $l['ctype'] = "Folder"; } 
								else 					{ $l['ctype'] = "Subnet"; }
				break;
				
				case "section":	$l['ctype'] = "Section";	break;
			}
		
			print "<tr>";
			print "	<td>$l[real_name]</td>";
			print "	<td>$l[ctype]</td>";
			
			# subnet, section or ip address
			if($l['ctype']=="IP address")	{
				print "	<td><a href='subnets/$l[sectionId]/$l[subnetId]/ipdetails/$l[tid]/'>".transform2long($l['ip_addr'])."</a></td>";			
			} 
			elseif($l['ctype']=="Subnet")   {
				print "	<td><a href='subnets/$l[sectionId]/$l[tid]/'>".transform2long($l['ip_addr'])."/$l[mask]</a></td>";							
			}
			elseif($l['ctype']=="Folder")   {
				print "	<td><a href='folder/$l[sectionId]/$l[tid]/'>$l[sDescription]</a></td>";						
			}
			
			print "	<td>"._("$l[caction]")."</td>";
			print "	<td>"._("$l[cresult]")."</td>";
			print "	<td>$l[cdate]</td>";
			print "	<td>$l[cdiff]</td>";
			print "</tr>";		
		}
	}
	
	print "</table>";
}
?>