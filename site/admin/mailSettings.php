<?php

/**
 *	Site settings
 **************************/

/* verify that user is admin */
checkAdmin();

/* fetch all mail settings */
$mailsettings = getAllMailSettings();

?>

<!-- title -->
<h4>phpIPAM <?php print _('Mail settings'); ?></h4>
<hr>

<form name="mailsettings" id="mailsettings">
<table id="mailsettingstbl" class="table table-hover table-condensed table-top table-auto">


<!-- Server settings -->
<tr class="settings-title">
	<th colspan="3"><h4><?php print _('Mail server type'); ?></h4></th>
</tr>

<!-- Server type -->
<tr>
	<td><?php print _('Server type'); ?></th>
	<td>
		<select name="mtype" class="form-control input-sm input-w-auto" id="mtype">
			<option value="localhost"><?php print _("Localhost"); ?></option>
			<option value="smtp" <?php if($mailsettings['mtype']=="smtp") print "selected='selected'"; ?>><?php print _("SMTP"); ?></option>
		</select>
	</td>
	<td class="info2"><?php print _('Select server type for sending mail messages'); ?></td>
</tr>


<!-- smtp -->
<tbody id="smtp" <?php if($mailsettings['mtype']=="localhost") print "style='display:none;'"; ?>>

<!-- Server settings -->
<tr class="settings-title">
	<th colspan="3"><h4><?php print _('SMTP settings'); ?></h4></th>
</tr>
<!-- Server -->
<tr>
	<td><?php print _('Server address'); ?></th>
	<td>
		<input type="text" name="mserver" class='smtp form-control input-sm' value="<?php print $mailsettings['mserver']; ?>">
	</td>
	<td class="info2"><?php print _('Set SMTP server address'); ?></td>
</tr>

<!-- Port -->
<tr>
	<td><?php print _('Port'); ?></th>
	<td>
		<input type="text" name="mport" class='smtp form-control input-sm' value="<?php print $mailsettings['mport']; ?>">
	</td>
	<td class="info2"><?php print _('Set SMTP server port'); ?> (25, 465 or 587)</td>
</tr>

<!-- Server auth -->
<tr>
	<td><?php print _('Server authentication'); ?></th>
	<td>
		<select name="mauth" class="smtp form-control input-sm input-w-auto">
			<option value="no"><?php print _('No'); ?></option>
			<option value="yes" <?php if($mailsettings['mauth']=="yes") print "selected='selected'"; ?>><?php print _('Yes'); ?></option>
		</select>
	</td>
	<td class="info2"><?php print _('Select yes if authentication is required'); ?></td>
</tr>

<!-- Username -->
<tr>
	<td><?php print _('Username'); ?></th>
	<td>
		<input type="text" name="muser" class='smtp form-control input-sm' value="<?php print $mailsettings['muser']; ?>">
	</td>
	<td class="info2"><?php print _('Set username for SMTP authentication'); ?></td>
</tr>

<!-- Password -->
<tr>
	<td><?php print _('Password'); ?></th>
	<td>
		<input type="password" name="mpass" class='smtp form-control input-sm' value="<?php print $mailsettings['mpass']; ?>">
	</td>
	<td class="info2"><?php print _('Set password for SMTP authentication'); ?></td>
</tr>

</tbody>



<!-- Sender settings -->
<tr class="settings-title">
	<th colspan="3"><h4><?php print _('Mail sender settings'); ?></h4></th>
</tr>

<!-- Admin name -->
<tr>
	<td class="title"><?php print _('Sender name'); ?></td>
	<td>
		<input type="text" size="50" class="form-control input-sm" name="mAdminName" value="<?php print $mailsettings['mAdminName']; ?>">
	</td>
	<td class="info2">
		<?php print _('Set administrator name to display when sending mails and for contact info'); ?>
	</td>
</tr>

<!-- Admin mail -->
<tr>
	<td class="title"><?php print _('Admin mail'); ?></td>
	<td>
		<input type="text" size="50" class="form-control input-sm" name="mAdminMail" value="<?php print $mailsettings['mAdminMail']; ?>">
	</td>
	<td class="info2">
		<?php print _('Set administrator e-mail to display when sending mails and for contact info'); ?>
	</td>
</tr>


<!-- test -->
<tr class="th">
	<td class="title"></td>
	<td class="submit" style="padding-top:30px;">
	<div class="btn-group pull-right">
		<a class="btn btn-sm btn-default sendTestMail"><i class="icon icon-gray icon-envelope"></i> <?php print _('Send test email'); ?></a>
		<input type="submit" class="btn btn-sm btn-default btn-success pull-right" value="<?php print _('Save changes'); ?>">
	</div>
	</td>
	<td></td>
</tr>

</table>
</form>


<!-- Result -->
<div class="settingsMailEdit"></div>