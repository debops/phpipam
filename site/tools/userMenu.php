<?php

/**
 *
 * Usermenu - user can change password and email
 *
 */
 
/* verify that user is authenticated! */
isUserAuthenticated ();

/* get username */
$ipamusername = getActiveUserDetails ();

/* get languages */
$langs = getLanguages ();

/* print hello */
print "<h4>$ipamusername[real_name], "._('here you can change your account details').":</h4>";
print "<hr><br>";

?>



<form id="userModSelf">
<table id="userModSelf" class="table table-striped table-condensed">

<!-- real name -->
<tr>
    <td><?php print _('Real name'); ?></td> 
    <td>
        <input type="text" class="form-control input-sm" name="real_name" value="<?php print $ipamusername['real_name']; ?>">
    </td>
    <td class="info2"><?php print _('Display name'); ?></td>
</tr>

<!-- username -->
<tr>
    <td><?php print _('E-mail'); ?></td> 
    <td>
        <input type="text" class="form-control input-sm"  name="email" value="<?php print $ipamusername['email']; ?>">
    </td>
    <td class="info2"><?php print _('Email address'); ?></td>
</tr>

<?php
# show pass only to local users!
if($ipamusername['domainUser'] == "0") {
?>
<!-- password -->
<tr>
    <td><?php print _('Password'); ?></td> 
    <td>
        <input type="password" class="userPass form-control input-sm" name="password1">
    </td style="white-space:nowrap">   
    <td class="info2"><?php print _('Password'); ?> <button id="randomPassSelf" class="btn btn-xs btn-default"><i class="fa fa-gray fa-random"></i></button><span id="userRandomPass" style="padding-left:15px;"></span></td>
</tr>

<!-- password repeat -->
<tr>
    <td><?php print _('Password'); ?> (<?php print _('repeat'); ?>)</td> 
    <td>
        <input type="password" class="userPass form-control input-sm" name="password2">
    </td>   
    <td class="info2"><?php print _('Re-type password'); ?></td>
</tr>
<?php } ?>

<!-- select language -->
<tr>
	<td><?php print _('Language'); ?></td>
	<td>
		<select name="lang" class="form-control input-sm">
			<?php
			foreach($langs as $lang) {
				if($lang['l_id']==$ipamusername['lang'])	{ print "<option value='$lang[l_id]' selected>$lang[l_name] ($lang[l_code])</option>"; }
				else										{ print "<option value='$lang[l_id]'		 >$lang[l_name] ($lang[l_code])</option>"; }
			}
			?>
		</select>
	</td>
	<td class="info2"><?php print _('Select language'); ?></td>
</tr>

<!-- Submit and hidden values -->
<tr class="th">
    <td></td> 
    <td class="submit">
        <input type="hidden" name="userId"     value="<?php print $ipamusername['id']; ?>">
        <input type="submit" class="btn btn-sm btn-default pull-right" value="<?php print _('Save changes'); ?>">
    </td>   
    <td></td>
</tr>

</table>
</form>


<!-- result -->
<div class="userModSelfResult" style="margin-bottom:90px;display:none"></div>


<!-- test -->
<h4 style='margin-top:30px;'><?php print _('Widgets'); ?></h4>
<hr>
<span class="info2"><?php print _("Select widgets to be displayed on dashboard"); ?></span>


<script type="text/javascript" src="js/jquery-ui-1.10.3.custom.min.js"></script>
<script>
$(document).ready(function() {
	// initialize sortable
	$( "#sortable" ).sortable({
		start: function( event, ui ) {
			var iid = $(ui.item).attr('id');
			$('li#'+ iid).addClass('alert alert-success');
		},
		stop: function( event, ui ) {
			var iid = $(ui.item).attr('id');
			$('li#'+ iid).removeClass('alert alert-success');
		}		
	});
	
	//get items
	$('#submitWidgets').click(function() {
		//get all ids that are checked
		var lis = $('#sortable li').map(function(i,n) {
			//only checked
			if($(this).find('input').is(':checked')) {
			return $(n).attr('id');	
			}
		}).get().join(';');
		
		//post
		$.post('site/tools/userMenuSetWidgets.php', {widgets: lis}, function(data) {
			$('.userModSelfResultW').html(data).fadeIn('fast');
		});
	});
});
</script>


<?php
# show all widgets, sortable

//user widgets form database
$uwidgets = explode(";",$ipamusername['widgets']);	//selected
$uwidgets = array_filter($uwidgets);

print "<ul id='sortable' class='sortable'>";

# get all widgets
if($ipamusername['role']=="Administrator") 	{ $widgets  = getAllWidgets(true, false); } 
else 										{ $widgets  = getAllWidgets(false, false); }

# first selected widgets already in user database
if(sizeof($uwidgets)>0) {
	foreach($uwidgets as $k) {
		$wtmp = $widgets[$k];
		print "<li id='$k'><i class='icon icon-move'></i><input type='checkbox' name='widget-$wtmp[wfile]' value='on' checked> $wtmp[wtitle]</li>";	
	}
}
# than others, based on admin or normal user
foreach($widgets as $k=>$w) {
	if(!in_array($k, $uwidgets))	{ 
	$wtmp = $widgets[$k];
	print "<li id='$k'><i class='icon icon-move'></i><input type='checkbox' name='widget-$wtmp[wfile]' value='on'> $wtmp[wtitle]</li>"; 
	}
}		

print "</ul>";
?>

<button class='btn btn-sm btn-default' id="submitWidgets"><i class="fa fa-check"></i> <?php print _('Save order'); ?></button>

<!-- result -->
<div class="userModSelfResultW" style="margin-bottom:90px;display:none"></div>