<?php

// include required scripts
require_once( dirname(__FILE__) . '/../functions.php' );
require_once( dirname(__FILE__) . '/Thread.php');
require_once( dirname(__FILE__) . '/../functions-mail.php');
require_once( dirname(__FILE__) . '/../scan/config-scan.php');

/*
set cronjob:
# update host statuses exery 15 minutes
*\/15 * * * *  /usr/local/bin/php /<sitepath>/functions/scripts/pingCheck.php
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


//get all IP addresses to be scanned
$addresses = getAllIPsforScan(true);

//get settings
$settings = getAllSettings();
// set ping statuses
$statuses = explode(";", $settings['pingStatus']);

//set execution start time
$sTime = time();


//verify that pign path is correct
if(!file_exists($pathPing)) {
	print "Invalid ping path! You can set parameters for scan under functions/scan/config-scan.php\n";
}
//threads not supported, scan 1 by one - it is highly recommended to enable threading for php
elseif(!$threads) {
	//print warning
	print "Warning: Threading is not supported!\n";
	$m=0;														//Array count
	//scan each
	foreach($addresses as $ip) {
		//calculate diff since last alive
		$tDiff = $sTime - strtotime($ip['lastSeen']);
		//set Old status
		if($tDiff < $statuses[1])	{ $addresses[$m]['oldStatus'] = 0; }	//old online
		else						{ $addresses[$m]['oldStatus'] = 2; }	//old offline
		//get status
		$code = pingHost (transform2long($ip['ip_addr']), $count, $timeout, false);
		//Online
		if($code == "0") {
			//update IP status
			@updateLastSeen($ip['id']);
			//set new seen
			$addresses[$m]['newSeen'] = date("Y-m-d H:i:s");
		} else {
			$code = 2;
		}
		//save new status
		$addresses[$m]['newStatus'] = $code;
		//check for status change
		if($addresses[$m]['oldStatus'] != $code) {
			$stateDiff[] = $addresses[$m];					//save to change array
		}
		//save exit code
		$addresses[$m]['newStatus'] = $code;
		//next
		$m++;
	}
}
//threaded
else {
	//get size of addresses to ping
	$size = sizeof($addresses);
	
	$z = 0;			//addresses array index

	//run per MAX_THREADS
    for ($m=0; $m<=$size; $m += $MAX_THREADS) {
        // create threads 
        $threads = array();
        
        //fork processes
        for ($i = 0; $i <= $MAX_THREADS && $i <= $size; $i++) {
        	//only if index exists!
        	if(isset($addresses[$z])) {
        		//calculate diff since last alive
				$tDiff = $sTime - strtotime($addresses[$z]['lastSeen']);
				//set Old status
				if($tDiff <= $statuses[1])	{ $addresses[$z]['oldStatus'] = 0; }	//old online
				else						{ $addresses[$z]['oldStatus'] = 2; }	//old offline        	

				//start new thread
	            $threads[$z] = new Thread( 'pingHost' );
	            $threads[$z]->start( Transform2long($addresses[$z]['ip_addr']), $count, $timeout, true );
	            $z++;				//next index
			}
        }

        // wait for all the threads to finish 
        while( !empty( $threads ) ) {
            foreach( $threads as $index => $thread ) {
                if( ! $thread->isAlive() ) {
                	//get exit code
                	$exitCode = $thread->getExitCode();
                	
                	//online, check diff
                	if($exitCode == 0) {
						//set new seen
						$addresses[$index]['newSeen'] = date("Y-m-d H:i:s");
						//if old is offline than check for time diff
						if($addresses[$index]['oldStatus']==2) {
							//calculate diff since last alive
							$tDiff2 = time() - strtotime($addresses[$index]['lastSeen']);
							//set New status
							if($tDiff2 >= $statuses[1])	{ 
								$stateDiff[] = $addresses[$index];	 				//change to online 
							}							
						}
	                	//update IP status
						@updateLastSeen($addresses[$index]['id']);	
                	} 
                	else {
                		//now offline
						$exitCode = 2;
						//if online before change
						if($addresses[$index]['oldStatus']==0) {
							//calculate diff since last alive
							$tDiff2 = time() - strtotime($addresses[$index]['lastSeen']);	
							//set New status
							if($tDiff2 >= $statuses[1])	{ 
								$stateDiff[] = $addresses[$index];	 				//change to offline
							}	
						}
					}
                	//save exit code for host
                    $addresses[$index]['newStatus'] = $exitCode;
                    //remove thread
                    unset( $threads[$index] );
                }
            }
            usleep(500);
        }
	}
}

//all done, mail diff?
if(sizeof($stateDiff)>0 && $email)
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
		$mail['subject'] 	= "phpIPAM IP state change ".date("Y-m-d H:i:s");
	
		//header
		$html[] = "<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.01 Transitional//EN' 'http://www.w3.org/TR/html4/loose.dtd'>";
		$html[] = "<html>";
		$html[] = "<head></head>";
		$html[] = "<body>";
		//title
		$html[] = "<h3>phpIPAM host changes</h3>";
		//table
		$html[] = "<table style='margin-left:10px;margin-top:5px;width:auto;padding:0px;border-collapse:collapse;border:1px solid gray;'>";
		$html[] = "<tr>";
		$html[] = "	<th style='padding:3px 8px;border:1px solid silver;border-bottom:2px solid gray;'>IP</th>";
		$html[] = "	<th style='padding:3px 8px;border:1px solid silver;border-bottom:2px solid gray;'>Description</th>";
		$html[] = "	<th style='padding:3px 8px;border:1px solid silver;border-bottom:2px solid gray;'>Subnet</th>";
		$html[] = "	<th style='padding:3px 8px;border:1px solid silver;border-bottom:2px solid gray;'>Section</th>";
		$html[] = "	<th style='padding:3px 8px;border:1px solid silver;border-bottom:2px solid gray;'>last seen</th>";
		$html[] = "	<th style='padding:3px 8px;border:1px solid silver;border-bottom:2px solid gray;'>old status</th>";
		$html[] = "	<th style='padding:3px 8px;border:1px solid silver;border-bottom:2px solid gray;'>new status</th>";

		$html[] = "</tr>";
		//Changes
		foreach($stateDiff as $change) {
			//reformat statuses
			if($change['oldStatus'] == 0)		{ $oldStatus = "<font style='color:#04B486'>Online</font>"; }
			elseif($change['oldStatus'] == 1)	{ $oldStatus = "Check failed"; }
			else								{ $oldStatus = "<font style='color:#DF0101'>Offline</font>"; }
			
			if($change['newStatus'] == 0)		{ $newStatus = "<font style='color:#04B486'>Online</font>"; }
			elseif($change['newStatus'] == 1)	{ $oldStatus = "Check failed"; }
			else								{ $newStatus = "<font style='color:#DF0101'>Offline</font>"; }
			//set subnet
			$subnet = getSubnetDetails($change['subnetId']);
			$subnetPrint = Transform2long($subnet['subnet'])."/".$subnet['mask']." - ".$subnet['description'];
			//set section
			$section = getSectionDetailsById($subnet['sectionId']);
			$sectionPrint = $section['name']." (".$section['description'].")";
			//ago
			if(is_null($change['lastSeen']) || $change['lastSeen']=="0000-00-00 00:00:00") {
				$ago	  = "never";
			} else {
				$timeDiff = $sTime - strtotime($change['lastSeen']);
				$ago 	  = $change['lastSeen']." (".sec2hms($timeDiff)." ago)";
			}
			
			$html[] = "<tr>";
			$html[] = "	<td style='padding:3px 8px;border:1px solid silver;'><a href='$settings[siteURL]subnets/$section[id]/$subnet[id]/'>".Transform2long($change['ip_addr'])."</a></td>";
			$html[] = "	<td style='padding:3px 8px;border:1px solid silver;'>$change[description]</td>";
			$html[] = "	<td style='padding:3px 8px;border:1px solid silver;'><a href='$settings[siteURL]subnets/$section[id]/$subnet[id]/'>$subnetPrint</a></td>";
			$html[] = "	<td style='padding:3px 8px;border:1px solid silver;'><a href='$settings[siteURL]subnets/$section[id]/'>$sectionPrint</a></td>";
			$html[] = "	<td style='padding:3px 8px;border:1px solid silver;'>$ago</td>";
			$html[] = "	<td style='padding:3px 8px;border:1px solid silver;'>$oldStatus</td>";
			$html[] = "	<td style='padding:3px 8px;border:1px solid silver;'>$newStatus</td>";

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
		//mail($settings['siteAdminMail'], $mail['subject'], $mail['content'], $mail['headers']);
	}
}

?>