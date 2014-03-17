<?php

/**
 *
 * Script to verify userentered input and verify it against database
 *
 * If successfull write values to session and go to main page! 
 *
 */


/* require scripts */
require_once('../../functions/functions-install.php');

/* fetch username / pass if they are provided */
if( !empty($_POST['ipamusername']) && !empty($_POST['ipampassword']) )  {

	// sanitize inputs
	//$_POST['ipamusername'] = sanitize($_POST['ipamusername']);	

	$ipamusername = $_POST['ipamusername'];
	$ipampassword['raw'] = $_POST['ipampassword'];
	$ipampassword['md5'] = md5($_POST['ipampassword']);
	
	// verify that there are no invalid characters
	if(strpos($ipamusername, " ") >0 ) 			{ die("<div class='alert alert-danger'>"._("Invalid characters in username")."!</div>"); }
	if(strpos($$ipampassword['raw'], " ") >0 ) 	{ die("<div class='alert alert-danger'>"._("Invalid characters in password")."!</div>"); }
	
	/* check local login */
	checkLogin ($ipamusername, $ipampassword['md5'], $ipampassword['raw']);
}
//Username / pass not provided
else {
	die('<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">×</button>'._('Please enter your username and password').'!</div>');
}

?>