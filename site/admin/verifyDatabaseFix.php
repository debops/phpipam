<?php

/**
 * Script to fix missing db fields
 ****************************************/
 
/* required functions */
require_once('../../functions/functions.php'); 

/* verify that user is admin */
checkAdmin();

/* verifications */
if(!isset($_POST['tableid']) || strlen(@$_POST['tableid'])<1 ) {
	die("<div class='alert alert-danger'>"._("Wrong parameters")."</div>");
}
else {
	//fix table
	if($_POST['type'] == "table") {
		fixTable($_POST['tableid']);
		print "<div class='alert alert-success'>"._('Table fixed')."!</div>";
	}
	//fix field
	elseif($_POST['type'] == "field") {
		fixField($_POST['tableid'], $_POST['fieldid']);	
		print "<div class='alert alert-success'>"._('Field fixed')."!</div>";
	}
	else {
		die("<div class='alert alert-danger'>"._("Wrong parameters")."</div>");
	}
}
?>