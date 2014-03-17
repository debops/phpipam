<?php

/**
 *	Print all available VLANs and configurations
 ************************************************/

/* verify that user is admin */
checkAdmin();

/* get all available VLANSs */
$vlans = getAllVlans ();

/* get custom fields */
$custom = getCustomFields('vlans');
?>

<h4><?php print _('Manage VLANs'); ?></h4>
<hr><br>

<!-- add new -->
<button class="btn btn-sm btn-default editVLAN" data-action="add" data-vlanid="" style="margin-bottom:10px;"><i class="fa fa-plus"></i> <?php print _('Add VLAN'); ?></button>

<?php
/* first check if they exist! */
if(!$vlans) {
	print '	<div class="alert alert-info alert-absolute">'._('No VLANs configured').'!</div>'. "\n";
}
else {
?>

<table id="vlanManagement" class="table table-striped table-top table-auto">
	<!-- headers -->
	<tr>
		<th><?php print _('Name'); ?></th>	
		<th><?php print _('Number'); ?></th>
		<th><?php print _('Description'); ?></th>
		<?php
		if(sizeof($custom) > 0) {
			foreach($custom as $field) {
				print "<th class='customField hidden-xs hidden-sm'>$field[name]</th>";
			}
		}
		?>
		<th></th>
	</tr>

	<!-- VLANs -->
	<?php
	foreach ($vlans as $vlan) {
	
	//print details
	print '<tr>'. "\n";
	
	print '	<td class="name">'. $vlan['name'] .'</td>'. "\n";
	print '	<td class="number">'. $vlan['number'] .'</td>'. "\n";
	print '	<td class="description">'. $vlan['description'] .'</td>'. "\n";	
	
	if(sizeof($custom) > 0) {
		foreach($custom as $field) {

			print "<td class='customField hidden-xs hidden-sm'>";
					
			//booleans
			if($field['type']=="tinyint(1)")	{
				if($vlan[$field['name']] == "0")		{ print _("No"); }
				elseif($vlan[$field['name']] == "1")	{ print _("Yes"); }
			} 
			//text
			elseif($field['type']=="text") {
				if(strlen($vlan[$field['name']])>0)	{ print "<i class='fa fa-gray fa-comment' rel='tooltip' data-container='body' data-html='true' title='".str_replace("\n", "<br>", $vlan[$field['name']])."'>"; }
				else											{ print ""; }
			}
			else {
				print $vlan[$field['name']];
				
			}
			print "</td>"; 

		}
	}
	
	print "	<td class='actions'>";
	print "	<div class='btn-group'>";
	print "		<button class='btn btn-xs btn-default editVLAN' data-action='edit'   data-vlanid='$vlan[vlanId]'><i class='fa fa-pencil'></i></button>";
	print "		<button class='btn btn-xs btn-default editVLAN' data-action='delete' data-vlanid='$vlan[vlanId]'><i class='fa fa-times'></i></button>";
	print "	</div>";
	print "	</td>";	
	print '</tr>'. "\n";
	
	}
}
?>
</table>