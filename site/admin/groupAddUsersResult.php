<?php

/**
 * Script to display usermod result
 *************************************/
 
/* required functions */
require_once('../../functions/functions.php'); 

/* verify that user is admin */
checkAdmin();

/* parse result */
foreach($_POST as $k=>$p) {
	if(substr($k, 0,4) == "user") {
		$users[substr($k, 4)] = substr($k, 4);
	}
} 


/* verify that description is present if action != delete */
if(strlen($_POST['gid'] == 0))	{ die("<div class='alert alert-error'>"._('Error - no group ID')."</div>"); }

/* add each user to group */
if(sizeof($users)>0) {
	foreach($users as $key=>$u) {
		if(!addUserToGroup($_POST['gid'], $u)) {
			# get user details
			$user = getUserDetailsById($u);
			$errors[] = $user['real_name'];
		}
	}
}
else {
	$errors[] = _("Please select user(s) to add to selected group!");
}

/* print result */
if(isset($errors)) {
	print "<div class='alert alert-error'>";
	print _("Failed to add users").":<hr>";
	print "<ul>";
	foreach($errors as $e) {
		print "<li>$e</li>";
	}
	print "</ul>";
	print "</div>";
}
else {
	print "<div class='alert alert-success'>"._('Users added to group')."</div>";
}

?>