<?php

/**
 * Script tomanage custom IP fields
 ****************************************/

/* required functions */
require_once('../../functions/functions.php'); 

/* verify that user is admin */
checkAdmin();


/* some verifications */
if( (empty($_POST['current'])) || (empty($_POST['next'])) ) 					{ die('<div class="alert alert-error">'._('Fileds cannot be empty').'!</div>'); }


/* reorder */
if(!reorderCustomField($_POST['table'], $_POST['next'], $_POST['current'])) 	{ die('<div class="alert alert-error">'._('Reordering failed').'!</div>');	 }
else 																			{ print '<div class="alert alert-success">'._('Fields reordered successfully').'!</div>';}

?>