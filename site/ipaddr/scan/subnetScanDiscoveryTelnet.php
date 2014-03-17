<?php

/*
 * Discover new hosts with ping
 *******************************/

/* required functions */
require_once('../../../functions/functions.php'); 

/* verify that user is logged in */
isUserAuthenticated(true);

/* verify that user has write permissions for subnet */
$subnetPerm = checkSubnetPermission ($_REQUEST['subnetId']);
if($subnetPerm < 2) 			{ die('<div class="alert alert-danger">'._('You do not have permissions to modify hosts in this subnet').'!</div>'); }

# get ports
if(strlen($_POST['port'])==0) 	{ die('<div class="alert alert-danger">'._('Please enter ports to scan').'!</div>'); }

//verify ports
$pcheck = explode(";", str_replace(",",";",$_POST['port']));
foreach($pcheck as $p) {
	if(!is_numeric($p)) {
		die("<div class='alert alert-danger'>"._("Invalid port")." ($p)</div>");
	}
}
$_POST['port'] = str_replace(",",";",$_POST['port']);

# verify post */
CheckReferrer();

# get subnet details
$subnet = getSubnetDetailsById ($_POST['subnetId']);

# get all existing IP addresses
$addresses = getIpAddressesBySubnetId ($_POST['subnetId']);

# set start and end IP address
$calc = calculateSubnetDetailsNew ( $subnet['subnet'], $subnet['mask'], 0, 0, 0, 0 );
$max = $calc['maxhosts'];

# loop and get all IP addresses for ping
for($m=1; $m<=$max;$m++) {
	// create array of IP addresses (if they do not already exist!)
	if (!checkDuplicate (transform2long($subnet['subnet']+$m), $_POST['subnetId'])) {
		$ip[] = $subnet['subnet']+$m;
	}
}

# check if any hits are present
if($ip) {
	# create 1 line for $argv
	$ip = implode(";", $ip);
	
	# get php exec path
	if(!$phpPath = getPHPExecutableFromPath()) {
		die('<div class="alert alert-danger">Cannot access php executable!</div>');
	}
	# set script
	$script = dirname(__FILE__) . '/../../../functions/scan/scanIPAddressesTelnetScript.php';
	
	# invoke CLI with threading support
	$cmd = "$phpPath $script '$ip' '$_POST[port]'";
	
	# save result to $output
	exec($cmd, $output, $retval);
	
	# die of error
	if($retval != 0) {
		die("<div class='alert alert-danger'>Error executing scan! Error code - $retval</div>");
	}
			
	# format result - alive
	$result = json_decode(trim($output[0]), true);
	$alive = $result['alive'];
	$dead  = @$result['dead'];
	$serr  = @$result['error'];
}

?>


<h5><?php print _('Scan results');?>:</h5>
<hr>

<?php
# empty
if(sizeof($alive)==0) {
	print "<div class='alert alert-info'>"._("No alive host found")."!</div>";
	# errors?
	if(isset($serr) && sizeof(@$serr)>0) {
		print "<div class='alert alert-danger'>"._("Errors occured during scan")."! (".sizeof($serr)." errors)</div>";
	}
}
# found alive
else {
	print "<form name='".$_REQUEST['pingType']."Form' class='".$_REQUEST['pingType']."Form'>";
	print "<table class='table table-striped table-top table-condensed'>";
	
	// titles
	print "<tr>";
	print "	<th>"._("IP")."</th>";
	print "	<th>"._("Port")."</th>";
	print "	<th>"._("Description")."</th>";
	print "	<th>"._("Hostname")."</th>";
	print "	<th></th>";
	print "</tr>";
	
	// alive
	$m=0;
	foreach($alive as $ip=>$port) {
	
		//resolve?
		if($scanDNSresolve) {
			$dns = gethostbyaddr ( transform2long($ip) );
		}
		else {
			$dns = "test";
		}
		
		//gather ports
		sort($port);
		$allports = implode(",", $port);
		
		print "<tr class='result$m'>";
		//ip
		print "<td>".transform2long($ip)."</td>";
		//port
		print "<td>$allports</td>";
		//description
		print "<td>";
		print "	<input type='text' class='form-control input-sm' name='description$m'>";
		print "	<input type='hidden' name='ip$m' value=".transform2long($ip).">";
		print "</td>";
		//hostname
		print "<td>";
		print "	<input type='text' class='form-control input-sm' name='dns_name$m' value='".@$dns."'>";
		print "</td>";
		//remove button
		print 	"<td><a href='' class='btn btn-xs btn-danger resultRemove' data-target='result$m'><i class='fa fa-times'></i></a></td>";
		print "</tr>";
		
		$m++;
	}
	
	//result
	print "<tr>";
	print "	<td colspan='4'>";
	print "<div id='subnetScanAddResult'></div>";
	print "	</td>";
	print "</tr>";	
	
	//submit
	print "<tr>";
	print "	<td colspan='4'>";
	print "		<a href='' class='btn btn-sm btn-success pull-right' id='saveScanResults' data-script='".$_REQUEST['pingType']."' data-subnetId='".$_REQUEST['subnetId']."'><i class='fa fa-plus'></i> "._("Add discovered hosts")."</a>";
	print "	</td>";
	print "</tr>";
	
	print "</table>";
	print "</form>";
}
?>