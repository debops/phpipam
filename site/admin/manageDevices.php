<?php

/**
 * Script to print devices
 ***************************/

/* verify that user is admin */
if (!checkAdmin()) die('');

/* get current devices */
$devices = getAllUniqueDevices();

/* get custom fields */
$custom = getCustomFields('devices');

?>

<h4><?php print _('Device management'); ?></h4>
<hr>
<div class="btn-group">
	<button class='btn btn-sm btn-default editSwitch' data-action='add'   data-switchid='' style='margin-bottom:10px;'><i class='fa fa-plus'></i> <?php print _('Add device'); ?></button>
	<a href="administration/manageDeviceTypes/" class="btn btn-sm btn-default"><i class="fa fa-tablet"></i> <?php print _('Manage device types'); ?></a>
</div>

<?php
/* first check if they exist! */
if(sizeof($devices) == 0) {
	print '	<div class="alert alert-warn alert-absolute">'._('No devices configured').'!</div>'. "\n";
}
/* Print them out */
else {

	print '<table id="switchManagement" class="table table-striped table-auto table-top">';

	#headers
	print '<tr>';
	print '	<th>'._('Hostname').'</th>';
	print '	<th>'._('IP address').'</th>';
	print '	<th>'._('Type').'</th>';
	print '	<th>'._('Vendor').'</th>';
	print '	<th>'._('Model').'</th>';
	print '	<th>'._('SW version').'</th>';
	print '	<th>'._('Description').'</th>';
	print '	<th><i class="icon-gray icon-info-sign" rel="tooltip" title="'._('Shows in which sections device will be visible for selection').'"></i> '._('Sections').'</th>';
	if(sizeof($custom) > 0) {
		foreach($custom as $field) {
			print "<th class='hidden-xs hidden-sm hidden-md'>$field[name]</th>";
		}
	}
	print '	<th class="actions"></th>';
	print '</tr>';

	foreach ($devices as $device) {
	
	//print details
	print '<tr>'. "\n";
	
	print '	<td>'. $device['hostname'] .'</td>'. "\n";
	print '	<td>'. $device['ip_addr'] .'</td>'. "\n";
	print '	<td>'. $device['tname'] .'</td>'. "\n";
	print '	<td>'. $device['vendor'] .'</td>'. "\n";
	print '	<td>'. $device['model'] .'</td>'. "\n";
	print '	<td>'. $device['version'] .'</td>'. "\n";
	print '	<td class="description">'. $device['description'] .'</td>'. "\n";
	
	//sections
	print '	<td class="sections">';
		$temp = explode(";",$device['sections']);
		if( (sizeof($temp) > 0) && (!empty($temp[0])) ) {
		foreach($temp as $line) {
			$section = getSectionDetailsById($line);
			if(!empty($section)) {
			print '<div class="switchSections">'. $section['name'] .'</div>'. "\n";
			}
		}
		}
	
	print '	</td>'. "\n";

	//custom
	if(sizeof($custom) > 0) {
		foreach($custom as $field) {
			
			print "<td class='hidden-xs hidden-sm hidden-md'>";
		
			//booleans
			if($field['type']=="tinyint(1)")	{
				if($device[$field['name']] == "0")		{ print _("No"); }
				elseif($device[$field['name']] == "1")	{ print _("Yes"); }
			} 
			//text
			elseif($field['type']=="text") {
				if(strlen($device[$field['name']])>0)	{ print "<i class='fa fa-gray fa-comment' rel='tooltip' data-container='body' data-html='true' title='".str_replace("\n", "<br>", $device[$field['name']])."'>"; }
				else											{ print ""; }
			}
			else {
				print $device[$field['name']];
				
			}
			print "</td>"; 

		}
	}
	
	print '	<td class="actions">'. "\n";
	print "	<div class='btn-group'>";
	print "		<button class='btn btn-xs btn-default editSwitch' data-action='edit'   data-switchid='$device[id]'><i class='fa fa-pencil'></i></button>";
	print "		<button class='btn btn-xs btn-default editSwitch' data-action='delete' data-switchid='$device[id]'><i class='fa fa-times'></i></button>";
	print "	</div>";
	print '	</td>'. "\n";
	
	print '</tr>'. "\n";

	}
	print '</table>';
}

?>


<!-- edit result holder -->
<div class="switchManagementEdit"></div>
