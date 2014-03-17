<?php

/* show available widgets */
require(dirname(__FILE__) . '../../../functions/functions.php');

/* verify that user is authenticated! */
isUserAuthenticated ();

/* get username */
$ipamusername = getActiveUserDetails ();

//user widgets form database
$uwidgets = explode(";",$ipamusername['widgets']);	//selected
$uwidgets = array_filter($uwidgets);

# get all widgets
if($ipamusername['role']=="Administrator") 	{ $widgets  = getAllWidgets(true, false); } 
else 										{ $widgets  = getAllWidgets(false, false); }
		
?>

<!-- header -->
<div class="pHeader"><?php print _('Add new widget to dashboard'); ?></div>

<!-- content -->
<div class="pContent">
	<?php
	print "<ul id='sortablePopup' class='sortable'>";
	# print widghets that are not yet selected
	$m = 0;
	foreach($widgets as $k=>$w) {
		if(!in_array($k, $uwidgets))	{ 
			$wtmp = $widgets[$k];
			//size fix
			if(strlen($wtmp['wsize'])==0)	{ $wtmp['wsize']=6; }
			print "<li id='$k'>";
			print "	<a href='' class='btn btn-xs fa-marg-right  btn-default widget-add' id='w-$wtmp[wfile]' data-size='$wtmp[wsize]' data-htitle='$wtmp[wtitle]'><i class='fa fa-plus'></i></a>"._($wtmp['wtitle']);
			print "	<div class='muted' style='margin-left:27px;'>"._($wtmp['wdescription'])."</div>";
			print "</li>"; 
			$m++;
		}
	}	
	print "</ul>";
		
	# print empty
	if($m==0)	{ print "<div class='alert alert-info'>"._("All available widgets are already on dashboard")."!</div>"; }
	?>
</div>

<!-- footer -->
<div class="pFooter">
	<button class="btn btn-sm btn-default hidePopups"><?php print _('Cancel'); ?></button>
</div>