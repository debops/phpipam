<?php

/**
 * Script to print edit / delete / new IP address
 * 
 * Fetches info from database
 *************************************************/


/* include required scripts */
require_once('../../functions/functions.php');

/* check referer and requested with */
CheckReferrer();

/* get posted values */
$subnetId= $_REQUEST['subnetId'];
$action  = $_REQUEST['action'];
$id      = $_REQUEST['id'];

/* set subnet -> for adding new only */
$subnet = getSubnetDetailsById($subnetId);
$subnet2 = $subnet;
$subnet = transform2long($subnet['subnet']) . "/" . $subnet['mask'];

/* verify that user is logged in */
isUserAuthenticated(false);

/* verify that user has write access */
$subnetPerm = checkSubnetPermission ($subnetId);
if($subnetPerm < 2) {
	print '<div class="pHeader">Edit IP address</div>';
	print '<div class="pContent">';
	print '<div class="alert alert-danger">'._('Cannot edit IP address details').'! <br>'._('You do not have write access for this network').'!</div>';
	print '</div>';
	print '<div class="pFooter"><button class="btn btn-sm btn-default hidePopups">'._('Cancel').'</button></div>';
	die();
}


/**
 *
 * if action is not add then fetch current details
 *
 * otherwise format the $ip field
 *
 */
if ($action == "all-add") {
    $details = array(
    	"ip_addr" => transform2long($id), 
    	"description" => "", 
    	"dns_name" => "", 
    	"owner"	=> "",
    	"switch" => "",
    	"port"	=> ""
    	 );	
}
else if ($action == "add") {	
	$first = getFirstAvailableIPAddress ($subnetId);
	if(!$first) { $first = ""; }
	else		{ $first = transform2long($first); }
	
    $details = array(
    	"ip_addr" => $first, 
    	"description" => "", 
    	"dns_name" => "", 
    	"owner"	=> "",
    	"switch" => "",
    	"port"	=> ""
    	 );
}
else {
	$details = getIpAddrDetailsById ($id);
}

/**
 *	Get first available IP address!
 */

/* Set title and button text */
if ($action == "add") 			{ $btnName = _("add");		$act = "add"; }
else if ($action == "all-add")	{ $btnName = _("add");  	$act = "add"; }
else if ($action == "edit") 	{ $btnName = _("edit"); 	$act = "edit"; }
else if ($action == "all-edit")	{ $btnName = _("edit"); 	$act = "edit"; }
else if ($action == "delete")	{ $btnName = _("delete"); 	$act = "delete"; }
else							{ $btnName = ""; }

/* set delete flag */
if($act=="delete")	{ $delete = "readonly='readonly'"; }
else				{ $delete = ""; }


/* get all selected fields for filtering */
$setFieldsTemp = getSelectedIPaddrFields();
/* format them to array! */
$setFields = explode(";", $setFieldsTemp);


/* get all custom fields */
$myFields = getCustomFields('ipaddresses');
$myFieldsSize = sizeof($myFields);
?>

<!-- header -->
<div class="pHeader"><?php print ucwords($btnName); ?> <?php print _('IP address'); ?></div>

<!-- content -->
<div class="pContent editIPAddress">

	<!-- IP address modify form -->
	<form class="editipaddress" role="form" name="editipaddress">
	<!-- edit IP address table -->
	<table id="editipaddress" class="table table-noborder table-condensed">

	<!-- IP address -->
	<tr>
		<td><?php print _('IP address'); ?> *</td>
		<td>
		<div class="input-group">
			<input type="text" name="ip_addr" class="ip_addr form-control input-sm" value="<?php print $details['ip_addr']; if(is_numeric($_REQUEST['stopIP'])>0) print "-".transform2long($_REQUEST['stopIP']); ?>" placeholder="<?php print _('IP address'); ?>">
    		<span class="input-group-addon">
    			<i class="fa fa-gray fa-info" rel="tooltip" data-html='true' data-placement="left" title="<?php print _('You can add,edit or delete multiple IP addresses<br>by specifying IP range (e.g. 10.10.0.0-10.10.0.25)'); ?>"></i>
    		</span>
			</div>
    		
   			<input type="hidden" name="action" 	 	value="<?php print $act; 	?>">
			<input type="hidden" name="id" 		 	value="<?php print $id; 		?>">
			<input type="hidden" name="subnet"   	value="<?php print $subnet; 	?>">
			<input type="hidden" name="subnetId" 	value="<?php print $subnetId; 	?>">		
			<input type="hidden" name="section" 	value="<?php print $subnet2['section']; ?>">	
			<input type="hidden" name="ip_addr_old" value="<?php print $details['ip_addr']; ?>">
			<?php if($action=="edit" || $action=="delete") { ?>
			<input type="hidden" name="nostrict" value="yes">
			<?php }  ?> 
    	</td>
	</tr>

	<!-- description -->
	<tr>
		<td><?php print _('Description'); ?></td>
		<td>
			<input type="text" name="description" class="ip_addr form-control input-sm" value="<?php if(isset($details['description'])) {print $details['description'];} ?>" size="30" 
			<?php if ( $act == "delete" ) { print " readonly";} ?> 
			placeholder="<?php print _('Description'); ?>">
		</td>
	</tr>


	<!-- DNS name -->
	<?php
	if(!isset($details['dns_name'])) {$details['dns_name'] = "";}
		print '<tr>'. "\n";
		print '	<td>'._('DNS name').'</td>'. "\n";
		print '	<td>'. "\n";
		print '	<div class="input-group">';
		print ' <input type="text" name="dns_name" class="ip_addr form-control input-sm" placeholder="'._('Hostname').'" value="'. $details['dns_name']. '" '.$delete.'>'. "\n";
		print '	 <span class="input-group-addon">'."\n";
		print "		<i class='fa fa-gray fa-repeat' id='refreshHostname' rel='tooltip' data-placement='left' title='"._('Click to check for hostname')."'></i></span>";
		print "	</span>";
		print "	</div>";
		print '	</td>'. "\n";
		print '</tr>'. "\n";
	?>
	<!-- MAC address -->
	<?php
	if(in_array('mac', $setFields)) {
		if(!isset($details['mac'])) {$details['mac'] = "";}	

		print '<tr>'. "\n";
		print '	<td>'._('MAC address').'</td>'. "\n";
		print '	<td>'. "\n";
		print ' <input type="text" name="mac" class="ip_addr form-control input-sm" placeholder="'._('MAC address').'" value="'. $details['mac']. '" size="30" '.$delete.'>'. "\n";
		print '	</td>'. "\n";
		print '</tr>'. "\n";
	}
	?>
	<!-- Owner -->
	<?php
	if(in_array('owner', $setFields)) {

		if(!isset($details['owner'])) {$details['owner'] = "";}	

		print '<tr>'. "\n";
		print '	<td>'._('Owner').'</td>'. "\n";
		print '	<td>'. "\n";
		print ' <input type="text" name="owner" class="ip_addr form-control input-sm" id="owner" placeholder="'._('IP address owner').'" value="'. $details['owner']. '" size="30" '.$delete.'>'. "\n";
		print '	</td>'. "\n";
		print '</tr>'. "\n";
	}
	?>
	<!-- switch / port -->
	<?php
	if(!isset($details['switch']))  {$details['switch'] = "";}	
	if(!isset($details['port'])) 	{$details['port'] = "";}	
	
	# both are active
	if(in_array('switch', $setFields)) {
		print '<tr>'. "\n";
		print '	<td>'._('Device').'</td>'. "\n";
		print '	<td>'. "\n";

		print '<select name="switch" class="ip_addr form-control input-sm input-w-auto" '.$delete.'>'. "\n";
		print '<option disabled>'._('Select device').':</option>'. "\n";
		print '<option value="" selected>'._('None').'</option>'. "\n";
		$devices = getAllUniqueDevices();
		
		foreach($devices as $device) {
			//check if permitted in this section!
			$sections=explode(";", $device['sections']);
			if(in_array($subnet2['sectionId'], $sections)) {
			//if same
			if($device['id'] == $details['switch']) { print '<option value="'. $device['id'] .'" selected>'. $device['hostname'] .'</option>'. "\n"; }
			else 									{ print '<option value="'. $device['id'] .'">'. $device['hostname'] .'</option>'. "\n";			 }
			}
		}
		print '</select>'. "\n";
		print '	</td>'. "\n";
		print '</tr>'. "\n";
	}
	# Port
	if(in_array('port', $setFields)) {

		if(!isset($details['port'])) {$details['port'] = "";}	

		print '<tr>'. "\n";
		print '	<td>'._('Port').'</td>'. "\n";
		print '	<td>'. "\n";
		print ' <input type="text" name="port"  class="ip_addr form-control input-sm input-w-150"  id="port"   placeholder="'._('Port').'"   value="'. $details['port']. '" size="30" '.$delete.'>'. "\n";
		print '	</td>'. "\n";
		print '</tr>'. "\n";
	}
	?>
	<!-- note -->
	<?php
	if(in_array('note', $setFields)) {

		if(!isset($details['note'])) {$details['note'] = "";}	

		print '<tr>'. "\n";
		print '	<td>'._('Note').'</td>'. "\n";
		print '	<td class="note">'. "\n";
		print ' <textarea name="note" class="ip_addr form-control input-sm" cols="23" rows="2" placeholder="'._('Additional notes about IP address').'" '.$delete.'>'. $details['note'] . '</textarea>'. "\n";
		print '	</td>'. "\n";
		print '</tr>'. "\n";
	}
	?>
	<!-- state -->
	<?php
	if(in_array('state', $setFields)) {
	

		print '<tr>'. "\n";
		print '	<td>'._('Type').'</td>'. "\n";
		print '	<td>'. "\n";
		print '		<select name="state" '.$delete.' class="ip_addr form-control input-sm input-w-auto">'. "\n";
		
		#active, reserved, offline
		print '		<option value="1" '; if(isset($details['state'])) { if ($details['state'] == "1") print 'selected'; } print '>'._('Active').'</option>'. "\n";
		print '		<option value="2" '; if(isset($details['state'])) { if ($details['state'] == "2") print 'selected'; } print '>'._('Reserved').'</option>'. "\n";
		print '		<option value="0" '; if(isset($details['state'])) { if ($details['state'] == "0") print 'selected'; } print '>'._('Offline').'</option>'. "\n";
		print '		<option value="3" '; if(isset($details['state'])) { if ($details['state'] == "3") print 'selected'; } print '>'._('DHCP').'</option>'. "\n";

		print '		</select>'. "\n";
		print '	</td>'. "\n";
		print '</tr>'. "\n";
	}
	?>
	<!-- exclude Ping -->
	<?php
	if($subnet2['pingSubnet']==1) {
		//we can exclude individual IP addresses from ping
		if(@$details['excludePing'] == "1")	{ $checked = "checked='checked'"; }
		else								{ $checked = ""; }
		
		print '<tr>';
	 	print '<td>'._("Ping exclude").'</td>';
	 	print '<td>';
	 	print "	<div class='checkbox info2'>";
		print ' 	<input type="checkbox" class="ip_addr" name="excludePing" value="1" '.$checked.' '.$delete.'>'. _('Exclude from ping status checks');
		print "	</div>";
	 	print '</td>';
	 	print '</tr>';
	}
	?>
	<tr>
		<td colspan="2"><hr></td>
	</tr>
	<!-- Custom fields -->
	<?php
	if(sizeof($myFields) > 0) {
		# count datepickers
		$timeP = 0;
			
		# all my fields
		foreach($myFields as $myField) {
			# replace spaces with |
			$myField['nameNew'] = str_replace(" ", "___", $myField['name']);
			
			# required
			if($myField['Null']=="NO")	{ $required = "*"; }
			else						{ $required = ""; }
			
			print '<tr>'. "\n";
			print '	<td>'. $myField['name'] .' '.$required.'</td>'. "\n";
			print '	<td>'. "\n";
			
			//set type
			if(substr($myField['type'], 0,3) == "set") {
				//parse values
				$tmp = explode(",", str_replace(array("set(", ")", "'"), "", $myField['type']));
				//null
				if($myField['Null']!="NO") { array_unshift($tmp, ""); }
								
				print "<select name='$myField[nameNew]' class='form-control input-sm input-w-auto' rel='tooltip' data-placement='right' title='$myField[Comment]'>";
				foreach($tmp as $v) {
					if($v==$details[$myField['name']])	{ print "<option value='$v' selected='selected'>$v</option>"; }
					else								{ print "<option value='$v'>$v</option>"; }
				}
				print "</select>";
			}
			//date and time picker
			elseif($myField['type'] == "date" || $myField['type'] == "datetime") {
				// just for first
				if($timeP==0) {
					print '<link rel="stylesheet" type="text/css" href="css/bootstrap/bootstrap-datetimepicker.min.css">';
					print '<script type="text/javascript" src="js/bootstrap-datetimepicker.min.js"></script>';
					print '<script type="text/javascript">';
					print '$(document).ready(function() {';
					//date only
					print '	$(".datepicker").datetimepicker( {pickDate: true, pickTime: false, pickSeconds: false });';
					//date + time
					print '	$(".datetimepicker").datetimepicker( { pickDate: true, pickTime: true } );';

					print '})';
					print '</script>';
				}
				$timeP++;
				
				//set size
				if($myField['type'] == "date")	{ $size = 10; $class='datepicker';		$format = "yyyy-MM-dd"; }
				else							{ $size = 19; $class='datetimepicker';	$format = "yyyy-MM-dd"; }
								
				//field
				if(!isset($details[$myField['name']]))	{ print ' <input type="text" class="'.$class.' form-control input-sm input-w-auto" data-format="'.$format.'" name="'. $myField['nameNew'] .'" maxlength="'.$size.'" '.$delete.' rel="tooltip" data-placement="right" title="'.$myField['Comment'].'">'. "\n"; }
				else									{ print ' <input type="text" class="'.$class.' form-control input-sm input-w-auto" data-format="'.$format.'" name="'. $myField['nameNew'] .'" maxlength="'.$size.'" value="'. $details[$myField['name']]. '" '.$delete.' rel="tooltip" data-placement="right" title="'.$myField['Comment'].'">'. "\n"; } 
			}	
			//boolean
			elseif($myField['type'] == "tinyint(1)") {
				print "<select name='$myField[nameNew]' class='form-control input-sm input-w-auto' rel='tooltip' data-placement='right' title='$myField[Comment]'>";
				$tmp = array(0=>"No",1=>"Yes");
				//null
				if($myField['Null']!="NO") { $tmp[2] = ""; }
				
				foreach($tmp as $k=>$v) {
					if(strlen($details[$myField['name']])==0 && $k==2)	{ print "<option value='$k' selected='selected'>"._($v)."</option>"; }
					elseif($k==$details[$myField['name']])				{ print "<option value='$k' selected='selected'>"._($v)."</option>"; }
					else												{ print "<option value='$k'>"._($v)."</option>"; }
				}
				print "</select>";
			}	
			//text
			elseif($myField['type'] == "text") {
				print ' <textarea class="form-control input-sm" name="'. $myField['nameNew'] .'" placeholder="'. $myField['name'] .'" '.$delete.' rowspan=3 rel="tooltip" data-placement="right" title="'.$myField['Comment'].'">'. $details[$myField['name']]. '</textarea>'. "\n";
			}	
			//default - input field
			else {
				print ' <input type="text" class="ip_addr form-control input-sm" name="'. $myField['nameNew'] .'" placeholder="'. $myField['name'] .'" value="'. $details[$myField['name']]. '" size="30" '.$delete.' rel="tooltip" data-placement="right" title="'.$myField['Comment'].'">'. "\n"; 
			}
						
			print '	</td>'. "\n";
			print '</tr>'. "\n";		
		}
	}
	?>	


	 <tr>
		<td colspan="2"><hr></td>
	 </tr>
	 
	 <tr>
	 	<td><?php print _('Unique'); ?></td>
	 	<td>
		<div class='checkbox info2'>
		 	<input type="checkbox" name="unique" value="1" <?php print $delete; ?>><?php print _('Unique hostname'); ?>
		</div>
	 	</td>
	 </tr>

	<?php 
	#get type
	 $type = IdentifyAddress( $subnet2['subnet'] );

	 if($subnet2['mask'] < 31 && $action=='add' && $type == "IPv4" ) { ?>
	 <!-- ignore NW /BC checks -->
	 <tr>
		<td><?php print _('Not strict'); ?></td>
		<td>
		<div class='checkbox info2'>
			<input type="checkbox" name="nostrict" value="yes" style="margin-top:0px;"><?php print _('Permit adding network/broadcast as IP'); ?>
		</div>
		</td>
	</tr>
	<?php } ?>

	<?php 
	 if($subnet2['mask'] < 127 && $action=='add' && $type == "IPv6" ) { ?>
	 <!-- ignore NW /BC checks -->
	 <tr>
		<td><?php print _('Not strict'); ?></td>
		<td>
		<div class='checkbox info2'>
			<input type="checkbox" name="nostrict" value="yes" style="margin-top:0px;"><?php print _('Permit adding network/broadcast as IP'); ?>
		</div>
		</td>
	</tr>
	<?php } ?>


</table>	<!-- end edit ip address table -->
</form>		<!-- end IP address edit form -->




</div>

<!-- footer -->
<div class="pFooter">
	<div class="btn-group">
		<button class="btn btn-sm btn-default hidePopups"><?php print _('Cancel'); ?></button>
		<?php
		# add delete if it came from visual edit!
		if($action == 'all-edit') {
		print "<button class='btn btn-sm btn-default btn-danger' id='editIPAddressSubmit' data-action='all-delete'><i class='fa fa-trash-o'></i> "._('Delete IP')."</button>";		
		}
		?>
		<button class="btn btn-sm btn-default <?php if($action=="delete") { print "btn-danger"; } else { print "btn-success"; } ?>" id="editIPAddressSubmit" data-action='<?php print $action; ?>'><i class="fa <?php if($act=="add") { print "fa-plus"; } else if ($act=="delete") { print "fa-trash-o"; } else { print "fa-check"; } ?>"></i> <?php print ucwords($btnName); ?> IP</button>
	</div>

	<!-- holder for result -->
	<div class="addnew_check"></div>
</div>
