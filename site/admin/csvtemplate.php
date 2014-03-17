<?php

/**
 *	Generate XLS file
 *********************************/
/* required functions */
require_once('../../functions/functions.php'); 

/* we dont need any errors! */
ini_set('display_errors', 0);

/* verify that user is admin */
checkAdmin();

require_once '../../functions/PEAR/Spreadsheet/Excel/Writer.php';

// Create a workbook
$filename = "phpipam_template_". date("Y-m-d") .".xls";
$workbook = new Spreadsheet_Excel_Writer();


//get all custom fields!
$myFields = getCustomFields('ipaddresses');

// Create a worksheet
$worksheet = $workbook->addWorksheet("template");

$lineCount = 1;


// set headers
$worksheet->write($lineCount, 0, _('ip address'));
$worksheet->write($lineCount, 1, _('ip state'));
$worksheet->write($lineCount, 2, _('description'));
$worksheet->write($lineCount, 3, _('hostname'));
$worksheet->write($lineCount, 4, _('mac'));
$worksheet->write($lineCount, 5, _('owner'));
$worksheet->write($lineCount, 6, _('device'));
$worksheet->write($lineCount, 7, _('port'));
$worksheet->write($lineCount, 8, _('note'));
$fc = 9;
foreach($myFields as $k=>$f) {
	$worksheet->write($lineCount, $fc, $k);
	$fc++;
}

// breaks
$lineCount = $lineCount +7;
$worksheet->write($lineCount, 0, "Available options for state and devices (delete all this before importing!)");
$lineCount++;

// write options for state
$worksheet->write($lineCount, 0, "Options for IP state:");
$worksheet->write($lineCount, 1, "Active");
$lineCount++;
$worksheet->write($lineCount, 1, "Offline");
$lineCount++;
$worksheet->write($lineCount, 1, "Reserved");
$lineCount++;
$worksheet->write($lineCount, 1, "DHCP");
$lineCount++;
$lineCount++;
$lineCount++;


// write options for devices
$devices = getAllUniqueDevices ();
$worksheet->write($lineCount, 0, "Available devices:");
foreach($devices as $k=>$d) {
	$worksheet->write($lineCount, 1, $d['hostname']);
	$lineCount++;
}
$lineCount++;
$lineCount++;


// sending HTTP headers
$workbook->send($filename);

// Let's send the file
$workbook->close();

?>