<?php

/**
 * Script to print mail notification form
 ********************************************/
 
/* use required functions */
require_once('../../functions/functions.php');

/* First chech referer and requested with */
CheckReferrer();

/* verify that user is authenticated! */
isUserAuthenticated (false);

/* get all site settings */
$settings = getAllSettings();

/* user details */
$userDetails = getActiveUserDetails();


/* get IP address id */
$id = $_REQUEST['id'];

/* fetch all IP address details */
$ip 	= getIpAddrDetailsById ($id);
$subnet = getSubnetDetailsById ($ip['subnetId']);

/* get VLAN details */
$subnet['VLAN'] = subnetGetVLANdetailsById($subnet['vlanId']);
$subnet['vlan'] = $subnet['VLAN']['number'];
if(!empty($subnet['VLAN']['name'])) {
	$subnet['vlan'] .= ' ('. $subnet['VLAN']['name'] .')';
}

/* set title */
$title = _('IP address details').' :: ' . $ip['ip_addr'];



/* Preset content */
$content .= '&bull; '._('IP address').': ' . "\t" . $ip['ip_addr'] . '/' . $subnet['mask']. "\n";
# desc
if(!empty($ip['description'])) {
$content .= '&bull; '._('Description').':' . "\t" . $ip['description'] . "\n";
}
# hostname
if(!empty($ip['dns_name'])) {
$content .= '&bull; '._('Hostname').':' . "\t" 	 . $ip['dns_name'] . "\n";
}
# subnet desc
if(!empty($subnet['description'])) {
$content .= '&bull; '._('Subnet desc').': ' . "\t" . $subnet['description']. "\n";
}
# VLAN
if(!empty($subnet['vlan'])) {
$content .= '&bull; '._('VLAN').': ' . "\t\t" 	 . $subnet['vlan'] . "\n";
}
# Switch
if(!empty($ip['switch'])) {
	# get device by id
	$device = getDeviceDetailsById($ip['switch']);
	$content .= "&bull; "._('Device').":\t\t"		 . $device['hostname'] . "\n";
}
# port
if(!empty($ip['port'])) {
$content .= "&bull; "._('Port').":\t"			 . $ip['port'] . "\n";
}
# custom
$myFields = getCustomFields('ipaddresses');
if(sizeof($myFields) > 0) {
	foreach($myFields as $myField) {
		if(!empty($ip[$myField['name']])) {
			$content .=  '&bull; '. $myField['name'] .":\t". $ip[$myField['name']] ."\n";
		}
	}
}


?>



<!-- header -->
<div class="pHeader"><?php print _('Send email notification'); ?></div>

<!-- content -->
<div class="pContent mailIPAddress">

	<!-- sendmail form -->
	<form name="mailNotify" id="mailNotify">
	<table id="mailNotify" class="table table-noborder table-condensed">

	<!-- recipient -->
	<tr>
		<th><?php print _('Recipients'); ?></th>
		<td>
			<input type="text" class='form-control input-sm pull-left' name="recipients" style="width:400px;margin-right:5px;">
			<i class="fa fa-info input-append" rel="tooltip" data-placement="bottom" title="<?php print _('Separate multiple recepients with ,'); ?>"></i>
		</td>
	</tr>

	<!-- title -->
	<tr>
		<th><?php print _('Title'); ?></t>
		<td>
			<input type="text" class='form-control input-sm' name="subject" style="width:400px;" value="<?php print $title; ?>">
		</td>
	</tr>
	
	<!-- content -->
	<tr>
		<th><?php print _('Content'); ?></th>
		<td style="padding-right:20px;">
			<textarea name="content" class='form-control input-sm' rows="7" style="width:100%;"><?php print $content; ?></textarea>
		</td>
	</tr>

	</table>
	</form>
</div>

<!-- footer -->
<div class="pFooter">
	<div class="btn-group">
		<button class="btn btn-sm btn-default hidePopups"><?php print _('Cancel'); ?></button>
		<button class="btn btn-sm btn-default btn-success" id="mailIPAddressSubmit"><i class="fa fa-envelope-o"></i> <?php print _('Send Mail'); ?></button>
	</div>
	
	<!-- holder for result -->
	<div class="sendmail_check"></div>
</div>