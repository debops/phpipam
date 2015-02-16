<?php

/* check if admin */
if(checkAdmin(false))	{ $admin = true; }

/* get all VLANs and subnet descriptions */
$vlans = getAllVlans (true);

/* get custom fields */
$custom = getCustomFields('vlans');

/* check customfields */
$ffields = json_decode($settings['hiddenCustomFields'], true);		
if(is_array($ffields['vlans']))	{ $ffields = $ffields['vlans']; }
else							{ $ffields = array(); }

//size of custom fields
$csize = sizeof($custom) - sizeof($ffields);

# title
print "<h4>"._('Available VLANs:')."</h4>";
print "<hr>";

if($admin) {
	print "<a class='btn btn-sm btn-default' href='".create_link("administration","manageVLANs")."' data-action='add'  data-switchid='' style='margin-bottom:10px;'><i class='fa fa-pencil'></i> ". _('Manage')."</a>";
}

# table
print "<table id='vlans' class='table slaves table-striped table-condensed table-top'>";

/* headers */
print '<tr">' . "\n";
print ' <th>'._('Number').'</th>' . "\n";
print ' <th>'._('Name').'</th>' . "\n";
print ' <th>'._('Description').'</th>' . "\n";
print ' <th>'._('Belonging subnets').'</th>' . "\n";
print ' <th>'._('Section').'</th>' . "\n";
if(sizeof($custom) > 0) {
	foreach($custom as $field) {
		if(!in_array($field['name'], $ffields)) {
			print "	<th class='hidden-xs hidden-sm hidden-md'>$field[name]</th>";
		}
	}
}
print '</tr>' . "\n";

$m = 0;
foreach ($vlans as $vlan) {

	# show free vlans - start
	if($settings['hideFreeRange']!="1") {
		if($m==0 && $vlan['number']!=1)	{
			print "<tr class='success'>";
			print "<td></td>";
			print "<td colspan='".(4+$csize)."'><btn class='btn btn-xs btn-default editVLAN' data-action='add' data-number='1'><i class='fa fa-plus'></i></btn> "._('VLAN')."1 - ".($vlan['number']-1)." (".($vlan['number']-1)." "._('free').")</td>";
			print "</tr>";
		}
		# show free vlans - before vlan
		if($m>0)	{
			if( (($vlans[$m]['number'])-($vlans[$m-1]['number'])-1) > 0 ) {
			print "<tr class='success'>";
			print "<td></td>";
			# only 1?
			if( (($vlans[$m]['number'])-($vlans[$m-1]['number'])-1) ==1 ) {
			print "<td colspan='".(4+$csize)."'><btn class='btn btn-xs btn-default editVLAN' data-action='add' data-number='".($vlan['number']-1)."'><i class='fa fa-plus'></i></btn> "._('VLAN')." ".($vlan['number']-1)." (".(($vlans[$m]['number'])-($vlans[$m-1]['number'])-1)." "._('free').")</td>";
			} else {
			print "<td colspan='".(4+$csize)."'><btn class='btn btn-xs btn-default editVLAN' data-action='add' data-number='".($vlans[$m-1]['number']+1)."'><i class='fa fa-plus'></i></btn> "._('VLAN')." ".($vlans[$m-1]['number']+1)." - ".($vlan['number']-1)." (".(($vlans[$m]['number'])-($vlans[$m-1]['number'])-1)." "._('free').")</td>";				
			}
			print "</tr>";
			}
		}
	}
		
	# new change detection
	if($m>0) {
		if($vlans[$m]['number']==$vlans[$m-1]['number'] &&  $vlans[$m]['name']==$vlans[$m-1]['name'] && $vlans[$m]['description']==$vlans[$m-1]['description'])	{ $change = 'nochange'; }
		else																																					{ $change = 'change'; }
	}
	# first
	else 																																						{ $change = 'change';	 }

	/* get section details */
	$section = getSectionDetailsById($vlan['sectionId']);

	/* check if it is master */
	if(!isset($vlan['masterSubnetId'])) {
																				{ $masterSubnet = true;}
	}
	else {
		if( ($vlan['masterSubnetId'] == 0) || (empty($vlan['masterSubnetId'])) ) { $masterSubnet = true;}
		else 																	 { $masterSubnet = false;}	
	}
	# check permission
	$permission = checkSubnetPermission ($vlan['subnetId']);
		
	if($permission != "0") {
		
		print "<tr class='$change'>";

		/* print first 3 only if change happened! */
		if($change == "change") {
			print ' <td><a href="'.create_link("tools","vlan",$vlan['vlanId']).'">'. $vlan['number'].'</a></td>' . "\n";
			print ' <td>'. $vlan['name']           .'</td>' . "\n";
			print ' <td>'. $vlan['description'] .'</td>' . "\n";			
		}
		else {
			print '<td></td>';
			print '<td></td>';
			print '<td></td>';	
		}

		if ($vlan['subnetId'] != null) {
			# subnet
			print " <td><a href='".create_link("subnets",$section['id'],$vlan['subnetId'])."'>". transform2long($vlan['subnet']) ."/$vlan[mask]</a></td>";
			# section
			print " <td><a href='".create_link("subnets",$section['id'])."'>$section[name]</a></td>";
        }
        else {
        	print '<td>---</td>'. "\n";
        	print '<td>---</td>'. "\n";
        }
    
        # custom
        if(sizeof($custom) > 0) {
	   		foreach($custom as $field) {
		   		# hidden
		   		if(!in_array($field['name'], $ffields)) {

					print "<td class='hidden-xs hidden-sm hidden-md'>";
				
					//booleans
					if($field['type']=="tinyint(1)")	{
						if($vlan[$field['name']] == "0")		{ print _("No"); }
						elseif($vlan[$field['name']] == "1")	{ print _("Yes"); }
					} 
					//text
					elseif($field['type']=="text") {
						if(strlen($vlan[$field['name']])>0)		{ print "<i class='fa fa-gray fa-comment' rel='tooltip' data-container='body' data-html='true' title='".str_replace("\n", "<br>", $vlan[$field['name']])."'>"; }
						else									{ print ""; }
					}
					else {
						print $vlan[$field['name']];
						
					}
					print "</td>"; 
				}
	    	}
	    }    
	    print '</tr>' . "\n";
	}

	# show free vlans - last
	if($settings['hideFreeRange']!="1") {
	if($m==(sizeof($vlans)-1))	{
		if($settings['vlanMax']>$vlan['number'])
		print "<tr class='success'>";
		print "<td></td>";
		print "<td colspan='".(4+$csize)."'><btn class='btn btn-xs btn-default editVLAN' data-action='add' data-number='".($vlan['number']+1)."'><i class='fa fa-plus'></i></btn> "._('VLAN')." ".($vlan['number']+1)." - ".$settings['vlanMax']." (".(($settings['vlanMax'])-($vlan['number']))." "._('free').")</td>";
		print "</tr>";
	}
	}

	# next VLAN
	$m++;
}


print '</table>';
?>
