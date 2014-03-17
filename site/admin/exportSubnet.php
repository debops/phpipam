<?php

/**
 *	Generate XLS file for subnet
 *********************************/

/* required functions */
require_once('../../functions/functions.php'); 

/* we dont need any errors! */
ini_set('display_errors', 0);

/* verify that user is authenticated! */
isUserAuthenticated ();

require_once '../../functions/PEAR/Spreadsheet/Excel/Writer.php';

// Create a workbook
$filename = "phpipam_subnet_export.xls";
$workbook = new Spreadsheet_Excel_Writer();

//get requested subnet Id
$subnetId = $_REQUEST['subnetId'];

//get all subnet details
$subnet = getSubnetDetailsById ($subnetId);

//get all IP addresses in subnet
$ipaddresses = getIpAddressesBySubnetId ($subnetId);

//get all custom fields!
$myFields = getCustomFields('ipaddresses');
$myFieldsSize = sizeof($myFields);

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


//set column size
$colSize = sizeof($_GET);
$colSize = $colSize + $myFieldsSize -2;


// Create a worksheet
$worksheet =& $workbook->addWorksheet($subnet['description']);

$lineCount = 0;

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


$worksheet->write($lineCount, $rowCount, transform2long($subnet['subnet']) . "/" .$subnet['mask'] . " - " . $subnet['description'] . $vlanText, $format_header );
$worksheet->mergeCells($lineCount, $rowCount, $lineCount, $colSize);
		
$lineCount++;
		
//set row count
$rowCount = 0;

//write headers
if( (isset($_GET['ip_addr'])) && ($_GET['ip_addr'] == "on") ) {
	$worksheet->write($lineCount, $rowCount, _('ip address') ,$format_title);
	$rowCount++;
}
if( (isset($_GET['state'])) && ($_GET['state'] == "on") ) {
	$worksheet->write($lineCount, $rowCount, _('ip state') ,$format_title);
	$rowCount++;
}
if( (isset($_GET['description'])) && ($_GET['description'] == "on") ) {
	$worksheet->write($lineCount, $rowCount, _('description') ,$format_title);
	$rowCount++;
}
if( (isset($_GET['dns_name'])) && ($_GET['dns_name'] == "on") ) {
	$worksheet->write($lineCount, $rowCount, _('hostname') ,$format_title);
	$rowCount++;
}
if( (isset($_GET['mac'])) && ($_GET['mac'] == "on") ) {
	$worksheet->write($lineCount, $rowCount, _('mac') ,$format_title);
	$rowCount++;
}
if( (isset($_GET['owner'])) && ($_GET['owner'] == "on") ) {
	$worksheet->write($lineCount, $rowCount, _('owner') ,$format_title);
	$rowCount++;
}
if( (isset($_GET['switch'])) && ($_GET['switch'] == "on") ) {
	$worksheet->write($lineCount, $rowCount, _('switch') ,$format_title);
	$rowCount++;
}
if( (isset($_GET['port'])) && ($_GET['port'] == "on") ) {
	$worksheet->write($lineCount, $rowCount, _('port') ,$format_title);
	$rowCount++;
}
if( (isset($_GET['note'])) && ($_GET['note'] == "on") ) {
	$worksheet->write($lineCount, $rowCount, _('note') ,$format_title);
	$rowCount++;
}

//custom
if(sizeof($myFields) > 0) {
	foreach($myFields as $myField) {
		//set temp name - replace space with three ___
		$myField['nameTemp'] = str_replace(" ", "___", $myField['name']);
		
		if( (isset($_GET[$myField['nameTemp']])) && ($_GET[$myField['nameTemp']] == "on") ) {
			$worksheet->write($lineCount, $rowCount, $myField['name'] ,$format_title);
			$rowCount++;
		}
	}
}
	
		
$lineCount++;
		
//write all IP addresses
foreach ($ipaddresses as $ip) {

	//reset row count
	$rowCount = 0;
		
	//we need to reformat state!
	switch($ip['state']) {
		case 0: $ip['state'] = _("Offline");	break;
		case 1: $ip['state'] = _("Active");		break;
		case 2: $ip['state'] = _("Reserved");	break;
		case 3: $ip['state'] = _("DHCP");		break;
	}
	
	//change switch ID to name
	$devices = getAllUniqueDevices ();
	foreach($devices as $d) {
		if($d['id']==$ip['switch'])	{ $ip['switch']=$d['hostname']; }
	}
	
	if( (isset($_GET['ip_addr'])) && ($_GET['ip_addr'] == "on") ) {
		$worksheet->write($lineCount, $rowCount, transform2long($ip['ip_addr']), $format_left);
		$rowCount++;
	}
	if( (isset($_GET['state'])) && ($_GET['state'] == "on") ) {
		$worksheet->write($lineCount, $rowCount, $ip['state']);
		$rowCount++;
	}
	if( (isset($_GET['description'])) && ($_GET['description'] == "on") ) {
		$worksheet->write($lineCount, $rowCount, $ip['description']);
		$rowCount++;
	}
	if( (isset($_GET['dns_name'])) && ($_GET['dns_name'] == "on") ) {
		$worksheet->write($lineCount, $rowCount, $ip['dns_name']);
		$rowCount++;
	}
	if( (isset($_GET['mac'])) && ($_GET['mac'] == "on") ) {
		$worksheet->write($lineCount, $rowCount, $ip['mac']);
		$rowCount++;
	}
	if( (isset($_GET['owner'])) && ($_GET['owner'] == "on") ) {
		$worksheet->write($lineCount, $rowCount, $ip['owner']);
		$rowCount++;
	}
	if( (isset($_GET['switch'])) && ($_GET['switch'] == "on") ) {
		$worksheet->write($lineCount, $rowCount, $ip['switch']);
		$rowCount++;
	}
	if( (isset($_GET['port'])) && ($_GET['port'] == "on") ) {
		$worksheet->write($lineCount, $rowCount, $ip['port']);
		$rowCount++;
	}
	if( (isset($_GET['note'])) && ($_GET['note'] == "on") ) {
		$worksheet->write($lineCount, $rowCount, $ip['note']);
		$rowCount++;
	}
	
	//custom
	if(sizeof($myFields) > 0) {
		foreach($myFields as $myField) {
			//set temp name - replace space with three ___
			$myField['nameTemp'] = str_replace(" ", "___", $myField['name']);
			
			if( (isset($_GET[$myField['nameTemp']])) && ($_GET[$myField['nameTemp']] == "on") ) {
				$worksheet->write($lineCount, $rowCount, $ip[$myField['name']]);
				$rowCount++;
			}
		}
	}
				
	$lineCount++;
}


//new line
$lineCount++;

// sending HTTP headers
$workbook->send($filename);

// Let's send the file
$workbook->close();

?>