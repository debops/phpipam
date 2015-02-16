<?php
/* site config */
require('config.php');

/* site functions */
require('functions/functions.php');

/* start session */
if(!isset($_SESSION)) {
if(strlen($phpsessname)>0) { session_name($phpsessname); } 
session_start();
ob_start();
}

# set default page
if(!isset($_GET['page'])) { $_GET['page'] = "dashboard"; }

# reset url for base
$url = createURL ();

# if not install fetch settings etc
if($_GET['page']!="install" ) {
	# check if this is a new installation
	require('functions/dbInstallCheck.php');
	
	# get all site settings
	$settings 	= getAllSettings();

	# escape GET vars to prevent SQL injection
	$_GET 		= filter_user_input ($_GET, true, true);
	$_REQUEST 	= filter_user_input ($_REQUEST, true, true);
}

/** include proper subpage **/
if($_GET['page']=="install")		{ require("site/install/index.php"); }
elseif($_GET['page']=="upgrade")	{ require("site/upgrade/index.php"); }
elseif($_GET['page']=="login")		{ require("site/login/index.php"); }
elseif($_GET['page']=="request_ip")	{ require("site/login/index.php"); }
else {
	# verify that user is logged in
	isUserAuthenticatedNoAjax(); 

	# make upgrade and php build checks
	include('functions/dbUpgradeCheck.php'); 	# check if database needs upgrade 
	include('functions/checkPhpBuild.php');		# check for support for PHP modules and database connection 
?>
<!DOCTYPE HTML>
<html lang="en">

<head>
	<base href="<?php print $url; ?>">

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
	<link rel="shortcut icon" type="image/png" href="css/images/favicon.png">
		
	<!-- js -->
	<script type="text/javascript" src="js/jquery-1.9.1.min.js"></script>
	<script type="text/javascript" src="js/jclock.jquery.js"></script>
	<?php if($_GET['page']=="login" || $_GET['page']=="request_ip") { ?>
	<script type="text/javascript" src="js/login.js"></script>
	<?php } ?>
	<script type="text/javascript" src="js/magic-1.1.min.js"></script>
<!-- 	<script type="text/javascript" src="js/magic-1.1.js"></script> -->
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
	<a href="<?php print create_link(null); ?>" class="btn btn-sm btn-default" id="hideError" style="margin-top:0px;">Hide</a>
</div>

<!-- Popups -->
<div id="popupOverlay"></div>
<div id="popup" class="popup popup_w400"></div>
<div id="popup" class="popup popup_w500"></div>
<div id="popup" class="popup popup_w700"></div>

<!-- loader -->
<div class="loading"><?php print _('Loading');?>...<br><i class="fa fa-spinner fa-spin"></i></div>

<!-- header -->
<div class="row" id="header">
	<!-- usermenu -->
	<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12 pull-right" id="user_menu">
		<?php include('site/userMenu.php'); ?>
	</div>
	<!-- title -->
	<div class="col-lg-6 col-lg-offset-3 col-md-6 col-md-offset-3 col-sm-12 col-xs-12">
		<div class="hero-pusher hidden-xs hidden-sm"></div>
		<div class="hero-unit">
			<a href="<?php print create_link(null); ?>"><?php print $settings['siteTitle']; ?></a>
		</div>
	</div>		
</div>  


<!-- page sections / menu -->
<div class="content">
<div id="sections_overlay">
	<?php $user = getActiveUserDetails(); ?>
    <?php if($_GET['page']!="login" && $_GET['page']!="request_ip" && $_GET['page']!="upgrade" && $_GET['page']!="install" && $user['passChange']!="Yes")  include('site/sections.php');?>
</div>
</div>


<!-- content -->
<div class="content_overlay">
<div class="container-fluid" id="mainContainer">
		<?php
		
		/* error */
		if($_GET['page'] == "error") {
			print "<div id='error'>";
			include_once('site/error.php');
			print "</div>";
		}
		/* password reset required */
		elseif($user['passChange']=="Yes") {
			print "<div id='dashboard' class='container'>";
			include_once("site/tools/changePassRequired.php");
			print "</div>";					
		}
		/* dashboard */
		elseif(!isset($_GET['page']) || $_GET['page'] == "dashboard") {
			print "<div id='dashboard'>";
			include_once("site/dashboard/index.php");
			print "</div>";
		}
		/* widgets */
		elseif(@$_GET['page']=="widgets") {
			print "<div id='dashboard'>";
			include_once("site/dashboard/widgets/".$_GET['section'].".php");
			print "</div>";			
		}
		/* side menus */
		else {
			print "<table id='subnetsMenu'>";
			print "<tr>";
			
			print "<td id='subnetsLeft'>";
			print "<div id='leftMenu' class='menu-$_GET[page]'>";
				if($_GET['page'] == "subnets" || $_GET['page'] == "vlan" || 
				   $_GET['page'] == "vrf" 	  || $_GET['page'] == "folder")										{ include_once("site/subnets.php"); }
				else if ($_GET['page'] == "tools")																{ include_once("site/tools/toolsMenu.php"); }
				else if ($_GET['page'] == "administration")														{ include_once("site/admin/adminMenu.php"); }	
			print "</div>";		
			print "</td>";
			
			print "<td id='subnetsContent'>";
			print "<div class='row' id='content'>";
				if( isset($_GET['section']) && (strlen($_GET['section']) == 0) )								{ unset($_GET['section']); }
				# subnet changelog
				if($_GET['page'] == "subnets" && $_GET['sPage'] == "changelog")									{ include_once("site/ipaddr/subnetChangelog.php"); }
				# subnets
				elseif($_GET['page'] == "subnets" && !isset($_GET['subnetId']))									{ include_once("site/ipaddr/sectionAllSubnets.php"); }
				# subnets, vrf, vlan, folder
				else if($_GET['page'] == "subnets" || $_GET['page'] == "vlan" 
					 || $_GET['page'] == "vrf"	   || $_GET['page'] == "folder")								{ include_once("site/ipaddr/ipAddressSwitch.php"); }
				# tools		
				else if ($_GET['page'] == "tools" && !isset($_GET['section']))									{ include_once("site/tools/showAll.php"); }
				else if ($_GET['page'] == "tools")																{ if(!file_exists("site/tools/$_GET[section].php")) { header("Location: ".create_link("error","404")); } else include_once("site/tools/$_GET[section].php"); }
				# admin
				else if ($_GET['page'] == "administration"  && !isset($_GET['section']))						{ include_once("site/admin/showAll.php"); }    	
				else if ($_GET['page'] == "administration"  && ($_GET['subnetId']=="sectionChangelog"))			{ include_once("site/admin/sectionChangelog.php"); }  
				else if ($_GET['page'] == "administration")														{ if(!file_exists("site/admin/$_GET[section].php")) { header("Location: ".create_link("error","404")); } else include_once("site/admin/$_GET[section].php"); }  	
			print "</div>";
			print "</td>";
			
			print "</tr>";
			print "</table>";
    	}
    	?>
      	
</div>
</div>

<!-- Base for IE -->
<div class="iebase hidden"><?php print BASE; ?></div>

<!-- pusher -->
<div class="pusher"></div>

<!-- end wrapper -->
</div>

<!-- weather prettyLinks are user, for JS! -->
<div id="prettyLinks" style="display:none"><?php print $settings['prettyLinks']; ?></div>

<!-- Page footer -->
<div class="footer"><?php include('site/footer.php'); ?></div>

<!-- export div -->
<div class="exportDIV"></div>

<!-- end body -->
</body>
</html>
<?php ob_end_flush(); ?>
<?php } ?>