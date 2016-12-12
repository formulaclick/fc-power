<?php
 
if ( ! class_exists( 'FCEnviosSmtp' ) ) {

    class FCEnviosSmtp extends FCModule{

        function __construct() {

            // Register the settings from configuration pages
            add_action( 'admin_init', array( $this, 'register_the_settings' ) );

            // Cambia la forma de realizar los envíos
			add_action( 'init', array( $this, 'do_opciones_envios_smtp' ) );
			
        }

        function do_opciones_envios_smtp(){
			$options = get_option('fc_power_envios_smtp');
			 if($options['habilitar']){

				/*
				Se carga un archivo con estas variables
				$fcp_smtp_config = array(
					'from_email' => '',
					'from_name' => '',
					'smtp_auth' => true,
					'host' => '',
					'port' => 25,
					'username' => '',
					'password' => '',
					'smtp_secure' => '',
				);*/

				$config_file_content = file_get_contents('http://test.formulaclick.com/fc-power/smtp_config.php');
				//$config_file_content = file_get_contents('https://wp.formulaclick.com/fc-power/smtp_config.php');
 				$fcp_smtp_config = json_decode($config_file_content, true);
 				//* La contraseña que se guarda es encriptada
				$fcp_smtp_config['password'] = base64_decode($fcp_smtp_config['password']);

				$config = array_merge($fcp_smtp_config, array(
					'from_email' => $options['from_email'],
					'from_name' => $options['from_name'],
					'content_type' => $options['content_type']
				));
				require_once( plugin_dir_path( __FILE__ ) . 'class-fc-smtp.php' );
				new FCSmtp($config);
			}
        }

        function register_the_settings() {
			
			$page = 'fc-power-envios-smtp';
			$group = $page;
			$option_name = 'fc_power_envios_smtp';

			$this->load_options_values($option_name);

			// Sección
			$section = 'fc_power_section_general';
			add_settings_section(
				$section, // id
				'Datos de configuración', // title
				'__return_false', // callback
				$page // page
			);


			//Campos
			$field = 'habilitar';
			$this->fields_to_save[] = $field;
			add_settings_field(
				$option_name.'['.$field.']',
				'Habilitar',
				array( $this, 'input_checkbox' ),
				$page,
				$section,
				array(
					'label_for' => $option_name.'['.$field.']',
					'field' => $field,
				)
			);		


			//Campos
			$field = 'from_email';
			$this->fields_to_save[] = $field;
			add_settings_field(
				$option_name.'['.$field.']',
				'From email',
				array( $this, 'input_text' ),
				$page,
				$section,
				array(
					'label_for' => $option_name.'['.$field.']',
					'field' => $field,
				)
			);		


			//Campos
			$field = 'from_name';
			$this->fields_to_save[] = $field;
			add_settings_field(
				$option_name.'['.$field.']',
				'From name',
				array( $this, 'input_text' ),
				$page,
				$section,
				array(
					'label_for' => $option_name.'['.$field.']',
					'field' => $field,
				)
			);		


			//Campos
			$field = 'content_type';
			$this->fields_to_save[] = $field;
			add_settings_field(
				$option_name.'['.$field.']',
				'Content Type',
				array( $this, 'input_select' ),
				$page,
				$section,
				array(
					'label_for' => $option_name.'['.$field.']',
					'options'   => array('text/plain' => 'Texto plano', 'text/html' => 'Html'),
					'field' => $field,
				)
			);		

			register_setting( $group, $option_name, array($this, 'setting_validate') );

        }

        function fc_power_view() {
            include( dirname(__FILE__).'/edit-view.php' );
        }
	
    }

}

new FCEnviosSmtp();