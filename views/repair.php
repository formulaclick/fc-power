<div class="wrap">
	<?php screen_icon(); ?>
	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
	<div class="grid">
		<div class="unit three-quarters">
            <p>Yo! Click the following link to optimize/repair your database:</p>
            <p><a target="_blank" href="<?php echo get_admin_url(); ?>maint/repair.php"><?php echo get_admin_url(); ?>maint/repair.php</a></p>
            <p><strong>Remember to deactivate the plugin</strong> when done optimizing!</p>

		</div>
	</div>
</div>