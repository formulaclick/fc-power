<?php
 
if ( ! class_exists( 'FCOpcionesGenerales' ) ) {
    /**
     * Class FCPower
     */
    class FCOpcionesGenerales extends FCModule{

        function __construct() {

            // Register the settings from configuration pages
            add_action( 'admin_init', array( $this, 'register_settings_opciones_generales' ) );

            // Cargamos los valores de la base de datos
			$this->load_options_values('fc_power_opciones_generales');

            // Opciones de Allow Repair
			add_action( 'init', array( $this, 'do_opciones_generales_allow_repair' ) );

            // Opciones de Avoid Regenerate Themes
			add_action( 'init', array( $this, 'do_opciones_generales_avoid_regenerate_themes' ) );

            // Opciones de show links
			add_action( 'plugins_loaded', array( $this, 'do_opciones_generales_show_links' ) );

            // Opciones de show adminmenu
			add_action( 'plugins_loaded', array( $this, 'do_opciones_generales_show_adminmenu' ) );

            // Ponemos login customizado
			add_action( 'init', array( $this, 'do_opciones_generales_custom_login' ) );

            // Opciones de disable emojis
			add_action( 'init', array( $this, 'do_opciones_generales_disable_emojis' ) );

            // Opciones de disable xmlrpc
			add_action( 'init', array( $this, 'do_opciones_generales_disable_xmlrpc' ) );
			
        }

        function do_opciones_generales_allow_repair(){
			 if($this->options['allow_repair']){
				define('WP_ALLOW_REPAIR', true);
			}
        }

        function do_opciones_generales_avoid_regenerate_themes(){
        	if($this->options['avoid_regenerate_themes']){
				define( 'CORE_UPGRADE_SKIP_NEW_BUNDLED', true );
			}
        }

        function do_opciones_generales_show_links(){
        	if (is_admin()){
			 	if(!$this->options['show_links']){
					update_option( 'link_manager_enabled', 0 );
				}else{
					update_option( 'link_manager_enabled', 1 );
				}
			}
        }

        function do_opciones_generales_show_adminmenu(){
        	if (!is_admin()){
				if(!$this->options['show_adminmenu']){
					add_filter('show_admin_bar', '__return_false');
				}	
			}
        }

        function do_opciones_generales_custom_login(){
			if($this->options['custom_login']){
				add_action( 'login_enqueue_scripts', array( $this, 'show_custom_logo' )  );
			}	
        }

		function show_custom_logo() { ?>
		    <style type="text/css">
		        #login h1 a {
		            background: url(<?php echo plugin_dir_url( __FILE__ ); ?>assets/img/site-login-logo.png) top center no-repeat;
		            padding-bottom: 10px;
					width:240px;
		        }
		    </style>
		<?php }

        function do_opciones_generales_disable_emojis(){

			if($this->options['disable_emojis']){
				remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
				remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
				remove_action( 'wp_print_styles', 'print_emoji_styles' );
				remove_action( 'admin_print_styles', 'print_emoji_styles' );	
				remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
				remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );	
				remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
				add_filter( 'tiny_mce_plugins', array( $this, 'disable_emojis_tinymce' )  );
			}	

        }

		function disable_emojis_tinymce( $plugins ) {
			if ( is_array( $plugins ) ) {
				return array_diff( $plugins, array( 'wpemoji' ) );
			} else {
				return array();
			}
		}

        function do_opciones_generales_disable_xmlrpc(){

			if($this->options['disable_xmlrpc']){
				add_filter('xmlrpc_enabled', '__return_false');
			}	

        }

        function register_settings_opciones_generales() {
			
			$page = 'fc-power-opciones-generales';
			$group = $page;
			$option_name = 'fc_power_opciones_generales';			

			// Sección
			$section = 'fc_power_section_general';
			add_settings_section(
				$section, // id
				'General', // title
				'__return_false', // callback
				$page // page
			);


			//Campos
			$field = 'allow_repair';
			$this->fields_to_save[] = $field;
			add_settings_field(
				$option_name.'['.$field.']',
				'Permitir reparación BBDD',
				array( $this, 'input_checkbox' ),
				$page,
				$section,
				array(
					'label_for' => $option_name.'['.$field.']',
					'field' => $field,
				)
			);					


			$field = 'avoid_regenerate_themes';
			$this->fields_to_save[] = $field;
			add_settings_field(
				$option_name.'['.$field.']',
				'Evitar que los temas se sobreescriban al actualizar',
				array( $this, 'input_checkbox' ),
				$page,
				$section,
				array(
					'label_for' => $option_name.'['.$field.']',
					'default' => true,
					'field' => $field,
				)
			);	


			$field = 'show_adminmenu';
			$this->fields_to_save[] = $field;
			add_settings_field(
				$option_name.'['.$field.']',
				'Mostrar Admin Menu en frontend',
				array( $this, 'input_checkbox' ),
				$page,
				$section,
				array(
					'label_for' => $option_name.'['.$field.']',
					'default' => true,
					'field' => $field,
				)
			);	


			$field = 'custom_login';
			$this->fields_to_save[] = $field;
			add_settings_field(
				$option_name.'['.$field.']',
				'Mostrar Custom Login',
				array( $this, 'input_checkbox' ),
				$page,
				$section,
				array(
					'label_for' => $option_name.'['.$field.']',
					'default' => false,
					'field' => $field,
				)
			);	


			$field = 'show_links';
			$this->fields_to_save[] = $field;
			add_settings_field(
				$option_name.'['.$field.']',
				'Mostrar Menu Enlaces',
				array( $this, 'input_checkbox' ),
				$page,
				$section,
				array(
					'label_for' => $option_name.'['.$field.']',
					'default' => false,
					'field' => $field,
				)
			);	


			$field = 'disable_emojis';
			$this->fields_to_save[] = $field;
			add_settings_field(
				$option_name.'['.$field.']',
				'Deshabilitar Emojis',
				array( $this, 'input_checkbox' ),
				$page,
				$section,
				array(
					'label_for' => $option_name.'['.$field.']',
					'default' => false,
					'field' => $field,
				)
			);	


			$field = 'disable_xmlrpc';
			$this->fields_to_save[] = $field;
			add_settings_field(
				$option_name.'['.$field.']',
				'Deshabilitar XML-RPC',
				array( $this, 'input_checkbox' ),
				$page,
				$section,
				array(
					'label_for' => $option_name.'['.$field.']',
					'default' => false,
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

new FCOpcionesGenerales();