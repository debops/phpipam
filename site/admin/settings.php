<?php

/**
 *	Site settings
 **************************/

/* verify that user is admin */
checkAdmin();

/* fetch all settings */
$settings = getAllSettings();

/* get all languages */
$langs = getLanguages ();
?>

<!-- title -->
<h4><?php print _('phpIPAM Server settings'); ?></h4>
<hr>

<form name="settings" id="settings">
<table id="settings" class="table table-hover table-condensed table-top">

<!-- site settings -->
<tr class="settings-title">
	<th colspan="3"><h4><?php print _('Site settings'); ?></h4></th>
</tr>

<!-- site title -->
<tr>
	<td><?php print _('Site title'); ?></th>
	<td>
		<input type="text" class="form-control input-sm input-w-250" name="siteTitle" value="<?php print $settings['siteTitle']; ?>">
	</td>
	<td class="info2"><?php print _('Set site title'); ?></td>
</tr>

<!-- site domain -->
<tr>
	<td><?php print _('Site domain'); ?></td>
	<td>
		<input type="text" class="form-control input-sm" name="siteDomain" value="<?php print $settings['siteDomain']; ?>">
	</td>
	<td class="info2"><?php print _('Set domain for sending mail notifications'); ?></td>
</tr>

<!-- site URL -->
<tr>
	<td class="title"><?php print _('Site URL'); ?></td>
	<td>
		<input type="text" class="form-control input-sm" name="siteURL" value="<?php print $settings['siteURL']; ?>">
	</td>
	<td class="info2"><?php print _('Set site URL'); ?></td>
</tr>
<!-- Default language -->
<tr>
	<td class="title"><?php print _('Default language'); ?></td>
	<td>
		<select name="defaultLang" class="form-control input-sm input-w-auto">
		<?php
		if(sizeof($langs)>0) {
			//default
			print "<option value='0'>Default</option>";
			foreach($langs as $lang) {
				if($lang['l_id']==$settings['defaultLang']) { print "<option value='$lang[l_id]' selected='selected'>$lang[l_name] ($lang[l_code])</option>"; }
				else										{ print "<option value='$lang[l_id]' 					>$lang[l_name] ($lang[l_code])</option>"; }
			}
		}
		?>
		</select>
	</td>
	<td class="info2"><?php print _('Select default language'); ?></td>
</tr>


<!-- Admin settings -->
<tr class="settings-title">
	<th colspan="3"><h4><?php print _('Admin settings'); ?></h4></th>
</tr>

<!-- Admin name -->
<tr>
	<td class="title"><?php print _('Admin name'); ?></td>
	<td>
		<input type="text" class="form-control input-sm" name="siteAdminName" value="<?php print $settings['siteAdminName']; ?>">
	</td>
	<td class="info2">
		<?php print _('Set administrator name'); ?>
	</td>
</tr>

<!-- Admin mail -->
<tr>
	<td class="title"><?php print _('Admin mail'); ?></td>
	<td>
		<input type="text" class="form-control input-sm" name="siteAdminMail" value="<?php print $settings['siteAdminMail']; ?>">
	</td>
	<td class="info2">
		<?php print _('Set administrator e-mail'); ?>
	</td>
</tr>



<!-- features -->
<tr class="settings-title">
	<th colspan="3"><h4><?php print _('Feature settings'); ?></h4></th>
</tr>

<!-- Domain auth -->
<tr>
	<td class="title"><?php print _('Auth type'); ?></td>
	<td>
		<select name="domainAuth" class="form-control input-sm input-w-auto">
			<option value="0" <?php if($settings['domainAuth'] == 0) print 'selected'; ?>><?php print _('Local authentication only'); ?></option>
			<option value="1" <?php if($settings['domainAuth'] == 1) print 'selected'; ?>><?php print _('AD authentication'); ?></option>
			<option value="2" <?php if($settings['domainAuth'] == 2) print 'selected'; ?>><?php print _('OpenLDAP authentication'); ?></option>
		</select>
	</td>
	<td class="info2">
		<?php print _('Set authentication type for users. Requires php LDAP support. Set connection settings in admin menu'); ?>
	</td>
</tr>

<!-- API -->
<tr>
	<td class="title"><?php print _('API'); ?></td>
	<td>
		<input type="checkbox" value="1" name="api" <?php if($settings['api'] == 1) print 'checked'; ?>>
	</td>
	<td class="info2">
		<?php print _('Enable or disable API server module'); ?>
	</td>
</tr>

<!-- IP requests -->
<tr>
	<td class="title"><?php print _('IP request module'); ?></td>
	<td>
		<input type="checkbox" value="1" name="enableIPrequests" <?php if($settings['enableIPrequests'] == 1) print 'checked'; ?>>
	</td>
	<td class="info2">
		<?php print _('Enable or disable IP request module'); ?>
	</td>
</tr>

<!-- VRF -->
<tr>
	<td class="title"><?php print _('Enable VRF support'); ?></td>
	<td>
		<input type="checkbox" value="1" name="enableVRF" <?php if($settings['enableVRF'] == 1) print 'checked'; ?>>
	</td>
	<td class="info2">
		<?php print _('Enable or disable VRF module'); ?>
	</td>
</tr>

<!-- DNS resolving -->
<tr>
	<td class="title"><?php print _('Resolve DNS names'); ?></td>
	<td>
		<input type="checkbox" value="1" name="enableDNSresolving" <?php if($settings['enableDNSresolving'] == 1) print 'checked'; ?>>
	</td>
	<td class="info2">
		<?php print _('Check reverse dns lookups for IP addresses that do not have hostname in database. (Activating this feature can significantly increase ip address pages loading time!)'); ?>
	</td>
</tr>

<!-- duplicate VLANs -->
<tr>
	<td class="title"><?php print _('Duplicate VLANs'); ?></td>
	<td>
		<input type="checkbox" value="1" name="vlanDuplicate" <?php if($settings['vlanDuplicate'] == 0) print ''; else print 'checked'; ?>>
	</td>
	<td class="info2">
		<?php print _('Allow duplicate VLAN numbers'); ?>
	</td>
</tr>

<!-- changelog -->
<tr>
	<td class="title"><?php print _('Changelog'); ?></td>
	<td>
		<input type="checkbox" value="1" name="enableChangelog" <?php if($settings['enableChangelog'] == 0) print ''; else print 'checked'; ?>>
	</td>
	<td class="info2">
		<?php print _('Enable changelog'); ?>
	</td>
</tr>

<!-- Ping status intervals -->
<tr>
	<td class="title"><?php print _('Ping status intervals'); ?></td>
	<td>
		<input type="text" class="form-control input-sm" name="pingStatus" value="<?php print $settings['pingStatus']; ?>">
	</td>
	<td class="info2">
		<?php print _('Ping status intervals for IP addresses in seconds - warning;offline (Default: 1800;3600)'); ?>
	</td>
</tr>

<!-- Ping path -->
<tr>
	<td class="title"><?php print _('Ping path'); ?></td>
	<?php
	//verify that ping file exists!
	if(!file_exists($settings['scanPingPath']))	{ $class="danger"; }
	else										{ $class=""; }
	?>
	<td class="<?php print $class; ?>">
		<input type="text" class="form-control input-sm" name="scanPingPath" value="<?php print $settings['scanPingPath']; ?>">
	</td>
	<td class="info2">
		<?php print _('Set path for ping executable file (default /bin/ping)'); ?>
	</td>
</tr>

<!-- Ping threads -->
<tr>
	<td class="title"><?php print _('Max scan threads'); ?></td>
	<td>
		<input type="text" class="form-control input-sm" name="scanMaxThreads" value="<?php print $settings['scanMaxThreads']; ?>">
	</td>
	<td class="info2">
		<?php print _('Set maximum nubmer of concurrent ICMP checks (default 128)'); ?>
	</td>
</tr>



<!-- Display -->
<tr class="settings-title">
	<th colspan="3"><h4><?php print _('Display settings'); ?></h4></th>
</tr>

<!-- DHCP compress -->
<tr>
	<td class="title"><?php print _('DHCP compress'); ?></td>
	<td>
		<input type="checkbox" value="1" name="dhcpCompress" <?php if($settings['dhcpCompress'] == 1) print 'checked'; ?>>
	</td>
	<td class="info2">
		<?php print _('Compress DHCP ranges in IP table'); ?>
	</td>
</tr>

<!-- Disable donation field -->
<tr>
	<td class="title"><?php print _('Hide donation button'); ?></td>
	<td>
		<input type="checkbox" value="1" name="donate" <?php if($settings['donate'] == 1) print 'checked'; ?>>
	</td>
	<td class="info2">
		<?php print _('Hide donation button'); ?>
	</td>
</tr>

<!-- Visual display limit -->
<tr>
	<td class="title"><?php print _('IP visual display limit'); ?></td>
	<td>
		<select name="visualLimit" class="form-control input-sm input-w-auto">
			<?php
			$opts = array(
				"0"=>_("Don't show visual display"),
				"19"=>"/19 (8190)",
				"20"=>"/20 (4094)",
				"21"=>"/21 (2046)",
				"22"=>"/22 (1024)",
				"23"=>"/23 (512)",
				"24"=>"/24 (255)"
			);
			
			foreach($opts as $key=>$line) {
				if($settings['visualLimit'] == $key) { print "<option value='$key' selected>$line</option>"; }
				else 								{ print "<option value='$key'>$line</option>"; }
			}
			
			?>
		</select>
	</td>
	<td class="info2">
		<?php print _('Select netmask limit for visual display of IP addresses (mask equal or bigger than - more then /22 not recommended)'); ?>
	</td>
</tr>


<!-- Output limit -->
<tr>
	<td class="title"><?php print _('IP address print limit'); ?></td>
	<td>
		<select name="printLimit" class="form-control input-sm input-w-auto">
			<?php
			$opts = array(
				"0"=>_("Show all"),
				"10"=>"10",
				"30"=>"30",
				"62"=>"62",
				"100"=>"100",
				"126"=>"126",
				"254"=>"254"
			);
			
			foreach($opts as $key=>$line) {
				if($settings['printLimit'] == $key) { print "<option value='$key' selected>$line</option>"; }
				else 								{ print "<option value='$key'>$line</option>"; }
			}
			
			?>
		</select>
	</td>
	<td class="info2">
		<?php print _('Number of IP addresses per page'); ?>
	</td>
</tr>

<!-- Subnet ordering -->
<tr>
	<td class="title"><?php print _('Subnet ordering'); ?></td>
	<td>
		<select name="subnetOrdering" class="form-control input-sm input-w-auto">
			<?php
			$opts = array(
				"subnet,asc"		=> _("Subnet, ascending"),
				"subnet,desc"		=> _("Subnet, descending"),
				"description,asc"	=> _("Description, ascending"),
				"description,desc"	=> _("Description, descending"),
			);
			
			foreach($opts as $key=>$line) {
				if($settings['subnetOrdering'] == $key) { print "<option value='$key' selected>$line</option>"; }
				else 									{ print "<option value='$key'>$line</option>"; }
			}
			
			?>
		</select>
	</td>
	<td class="info2">
		<?php print _('How to order display of subnets'); ?>
	</td>
</tr>


<!-- result -->
<tr class="th">
	<td colspan="2">
		<div class="settingsEdit"></div>
	</td>
	<td></td>
</tr>

<!-- Submit -->
<tr class="th">
	<td class="title"></td>
	<td class="submit">
		<input type="submit" class="btn btn-sm btn-success pull-right" value="<?php print _('Save changes'); ?>">
	</td>
	<td></td>
</tr>

</table>
</form>