<?php

# get array of IP addresses
$ipVisual = getIpAddressesForVisual($subnetId);

# set ping statuses
$statuses = explode(";", $settings['pingStatus']);

# if empty
if(sizeof($ipVisual) == '0')	{ $ipVisual = array(); }

# show squares to display free/used subnet
if(sizeof($slaves) == 0 && $type == 0) {

	print "<br><h4>"._('Visual subnet display')." <i class='icon-gray icon-info-sign' rel='tooltip' data-html='true' title='"._('Click on IP address box<br>to manage IP address')."!'></i></h4><hr>";
	print "<div class='ip_vis'>";
	# get max hosts
	$max = MaxHosts($SubnetDetails['mask'], $type);
	$max = $SubnetDetails['subnet'] + $max;

	# set start and stop values
	$section = getSectionDetailsById($_GET['section']);
	
	# /31 and /32
	if($SubnetDetails['mask']=="31"||$SubnetDetails['mask']=="32") {
		$start = $SubnetDetails['subnet'];
		$stop  = gmp_strval(gmp_add($max,1));
		$stop  = gmp_strval(gmp_sub($max,1));
	}
	# strict mode?
	elseif($section['strictMode']==1) {
		$start = gmp_strval(gmp_add($SubnetDetails['subnet'],1));	
		$stop  = $max;	
	} else {
		$start = $SubnetDetails['subnet'];
		$stop  = gmp_strval(gmp_add($max,1));
	}
	
	for($m=$start; $m<=$stop; $m=gmp_strval(gmp_add($m,1))) {
		# already exists
		if (array_key_exists((string)$m, $ipVisual)) {
		
			# fix for empty states - if state is disabled, set to active
			if(strlen($ipVisual[$m]['state'])==0) { $ipVisual[$m]['state'] = 1; }
		
			$class = $ipVisual[$m]['state'];
			$id = (int)$ipVisual[$m]['id'];
			$action = 'all-edit';
			
			# tooltip
			$title = transform2long($ipVisual[$m]['ip_addr']);
			if(strlen($ipVisual[$m]['dns_name'])>0)		{ $title .= "<br>".$ipVisual[$m]['dns_name']; }
			if(strlen($ipVisual[$m]['desc'])>0)			{ $title .= "<br>".$ipVisual[$m]['desc']; }

			# host state
			$state = "unknown";
			if ($SubnetDetails['pingSubnet']=="1" && ($class == 1 || $class == 2)) {
				$tDiff = time() - strtotime($ipVisual[$m]['lastSeen']);
				if ($ipVisual[$m]['excludePing'] == "1") { $state = "exclude"; $title .= "<br>"._("Pings disabled"); }
				elseif ($tDiff < $statuses[0])           { $state = "alive";   $title .= "<hr>"._("Last seen").": ".secondsToDays(strtotime($ipVisual[$m]['lastSeen']))." "._("day(s) ago")."<br>(".$ipVisual[$m]['lastSeen'].")"; }
				elseif ($tDiff < $statuses[1])           { $state = "warning"; $title .= "<hr>"._("Last seen").": ".secondsToDays(strtotime($ipVisual[$m]['lastSeen']))." "._("day(s) ago")."<br>(".$ipVisual[$m]['lastSeen'].")"; }
				elseif ($tDiff < 2592000)                { $state = "offline"; $title .= "<hr>"._("Last seen").": ".secondsToDays(strtotime($ipVisual[$m]['lastSeen']))." "._("day(s) ago")."<br>(".$ipVisual[$m]['lastSeen'].")"; }
				elseif ($ipVisual[$m]['lastSeen'] == "0000-00-00 00:00:00") { $state = "neutral"; $title .= "<hr>"._("Last seen").": "._("Never"); }
				else { $state = "disconnected"; $title .= "<hr>"._("Last seen").": ".secondsToDays(strtotime($ipVisual[$m]['lastSeen']))." "._("day(s) ago")."<br>(".$ipVisual[$m]['lastSeen'].")"; }
			}

    	}
    	else {
    		# print add
    		$class = 9;
		$state = "unknown";
    		$id = $m;
    		$action = 'all-add';
    		$title = "";
    	}
   		# permissions
		$permission = checkSubnetPermission ($subnetId);
		
		# print box
		if($permission > 1) {
			print "<span class='state-$state ip-$class modIPaddr'  data-action='$action' rel='tooltip' title='$title' data-position='top' data-html='true' data-subnetId='".$subnetId."' data-id='$id'>.".substr(strrchr(transform2long($m), "."), 1)."</span>";	
		}	
		else {
			print "<span class='ip-$class '  data-action='$action' data-subnetId='".$subnetId."' data-id='$id'>.".substr(strrchr(transform2long($m), "."), 1)."</span>";				
		}
	}
	print "</div>";
	print "<div style='clear:both;padding-bottom:20px;'></div>";	# clear float
}
?>