<div id="login">
<form name="login" id="login" class="form-inline" method="post">  

<div class="loginForm">
<table class="login">

	<legend><?php print _('Please login'); ?></legend>
          
		<!-- username -->
		<tr>
			<th><?php print _('Username'); ?></th>
            <td>
            	<input type="text" id="username" name="ipamusername" class="login form-control input-sm" placeholder="<?php print _('Username'); ?>" autofocus="autofocus" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"></input>
            </td>
        </tr>
            
        <!-- password -->
        <tr>
            <th><?php print _('Password'); ?></th>
            <td>
                <input type="password" id="password" name="ipampassword" class="login form-control input-sm" placeholder="<?php print _('Password'); ?>" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"></input>
                <?php
                // add requested var for redirect
                if(isset($_SESSION['phpipamredirect'])) {
	                print "<input type='hidden' name='phpipamredirect' id='phpipamredirect' value='$_SESSION[phpipamredirect]'>";
                }
                ?>
            </td>
        </tr>
        
        <?php
        # do we need captcha?
		if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))	{ $ip = $_SERVER['HTTP_X_FORWARDED_FOR']; }
		else										{ $ip = $_SERVER['REMOTE_ADDR']; }
		$cnt = check_blocked_ip ($ip);
		if($cnt>4) {       
        ?>
		<!-- captcha -->
		<tr>
			<th><?php print _('Security code'); ?></th>
			<td>
			<div class="row" style="margin-left:0px;">
			<div class="col-xs-6">
				<input id="validate_captcha" type="text" name="captcha" class="login form-control input-sm col-xs-12">
			</div>
			<div class="col-xs-6">
				<img src="site/login/captcha/captchashow.php" class="imgcaptcha" align="captcha">
			</div>
			</div>
			</td>
		</tr>
		<?php } ?>

            
        <!-- submit -->
        <tr>
            <td class="submit" colspan="2">
            	<hr>
                <input type="submit" value="<?php print _('Login'); ?>" class="btn btn-sm btn-default pull-right"></input>
            </td>
        </tr>           
</table>
</div>

</form> 


<?php   
/* show request module if enabled in config file */
if($settings['enableIPrequests'] == 1) {
?>
<div class="iprequest">
	<a href="<?php print create_link("request_ip"); ?>">
	<i class="fa fa-plus fa-pad-right"></i> <?php print _('Request new IP address'); ?>
	</a>	
</div>
<?php
}
?>
</div>