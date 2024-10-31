<?php
/**
 * Settings
 **/

 	// not run if accessed directly
	if( ! defined("ABSPATH" ) )
   		 die("Not Allewed");
?>
<div class="pef_setting_page_hide" style="display: none;">
	<h2><?php _e('PE Form Settings' , 'wp-registration') ?></h2>
</div>
<?php  	
 	PEF_Settings()->display();