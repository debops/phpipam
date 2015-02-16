<?php

/**
 *
 * Script to verify userentered input and verify it against database
 *
 * If successfull write values to session and go to main page! 
 *
 */


/* require scripts */
require_once('../../config.php');
require_once('../../functions/functions-install.php');

/* fetch username / pass if they are provided */
if( !empty($_POST['ipamusername']) && !empty($_POST['ipampassword']) )  {

	# initialize array
	$ipampassword = array();
	
	// verify that there are no invalid characters
	if(strpos($_POST['ipamusername'], " ") >0 ) 	{ die("<div class='alert alert-danger'>"._("Invalid characters in username")."!</div>"); }
	
	// check failed table
	if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))	{ $ip = $_SERVER['HTTP_X_FORWARDED_FOR']; }
	else										{ $ip = $_SERVER['REMOTE_ADDR']; }
	$cnt = check_blocked_ip ($ip);
	
	# check for failed logins and captcha
	if($cnt<5) {  }
	elseif(!isset($_POST['captcha'])) {
		updateLogTable ("Login IP blocked", "Login from IP address $ip was blocked because of 5 minute block after 5 failed attempts", 1);
		die('<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">×</button>'._('You have been blocked for 5 minutes due to authentication failures').'!</div>');
	}
	# captcha check
	else {
		# start session
		if(strlen($phpsessname)>0) { session_name($phpsessname); } 
		session_start();
		# check captcha
		if($_POST['captcha']!=$_SESSION['securimage_code_value']) {
												   die("<div class='alert alert-danger'>"._("Invalid security code")."!</div>");
		}
		session_write_close();
	}
		
	/* check local login */
	checkLogin ($_POST['ipamusername'], md5($_POST['ipampassword']), $_POST['ipampassword']);
}
//Username / pass not provided
else {
	die('<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">×</button>'._('Please enter your username and password').'!</div>');
}

?>
