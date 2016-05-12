<div class="wrap">
	<?php screen_icon(); ?>
    
	<?php if ( isset( $_GET['settings-updated'] ) ) {
        echo "<div class='updated'><p>Datos guardados correctamente</p></div>";
    } ?>

    <?php //settings_errors(); ?>
    
	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
	
	<div class="grid">
		<div class="unit three-quarters">
			<form method="post" action="options.php">
				<?php settings_fields( 'fc-power-envios-smtp' ); ?>
                <?php do_settings_sections( 'fc-power-envios-smtp' ); ?>
				<?php submit_button('Guardar datos'); ?>
			</form>
		</div>
	</div>

	<h3>Prueba de envío</h3>

	<?php 
	if ( isset( $_POST['fc_envios_smtp_test_submit'] ) && check_admin_referer( plugin_basename( __FILE__ ), 'fc_power_nonce_name' ) ) {	
		$to = isset( $_POST['fc_envios_smtp_to_test'] ) ? $_POST['fc_envios_smtp_to_test'] : '';
		$subject = isset( $_POST['fc_envios_smtp_subject_test'] ) ? $_POST['fc_envios_smtp_subject_test'] : '';
		$message = isset( $_POST['fc_envios_smtp_message_test'] ) ? $_POST['fc_envios_smtp_message_test'] : '';
		if($subject == '') $subject = 'Asunto';
		if($message == '') $message = 'Mensaje de prueba';
		if(!$to == ''){
			$result = test_mail($to, $subject, $message);
			echo $result;
		}			
	}
	?>

	<form id="smtp_form" method="post" action="">					
		<table class="form-table">
			<tr valign="top">
				<th scope="row">Para:</th>
				<td>
					<input type="text" name="fc_envios_smtp_to_test" value=""/><br />
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">Asunto:</th>
				<td>
					<input type="text" name="fc_envios_smtp_subject_test" value=""/><br />
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">Mensaje:</th>
				<td>
					<textarea name="fc_envios_smtp_message_test" id="fc_envios_smtp_message_test" rows="5"></textarea><br />
				</td>
			</tr>				
		</table>
		<p class="submit">
			<input type="submit" class="button-primary" value="Enviar" />
			<input type="hidden" name="fc_envios_smtp_test_submit" value="submit" />
			<?php wp_nonce_field( plugin_basename( __FILE__ ), 'fc_power_nonce_name' ); ?>
		</p>				
	</form>

</div>

<?php

function test_mail( $to_email, $subject, $message ) {
		
	$errors = '';

	require_once( ABSPATH . WPINC . '/class-phpmailer.php' );
	$mail = new PHPMailer();
            
    $charset = get_bloginfo( 'charset' );
	$mail->CharSet = $charset;
            
	$from_name = get_option('fc_power_envios_smtp_from_name');
	$from_email = get_option('fc_power_envios_smtp_from_email');
	$smtp_auth = get_option('fc_power_envios_smtp_auth');
	$smtp_secure = get_option('fc_power_envios_smtp_seguridad');

	$mail->IsSMTP();
	
	/* If using smtp auth, set the username & password */
	if( $smtp_auth ){
		$mail->SMTPAuth = true;
		$mail->Username = get_option('fc_power_envios_smtp_username');
		$mail->Password = get_option('fc_power_envios_smtp_password');
	}
	
	/* Set the SMTPSecure value, if set to none, leave this blank */
	if ( $smtp_secure !== '' ) {
		$mail->SMTPSecure = $smtp_secure;
	}
            
    /* PHPMailer 5.2.10 introduced this option. However, this might cause issues if the server is advertising TLS with an invalid certificate. */
    $mail->SMTPAutoTLS = false;
	
	/* Set the other options */
	$mail->Host = get_option('fc_power_envios_smtp_host');
	$mail->Port = get_option('fc_power_envios_smtp_port');
	$mail->SetFrom( $from_email, $from_name );
	$mail->isHTML( true );
	$mail->Subject = $subject;
	$mail->MsgHTML( $message );
	$mail->AddAddress( $to_email );
	$mail->SMTPDebug = 0;

	/* Send mail and return result */
	if ( ! $mail->Send() )
		$errors = $mail->ErrorInfo . '<pre>' . print_r($mail, true) . '</pre>';
	
	$mail->ClearAddresses();
	$mail->ClearAllRecipients();
		
	if ( ! empty( $errors ) ) {
		return $errors;
	}
	else{
		return 'Email de test enviado correctamente';
	}
}

?>