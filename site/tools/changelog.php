<?php

/**
 * Script to display available VLANs
 */

/* required functions */
if(!function_exists('getSubnetStatsDashboard')) {
require_once( dirname(__FILE__) . '/../../../functions/functions.php' );
}

/* verify that user is authenticated! */
isUserAuthenticated ();

# header
print "<h4 style='margin-top:30px;'>"._('Changelog')."</h4><hr>";

# if enabled
if($settings['enableChangelog'] == 1) {	
	# set default size
	if(!isset($_REQUEST['climit']))	{ $_REQUEST['climit'] = 50; }
?>
	
	<!-- filter -->
	<form name='cform' id='cform' class='form-inline'>
		<div class='input-group pull-right' style='margin-bottom:20px;'>
		
		<div class='form-group'>
			<select name='climit' class='input-sm climit form-control'>
			<?php
			$printLimits = array(50,100,250,500);
			foreach($printLimits as $l) {
				if($l == $_REQUEST['climit'])	{ print "<option value='$l' selected='selected'>$l</option>"; }
				else							{ print "<option value='$l'>$l</option>"; }
			}
			?>
			</select>
		</div>
		
		<div class='form-group'>
			<input class='span2 cfilter input-sm form-control' name='cfilter' value='<?php print $_REQUEST['cfilter'];?>' type='text' style='width:150px;'>
			<span class="input-group-btn">
				<input type='submit' class='btn btn-sm btn-default' value='<?php print _('Search');?>'>
			</span>
		</div>
		
		</div>
	</form>
	
	<?php
	# printout
	include_once('changelogPrint.php');
}
else {
	print "<div class='alert alert-info'>"._("Change logging is disabled. You can enable it under administration")."!</div>";
}
?>