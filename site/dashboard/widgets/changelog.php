<?php

/*
 * Script to print some stats on home page....
 *********************************************/

/* required functions */
if(!function_exists('getSubnetStatsDashboard')) {
require_once( dirname(__FILE__) . '/../../../functions/functions.php' );
}

/* if direct request that redirect to tools page */
if($_SERVER['HTTP_X_REQUESTED_WITH']!="XMLHttpRequest")	{ 
	header("Location: ".BASE."tools/changelog/");
}

/* get logs */
$clogs = getAllChangelogs(false, "", 50);
?>



<?php
if(sizeof($clogs)==0) {
	print "<blockquote style='margin-top:20px;margin-left:20px;'>";
	print "<p>"._("No changelogs available")."</p>";
	print "<small>"._("No changelog entries are available")."</small>";
	print "</blockquote>";
} 
# print
else {

	# printout
	print "<table class='table changelog table-hover table-top table-condensed'>";

	# headers
	print "<tr>";
	print "	<th>"._('User')."</th>";
	print "	<th>"._('Object')."</th>";
	print "	<th>"._('Date')."</th>";
	print "	<th>"._('Change')."</th>";
	print "</tr>";
	
	# logs
	$pc = 0;					//print count
	foreach($clogs as $l) {
	
		if($pc < 5) {
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
				
				print "	<td>$l[cdate]</td>";
				print "	<td>$l[cdiff]</td>";
				print "</tr>";		
			
				$pc++;
			}
		}
	}
	
	print "</table>";


} 
?>

