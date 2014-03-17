<?php

/**
 * Script to manage sections
 *************************************************/


/* verify that user is admin */
checkAdmin();

$sections = fetchSections (true);

# ets do some reordering to show slaves!
foreach($sections as $s) {
	if($s['masterSection']=="0") {
		# it is master
		$s['class'] = "master";
		$sectionssorted[] = $s;
		# check for slaves
		foreach($sections as $ss) {
			if($ss['masterSection']==$s['id']) {
				$ss['class'] = "slave";
				$sectionssorted[] = $ss;
			}
		}
	}
}

$sections = $sectionssorted;
?>

<h4><?php print _('Section management'); ?></h4>
<hr>

<!-- Add new section -->
<div class="btn-group" style='margin-bottom:20px;margin-top:10px;'>
	<button class='btn btn-sm btn-default editSection' data-action='add'><i class='fa fa-plus'></i> <?php print _('Add section'); ?></button>
	<button class='btn btn-sm btn-default sectionOrder' ><i class='fa fa-tasks'></i> <?php print _('Section order'); ?></button>
</div>

<!-- show sections -->
<?php if(sizeof($sections) > 0) { ?>
<table id="manageSection" class="table table-striped table-condensed table-top">
<!-- headers -->
<tr>
    <th><?php print _('Name'); ?></th>
    <th><?php print _('Description'); ?></th>
    <th><?php print _('Parent'); ?></th>
    <th><?php print _('Strict mode'); ?></th>
    <th><?php print _('Show VLANs'); ?></th>
    <th><?php print _('Show VRFs'); ?></th>
    <th><?php print _('Group Permissions'); ?></th>
    <th></th>
</tr>

<!-- existing sections -->
<?php
foreach ($sections as $section)
{
	print '<tr class="'.$section['class'].'">'. "\n";

    print '	<td>'. str_replace("_", " ", $section['name']).'</td>'. "\n";
    print '	<td>'. $section['description'] .'</td>'. "\n";
    
    # master Section
    if($section['masterSection']!= "0") {
		# get section details
		$ssec = getSectionDetailsById($section['masterSection']);    
	    print "	<td>$ssec[name]</td>";	    
    } else {
	    print "	<td>/</td>";	    
    }

    # strictMode
    if($section['strictMode'] == 0)	{ $mode = _("No"); }
    else							{ $mode = _("Yes"); }
  
    print '	<td>'. $mode .'</td>'. "\n";
    
    # Show VLANs
    print " <td>";
    if(@$section['showVLAN']==1) { print _("Yes"); }
    else						 { print _("No"); }
    print "	</td>";

    # Show VRFs
    print " <td>";
    if(@$section['showVRF']==1)  { print _("Yes"); }
    else						 { print _("No"); }
    print "	</td>";

    # permissions
	print "<td>";    
    if(strlen($section['permissions'])>1) {
    	$permissions = parseSectionPermissions($section['permissions']);
    	# print for each if they exist
    	if(sizeof($permissions) > 0) {
    		foreach($permissions as $key=>$p) {
	    		# get subnet name
	    		$group = getGroupById($key);
	    		# parse permissions
	    		$perm   = parsePermissions($p);
	    	
	    		print $group['g_name']." : ".$perm."<br>"; 
	    	}   
    	}
    	else {
	    	print _("All groups: No access");
    	}
    }
	print "</td>";
    
   	print '	<td class="actions">'. "\n";
   	print "	<div class='btn-group btn-group-xs'>";
	print "		<button class='btn btn-default editSection' data-action='edit'   data-sectionid='$section[id]'><i class='fa fa-pencil'></i></button>";
	print "		<a class='btn btn-default' href='administration/manageSection/sectionChangelog/$section[id]/'><i class='fa fa-clock-o'></i></a>";
	print "		<button class='btn btn-default editSection' data-action='delete' data-sectionid='$section[id]'><i class='fa fa-times'></i></button>";
	print "	</div>";
	print '	</td>'. "\n";
    
	print '</tr>'. "\n";;
}
?>

</table>	<!-- end table -->

<!-- show no configured -->
<?php } else { ?>
<div class="alert alert-warn alert-absolute"><?php print _('No sections configured'); ?>!</div>
<?php } ?>


<!-- permissions info -->
<div class="alert alert-info alert-absolute" style="margin-top:15px;">
<?php print _('Permissions info'); ?><hr>
<ul>
	<li><?php print _('If group is not set in permissions then it will not have access to subnet'); ?></li>
	<li><?php print _('Groups with RO permissions will not be able to create new subnets'); ?></li>
	<li><?php print _('Subnet permissions must be set separately. By default if group has access to section<br>it will have same permission on subnets'); ?></li>
	<li><?php print _('You can choose to delegate section permissions to all underlying subnets'); ?></li>
	<li><?php print _('If group does not have access to section it will not be able to access subnet, even if<br>subnet permissions are set'); ?></li>
</ul>
</div>