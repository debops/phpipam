<?php

/**
 * HomePage display script
 *  	show somw statistics, links, help,...
 *******************************************/

/* verify login and permissions */
isUserAuthenticated(); 

?>
<script type="text/javascript">
//show clock
$(function($) {
	$('span.jclock').jclock();
});
</script>


<!-- charts -->
<script language="javascript" type="text/javascript" src="js/flot/jquery.flot.js"></script>
<script language="javascript" type="text/javascript" src="js/flot/jquery.flot.categories.js"></script>
<!--[if lte IE 8]><script language="javascript" type="text/javascript" src="js/flot/excanvas.min.js"></script><![endif]-->


<div class="welcome">
<b><?php $user = getActiveUserDetails(); print_r($user['real_name']); ?></b>, <?php print _('welcome to your IPAM dashboard'); ?>. <span class="jclock pull-right"></span>
</div>

<?php
/* print number of requests if admin and if they exist */
$requestNum = countRequestedIPaddresses();
if( ($requestNum != 0) && (checkAdmin(false,false))) {
	print '<div class="alert alert-info">'._('There are').' <b><a href="administration/manageRequests/" id="adminRequestNotif">'. $requestNum .' '._('requests').'</a></b> '._('for IP address waiting for your approval').'!</div>';
}
?>


<?php

# show user-selected widgets
$uwidgets = array_filter(explode(";",$user['widgets']));
$uwidgetschunk = array_chunk($uwidgets, 2);		//chunk it into 2

# get all widgets
if($user['role']=="Administrator") 	{ $widgets = getAllWidgets(true); }
else								{ $widgets = getAllWidgets(false); } 

# print
foreach($uwidgetschunk as $w) {

	print '<div class="row-fluid">';

	//first
	$wdet = $widgets[$w[0]];
	if(array_key_exists($w[0], $widgets)) {
	print "	<div class='span6' id='w-$w[0]'>";
	print "	<div class='inner'>";
	print "		<h4>"._($wdet)."</h4>";
	print "		<div class='hContent'>";
	print "		<div style='text-align:center;padding-top:50px;'><strong>"._('Loading statistics')."</strong><br><img src='css/images/loading_dash.gif'></div>";
	print "		</div>";
	print "	</div>";
	print "	</div>";
	}
	else {
	print "	<div class='span6' id='w-$w[0]'>";
	print "	<div class='inner'>";
	print "	<blockquote style='margin-top:20px;margin-left:20px;'><p>Invalid widget $w[0]</p></blockquote>";
	print "	</div>";
	print "	</div>";
	}
	
	//second
	if(isset($w[1])) {
	$wdet = $widgets[$w[1]];
	if(array_key_exists($w[1], $widgets)) {
	print "	<div class='span6' id='w-$w[1]'>";
	print "	<div class='inner'>";
	print "		<h4>"._($wdet)."</h4>";
	print "		<div class='hContent'>";
	print "		<div style='text-align:center;padding-top:50px;'><strong>"._('Loading statistics')."</strong><br><img src='css/images/loading_dash.gif'></div>";
	print "		</div>";
	print "	</div>";
	print "	</div>";
	}
	else {
	print "	<div class='span6' id='w-$w[1]'>";
	print "	<div class='inner'>";
	print "	<blockquote style='margin-top:20px;margin-left:20px;'><p>Invalid widget $w[1]</p></blockquote>";
	print "	</div>";
	print "	</div>";
	}
	}
	
	print "</div>";
}

# empty
if(sizeof($uwidgets)==0) {
	print "<br><div class='alert alert-warning'><strong>"._('No widgets selected')."!</strong> <hr>"._('Please select widgets to be displayed on dashboard on user menu page')."!</div>";
}

?>
<hr>