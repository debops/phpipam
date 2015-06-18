<?php

/*
 * Script to print pie graph for subnet usage
 ********************************************/
 
# if slaves reset IP addresses!
$slaves = getAllSlaveSubnetsBySubnetId ($subnetId); 

# set ping statuses
$statuses = explode(";", $settings['pingStatus']);

# check number of hosts for all subnets, including sub-subnets!
if(sizeof($slaves)>0) {
	$ipaddresses = getIpAddressesBySubnetIdSlavesSort ($subnetId);
}

# for statistics we need to deduct 2 for each subnet bigger than /31
$d=0;
foreach($slaves as $s) {
	if($s['mask']<31) {
		$d=$d+2;
	}
}

# get offline, reserved and DHCP
$out['offline']  = 0;
$out['online']   = 0;
$out['reserved'] = 0;
$out['dhcp']     = 0;
$out['warning']  = 0;
$out['error']    = 0;
$out['neutral']  = 0;
$out['disconnected'] = 0;
	
foreach($ipaddresses as $ip) {
	if		($ip['state'] == "0")	{ $out['offline']++; 	}
	else if ($ip['state'] == "2")	{ $out['reserved']++; 	}
	else if ($ip['state'] == "3")	{ $out['dhcp']++; 		}
	else if ($ip['state'] == "1")	{
		if ($SubnetDetails['pingSubnet']=="1") {
			$tDiff = time() - strtotime($ip['lastSeen']);
			if ($tDiff < $statuses[0]) { $out['online']++; }
			elseif ($tDiff < $statuses[1]) { $out['warning']++; }
			elseif ($tDiff < 2592000) { $out['error']++; }
			elseif ($ip['lastSeen'] == "0000-00-00 00:00:00") { $out['neutral']++; }
			else { $out['disconnected']++; }
		} else { $out['online']++; }
	}
}
# get details
if ($SubnetDetails['pingSubnet']=="1") {
	$details = calculateSubnetDetailsNewActive ( $SubnetDetails['subnet'], $SubnetDetails['mask'], $out['online'], $out['warning'], $out['error'], $out['neutral'], $out['disconnected'], $out['offline'], $out['reserved'], $out['dhcp'], $d );
} else {
	$details = calculateSubnetDetailsNew ( $SubnetDetails['subnet'], $SubnetDetails['mask'], $out['online'], $out['offline'], $out['reserved'], $out['dhcp'], $d );
}
?>

<!-- charts -->
<script language="javascript" type="text/javascript" src="js/flot/jquery.flot.js"></script>
<script language="javascript" type="text/javascript" src="js/flot/jquery.flot.pie.js"></script>
<!--[if lte IE 8]><script language="javascript" type="text/javascript" src="js/flot/excanvas.min.js"></script><![endif]-->


<script type="text/javascript">
$(function () {
    
    var data = [
    	<?php
     	if($details['freehosts_percent']>0)  {
    		$details['freehosts_percent'] = str_replace(",", ".", $details['freehosts_percent']);
    		print "{ label: '"._('Free')."',     data: $details[freehosts_percent], color: '#D8D8D8' }, ";		# free hosts
    	}
    	if($details['neutral_percent']>0) {
    	    $details['neutral_percent'] = str_replace(",", ".", $details['neutral_percent']);
			print "{ label: '"._('Neutral')."',   data: $details[neutral_percent],    color: '#A5D0ED' }, ";		# active hosts
    	}
    	if($details['online_percent']>0) {
    	    $details['online_percent'] = str_replace(",", ".", $details['online_percent']);
			print "{ label: '"._('Active')."',   data: $details[online_percent],    color: '#A9C9A4' }, ";		# active hosts
    	}
    	if($details['warning_percent']>0) {
    	    $details['warning_percent'] = str_replace(",", ".", $details['warning_percent']);
			print "{ label: '"._('Warning')."',   data: $details[warning_percent],    color: '#FFCE57' }, ";		# active hosts
    	}
    	if($details['error_percent']>0) {
    	    $details['error_percent'] = str_replace(",", ".", $details['error_percent']);
			print "{ label: '"._('Error')."',   data: $details[error_percent],    color: '#F59C99' }, ";		# active hosts
    	}
    	if($details['disconnected_percent']>0) {
    	    $details['disconnected_percent'] = str_replace(",", ".", $details['disconnected_percent']);
			print "{ label: '"._('Disconnected')."',   data: $details[disconnected_percent],    color: '#FFA1E3' }, ";		# active hosts
    	}
    	if($details['offline_percent']>0) {
        	$details['offline_percent'] = str_replace(",", ".", $details['offline_percent']);
    		print "{ label: '"._('Offline')."',  data: $details[offline_percent],   color: '#F59C99'  },";		# offline hosts	    	
		}
    	if($details['reserved_percent']>0) {
    		$details['reserved_percent'] = str_replace(",", ".", $details['reserved_percent']);
    		print "{ label: '"._('Reserved')."', data: $details[reserved_percent],  color: '#9AC0CD' },";			# reserved hosts	     	
		}
    	if($details['dhcp_percent']>0) {
    		$details['dhcp_percent'] = str_replace(",", ".", $details['dhcp_percent']);
    		print "{ label: '"._('DHCP')."',     data: $details[dhcp_percent],      color: '#a9a9a9' },";		# dhcp hosts	 
    	}
    	?>
    

	];
	
	var options = {
    series: {
        pie: {
            show: true,
            label: {
	            show: true,
	            radius: 1,
	            threshold: 0.01	//hide < 1%
            },
            background: {
	            color: 'red'
            },
            radius: 0.9,
            stroke: {
	            color: '#fff',
	            width: 2
            },
            offset: {
	            left: 0
            }
            
        }
    },
    legend: {
	    show: true,
	    backgroundColor: ""
    },
	grid: {
		hoverable: false,
	  	clickable: true
	},
    highlightColor: '#AA4643',
    colors: ['#D8D8D8', '#a9a9a9', '#da4f49', '#08c', '#5bb75b' ],		//free, active, offline, reserved, dhcp
    grid: {
	        show: true,
	        aboveData: false,
	        color: "#666",
	        backgroundColor: "white",
    		borderWidth: 0,
    		borderColor: null,
    		minBorderMargin: null,
    		clickable: true,
    		hoverable: true,
    		autoHighlight: true,
    		mouseActiveRadius: 3
    		}
    };
    
    $.plot($("#pieChart"), data, options);
});
</script>