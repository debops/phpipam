<?php

/* verify that user is authenticated! */
isUserAuthenticated ();

/* get posted search term */
if($_REQUEST['ip']) { $searchTerm = $_REQUEST['ip']; }
else				{ $searchTerm = ""; }

?>

<h4><?php print _('Search IP database');?></h4>
<hr>

<!-- search form -->
<form id="search" name="search" class='form-inline'>
	<div class='input-group'>

	<div class='form-group'>
		<input class="search input-sm form-control" name="ip" value="<?php print $searchTerm; ?>" type="text" autofocus="autofocus" style='width:250px;'>
		<span class="input-group-btn">
			<button type="submit" class="btn btn-sm btn-default"><?php print _('search');?></button>
		</span>
	</div>
	
	</div>
</form>

<!-- result -->
<div class="searchResult">
<?php
/* include results if IP address is posted */
if ($searchTerm) 	{ include('searchResults.php'); }
else 				{ include('searchTips.php');}
?>
</div>