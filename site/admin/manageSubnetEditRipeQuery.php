<?php

/** 
 * Function to get RIPe info for network
 ********************************************/

# required functions */
require_once('../../functions/functions.php'); 
# verify that user is admin
checkAdmin();
# verify post
CheckReferrer();

/* filter input */
$_POST = filter_user_input($_POST, true, true, false);


/* http://apps.db.ripe.net/whois/lookup/ripe/inetnum/212.58.224.0-212.58.255.255.html.xml */
/* http://apps.db.ripe.net/whois/lookup/ripe/inet6num/2102:840::/32.xml */


# identify address and set proper url
$type = IdentifyAddress($_POST['subnet']);
	
if ($type == "IPv4") 	{ $url = "http://apps.db.ripe.net/whois/lookup/ripe/inetnum/$_POST[subnet].xml"; }
else 					{ $url = "http://apps.db.ripe.net/whois/lookup/ripe/inet6num/$_POST[subnet].xml"; }

/* querry ripe db and parse result */
$xml = @simplexml_load_file($url);

/* fail */
if (!$xml) {
	/* save to json and return */
	header("Content-type: text/javascript");
	echo json_encode(array("Error"=>"Subnet not present in RIPE DB<br>Error opening URL $url"));	
}
else {
	foreach($xml->objects->object[0]->attributes->children() as $m=>$subtag) {
	    $a = (string) $subtag->attributes()->name;
	    $b = (string) $subtag->attributes()->value;
	    
	    # replace - with _
	    $a = str_replace("-", "_", $a);
	    
	    $out["$a"] .= $b.'\n';
	}
	
	# replace last newlines
	foreach($out as $key=>$val) {
		$out[$key] = rtrim($val, "\\n");
	}
	
	/* save to json and return */
	header("Content-type: text/javascript");
	echo json_encode($out);	
}

?>