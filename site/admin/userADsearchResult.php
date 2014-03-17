<?php

/**
 * Script to display usermod result
 *************************************/
 
/* required functions */
require_once('../../functions/functions.php'); 

/* verify that user is admin */
checkAdmin();

//dont debug
ini_set('display_errors', 1);
error_reporting(E_ERROR);

//include AD script
include (dirname(__FILE__) . "/../../functions/adLDAP/src/adLDAP.php");

// get All settings
$settings = getAllSettings();

//open connection
try {
	//get settings for connection
	$ad = getADSettings();
		
	//AD
	$adldap = new adLDAP(array( 'base_dn'=>$ad['base_dn'], 'account_suffix'=>$ad['account_suffix'], 
								'domain_controllers'=>explode(";",$ad['domain_controllers']), 'use_ssl'=>$ad['use_ssl'],
								'use_tls'=> $ad['use_tls'], 'ad_port'=> $ad['ad_port']
								));

	//try to login with higher credentials for search
	$authUser = $adldap->user()->authenticate($ad['adminUsername'], $ad['adminPassword']);
	if ($authUser == false) {
		throw new adLDAPException ('Invalid credentials');
	}
	
	// set OpenLDAP flag
	if($settings['domainAuth'] == "2") { $adldap->setUseOpenLDAP(true); }

	//search for domain user!
	$userinfo = $adldap->user()->info("$_POST[dname]*", array("*"));
	
	//echo $adldap->getLastError();
}
catch (adLDAPException $e) {
	die('<div class="alert alert-danger">'. $e .'</div>');
}


//at least 2 chars
if(strlen($_POST['dname'])<2) {
	die("<div class='alert alert-warning'>"._('Please enter at least 2 characters')."</div>");
}

//check for found
if(!isset($userinfo['count'])) {
	print "<div class='alert alert-info alert-block'>";
	print _('No users found')."!<hr>";
	print _('Possible reasons').":";
	print "<ul>";
	print "<li>"._('Username not existing')."</li>";
	print "<li>"._('Invalid baseDN setting for AD')."</li>";
	print "<li>"._('AD account does not have enough privileges for search')."</li>";
	print "</div>";
} else {
	print _(" Following users were found").": ($userinfo[count]):<hr>";
	
	print "<table class='table table-striped'>";
	
	unset($userinfo['count']);
	if(sizeof(@$userinfo)>0 && isset($userinfo)) {
	 	foreach($userinfo as $u) {
			print "<tr>";
			print "	<td>".$u['displayname'][0];
			print "</td>";
			print "	<td>".$u['samaccountname'][0]."</td>";
			print "	<td>".$u['mail'][0]."</td>";
			//actions
			print " <td style='width:10px;'>";
			print "		<a href='' class='btn btn-sm btn-default btn-success userselect' data-uname='".$u['displayname'][0]."' data-username='".$u['samaccountname'][0]."' data-email='".$u['mail'][0]."'>"._('Select')."</a>";
			print "	</td>";
			print "</tr>";
		}
	}
	
	print "</table>";
}


?>