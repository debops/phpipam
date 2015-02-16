<?php 

/**
 *	Export search results
 ****************************/
require_once('../../functions/functions.php');

/* hide errors! */
ini_set('display_errors', 0);

/* get query */
$searchTerm = $_GET['searchTerm'];

/* verify that user is admin */
checkAdmin();

/* get result */
/* change * to % for database wildchar */
$searchTerm = str_replace("*", "%", $searchTerm);

/* check if mac address */
if(strlen($searchTerm) == 17) {
	//count : -> must be 5
	if(substr_count($searchTerm, ":") == 5) {
		$type = "mac";
	}
}
else if(strlen($searchTerm) == 12) {
	//no dots or : -> mac without :
	if( (substr_count($searchTerm, ":") == 0) && (substr_count($searchTerm, ".") == 0) ) {
		$type = "mac";
	}	
}
/* ok, not MAC! */
else {
	/* identify address type */
	$type = IdentifyAddress( $searchTerm );
}

if ($type == "IPv4") {
	/* reformat the IPv4 address! */
	$searchTermEdited = reformatIPv4forSearch ($searchTerm);
}
else if ($type == "mac") {
}
else {
	/* reformat the IPv4 address! */
	$searchTermEdited = reformatIPv6forSearch ($searchTerm);
}


/* get all custom fields */
$myFields = getCustomFields('ipaddresses');
$myFieldsSize = sizeof($myFields);


/* set the query */
$query  = 'select * from ipaddresses where ';
/* $query .= 'ip_addr like "' . $searchTerm . '%" '; */					//ip address in decimal
$query .= '`ip_addr` between "'. $searchTermEdited['low'] .'" and "'. $searchTermEdited['high'] .'" ';	//ip range
$query .= 'or `dns_name` like "%' . $searchTerm . '%" ';					//hostname
$query .= 'or `owner` like "%' . $searchTerm . '%" ';						//owner
# custom fields
if(sizeof($myFields) > 0) {
	foreach($myFields as $myField) {
		$query .= 'or `'. $myField['name'] .'` like "%' . $searchTerm . '%" ';
	}
}
$query .= 'or `switch` like "%' . $searchTerm . '%" ';
$query .= 'or `port` like "%' . $searchTerm . '%" ';						//port search
$query .= 'or `description` like "%' . $searchTerm . '%" ';				//descriptions
$query .= 'or `note` like "%' . $searchTerm . '%" ';						//note
$query .= 'or `mac` like "%' . $searchTerm . '%" ';						//mac
$query .= 'order by `ip_addr` asc;';

/* get result */
$result = searchAddresses ($query);


/* get all selected fields for IP print */
$setFieldsTemp = getSelectedIPaddrFields();
/* format them to array! */
$setFields = explode(";", $setFieldsTemp);

//count fields!
$fieldCount = sizeof($setFields) + $myFieldsSize + 3;


/*
 *	Write xls
 *********************/
require_once '../../functions/PEAR/Spreadsheet/Excel/Writer.php';

// Create a workbook
$filename = _("phpipam_search_export_"). $searchTerm .".xls";
$workbook = new Spreadsheet_Excel_Writer();

//formatting headers
$format_header =& $workbook->addFormat();
$format_header->setBold();
$format_header->setColor('white');
$format_header->setFgColor('black');

//formatting titles
$format_title =& $workbook->addFormat();
$format_title->setColor('black');
$format_title->setFgColor(22);			//light gray
$format_title->setBottom(2);
$format_title->setLeft(1);
$format_title->setRight(1);
$format_title->setTop(1);
$format_title->setAlign('left');

//formatting content - borders around IP addresses
$format_right =& $workbook->addFormat();
$format_right->setRight(1);
$format_left =& $workbook->addFormat();
$format_left->setLeft(1);
$format_top =& $workbook->addFormat();
$format_top->setTop(1);


$lineCount = 0;		//for line change
$m = 0;				//for section change

// Create a worksheet
$worksheet =& $workbook->addWorksheet(_('IP Search results'));

//write headers
$x = 0;
	$worksheet->write($lineCount, $x, _('ip address') ,$format_title);		$x++;

# state
if(in_array('state', $setFields)) {
	$worksheet->write($lineCount, $x, _('state') ,$format_title);			$x++;
}
# description, note
	$worksheet->write($lineCount, $x, _('description') ,$format_title);	$x++;
	$worksheet->write($lineCount, $x, _('hostname') ,$format_title);		$x++;
# switch
if(in_array('switch', $setFields)) {
	$worksheet->write($lineCount, $x, _('device') ,$format_title);			$x++;
}
# port
if(in_array('port', $setFields)) {
	$worksheet->write($lineCount, $x, _('port') ,$format_title);			$x++;
}
# owner
if(in_array('owner', $setFields)) {
	$worksheet->write($lineCount, $x, _('owner') ,$format_title);			$x++;
}
# mac
if(in_array('mac', $setFields)) {
	$worksheet->write($lineCount, $x, _('mac') ,$format_title);				$x++;
}
# note
if(in_array('note', $setFields)) {
	$worksheet->write($lineCount, $x, _('note') ,$format_title);			$x++;
}
//custom
if(sizeof($myFields) > 0) {
	foreach($myFields as $myField) {
	$worksheet->write($lineCount, $x, $myField['name'], $format_title);	$x++;
	}
}

//new line
$lineCount++;


//Write IP addresses
foreach ($result as $ip) {

	//get the Subnet details
	$subnet = getSubnetDetailsById ($ip['subnetId']);
	//get section
	$section = getSectionDetailsById ($subnet['sectionId']);
	
	//Write title - subnet details
	$vlan = subnetGetVLANdetailsById($subnet['vlanId']);
	//format vlan
	if(strlen($vlan['number']) > 0) {
		$vlanText = " (vlan: " . $vlan['number'];	
		if(strlen($vlan['name']) > 0) {
			$vlanText .= ' - '. $vlan['name'] . ')';
		}
		else {
				$vlanText .= ")";
		}
	}

	//section change
	if ($result[$m]['subnetId'] != $result[$m-1]['subnetId']) {

		//new line
		$lineCount++;

		//subnet details
		$worksheet->write($lineCount, 0, transform2long($subnet['subnet']) . "/" .$subnet['mask'] . " - " . $subnet['description'] . $vlanText, $format_header );
		$worksheet->mergeCells($lineCount, 0, $lineCount, $fieldCount-1);
	
		//new line
		$lineCount++;
	}
	$m++;
	
	
	//we need to reformat state!
	switch($ip['state']) {
		case 0: $ip['state'] = "Offline";	break;
		case 1: $ip['state'] = "Active";	break;
		case 2: $ip['state'] = "Reserved";	break;
	}
	
	$x = 0;
	$worksheet->write($lineCount, $x, transform2long($ip['ip_addr']), $format_left);	$x++;
	# state
	if(in_array('state', $setFields)) {
	$worksheet->write($lineCount, $x, _($ip['state']) );					$x++;
	}
	$worksheet->write($lineCount, $x, $ip['description']);					$x++;
	$worksheet->write($lineCount, $x, $ip['dns_name']);						$x++;
	# switch
	if(in_array('switch', $setFields)) {
		if(strlen($ip['switch'])>0 && $ip['switch']!=0) {
			# get switch
			$switch = getDeviceDetailsById($ip['switch']);
			$ip['switch'] = $switch['hostname'];
		}
	$worksheet->write($lineCount, $x, $ip['switch']);						$x++;
	}
	# port
	if(in_array('port', $setFields)) {
	$worksheet->write($lineCount, $x, $ip['port']);							$x++;
	}
	# owner
	if(in_array('owner', $setFields)) {
	$worksheet->write($lineCount, $x, $ip['owner']);						$x++;
	}
	# mac
	if(in_array('mac', $setFields)) {
	$worksheet->write($lineCount, $x, $ip['mac']);							$x++;
	}
	# note
	if(in_array('note', $setFields)) {
	$worksheet->write($lineCount, $x, $ip['note']);							$x++;
	}
	
	#custom
	if(sizeof($myFields) > 0) {
		foreach($myFields as $myField) {
			$worksheet->write($lineCount, $x, $ip[$myField['name']]); $x++;
		}
	}
	
	//new line
	$lineCount++;
}




/**
 *	Subnet results
 ***************************/
/* check also subnets! */
$allSubnets = searchSubnets ($searchTerm, $searchTermEdited);

$lineCount = 0;

$worksheet =& $workbook->addWorksheet(_('Subnet search results'));

//write headers
$worksheet->write($lineCount, 0, _('Section') ,$format_title);
$worksheet->write($lineCount, 1, _('Subet') ,$format_title);
$worksheet->write($lineCount, 2, _('Mask') ,$format_title);
$worksheet->write($lineCount, 3, _('Description') ,$format_title);
$worksheet->write($lineCount, 4, _('Master subnet') ,$format_title);
$worksheet->write($lineCount, 5, _('VLAN') ,$format_title);
$worksheet->write($lineCount, 6, _('IP requests') ,$format_title);

//new line
$lineCount++;

foreach($allSubnets as $line) {

	//get section details 
	$section = getSectionDetailsById ($line['sectionId']);
	
	//format master subnet
	if($line['masterSubnetId'] == 0) { $line['masterSubnetId'] = "/"; }
	else {
		$line['masterSubnetId'] = getSubnetDetailsById ($line['masterSubnetId']);
		$line['masterSubnetId'] = transform2long($line['masterSubnetId']['subnet']) .'/'. $line['masterSubnetId']['mask'];
	}
	//admin lock
	if($line['adminLock'] == 1) { $line['adminLock'] = 'yes'; }
	else 						{ $line['adminLock'] = ''; }
	//allowRequests
	if($line['allowRequest'] == 1) 	{ $line['allowRequest'] = 'yes'; }
	else 							{ $line['allowRequest'] = ''; }

	//print subnet
	$worksheet->write($lineCount, 0, $section['name'], $format_left);
	$worksheet->write($lineCount, 1, transform2long($line['subnet']));
	$worksheet->write($lineCount, 2, $line['mask']);
	$worksheet->write($lineCount, 3, $line['description']);
	$worksheet->write($lineCount, 4, $line['masterSubnetId']);
	$worksheet->write($lineCount, 5, $line['VLAN']);
	$worksheet->write($lineCount, 6, $line['allowRequests']);
	
	//new line
	$lineCount++;
}

//top border line at bottom of IP addresses
$worksheet->write($lineCount, 0, "", $format_top);
$worksheet->write($lineCount, 1, "", $format_top);
$worksheet->write($lineCount, 2, "", $format_top);
$worksheet->write($lineCount, 3, "", $format_top);
$worksheet->write($lineCount, 4, "", $format_top);
$worksheet->write($lineCount, 5, "", $format_top);
$worksheet->write($lineCount, 6, "", $format_top);


// sending HTTP headers
$workbook->send($filename);

// Let's send the file
$workbook->close();



?>