<div class="content upgrade-db" style="width:600px;margin:auto;background:white;padding:10px;margin-top:10px;border-radius:6px;border:1px solid #eee;">
<?php

/**
 * Check for fresh installation
 ****************************************************/

# show only errors
error_reporting(E_ERROR);


if(!tableExists("ipaddresses")) { ?>
	
		<!-- javascript -->
		<script type="text/javascript">
		$(document).ready(function () {
			$("div.loading").hide(); 
			$("table.dbUpgrade a").click(function() { 
				var div = $(this).attr("id");
				$("table.dbUpgrade tbody.content").not("table.dbUpgrade tbody." + div).hide();	
				$("table.dbUpgrade tbody." + div).show("fast");	
				$("table.dbUpgrade i").removeClass("fa fa-angle-down").addClass('fa fa-angle-right');	
				$("table.dbUpgrade a#"+div+" i").removeClass("fa fa-angle-right").addClass('fa fa-angle-down');	
				
			return false; 
			}); 
			$(document).on("click", "input.upgrade", function() { 
				$("div.loading").fadeIn("fast"); 
				var postData = $("#install").serialize(); 
				$.post("site/admin/databaseInstall.php", postData, function(data) { 
					$("div.upgradeResult").html(data).slideDown("fast"); 
					$("div.loading").fadeOut("fast"); 
				}); 
			}); 
			$(document).on("click", "div.error", function() { 
				$(this).stop(true,true).show(); 
			}); 
		}); 
		</script> 
	
		<!-- title -->
		<h4>phpIPAM database installation</h4>
		<hr><br>
		<div class="info2">Please select installation type:</div>
		

		<table class="dbUpgrade table">
		
		<!-- install -->
		<tr>
			<th><a href="#" id="upgrade"><i class="fa fa-angle-right"></i> Automatic database installation</th>
		</tr>
		<tr>

		<tbody style="display:none;" class="upgrade content">
		<tr>
			<td>
			<div class="alert alert-info">Clicking on install button will install required database files. Please fill in following database connection details:</div>
			<form id="install">
			<div class="row">
				MySQL username (user with permissions to create new MySQL database):
				<input type="text"     class='form-control input-sm input-w-200' style='margin-bottom:5px;' name="mysqlrootuser"  value="root">
				MySQL password:
				<input type="password" class='form-control input-sm input-w-200' style='margin-bottom:5px;' name="mysqlrootpass">
				MySQL database location *
				<input type="text"     class='form-control input-sm input-w-200' style='margin-bottom:5px;' name="mysqllocation" 	value="<?php print $db['host']; ?>" disabled>
				Database name*
				<input type="text"     class='form-control input-sm input-w-200' style='margin-bottom:5px;' name="mysqltable" 	value="<?php print $db['name']; ?>" disabled>
				<span style="color:gray;"> * Please change database name and location by modifying config.php file!</span><br>
				<input type="button" class="upgrade btn btn-sm btn-default btn-success" version="0" value="Install phpipam database">
			</div>
			</form>
		
			<div class="upgradeResult"></div>
		</td>
		</tr>	
		</tbody>
		
		
		<!-- SQL import instructions -->
		<tr>
			<th><a href="#" id="sqlUpgrade"><i class="fa fa-angle-right"></i> MySQL import instructions</a></th>
		</tr>	

		<tbody style="display:none;" class="sqlUpgrade content">
		<tr>
		<td>
			<div class="sqlUpgrade">
			<pre>/* import upgrade file */
mysql -u root -p my_root_pass < db/SCHEMA.sql</pre>
			</div>
		</td>
		</tr>
		</tbody>
			
	
		<!-- Manual instructions -->
		<tr>
			<th><a href="#" id="manualUpgrade"><i class="fa fa-angle-right"></i> Manual install instructions</a></th>
		</tr>
		
		<tbody style="display:none;" class="manualUpgrade content">
		<tr>
		<td>
			<div class="manualUpgrade">
			<pre><?php $file = file_get_contents("db/SCHEMA.sql"); print_r($file); ?></pre>
			</div>
		</td>
		</tr>
		</tbody>
	
		</table>
<?php
}
else {
	# already installed
	header("Location: /");
}
?>
</div>