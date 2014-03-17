<?php

/**
 * Script to get all active IP requests
 ****************************************/

/* verify that user is admin */
checkAdmin();

?>


<h4><?php print _('Active Directory connection settings'); ?></h4>
<hr><br>

<div class="alert alert-info">
<?php print _('Here you can set parameters for connecting to AD for authenticating users. phpIPAM uses'); ?> <a href="http://adldap.sourceforge.net/">adLADP</a> <?php print _('to authenticate users. If you need additional settings please take a look at functions/adLDAP or check online documentation!'); ?>
<hr>
<strong><?php print _('Instructions'); ?></strong><br>
<?php print _('First create new user under user management with <u>same username as on AD</u> and set usertype to domain user. Also set proper permissions - group membership for new user'); ?>
</div>


<!-- check for ldap support in php! -->
<?php
/* Available extensions */
$availableExt = get_loaded_extensions();
/* check if ldap exists */
if (!in_array("ldap", $availableExt)) { print '<div class="alert alert alert-danger"><strong>Warning:</strong> '._('ldap extension not enabled in php').'!</div>'; }

?>

<form id="ad">
<table id="ad" class="table table-top">

<!-- DC -->
<tr>
	<td><?php print _('Domain controllers'); ?></td>
	<td>
		<input type="text" name="domain_controllers" class="form-control input-sm" value="<?php print $adSettings['domain_controllers']; ?>">
		<input type="hidden" name="type" value="1">
	</td>
	<td class="info2"><?php print _('Enter domain controllers, separated by ; (default: dc1.domain.local;dc2.domain.local)'); ?>
	</td>
</tr>

<!-- BasedN -->
<tr>
	<td><?php print _('Base DN'); ?></td>
	<td>
		<input type="text" name="base_dn" class="form-control input-sm" value="<?php print $adSettings['base_dn']; ?>">		
	</td>
	<td class="base_dn info2"> 
		<?php print _('Enter base DN for LDAP (default: CN=Users,CN=Company,DC=domain,DC=local)<br>
		If this is set to null then adLDAP will attempt to obtain this automatically from the rootDSE'); ?>
	</td>
</tr>

<!-- Account suffix -->
<tr>
	<td><?php print _('Account suffix'); ?></td>
	<td>
		<input type="text" name="account_suffix" class="form-control input-sm" value="<?php print $adSettings['account_suffix']; ?>">			
	</td>
	<td class="info2">
		<?php print _('The account suffix for your domain (default: @domain.local)'); ?>
	</td>
</tr>

<!-- USername -->
<tr>
	<td><?php print _('Domain account'); ?></td>
	<td>
		<input type="text" name="adminUsername" class="form-control input-sm" placeholder="<?php print _('Username'); ?>" value="<?php print $adSettings['adminUsername']; ?>"><br>			
		<input type="password" name="adminPassword" class="form-control input-sm" placeholder="<?php print _('Password'); ?>" value="<?php print $adSettings['adminPassword']; ?>">			
	</td>
	<td class="info2">
		<?php print _('Domain account for search operations'); ?>
	</td>
</tr>

<!-- SSL -->
<tr>
	<td><?php print _('Use SSL'); ?></td>
	<td>
		<select name="use_ssl" class="form-control input-sm input-w-auto">
			<option value="0" <?php if($adSettings['use_ssl'] == 0) { print 'selected'; } ?>><?php print _('false'); ?></option>
			<option value="1" <?php if($adSettings['use_ssl'] == 1) { print 'selected'; } ?>><?php print _('true'); ?></option>
		</select>
	</td>
	<td class="info2">
		<?php print _('Use SSL (LDAPS), your server needs to be setup (default: false), please see'); ?><bR>
    	<a href="http://adldap.sourceforge.net/wiki/doku.php?id=ldap_over_ssl">http://adldap.sourceforge.net/wiki/doku.php?id=ldap_over_ssl</a>
	</td>
</tr>

<!-- TLS -->
<tr>
	<td><?php print _('Use TLS'); ?></td>
	<td>
		<select name="use_tls" class="form-control input-sm input-w-auto">
			<option value="0" <?php if($adSettings['use_tls'] == 0) { print 'selected'; } ?>><?php print _('false'); ?></option>
			<option value="1" <?php if($adSettings['use_tls'] == 1) { print 'selected'; } ?>><?php print _('true'); ?></option>
		</select>
	</td>
	<td class="info2">
		<?php print _('If you wish to use TLS you should ensure that useSSL is set to false and vice-versa (default: false)'); ?>
	</td>
</tr>


<!-- AD port -->
<tr>
	<td><?php print _('AD port'); ?></td>
	<td>
		<input type="text" name="ad_port" class="form-control input-sm input-w-100" value="<?php print $adSettings['ad_port']; ?>">	
	</td>
	<td class="port info2">
		<?php print _('The default port for LDAP non-SSL connections (default: 389)'); ?>
	</td>
</tr>

<!-- submit -->
<tr class="th">
	<td></td>
	<td>
	<div class="btn-group">
		<input type="button" class="btn btn-sm btn-default" id="checkAD" value="<?php print _('Test settings'); ?>">
		<input type="submit" class="btn btn-sm btn-default" value="<?php print _('Save settings'); ?>">
	</div>
	</td>
	<td></td>
</tr>

</table>
</form>


<!-- result -->
<div class="manageADresult"></div>
