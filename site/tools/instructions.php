<?php

/**
 *	print instructions
 **********************************************/

/* fetch instructions and print them in instructions div */
$instructions = fetchInstructions();
$instructions = $instructions[0]['instructions'];

/* format line breaks */
$instructions = stripslashes($instructions);		//show html

/* prevent <script> */
$instructions = str_replace("<script", "<div class='error'><xmp><script", $instructions);
$instructions = str_replace("</script>", "</script></xmp></div>", $instructions);

?>

<h4><?php print _('Instructions for managing IP addresses');?></h4>
<hr>

<div class="instructions well">
<?php print $instructions; ?>
</div>