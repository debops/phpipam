<?php

/**
 * Script tomanage custom IP fields
 ****************************************/

/* verify that user is admin */
checkAdmin();

/* custom IP addresses */
$cFields['ipaddresses'] 			= getCustomFields('ipaddresses');
$cFieldsNum['ipaddresses']			= getCustomFieldsNumArr('ipaddresses');
$cFields['ipaddresses']['title'] 	= "Custom IP address fields";
$cFields['ipaddresses']['tooltip'] 	= "Add new custom IP address field";

/* custom subnet */
$cFields['subnets'] 				= getCustomFields('subnets');
$cFieldsNum['subnets']				= getCustomFieldsNumArr('subnets');
$cFields['subnets']['title'] 		= "Custom subnet fields";
$cFields['subnets']['tooltip'] 		= "Add new custom subnet field";

/* custom vlan */
$cFields['vlans'] 					= getCustomFields('vlans');
$cFieldsNum['vlans']				= getCustomFieldsNumArr('vlans');
$cFields['vlans']['title'] 			= "Custom VLAN fields";
$cFields['vlans']['tooltip'] 		= "Add new custom VLAN field";

/* custom users */
$cFields['users'] 					= getCustomFields('users');
$cFieldsNum['users']				= getCustomFieldsNumArr('users');
$cFields['users']['title'] 			= "Custom User fields";
$cFields['users']['tooltip'] 		= "Add new custom User field";

/* custom devices */
$cFields['devices'] 				= getCustomFields('devices');
$cFieldsNum['devices']				= getCustomFieldsNumArr('devices');
$cFields['devices']['title'] 		= "Custom device fields";
$cFields['devices']['tooltip'] 		= "Add new custom device field";


?>


<h4><?php print _('Custom fields'); ?></h4>
<hr>

<div class="alert alert-info alert-absolute"><?php print _('You can add additional custom fields to IP addresses and subnets (like CustomerId, location, ...)'); ?>.</div>
<hr style="margin-top:50px;clear:both;">


<table class="customIP table table-striped table-auto table-top" style="min-width:400px;">

<tr>
	<td></td>
	<td><?php print _('Title'); ?></td>
	<td><?php print _('Description'); ?></td>
	<td><?php print _('Field type'); ?></td>
	<td><?php print _('Default'); ?></td>
	<td><?php print _('Required'); ?></td>
	<td></td>
</tr>


	<?php
	# printout each
	foreach($cFields as $k=>$cf) {
	
		# save vars and unset
		$title   = $cf['title'];
		$tooltip = $cf['tooltip'];

		unset($cf['title']);
		unset($cf['tooltip']);
		
		# set key 
		$table = $k;

	
		print "<tbody id='custom-$k'>";
	
		//title
		print "	<tr>";
		print "	<th colspan='8'>";
		print "		<h5>"._($title)."</h5>";
		print "	</th>";
		print "	</tr>";
		
		//empty
		if(sizeof($cf) == 0) {
		print "	<tr>";
		print "	<td colspan='8'>";
		print "		<div class='alert alert-info alert-nomargin'>"._('No custom fields created yet')."</div>";
		print "	</td>";
		print "	</tr>";
		}
		//content
		else {
			$size = sizeof($cf);		//we must remove title
			$m=0;
						
			foreach($cf as $f)
			{
				print "<tr>";
	
				# ordering
				if (( ($m+1) != $size) ) 	{ print "<td style='width:10px;'><button class='btn btn-xs btn-default down' data-direction='down' data-table='$table' rel='tooltip' title='Move down' data-fieldname='".$cFieldsNum[$table][$m]."' data-nextfieldname='".$cFieldsNum[$table][$m+1]."'><i class='fa fa-chevron-down'></i></button></td>";	}
				else 						{ print "<td style='width:10px;'></td>";}
		
				print "<td class='name'>$f[name]</td>";
	
				# description
				print "<td>$f[Comment]</td>";
				
				# type
				print "<td>$f[type]</td>";

				# default
				print "<td>$f[Default]</td>";
				
				
				# NULL
				if(@$f['Null']=="NO")		{ print "<td>"._('Required')."</td>"; }
				else						{ print "<td></td>"; }
	
				#actions
				print "<td class='actions'>";
				print "	<div class='btn-group'>";
				print "		<button class='btn btn-xs btn-default edit-custom-field' data-action='edit'   data-fieldname='$f[name]' data-table='$table'><i class='fa fa-pencil'></i></button>";
				print "		<button class='btn btn-xs btn-default edit-custom-field' data-action='delete' data-fieldname='$f[name]' data-table='$table'><i class='fa fa-times'></i></button>";
				print "	</div>";
		
				# warning for older versions
				if((is_numeric(substr($f['name'], 0, 1))) || (!preg_match('!^[\w_ ]*$!', $f['name'])) ) { print '<span class="alert alert-warning"><strong>Warning</strong>: '._('Invalid field name').'!</span>'; }
		
				print "</td>";
				print "</tr>";
				
				$prevName = $field['name'];
				$m++;	
			}			
		}

		//add
		print "<tr>";
		print "<td colspan='8' style='padding-right:0px;'>";
		print "	<button class='btn btn-xs btn-default pull-right edit-custom-field' data-action='add'  data-fieldname='$field[name]' data-table='$table' rel='tooltip' data-placement='right' title='"._($tooltip)."'><i class='fa fa-plus'></i>";
		print "</td>";
		print "</tr>";
	
		//result
		print "<tr>";
		print "	<td colspan='8	' class='result'>";
		print "		<div class='$table-order-result'></div>";
		print "</td>";
		print "</tr>";

	
		print "</tbody>";
	}
	
	?>

</table>