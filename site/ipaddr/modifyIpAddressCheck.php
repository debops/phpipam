<?php

/**
 * Script to check edited / deleted / new IP addresses
 * If all is ok write to database
 *************************************************/
 
/* include required scripts */
require_once('../../functions/functions.php');

/* check referer and requested with */
CheckReferrer();

/* verify that user is authenticated! */
isUserAuthenticated (true);

/* filter input */
$_POST = filter_user_input($_POST, true, true, false);
$_POST['action'] = filter_user_input($_POST['action'], false, false, true);

/* verify that user has write access */
$subnetPerm = checkSubnetPermission ($_POST['subnetId']);
if($subnetPerm < 2) 	{ die('<div class="alert alert-danger">'._('Cannot edit IP address').'!</div>'); }

/* get posted values */
if ( !empty($_POST['ip_addr']) ) 		{ $ip['ip_addr'] = $_POST['ip_addr']; }
else 									{ $ip['ip_addr'] = "";}
//description
if ( !empty($_POST['description']) ) 	{ $ip['description'] = $_POST['description']; }
else 								 	{ $ip['description'] = ""; }
//hostname
if ( !empty($_POST['dns_name']) ) 		{ $ip['dns_name'] = $_POST['dns_name']; }
else 									{ $ip['dns_name'] = ""; }
//mac
if ( !empty($_POST['mac']) ) 			{ $ip['mac'] = $_POST['mac']; }
else 									{ $ip['mac'] = ""; }
//owner
if ( !empty($_POST['owner']) ) 			{ $ip['owner'] = $_POST['owner']; }
else 									{ $ip['owner'] = ""; }
//switch
$ip['switch'] = @$_POST['switch'];
//port
if ( !empty($_POST['port']) ) 			{ $ip['port'] = $_POST['port']; }
else 									{ $ip['port'] = ""; }
//note
if ( !empty($_POST['note']) ) 			{ $ip['note'] = $_POST['note'];}
else 									{ $ip['note'] = ""; }


//custom
$myFields = getCustomFields('ipaddresses');
if(sizeof($myFields) > 0) {
	foreach($myFields as $myField) {
		# replace possible ___ back to spaces!
		$myField['nameTest']      = str_replace(" ", "___", $myField['name']);
		
		if(isset($_POST[$myField['nameTest']])) { $ip[$myField['name']] = $_POST[$myField['nameTest']];}

		//booleans can be only 0 and 1!
		if($myField['type']=="tinyint(1)") {
			if($ip[$myField['name']]>1) {
				$ip[$myField['name']] = "";
			}
		}
				
		//not null!
		if($myField['Null']=="NO" && strlen($ip[$myField['name']])==0 && !checkAdmin(false,false)) {
			die('<div class="alert alert-danger">"'.$myField['name'].'" can not be empty!</div>');
		}
	}
}

// those values must be present	
$ip['action']  		= $_POST['action'];
$ip['subnet']  		= $_POST['subnet'];
$ip['subnetId']		= $_POST['subnetId'];
$ip['section'] 		= $_POST['section'];
$ip['id']      		= $_POST['id'];
$ip['state']   		= $_POST['state'];
$ip['excludePing']	= $_POST['excludePing'];


//we need old values for mailing
if($ip['action']=="edit" || $ip['action']=="delete") {
	$ipold = getIpAddrDetailsById($_POST['id']);
}

//replace ' in all instances
foreach($ip as $k=>$v) {
	//escape " and '
	$ip[$k] = str_replace("'", "\'", $v);
}

# set excludePing
if($ip['excludePing'] != "1") { $ip['excludePing'] = "0"; }

//delete form visual
if(isset($_POST['action-visual'])) {
	/* replace action to delete if action-visual == delete */
	if($_POST['action-visual'] == "delete") { $ip['action'] = "delete"; }	
}

//detect proper hostname
/*
if(strlen($_POST['dns_name'])>0 && !validateHostname($_POST['dns_name'])) {
	die('<div class="alert alert-danger">'._('Invalid hostname').'!</div>');
}
*/


//no strict checks - for range networks and /31, /32
if(isset($_POST['nostrict'])) {
	if($_POST['nostrict'] == "yes") { $nostrict = true; }
	else							{ $nostrict = false; }
}
else 								{ $nostrict = false; }

/* check if range is being added? */
if (strlen(strstr($ip['ip_addr'],"-")) > 0) {
	//range
	
	/* remove possible spaces */
	$ip['ip_addr'] = str_replace(" ", "", $ip['ip_addr']);
	
	/* get start and stop */
	$range		 = explode("-", $ip['ip_addr']);
	$ip['start'] = $range[0];
	$ip['stop']  = $range[1];
	
	/* verify both IP addresses */
	$verify1 = VerifyIpAddress( $ip['start'], $ip['subnet'], $nostrict );
	$verify2 = VerifyIpAddress( $ip['stop'] , $ip['subnet'], $nostrict );
	
	/* die if wrong IP or not in correct subnet */
	if($verify1) { die('<div class="alert alert-danger">'._('Error').': '. $verify1 .' ('. $ip['start'] .')</div>'); }
	if($verify2) { die('<div class="alert alert-danger">'._('Error').': '. $verify2 .' ('. $ip['stop']  .')</div>'); }
	
	/* set update for update */
	$ip['type'] = "series";
	
	/* go from start to stop and insert / update / delete IPs */
	$start = transform2decimal($ip['start']);
	$stop  = transform2decimal($ip['stop']);

	/* we can add only 200 IP's at once! */
	$size = gmp_strval(gmp_sub($stop,$start));
	if($size > 255) { die('<div class="alert alert-danger">'._('Only 255 IP addresses at once').'!</div>'); }
	
	/* set limits */
	$m = gmp_strval($start);
	$n = gmp_strval(gmp_add($stop,1));

    /* execute insert / update / delete query */  
    if ( $ip['action']=="delete" && !isset($_POST['deleteconfirm'])) {
	    $range = str_replace("-", " - ", $ip['ip_addr']);
		# for ajax to prevent reload
		print "<div style='display:none'>alert alert-danger</div>";
		# result
		print "<div class='alert alert-warning'>";
		print "<strong>"._("Warning")."</strong>: "._("Are you sure you want to delete IP address range")."?";
		print "<hr>$range<div style='text-align:right'>";
		print "<div class='btn-group'>";
		print "	<a class='btn btn-sm btn-danger editIPSubmitDelete' id='editIPSubmitDelete'>"._("Confirm")."</a>";
		print "</div>";
		print "</div>";
		print "</div>";
	}
	else {
		/* for each IP */
		while (gmp_cmp($m, $n) != 0) {	
		
			//reset IP address field
			$ip['ip_addr'] = transform2long($m);
		
			//modify action - if delete ok, dynamically reset add / edit -> if IP already exists set edit
			if($ip['action'] != "delete") {
			   	if (checkDuplicate ($ip['ip_addr'], $ip['subnetId'])) 	{ $ip['action'] = "edit"; }
			    else 													{ $ip['action'] = "add"; }
			}
		
			//if it fails set error log
			if (!modifyIpAddress($ip)) {
		        $errors[] = _('Cannot').' '. $ip['action']. ' '._('IP address').' '. transform2long($m);
		    }			
			/* next IP */
			$m = gmp_strval(gmp_add($m,1));
		}
		
		/* print errors if they exist */
		if(isset($errors)) {
			print '<div class="alert alert-danger">';
			$log = prepareLogFromArray ($errors);
			print $log;
			print '</div>';
			updateLogTable ('Error '. $ip['action'] .' range '. $ip['start'] .' - '. $ip['stop'], $log, 2);
		}
		else {
			# set IP
			$ip['ip_addr'] = $ip['start'] .' - '. $ip['stop'];
			
	    	/* @mail functions ------------------- */
			include_once('../../functions/functions-mail.php');
			sendObjectUpdateMails("ip", $ip['action'], array(), $ip, true);

			print '<div class="alert alert-success">'._('Range').' '. $ip['start'] .' - '. $ip['stop'] .' '._('updated successfully').'!</div>';
			updateLogTable ('Range '. $ip['start'] .' - '. $ip['stop'] .' '. $ip['action'] .' successfull!', 'Range '. $ip['start'] .' - '. $ip['stop'] .' '. $ip['action'] .' '._('successfull').'!', 0);
		}	
	}
}
/* no range, single IP address */
else {

	/* unique */
	if(isset($_POST['unique'])) {
		if($_POST['unique'] == "1" && strlen($_POST['dns_name'])>0) {
			# check if unique
			if(!isHostUnique($_POST['dns_name'])) {
				die('<div class="alert alert-danger">'._('Hostname is not unique').'!</div>');
			}
		}
	}

	/* verify ip address */
	if($ip['action'] == "move")	{ 
		$subnet = getSubnetDetailsById($_POST['newSubnet']);
		$subnet = transform2long($subnet['subnet'])."/".$subnet['mask'];
		$verify = VerifyIpAddress( $ip['ip_addr'], $subnet, $nostrict ); 
		
		$ip['newSubnet'] = $_POST['newSubnet'];
	}
	else { 
		$verify = VerifyIpAddress( $ip['ip_addr'], $ip['subnet'], $nostrict ); 
	}

	/* if errors are present print them, else execute query! */
	if($verify) 				{ die('<div class="alert alert-danger">'._('Error').': '. $verify .' ('. $ip['ip_addr'] .')</div>'); }
	else {
		/* set update for update */
		$ip['type'] = "single";

		/* check for duplicate entry! needed only in case new IP address is added, otherwise the code is locked! */
	    if ($ip['action'] == "add") {  
	        if (checkDuplicate ($ip['ip_addr'], $ip['subnetId'])) {
	            die ('<div class="alert alert-danger">'._('IP address').' '. $ip['ip_addr'] .' '._('already existing in database').'!</div>');
	        }
	    }  

		/* check for duplicate entry on edit! */
	    if ($ip['action'] == "edit") {  
	    	# if IP is the same than it can already exist!
	    	if($ip['ip_addr'] != $_POST['ip_addr_old']) {
	        	if (checkDuplicate ($ip['ip_addr'], $ip['subnetId'])) {
	        	    die ('<div class="alert alert-danger">'._('IP address').' '. $ip['ip_addr'] .' '._('already existing in database').'!</div>');
	        	}	
	    	}
	    } 
	    /* move checks */
	    if($ip['action'] == "move") {
		    # check if not already used in new subnet
	        if (checkDuplicate ($ip['ip_addr'], $ip['newSubnet'])) {
	            die ('<div class="alert alert-danger">'._('Duplicate IP address').' '. $ip['ip_addr'] .' '._('already existing in selected network').'!</div>');
	        }		   
	    }

	    /* execute insert / update / delete query */  
	    if ($ip['action']=="delete" && !isset($_POST['deleteconfirm'])) {
			# for ajax to prevent reload
			print "<div style='display:none'>alert alert-danger</div>";
			# result
			print "<div class='alert alert-warning'>";
			print "<strong>"._("Warning")."</strong>: "._("Are you sure you want to delete IP address")."?";
			print "<hr><div style='text-align:right'>";
			print "<div class='btn-group'>";
			print "	<a class='btn btn-sm btn-danger editIPSubmitDelete' id='editIPSubmitDelete'>"._("Confirm")."</a>";
			print "</div>";
			print "</div>";
			print "</div>";
		}
		else {
			# modify
		    if (!modifyIpAddress($ip)) {
		        print '<div class="alert alert-danger">'._('Error inserting IP address').'!</div>';
		        updateLogTable ('Error '. $ip['action'] .' IP address '. $ip['ip_addr'], 'Error '. $ip['action'] .' IP address '. $ip['ip_addr'] .'<br>SubnetId: '. $ip['subnetId'], 2);
		    }
		    else {
				//set arrays
				if($ip['action']=="add")		{ $old = array();	$new = $ip; }
				elseif($ip['action']=="delete")	{ $old = $ipold;	$new = array(); }
				else							{ $old = $ipold;	$new = $ip; }
	
		    	/* @mail functions ------------------- */
				include_once('../../functions/functions-mail.php');
				sendObjectUpdateMails("ip", $ip['action'], $old, $new);
	
		        print '<div class="alert alert-success">'._("IP $ip[action] successful").'!</div>';
		        updateLogTable ($ip['action'] .' of IP address '. $ip['ip_addr'] .' succesfull!', $ip['action'] .' of IP address '. $ip['ip_addr'] .' succesfull!<br>SubnetId: '. $ip['subnetId'], 0);
		    }
		}
	}
}
?>