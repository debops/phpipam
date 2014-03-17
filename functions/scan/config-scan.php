<?php

/**
 *
 *	Config file for network scanning paths etc
 *
 */

//general configs
$scanDNSresolve = true;							// try to resolve DNS name
$scanIPv6 		= false;						// not yet

//configs
$settings = getAllSettings();

// set max concurrent threads
$MAX_THREADS = $settings['scanMaxThreads'];	

// ping path
$pathPing = $settings['scanPingPath'];

// nmap path
//$pathNmap = "/usr/local/bin/nmap";


?>