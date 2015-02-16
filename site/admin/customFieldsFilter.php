<?php

/** 
 * set which custom field to display
 ************************/


/*
	provided values are:
		table		= name of the table
 */ 
 

/* required functions */
require_once('../../functions/functions.php'); 

/* verify that user is admin */
checkAdmin();

/* escape vars to prevent SQL injection */
$_POST = filter_user_input ($_POST, true, true);
$table = $_POST['table'];

//get val from settings
$settins = getAllSettings();
$filters = json_decode($settings[0]['hiddenCustomFields'], true);
if(!isset($filters[$table]))	{ $filters[$table] = array(); }

//get custom fields
$custom = getCustomFields($table);
?>


<div class="pHeader"><?php print _('Filter custom field display'); ?></div>


<div class="pContent">

	<form id="editCustomFieldsFilter">
	<table id="editCustomFields" class="table table-noborder table-condensed">
		
	<input type="hidden" name="table" value="<?php print $table; ?>">

	<?php
	foreach($custom as $k=>$c) {
		print "<tr>";
		# select
		print "	<td>";
		if(in_array($k, $filters[$table]))	{ print "<input type='checkbox' class='form-controla' name='$k' checked>"; }
		else								{ print "<input type='checkbox' class='form-controla' name='$k'>"; }
		print "	</td>";
		# name and comment
		print "	<td>".$k." (".$c['Comment'].")</td>";
		print "</tr>";
	}	
		
	?>
	</table>
	</form>	
	
	<hr>
	<div class="text-muted">
	<?php print _("Selected fields will not be visible in table view, only in detail view"); ?>
	</div>
	<hr>
</div>


<!-- footer -->
<div class="pFooter">
	<div class="btn-group">
		<button class="btn btn-sm btn-default hidePopups"><?php print _('Close'); ?></button>
		<button class="btn btn-sm btn-default " id="editcustomFilterSubmit"><i class="fa fa-check"></i> <?php print ucwords(_("Save filter")); ?></button>
	</div>
	<!-- result -->
	<div class="customEditFilterResult"></div>
</div>