	<?php

/**
 *	Mail settings
 **************************/

/* required functions */
require_once('../../functions/functions.php'); 

/* verify that user is admin */
checkAdmin(false);

/* get settings form post */
$settings = $_POST;
$sitesettings = getAllSettings();

/* set mail parameters */
require_once '../../functions/phpMailer/class.phpmailer.php';

// set mail content
$mail['html']  = "<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.01 Transitional//EN' 'http://www.w3.org/TR/html4/loose.dtd'><html><head><meta http-equiv='Content-Type' content='text/html; charset=utf-8'></head><body style='margin:0px;padding:0px;background:#f9f9f9;border-collapse:collapse;'>";
$mail['html'] .= "phpIPAM test HTML message";
$mail['html'] .= "</body></html>";
$mail['plain'] = "phpIPAM test text message";

//initialize
$pmail = new PHPMailer(true);				//localhost
$pmail->CharSet="UTF-8";					//set utf8
$pmail->SMTPDebug = 2;						//debugging
$pmail->Debugoutput = 'html';				//debug type

# localhost
if($settings['mtype']=="localhost") {
	// initialize
	try {	
		$pmail->setFrom($settings['mAdminMail'], $settings['mAdminName']);
		$pmail->addAddress($sitesettings['siteAdminMail'], $sitesettings['siteAdminName']);
		$pmail->Subject = 'phpIPAM localhost mail test';
		$pmail->msgHTML($mail['html']);
		$pmail->AltBody = $mail['plain'];
		//send
		$pmail->send();
		
	} catch (phpmailerException $e) {
	  	die("<div class='alert alert-danger'>Mailer Error: " . $e->errorMessage(). "</div>");
	} catch (Exception $e) {
	  	die("<div class='alert alert-danger'>Mailer Error: " . $e->errorMessage(). "</div>");
	}
	
	//if error not sent print ok
	print "<div class='alert alert-success'>Message sent to site admin ( $sitesettings[siteAdminMail] )!</div>";
}
# smtp
elseif($settings['mtype']=="smtp") {
	// initialize
	try {
		$pmail->isSMTP();	
		
		//server
		$pmail->Host = $settings['mserver'];
		$pmail->Port = $settings['mport'];
		
		//auth or not?
		if($settings['mauth']=="yes") {
			$pmail->SMTPAuth = true;
			$pmail->Username = $settings['muser'];
			$pmail->Username = $settings['mpass'];
		} else {
			$pmail->SMTPAuth = false;	
		}

		$pmail->setFrom($settings['mAdminMail'], $settings['mAdminName']);
		$pmail->addAddress($sitesettings['siteAdminMail'], $sitesettings['siteAdminName']);
		$pmail->Subject = 'phpIPAM localhost mail test';
		$pmail->msgHTML($mail['html']);
		$pmail->AltBody = $mail['plaint'];
		//send
		$pmail->send();
				
	} catch (phpmailerException $e) {
	  	die("<div class='alert alert-danger'>Mailer Error: " . $e->errorMessage(). "</div>");
	} catch (Exception $e) {
	  	die("<div class='alert alert-danger'>Mailer Error: " . $e->errorMessage(). "</div>");
	}
	
	//if error not sent print ok
	print "<div class='alert alert-success'>Message sent to site admin ( $sitesettings[siteAdminMail] )!</div>";	
}
# wrong type
else {
	die("<div class='alert alert alert-danger'>"._("Invalid mail server type")."</div>");
}

?>