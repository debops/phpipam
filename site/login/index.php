<?php

# make upgrade and php build checks
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
	<link rel="shortcut icon" href="css/images/favicon.png">
		
	<!-- js -->
	<script type="text/javascript" src="js/jquery-1.9.1.min.js"></script>
	<script type="text/javascript" src="js/login.js"></script>
	<script type="text/javascript" src="js/bootstrap.min.js"></script>
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
<div class="row header-install" id="header">
	<div class="col-xs-12">
		<div class="hero-unit" style="padding:20px;margin-bottom:10px;">
			<a href="<?php print create_link(null); ?>"><?php print $settings['siteTitle']." | "._('login');?></a>
		</div>
	</div>		
</div>  

<!-- content -->
<div class="content_overlay">
<div class="container-fluid" id="mainContainer">

	<?php 
	/* logout? */
	if (isset($_SESSION['ipamusername'])) 	{ 
		# destroy session 
		session_destroy();	
		# update table
		updateLogTable ('User has logged out', 0); 
		# set logout flag or timeout flag
		if(@$_GET['section']=="timeout")	{ $timeout = true; }
		else								{ $logout = true; }
	}
	
	# set default language
	if(isset($settings['defaultLang']) && !is_null($settings['defaultLang']) ) {
		# get language
		$lang = getLangById ($settings['defaultLang']);
		
		putenv("LC_ALL=$lang[l_code]");
		setlocale(LC_ALL, $lang['l_code']);		// set language		
		bindtextdomain("phpipam", "./functions/locale");	// Specify location of translation tables
		textdomain("phpipam");								// Choose domain
	}
	?>
		
	<?php 
	# include proper subpage
	if($_GET['page'] == "login") 				{ include_once('loginForm.php'); }
	else if ($_GET['page'] == "request_ip") 	{ include_once('requestIPform.php'); }
	else 										{ $_GET['subnetId'] = "404"; print "<div id='error'>"; include_once('site/error.php'); print "</div>"; }
	?>
	
	<!-- login response -->
	<div id="loginCheck">
		<?php if (@$logout)  print '<div class="alert alert-success">'._('You have logged out').'</div>'; ?>
		<?php if (@$timeout) print '<div class="alert alert-success">'._('You session has timed out').'</div>'; ?>
	</div>

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