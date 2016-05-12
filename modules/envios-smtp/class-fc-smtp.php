<?php

class FCSmtp {

	var $config = array(
		'from_email' => '',
		'from_name' => 'From',
		'smtp_auth' => true,
		'host' => 'cloud1.formulaclick.com',
		'port' => 587,
		'username' => '',
		'password' => '',
		'smtp_secure' => 'tls',
		'content_type' => 'text/plain',
	);

	public function __construct($config)  {
		$this->config = array_merge($this->config, $config);
		add_action( 'phpmailer_init', array( $this, 'configure_smtp' ) );
		add_filter( 'wp_mail_from', array( $this, 'from_email' ) );
		add_filter( 'wp_mail_from_name', array( $this, 'from_name' ) );
		add_filter( 'wp_mail_content_type', array( $this, 'content_type' ) );

		//si es html, hacemos ajustes para que el email de recuperacion de password se vea bien
		if($this->config['content_type'] == 'text/html'){
			add_filter('retrieve_password_message', array( $this, 'retrieve_password_message_fix' ) );
		}

	}

	public function from_email() {
	    return $this->config['from_email'];
	}

	public function from_name() {
	    return $this->config['from_name'];
	}

	public function content_type() {
	    return $this->config['content_type'];
	}

	public function retrieve_password_message_fix($message){
	     // Replace first open bracket
	     $message = str_replace('<', '', $message);

	     // Replace second open bracket
	     $message = str_replace('>', '', $message);

	     // Convert line returns to <br>'s
	     $message = str_replace("\r\n", '<br>', $message);

	     return $message;
	}

	public function configure_smtp( $phpmailer ){
	    $phpmailer->isSMTP();
	    $phpmailer->Host = $this->config['host'];
	    $phpmailer->Port = $this->config['port'];
	    $phpmailer->SMTPSecure = $this->config['smtp_secure'];
	    if( $this->config['smtp_auth'] ){
		    $phpmailer->SMTPAuth = true;
		    $phpmailer->Username = $this->config['username'];
		    $phpmailer->Password = $this->config['password'];
	    }
        //PHPMailer 5.2.10 introduced this option. However, this might cause issues if the server is advertising TLS with an invalid certificate.
	    $phpmailer->SMTPAutoTLS = false;/*comprobarrr*/
	}
	
}
