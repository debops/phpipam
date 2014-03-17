<?php

/**
 * Script to get all active IP requests
 ****************************************/

/* required functions */
require_once('../../functions/functions.php'); 

/* glue together */
$selected = implode(';', $_POST);

/* write to database */
if(!updateSelectedIPaddrFields($selected)) 	{ print '<div class="alert alert alert-danger alert-absolute">'._('Update failed').'!</div>'; }
else 										{ print '<div class="alert alert-success alert-absolute">'._('Update successfull').'!</div>'; }
?>