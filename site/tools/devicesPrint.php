<script type="text/javascript">
/* fix for ajax-loading tooltips */
$('body').tooltip({ selector: '[rel=tooltip]' });
</script>

<?php

/**
 * Script to display devices
 *
 */


/* verify that user is authenticated! */
if(!function_exists(isUserAuthenticated)) {
	require('../../functions/functions.php');
}
isUserAuthenticated ();

/* sorting */
if(!isset($_POST['direction'])) {
	$sort['direction'] = 'asc';
	$sort['field']	   = 'hostname';
	
	$sort['directionNext'] = "desc";
	
	$_POST['direction']  = "hostname|asc";
}
else {
	/* use required functions */
	require_once('../../functions/functions.php');
	
	/* format posted values! */
	$tmp = explode("|", $_POST['direction']);

	$sort['field'] 	   = $tmp[0];
	$sort['direction'] = $tmp[1];	

	if($sort['direction'] == "asc") { $sort['directionNext'] = "desc"; }
	else 							{ $sort['directionNext'] = "asc"; }	
}

/* filter */
if(isset($_POST['ffield'])) {
	$filter = true;
	/* get all unique devices */
	$devices = getAllUniqueDevicesFilter($_POST['ffield'], $_POST['fval'], $sort['field'], $sort['direction']);		
} 
else {
	$filter = false;
	/* get all unique devices */
	$devices = getAllUniqueDevices($sort['field'], $sort['direction']);	
}

/* get custom fields */
$custom = getCustomFields('devices');


//sort icons
if($sort['direction'] == 'asc') 	{ $icon = "<i class='fa fa-angle-down'></i> "; }
else								{ $icon = "<i class='fa fa-angle-up'></i> "; }


//filter
print "<div class='filter' style='margin-bottom:5px;text-align:right'>";
print "<form class='form-inline' id='deviceFilter'>";
	//select
	$select = array("hostname"=>"Hostname", "ip_addr"=>"IP address", "description"=>"Description", "type"=>"Type", "vendor"=>"Vendor", "model"=>"Model", "version"=>"Version");
	foreach($custom as $c) {
		$select[$c['name']] = $c['name'];
	}
	
	print "	<select class='form-control input-sm' name='ffield'>";
	foreach($select as $k=>$v) {
		if(@$_POST['ffield']==$k)	{ print "<option value='$k' selected='selected'>"._("$v")."</option>"; }
		else						{ print "<option value='$k'>"._("$v")."</option>"; }
	}
	print "	</select>";
	
	//field
	print "<input type='text' name='fval' class='input-sm form-control' value='".@$_POST['fval']."' placeholder='"._('Search string')."'>";
	print "<input type='hidden' name='direction' value='$_POST[direction]'>";
	print "<input type='submit' class='btn btn-sm btn-default' value='"._("Filter")."'>";

print "</form>";
print "</div>";


# filter notification
if($filter)
print "<div class='alert alert-warning'>Filter applied: '$_POST[ffield] like *$_POST[fval]*'</div>";


print '<table id="switchManagement" class="table table-striped table-top">';

#headers
print '<tr>';
print "	<th><a href='' data-id='hostname|$sort[directionNext]' 		class='sort' rel='tooltip' data-container='body' title='"._('Sort by hostname')."'>"; ; 	if($sort['field'] == "hostname") 	print $icon; print _('Hostname').'</a></th>';
print "	<th><a href='' data-id='ip_addr|$sort[directionNext]'  	 	class='sort' rel='tooltip' data-container='body' title='"._('Sort by IP address')."'>"; ; 	if($sort['field'] == "ip_addr") 	print $icon; print _('IP address').'</th>';
print "	<th><a href='' data-id='description|$sort[directionNext]'  	class='sort' rel='tooltip' data-container='body' title='"._('Sort by description')."'>"; ; 	if($sort['field'] == "description") print $icon; print _('Description').'</th>';
print "	<th style='color:#428bca'>"._('Number of hosts').'</th>';
print "	<th class='hidden-sm'><a href='' 		   data-id='type|$sort[directionNext]'    class='sort' rel='tooltip' data-container='body' title='"._('Sort by type')."'>"; ; 		if($sort['field'] == "type") print $icon; print _('Type').'</th>';
print "	<th class='hidden-sm hidden-xs'><a href='' data-id='vendor|$sort[directionNext]'  class='sort' rel='tooltip' data-container='body' title='"._('Sort by vendor')."'>"; ; 	if($sort['field'] == "vendor") print $icon; print _('Vendor').'</th>';
print "	<th class='hidden-sm hidden-xs'><a href='' data-id='model|$sort[directionNext]'   class='sort' rel='tooltip' data-container='body' title='"._('Sort by model')."'>"; ; 		if($sort['field'] == "model") print $icon; 	print _('Model').'</th>';
print "	<th class='hidden-sm hidden-xs'><a href='' data-id='version|$sort[directionNext]' class='sort' rel='tooltip' data-container='body' title='"._('Sort by version')."'>"; ; 	if($sort['field'] == "version") print $icon; print _('SW version').'</th>';

if(sizeof($custom) > 0) {
	foreach($custom as $field) {
		print "<th class='hidden-sm hidden-xs hidden-md'><a href='' data-id='$field[name]|$sort[directionNext]'  class='sort' rel='tooltip' data-container='body' title='"._('Sort by')." $field[name]'>"; ; 	if($sort['field'] == $field['name']) print $icon; print $field['name']."</th>";
	}
}
print '	<th class="actions"></th>';
print '</tr>';

if(sizeof($devices) == 0) {
	$colspan = 9 + sizeof($custom);
	print "<tr>";
	print "	<td colspan='$colspan'><div class='alert alert-info'>"._('No devices configured')."!</div></td>";
	print "</tr>";
} else {
	foreach ($devices as $device) {
	
	//count items
	$cnt = countIPaddressesBySwitchId($device['id']);
	
	//print details
	print '<tr>'. "\n";
	
	print "	<td>". $device['hostname'] .'</td>'. "\n";
	print "	<td>". $device['ip_addr'] .'</td>'. "\n";
	print '	<td class="description">'. $device['description'] .'</td>'. "\n";
	print '	<td><strong>'. $cnt .'</strong> '._('Hosts').'</td>'. "\n";
	print '	<td class="hidden-sm">'. $device['tname'] .'</td>'. "\n";
	print '	<td class="hidden-sm hidden-xs">'. $device['vendor'] .'</td>'. "\n";
	print '	<td class="hidden-sm hidden-xs">'. $$device['model'] .'</td>'. "\n";
	print '	<td class="hidden-sm hidden-xs">'. $device['version'] .'</td>'. "\n";
	
	//custom
	if(sizeof($custom) > 0) {
		foreach($custom as $field) {
			print "<td class='hidden-sm hidden-xs hidden-md'>".$device[$field['name']]."</td>";
		}
	}
	
	print '	<td class="actions"><a href="tools/devices/hosts/'.$device['id'].'/" class="btn btn-sm btn-default"><i class="fa fa-angle-right"></i> '._('Show all hosts').'</a></td>';	
	print '</tr>'. "\n";
	
	}
	
	# print for unspecified
	print '<tr class="unspecified">'. "\n";
	
	$cnt = countIPaddressesBySwitchId(NULL);
	
	print '	<td>'._('Device not specified').'</td>'. "\n";
	print '	<td></td>'. "\n";
	print '	<td></td>'. "\n";
	print '	<td><strong>'. $cnt .'</strong> '._('Hosts').'</td>'. "\n";
	print '	<td class="hidden-sm"></td>'. "\n";
	print '	<td class="hidden-sm hidden-xs"></td>'. "\n";
	print '	<td class="hidden-sm hidden-xs"></td>'. "\n";
	print '	<td class="hidden-sm hidden-xs"></td>'. "\n";
	
	//custom
	if(sizeof($custom) > 0) {
		foreach($custom as $field) {
			print "<td class='hidden-sm hidden-xs hidden-md'></td>";
		}
	}
	print '	<td class="actions"><a href="tools/devices/hosts/0/" class="btn btn-sm btn-default"><i class="fa fa-angle-right"></i> '._('Show all hosts').'</a></td>';		
	print '</tr>'. "\n";	
}	

print '</table>';


?>