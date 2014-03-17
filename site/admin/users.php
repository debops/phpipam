<?php

/**
 * Script to edit / add / delete users
 *************************************************/


/* verify that user is admin */
checkAdmin();

/**
 * First print table of existing users with edit / delete links!
 */
$users = getAllUsers();

/* get all settings */
$settings = getallSettings();

/* get custom fields */
$custom = getCustomFields('users');

/* get languages */
$langs = getLanguages ();
?>

<!-- display existing users -->
<h4><?php print _('User management'); ?></h4>
<hr><br>

<!-- Add new -->
<button class='btn btn-sm btn-default editUser' style="margin-bottom:10px;" data-action='add'><i class='fa fa-plus'></i> <?php print _('Create user'); ?></button>


<!-- table -->
<table id="userPrint" class="table table-striped table-top table-auto">

<!-- Headers -->
<tr>
	<th></th>
    <th><?php print _('Real Name'); ?></th>
    <th><?php print _('Username'); ?></th>
    <th><?php print _('E-mail'); ?></th>
    <th><?php print _('Role'); ?></th>
    <th><?php print _('Language'); ?></th>
    <th><?php print _('Type'); ?></th>
    <th><?php print _('Groups'); ?></th>
	<?php
	if(sizeof($custom) > 0) {
		foreach($custom as $field) {
			print "<th>$field[name]</th>";
		}
	}
	?>
    <th></th>
</tr>

<?php
/* print existing sections */
foreach ($users as $user)
{
	print '<tr>' . "\n";
	
	# set icon based on normal user or admin
	if($user['role'] == "Administrator") 	{ print '	<td><img src="css/images/userVader.png" rel="tooltip" title="'._('Administrator').'"></td>'. "\n"; }
	else 									{ print '	<td><img src="css/images/userTrooper.png" rel="tooltip" title="'. _($user['role']) .'"></td>'. "\n";	}
	
	print '	<td>' . $user['real_name'] . '</td>'. "\n";
	print '	<td>' . $user['username']  . '</td>'. "\n";
	print '	<td>' . $user['email']     . '</td>'. "\n";
	print '	<td>' . $user['role']      . '</td>'. "\n";
	
	# language
	if(strlen($user['lang'])>0) {
		# get lang name
		$lname = getLangById($user['lang']);
		print "<td>$lname[l_name]</td>";
	}
	else {
		print "<td>English (default)</td>";
	}
	
	# local or ldap?
	if($user['domainUser'] == "0") 			{ print '	<td>'._('Local user').'</td>'. "\n"; }
	else 									{
		if($settings['domainAuth'] == "2") 	{ print '	<td>'._('LDAP user').'</td>'. "\n"; }
		else 								{ print '	<td>'._('Domain user').'</td>'. "\n"; }
	}

	# groups
	if($user['role'] == "Administrator") {
	print '	<td>'._('All groups').'</td>'. "\n";		
	}
	else {
		$groups = json_decode($user['groups'], true);
		$gr = parseUserGroups($groups);
	
		print '	<td>';
		if(sizeof($gr)>0) {
			foreach($gr as $group) {
				print $group['g_name']."<br>";
			}
		}
		print '	</td>'. "\n";
	}

	# custom
	if(sizeof($custom) > 0) {
		foreach($custom as $field) {
			print "<td>";

			//booleans
			if($field['type']=="tinyint(1)")	{
				if($user[$field['name']] == "0")		{ print _("No"); }
				elseif($user[$field['name']] == "1")	{ print _("Yes"); }
			} 
			//text
			elseif($field['type']=="text") {
				if(strlen($user[$field['name']])>0)		{ print "<i class='fa fa-gray fa-comment' rel='tooltip' data-container='body' data-html='true' title='".str_replace("\n", "<br>", $user[$field['name']])."'>"; }
				else									{ print ""; }
			}
			else {
				print $user[$field['name']];
				
			}
			
			print "</td>";

		}
	}
	
	# edit, delete
	print "	<td class='actions'>";
	print "	<div class='btn-group'>";
	print "		<button class='btn btn-xs btn-default editUser' data-userid='$user[id]' data-action='edit'  ><i class='fa fa-pencil'></i></button>";
	print "		<button class='btn btn-xs btn-default editUser' data-userid='$user[id]' data-action='delete'><i class='fa fa-times'></i></button>";
	print "	</div>";
	print "	</td>";
	
	print '</tr>' . "\n";
}

?>

</table>

<div class="alert alert-info alert-absolute">
<ul>
	<li><?php print _('Adminstrator users will be able to view and edit all all sections and subnets'); ?></li>
	<li><?php print _('Normal users will have premissions set based on group access to sections and subnets'); ?></li>
</ul>
</div>