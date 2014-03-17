<?php

/*
 *	Script to inserte imported file to database!
 **********************************************/
 
/* we need functions */
require_once('../../functions/functions.php');

/* verify that user is logged in */
isUserAuthenticated(true);

/* get subnet ID and type */
$subnetId = $_POST['subnetId'];
$filetype = $_POST['filetype'];
$filetype = end(explode(".", $filetype));


/* get $outFile based on provided filetype */
if ($filetype == "csv") {
	/* get file to string */
	$outFile = file_get_contents('csvupload/import.csv') or die ('<div class="alert alert alert-danger">'._('Cannot open csvupload/import.csv').'</div>');

	/* format file */
	$outFile = str_replace( array("\r\n","\r") , "\n" , $outFile);	//replace windows and Mac line break
	$outFile = explode("\n", $outFile);
}
else {
	/* include functions */
	require_once('../../functions/functions.php');
	/* get excel file */
	require_once('../../functions/excel_reader2.php');				//excel reader 2.21
	$data = new Spreadsheet_Excel_Reader('csvupload/import.xls' ,false);	
	
	//get number of rows
	$numRows = $data->rowcount(0);
	$numRows++;

	//get custom fields
	$myFields = getCustomFields('ipaddresses');
	$myFieldsSize = sizeof($myFields);
	
	//add custom fields
	$numRows = $numRows + $myFieldsSize;
	
	//get all to array!
	for($m=0; $m < $numRows; $m++) {

		//IP must be present!
		if(filter_var($data->val($m,'A'), FILTER_VALIDATE_IP)) {
		
			$outFile[$m]  = $data->val($m,'A').','.$data->val($m,'B').','.$data->val($m,'C').','.$data->val($m,'D').',';
			$outFile[$m] .= $data->val($m,'E').','.$data->val($m,'F').','.$data->val($m,'G').','.$data->val($m,'H').',';
			$outFile[$m] .= $data->val($m,'I');
			//add custom fields
			if(sizeof($myFields) > 0) {
				$currLett = "J";
				foreach($myFields as $field) {
					$outFile[$m] .= ",".$data->val($m,$currLett++);
				}
			}
		}
	}
}

/* import each value */
foreach($outFile as $k=>$line) {

	//escape " and '
	#$line = str_replace("\"", "\\\"", $line);
	#$line = str_replace("'", "\'", $line);

	// explode it to array for verifications
	$lineArr = explode(",", $line);
	
	// array size must be at least 9
	if(sizeof($lineArr)<9) {
		$errors[] = "Line $k is invalid";
		unset($outFile[$k]);									//wrong line, unset!
	}
	// all good, reformat
	else {
		// reformat IP state
		if	  ($lineArr[1]=="Offline"  || $lineArr[1]=="Offline")	{ $lineArr[1] = 0; }
		elseif($lineArr[1]=="Reserved" || $lineArr[1]=="Reserved")	{ $lineArr[1] = 2; }
		elseif($lineArr[1]=="DHCP"	   || $lineArr[1]=="DHCP")		{ $lineArr[1] = 3; }
		else														{ $lineArr[1] = 1; }
		
		// reformat device
		$devices = getAllUniqueDevices ();
		foreach($devices as $d) {
			if($d['hostname']==$lineArr[6])	{ $lineArr[6] = $d['id']; }
		}
		
		// insert
		$import = importCSVline ($lineArr, $subnetId);
		if (strlen($import) > 1) {
			$errors[] = $import;
		}
	}		
}


/* print errors */
if(isset($errors)) {
	print '<div class="alert alert alert-danger">'._('Errors occured when importing to database!').'<br>';
	foreach ($errors as $error) {
		print $error . "<br>";
	}
	print '</div>';
}
else {
	print '<div class="alert alert-success">'._('Import successfull').'!</div>';
}

/* erase file! */
unlink('csvupload/import.'.$filetype);

?>