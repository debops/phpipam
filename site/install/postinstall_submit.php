<?php

/**
 *	Post-installation submit
 */

require('../../functions/functions.php');

/* sanitize */
$_POST = filter_user_input ($_POST, true, true, false);

/* only permit if Admin user has default pass !!! */
$admin = getUserDetailsByName ("Admin");
if($admin['password']!='$6$rounds=3000$JQEE6dL9NpvjeFs4$RK5X3oa28.Uzt/h5VAfdrsvlVe.7HgQUYKMXTJUsud8dmWfPzZQPbRbk8xJn1Kyyt4.dWm4nJIYhAV2mbOZ3g.') {
	die("<div class='alert alert-danger'>Not allowed !</div>");
}
else {
	/* check lenghts */
	if(strlen($_POST['password1'])<8)				{ die("<div class='alert alert-danger'>"._("Invalid password")."</div>"); }
	if(strlen($_POST['password2'])<8)				{ die("<div class='alert alert-danger'>"._("Invalid password")."</div>"); }
	
	/* check match */
	if($_POST['password1']!=$_POST['password2'])	{ die("<div class='alert alert-danger'>"._("Passwords do not match")."</div>"); }
	
	/* Crypt password */
	$_POST['password1'] = crypt_user_pass($_POST['password1']);
	
	/* all good, update password! */
	if(!postauth_update($_POST['password1'], $_POST['siteTitle'], $_POST['siteURL']))	{ }
	else											{ print "<div class='alert alert-success'>Settings updated, installation complete!<hr><a class='btn btn-sm btn-default' href='".create_link("login")."'>Proceed to login</a>"; }

}
?>