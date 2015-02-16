<?php

/**
 * Script to display language edit
 *************************************/
 
/* required functions */
require_once('../../functions/functions.php'); 

/* verify that user is admin */
checkAdmin();

/* filter input */
$_POST = filter_user_input($_POST, true, true, false);
$_POST['action'] = filter_user_input($_POST['action'], false, false, true);

/* verify that description is present if action != delete */
if($_POST['action'] != "delete" && strlen($_POST['l_code']) < 2)	{ die("<div class='alert alert alert-danger'>">_('Code must be at least 2 characters long')."!</div>"); }
if($_POST['action'] != "delete" && strlen($_POST['l_name']) < 2)	{ die("<div class='alert alert alert-danger'>">_('Name must be at least 2 characters long')."!</div>"); }

/* try to execute */
if(!modifyLang($_POST)) { print "<div class='alert alert alert-danger'  >"._("Language $_POST[action] error")."!</div>"; }
else 					{ print "<div class='alert alert-success'>"._("Language $_POST[action] success")."!</div>"; }

?>