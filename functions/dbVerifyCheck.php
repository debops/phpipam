<?php

/**
 * Script to verify database structure
 ****************************************/

/* verify that user is admin */
require_once( dirname(__FILE__) . '/functions.php' );


/* title */
print "\n".'Database structure verification'. "\n--------------------\n";


/* check for errors */
$errors = verifyDatabase();

/* print result */
if( (!isset($errors['tableError'])) && (!isset($errors['fieldError'])) ) {
	print 'All tables and fields are installed properly'. "\n";
}
else {
	//tables
	if (isset($errors['tableError'])) {
		print 'Missing tables:'. "\n";
		
		foreach ($errors['tableError'] as $table) {
			print " - ".$table."\n";
		}
	}
	
	//fields
	if (isset($errors['fieldError'])) {
		print "\n".'Missing fields'. "\n";
		
		foreach ($errors['fieldError'] as $table=>$field) {
			print 'Table `'. $table .'`: missing field `'. $field .'`;'."\n";
		}
	}
}

print "\n";

?>