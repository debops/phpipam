<?php

/*

This is a template for creating new widgets

*/

/* required functions */
if(!function_exists('getSubnetStatsDashboard')) {
require_once( dirname(__FILE__) . '/../../../functions/functions.php' );
}

/* You can check who requested this, to adjust parameters  */
if($_SERVER['HTTP_X_REQUESTED_WITH']=="XMLHttpRequest")	{ $dashboard = true; }
else													{ $dashboard = false; }

?>

<!-- CSS -->
<style type="text/css">
/* You can write your CSS here */
</style>

<!-- JS -->
<script type="text/javascript">
$(document).ready(function() {
	//if you need some JS write it here, jQuery is already included
	return false;
});
</script>