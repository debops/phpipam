<?php

/**
 * Script to confirm / reject IP address request
 ***********************************************/

require_once('../../functions/functions.php'); 
require_once('../../config.php'); 

/* verify that user is admin */
checkAdmin();

/* get posted request id */
$requestId = $_POST['requestId'];

/* fetch request */
$request = getIPrequestById ($requestId);

if(sizeof($request) == 0) {
	die("<div class='alert alert alert-danger'>"._('Request does not exist')."!</div>");
}

/* get all selected fields for filtering */
$setFieldsTemp = getSelectedIPaddrFields();
/* format them to array! */
$setFields = explode(";", $setFieldsTemp);

/* get all custom fields */
$myFields = getCustomFields('ipaddresses');
$myFieldsSize = sizeof($myFields);
?>


<!-- header -->
<div class="pHeader"><?php print _('Manage IP address request'); ?></div>

<!-- content -->
<div class="pContent">
	
	<!-- IP address request form -->
	<form class="manageRequestEdit" name="manageRequestEdit">
	<!-- edit IP address table -->
	<table id="manageRequestEdit" class="table table-striped table-condensed">
	<!-- Section -->
	<tr>
		<th><?php print _('Requested subnet'); ?></th>
		<td>
			<select name="subnetId" id="subnetId" class="form-control input-sm input-w-auto">
			<?php
			$subnets = fetchAllSubnets ();
		
			foreach($subnets as $subnet) {
				/* show only subnets that allow IP exporting */
				if($subnet['allowRequests'] == 1) {
					if($request['subnetId'] == $subnet['id'])	{ print '<option value="'. $subnet['id'] .'" selected>' . Transform2long($subnet['subnet']) .'/'. $subnet['mask'] .' ['. $subnet['description'] .']</option>'; }
					else 										{ print '<option value="'. $subnet['id'] .'">' . Transform2long($subnet['subnet']) .'/'. $subnet['mask'] .' ['. $subnet['description'] .']</option>'; }
				}
			}
			?>
			</select>
		</td>
	</tr>
	<!-- IP address -->
	<tr>
		<th><?php print _('IP address'); ?></th>
		<td>
			<input type="text" name="ip_addr" class="ip_addr form-control input-sm" value="<?php print transform2long(getFirstAvailableIPAddress ($request['subnetId'])); ?>" size="30">			
			<input type="hidden" name="requestId" value="<?php print $request['id']; ?>">
			<input type="hidden" name="requester" value="<?php print $request['requester']; ?>">
    	</td>
	</tr>
	<!-- description -->
	<tr>
		<th><?php print _('Description'); ?></th>
		<td>
			<input type="text" name="description" class="form-control input-sm" value="<?php if(isset($request['description'])) { print $request['description'];} ?>" size="30" placeholder="<?php print _('Enter IP description'); ?>">
		</td>
	</tr>
	<!-- DNS name -->
	<tr>
		<th><?php print _('Hostname'); ?></th>
		<td>
			<input type="text" name="dns_name" class="form-control input-sm" value="<?php if(isset($request['dns_name'])) { print $request['dns_name'];} ?>" size="30" placeholder="<?php print _('Enter hostname'); ?>">
		</td>
	</tr>

	<?php if(in_array('state', $setFields)) { ?>
	<!-- state -->
	<tr>
		<th><?php print _('State'); ?></th>
		<td>
			<select name="state" class="form-control input-sm input-w-auto">
				<option value="1" <?php if(isset($request['state'])) { if ($request['state'] == "1") { print 'selected'; }} ?>><?php print _('Active'); ?></option>
				<option value="2" <?php if(isset($request['state'])) { if ($request['state'] == "2") { print 'selected'; }} ?>><?php print _('Reserved'); ?></option>
				<option value="0" <?php if(isset($request['state'])) { if ($request['state'] == "0") { print 'selected'; }} ?>><?php print _('Offline'); ?></option>
				<option value="3" <?php if(isset($request['state'])) { if ($request['state'] == "3") { print 'selected'; }} ?>><?php print _('DHCP'); ?></option>
			</select>
		</td>
	</tr>
	<?php } ?>
	
	<?php if(in_array('owner', $setFields)) { ?>
	<!-- owner -->
	<tr>
		<th><?php print _('Owner'); ?></th>
		<td>
			<input type="text" name="owner" class="form-control input-sm" id="owner" value="<?php if(isset($request['owner'])) { print $request['owner']; } ?>" size="30" placeholder="<?php print _('Enter IP owner'); ?>">
		</td>
	</tr>
	<?php } ?>
	
	<?php if(in_array('switch', $setFields)) { ?>
	<!-- switch / port -->
	<tr>
		<th><?php print _('Device'); ?> / <?php print _('port'); ?></th>		
		<td>
			<select name="switch" class="form-control input-sm input-w-100">
				<option disabled><?php print _('Select device'); ?>:</option>
				<option value="" selected><?php print _('None'); ?></option>
				<?php
				$devices = getAllUniqueDevices();
		
				foreach($devices as $device) {
					if($device['id'] == $details['switch']) { print '<option value="'. $device['id'] .'" selected>'. $device['hostname'] .'</option>'. "\n"; }
					else 									{ print '<option value="'. $device['id'] .'">'. $device['hostname'] .'</option>'. "\n";			 }
				}
				?>
			</select>
			<?php if(in_array('port', $setFields)) { ?>
			/
			<input type="text" name="port" class="form-control input-sm input-w-100" value="<?php if(isset($request['port'])) { print $request['port']; } ?>"  placeholder="<?php print _('Port'); ?>" 
			<?php if ( isset($btnName)) { if ( $btnName == "Delete" ) { print " readonly "; }} ?> 
			>
			
		</td>
	</tr>
	<?php } ?>	
		</td>
	</tr>
	<?php } ?>
	
	<?php if(in_array('note', $setFields)) { ?>
	<!-- note -->
	<tr>
		<th><?php print _('Note'); ?></th>
		<td>
			<input type="text" name="note" class="form-control input-sm" id="note" placeholder="<?php print _('Write note'); ?>" size="30">
		</td>
	</tr>	
	<?php } ?>
	
	<!-- Custom fields -->
	<?php
	if(sizeof($myFields) > 0) {
		print "<tr><td colspan='2'><hr></td></tr>";
		# all my fields
		foreach($myFields as $myField) {
			# replace spaces with |
			$myField['nameNew'] = str_replace(" ", "___", $myField['name']);
			
			print '<tr>'. "\n";
			print '	<th>'. $myField['name'] .'</th>'. "\n";
			print '	<td>'. "\n";
			print ' <input type="text" class="form-control input-sm" name="'. $myField['nameNew'] .'" placeholder="'. $myField['name'] .'" size="30">'. "\n";
			print '	</td>'. "\n";
			print '</tr>'. "\n";		
		}
	}
	?>	
	
	<!-- divider -->
	<tr>
		<td colspan="2"><hr></td>
	</tr>
	
	<!-- requested by -->
	<tr>
		<th><?php print _('Requester email'); ?></th>
		<td><?php if(isset($request['requester'])) { print $request['requester']; } ?></td>
	</tr>
	<!-- comment -->
	<tr>
		<th><?php print _('Requester comment'); ?></th>
		<td><i><?php if(isset($request['comment'])) { if(!empty($request['comment'])) { print '"'. $request['comment'] .'"'; print "<input type='hidden' name='comment' value='$request[comment]'>"; }} ?></i></td>
	</tr>
	<!-- Admin comment -->
	<tr>
		<th><?php print _('Comment approval/reject'); ?>:</th>
		<td>
			<textarea name="adminComment" rows="3" cols="30" class="form-control input-sm" placeholder="<?php print _('Enter reason for reject/approval to be sent to requester'); ?>"></textarea>
		</td>
	</tr>

	</table>
	</form>	
</div>

<!-- footer -->
<div class="pFooter">
	<div class="btn-group">
		<button class="btn btn-sm btn-default hidePopups"><?php print _('Cancel'); ?></button>
		<button class="btn btn-sm btn-default btn-danger manageRequest" data-action='reject'><i class="fa fa-times"></i> <?php print _('Reject'); ?></button>
		<button class="btn btn-sm btn-default btn-success manageRequest" data-action='accept'><i class="fa fa-check"></i> <?php print _('Accept'); ?></button>
	</div>
	
	<!-- result -->
	<div class="manageRequestResult"></div>
</div>