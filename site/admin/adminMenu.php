<?php
/*
 * Print Admin menu pn left if user is admin
 *************************************************/

/* verify that user is admin */
checkAdmin(); 

/* get all site settings */
$settings = getAllSettings();

/* filter input */
$_GET = filter_user_input($_GET, true, true, false);
?>


<div class="panel panel-default adminMenu">

	<div class="panel-heading">
		<h3 class="panel-title"><?php print _('Server management'); ?></h3>
	</div>

	<ul class="list-group">
		<li class="list-group-item <?php if($_GET['section'] == "settings") print "active"; ?>">
			<a href="<?php print create_link("administration", "settings"); ?>"><i class="fa fa-angle-right pull-right"></i> <?php print _('IPAM settings'); ?></a>
		</li>
		<li class="list-group-item <?php if($_GET['section'] == "users") print "active"; ?>">
			<a href="<?php print create_link("administration", "users"); ?>"><i class="fa fa-angle-right pull-right"></i> <?php print _('User management'); ?></a>
		</li>
		<li class="list-group-item <?php if($_GET['section'] == "groups") print "active"; ?>">
			<a href="<?php print create_link("administration", "groups"); ?>"><i class="fa fa-angle-right pull-right"></i> <?php print _('Group management'); ?></a>
		</li>
	    <?php # show AD conection settings if enabled in config!
	    if($settings['domainAuth'] == 1) { ?>
		<li class="list-group-item <?php if($_GET['section'] == "manageAD") print "active"; ?>">
			<a href="<?php print create_link("administration", "manageAD"); ?>"><i class="fa fa-angle-right pull-right"></i> <?php print _('AD connection settings'); ?></a>
		</li>
		<?php } ?>
	    <?php # show OpenLDAP connection settings if enabled in config!
	    if($settings['domainAuth'] == 2) { ?>
		<li class="list-group-item <?php if($_GET['section'] == "manageAD") print "active"; ?>">
			<a href="<?php print create_link("administration", "manageAD"); ?>"><i class="fa fa-angle-right pull-right"></i> <?php print _('OpenLDAP connection settings'); ?></a>
		</li>
		<?php } ?>
		<li class="list-group-item <?php if($_GET['section'] == "mailSettings") print "active"; ?>">
			<a href="<?php print create_link("administration", "mailSettings"); ?>"><i class="fa fa-angle-right pull-right"></i> <?php print _('Mail settings'); ?></a>
		</li>
	    <?php # show API settings if enabled in config!
	    if($settings['api'] == 1) { ?>
		<li class="list-group-item <?php if($_GET['section'] == "api") print "active"; ?>">
			<a href="<?php print create_link("administration", "api"); ?>"><i class="fa fa-angle-right pull-right"></i> <?php print _('API management'); ?></a>
		</li>
		<?php } ?>
		<li class="list-group-item <?php if($_GET['section'] == "languages") print "active"; ?>">
			<a href="<?php print create_link("administration", "languages"); ?>"><i class="fa fa-angle-right pull-right"></i> <?php print _('Languages'); ?></a>
		</li>	
		<li class="list-group-item <?php if($_GET['section'] == "widgets") print "active"; ?>">
			<a href="<?php print create_link("administration", "widgets"); ?>"><i class="fa fa-angle-right pull-right"></i> <?php print _('Widgets'); ?></a>
		</li>	
	
		<li class="list-group-item <?php if($_GET['section'] == "instructions") print "active"; ?>">
			<a href="<?php print create_link("administration", "instructions"); ?>"><i class="fa fa-angle-right pull-right"></i> <?php print _('Edit instructions'); ?></a>
		</li>	
		<li class="list-group-item <?php if($_GET['section'] == "logs") print "active"; ?>">
			<a href="<?php print create_link("administration", "logs"); ?>"><i class="fa fa-angle-right pull-right"></i> <?php print _('Log files'); ?></a>
		</li>	
	</ul>
</div>

<div class="panel panel-default adminMenu">

	<div class="panel-heading">
		<h3 class="panel-title"><?php print _('IP related management'); ?></h3>
	</div>

	<ul class="list-group">
		<li class="list-group-item <?php if($_GET['section'] == "manageSection") print "active"; ?>">
			<a href="<?php print create_link("administration", "manageSection"); ?>"><i class="fa fa-angle-right pull-right"></i> <?php print _('Section management'); ?></a>
		</li>
		<li class="list-group-item <?php if($_GET['section'] == "manageSubnet") print "active"; ?>">
			<a href="<?php print create_link("administration", "manageSubnet"); ?>"><i class="fa fa-angle-right pull-right"></i> <?php print _('Subnet management'); ?></a>
		</li>
		<li class="list-group-item <?php if($_GET['section'] == "manageDevices") print "active"; ?>">
			<a href="<?php print create_link("administration", "manageDevices"); ?>"><i class="fa fa-angle-right pull-right"></i> <?php print _('Device management'); ?></a>
		</li>
		<li class="list-group-item <?php if($_GET['section'] == "manageVLANs") print "active"; ?>">
			<a href="<?php print create_link("administration", "manageVLANs"); ?>"><i class="fa fa-angle-right pull-right"></i> <?php print _('VLAN management'); ?></a>
		</li>
	    <?php # show IP request link if enabled in config file!
	    if($settings['enableVRF'] == 1) {  ?>
	    <li class="list-group-item <?php if($_GET['section'] == "manageVRF") print "active"; ?>">
			<a href="<?php print create_link("administration", "manageVRF"); ?>"><i class="fa fa-angle-right pull-right"></i> <?php print _('VRF management'); ?></a>
		</li>    
	    <?php } ?>
	    <li class="list-group-item <?php if($_GET['section'] == "ripeImport") print "active"; ?>">
			<a href="<?php print create_link("administration", "ripeImport"); ?>"><i class="fa fa-angle-right pull-right"></i> <?php print _('RIPE import'); ?></a>
		</li>    
	    <?php # show IP request link if enabled in config file!  */
	    if($settings['enableIPrequests'] == 1) { ?>
	    <li class="list-group-item <?php if($_GET['section'] == "manageRequests") print "active"; ?>">
			<a href="<?php print create_link("administration", "manageRequests"); ?>"><i class="fa fa-angle-right pull-right"></i> <?php print _('IP requests'); ?> <?php if(($requestNum = countRequestedIPaddresses()) != 0) { print "<span class='ipreqMenu'>$requestNum</span>";} ?></a>
		</li> 
	    <?php } ?>
	    <li class="list-group-item <?php if($_GET['section'] == "filterIPFields") print "active"; ?>">
			<a href="<?php print create_link("administration", "filterIPFields"); ?>"><i class="fa fa-angle-right pull-right"></i> <?php print _('Filter IP fields'); ?></a>
		</li> 
	    <li class="list-group-item <?php if($_GET['section'] == "customFields") print "active"; ?>">
			<a href="<?php print create_link("administration", "customFields"); ?>"><i class="fa fa-angle-right pull-right"></i> <?php print _('Custom fields'); ?></a>
		</li> 
	</ul>
</div>


<div class="panel panel-default adminMenu">

	<div class="panel-heading">
		<h3 class="panel-title"><?php print _('Tools'); ?></h3>
	</div>

	<ul class="list-group">
		<li class="list-group-item <?php if($_GET['section'] == "versionCheck") print "active"; ?>">
			<a href="<?php print create_link("administration", "versionCheck"); ?>"><i class="fa fa-angle-right pull-right"></i> <?php print _('Version check'); ?></a>
		</li>
		<li class="list-group-item <?php if($_GET['section'] == "verifyDatabase") print "active"; ?>">
			<a href="<?php print create_link("administration", "verifyDatabase"); ?>"><i class="fa fa-angle-right pull-right"></i> <?php print _('Verify database'); ?></a>
		</li>
		<li class="list-group-item <?php if($_GET['section'] == "replaceFields") print "active"; ?>">
			<a href="<?php print create_link("administration", "replaceFields"); ?>"><i class="fa fa-angle-right pull-right"></i> <?php print _('Replace fields'); ?></a>
		</li>
		<li class="list-group-item <?php if($_GET['section'] == "export") print "active"; ?>">
			<a href="<?php print create_link("administration", "export"); ?>"><i class="fa fa-angle-right pull-right"></i> <?php print _('Export database'); ?></a>
		</li>
	</ul>
</div>
