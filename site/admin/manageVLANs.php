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

/* check customfields */
$ffields = json_decode($settings['hiddenCustomFields'], true);		
if(is_array($ffields['vlans']))	{ $ffields = $ffields['vlans']; }
else							{ $ffields = array(); }

//size of custom fields
$csize = sizeof($custom) - sizeof($ffields);

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

<table id="vlanManagement" class="table slaves table-striped table-top">
	<!-- headers -->
	<tr>
		<th><?php print _('Name'); ?></th>	
		<th><?php print _('Number'); ?></th>
		<th><?php print _('Description'); ?></th>
		<?php
		if(sizeof($custom) > 0) {
			foreach($custom as $field) {
				if(!in_array($field['name'], $ffields)) {
					print "<th class='customField hidden-xs hidden-sm'>$field[name]</th>";
				}
			}
		}
		?>
		<th></th>
	</tr>

	<!-- VLANs -->
	<?php
	$m=0;
	foreach ($vlans as $vlan) {

		# show free vlans - start
		if($settings['hideFreeRange']!="1") {
		if($m==0 && $vlan['number']!=1)	{
			print "<tr class='success'>";
			print "<td></td>";
			print "<td colspan='".(3+$csize)."'><btn class='btn btn-xs btn-default editVLAN' data-action='add' data-number='1'><i class='fa fa-plus'></i></btn> "._('VLAN')."1 - ".($vlan['number']-1)." (".($vlan['number']-1)." "._('free').")</td>";
			print "</tr>";
		}
		# show free vlans - before vlan
		if($m>0)	{
			if( (($vlans[$m]['number'])-($vlans[$m-1]['number'])-1) > 0 ) {
			print "<tr class='success'>";
			print "<td></td>";
			# only 1?
			if( (($vlans[$m]['number'])-($vlans[$m-1]['number'])-1) ==1 ) {
			print "<td colspan='".(3+$csize)."'><btn class='btn btn-xs btn-default editVLAN' data-action='add' data-number='".($vlan['number']-1)."'><i class='fa fa-plus'></i></btn> "._('VLAN')." ".($vlan['number']-1)." (".(($vlans[$m]['number'])-($vlans[$m-1]['number'])-1)." "._('free').")</td>";
			} else {
			print "<td colspan='".(3+$csize)."'><btn class='btn btn-xs btn-default editVLAN' data-action='add' data-number='".($vlans[$m-1]['number']+1)."'><i class='fa fa-plus'></i></btn> "._('VLAN')." ".($vlans[$m-1]['number']+1)." - ".($vlan['number']-1)." (".(($vlans[$m]['number'])-($vlans[$m-1]['number'])-1)." "._('free').")</td>";				
			}
			print "</tr>";
		}
		}
		}
	
		//print details
		print '<tr>'. "\n";
		
		print '	<td class="name">'. $vlan['name'] .'</td>'. "\n";
		print '	<td class="number"><a href="'.create_link("tools","vlan",$vlan['vlanId']).'">'. $vlan['number'] .'</a></td>'. "\n";
		print '	<td class="description">'. $vlan['description'] .'</td>'. "\n";	
		
		if(sizeof($custom) > 0) {
			foreach($custom as $field) {
				if(!in_array($field['name'], $ffields)) {
	
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
		}
		
		print "	<td class='actions'>";
		print "	<div class='btn-group'>";
		print "		<button class='btn btn-xs btn-default editVLAN' data-action='edit'   data-vlanid='$vlan[vlanId]'><i class='fa fa-pencil'></i></button>";
		print "		<button class='btn btn-xs btn-default editVLAN' data-action='delete' data-vlanid='$vlan[vlanId]'><i class='fa fa-times'></i></button>";
		print "	</div>";
		print "	</td>";	
		print '</tr>'. "\n";

		# show free vlans - last
		if($settings['hideFreeRange']!="1") {
		if($m==(sizeof($vlans)-1))	{
			if($settings['vlanMax']>$vlan['number'])
			print "<tr class='success'>";
			print "<td></td>";
			print "<td colspan='".(3+$csize)."'><btn class='btn btn-xs btn-default editVLAN' data-action='add' data-number='".($vlan['number']+1)."'><i class='fa fa-plus'></i></btn> "._('VLAN')." ".($vlan['number']+1)." - ".$settings['vlanMax']." (".(($settings['vlanMax'])-($vlan['number']))." "._('free').")</td>";
			print "</tr>";
		}
		}
	
	$m++;
	
	}
}
?>
</table>