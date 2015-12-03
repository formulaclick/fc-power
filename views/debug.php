<div class="wrap">
	<?php screen_icon(); ?>
	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
    <?php $debug_file = file_get_contents(WP_CONTENT_DIR . '/debug.log');?>
	<div class="grid">
		<div class="unit three-quarters">
            <div><p>Asegurarse de que en wp-config.php tenemos puesto define( 'WP_DEBUG_LOG', true );</p></div>
            
            <div>
            	<?php //echo WP_CONTENT_DIR . 'debug.log'?>
                <?php echo(nl2br($debug_file))?>
            </div>

		</div>
	</div>
</div>