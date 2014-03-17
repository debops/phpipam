<?php
/*
 * Print Admin menu pn left if user is admin
 *************************************************/

/* verify that user is admin */
checkAdmin();

/* get all site settings */
$settings = getAllSettings();
?>


<div class="panel panel-default adminMenu">

	<div class="panel-heading">
		<h3 class="panel-title"><?php print _('Server management'); ?></h3>
	</div>

	<ul class="list-group">
		<li class="list-group-item <?php if($_REQUEST['adminId'] == "settings") print "active"; ?>">
			<a href="administration/settings/"><i class="fa fa-angle-right pull-right"></i> <?php print _('IPAM settings'); ?></a>
		</li>
		<li class="list-group-item <?php if($_REQUEST['adminId'] == "users") print "active"; ?>">
			<a href="administration/users/"><i class="fa fa-angle-right pull-right"></i> <?php print _('User management'); ?></a>
		</li>
		<li class="list-group-item <?php if($_REQUEST['adminId'] == "groups") print "active"; ?>">
			<a href="administration/groups/"><i class="fa fa-angle-right pull-right"></i> <?php print _('Group management'); ?></a>
		</li>
	    <?php # show AD conection settings if enabled in config!
	    if($settings['domainAuth'] == 1) { ?>
		<li class="list-group-item <?php if($_REQUEST['adminId'] == "manageAD") print "active"; ?>">
			<a href="administration/manageAD/"><i class="fa fa-angle-right pull-right"></i> <?php print _('AD connection settings'); ?></a>
		</li>
		<?php } ?>
	    <?php # show OpenLDAP connection settings if enabled in config!
	    if($settings['domainAuth'] == 2) { ?>
		<li class="list-group-item <?php if($_REQUEST['adminId'] == "manageAD") print "active"; ?>">
			<a href="administration/manageAD/"><i class="fa fa-angle-right pull-right"></i> <?php print _('OpenLDAP connection settings'); ?></a>
		</li>
		<?php } ?>
		<li class="list-group-item <?php if($_REQUEST['adminId'] == "mailSettings") print "active"; ?>">
			<a href="administration/mailSettings/"><i class="fa fa-angle-right pull-right"></i> <?php print _('Mail settings'); ?></a>
		</li>
	    <?php # show API settings if enabled in config!
	    if($settings['api'] == 1) { ?>
		<li class="list-group-item <?php if($_REQUEST['adminId'] == "api") print "active"; ?>">
			<a href="administration/api/"><i class="fa fa-angle-right pull-right"></i> <?php print _('API management'); ?></a>
		</li>
		<?php } ?>
		<li class="list-group-item <?php if($_REQUEST['adminId'] == "languages") print "active"; ?>">
			<a href="administration/languages/"><i class="fa fa-angle-right pull-right"></i> <?php print _('Languages'); ?></a>
		</li>	
		<li class="list-group-item <?php if($_REQUEST['adminId'] == "widgets") print "active"; ?>">
			<a href="administration/widgets/"><i class="fa fa-angle-right pull-right"></i> <?php print _('Widgets'); ?></a>
		</li>	
	
		<li class="list-group-item <?php if($_REQUEST['adminId'] == "instructions") print "active"; ?>">
			<a href="administration/instructions/"><i class="fa fa-angle-right pull-right"></i> <?php print _('Edit instructions'); ?></a>
		</li>	
		<li class="list-group-item <?php if($_REQUEST['adminId'] == "logs") print "active"; ?>">
			<a href="administration/logs/"><i class="fa fa-angle-right pull-right"></i> <?php print _('Log files'); ?></a>
		</li>	
	</ul>
</div>

<div class="panel panel-default adminMenu">

	<div class="panel-heading">
		<h3 class="panel-title"><?php print _('IP related management'); ?></h3>
	</div>

	<ul class="list-group">
		<li class="list-group-item <?php if($_REQUEST['adminId'] == "manageSection") print "active"; ?>">
			<a href="administration/manageSection/"><i class="fa fa-angle-right pull-right"></i> <?php print _('Section management'); ?></a>
		</li>
		<li class="list-group-item <?php if($_REQUEST['adminId'] == "manageSubnet") print "active"; ?>">
			<a href="administration/manageSubnet/"><i class="fa fa-angle-right pull-right"></i> <?php print _('Subnet management'); ?></a>
		</li>
		<li class="list-group-item <?php if($_REQUEST['adminId'] == "manageDevices") print "active"; ?>">
			<a href="administration/manageDevices/"><i class="fa fa-angle-right pull-right"></i> <?php print _('Device management'); ?></a>
		</li>
		<li class="list-group-item <?php if($_REQUEST['adminId'] == "manageVLANs") print "active"; ?>">
			<a href="administration/manageVLANs/"><i class="fa fa-angle-right pull-right"></i> <?php print _('VLAN management'); ?></a>
		</li>
	    <?php # show IP request link if enabled in config file!
	    if($settings['enableVRF'] == 1) {  ?>
	    <li class="list-group-item <?php if($_REQUEST['adminId'] == "manageVRF") print "active"; ?>">
			<a href="administration/manageVRF/"><i class="fa fa-angle-right pull-right"></i> <?php print _('VRF management'); ?></a>
		</li>    
	    <?php } ?>
	    <li class="list-group-item <?php if($_REQUEST['adminId'] == "ripeImport") print "active"; ?>">
			<a href="administration/ripeImport/"><i class="fa fa-angle-right pull-right"></i> <?php print _('RIPE import'); ?></a>
		</li>    
	    <?php # show IP request link if enabled in config file!  */
	    if($settings['enableIPrequests'] == 1) { ?>
	    <li class="list-group-item <?php if($_REQUEST['adminId'] == "manageRequests") print "active"; ?>">
			<a href="administration/manageRequests/"><i class="fa fa-angle-right pull-right"></i> <?php print _('IP requests'); ?> <?php if(($requestNum = countRequestedIPaddresses()) != 0) { print "<span class='ipreqMenu'>$requestNum</span>";} ?></a>
		</li> 
	    <?php } ?>
	    <li class="list-group-item <?php if($_REQUEST['adminId'] == "filterIPFields") print "active"; ?>">
			<a href="administration/filterIPFields/"><i class="fa fa-angle-right pull-right"></i> <?php print _('Filter IP fields'); ?></a>
		</li> 
	    <li class="list-group-item <?php if($_REQUEST['adminId'] == "customFields") print "active"; ?>">
			<a href="administration/customFields/"><i class="fa fa-angle-right pull-right"></i> <?php print _('Custom fields'); ?></a>
		</li> 
	</ul>
</div>


<div class="panel panel-default adminMenu">

	<div class="panel-heading">
		<h3 class="panel-title"><?php print _('IP related management'); ?></h3>
	</div>

	<ul class="list-group">
		<li class="list-group-item <?php if($_REQUEST['adminId'] == "versionCheck") print "active"; ?>">
			<a href="administration/versionCheck/"><i class="fa fa-angle-right pull-right"></i> <?php print _('Version check'); ?></a>
		</li>
		<li class="list-group-item <?php if($_REQUEST['adminId'] == "verifyDatabase") print "active"; ?>">
			<a href="administration/verifyDatabase/"><i class="fa fa-angle-right pull-right"></i> <?php print _('Verify database'); ?></a>
		</li>
		<li class="list-group-item <?php if($_REQUEST['adminId'] == "replaceFields") print "active"; ?>">
			<a href="administration/replaceFields/"><i class="fa fa-angle-right pull-right"></i> <?php print _('Replace fields'); ?></a>
		</li>
		<li class="list-group-item <?php if($_REQUEST['adminId'] == "export") print "active"; ?>">
			<a href="administration/export/"><i class="fa fa-angle-right pull-right"></i> <?php print _('Export database'); ?></a>
		</li>
	</ul>
</div>