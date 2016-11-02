<div class="wrap">
	<?php screen_icon(); ?>
    
	<?php if ( isset( $_GET['settings-updated'] ) ) {
        echo "<div class='updated'><p>Datos guardados correctamente</p></div>";
    } ?>

	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
    
	<?php 
	$options = get_option('fc_power_opciones_generales', null);
	if(isset($options['allow_repair']) && $options['allow_repair']){?>    

		<div class="grid">
			<div class="unit three-quarters">
	            <p>Pincha en el link para optimizar/reparar tu base de datos:</p>
	            <p><a target="_blank" href="<?php echo get_admin_url(); ?>maint/repair.php"><?php echo get_admin_url(); ?>maint/repair.php</a></p>
			</div>
		</div>

	<?php } ?>
	
	<div class="grid">
		<div class="unit three-quarters">
			<form method="post" action="options.php">
				<?php settings_fields( 'fc-power-opciones-generales' ); ?>
                <?php do_settings_sections( 'fc-power-opciones-generales' ); ?>
				<?php submit_button('Guardar datos'); ?>
			</form>
		</div>
	</div>
</div>