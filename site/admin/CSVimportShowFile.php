<?php

/*
 *	Script to parse imported file!
 ********************************/


/* get filetype */
$filetype = $_POST['filetype'];
$filetype = end(explode(".", $filetype));


/* get $outFile based on provided filetype */
if ($filetype == "csv") {
	/* get file to string */
	$outFile = file_get_contents('csvupload/import.csv') or die (_('<div class="alert alert alert-danger">Cannot open csvupload/import.csv</div>'));

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
		$outFile[$m] = $data->val($m,'A').','.$data->val($m,'B').','.$data->val($m,'C').','.$data->val($m,'D').',
				   ' . $data->val($m,'E').','.$data->val($m,'F').','.$data->val($m,'G').','.$data->val($m,'H').',
				   ' . $data->val($m,'I');
		//add custom fields
		if(sizeof($myFields) > 0) {
			$currLett = "J";
			foreach($myFields as $field) {
				$outFile[$m] .= ",".$data->val($m,$currLett++);
			}
		}
	}
	/* 	echo $data->dump(false,false); */
}


/*
 *	print table
 *********************/
print '<table class="table table-condensed">';

// headers 
print '<tr>';
print '	<th>'._('IP').'</th>';
print '	<th>'._('Status').'</th>';
print '	<th>'._('Description').'</th>';
print '	<th>'._('Hostname').'</th>';
print '	<th>'._('MAC').'</th>';
print '	<th>'._('Owner').'</th>';
print '	<th>'._('Switch').'</th>';
print '	<th>'._('Port').'</th>';
print '	<th>'._('Note').'</th>';
// Add custom fields 
if(sizeof($myFields) > 0) {
	foreach($myFields as $field) {
		print "	<th>$field[name]</th>";
	}
}
print '</tr>';


// values - $outFile is provided by showscripts
$errors = 0;
foreach($outFile as $line) {

	//put it to array
	$field = explode(",", $line);

	//verify IP address
	if(!filter_var($field[0], FILTER_VALIDATE_IP)) 	{ $class = "danger";	$errors++; }
	else											{ $class = ""; }

	//print
	print '<tr class="'.$class.'">';
	foreach ($field as $value) {
		if (!empty($field[0])) {			//IP address must be present otherwise ignore field
			print '<td>'. $value .'</td>';
		}
	}
	print '</tr>';
}
print '</table>';
?>

<!-- confirmation -->
<h4>3.) <?php print _('Import to database'); ?></h4>
<hr>
<?php
// errors?
if($errors>0) {
	print "<div class='alert alert alert-danger'>"._("Errors marked with red will be ignored from importing")."!</div>";
}
?>
<br><?php print _('Should I import values to database'); ?>?

<!-- YES / NO -->
<div class="btn-group" style="margin-bottom:10px;">
	<input type="button" value="<?php print _('Yes'); ?>" class="btn btn-sm btn-default btn-success" id="csvImportYes">
	<input type="button" value="<?php print _('No'); ?>"  class="btn btn-sm btn-default" id="csvImportNo">
</div>	
