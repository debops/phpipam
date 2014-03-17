<?php

/*
 * Discover new hosts with ping
 *******************************/

/* required functions */
require_once('../../../functions/functions.php'); 
require_once( dirname(__FILE__) . '/../../../functions/scan/config-scan.php');

/* verify that user is logged in */
isUserAuthenticated(true);

/* verify that user has write permissions for subnet */
$subnetPerm = checkSubnetPermission ($_REQUEST['subnetId']);
if($subnetPerm < 2) 		{ die('<div class="alert alert-danger">'._('You do not have permissions to modify hosts in this subnet').'!</div>'); }

# verify post */
CheckReferrer();

# get subnet details
$subnet = getSubnetDetailsById ($_POST['subnetId']);

# get php exec path
if(!$phpPath = getPHPExecutableFromPath()) {
	die('<div class="alert alert-danger">Cannot access php executable!</div>');
}
# set script
$script = dirname(__FILE__) . '/../../../functions/scan/scanIPAddressesScript.php';

# invoke CLI with threading support
$cmd = "$phpPath $script 'discovery' '".transform2long($subnet['subnet'])."/$subnet[mask]' '$_POST[subnetId]'";

# save result to $output
exec($cmd, $output, $retval);

# die of error
if($retval != 0) {
	print "<div class='alert alert-danger'>Error executing scan! Error code - $retval</div>";
	
	if($_POST['debug']==1) {
	print "<pre>";
	print_r($output);
	print "</pre>";		
	}
	
	die();
}
		
# format result - alive
$result = json_decode(trim($output[0]), true);
$alive = @$result['alive'];
$dead  = @$result['dead'];
$serr  = @$result['error'];
$error = @$result['errors'];

#verify that pign path is correct
if(!file_exists($pathPing)) { $pingError = true; }

?>


<h5><?php print _('Scan results');?>:</h5>
<hr>

<?php
# error?
if(isset($error)) {
	print "<div class='alert alert-danger'><strong>"._("Error").": </strong>$error</div>";
}
# wrong ping path
elseif($pingError) {
	print '<div class="alert alert-danger">'._("Invalid ping path")."<hr>". _("You can set parameters for scan under functions/scan/config-scan.php").'</div>';
}
# empty
elseif(sizeof($alive)==0) {
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
	print "	<th>"._("Description")."</th>";
	print "	<th>"._("Hostname")."</th>";
	print "	<th></th>";
	print "</tr>";
	
	// alive
	$m=0;
	foreach($alive as $ip) {
	
		//resolve?
		if($scanDNSresolve) {
			$dns = gethostbyaddr ( transform2long($ip) );
		}
		else {
			$dns = "test";
		}
		
		print "<tr class='result$m'>";
		//ip
		print "<td>".transform2long($ip)."</td>";
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


# debug?
if($_POST['debug']==1) {
	print "<hr>";
	print "<pre>";
	print_r($result);
	print "</pre>";
}
?>