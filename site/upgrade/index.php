<?php

# make upgrade and php build checks
include('functions/checkPhpBuild.php');		# check for support for PHP modules and database connection 

# verify that user is logged in
isUserAuthenticatedNoAjax(); 
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
	<link rel="shortcut icon" href="css/images/favicon.png">
		
	<!-- js -->
	<script type="text/javascript" src="js/jquery-1.9.1.min.js"></script>
	<script type="text/javascript" src="js/jclock.jquery.js"></script>
	<script type="text/javascript" src="js/login.js"></script>
	<script type="text/javascript" src="js/install.js"></script>
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
			<a href="<?php print create_link(null); ?>"><?php print $settings['siteTitle']." | "._('upgrade');?></a>
		</div>
	</div>		
</div>  

<!-- content -->
<div class="content_overlay">
<div class="container" id="dashboard">


<?php

/**
 * Check if database needs upgrade to newer version
 ****************************************************/


/**
 * checks
 *
 *	$settings['version'] = installed version (from database)
 *	VERSION 			 = file version
 *	LAST_POSSIBLE		 = last possible for upgrade
 */


# not authenticated
if (isUserAuthenticatedNoAjax()) {
	header("Location: ".create_link("login"));	
}
# authenticated, but not admins
elseif (!checkAdmin(false)) {
	# version is ok
	if ($settings['version'] == VERSION) {
		header("Location: ".create_link("login"));
	} 
	# upgrade needed
	else {
		$title 	  = 'phpipam upgrade required';
		$content  = '<div class="alert alert-warning">Database needs upgrade. Please contact site administrator (<a href="mailto:'. $settings['siteAdminMail'] .'">'. $settings['siteAdminName'] .'</a>)!</div>';
	}
}
# admins that are authenticated
elseif(checkAdmin(false)) { 
	# version ok
	if ($settings['version'] == VERSION) {
		$title 	  = "Database upgrade check";
		$content  = "<div class='alert alert-success'>Database seems up to date and doesn't need to be upgraded!</div>";
		$content .= '<a href="'.create_link(null).'"><button class="btn btn-sm btn-default">Go to dashboard</button></a>';		
	}
	# version too old
	elseif ($settings['version'] < LAST_POSSIBLE) {
		$title 	  = "Database upgrade check";
		$content  = "<div class='alert alert-danger'>Your phpIPAM version is too old to be upgraded, at least version ".LAST_POSSIBLE." is required for upgrade.</div>";
	}
	# upgrade needed
	elseif ($settings['version'] < VERSION) {
		$title	  = "phpipam database upgrade required";
		$title	 .= "<hr><div class='text-muted' style='font-size:13px;padding-top:5px;'>Database needs to be upgraded to version <strong>v".VERSION."</strong>, it seems you are using phpipam version <strong>v$settings[version]</strong>!</div>";
		
		// automatic
		$content  = "<h5 style='padding-top:10px;'>Automatic database upgrade</h5><hr>";
		$content .= "<div style='padding:10px 0px;'>";
		$content .= "<div class='alert alert-warning' style='margin-bottom:5px;'><strong>Warning!</strong> Backup database first before attempting to upgrade it! You have been warned.</div>";
		$content .= "<span class='text-muted'>Clicking on upgrade button will automatically update database to newest version!</span>";
		$content .= "<div class='text-right'><input type='button' class='upgrade btn btn-sm btn-default btn-success' style='margin-top:10px;' version='$settings[version]' value='Upgrade phpipam database'></div>";
		$content .= "<div id='upgradeResult'></idv>";
		$content .= "</div>";
		
		// manual
		$content .= "<h5 style='padding-top:10px;'>Manual upgrade instructions</h5><hr>";
		$content .= "<div style='padding:10px 15px;'>";
		$content .= "<a class='btn btn-sm btn-default' href='#' id='manualUpgrade'>Show instructions</a>";
		$content .= "<div style='display:none' id='manualShow'>";
		$content .= "<span class='text-muted'>copy and paste below commands to mysql directly!</span>";
		// get file
		$dir = "db/";
		$files = scandir($dir);
		foreach($files as $f) {
			//get only UPDATE- for specific version
			if(substr($f, 0, 6) == "UPDATE") {
				$ver = str_replace(".sql", "",substr($f, 8));
				if($ver>$settings['version']) {
					//printout
					$tmp[] = file_get_contents("db/$f");
				}
			}
		}
		$tmp = implode("<br><br>", $tmp);
		$content .= "<pre>".str_replace("\n","<br>",$tmp)."</pre>";
		$content .= "</div>";
		$content .= "</div>";
	}
	# upgrade not needed, redirect to login
	else {
		header("Location: ".create_link("login"));		
	}
}
# default, smth is wrong
else {
	header("Location: ".create_link("login"));		
}

?>	

	<div class="widget-dash col-xs-12 col-md-8 col-md-offset-2">
	<div class="inner install" style="min-height:auto;">
		<h4><?php print $title; ?></h4>
	
		<div class="hContent">
		<div style="padding:10px;">
			<?php print $content; ?>			
		</div>
		</div>
	</div>	
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