<?php

/**
 * Search IRPE databse for AS imports
 *************************************************/

/* required functions */
require_once('../../functions/functions.php'); 

/* verify that user is admin */
checkAdmin();

# get AS
$as = $_POST['as'];

# strip AS if provided, to get just the number
if(substr($as, 0,2)=="AS" || substr($as, 0,2)=="as") {
	$as = substr($as, 2);
};


//open connection
$povezava = fsockopen("whois.ripe.net", 43, $errno, $errstr, 5); 
if(!$povezava) 	{ echo "$errstr ($errno)";	}
else { 
	//fetch result
	fputs ($povezava, '-i origin as'. $as ."\r\n"); 
	//save result to var out
    while (!feof($povezava)) { $out .= fgets($povezava); }
     
    //parse it
    $out = explode("\n", $out);
    
    //we only need route
    foreach($out as $line) {
		if (strlen(strstr($line,"route"))>0) {
			//replace route6 with route
			$line = str_replace("route6:", "route:", $line);
			//only take IP address
			$line = explode("route:", $line);
			$line = trim($line[1]);
			//set result
			$subnet[] = $line;
		}
    }
}

/* get all sections */
$sections = fetchSections();



if(sizeof($subnet) == 0) {
	print '<div class="alert alert-danger alert-absolute">'._('No subnets found').'!</div></td>'. "\n";
}
else {

	print '<form name="asImport" id="asImport">';
	print '<table class="asImport table table-striped table-condensed table-top table-auto">';

	print '<tr>';
	print '	<th colspan="5">'._('I found the following routes belonging to AS').' '.$as.':</th>';
	print '</tr> ';


	print "<tr>";
	print "	<th></th>";
	print "	<th>"._('Subnet')."</th>";
	print "	<th>"._('select section')."</th>";
	print "	<th>"._('Description')."</th>";
	print "	<th>"._('VLAN')."</th>";

	print "</tr>";
	

	$m = 0;
	foreach ($subnet as $route) {
	
		# only not empty
		if(strlen($route)>2) {

		print '<tr>'. "\n";

		//delete
		print '<td class="removeSubnet">'. "\n";
		print '	<button class="btn btn-xs btn-default btn-danger" rel="tooltip" title="'._('Remove this subnet').'"><i class="fa fa-times"></i></button>'. "\n";
		print '</td>'. "\n";

		//subnet
		print '<td>'. "\n";
		print '<input type="text" class="form-control input-sm" name="subnet-'. $m .'" value="'. $route .'">'. "\n";
		print '</td>'. "\n";

		//section
		print '<td>'. "\n";
		print '<select name="section-'. $m .'" class="form-control input-sm input-w-auto">'. "\n";
	
		foreach($sections as $section) {
			print '<option value="'. $section['id'] .'">'. $section['name'] .'</option>';
		}
	
		print '</select>'. "\n";
		print '</td>'. "\n";
	
		//description
		print '<td>'. "\n";
		print '<input type="text" class="form-control input-sm input-w-250" name="description-'. $m .'">'. "\n";
		print '</td>'. "\n";

		//VLAN
		print '<td>'. "\n";
		print '<input type="text" class="form-control input-sm input-w-100" class="vlan" name="vlan-'. $m .'">'. "\n";
		print '</td>'. "\n";
		
		print '</tr>'. "\n";
		
		}

		$m++;	
	}

	//submit
	print '<tr style="border-top:1px solid white" class="th">'. "\n";
	print '<td colspan="5" style="text-align:right">'. "\n";
	print '	<input type="submit" class="btn btn-sm btn-default" value="'._('Import to database').'">'. "\n";
	print '</td>'. "\n";
	print '</tr>'. "\n";

	print '</table>'. "\n";
	print '</form>'. "\n";
}


print '	<div class="ripeImportResult"></div>'. "\n";
?>