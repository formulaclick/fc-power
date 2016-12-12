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
		$subject = 'Prueba de envio';
		$message = 'Cuerpo del mensaje';
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

	$config_file_content = file_get_contents('http://test.formulaclick.com/fc-power/smtp_config.php');
 	$fcp_smtp_config = json_decode($config_file_content, true);
	$fcp_smtp_config['password'] = base64_decode($fcp_smtp_config['password']);

	$options = get_option('fc_power_envios_smtp');
	$config = array_merge($fcp_smtp_config, array(
		'from_email' => $options['from_email'],
		'from_name' => $options['from_name']
	));

	$mail->IsSMTP();
	
    $mail->Host = $config['host'];
    $mail->Port = $config['port'];
    //$mail->SMTPSecure = $config['smtp_secure'];
    if( $config['smtp_auth'] ){
	    $mail->SMTPAuth = true;
	    $mail->Username = $config['username'];
	    $mail->Password = $config['password'];
    }

	/* Set the SMTPSecure value, if set to none, leave this blank */
	if ( $config['smtp_secure'] !== '' ) {
		$mail->SMTPSecure = $config['smtp_secure'];
	}

    //PHPMailer 5.2.10 introduced this option. However, this might cause issues if the server is advertising TLS with an invalid certificate.
    $mail->SMTPAutoTLS = false;/*comprobarrr*/

	$mail->SetFrom( $config['from_email'], $config['from_name'] );
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

