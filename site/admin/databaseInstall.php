<?php

/*
 *	Script to upgrade database
 **************************************/

/* use required functions */
require('../../config.php');
require('../../functions/functions-install.php');

# make sure it is properly requested
if($_SERVER['HTTP_X_REQUESTED_WITH']!="XMLHttpRequest")						{ die("<div class='alert alert-danger'>Invalid request!</div>"); }

# if already installed ignore!
if(tableExists("widgets", false)) { 

	require('../../functions/functions-admin.php');
	# check for possible errors
	$errors = verifyDatabase();	
	if( (isset($errors['tableError'])) || (isset($errors['fieldError'])) ) 	{ }
	else 																	{ die("<div class='alert alert-danger'>Database already installed!</div>");  }
}

# get privileged username and pass
$root['user'] = $_POST['mysqlrootuser'];
$root['pass'] = $_POST['mysqlrootpass'];

# get possible advanced
if(@$_POST['dropdb']=="on")			{ $dropdb = true; }
else								{ $dropdb = false; }

if(@$_POST['createdb']=="on")		{ $createdb = true; }
else								{ $createdb = false; }

if(@$_POST['creategrants']=="on")	{ $creategrants = true; }
else								{ $creategrants = false; }

/* try to install new database */
if(installDatabase($root['user'], $root['pass'], $dropdb, $createdb, $creategrants)) {

	print '<div class="alert alert-block alert-success">';
	print 'Database installed successfully! <a href="'.create_link("install", "install_automatic", "configure").'" class="btn btn-sm btn-default">Continue</a>';
	print '</div>';
}

?>