<?php

/**
 * Script to print add / edit / delete group
 *************************************************/

/* required functions */
require_once('../../functions/functions.php'); 

/* verify that user is admin */
checkAdmin();

/* get all settings */
$settings = getAllSettings();

?>


<!-- header -->
<div class="pHeader">
<?php
/**
 * If action is not set get it form post variable!
 */
if($_POST['action'] != "add") {
    
    //fetch all group details
    $api = getAPIkeyById($_POST['appid']);
    
    print ucwords($_POST['action']) .' '._('api').' '.$api['app_id'];
}
else {
	//generate code
	$api['app_code'] = str_shuffle(md5(microtime()));
	print _('Add new api key');
}
?>
</div>


<!-- content -->
<div class="pContent">

	<form id="apiEdit" name="apiEdit">
	<table class="groupEdit table table-noborder table-condensed">

	<!-- id -->
	<tr>
	    <td><?php print _('App id'); ?></td> 
	    <td>
	    	<input type="text" name="app_id" class="form-control input-sm" value="<?php print @$api['app_id']; ?>" <?php if($_POST['action'] == "delete") print "readonly"; ?>>
	        <input type="hidden" name="id" value="<?php print $api['id']; ?>">
    		<input type="hidden" name="action" value="<?php print $_POST['action']; ?>">
	    </td>
       	<td class="info2"><?php print _('Enter application identifier'); ?></td>
    </tr>

	<!-- code -->
	<tr>
	    <td><?php print _('App code'); ?></td> 
	    <td><input type="text" id="appcode" name="app_code" class="form-control input-sm"  value="<?php print @$api['app_code']; ?>"  maxlength='32' <?php if($_POST['action'] == "delete") print "readonly"; ?>></td>
       	<td class="info2"><?php print _('Application code'); ?> <button class="btn btn-xs btn-default" id="regApiKey"><i class="fa fa-random"></i> <?php print _('Regenerate'); ?></button></td>
    </tr>

	<!-- permissions -->
	<tr>
	    <td><?php print _('App permissions'); ?></td> 
	    <td>
	    	<select name="app_permissions" class="form-control input-sm input-w-auto">
	    	<?php
	    	$perms = array("0"=>"Disabled","1"=>"Read","2"=>"Read / Write");
	    	foreach($perms as $k=>$p) {
		    	if($k==$api['app_permissions'])	{ print "<option value='$k' selected='selected'>"._($p)."</option>"; }
		    	else							{ print "<option value='$k' 				   >"._($p)."</option>"; }
	    	}
	    	?>
	    	</select>
       	<td class="info2"><?php print _('Application permissions'); ?></td>
    </tr>

    <!-- description -->
<!--
    <tr>
    	<td><?php print _('Description'); ?></td> 
    	<td>
    		<input type="text" name="app_description" class="input-xlarge" value="<?php print @$api['app_description']; ?>" <?php if($_POST['action'] == "delete") print "readonly"; ?>>
    	</td>   
    	<td class="info2"><?php print _('Enter description'); ?></td>
    </tr>
-->

</table>
</form>

</div>




<!-- footer -->
<div class="pFooter">
	<div class="btn-group">
		<button class="btn btn-sm btn-default hidePopups"><?php print _('Cancel'); ?></button>
		<button class="btn btn-sm btn-default <?php if($_POST['action']=="delete") { print "btn-danger"; } else { print "btn-success"; } ?>" id="apiEditSubmit"><i class="fa <?php if($_POST['action']=="add") { print "fa-plus"; } else if ($_POST['action']=="delete") { print "fa-trash-o"; } else { print "fa-check"; } ?>"></i> <?php print ucwords(_($_POST['action'])); ?></button>
	</div>
	<!-- Result -->
	<div class="apiEditResult"></div>
</div>
