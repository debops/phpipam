<?php

/* Script to check status of IP addresses provided in $argv in decimal, returns alive and dead */

//it can only be run from cmd!
$sapi_type = php_sapi_name();
if($sapi_type != "cli") { die(); }

// include required scripts
require_once( dirname(__FILE__) . '/../functions.php' );
require_once( dirname(__FILE__) . '/../scripts/Thread.php');
require_once( dirname(__FILE__) . '/config-scan.php');

// no error reporting!
ini_set('display_errors', 1);
error_reporting(E_ALL ^ E_NOTICE ^ E_STRICT);

// test to see if threading is available
if( !Thread::available() ) 	{ 
	$res['errors'] = "Threading is required for scanning subnets. Please recompile PHP with pcntl extension";
	$res   = json_encode($res);
	print_r($res);
	die(); 	
}

$count = 1;						// number of pings
$timeout = 1;					// timeout in seconds

// set result arrays
$alive = array();				// alive hosts
$dead  = array();				// dead hosts

// get scan type (update or discovery)
// get subnet to be scanned from argument1 $argv[1]
// get subnetId from $argv2
$scanType	= $argv[1];
$subnetFull	= $argv[2];
$subnetId 	= $argv[3];

$subTemp = explode("/", $subnetFull);
$subnet  = $subTemp[0];
$mask	 = $subTemp[1];



/* for discovery ping */
if($scanType=="discovery") {
	
	// get all existing IP addresses
	$addresses = getIpAddressesBySubnetId ($subnetId);
		
	// set start and end IP address
	$calc = calculateSubnetDetailsNew ( $subnet, $mask, 0, 0, 0, 0 );
	$max = $calc['maxhosts'];

	// we should support only up to 4094 hosts!
	if(($max - sizeof($addresses))>4094) {
		$res['errors'] = "Scanning from GUI is only available for subnets up to /20 or 4094 hosts!";
		$res   = json_encode($res);
		print_r($res);
		die(); 			
	}
		
	// loop and get all IP addresses for ping
	for($m=1; $m<=$max; $m++) {
		$ip[] = transform2decimal($subnet)+$m;
	}
	
	// remove already existing
	foreach($addresses as $a) {
		$key = array_search($a['ip_addr'], $ip);
		if($key!==false) {
			unset($ip[$key]);	
		}
	}
	
	//reindex array for pinging
	$ip = array_values($ip);

}
/* status update */
elseif($scanType=="update") {
	
	// get all existing IP addresses
	$addresses = getIpAddressesBySubnetId ($subnetId);
	
	// we should support only up to 4096 hosts!
	if(sizeof($addresses)>4094) {
		$res['errors'] = "Scanning from GUI is only available for subnets up to /20 or 4094 hosts!";
		$res   = json_encode($res);
		print_r($res);
		die(); 	
	}
	
	# exclude those marked as don't ping
	$n=0;
	$excluded = array();
	foreach($addresses as $m=>$ipaddr) {
		if($ipaddr['excludePing']=="1") {
			//set result
			$excluded[] = $ipaddr['ip_addr'];
			//next
			$n++;
		}	
		# create ip's from ip array for ones that need to be checked
		else {
			$ip[] = $ipaddr['ip_addr'];
		}
		
		# set excluded for result
		$out['excluded'] = $excluded;
	}

	//reindex array for pinging
	$ip = array_values($ip);
	
	//set max
	$max = sizeof($ip);
}


$z = 0;			//addresses array index


// run per MAX_THREADS
for ($m=0; $m<=$max; $m += $MAX_THREADS) {
    // create threads 
    $threads = array();
    
    // fork processes
    for ($i = 0; $i <= $MAX_THREADS && $i <= $max; $i++) {
    	//only if index exists!
    	if(isset($ip[$z])) {      	
			//start new thread
            $threads[$z] = new Thread( 'pingHost' );
            $threads[$z]->start( Transform2long($ip[$z]), $count, $timeout, true );
            $z++;				//next index
		}
    }

    // wait for all the threads to finish 
    while( !empty( $threads ) ) {
        foreach( $threads as $index => $thread ) {
            if( ! $thread->isAlive() ) {
            	//get exit code
            	$exitCode = $thread->getExitCode();
            	//online, save to array
            	if($exitCode == 0) {
            		$out['alive'][] = $ip[$index];
            	}
            	//ok, but offline
            	elseif($exitCode == 1 || $exitCode == 2) {
	            	$out['dead'][]  = $ip[$index];
            	}
            	//error
            	else {
	            	$out["error"][] = $ip[$index];
            	}
            	//$out['exitcodes'][] = $exitCode;
                //remove thread
                unset( $threads[$index] );
            }
        }
        usleep(200);
    }
}

# save to json
$out = json_encode($out);

# print result
print_r($out);
?>