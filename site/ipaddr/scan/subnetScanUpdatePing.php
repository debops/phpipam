<?php

/*
 * Update alive status of all hosts in subnet
 ***************************/

/* required functions */
require_once('../../../functions/functions.php'); 

/* verify that user is logged in */
isUserAuthenticated(true);

/* verify that user has write permissions for subnet */
$subnetPerm = checkSubnetPermission ($_REQUEST['subnetId']);
if($subnetPerm < 2) 	{ die('<div class="alert alert-danger">'._('You do not have permissions to modify hosts in this subnet').'!</div>'); }

/* verify post */
CheckReferrer();

# get subnet details
$subnet = getSubnetDetailsById ($_POST['subnetId']);

# get all existing IP addresses
$addresses = getIpAddressesBySubnetId ($_POST['subnetId']);


# get php exec path
if(!$phpPath = getPHPExecutableFromPath()) {
	die('<div class="alert alert-danger">Cannot access php executable!</div>');
}
# set script
$script = dirname(__FILE__) . '/../../../functions/scan/scanIPAddressesScript.php';

# invoke CLI with threading support
$cmd = "$phpPath $script 'update' '".transform2long($subnet['subnet'])."/$subnet[mask]' '$_POST[subnetId]'";

# save result to $output
exec($cmd, $output, $retval);
	
# die of error
if($retval != 0) {
	die("<div class='alert alert-danger'>Error executing scan! Error code - $retval</div>");
}
		
# format result - alive
$result = json_decode(trim($output[0]), true);

# recode to same array with statuses 
$m=0;
foreach($result as $k=>$r) {

	foreach($r as $ip) {
		# get details
		$ipdet = getIpAddrDetailsByIPandSubnet ($ip, $_POST['subnetId']);

		# format output
		$res[$ip]['ip_addr'] 	 = $ip;
		$res[$ip]['description'] = $ipdet['description'];
		$res[$ip]['dns_name'] 	 = $ipdet['dns_name'];
		
		//online
		if($k=="alive")	{ 
			$res[$ip]['status'] = "Online";			
			$res[$ip]['code']=0; 
			//update alive time
			@updateLastSeen($ipdet['id']);
		}		
		//offline
		elseif($k=="dead")	{ 
			$res[$ip]['status'] = "Offline";			
			$res[$ip]['code']=1; 
		}
		//excluded
		elseif($k=="excluded")	{ 
			$res[$ip]['status'] = "Excluded form check";			
			$res[$ip]['code']=100; 
		}
		else { 
			$res[$ip]['status'] = "Error";
			$res[$ip]['code']=2; 
		}			
		$m++;
	}
}

#  errors
$error = @$result['errors'];
?>


<h5><?php print _('Scan results');?>:</h5>
<hr>

<?php
# error?
if(isset($error)) {
	print "<div class='alert alert-danger'><strong>"._("Error").": </strong>$error</div>";
}
//empty
elseif(!isset($res)) {
	print "<div class='alert alert-info'>"._('Subnet is empty')."</div>";
}
else {
	# order by IP address
	ksort($res);

	//table
	print "<table class='table table-condensed table-top'>";
	
	//headers
	print "<tr>";
	print "	<th>"._('IP')."</th>";
	print "	<th>"._('Description')."</th>";
	print "	<th>"._('status')."</th>";
	print "	<th>"._('hostname')."</th>";
	print "</tr>";
	
	//loop
	foreach($res as $r) {
		//set class
		if($r['code']==0)		{ $class='success'; }
		elseif($r['code']==100)	{ $class='warning'; }		
		else					{ $class='danger'; }
	
		print "<tr class='$class'>";
		print "	<td>".transform2long($r['ip_addr'])."</td>";
		print "	<td>".$r['description']."</td>";
		print "	<td>"._("$r[status]")."</td>";
		print "	<td>".$r['dns_name']."</td>";

		print "</tr>";
	}
	
	print "</table>";
}
?>