<?php

/**
 *
 *	phpipam installation page!
 *
 */

# check if php is built properly
include('functions/checkPhpBuild.php');		# check for support for PHP modules and database connection 

# if already installed than redirect !
if(tableExists("vrf")) {

	# we permit if admin pass is default!
	$admin = getUserDetailsByName ("Admin");
	if($admin['password']!='$6$rounds=3000$JQEE6dL9NpvjeFs4$RK5X3oa28.Uzt/h5VAfdrsvlVe.7HgQUYKMXTJUsud8dmWfPzZQPbRbk8xJn1Kyyt4.dWm4nJIYhAV2mbOZ3g.') {
	
		if(defined('BASE')) { header("Location: ".BASE.create_link(null)); }
		else 				{ header("Location: ".create_link(null));} 
		die();	
	}
}

# printout
?>

<!DOCTYPE HTML>
<html lang="en">

<head>
	<base href="<?php print $url; ?>">

	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta http-equiv="Cache-Control" content="no-cache, must-revalidate">
	
	<meta name="Description" content=""> 
	<meta name="title" content="phpipam installation"> 
	<meta name="robots" content="noindex, nofollow"> 
	<meta http-equiv="X-UA-Compatible" content="IE=9" >
	
	<meta name="viewport" content="width=device-width, initial-scale=0.7, maximum-scale=1, user-scalable=no">
	
	<!-- chrome frame support -->
	<meta http-equiv="X-UA-Compatible" content="chrome=1">
  
	<!-- title -->
	<title>phpipam installation</title>
	
	<!-- css -->
	<link rel="stylesheet" type="text/css" href="css/bootstrap/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="css/bootstrap/bootstrap-custom.css">
	<link rel="stylesheet" type="text/css" href="css/font-awesome/font-awesome.min.css">
	<link rel="shortcut icon" href="css/images/favicon.png">
		
	<!-- js -->
	<script type="text/javascript" src="js/jquery-1.9.1.min.js"></script>
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
	<a href="<?php print create_link(null,null,null,null,null,true); ?>" class="btn btn-sm btn-default" id="hideError" style="margin-top:0px;">Hide</a>
</div>

<!-- loader -->
<div class="loading"><?php print _('Loading');?>...<br><i class="fa fa-spinner fa-spin"></i></div>

<!-- header -->
<div class="row header-install" id="header">
	<div class="col-xs-12">
		<div class="hero-unit" style="padding:20px;margin-bottom:10px;">
			<a href="<?php print create_link(null,null,null,null,null,true); ?>">phpipam installation</a>
		</div>
	</div>	
</div> 


<!-- content -->
<div class="content_overlay">
<div class="container-fluid" id="mainContainer">
<div class='container' id='dashboard'>

<?php
# select install type
if(!isset($_GET['section']))										{ include("select_install_type.php"); }
# open subpage
else {
	//check if subnetId == configure than already installed
	if(@$_GET['subnetId']=="configure")								{ include(dirname(__FILE__)."/postinstall_configure.php"); }
	else {
		// verify that page exists
		if(!file_exists(dirname(__FILE__)."/$_GET[section].php"))	{ include("invalid_install_type.php"); }
		else														{ include(dirname(__FILE__)."/$_GET[section].php"); }
	}
}

?>

<!-- Base for IE -->
<div class="iebase hidden"><?php print BASE; ?></div>

<!-- pusher -->
<div class="pusher"></div>

<!-- end wrapper -->
</div>

<!-- Page footer -->
<div class="footer"><?php include('site/footer.php'); ?></div>

<!-- end body -->
</body>
</html>
<?php ob_end_flush(); ?>
