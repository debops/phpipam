<?php

/**
 * Script to display widget edit
 *************************************/
 
/* required functions */
require_once('../../functions/functions.php'); 

/* verify that user is admin */
checkAdmin();

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