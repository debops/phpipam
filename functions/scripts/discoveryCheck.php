<?php

// include required scripts
require_once( dirname(__FILE__) . '/../functions.php' );
require_once( dirname(__FILE__) . '/Thread.php');
require_once( dirname(__FILE__) . '/../functions-mail.php');
require_once( dirname(__FILE__) . '/../scan/config-scan.php');

/*
set cronjob:
# update host statuses exery 15 minutes
*\/15 * * * *  /usr/local/bin/php /<sitepath>/functions/scripts/discoveryCheck.php
*/

// config
$email = true;							//set mail with status diff to admins
$emailText = false;						//format to send mail via text or html
//$wait = 500;							//time to wait for response in ms
$count = 1;								//number of pings to send
$timeout = 1;							//timeut in seconds

// response
$stateDiff = array();					//Array with differences, can be used to email to admins

// test to see if threading is available
if( !Thread::available() ) 	{ $threads = false; }		//pcntl php extension required
else						{ $threads = true; }


//get all IP to be discovered > in decimal format
$addresses = getSubnetsToDiscover ();

//get settings
$settings = getAllSettings();

//verify that pign path is correct
if(!file_exists($settings['scanPingPath'])) {
	print "Invalid ping path! You can set parameters for scan under Administration > ping settings\n";
}
//threads not supported, scan 1 by one - it is highly recommended to enable threading for php
elseif(!$threads) {
	//print warning
	print "Warning: Threading is not supported!\n";
}
//threaded
else {
	//get size of addresses to ping
	$size = sizeof($addresses);
	
	$z = 0;			//addresses array index

	//run per MAX_THREADS
    for ($m=0; $m<=$size; $m += $settings['scanMaxThreads']) {
        // create threads 
        $threads = array();
        
        //fork processes
        for ($i = 0; $i <= $settings['scanMaxThreads'] && $i <= $size; $i++) {
        	//only if index exists!
        	if(isset($addresses[$z])) {      	
				//start new thread
	            $threads[$z] = new Thread( 'pingHost' );
	            $threads[$z]->start( Transform2long($addresses[$z]['ip_addr']), $count, $timeout, true );
			}
	        $z++;				//next index
        }

        // wait for all the threads to finish 
        while( !empty( $threads ) ) {
            foreach( $threads as $index => $thread ) {
                if( ! $thread->isAlive() ) {
                	//get exit code
                	$exitCode = $thread->getExitCode();
                	
					//unset dead hosts
					if($exitCode != 0) {
						$dead[]=$addresses[$index];
						unset($addresses[$index]);
					}
  
                    //remove thread
                    unset( $threads[$index]);
                }
            }
            usleep(500);
        }
	}
}


// first add to IP table
foreach($addresses as $k=>$ip) {
	// try to resolve
	$ip['dns_name'] = ResolveDnsName ($ip['ip_addr']);
	if($ip['dns_name']['class']=="resolved")	{ 
		$ip['dns_name'] = $ip['dns_name']['name'];
		$addresses[$k]['dns_name'] = $ip['dns_name']; 
	}
	else { 
		$ip['dns_name'] = ""; 
		$addresses[$k]['dns_name'] = _("unresolved");
	}
	
	// insert
	if(!insert_discovered_ip($ip))	{ print "Cannot add discovered IP ".transform2long($ip['ip_addr'])."\n"; }
}

// all done, mail discovered?
if(sizeof($addresses)>0 && $email)
{
	//send text array, cron will do that by default if you don't redirect output > /dev/null 2>&1
	if($emailText) {
		print_r($stateDiff);		
	}
	//html
	else {
	
		$mail['from']		= "$settings[siteTitle] <ipam@$settings[siteDomain]>";
		$mail['headers']	= 'From: ' . $mail['from'] . "\r\n";
		$mail['headers']   .= "Content-type: text/html; charset=utf8" . "\r\n";
		$mail['headers']   .= 'X-Mailer: PHP/' . phpversion() ."\r\n";
		
		//subject
		$mail['subject'] 	= "phpIPAM new addresses detected ".date("Y-m-d H:i:s");
	
		//header
		$html[] = "<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.01 Transitional//EN' 'http://www.w3.org/TR/html4/loose.dtd'>";
		$html[] = "<html>";
		$html[] = "<head></head>";
		$html[] = "<body>";
		//title
		$html[] = "<h3>phpIPAM found ".sizeof($addresses)." new hosts</h3>";
		//table
		$html[] = "<table style='margin-left:10px;margin-top:5px;width:auto;padding:0px;border-collapse:collapse;border:1px solid gray;'>";
		$html[] = "<tr>";
		$html[] = "	<th style='padding:3px 8px;border:1px solid silver;border-bottom:2px solid gray;'>IP</th>";
		$html[] = "	<th style='padding:3px 8px;border:1px solid silver;border-bottom:2px solid gray;'>Hostname</th>";
		$html[] = "	<th style='padding:3px 8px;border:1px solid silver;border-bottom:2px solid gray;'>Subnet</th>";
		$html[] = "	<th style='padding:3px 8px;border:1px solid silver;border-bottom:2px solid gray;'>Section</th>";

		$html[] = "</tr>";
		//Changes
		foreach($addresses as $change) {
			//set subnet
			$subnet = getSubnetDetails($change['subnetId']);
			$subnetPrint = Transform2long($subnet['subnet'])."/".$subnet['mask']." - ".$subnet['description'];
			//set section
			$section = getSectionDetailsById($subnet['sectionId']);
			$sectionPrint = $section['name']." (".$section['description'].")";
			
			$html[] = "<tr>";
			$html[] = "	<td style='padding:3px 8px;border:1px solid silver;'>".Transform2long($change['ip_addr'])."</td>";
			$html[] = "	<td style='padding:3px 8px;border:1px solid silver;'>$change[dns_name]</td>";
			$html[] = "	<td style='padding:3px 8px;border:1px solid silver;'><a href='$settings[siteURL]".create_link("subnets",$section['id'],$subnet['id'])."'>$subnetPrint</a></td>";
			$html[] = "	<td style='padding:3px 8px;border:1px solid silver;'><a href='$settings[siteURL]".create_link("subnets",$section['id'])."'>$sectionPrint</a></td>";

			$html[] = "</tr>";
		}
		$html[] = "</table>";
		//footer
		
		//end
		$html[] = "</body>";
		$html[] = "</html>";
		
		//save to array
		$mail['content'] = implode("\n", $html);

		//send to all admins
		sendStatusUpdateMail($mail['content'], $mail['subject']);
	}
}

?>