<?php

/**
 * Script to print add / edit / delete users
 *************************************************/

/* required functions */
require_once('../../functions/functions.php'); 

/* verify that user is admin */
checkAdmin();

/* get all settings */
$settings = getAllSettings();

/* get custom fields */
$custom = getCustomFields('users');

/* get languages */
$langs = getLanguages ();
?>


<script type="text/javascript">
$(document).ready(function(){
     if ($("[rel=tooltip]").length) { $("[rel=tooltip]").tooltip(); }
});
</script>


<!-- header -->
<div class="pHeader">
<?php
/**
 * If action is not set get it form post variable!
 */
if (!$action) {
    $action = $_POST['action'];
    $id     = $_POST['id'];
    
    //fetch all requested userdetails
    $user = getUserDetailsById($id);
    
    if(!empty($user['real_name'])) 	{ print _(ucwords($action)." user").' '. $user['real_name']; }
    else 							{ print _('Add new user'); }
}
else {
	/* Set dummy data  */
	$user['real_name'] = '';
	$user['username']  = '';
	$user['email']     = '';
	$user['password']  = '';
	
	print _('Add new user');
}

# set default language
if(isset($settings['defaultLang']) && !is_null($settings['defaultLang']) && $action=="add" ) {
	$user['lang']=$settings['defaultLang'];
}
?>
</div>


<!-- content -->
<div class="pContent">

	<form id="usersEdit" name="usersEdit">
	<table class="usersEdit table table-noborder table-condensed">

	<!-- real name -->
	<tr>
	    <td><?php print _('Real name'); ?></td> 
	    <td><input type="text" class="form-control input-sm" name="real_name" value="<?php print $user['real_name']; ?>"></td>
       	<td class="info2"><?php print _('Enter users real name'); ?></td>
    </tr>

    <!-- username -->
    <tr>
    	<td><?php print _('Username'); ?></td> 
    	<td><input type="text" class="form-control input-sm" name="username" value="<?php print $user['username']; ?>" <?php if($action == "edit" || $action == "delete") print 'readonly'; ?>></td>   
    	<td class="info2">
    		<a class='btn btn-xs btn-default adsearchuser' rel='tooltip' title='Search AD for user details'><i class='fa fa-search'></i></a>
			<?php print _('Enter username'); ?>
		</td>
    </tr>

    <!-- email -->
    <tr>
    	<td><?php print _('e-mail'); ?></td> 
    	<td><input type="text" class="form-control input-sm input-w-250" name="email" value="<?php print $user['email']; ?>"></td>
    	<td class="info2"><?php print _('Enter users email address'); ?></td>
    </tr>

<!-- type -->
<?php
/* if domainauth is not enabled default to local user */
if($settings['domainAuth'] == 0) {
	if($_POST['action'] == "add") 	{ print '<input type="hidden" name="domainUser" value="0">'. "\n"; }
	else 							{ print '<input type="hidden" name="domainUser" value="'. $user['domainUser'] .'">'. "\n"; }
}
else {

	print '<tr>'. "\n";
    print '	<td>'._('User Type').'</td> '. "\n";
    print '	<td>'. "\n";
    print '	<select name="domainUser" class="form-control input-sm input-w-auto" id="domainUser">'. "\n";
    print '	<option value="0" '. "\n";
    		if ($user['domainUser'] == "0") print "selected"; 
    print '	>'._('Local user').'</option>'. "\n";
    print '	<option value="1" '. "\n"; 
    		if ($user['domainUser'] == "1") print "selected"; 
    print '	>'._('Domain user').'</option> '. "\n";
    print '	</select>'. "\n";
    print '	</td> '. "\n";
    print '	<td class="info2">';
    print _('Set user type').''. "\n";
    print '	<ul>'. "\n";
    print '		<li>'._('Local authenticates here').'</li>'. "\n";
    print '		<li>'._('Domain authenticates on AD, but still needs to be setup here for permissions etc.').'</li>'. "\n";
    print '	</ul>'. "\n";
    print '	</td>  '. "\n";
	print '</tr>'. "\n";

}
	if ($user['domainUser'] == "1") { $disabled = "disabled"; }
	else 							{ $disabled = ""; }
?>

	<!-- Language -->
	<tr>
		<td><?php print _('Language'); ?></td>
		<td>
			<select name="lang" class="form-control input-sm input-w-auto">
				<?php
				foreach($langs as $lang) {
					if($lang['l_id']==$user['lang'])	{ print "<option value='$lang[l_id]' selected>$lang[l_name] ($lang[l_code])</option>"; }
					else								{ print "<option value='$lang[l_id]'		 >$lang[l_name] ($lang[l_code])</option>"; }
				}
				?>
			</select>
		</td>
		<td class="info2"><?php print _('Select language'); ?></td>
	</tr>

    <!-- password -->
    <tr class="password">
    	<td><?php print _('Password'); ?></td> 
    	<td><input type="password" class="userPass form-control input-sm" name="password1" <?php print $disabled; ?>></td>
    	<td class="info2"><?php print _("User's password"); ?> (<a href="#" id="randomPass"><?php print _('click to generate random'); ?>!</a>)</td>
    </tr>

    <!-- password repeat -->
    <tr class="password">
    	<td><?php print _('Password'); ?></td> 
    	<td><input type="password" class="userPass form-control input-sm" name="password2" <?php print $disabled; ?>></td>   
    	<td class="info2"><?php print _('Re-type password'); ?></td>
    </tr>

    <!-- send notification mail -->
    <tr>
    	<td><?php print _('Notification'); ?></td> 
    	<td><input type="checkbox" name="notifyUser" <?php if($action == "add") { print 'checked'; } else if($action == "delete") { print 'disabled="disabled"';} ?>></td>   
    	<td class="info2"><?php print _('Send notification email to user with account details'); ?></td>
    </tr>

    <!-- role -->
    <tr>
    	<td><?php print _('User role'); ?></td> 
    	<td>
        <select name="role" class="form-control input-sm input-w-auto">
            <option value="Administrator"   <?php if ($user['role'] == "Administrator") print "selected"; ?>><?php print _('Administrator'); ?></option>
            <option value="User" 			<?php if ($user['role'] == "User" || $_POST['action'] == "add") print "selected"; ?>><?php print _('Normal User'); ?></option>
        </select>
        
        
        <input type="hidden" name="userId" value="<?php if(isset($user['id'])) { print $user['id']; } ?>">
        <input type="hidden" name="action" value="<?php print $action; ?>">
        
        </td> 
        <td class="info2"><?php print _('Select user role'); ?>
	    	<ul>
		    	<li><?php print _('Administrator is almighty'); ?></li>
		    	<li><?php print _('Users have access defined based on groups'); ?></li>
		    </ul>
		</td>  
	</tr>
	
	<!-- groups -->
	<tr>
		<td><?php print _('Groups'); ?></td>
		<td class="groups">
		<?php
		$groups = getAllGroups();		# all groups
		$ugroups = json_decode($user['groups'], true);
		$ugroups = parseUserGroupsIds($ugroups);
		
		if($groups) {
			foreach($groups as $g) {
				# empty fix
				if(sizeof($ugroups) > 0) {	
					if(in_array($g['g_id'], $ugroups)) 	{ print "<input type='checkbox' name='group$g[g_id]' checked>$g[g_name]<br>"; }
					else 								{ print "<input type='checkbox' name='group$g[g_id]'>$g[g_name]<br>"; }
				}
				else {
														{ print "<input type='checkbox' name='group$g[g_id]'>$g[g_name]<br>"; }
				}
			}
		}
		else {
			print "<div class='alert alert-info'>"._("No groups configured")."</div>";
		}
		
		?>
		</td>
		<td class="info2"><?php print _('Select to which groups the user belongs to'); ?></td>
	</tr>

	<!-- Custom -->
	<?php
	if(sizeof($custom) > 0) {
		print '<tr>';
		print '	<td colspan="3"><hr></td>';
		print '</tr>';

		# count datepickers
		$timeP = 0;
			
		# all my fields
		foreach($custom as $myField) {
			# replace spaces with |
			$myField['nameNew'] = str_replace(" ", "___", $myField['name']);
			
			# required
			if($myField['Null']=="NO")	{ $required = "*"; }
			else						{ $required = ""; }
			
			print '<tr>'. "\n";
			print '	<td>'. $myField['name'] .' '.$required.'</td>'. "\n";
			print '	<td>'. "\n";
			
			//set type
			if(substr($myField['type'], 0,3) == "set") {
				//parse values
				$tmp = explode(",", str_replace(array("set(", ")", "'"), "", $myField['type']));
				//null
				if($myField['Null']!="NO") { array_unshift($tmp, ""); }
								
				print "<select name='$myField[nameNew]' class='form-control input-sm input-w-auto' rel='tooltip' data-placement='right' title='$myField[Comment]'>";
				foreach($tmp as $v) {
					if($v==$user[$myField['name']])	{ print "<option value='$v' selected='selected'>$v</option>"; }
					else								{ print "<option value='$v'>$v</option>"; }
				}
				print "</select>";
			}
			//date and time picker
			elseif($myField['type'] == "date" || $myField['type'] == "datetime") {
				// just for first
				if($timeP==0) {
					print '<link rel="stylesheet" type="text/css" href="css/bootstrap/bootstrap-datetimepicker.min.css">';
					print '<script type="text/javascript" src="js/bootstrap-datetimepicker.min.js"></script>';
					print '<script type="text/javascript">';
					print '$(document).ready(function() {';
					//date only
					print '	$(".datepicker").datetimepicker( {pickDate: true, pickTime: false, pickSeconds: false });';
					//date + time
					print '	$(".datetimepicker").datetimepicker( { pickDate: true, pickTime: true } );';

					print '})';
					print '</script>';
				}
				$timeP++;
				
				//set size
				if($myField['type'] == "date")	{ $size = 10; $class='datepicker';		$format = "yyyy-MM-dd"; }
				else							{ $size = 19; $class='datetimepicker';	$format = "yyyy-MM-dd"; }
								
				//field
				if(!isset($user[$myField['name']]))	{ print ' <input type="text" class="'.$class.' form-control input-sm input-w-auto" data-format="'.$format.'" name="'. $myField['nameNew'] .'" maxlength="'.$size.'" '.$delete.' rel="tooltip" data-placement="right" title="'.$myField['Comment'].'">'. "\n"; }
				else									{ print ' <input type="text" class="'.$class.' form-control input-sm input-w-auto" data-format="'.$format.'" name="'. $myField['nameNew'] .'" maxlength="'.$size.'" value="'. $user[$myField['name']]. '" '.$delete.' rel="tooltip" data-placement="right" title="'.$myField['Comment'].'">'. "\n"; } 
			}	
			//boolean
			elseif($myField['type'] == "tinyint(1)") {
				print "<select name='$myField[nameNew]' class='form-control input-sm input-w-auto' rel='tooltip' data-placement='right' title='$myField[Comment]'>";
				$tmp = array(0=>"No",1=>"Yes");
				//null
				if($myField['Null']!="NO") { $tmp[2] = ""; }
				
				foreach($tmp as $k=>$v) {
					if(strlen($user[$myField['name']])==0 && $k==2)	{ print "<option value='$k' selected='selected'>"._($v)."</option>"; }
					elseif($k==$user[$myField['name']])				{ print "<option value='$k' selected='selected'>"._($v)."</option>"; }
					else												{ print "<option value='$k'>"._($v)."</option>"; }
				}
				print "</select>";
			}	
			//text
			elseif($myField['type'] == "text") {
				print ' <textarea class="form-control input-sm" name="'. $myField['nameNew'] .'" placeholder="'. $myField['name'] .'" '.$delete.' rowspan=3 rel="tooltip" data-placement="right" title="'.$myField['Comment'].'">'. $user[$myField['name']]. '</textarea>'. "\n";
			}	
			//default - input field
			else {
				print ' <input type="text" class="ip_addr form-control input-sm" name="'. $myField['nameNew'] .'" placeholder="'. $myField['name'] .'" value="'. $user[$myField['name']]. '" size="30" '.$delete.' rel="tooltip" data-placement="right" title="'.$myField['Comment'].'">'. "\n"; 
			}
						
			print '	</td>'. "\n";
			print '</tr>'. "\n";		
		}


	}
	
	?>

	
</table>
</form>

</div>




<!-- footer -->
<div class="pFooter">
	<div class="btn-group">
		<button class="btn btn-sm btn-default hidePopups"><?php print _('Cancel'); ?></button>
		<button class="btn btn-sm btn-default <?php if($_POST['action']=="delete") { print "btn-danger"; } else { print "btn-success"; } ?>" id="editUserSubmit"><i class="fa <?php if($_POST['action']=="add") { print "fa-plus"; } else if ($_POST['action']=="delete") { print "fa-trash-o"; } else { print "fa-check"; } ?>"></i> <?php print ucwords(_($_POST['action'])); ?></button>
	</div>

	<!-- Result -->
	<div class="usersEditResult"></div>
</div>
