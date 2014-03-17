<?php

/**
 * Script to edit / add / delete groups
 *************************************************/

/* verify that user is admin */
checkAdmin();

/**
 * First print table of existing groups with edit / delete links!
 */
$groups = getAllGroups();

/* get all settings */
$settings = getallSettings();
?>

<!-- display existing groups -->
<h4><?php print _('Group management'); ?></h4>
<hr><br>

<!-- Add new -->
<button class='btn btn-sm btn-default editGroup' style="margin-bottom:10px;" data-action='add'><i class='fa fa-plus'></i> <?php print _('Create group'); ?></button>


<!-- table -->
<table id="userPrint" class="table table-striped table-top table-auto">

<!-- Headers -->
<tr>
    <th><?php print _('Group name'); ?></th>
    <th><?php print _('Group description'); ?></th>
    <th><?php print _('Belonging users'); ?></th>
    <th><?php print _('Section permissions'); ?></th>
    <th colspan="2"></th>
</tr>

<!-- admins -->
<tr>
	<td><?php print _('Administrators'); ?></td>
	<td><?php print _('Administrator level users'); ?></td>
	<td>
	<?php
	$admins = getAllAdminUsers();
	foreach($admins as $a) {
		print "$a[real_name]<br>";
	}
	?>
	</td>
	<td><?php print _('All sections : Read / Write'); ?></td>
	<td colspan="2"></td>
</tr>

<?php
/* print existing sections */
if($groups) {
	foreach ($groups as $g)
	{
		print '<tr>' . "\n";
		
		print '	<td>' . $g['g_name'] . '</td>'. "\n";
		print '	<td>' . $g['g_desc'] . '</td>'. "\n";
		
		# users in group
		print "	<td>";
		$u = getUsersInGroup($g['g_id']);
		if(sizeof($u)>0) {
			foreach($u as $name) {
				# get details
				$user = getUserDetailsById($name);
				print "$user[real_name]<br>";
			}
		}
		print "</td>";
	
		
		# section permissions
		print "	<td>";
		$s = getSectionPermissionsByGroup($g['g_id']);
		if(sizeof($s)>0) {
			foreach($s as $sec=>$perm) {
				# reformat permissions
				$perm = parsePermissions($perm);
				print $sec." : ".$perm."<br>";
			}
		}
		print "</td>";
		
		
		# add/remove users
		print "	<td class='actions'>";
		print "	<div class='btn-group'>";
		print "		<button class='btn btn-xs btn-default addToGroup' 		data-groupid='$g[g_id]' data-action='add'    rel='tooltip' data-container='body'  title='"._('add users to this group')."'>   	<i class='fa fa-plus'></i></button>";
		print "		<button class='btn btn-xs btn-default removeFromGroup' 	data-groupid='$g[g_id]' data-action='remove' rel='tooltip' data-container='body'  title='"._('remove users from this group')."'><i class='fa fa-minus'></i></button>";
		print "	</div>";
		print "</td>";
	
		# edit, delete	
		print "<td class='actions'>";	
		print "	<div class='btn-group'>";
		print "		<button class='btn btn-xs btn-default editGroup'  		data-groupid='$g[g_id]' data-action='edit'   rel='tooltip' data-container='body'  title='"._('edit group details')."'>	<i class='fa fa-pencil'></i></button>";
		print "		<button class='btn btn-xs btn-default editGroup'  		data-groupid='$g[g_id]' data-action='delete' rel='tooltip' data-container='body'  title='"._('remove group')."'>		<i class='fa fa-times'></i></button>";
		print "	</div>";
		print "</td>";
		
		print '</tr>' . "\n";
	}
}

?>

</table>