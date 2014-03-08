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
if($subnetPerm < 2) 	{ die('<div class="alert alert-error">'._('You do not have permissions to modify hosts in this subnet').'!</div>'); }

/* verify post */
CheckReferrer();

# get subnet details
$subnet = getSubnetDetailsById ($_POST['subnetId']);

# get all existing IP addresses
$addresses = getIpAddressesBySubnetId ($_POST['subnetId']);


# loop and check
foreach($addresses as $m=>$ip) {

	//if strictly disabled for ping
	if($ip['excludePing']=="1") {
		$ip['status'] = "excluded from check";
	}
	//ping
	else {
		$code = pingHostPear (transform2long($ip['ip_addr']), 1, false);
		
		//success
		if($code['code']==0) {
			@updateLastSeen($ip['id']);				//update last seen
			$code['text'] = "Online (".$code['text'].")";
		}
		else {
			//never?
			if($ip['lastSeen']=="0000-00-00 00:00:00" || is_null($ip['lastSeen'])) {
				$code['text'] = $code['text']." (last seen - never)";
			} else {
				$code['text'] = $code['text']." (last seen $ip[lastSeen])";
			}
		}

		$res[$m] = $ip;
		$res[$m]['code'] = $code['code'];
		$res[$m]['status'] = $code['text'];
	}	
}
?>


<h5><?php print _('Scan results');?>:</h5>
<hr>

<?php
//empty
if(!isset($res)) {
	print "<div class='alert alert-info'>"._('Subnet is empty')."</div>";
}
else {
	//table
	print "<table class='table table-condensed'>";
	
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
		if($r['code']==0)	{ $class='success'; }
		else				{ $class='error'; }
	
		print "<tr class='$class'>";
		print "	<td>".transform2long($r['ip_addr'])."</td>";
		print "	<td>$r[dns_name]</td>";
		print "	<td>$r[status]</td>";
		print "	<td>$r[description]</td>";

		print "</tr>";
	}
	
	print "</table>";
}
?>