<?php

/*
 *	Script to upgrade database
 **************************************/

/* use required functions */
require_once('../../config.php');
require_once('../../functions/functions.php');

/* get all site settings */
$settings = getAllSettings();

/* display only to admin users */
if(!checkAdmin(false)) { die('<div class="alert alert alert-danger">Admin user required!</div>'); }

/* get version */
$version = $settings['version'];

/* try to upgrade database */
if(upgradeDatabase($version)) {
	print '<div class="alert alert-success">Database upgraded successfully!</div>';
}

# check for possible errors
$errors = verifyDatabase();	
if( (isset($errors['tableError'])) || (isset($errors['fieldError'])) ) 	{ 

	print '<div class="alert alert-danger">'. "\n";

	# print errors
	if (isset($errors['tableError'])) {
		print '<strong>'._('Missing table').'s:</strong>'. "\n";
		print '<ul class="fix-table">'. "\n";
		
		foreach ($errors['tableError'] as $table) {
			print '<li>'.$table.'</li>'. "\n";
		}
		print '</ul>'. "\n";	
	}
	
	//fields
	if (isset($errors['fieldError'])) {
		print '<strong>'._('Missing fields').':</strong>'. "\n";
		print '<ul class="fix-field">'. "\n";
		foreach ($errors['fieldError'] as $table=>$field) {
			print '<li>Table `'. $table .'`: missing field `'. $field .'`;</li>'. "\n";
		}
		print '</ul>'. "\n";
	}
	print "</div>";
}
# all good
else { 
	print "<div class='alert alert-success'>Database verification succesfull, all fields are installed properly! <a class='btn btn-sm btn-default' href='".create_link("login")."'>"._('Login')."</a></div>";  
}
?>