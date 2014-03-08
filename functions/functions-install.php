<?php

#
# include database functions
#
require( dirname(__FILE__) . '/../config.php' );
require( dirname(__FILE__) . '/../functions/dbfunctions.php' );


/**
 * php debugging on/off - ignore notices
 */
if ($debugging == 0) {
  	ini_set('display_errors', 1);
    error_reporting(E_ERROR | E_WARNING);
}
else{
    ini_set('display_errors', 1); 
    error_reporting(E_ALL ^ E_NOTICE);
}


/**
 * Update log table
 */
function updateLogTable ($command, $details = NULL, $severity = 0)
{
    global $db;                                                                      # get variables from config file
    $database    = new database($db['host'], $db['user'], $db['pass']); 
    
    
    /* select database */
    try {
    	$database->selectDatabase($db['name']);
    }
    catch (Exception $e) {
    	return false;
    	die();
	}
	
    /* Check connection */
	if (!$database->connect_error) {

	   	/* set variable */
	    $date = date("Y-m-d H:i:s");
	    $user = getActiveUserDetails();
	    $user = $user['username'];
    
    	/* set query */
    	$query  = 'insert into logs '. "\n";
        $query .= '(`severity`, `date`,`username`,`ipaddr`,`command`,`details`)'. "\n";
        $query .= 'values'. "\n";
        $query .= '("'.  $severity .'", "'. $date .'", "'. $user .'", "'. $_SERVER['REMOTE_ADDR'] .'", "'. $command .'", "'. $details .'");';
	    
	    /* execute */
    	try {
    		$database->executeMultipleQuerries($query);
    	}
    	catch (Exception $e) {
    		$error =  $e->getMessage();
    		return false;
		}
		return true;
	}
	else {
		return false;
	}
}


/**
 * Get user details by name
 */
function getUserDetailsByName ($username)
{
    global $db;                                                                      # get variables from config file
    /* set query, open db connection and fetch results */
    $query    = 'select * from users where username LIKE BINARY "'. $username .'";';
    $database = new database($db['host'], $db['user'], $db['pass'], $db['name']);  

    /* execute */
    try { $details = $database->getArray( $query ); }
    catch (Exception $e) { 
        $error =  $e->getMessage(); 
        print ("<div class='alert alert-error'>"._('Error').": $error</div>");
        return false;
    } 
    
    //we only need 1st field
    $details = $details[0];
    
    /* return results */
    return($details);
}


/**
 * Get active users username - from session!
 */
function getActiveUserDetails ()
{
/*     session_start(); */
	if(isset($_SESSION['ipamusername'])) {
    	return getUserDetailsByName ($_SESSION['ipamusername']);
    }
    else {
    	return false;
    }
    session_write_close();
}


/**
 * Get user lang
 */
function getUserLang ($username)
{
    global $db;                                                                      # get variables from config file
    /* set query, open db connection and fetch results */
    $query    = 'select `lang`,`l_id`,`l_code`,`l_name` from `users` as `u`,`lang` as `l` where `l_id` = `lang` and `username` = "'.$username.'";;';
    $database = new database($db['host'], $db['user'], $db['pass'], $db['name']);  

    /* execute */
    try { $details = $database->getArray( $query ); }
    catch (Exception $e) { 
        $error =  $e->getMessage(); 
        print ("<div class='alert alert-error'>"._('Error').": $error</div>");
        return false;
    } 
    
    /* return results */
    return($details[0]);
}


/**
 * Get lang by id
 */
function getLangById ($id)
{
    global $db;                                                                      # get variables from config file
    /* set query, open db connection and fetch results */
    $query    = 'select * from `lang` where `l_id` = "'.$id.'";';
    $database = new database($db['host'], $db['user'], $db['pass'], $db['name']);  

    /* execute */
    try { $details = $database->getArray( $query ); }
    catch (Exception $e) { 
        $error =  $e->getMessage(); 
        print ("<div class='alert alert-error'>"._('Error').": $error</div>");
        return false;
    } 
    
    /* return results */
    return($details[0]);
}


/**
 * Get all site settings
 */
function getAllSettings()
{
    global $db;                                                                      # get variables from config file
    $database    = new database($db['host'], $db['user'], $db['pass']); 

    /* Check connection */
    if ($database->connect_error) {
    	die('Connect Error (' . $database->connect_errno . '): '. $database->connect_error);
	}
	
    /* first check if table settings exists */
    $query    = 'SELECT COUNT(*) AS count FROM information_schema.tables WHERE table_schema = "'. $db['name'] .'" AND table_name = "settings";';

    /* execute */
    try { $count = $database->getArray( $query ); }
    catch (Exception $e) { 
        $error =  $e->getMessage(); 
        print ("<div class='alert alert-error'>"._('Error').": $error</div>");
        return false;
    } 
  
	/* return true if it exists */
	if($count[0]['count'] == 1) {

		/* select database */
		$database->selectDatabase($db['name']);
	
	    /* first update request */
	    $query    = 'select * from settings where id = 1';

	    /* execute */
	    try { $settings = $database->getArray( $query ); }
	    catch (Exception $e) { 
        	$error =  $e->getMessage(); 
        	print ("<div class='alert alert-error'>"._('Error').": $error</div>");
        	return false;
        }   
		/* return settings */
		return($settings[0]);
	}
	else {
		return false;
	}
}


/**
 * Get Domain settings for authentication
 */
function getADSettings()
{
    global $db;                                                                      # get variables from config file
    $database    = new database($db['host'], $db['user'], $db['pass']); 

    /* Check connection */
    if ($database->connect_error) {
    	die('Connect Error (' . $database->connect_errno . '): '. $database->connect_error);
	}
	
    /* first check if table settings exists */
    $query    = 'SELECT COUNT(*) AS count FROM information_schema.tables WHERE table_schema = "'. $db['name'] .'" AND table_name = "settingsDomain";';

    /* execute */
    try { $count = $database->getArray( $query ); }
    catch (Exception $e) { 
        $error =  $e->getMessage(); 
        print ("<div class='alert alert-error'>"._('Error').": $error</div>");
        return false;
    }  
  
	/* return true if it exists */
	if($count[0]['count'] == 1) {

		/* select database */
		$database->selectDatabase($db['name']);
	
	    /* first update request */
	    $query    = 'select * from `settingsDomain` limit 1;';

	    /* execute */
	    try { $settings = $database->getArray( $query ); }
	    catch (Exception $e) { 
        	$error =  $e->getMessage(); 
        	print ("<div class='alert alert-error'>"._('Error').": $error</div>");
        	return false;
        } 
	    
	    /* reformat DC */
  		$dc = str_replace(" ", "", $settings[0]['domain_controllers']);
  		$dcTemp = explode(";", $dc);
  		$settings[0]['domain_controllers'] = $dcTemp;
  		  
		/* return settings */
		return($settings[0]);
	}
	else {
		return false;
	}
}



/**
 * Login authentication
 *
 * First we try to authenticate via local database
 * if it fails we querry the AD, if set in config file
 */
function checkLogin ($username, $md5password, $rawpassword) 
{
    global $db;                                                                      # get variables from config file
    
    /* check if user exists in local database */
    $database 	= new database($db['host'], $db['user'], $db['pass'], $db['name']);
    $query 		= 'select * from `users` where `username` = binary "'. $username .'" and `password` = BINARY "'. $md5password .'" and `domainUser` = "0" limit 1;';

    /* execute */
    try { $result = $database->getArray( $query ); }
    catch (Exception $e) { 
        $error =  $e->getMessage(); 
        print ("<div class='alert alert-error'>"._('Error').": $error</div>");
        return false;
    } 

    /* close database connection */
    $database->close();
    
   	/* locally registered */
    if (sizeof($result) !=0 ) 	{ 

    	# get user lang
    	$lang = getLangById ($result[0]['lang']);
    	
    	/* start session and set variables */
    	session_start();
    	$_SESSION['ipamusername'] = $username;
    	$_SESSION['ipamlanguage'] = $lang['l_code'];
    	session_write_close();
    	
    	# print success
    	print('<div class="alert alert-success">'._('Login successful').'!</div>');	
    	# write log file
    	updateLogTable ('User '. $username .' logged in.', "", 0); 
    }
    /* locally failed, try domain */
    else {
    	/* fetch settings */
    	$settings = getAllSettings();  
    	
    	/* if local failed and AD/OpenLDAP is selected try to authenticate */
    	if ( $settings['domainAuth'] != "0") {
    		
    		/* verify that user is in database! */
    		$database 	= new database($db['host'], $db['user'], $db['pass'], $db['name']);
    		$query 		= 'select * from `users` where `username` = binary "'. $username .'" and `domainUser` = "1" limit 1;';
    		
    		/* execute */
    		try { $result = $database->getArray( $query ); }
    		catch (Exception $e) { 
	    		$error =  $e->getMessage(); 
	    		print ("<div class='alert alert-error'>"._('Error').": $error</div>");
	    		return false;
	    	} 
    		
    		/* close database connection */
    		$database->close();
    		
    		if(sizeof($result)!=0) {

				/* check if user exist in database and has domain user flag */		
				$authAD = checkADLogin ($username, $rawpassword);
		
				if($authAD == "ok") {
					# get user lang
					$lang = getLangById ($result[0]['lang']);

	    			/* start session and set variables */
	    			session_start();
	    			$_SESSION['ipamusername'] = $username;
	    			$_SESSION['ipamlanguage'] = $lang['l_code'];
	    			session_write_close();
	    		
	    			# print success
	    			if($settings['domainAuth'] == "1") {
		    			print('<div class="alert alert-success">'._('AD login successful').'!</div>');	
		    			updateLogTable ('User '. $username .' logged in.', "", 0); 	
		    		}
		    		else {
		    			print('<div class="alert alert-success">'._('LDAP login successful').'!</div>');	
		    			updateLogTable ('User '. $username .' logged in.', "", 0); 			    	
		    		}
		    	}
		    	# failed to connect
		    	else if ($authAD == 'Failed to connect to AD!') {
					# print error
					if($settings['domainAuth'] == "1") {
					    print('<div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">×</button>'._('Failed to connect to AD server').'!</div>');	
					    updateLogTable ('Failed to connect to AD!', "", 2); 	
					}
					else {
				    	print('<div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">×</button>'._('Failed to connect to LDAP server').'!</div>');	
				    	updateLogTable ('Failed to connect to LDAP!', "", 2); 						
				    }
				}
				# failed to authenticate
				else if ($authAD == 'Failed to authenticate user via AD!') {
					# print error
					if($settings['domainAuth'] == "1") {
					    print('<div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">×</button>'._('Failed to authenticate user against AD').'!</div>');	
					    updateLogTable ('User '. $username .' failed to authenticate against AD.', "", 2); 	
					}
					else {
				    	print('<div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">×</button>'._('Failed to authenticate user against LDAP').'!</div>');	
				    	updateLogTable ('User '. $username .' failed to authenticate against LDAP.', "", 2); 					
				    }
				}
				# wrong user/pass
				else {
					# print error
					if($settings['domainAuth'] == "1") {
					    print('<div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">×</button>'._('Wrong username or password').'!</div>');
					    updateLogTable ('User '. $username .' failed to authenticate against AD.', "", 2); 
					}
					else {
				    	print('<div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">×</button>'._('Wrong username or password').'!</div>');
				    	updateLogTable ('User '. $username .' failed to authenticate against LDAP.', "", 2); 					
				    }
				}
			}
			# user not in db
			else {
				# print error
				if($settings['domainAuth'] == "1") {
				    print('<div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">×</button>'._('Wrong username or password').'!</div>');
				    updateLogTable ('User '. $username .' failed to authenticate against AD.', "", 2); 
				}
				else {
				    print('<div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">×</button>'._('Wrong username or password').'!</div>');
				    updateLogTable ('User '. $username .' failed to authenticate against LDAP.', "", 2); 					
				}				
			}
    	}
    	/* only local set, print error! */
    	else {
    		# print error
			print('<div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">×</button>'._('Failed to log in').'!</div>');	
			# write log file
	    	updateLogTable ('User '. $username .' failed to log in.', "", 2);
    	}   
    }
}



/**
 * Check user against AD
 */
function checkADLogin ($username, $password)
{
	/* first checked if it is defined in database - username and ad option */
    global $db;                                                                      # get variables from config file
/*     global $ad; */
    
    /* check if user exists in local database */
    $database 	= new database($db['host'], $db['user'], $db['pass'], $db['name']);
    $query 		= 'select count(*) as count from users where `username` = binary "'. $username .'" and `domainUser` = "1";';
    
    /* execute */
    try { $result = $database->getArray( $query ); }
    catch (Exception $e) { 
        $error =  $e->getMessage(); 
        print ("<div class='alert alert-error'>"._('Error').": $error</div>");
        return false;
    } 

    /* close database connection */
    $database->close();

    /* get All settings */
    $settings = getAllSettings();
    
    /* if yes try with AD */
    if($result[0]['count'] == "1") {
		//include login script
		include (dirname(__FILE__) . "/adLDAP/src/adLDAP.php");
	
		//open connection
		try {
			//get settings for connection
			$ad = getADSettings();
			
			//AD
	    	$adldap = new adLDAP(array( 'base_dn'=>$ad['base_dn'], 'account_suffix'=>$ad['account_suffix'], 
	    								'domain_controllers'=>$ad['domain_controllers'], 'use_ssl'=>$ad['use_ssl'],
	    								'use_tls'=> $ad['use_tls'], 'ad_port'=> $ad['ad_port']
	    								));
	    	
	    	// set OpenLDAP flag
	    	if($settings['domainAuth'] == "2") { $adldap->setUseOpenLDAP(true); }
	    	
		}
		catch (adLDAPException $e) {
			die('<div class="alert alert-error">'. $e .'</div>');
		}

		//user authentication
		$authUser = $adldap->authenticate($username, $password);
		
		if($authUser == true) { 
			updateLogTable ('User '. $username .' authenticated against AD.', "", 0);
			return 'ok'; 
		}
		else { 
			updateLogTable ('User '. $username .' failed to authenticate against AD.', "", 2);
			$err = $adldap->getLastError();
			print "<div class='alert alert-error'>$err</div>";
			return 'Failed to authenticate user via AD!'; 
		}
    }
    //user not defined as AD user or user not existing
    else {
    	return false;
    }
}


/**
 * Check if user is admin
 */
function checkAdmin ($die = true) 
{
    global $db;                                                                      # get variables from config file
    
    /* first get active username */
    session_start();
    $ipamusername = $_SESSION['ipamusername'];
    session_write_close();
    
    /* set check query and get result */
    $database = new database ($db['host'], $db['user'], $db['pass'], $db['name']);
    $query = 'select role from users where username = "'. $ipamusername .'";';
    
    /* execute */
    try { $role = $database->getRow( $query ); }
    catch (Exception $e) { 
        $error =  $e->getMessage(); 
        print ("<div class='alert alert-error'>"._('Error').": $error</div>");
        return false;
    } 

    /* close database connection */
    $database->close();
    
    /* return true if admin, else false */
    if ($role[0] == "Administrator") {
        return true;
    }
    else {
    	//die
    	if($die == true) { die('<div class="alert alert-error">'._('Administrator level privileges are required to access this site').'!</div>'); }
    	//return false if called
    	else 			 { return false; }
    	//update log
    	updateLogTable ('User '. $ipamusername .' tried to access admin page.', "", 2);
    }      
}


/*********************************
	Upgrade check functions
*********************************/


/**
 * Get all tables
 */
function getAllTables()
{
    global $db;                                                                      # get variables from config file
    $database    = new database($db['host'], $db['user'], $db['pass'], $db['name']); 
    
    /* first update request */
    $query    = 'show tables;';

    /* execute */
    try { $tables = $database->getArray( $query ); }
    catch (Exception $e) { 
        $error =  $e->getMessage(); 
        print ("<div class='alert alert-error'>"._('Error').": $error</div>");
        return false;
    } 
  
	/* return all tables */
	return $tables;
}


/**
 * Check if specified table exists
 */
function tableExists($table)
{
    global $db;                                                                      # get variables from config file
    $database    = new database($db['host'], $db['user'], $db['pass']); 

    /* Check connection */
    if ($database->connect_error) {
    	die('Connect Error (' . $database->connect_errno . '): '. $database->connect_error);
	}
    
    /* first update request */
    $query    = 'SELECT COUNT(*) AS count FROM information_schema.tables WHERE table_schema = "'. $db['name'] .'" AND table_name = "'. $table .'";';

    /* execute */
    try { $count = $database->getArray( $query ); }
    catch (Exception $e) { 
        $error =  $e->getMessage(); 
        print ("<div class='alert alert-error'>"._('Error').": $error</div>");
        return false;
    } 
  
	/* return true if it exists */
	if($count[0]['count'] == 1)	{ return true; }
	else 						{ return false; }
}


/**
 * describe specific table
 */
function fieldExists($table, $fieldName)
{
    global $db;                                                                      # get variables from config file
    $database    = new database($db['host'], $db['user'], $db['pass'], $db['name']); 
    
    /* first update request */
    $query    = 'describe `'. $table .'` `'. $fieldName .'`;';

    /* execute */
    try { $count = $database->getArray( $query ); }
    catch (Exception $e) { 
        $error =  $e->getMessage(); 
        print ("<div class='alert alert-error'>"._('Error').": $error</div>");
        return false;
    } 
  
	/* return true if it exists */
	if(sizeof($count) == 0) { return false; }
	else 					{ return true; }
}


/**
 * install databases
 */
function installDatabase($root)
{
    global $db;                                                                      # get variables from config file
    
    error_reporting(E_ERROR); 
    
    $databaseRoot    = new database($db['host'], $root['user'], $root['pass']); 
    
    /* Check connection */
    if ($databaseRoot->connect_error) {
    	die('<div class="alert alert-error">Connect Error (' . $databaseRoot->connect_errno . '): '. $databaseRoot->connect_error). "</div>";
	}
    
 	/* first create database */
    $query = "create database ". $db['name'] .";";

    /* execute */
    try {
    	$databaseRoot->executeQuery( $query );
    }
    catch (Exception $e) {
    	$error =  $e->getMessage();
    	die('<div class="alert alert-error">'. $error .'</div>');
	} 
    
    /* select database */
	$databaseRoot->selectDatabase($db['name']);

	/* set permissions! */
	$query = 'grant ALL on '. $db['name'] .'.* to '. $db['user'] .'@localhost identified by "'. $db['pass'] .'";';

    /* execute */
    try {
    	$databaseRoot->executeMultipleQuerries( $query );
    }
    catch (Exception $e) {
    	$error =  $e->getMessage();
    	die('<div class="alert alert-error">Cannot set permissions for user '. $db['user'] .': '. $error. '</div>');
	}
    
    /* try importing SCHEMA file */
    $query       = file_get_contents("../../db/SCHEMA.sql");
    
    /* execute */
    try {
    	$databaseRoot->executeMultipleQuerries( $query );
    }
    catch (Exception $e) {
    	$error =  $e->getMessage();
    	die('<div class="alert alert-error">Cannot install sql SCHEMA file: '. $error. '</div>');
	}
	    
    /* return true, if some errors occured script already died! */
    sleep(1);
   	updateLogTable ('Database installed successfully!', "version 0.9 installed", 1);
   	return true;
}

?>