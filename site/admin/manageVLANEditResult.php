<?php 

/**
 * Script to edit VLAN details
 *******************************/

/* required functions */
require_once('../../functions/functions.php'); 

/* verify that user is admin */
if (!checkAdmin()) die('');

/* prevent XSS in action */
$_POST['action'] = filter_user_input ($_POST['action'], false, true, true);
/* escape vars to prevent SQL injection */
$_POST = filter_user_input ($_POST, true, true);

/* get modified details */
$vlan = $_POST;

/* get settings */
$settings = getAllSettings ();

/* if it already exist DIE! */
if($settings['vlanDuplicate'] == "0") {
if($vlan['action'] == "add") {
	if(!getVLANbyNumber($vlan['number'])) 	{ }
	else 									{ die('<div class="alert alert-danger">'._('VLAN already exists').'!</div>'); }	
}
}

/* if number too high */
if($vlan['number']>$settings['vlanMax'])	{ die('<div class="alert alert-danger">'._('Highest possible VLAN number is ').$settings['vlanMax'].'!</div>'); }
if($vlan['action']=="add") {
	if($vlan['number']<0)					{ die('<div class="alert alert-danger">'._('VLAN number cannot be negative').'!</div>'); }
	elseif(!is_numeric($vlan['number']))	{ die('<div class="alert alert-danger">'._('Not number').'!</div>'); }
}

//custom
$myFields = getCustomFields('vlans');
if(sizeof($myFields) > 0) {
	foreach($myFields as $myField) {
		# replace possible ___ back to spaces!
		$myField['nameTest']      = str_replace(" ", "___", $myField['name']);
		
		if(isset($_POST[$myField['nameTest']])) { $vlan[$myField['name']] = $vlan[$myField['nameTest']];}
	}
}

/* sanitize post! */
$vlan['name'] 		 = htmlentities($vlan['name'], ENT_COMPAT | ENT_HTML401, "UTF-8");			# prevent XSS
$vlan['number'] 	 = htmlentities($vlan['number'], ENT_COMPAT | ENT_HTML401, "UTF-8");		# prevent XSS
$vlan['description'] = htmlentities($vlan['description'], ENT_COMPAT | ENT_HTML401, "UTF-8");	# prevent XSS

/* Hostname must be present! */
if($vlan['number'] == "") 					{ die('<div class="alert alert-danger">'._('Number is mandatory').'!</div>'); }

/* update details */
if($vlan['action']=="add") {
	if(!$id=updateVLANDetails($vlan, true)) 	{ print('<div class="alert alert-danger"  >'._("Failed to $vlan[action] VLAN").'!</div>'); }
	else 										{ print('<div class="alert alert-success">'._("VLAN $vlan[action] successfull").'!</div><p id="vlanidforonthefly" style="display:none">'.$id.'</p>'); }	
} else {
	if(!updateVLANDetails($vlan, false)) 		{ print('<div class="alert alert-danger"  >'._("Failed to $vlan[action] VLAN").'!</div>'); }
	else 										{ print('<div class="alert alert-success">'._("VLAN $vlan[action] successfull").'!</div><p id="vlanidforonthefly" style="display:none">'.$id.'</p>'); }
}

?>