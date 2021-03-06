<?php

/** 
 * Edit custom IP field
 ************************/

/* required functions */
require_once('../../functions/functions.php'); 

/* verify that user is admin */
checkAdmin();

/* prevent XSS in action */
$_POST['action'] = filter_user_input ($_POST['action'], false, true, true);

/* checks */
if($_POST['action'] == "delete") {
	# no cehcks
}
else {
	# remove spaces
	$_POST['name'] = trim($_POST['name']);
	
	# length > 4 and < 12
	if( (strlen($_POST['name']) < 2) || (strlen($_POST['name']) > 24) ) 	{ $errors[] = _('Name must be between 4 and 24 characters'); }
	
	/* validate HTML */
	
	# must not start with number
	if(is_numeric(substr($_POST['name'], 0, 1))) 							{ $errors[] = _('Name must not start with number'); }		

	# only alphanumeric and _ are allowed
	if(!preg_match('!^[\w_ ]*$!', $_POST['name'])) 							{ $errors[] = _('Only alphanumeric, spaces and underscore characters are allowed'); }
	
	# db type validations
	
	//boolean
	if($_POST['fieldType']=="bool")	{
		if($_POST['fieldSize']!=0 && $_POST['fieldSize']!=1)				{ $errors[] = _('Boolean values can only be 0 or 1'); }
		if($_POST['fieldDefault']!=0 && $_POST['fieldDefault']!=1)			{ $errors[] = _('Default boolean values can only be 0 or 1'); }
	}
	//varchar
	elseif($_POST['fieldType']=="varchar") {
		if(!is_numeric($_POST['fieldSize']))								{ $errors[] = _('Fieldsize must be numeric'); }
		if($_POST['fieldSize']>256)											{ $errors[] = _('Varchar size limit is 256 characters'); }
	}
	//number
	elseif($_POST['fieldType']=="int") {
		if(!is_numeric($_POST['fieldSize']))								{ $errors[] = _('Interer values must be numeric'); }

	}
}

/* die if errors otherwise execute */
if(sizeof($errors) != 0) {
	print '<div class="alert alert alert-danger">'._('Please correct the following errors').':'. "\n";
	print '<ul>'. "\n";
	foreach($errors as $error) {
		print '<li style="text-align:left">'. $error .'</li>'. "\n";
	}
	print '</ul>'. "\n";
	print '</div>'. "\n";
}
else {
	if(!updateCustomField($_POST)) 	{ print '<div class="alert alert alert-danger"  >'._("Failed to $_POST[action] field").'!</div>';}
	else 							{ print '<div class="alert alert-success">'._("Field $_POST[action] success").'!</div>';}
}

?>