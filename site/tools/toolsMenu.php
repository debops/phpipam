<?php

/**
 * Script to display menu
 *
 */

/* verify that user is authenticated! */
isUserAuthenticated ();

/* get settings */
$settings = getAllSettings();

/* get all selected fields for IP print */
$setFieldsTemp = getSelectedIPaddrFields();
/* format them to array! */
$setFields = explode(";", $setFieldsTemp);
?>


<div class="panel panel-default toolsMenu">

	<div class="panel-heading">
		<h3 class="panel-title"><?php print _('Tools'); ?></h3>
	</div>

	<ul class="list-group">
		<li class="list-group-item <?php if($_REQUEST['toolsId'] == "ipCalc") print "active"; ?>">
			<a href="tools/ipCalc/"><i class="fa fa-angle-right pull-right icon-gray"></i> <?php print _('IP calculator'); ?></a>
		</li>
		<li class="list-group-item <?php if($_REQUEST['toolsId'] == "instructions") print "active"; ?>">
			<a href="tools/instructions/"><i class="fa fa-angle-right pull-right icon-gray"></i> <?php print _('Instructions'); ?></a>
		</li>   
	    <?php # if vrf enabled
	    if($settings['enableChangelog'] == 1) { ?> 
		<li class="list-group-item <?php if($_REQUEST['toolsId'] == "changelog") print "active"; ?>">
			<a href="tools/changelog/"><i class="fa fa-angle-right pull-right icon-gray"></i> <?php print _('Changelog'); ?></a>
		</li>
		<?php } ?> 
		<li class="list-group-item <?php if($_REQUEST['toolsId'] == "search") print "active"; ?>">
			<a href="tools/search/"><i class="fa fa-angle-right pull-right icon-gray"></i> <?php print _('Search'); ?></a>
		</li>
	</ul>
</div>


<div class="panel panel-default toolsMenu">

	<div class="panel-heading">
		<h3 class="panel-title"><?php print _('Subnets'); ?></h3>
	</div>
	
	<ul class="list-group">	    
		<li class="list-group-item <?php if($_REQUEST['toolsId'] == "favourites") print "active"; ?>">
			<a href="tools/favourites/"><i class="fa fa-angle-right pull-right icon-gray"></i> <?php print _('Favourite networks'); ?></a>
		</li> 
		<li class="list-group-item <?php if($_REQUEST['toolsId'] == "subnets") print "active"; ?>">
			<a href="tools/subnets/"><i class="fa fa-angle-right pull-right icon-gray"></i> <?php print _('Subnets'); ?></a>
		</li>  
		<li class="list-group-item <?php if($_REQUEST['toolsId'] == "vlan") print "active"; ?>">
			<a href="tools/vlan/"><i class="fa fa-angle-right pull-right icon-gray"></i> <?php print _('VLANs'); ?></a>
		</li> 
		<li class="list-group-item <?php if($_REQUEST['toolsId'] == "devices") print "active"; ?>">
			<a href="tools/devices/"><i class="fa fa-angle-right pull-right icon-gray "></i> <?php print _('Devices'); ?></a>
		</li>
	    <?php # if vrf enabled
	    if($settings['enableVRF'] == 1) { ?>
		<li class="list-group-item <?php if($_REQUEST['toolsId'] == "vrf") print "active"; ?>">
			<a href="tools/vrf/"><i class="fa fa-angle-right pull-right icon-gray"></i> <?php print _('VRF'); ?></a>
		</li>  
	</ul>  
    <?php } ?>    
</div>


<div class="panel panel-default toolsMenu">

	<div class="panel-heading">
		<h3 class="panel-title"><?php print _('User menu'); ?></h3>
	</div>
	
	<ul class="list-group">
		<li class="list-group-item <?php if($_REQUEST['toolsId'] == "userMenu") print "active"; ?>">
			<a href="tools/userMenu/"><i class="fa fa-angle-right pull-right icon-gray"></i> <?php print _('My account'); ?></a>
		</li> 
	</ul> 
</div>