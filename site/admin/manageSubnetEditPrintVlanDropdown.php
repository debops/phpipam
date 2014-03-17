<?php

/*
 * Print edit subnet
 *********************/

/* required functions */
if(!function_exists('getSubnetStatsDashboard')) {
	require_once('../../functions/functions.php'); 
}

/* verify that user is logged in */
isUserAuthenticated(false);
?>

<select name="vlanId" class="form-control input-sm input-w-auto">
	<option disabled="disabled"><?php print _('Select VLAN'); ?>:</option>
	<?php
		$vlans = getAllVLANs();
		
		if($_POST['action'] == "add") { $vlan['vlanId'] = 0; }

		$tmp[0]['vlanId'] = 0;
		$tmp[0]['number'] = _('No VLAN');
		
		# on-the-fly
		$tmp[1]['vlanId'] = 'Add';
		$tmp[1]['number'] = _('+ Add new VLAN');	
		
		array_unshift($vlans, $tmp[0]);
		array_unshift($vlans, $tmp[1]);

		foreach($vlans as $vlan) {
			/* set structure */
			$printVLAN = $vlan['number'];
			
			if(!empty($vlan['name'])) { $printVLAN .= " ($vlan[name])"; }
			
			/* selected? */
			if($subnetDataOld['vlanId'] == $vlan['vlanId']) { print '<option value="'. $vlan['vlanId'] .'" selected>'. $printVLAN .'</option>'. "\n"; }
			elseif($_POST['vlanId'] == $vlan['vlanId']) 	{ print '<option value="'. $vlan['vlanId'] .'" selected>'. $printVLAN .'</option>'. "\n"; }
			else 											{ print '<option value="'. $vlan['vlanId'] .'">'. $printVLAN .'</option>'. "\n"; }
		}
?>
</select>
