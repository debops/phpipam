<?php

/** 
 * set which custom field to display
 ************************/


/* required functions */
require_once('../../functions/functions.php'); 

/* verify that user is admin */
checkAdmin();

/* escape vars to prevent SQL injection */
$_POST = filter_user_input ($_POST, true, true);

# set table name
$table = $_POST['table'];
unset($_POST['table']);

/* enthing to write? */
if(sizeof($_POST)>0) {
	foreach($_POST as $k=>$v) {
		$filtered[] = $k;
	}
}
else {
	$filtered[] = null;
}

/* save */
if(!save_filtered_custom_fields($table, $filtered))	{  }
else												{ print "<div class='alert alert-success'>"._('Fields saved')."</div>"; }
?>