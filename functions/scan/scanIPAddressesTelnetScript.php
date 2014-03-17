<?php

/* Script to check status of IP addresses provided in $argv in decimal, returns alive and dead */

//it can only be run from cmd!
$sapi_type = php_sapi_name();
if($sapi_type != "cli") { die(); }

// include required scripts
require_once( dirname(__FILE__) . '/../functions.php' );
require_once( dirname(__FILE__) . '/../scripts/Thread.php');

// no error reporting!
ini_set('display_errors', 1);
error_reporting(E_ALL ^ E_NOTICE ^ E_STRICT);

// test to see if threading is available
if( !Thread::available() ) 	{ 
	$error[] = "Threading is required for scanning subnets. Please recompile PHP with pcntl extension";
	$error   = json_decode($error);
	die($error); 
}

$timeout = 2;					//timeout in seconds

// get all IP addresses to be scanned from $argv cmd line
$addresses = explode(";",$argv[1]);

// get all ports to scan
$ports = $argv[2];


$z = 0;			//addresses array index


/*
test
*/
$ports = explode(";", $ports);

$out = array();

//reset array, set each IP together with port
foreach($addresses as $k=>$v) {
	foreach($ports as $p) {
		$addresses2[] = array("ip"=>$v, "port"=>$p);	
	}
}
$addresses = $addresses2;

// get size of addresses to ping
$size = sizeof($addresses);


// run per MAX_THREADS
for ($m=0; $m<=$size; $m += $MAX_THREADS) {
    // create threads 
    $threads = array();
    
    // fork processes
    for ($i = 0; $i <= $MAX_THREADS && $i <= $size; $i++) {
    	//only if index exists!
    	if(isset($addresses[$z])) {      	
			//start new thread
            $threads[$z] = new Thread( 'telnetHost' );
            $threads[$z]->start( Transform2long($addresses[$z]['ip']), $addresses[$z]['port'], $timeout, true );
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
            		$out['alive'][$addresses[$index]['ip']][] = $addresses[$index]['port'];
            	}
            	//ok, but offline
            	else {
	            	$out['dead'][$addresses[$index]['ip']][] = $addresses[$index]['port'];
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