<?php

/**
 * Script to display widget edit
 *************************************/
 
/* required functions */
require_once('../../functions/functions.php'); 

/* verify that user is admin */
checkAdmin();

/* filter input */
$_POST = filter_user_input($_POST, true, true, false);
$_POST['action'] = filter_user_input($_POST['action'], false, false, true);

/* must be numeric */
if($_POST['action']=="edit"||$_POST['action']=="delete") {
	if(!is_numeric($_POST['wid']))	{ die('<div class="alert alert-danger">'._("Invalid ID").'</div>'); }
}

/* Title and path must be present! */
if($_POST['action']!="delete") {
if(strlen($_POST['wtitle'])==0 || strlen($_POST['wfile'])==0) {
	die("<div class='alert alert-danger'>"._("Filename and title are mandatory")."!</div>");
}
}

/* Remove .php form wfile if it is present */
$_POST['wfile'] = str_replace(".php","",trim(@$_POST['wfile']));

/* try to execute */
if(!modifyWidget($_POST)) 	{ print "<div class='alert alert-danger'  >"._("Widget $_POST[action] error")."!</div>"; }
else 						{ print "<div class='alert alert-success'>"._("Widget $_POST[action] success")."!</div>"; }

?>