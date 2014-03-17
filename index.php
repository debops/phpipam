<?php

/* set cookie parameters for max lifetime */
/*
ini_set('session.gc_maxlifetime', '86400');
ini_set('session.save_path', '/tmp/php_sessions/');
*/

session_start();
ob_start();

/* site config */
require('config.php');

/* site functions */
require('functions/functions.php');

# set default page
if(!isset($_REQUEST['page'])) { $_REQUEST['page'] = "dashboard"; }

/* check for new installation */
if($_REQUEST['page'] != "install") { require('functions/dbInstallCheck.php'); }
if($_REQUEST['page'] == "install") { 
	$settings['siteTitle'] = "phpIPAM"; 
}
else {
	/* get all site settings */
	$settings = getAllSettings();
}

/* verify login and permissions */
if($_REQUEST['page'] != "login" && $_REQUEST['page'] != "request_ip"  && $_REQUEST['page'] != "upgrade" && $_REQUEST['page'] != "install") { isUserAuthenticatedNoAjax(); }


if($_REQUEST['page'] != 'upgrade' && $_REQUEST['page'] != "login" && $_REQUEST['page'] != "install") { 
	include('functions/dbUpgradeCheck.php'); 	# check if database needs upgrade 
	include('functions/checkPhpBuild.php');		# check for support for PHP modules and database connection 
}

/* recreate base */
if($_SERVER['SERVER_PORT'] == "443") 		{ $url = "https://$_SERVER[SERVER_NAME]".BASE; }
/* custom port */
elseif($_SERVER['SERVER_PORT'] != "80")  	{ $url = "http://$_SERVER[SERVER_NAME]:$_SERVER[SERVER_PORT]".BASE; }
/* normal http */
else								 		{ $url = "http://$_SERVER[SERVER_NAME]".BASE; }

/* site header */
?>
<!DOCTYPE HTML>
<html lang="en">

<head>
	<base href="<?php print $url; ?>" />

	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta http-equiv="Cache-Control" content="no-cache, must-revalidate">
	
	<meta name="Description" content=""> 
	<meta name="title" content="<?php print $settings['siteTitle']; ?>"> 
	<meta name="robots" content="noindex, nofollow"> 
	<meta http-equiv="X-UA-Compatible" content="IE=9" >
	
	<meta name="viewport" content="width=device-width, initial-scale=0.7, maximum-scale=1, user-scalable=no">
	
	<!-- chrome frame support -->
	<meta http-equiv="X-UA-Compatible" content="chrome=1">
  
	<!-- title -->
	<title><?php print $settings['siteTitle']; ?></title>
	
	<!-- css -->
	<link rel="stylesheet" type="text/css" href="css/bootstrap/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="css/bootstrap/bootstrap-custom.css">
	<link rel="stylesheet" type="text/css" href="css/font-awesome/font-awesome.min.css">
	<link rel="shortcut icon" href="css/images/favicon.ico">
		
	<!-- js -->
	<script type="text/javascript" src="js/jquery-1.9.1.min.js"></script>
	<script type="text/javascript" src="js/jclock.jquery.js"></script>
	<script type="text/javascript" src="js/login.js"></script>
	<script type="text/javascript" src="js/magic-1.0.min.js"></script>
<!-- 	<script type="text/javascript" src="js/magic-1.0.js"></script> -->
	<script type="text/javascript" src="js/bootstrap.min.js"></script>
	<script type="text/javascript" src="js/jquery-ui-1.10.4.custom.min.js"></script>
	<script type="text/javascript">
	$(document).ready(function(){
	     if ($("[rel=tooltip]").length) { $("[rel=tooltip]").tooltip(); }
	});
	</script>
	<!--[if lt IE 9]>
	<script type="text/javascript" src="js/dieIE.js"></script>
	<![endif]-->
</head>

<!-- body -->
<body>

<!-- wrapper -->
<div class="wrapper">

<!-- jQuery error -->
<div class="jqueryError">
	<div class='alert alert-danger' style="width:400px;margin:auto">jQuery error!</div>
	<div class="jqueryErrorText"></div><br>
	<a href="" class="btn btn-sm btn-default" id="hideError" style="margin-top:0px;">Hide</a>
</div>

<!-- Popups -->
<div id="popupOverlay"></div>
<div id="popup" class="popup popup_w400"></div>
<div id="popup" class="popup popup_w500"></div>
<div id="popup" class="popup popup_w700"></div>

<!-- loader -->
<div class="loading"><?php print _('Loading');?>...<br><i class="fa fa-spinner fa-spin"></i></div>


<!-- helpers -->
<!--
<div class="visible-xs">XS</div>
<div class="visible-sm">SM</div>
<div class="visible-md">MD</div>
<div class="visible-lg">LG</div>
-->

<!-- header -->
<div class="row" id="header">

	<!-- usermenu -->
	<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12 pull-right" id="user_menu">
		<?php if($_REQUEST['page'] != "login" && $_REQUEST['page'] != "logout" && $_REQUEST['page'] != "request_ip" && $_REQUEST['page'] != "upgrade" && $_REQUEST['page'] != "install") include('site/userMenu.php');?>
	</div>
  
 
	<!-- title -->
	<div class="col-lg-6 col-lg-offset-3 col-md-6 col-md-offset-3 col-sm-12 col-xs-12">
		<div class="hero-pusher hidden-xs hidden-sm"></div>
		<div class="hero-unit">
			<a href=""><?php print $settings['siteTitle']; if($_REQUEST['page'] == "login") { print " | "._('login'); } if($_REQUEST['page'] == "install") { print " | "._('installation'); } ?></a>
		</div>
	</div>
	
</div>  


<!-- page sections / menu -->
<div class="content">
<div id="sections_overlay">
    <?php if($_REQUEST['page'] != "login" && $_REQUEST['page'] != "logout" && $_REQUEST['page'] != "request_ip" && $_REQUEST['page'] != "upgrade" && $_REQUEST['page'] != "install")  include('site/sections.php');?>
</div>
</div>


<!-- content -->
<div class="content_overlay">
<div class="container-fluid" id="mainContainer">
		<?php
		/* error */
		if($_REQUEST['page'] == "error") {
			print "<div id='error'>";
			include_once('site/error.php');
			print "</div>";
		}
		/* upgrade */
		elseif ($_REQUEST['page'] == "upgrade") {
			print "<div id='dashboard'>";
			include_once("site/upgrade/index.php");
			print "</div>";			
		}
		/* install */
		elseif ($_REQUEST['page'] == "install") {
			print "<div id='dashboard'>";
			include_once("site/install/index.php");
			print "</div>";			
		}
		/* login, logout, ipRequest */
		elseif($_REQUEST['page'] == "login" || $_REQUEST['page'] == "logout" || $_REQUEST['page'] == "request_ip") {
			print "<div id='dashboard'>";
			include_once("site/login/index.php");
			print "</div>";			
		}
		/* dashboard */
		elseif(!isset($_REQUEST['page']) || $_REQUEST['page'] == "dashboard") {
			print "<div id='dashboard'>";
			include_once("site/dashboard/index.php");
			print "</div>";
		}
		/* widgets */
		elseif(@$_REQUEST['page']=="widgets") {
			print "<div id='dashboard'>";
			include_once("site/dashboard/widgets/".$_REQUEST['subpage'].".php");
			print "</div>";			
		}
		/* side menus */
		else {
			print "<table id='subnetsMenu'>";
			print "<tr>";
			
			print "<td id='subnetsLeft'>";
			print "<div id='leftMenu' class='menu-$_REQUEST[page]'>";
				if($_REQUEST['page'] == "subnets" || $_REQUEST['page'] == "vlan" || 
				   $_REQUEST['page'] == "vrf" 	  || $_REQUEST['page'] == "folder")										{ include_once("site/subnets.php"); }
				else if ($_REQUEST['page'] == "tools")																	{ include_once("site/tools/toolsMenu.php"); }
				else if ($_REQUEST['page'] == "administration")															{ include_once("site/admin/adminMenu.php"); }	
			print "</div>";		
			print "</td>";
			
			print "<td id='subnetsContent'>";
			print "<div class='row' id='content'>";
				if( isset($_REQUEST['toolsId']) && (strlen($_REQUEST['toolsId']) == 0) )	{ unset($_REQUEST['toolsId']); }
				# subnet changelog
				if($_REQUEST['page'] == "subnets" && $_REQUEST['sPage'] == "changelog")									{ include_once("site/ipaddr/subnetChangelog.php"); }
				# subnets
				elseif($_REQUEST['page'] == "subnets" && !isset($_REQUEST['subnetId']))									{ include_once("site/ipaddr/sectionAllSubnets.php"); }
				# subnets, vrf, vlan, folder
				else if($_REQUEST['page'] == "subnets" || $_REQUEST['page'] == "vlan" 
					 || $_REQUEST['page'] == "vrf"	   || $_REQUEST['page'] == "folder")								{ include_once("site/ipaddr/ipAddressSwitch.php"); }
				# tools		
				else if ($_REQUEST['page'] == "tools" && !isset($_REQUEST['toolsId']))									{ print "<div class='alert alert-info alert-dash'><i class='icon-gray icon-chevron-left'></i> "._('Please select tool from left menu!')."</div>"; }
				else if ($_REQUEST['page'] == "tools")																	{ include_once("site/tools/$_REQUEST[toolsId].php"); }
				# admin
				else if ($_REQUEST['page'] == "administration"  && !isset($_REQUEST['adminId']))						{ print "<div class='alert alert-info alert-dash'><i class='icon-gray icon-chevron-left'></i> "._('Please select setting from left menu!')."</div>"; }    	
				else if ($_REQUEST['page'] == "administration")															{ include_once("site/admin/$_REQUEST[adminId].php"); }    	
			print "</div>";
			print "</td>";
			
			print "</tr>";
			print "</table>";
    	}
    	?>
      	
</div>
</div>

<!-- pusher -->
<div class="pusher"></div>

<!-- end wrapper -->
</div>

<!-- Page footer -->
<div class="footer"><?php include('site/footer.php'); ?></div>

<!-- export div -->
<div class="exportDIV"></div>

<!-- end body -->
</body>
</html>
<?php ob_end_flush(); ?>