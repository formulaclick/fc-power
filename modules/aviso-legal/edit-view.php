<div class="wrap fc-power-edit-view">
	<?php screen_icon(); ?>
    
	<?php if ( isset( $_GET['settings-updated'] ) ) {
        echo "<div class='updated'><p>Datos guardados correctamente</p></div>";
    } ?>

    <?php //settings_errors(); 


    ?>
    
	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
	
	<div class="grid">
		<div class="unit three-quarters">
			<form method="post" action="options.php">
				<?php settings_fields( 'fc-power-aviso-legal' ); ?>
                <?php do_settings_sections( 'fc-power-aviso-legal' ); ?>
				<?php submit_button('Guardar datos'); ?>
			</form>
		</div>
	</div>

</div>