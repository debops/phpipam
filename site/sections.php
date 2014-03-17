<!--[if lt IE 9]>
<style type="text/css">
.tooltipBottom,
.tooltipLeft,
.tooltipTop,
.tooltipTopDonate,
.tooltip,
.tooltipRightSubnets { 
	filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#e61d2429', endColorstr='#b3293339',GradientType=0 );
}
.tooltipBottom {
	filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#e61d2429', endColorstr='#b3293339',GradientType=0 );
}
</style>
<![endif]-->


<?php

/**
 * Script to print sections and admin link on top of page
 ********************************************************/

/* use scripts, but only if requested through post! */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once( dirname(__FILE__) . '/../functions/functions.php' );
}

/* verify that user is authenticated! */
isUserAuthenticated ();

/* fetch result */
$sections = fetchSections ();

/* get all selected fields for IP print */
$setFieldsTemp = getSelectedIPaddrFields();
/* format them to array! */
$setFields = explode(";", $setFieldsTemp);

?>

<!-- Section nabvigation -->
<div class="navbar" id="menu">
<nav class="navbar navbar-default" id="menu-navbar" role="navigation">

	<!-- Collapsed display for mobile -->
	<div class="navbar-header">
		<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#menu-collapse">
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
		</button>
		<span class="navbar-brand visible-xs"><?php print _("Subnets menu"); ?></span>
	</div>
		

	<!-- menu -->
	<div class="collapse navbar-collapse" id="menu-collapse">
	

	<!-- sections -->
	<ul class="nav navbar-nav sections">
		<?php
		# if section is not set
		if(!isset($_REQUEST['section'])) { $_REQUEST['section'] = ""; }
		
		$n=0;		//count of sections for empty!
		
		foreach($sections as $section) {
			# check permissions for user
			$perm = checkSectionPermission ($section['id']);
			if($perm > 0 ) {
				$n++;
			
				# print only masters!
				if($section['masterSection']=="0" || empty($section['masterSection'])) {
				
					# check if has slaves
					unset($sves);
					foreach($sections as $s) {
						if($s['masterSection']==$section['id']) { $sves[$s['id']] = $s; }
					}
					
					# slaves?
					if(isset($sves)) {
				
						print "<li class='dropdown'>";						
						
						
						print " <a class='dropdown-toggle' data-toggle='dropdown'>$section[name]<b class='caret' style='maring-top:0px;margin-left:5px;'></b></a>";
						print "		<ul class='dropdown-menu tools'>";	
						
						//section
						if($_REQUEST['section']==$section['id'])	{ print "<li class='active'><a href='subnets/$section[id]/'>$section[name]</a></li>"; }
						else										{ print "<li><a href='subnets/$section[id]/'>$section[name]</a></li>"; }
												
						print "			<li class='divider'></li>";	

						//subsections
						foreach($sves as $sl) {
							if($_REQUEST['section']==$sl['id']) { print "<li class='active'><a href='subnets/$sl[id]/'><i class='fa fa-angle-right'></i> $sl[name]</a></li>"; }
							else								{ print "<li><a href='subnets/$sl[id]/'><i class='fa fa-angle-right'></i> $sl[name]</a></li>"; }
						}
						
						print "		</ul>";
						print "</li>";
					}
					# no slaves
					else {
						if( ($section['name'] == $_REQUEST['section']) || ($section['id'] == $_REQUEST['section']) ) 	{ print "<li class='active'>"; }
						else 																							{ print "<li>"; }	

						print "	<a href='subnets/$section[id]/' rel='tooltip' data-placement='bottom' title='"._('Show all subnets in section')." $section[name]'>$section[name]</a>";
						print "</li>";	
					}
				}
			}
		}
		
		# empty
		if($n==0)	{ print "No sections available!"; }
		?>
	</ul>	


    <?php
    # print admin menu if admin user and don't die!
	if(checkAdmin(false)) {
		# if adminId is not set
		if(!isset($_REQUEST['adminId'])) { $_REQUEST['adminId'] = ""; }
	
		print "<ul class='nav navbar-nav navbar-right'>";
		print "	<li class='dropdown administration'>";
		# title
		print "	<a class='dropdown-toggle btn-danger' data-toggle='dropdown' href='administration/' id='admin' rel='tooltip' data-placement='bottom' title='"._('Show Administration menu')."'><i class='fa fa-cog'></i> "._('Administration')." <b class='caret'></b></a>";
		# dropdown
		print "		<ul class='dropdown-menu admin'>";
		
		# show IP request link if enabled in config file!
		if($settings['enableIPrequests'] == 1) {    
			$requestNum = countRequestedIPaddresses();
			if($requestNum != 0) {
				print "<li class='nav-header'>IP address requests</li>";
				print "<li "; if($_REQUEST['adminId'] == "manageRequests") print "class='active'"; print "><a href='administration/manageRequests/'>"._('IP requests')." ($requestNum)</a></li>";
				print "<li class='divider'></li>";
			}
		}
		print "		<li class='nav-header'>"._('Server management')."</li>";
		print "		<li "; if($_REQUEST['adminId'] == "manageRequests") print "class='active'"; print "><a href='administration/settings/'>"._('IPAM settings')."</a></li>";
		print "		<li "; if($_REQUEST['adminId'] == "users") 			print "class='active'"; print "><a href='administration/users/'>"._('Users')."</a></li>";
		print "		<li "; if($_REQUEST['adminId'] == "groups") 		print "class='active'"; print "><a href='administration/groups/'>"._('Groups')."</a></li>";
		print "		<li "; if($_REQUEST['adminId'] == "logs") 			print "class='active'"; print "><a href='administration/logs/'>"._('Log files')."</a></li>";

		print "		<li class='divider'></li>";
		print "		<li class='nav-header'>"._('IP related settings')."</li>";
		print "		<li "; if($_REQUEST['adminId'] == "manageSection") 	print "class='active'"; print "><a href='administration/manageSection/'>"._('Sections')."</a></li>";
		print "		<li "; if($_REQUEST['adminId'] == "manageSubnet") 	print "class='active'"; print "><a href='administration/manageSubnet/'>"._('Subnets')."</a></li>";
		print "		<li "; if($_REQUEST['adminId'] == "manageDevices") 	print "class='active'"; print "><a href='administration/manageDevices/'>"._('Devices')."</a></li>";
		print "		<li "; if($_REQUEST['adminId'] == "manageVLANs") 	print "class='active'"; print "><a href='administration/manageVLANs/'>"._('VLANs')."</a></li>";
		# vrf if enabled
		if($settings['enableVRF'] == 1) { 
		print "		<li "; if($_REQUEST['adminId'] == "manageVRF") 		print "class='active'"; print "><a href='administration/manageVRF/'>"._('VRF')."</a></li>";
		}
		print "		<li class='divider'></li>";
		print "		<li><a href='administration/'>"._('Show all settings')."</a></li>";		
		print "		</ul>";
		
		print "	</li>";
		print "</ul>";
	}
    
    ?>


	<!-- Tools (for small menu) -->
	<ul class="nav navbar-nav visible-xs visible-sm navbar-right">
		<li class="dropdown">
			<a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php print _('Tools'); ?> <b class="caret"></b></a>
			<ul class="dropdown-menu">

    			<?php
    				# if adminId is not set
    				if(!isset($_REQUEST['toolsId'])) { $_REQUEST['toolsId'] = ""; }
		    		
		    		print "	<li "; if($_REQUEST['toolsId'] == "ipCalc") 	print "class='active'"; print "><a href='tools/ipCalc/'>"._('IP calculator')."</a></li>"; 
			    	print "	<li "; if($_REQUEST['toolsId'] == "devices") 	print "class='active'"; print "><a href='tools/devices/'>"._('Devices')."</a></li>";
			    	if($settings['enableVRF'] == 1) {									# print VRFs if enabled
			    	print "	<li "; if($_REQUEST['toolsId'] == "vrf") 		print "class='active'"; print "><a href='tools/vrf/'>"._('VRFs')."</a></li>"; 
				    }
			    	print "	<li "; if($_REQUEST['toolsId'] == "vlan") 		print "class='active'"; print "><a href='tools/vlan/'>"._('VLANs')."</a></li>"; 	
			    	print "	<li "; if($_REQUEST['toolsId'] == "subnets") 	print "class='active'"; print "><a href='tools/subnets/'>"._('Subnets')."</a></li>"; 
			    	print "	<li "; if($_REQUEST['toolsId'] == "search") 	print "class='active'"; print "><a href='tools/search/'>"._('Search')."</a></li>"; 
			    	print "	<li "; if($_REQUEST['toolsId'] == "instructions") 	print "class='active'"; print "><a href='tools/instructions/'>"._('Show IP addressing guide')."</a></li>"; 
			    	print "	<li "; if($_REQUEST['toolsId'] == "favourites") print "class='active'"; print "><a href='tools/favourites/'>"._('Favourite networks')."</a></li>"; 
			    	if($settings['enableChangelog'] == 1) {								# print enableChangelog if enabled
			    	print "	<li "; if($_REQUEST['toolsId'] == "changelog")  print "class='active'"; print "><a href='tools/changelog/'>"._('Changelog')."</a></li>"; 
					}
			    	print "	<li class='divider'></li>";
			    	print "	<li><a href='tools/'>"._('Show all tools')."</a></li>";	

    			?>

			</ul>
		</li>
	</ul>
	

	<!-- Tools -->
	<ul class="nav navbar-nav navbar-right hidden-xs hidden-sm icon-ul">

		<!-- Dash lock/unlock -->
		<?php if($_REQUEST['page']=="dashboard") { ?>
			<li class="w-lock">
				<a href="#" rel='tooltip' class="icon-li" data-placement='bottom' title="<?php print _('Clik to reorder widgets'); ?>"><i class='fa fa-dashboard'></i></a>
			</li>
		<?php } ?>
		
		<!-- Favourites -->	
		<?php
		//check if user has favourite subnets
		$user = getActiveUserDetails();
		if(strlen(trim($user['favourite_subnets']))>0) {
		?>
		<li class="<?php if($_REQUEST['toolsId']=="favourites") print " active"; ?>">
			<a href="tools/favourites/" class="icon-li" rel='tooltip' data-placement='bottom' title="<?php print _('Favourite networks'); ?>"><i class='fa fa-star-o'></i></a>
		</li>
		<?php } ?>

		<!-- instructions -->
		<li class="<?php if($_REQUEST['toolsId']=="instructions") print " active"; ?>">
			<a href="tools/instructions/" class="icon-li" rel='tooltip' data-placement='bottom' title="<?php print _('Show IP addressing guide'); ?>"><i class='fa fa-info'></i></a>
		</li>
		
		<!-- tools -->
		<li class="tools dropdown <?php if(isset($_REQUEST['toolsId']) && ($_REQUEST['toolsId']!="instructions") && (strlen($_REQUEST['toolsId'])>0) && ($_REQUEST['toolsId']!="favourites")) { print " active"; } ?>">
    		<a class="dropdown-toggle icon-li" data-toggle="dropdown" href="" rel='tooltip' data-placement='bottom' title='<?php print _('Show tools menu'); ?>'><i class="fa fa-wrench"></i></a>
    		<ul class="dropdown-menu tools">
    			<!-- public -->
    			<li class="nav-header"><?php print _('Available IPAM tools'); ?> </li>
    			<!-- private -->
    			<?php
    				# if adminId is not set
    				if(!isset($_REQUEST['toolsId'])) { $_REQUEST['toolsId'] = ""; }
		    		
		    		print "	<li "; if($_REQUEST['toolsId'] == "ipCalc") 	print "class='active'"; print "><a href='tools/ipCalc/'>"._('IP calculator')."</a></li>"; 
			    	print "	<li "; if($_REQUEST['toolsId'] == "devices") 	print "class='active'"; print "><a href='tools/devices/'>"._('Devices')."</a></li>";
			    	if($settings['enableVRF'] == 1) {									# print VRFs if enabled
			    	print "	<li "; if($_REQUEST['toolsId'] == "vrf") 		print "class='active'"; print "><a href='tools/vrf/'>"._('VRFs')."</a></li>"; 
				    }
			    	print "	<li "; if($_REQUEST['toolsId'] == "vlan") 		print "class='active'"; print "><a href='tools/vlan/'>"._('VLANs')."</a></li>"; 	
			    	print "	<li "; if($_REQUEST['toolsId'] == "subnets") 	print "class='active'"; print "><a href='tools/subnets/'>"._('Subnets')."</a></li>"; 
			    	print "	<li "; if($_REQUEST['toolsId'] == "search") 	print "class='active'"; print "><a href='tools/search/'>"._('Search')."</a></li>"; 
			    	print "	<li "; if($_REQUEST['toolsId'] == "favourites") print "class='active'"; print "><a href='tools/favourites/'>"._('Favourite networks')."</a></li>"; 
			    	if($settings['enableChangelog'] == 1) {								# print enableChangelog if enabled
			    	print "	<li "; if($_REQUEST['toolsId'] == "changelog")  print "class='active'"; print "><a href='tools/changelog/'>"._('Changelog')."</a></li>"; 
					}
			    	print "	<li class='divider'></li>";
			    	print "	<li><a href='tools/'>"._('Show all tools')."</a></li>";	

    			?>
    		</ul>
    	</li>
    	
    	<!-- DB verification -->
		<?php
		if(checkAdmin(false) && $settings['dbverified']!=1) {
		//check
		if(sizeof($dberrsize = verifyDatabase())>0) {
			$esize = sizeof($dberrsize['tableError']) + sizeof($dberrsize['fieldError']);
			print "<li>";
			print "	<a href='administration/verifyDatabase/' class='icon-li btn-danger' rel='tooltip' data-placement='bottom' title='"._('Database errors detected')."'><i class='fa fa-exclamation-triangle'></i><sup>$esize</sup></a>";
			print "</li>";		
		} 
		//all good, update flag
		else {
			updateDBverify();
		}
		}
		?>		

		<?php
		/* print number of requests if admin and if they exist */
		$requestNum = countRequestedIPaddresses();
		if( ($requestNum != 0) && (checkAdmin(false,false))) { ?>
		<li>
			<a href="administration/manageRequests/" rel='tooltip' class="icon-li btn-info" data-placement='bottom' title="<?php print $requestNum." "._('requests')." "._('for IP address waiting for your approval'); ?>"><i class='fa fa-envelope-o' style="padding-right:2px;"></i><sup><?php print $requestNum; ?></sup></a>
		</li>
		<?php } ?>
		
		<?php
		//check for new version periodically, 1x/week
		$now = date("Y-m-d H:i:s");
		if( checkAdmin(false) && (strtotime($now) - strtotime($settings['vcheckDate'])) > 604800 ) {
			//check for new version
			if(!$version = getLatestPHPIPAMversion()) {
				//we failed, so NW is not ok. update time anyway to avoid future failures
				updatePHPIPAMversionCheckTime();
			}
			else {
				//new
				if ($settings['version'] < $version) {						
					print "<li>";
					print "	<a href='administration/versionCheck/' class='icon-li btn-warning' rel='tooltip' data-placement='bottom' title='"._('New version available')."'><i class='fa fa-bullhorn'></i><sup>$version</sup></a>";
					print "</li>";	
				}	
				//nothing new
				else {
					updatePHPIPAMversionCheckTime();
				}
			}		
		}
		?>

	</ul>

	</div>	 <!-- end menu div -->
			
</nav>			    
</div>

<?php



?>