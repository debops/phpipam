<?php

/** 
 * Function to add / edit / delete section
 *************************************************/

/* required functions */
require_once('../../functions/functions.php'); 

/* filter input */
$_POST = filter_user_input($_POST, true, true, false);

/* verify that user is admin */
checkAdmin();

/* verify post */
CheckReferrer();

/* create array of ordering */
$otmp = explode(";", $_POST['position']);
foreach($otmp as $ot) {
	$ptmp = explode(":", $ot);
	
	$order[$ptmp[0]] = $ptmp[1];
}

/* do action! */
if (UpdateSectionOrder ($order)) {
    print '<div class="alert alert-success">'._("Section reordering successful").'!</div>';
}

?>