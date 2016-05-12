<?php
 
if ( ! class_exists( 'FCEnviosSmtp' ) ) {
    /**
     * Class FCPower
     */
    class FCEnviosSmtp extends FCModule{

        function __construct() {

            // Register the settings from configuration pages
            add_action( 'admin_init', array( $this, 'register_settings_opciones_generales' ) );

            // Opciones de Allow Repair
			add_action( 'init', array( $this, 'do_opciones_envios_smtp' ) );

			
        }

        function do_opciones_envios_smtp(){

			 if(get_option('fc_power_envios_smtp_habilitar')){

			 	/*
				$fc_email_config = array(
					'from_email' => 'notificaciones-wp@formulaclick.net',
					'from_name' => 'Notificaciones Wordpress',
					'smtp_auth' => true,
					'host' => 'mail.formulaclick.net',
					'port' => 25,
					'username' => 'notificaciones-wp@formulaclick.net',
					'password' => 'Tog3b?36',
					'smtp_secure' => '',
				);
				*/

				$fc_email_config = array(
					'from_email' => get_option('fc_power_envios_smtp_from_email'),
					'from_name' => get_option('fc_power_envios_smtp_from_name'),
					'smtp_auth' => get_option('fc_power_envios_smtp_auth'),
					'host' => get_option('fc_power_envios_smtp_host'),
					'port' => get_option('fc_power_envios_smtp_port'),
					'username' => get_option('fc_power_envios_smtp_username'),
					'password' => get_option('fc_power_envios_smtp_password'),
					'smtp_secure' => get_option('fc_power_envios_smtp_seguridad'),
				);

				require_once( plugin_dir_path( __FILE__ ) . 'class-fc-smtp.php' );
				new FCSmtp($fc_email_config);

			}

        }


        function register_settings_opciones_generales() {
			
			$page = 'fc-power-envios-smtp';
			$group = $page;
			

			// Sección
			$section = 'fc_power_section_general';
			add_settings_section(
				$section, // id
				'Datos de configuración', // title
				'__return_false', // callback
				$page // page
			);


			//Campos
			add_settings_field(
				'fc_power_envios_smtp_habilitar', // id
				'Habilitar', // title
				array( $this, 'input_checkbox' ), // callback
				$page, // page
				$section, // section
				array('label_for' => 'fc_power_envios_smtp_habilitar', 'description' => 'Habilitar envíos usando la siguiente configuración SMTP')
			);
			register_setting( $group, 'fc_power_envios_smtp_habilitar' );


			add_settings_field(
				'fc_power_envios_smtp_from_email', // id
				'From email', // title
				array( $this, 'input_text' ), // callback
				$page, // page
				$section, // section
				array('label_for' => 'fc_power_envios_smtp_from_email')
			);
			register_setting( $group, 'fc_power_envios_smtp_from_email' );


			add_settings_field(
				'fc_power_envios_smtp_from_name', // id
				'From name', // title
				array( $this, 'input_text' ), // callback
				$page, // page
				$section, // section
				array('label_for' => 'fc_power_envios_smtp_from_name')
			);
			register_setting( $group, 'fc_power_envios_smtp_from_name' );


			add_settings_field(
				'fc_power_envios_smtp_auth', // id
				'Autenticación SMTP', // title
				array( $this, 'input_checkbox' ), // callback
				$page, // page
				$section, // section
				array('label_for' => 'fc_power_envios_smtp_auth')
			);
			register_setting( $group, 'fc_power_envios_smtp_auth' );


			add_settings_field(
				'fc_power_envios_smtp_host', // id
				'Host', // title
				array( $this, 'input_text' ), // callback
				$page, // page
				$section, // section
				array('label_for' => 'fc_power_envios_smtp_host')
			);
			register_setting( $group, 'fc_power_envios_smtp_host' );


			add_settings_field(
				'fc_power_envios_smtp_port', // id
				'Puerto', // title
				array( $this, 'input_text' ), // callback
				$page, // page
				$section, // section
				array('label_for' => 'fc_power_envios_smtp_port', 'classes' => array(), 'description' => '587 para tls, 25 para envio no seguro')
			);
			register_setting( $group, 'fc_power_envios_smtp_port' );


			add_settings_field(
				'fc_power_envios_smtp_username', // id
				'Usuario', // title
				array( $this, 'input_text' ), // callback
				$page, // page
				$section, // section
				array('label_for' => 'fc_power_envios_smtp_username')
			);
			register_setting( $group, 'fc_power_envios_smtp_username' );


			add_settings_field(
				'fc_power_envios_smtp_password', // id
				'Password', // title
				array( $this, 'input_text' ), // callback
				$page, // page
				$section, // section
				array('label_for' => 'fc_power_envios_smtp_password', 'classes' => array())
			);
			register_setting( $group, 'fc_power_envios_smtp_password' );


			add_settings_field(
				'fc_power_envios_smtp_seguridad', // id
				'Seguridad', // title
				array( $this, 'input_select' ), // callback
				$page, // page
				$section, // section
				array('label_for' => 'fc_power_envios_smtp_seguridad', 'options' => array('' => 'Ninguna', 'tls' => 'TLS', 'ssl' => 'SSL'))
			);
			register_setting( $group, 'fc_power_envios_smtp_seguridad' );

        }

        function fc_power_view() {
            include( dirname(__FILE__).'/edit-view.php' );
        }
	
    }

}

new FCEnviosSmtp();