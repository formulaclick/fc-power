<div class="wrap">
	<?php screen_icon(); ?>
    
	<?php if ( isset( $_GET['settings-updated'] ) ) {
        echo "<div class='updated'><p>Datos guardados correctamente</p></div>";
    } ?>

    <?php //settings_errors(); ?>
    
	<h2>FC Power Configuración</h2>
	<div class="grid">
		<div class="unit three-quarters">
			<form method="post" action="options.php">
				<?php settings_fields( 'default' ); ?>
				<table class="wp-list-table widefat fixed pages">
					<thead valign="top">
					<tr>
						<th class="manage-column column-title" scope="row" width="20%">Título</th>
                        <th class="manage-column column-title" scope="row" width="80%">Valor</th>
					</tr>
					</thead>
					<tbody>
                      <tr>
                          <td>
                              <p>Listado de plugins. Uno por línea.<br /><a href="admin.php?page=<?php echo TGM_Plugin_Activation::get_instance()->menu; ?>">Instalar Plugins</a></p>
                          </td>
                          <td>
                          	<p><textarea id="fc_power_plugin_list" name="fc_power_plugin_list" style="width:100%; height:300px"><?php echo get_option('fc_power_plugin_list'); ?></textarea></p>
                          </td>
                      </tr>                   
					</tbody>
				</table>
				<?php submit_button('Guardar datos'); ?>
			</form>
		</div>
	</div>
</div>