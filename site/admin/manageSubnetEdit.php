<?php

/*
 * Print edit subnet
 *********************/

/* required functions */
require_once('../../functions/functions.php'); 

/* verify that user is logged in */
isUserAuthenticated(false);

/* verify that user has permissions if add */
if($_POST['action'] == "add") {
	$sectionPerm = checkSectionPermission ($_POST['sectionId']);
	if($sectionPerm != 3) {
		die("<div class='pHeader'>"._('Error')."</div><div class='pContent'><div class='alert alert-danger'>"._('You do not have permissions to add new subnet in this section')."!</div></div><div class='pFooter'><button class='btn btn-sm btn-default hidePopups'>"._('Close')."</button>");
	}
}
/* otherwise check subnet permission */
else {
	$subnetPerm = checkSubnetPermission ($_POST['subnetId']);
	if($subnetPerm != 3) {
		die("<div class='pHeader'>"._('Error')."</div><div class='pContent'><div class='alert alert-danger'>"._('You do not have permissions to add edit/delete this subnet')."!</div></div><div class='pFooter'><button class='btn btn-sm btn-default hidePopups'>"._('Close')."</button>");
	}
}

/* verify post */
CheckReferrer();


/*
	This can be called from subnetManagement, subnet edit in IP details page and from IPCalc!
	
	From IP address list we must also provide delete button!
	
	From search we directly provide 
		subnet / mask
	
*/

# we are editing or deleting existing subnet, get old details
if ($_POST['action'] != "add") {
    $subnetDataOld = getSubnetDetailsById ($_POST['subnetId']);
}
# we are adding new subnet - get section details
else {
	# for selecting master subnet if added from subnet details!
	if(strlen($_REQUEST['subnetId']) > 0) {
    	$tempData = getSubnetDetailsById ($_POST['subnetId']);	
    	$subnetDataOld['masterSubnetId'] = $tempData['id'];			// same master subnet ID for nested
    	$subnetDataOld['vlanId'] 		 = $tempData['vlanId'];		// same default vlan for nested
    	$subnetDataOld['vrfId'] 		 = $tempData['vrfId'];		// same default vrf for nested
	}
	$sectionName = getSectionDetailsById ($_POST['sectionId']);
	
	# set master if it came from free space!
	if(isset($_POST['freespaceMSID'])) {
		$subnetDataOld['masterSubnetId'] = $_POST['freespaceMSID'];		// dump name, but it will do :)
	}
}

/* get custom subnet fields */
$customSubnetFields = getCustomFields('subnets');

/* vlan result on the fly */
if(isset($_POST['vlanId'])) {
	$subnetDataOld['vlanId'] = $_POST['vlanId'];
}

# set readonly flag
if($_POST['action'] == "edit" || $_POST['action'] == "delete")	{ $readonly = true; }
else															{ $readonly = false; }
?>



<!-- header -->
<div class="pHeader"><?php print ucwords(_("$_POST[action]")); ?> <?php print _('subnet'); ?></div>


<!-- content -->
<div class="pContent">

	<form id="editSubnetDetails">
	<table class="editSubnetDetails table table-noborder table-condensed">

    <!-- name -->
    <tr>
        <td class="middle"><?php print _('Subnet'); ?></td>
        <td>
        	<?php
        	# set CIDR
        	if ($_POST['location'] == "ipcalc") { $cidr = $_POST['subnet'] .'/'. $_POST['bitmask']; }  
            if ($_POST['action'] != "add") 		{ $cidr = transform2long($subnetDataOld['subnet']) .'/'. $subnetDataOld['mask']; }       	
        	?>
            <input type="text" class="form-control input-sm input-w-200" name="subnet"   placeholder="<?php print _('subnet in CIDR'); ?>"   value="<?php print $cidr; ?>" <?php if ($readonly) print "readonly"; ?>>
        </td>
        <td class="info2">
        	<button class="btn btn-xs btn-default"  id='get-ripe' rel='tooltip' data-placement="bottom" title='<?php print _('Get information from RIPE database'); ?>'><i class="fa fa-refresh"></i></button>
        	<?php print _('Enter subnet in CIDR format (e.g. 192.168.1.1/24)'); ?>
        </td>
    </tr>

    <!-- description -->
    <tr>
        <td class="middle"><?php print _('Description'); ?></td>
        <td>
            <input type="text" class="form-control input-sm input-w-200" id="field-description" name="description"  placeholder="<?php print _('subnet description'); ?>" value="<?php if(isset($subnetDataOld['description'])) {print $subnetDataOld['description'];} ?>">
        </td>
        <td class="info2"><?php print _('Enter subnet description'); ?></td>
    </tr>  

    <?php if($_POST['action'] != "add") { ?>
    <!-- section -->
    <tr>
        <td class="middle"><?php print _('Section'); ?></td>
        <td>
        	<select name="sectionIdNew" class="form-control input-sm input-w-auto">
            	<?php
           		$sections = fetchSections();
            
            	foreach($sections as $section) {
            		/* selected? */
            		if($_POST['sectionId'] == $section['id']) { print '<option value="'. $section['id'] .'" selected>'. $section['name'] .'</option>'. "\n"; }
            		else 									  { print '<option value="'. $section['id'] .'">'. $section['name'] .'</option>'. "\n"; }
            	}
            ?>
            </select>
        	
        	</select>
        </td>
        <td class="info2"><?php print _('Move to different section'); ?></td>
    </tr>  
    <?php } ?>
    
    <!-- vlan -->
    <tr>
        <td class="middle"><?php print _('VLAN'); ?></td>
        <td id="vlanDropdown"> 
			<?php include('manageSubnetEditPrintVlanDropdown.php'); ?>
         </td>
        <td class="info2"><?php print _('Select VLAN'); ?></td>
    </tr>

    <!-- Master subnet -->
    <tr>
        <td><?php print _('Master Subnet'); ?></td>
        <td>
        	<?php printDropdownMenuBySection($_POST['sectionId'], $subnetDataOld['masterSubnetId']); ?>
        </td>
        <td class="info2"><?php print _('Enter master subnet if you want to nest it under existing subnet, or select root to create root subnet'); ?>!</td>
    </tr>

    <?php
    /* get all site settings */
	$settings = getAllSettings();
	$VRFs 	  = getAllVRFs();
	
	/* set default value */
	if(empty($subnetDataOld['vrfId'])) 			{ $subnetDataOld['vrfId'] = "0"; }
	/* set default value */
	if(empty($subnetDataOld['allowRequests'])) 	{ $subnetDataOld['allowRequests'] = "0"; }

	/* if vlan support is enabled print available vlans */	
	if($settings['enableVRF'] == 1) {
	
		print '<tr>' . "\n";
        print '	<td class="middle">'._('VRF').'</td>' . "\n";
        print '	<td>' . "\n";
        print '	<select name="vrfId" class="form-control input-sm input-w-auto">'. "\n";
        
        //blank
        print '<option disabled="disabled">'._('Select VRF').'</option>';
        print '<option value="0">'._('None').'</option>';
        
        if($VRFs!=false) {
        foreach($VRFs as $vrf) {
        
        	if ($vrf['vrfId'] == $subnetDataOld['vrfId']) 	{ print '<option value="'. $vrf['vrfId'] .'" selected>'. $vrf['name'] .'</option>'; }
        	else 											{ print '<option value="'. $vrf['vrfId'] .'">'. $vrf['name'] .'</option>'; }
        }
        }
        
        print ' </select>'. "\n";
        print '	</td>' . "\n";
        print '	<td class="info2">'._('Add this subnet to VRF').'</td>' . "\n";
    	print '</tr>' . "\n";
	
	}
	else {
		print '<tr style="display:none"><td colspan="8"><input type="hidden" name="vrfId" value="'. $subnetDataOld['vrfId'] .'"></td></tr>'. "\n";
	}

	?>
	<?php if($_POST['action'] == "edit") { ?>
	<!-- resize / split -->
	<tr>
        <td class="middle"><?php print _('Resize'); ?> / <?php print _('split'); ?></td>
        <td>
	    <div class="btn-group">
        	<button class="btn btn-xs btn-default" id="resize" 											rel="tooltip" data-container='body' title="<?php print _('Resize subnet'); ?>" data-subnetId="<?php print $_POST['subnetId']; ?>"><i class="fa fa-gray fa-arrows-v"></i></button>
        	<?php
        	# check if it has slaves - if yes it cannot be splitted!
        	$slaves = subnetContainsSlaves($_POST['subnetId']);
        	?>
        	<button class="btn btn-xs btn-default <?php if($slaves) print "disabled"; ?>" id="split"    rel="tooltip" data-container='body' title="<?php print _('Split subnet'); ?>"    data-subnetId="<?php print $_POST['subnetId']; ?>"><i class="fa fa-gray fa-expand"></i></button>
        	<button class="btn btn-xs btn-default" 										  id="truncate" rel="tooltip" data-container='body' title="<?php print _('Truncate subnet'); ?>" data-subnetId="<?php print $_POST['subnetId']; ?>"><i class="fa fa-gray fa-trash-o"></i></button>
	    </div>
        </td>
        <td class="info2"><?php print _('Resize, split or truncate this subnet'); ?></td>
    </tr>
    <?php } ?>
	
	<?php
	/* allow / deny IP requests if enabled in settings */	
	if($settings['enableIPrequests'] == 1) {
	
		if( isset($subnetDataOld['allowRequests']) && ($subnetDataOld['allowRequests'] == 1) )	{ $checked = "checked"; }
		else																					{ $checked = ""; }
	
		print '<tr>' . "\n";
        print '	<td>'._('IP Requests').'</td>' . "\n";
        print '	<td>' . "\n";
        print '		<input type="checkbox" name="allowRequests" value="1" '.$checked.'>'. "\n";
        print '	</td>' . "\n";
        print '	<td class="info2">'._('Allow or deny IP requests for this subnet').'</td>' . "\n";
    	print '</tr>' . "\n";
	
	}
	else {
		print '<tr style="display:none"><td colspan="8"><input type="hidden" name="allowRequests" value="'. $subnetDataOld['allowRequests'] .'"></td></tr>'. "\n";
	}	

		/* show names instead of ip address! */
		print '<tr>' . "\n";
        print '	<td>'._('Show as name').'</td>' . "\n";
        print '	<td>' . "\n";
        print '		<input type="checkbox" name="showName" value="1" ' . "\n";
        
        if( isset($subnetDataOld['showName']) && ($subnetDataOld['showName'] == 1)) {
        	print 'checked';
        }
        
        print ' >'. "\n";
        
        # hidden ones
        ?>
            <!-- hidden values -->
            <input type="hidden" name="sectionId"       value="<?php print $_POST['sectionId'];    ?>">
            <input type="hidden" name="subnetId"        value="<?php print $_POST['subnetId'];     ?>">       
            <input type="hidden" name="action"    		value="<?php print $_POST['action']; ?>">
            <input type="hidden" name="location"    	value="<?php print $_POST['location']; ?>">   
            <?php if(isset($_POST['freespaceMSID'])) { ?>     
            <input type="hidden" name="freespace"    	value="true">  
            <?php } ?>
            <input type="hidden" name="vrfIdOld"        value="<?php print $subnetDataOld['vrfId'];    ?>">

        <?php
        print '	</td>' . "\n";
        print '	<td class="info2">'._('Show Subnet name instead of subnet IP address').'</td>' . "\n";
    	print '</tr>' . "\n";	    

		#
		if( isset($subnetDataOld['pingSubnet']) && ($subnetDataOld['pingSubnet'] == 1) )	{ $checked = "checked"; }
		else																				{ $checked = ""; }
	
		# check host status
		print '<tr>' . "\n";
        print '	<td>'._('Check hosts status').'</td>' . "\n";
        print '	<td>' . "\n";
        print '		<input type="checkbox" name="pingSubnet" value="1" '.$checked.'>'. "\n";
        print '	</td>' . "\n";
        print '	<td class="info2">'._('Ping hosts inside subnet to check avalibility').'</td>' . "\n";

    	# custom Subnet fields
	    if(sizeof($customSubnetFields) > 0) {
	    	# count datepickers
			$timeP = 0;
		
	    	print "<tr>";
	    	print "	<td colspan='3' class='hr'><hr></td>";
	    	print "</tr>";
		    foreach($customSubnetFields as $field) {

		    	# replace spaces
		    	$field['nameNew'] = str_replace(" ", "___", $field['name']);
		    	# retain newlines
		    	$subnetDataOld[$field['name']] = str_replace("\n", "\\n", $subnetDataOld[$field['name']]);
		    	
		    	# required
				if($field['Null']=="NO")	{ $required = "*"; }
				else						{ $required = ""; }

				print '<tr>'. "\n";
				print '	<td>'. $field['name'] .' '.$required.'</td>'. "\n";
				print '	<td colspan="2">'. "\n";
				
				//set type
				if(substr($field['type'], 0,3) == "set") {
					//parse values
					$tmp = explode(",", str_replace(array("set(", ")", "'"), "", $field['type']));
					//null
					if($field['Null']!="NO") { array_unshift($tmp, ""); }
									
					print "<select name='$field[nameNew]' class='form-control input-sm input-w-auto' rel='tooltip' data-placement='right' title='$field[Comment]'>";
					foreach($tmp as $v) {
						if($v==$subnetDataOld[$field['name']])	{ print "<option value='$v' selected='selected'>$v</option>"; }
						else								{ print "<option value='$v'>$v</option>"; }
					}
					print "</select>";
				}
				//date and time picker
				elseif($field['type'] == "date" || $field['type'] == "datetime") {
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
					if($field['type'] == "date")	{ $size = 10; $class='datepicker';		$format = "yyyy-MM-dd"; }
					else							{ $size = 19; $class='datetimepicker';	$format = "yyyy-MM-dd"; }
									
					//field
					if(!isset($subnetDataOld[$field['name']]))	{ print ' <input type="text" class="'.$class.' form-control input-sm input-w-auto" data-format="'.$format.'" name="'. $field['nameNew'] .'" maxlength="'.$size.'" '.$delete.' rel="tooltip" data-placement="right" title="'.$field['Comment'].'">'. "\n"; }
					else										{ print ' <input type="text" class="'.$class.' form-control input-sm input-w-auto" data-format="'.$format.'" name="'. $field['nameNew'] .'" maxlength="'.$size.'" value="'. $subnetDataOld[$field['name']]. '" '.$delete.' rel="tooltip" data-placement="right" title="'.$field['Comment'].'">'. "\n"; } 
				}	
				//boolean
				elseif($field['type'] == "tinyint(1)") {
					print "<select name='$field[nameNew]' class='form-control input-sm input-w-auto' rel='tooltip' data-placement='right' title='$field[Comment]'>";
					$tmp = array(0=>"No",1=>"Yes");
					//null
					if($field['Null']!="NO") { $tmp[2] = ""; }
					
					foreach($tmp as $k=>$v) {
						if(strlen($subnetDataOld[$field['name']])==0 && $k==2)	{ print "<option value='$k' selected='selected'>"._($v)."</option>"; }
						elseif($k==$subnetDataOld[$field['name']])				{ print "<option value='$k' selected='selected'>"._($v)."</option>"; }
						else													{ print "<option value='$k'>"._($v)."</option>"; }
					}
					print "</select>";
				}	
				//text
				elseif($field['type'] == "text") {
					print ' <textarea class="form-control input-sm" name="'. $field['nameNew'] .'" placeholder="'. $field['name'] .'" '.$delete.' rowspan=3 rel="tooltip" data-placement="right" title="'.$field['Comment'].'">'. $subnetDataOld[$field['name']]. '</textarea>'. "\n"; 				
				}	
				//default - input field
				else {
					print ' <input type="text" class="ip_addr form-control input-sm" name="'. $field['nameNew'] .'" placeholder="'. $field['name'] .'" value="'. $subnetDataOld[$field['name']]. '" size="30" '.$delete.' rel="tooltip" data-placement="right" title="'.$field['Comment'].'">'. "\n"; 				
				}
							
				print '	</td>'. "\n";
				print '</tr>'. "\n";
		    }
	    }
	    
	    # divider
	    print "<tr>";
	    print "	<td colspan='3' class='hr'><hr></td>";
	    print "</tr>";
    ?>
    
    </table>
    </form> 
    
    <?php
    # warning if delete
    if($_POST['action'] == "delete" || ($_POST['location'] == "IPaddresses" && $_POST['action'] != "add"  )) {
	    print "<div class='alert alert-warning' style='margin-top:0px;'><strong>"._('Warning')."</strong><br>"._('Removing subnets will delete ALL underlaying subnets and belonging IP addresses')."!</div>";
    }
    ?>


</div>


<!-- footer -->
<div class="pFooter">
	<div class="btn-group">
		<button class="btn btn-sm btn-default hidePopups"><?php print _('Cancel'); ?></button>
		<?php
		//if action == edit and location = IPaddresses print also delete form
		if(($_POST['action'] == "edit") && ($_POST['location'] == "IPaddresses") ) {
			print "<button class='btn btn-sm btn-default btn-danger editSubnetSubmitDelete editSubnetSubmit'><i class='icon-white icon-trash'></i> "._('Delete subnet')."</button>";
		}
		?>
		<button type="submit" class="btn btn-sm btn-default editSubnetSubmit <?php if($_POST['action']=="delete") print "btn-danger"; else print "btn-success"; ?>"><i class="fa <?php if($_POST['action']=="add") { print "fa-plus"; } else if ($_POST['action']=="delete") { print "fa-trash-o"; } else { print "fa-check"; } ?>"></i> <?php print ucwords(_($_POST['action'])); ?></button>
	</div>
	
	<div class="manageSubnetEditResult"></div>
	<!-- vlan add holder from subnets -->
	<div id="addNewVlanFromSubnetEdit" style="display:none"></div>
</div>