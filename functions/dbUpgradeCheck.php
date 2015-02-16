<?php

/**
 * Check if database needs upgrade to newer version
 ****************************************************/

/* use required functions */

/* redirect */
if($settings['version'] < VERSION) { 
	if(defined('BASE')) { header("Location: ".BASE.create_link("upgrade")); }
	else 				{ header("Location: ".create_link("upgrade"));} 
	die();
}
?>