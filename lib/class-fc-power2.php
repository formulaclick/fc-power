<?php
/**
 * @package		FCPower
 */
 
if ( ! class_exists( 'FCPower' ) ) {
    /**
     * Class FCPower
     */
    class FCPower {

        /**
         * Plugin version, used for cache-busting of style and script file references.
         *
         * @var     string
         */
        const VERSION = '1.0.0';

        /**
         * @var string
         */
        protected $plugin_basename = 'fc-power';

        /**
         * @var string
         */
        protected $transient_key = 'fc_power_pluginlist';

        /**
         * @var int
         */
        protected $transient_timeout = null;

        /**
         * Instance of this class.
         * @var      object
         */
        protected static $instance = null;

        /**
         *
         */
        function __construct() {

            // Activate plugin when new blog is added
            add_action( 'wpmu_new_blog', array( $this, 'activate_new_site' ) );

            // Main functions
            add_action( 'tgmpa_register', array( $this, 'register_required_plugins') );

            // Admin stuff
            // Add the options page and menu item.
            add_action( 'admin_menu', array( $this, 'add_admin_menus' ) );

            // Register the settings from configuration pages
            add_action( 'admin_init', array( $this, 'register_settings_configuration' ) );
            add_action( 'admin_init', array( $this, 'register_settings_aviso_legal' ) );
			
        }

        /**
         * Return an instance of this class.
         *
         * @return    object    A single instance of this class.
         */
        public static function get_instance() {

            // If the single instance hasn't been set, set it now.
            if ( null == self::$instance ) {
                self::$instance = new self;
            }

            return self::$instance;
        }
		
        /**
         * The main logic to set the recommended plugins
         */
        function register_required_plugins() {

			global $fc_power_plugin_list, $fc_power_plugin_list_private;//lo cogemos del archivo
			
            if(get_transient( $this->transient_key )){
                $plugins = get_transient( $this->transient_key );
            }else{
				$plugins = array();
				foreach($fc_power_plugin_list as $slug){
					$slug = trim($slug);
					$args = (object) array( 'slug' => $slug );
					$request = array( 'action' => 'plugin_information', 'timeout' => 15, 'request' => serialize( $args) );
					$url = 'http://api.wordpress.org/plugins/info/1.0/';
					$response = wp_remote_post( $url, array( 'body' => $request ) );
					$plugin_info = unserialize( $response['body'] );
					if(!isset($plugin_info->name)) continue;
					$plugins[] = array('slug' => $slug, 'required' => false, 'name' => $plugin_info->name); 
				}
				foreach($fc_power_plugin_list_private as $plugin_array){
					$plugins[] = $plugin_array;
				}
				set_transient($this->transient_key, $plugins, $this->transient_timeout);
			}

            // convert to object
            $theme_text_domain = 'tgmpa';

            /**
             * Array of configuration settings. Amend each line as needed.
             * If you want the default strings to be available under your own theme domain,
             * leave the strings uncommented.
             * Some of the strings are added into a sprintf, so see the comments at the
             * end of each line for what each argument will be.
             */
            $config = array(
                    'id'           => $theme_text_domain,                 // Unique ID for hashing notices for multiple instances of TGMPA.
                    'default_path' => '',                      // Default absolute path to bundled plugins.
                    'menu'         => 'fc-power-plugins',      // Menu slug.
					'parent_slug'  => 'fc-power',           // Parent menu slug.
                    'capability'   => 'install_plugins',       // Capability needed to view plugin install page, should be a capability associated with the parent menu used.
                    'has_notices'  => true,                    // Show admin notices or not.
                    'dismissable'  => true,                    // If false, a user cannot dismiss the nag message.
                    'dismiss_msg'  => '',                      // If 'dismissable' is false, this message will be output at top of nag.
                    'is_automatic' => false,                   // Automatically activate plugins after installation or not.
                    'message'      => '',                      // Message to output right before the plugins table.
                    'strings'      => array(
                            'page_title'   => 'Instalación de plugins por defecto',
							'menu_title'   => 'Plugins',
							'activated_successfully' => 'El plugin fue activado correctamente:',
                            'notice_can_install_required'     => _n_noop(
                                    'FCPower requires the following plugin: %1$s.',
                                    'FCPower requires the following plugins: %1$s.',
                                    'fc-power'
                            ), // %1$s = plugin name(s).
                            'notice_can_install_recommended'  => _n_noop(
                                    'FCPower recommends the following plugin: %1$s.',
                                    'FCPower recommends the following plugins: %1$s.',
                                    'fc-power'
                            ),
                    )
            );
	
            if(isset($plugins) && current_user_can( 'install_plugins' )){
                tgmpa( $plugins, $config );
            }
        }

        /**
         * Add the menus
         */
        function add_admin_menus() {

            add_menu_page( 'FCPower Config', 'FCPower', 'install_plugins', 'fc-power', array ( $this, 'fc_power_view_configuration' ), plugins_url('assets/img/icon.png', dirname(__FILE__)), 69.324 );
            add_submenu_page('fc-power', 'Configuración', 'Configuración', 'install_plugins', 'fc-power', array( $this, 'fc_power_view_configuration' ) );
            add_submenu_page('fc-power', 'Aviso Legal y privacidad', 'Aviso Legal', 'install_plugins', 'fc-power-aviso-legal', array( $this, 'fc_power_view_aviso_legal' ) );
			add_submenu_page('fc-power', 'Debug', 'Debug', 'install_plugins', 'fc-power-debug', array( $this, 'fc_power_view_debug' ) );
			
			if(get_option('fc_power_allow_repair')){
            	add_submenu_page('fc-power', 'Reparación', 'Reparación', 'install_plugins', 'fc-power-repair', array( $this, 'fc_power_view_repair' ) );
			}
        }

        /**
         * Register the settings
         */
        function register_settings_configuration() {
            //add_option( 'fc_power_plugin_list', '');
		
			// general
			add_settings_section(
				'fc_power_section_general', // id
				'General', // title
				'__return_false', // callback
				'fc-power-configuration' // page
			);
	
			add_settings_field(
				'fc_power_email_notificaciones', // id
				'Email notificaciones', // title
				array( $this, 'input_text' ), // callback
				'fc-power-configuration', // page
				'fc_power_section_general', // section
				array(
					'label_for' => 'fc_power_email_notificaciones',
					'classes'   => array(),
				) // args
			);

			add_settings_field(
				'fc_power_allow_repair', // id
				'Permitir reparación BBDD', // title
				array( $this, 'input_checkbox' ), // callback
				'fc-power-configuration', // page
				'fc_power_section_general', // section
				array(
					'label_for' => 'fc_power_allow_repair',
					'classes'   => array(),
				) // args
			);

			add_settings_field(
				'fc_power_show_adminmenu', // id
				'Mostrar Admin Menu', // title
				array( $this, 'input_checkbox' ), // callback
				'fc-power-configuration', // page
				'fc_power_section_general', // section
				array(
					'label_for' => 'fc_power_show_adminmenu',
					'classes'   => array(),
				) // args
			);

			add_settings_field(
				'fc_power_custom_login', // id
				'Mostrar Custom Login', // title
				array( $this, 'input_checkbox' ), // callback
				'fc-power-configuration', // page
				'fc_power_section_general', // section
				array(
					'label_for' => 'fc_power_custom_login',
					'classes'   => array(),
				) // args
			);

			add_settings_field(
				'fc_power_show_links', // id
				'Mostrar Menu Enlaces', // title
				array( $this, 'input_checkbox' ), // callback
				'fc-power-configuration', // page
				'fc_power_section_general', // section
				array(
					'label_for' => 'fc_power_show_links',
					'classes'   => array(),
				) // args
			);
												
			register_setting( 'fc-power-configuration', 'fc_power_email_notificaciones' );
			register_setting( 'fc-power-configuration', 'fc_power_allow_repair' );
			register_setting( 'fc-power-configuration', 'fc_power_show_adminmenu' );
			register_setting( 'fc-power-configuration', 'fc_power_custom_login' );
			register_setting( 'fc-power-configuration', 'fc_power_show_links' );
			//register_setting( 'default', 'fc_power_plugin_list', array( $this, 'save_keys' ) );
			
        }
		
		function register_settings_aviso_legal() {
			
			// general
			add_settings_section(
				'fc_power_section_general', // id
				'Datos del aviso legal', // title
				'__return_false', // callback
				'fc-power-aviso-legal' // page
			);		
				
			add_settings_field(
				'fc_power_aviso_legal_tag_titulos', // id
				'Tag de los títulos', // title
				array( $this, 'input_select' ), // callback
				'fc-power-aviso-legal', // page
				'fc_power_section_general', // section
				array(
					'label_for' => 'fc_power_aviso_legal_tag_titulos',
					'options'   => array('strong'=>'Negrita', 'h2'=>'Encabezado h2', 'h3'=>'Encabezado h3', 'h4'=>'Encabezado h4', 'h5'=>'Encabezado h5', 'h6'=>'Encabezado h6'),
				) // args
			);			
			register_setting( 'fc-power-aviso-legal', 'fc_power_aviso_legal_tag_titulos' );

			add_settings_field(
				'fc_power_aviso_legal_RRRRR', // id
				'Nombre real de la empresa', // title
				array( $this, 'input_text' ), // callback
				'fc-power-aviso-legal', // page
				'fc_power_section_general', // section
				array(
					'label_for' => 'fc_power_aviso_legal_RRRRR',
					'classes'   => array(),
				) // args
			);		
			register_setting( 'fc-power-aviso-legal', 'fc_power_aviso_legal_RRRRR' );
			
			add_settings_field(
				'fc_power_aviso_legal_NNNNN', // id
				'Alias', // title
				array( $this, 'input_text' ), // callback
				'fc-power-aviso-legal', // page
				'fc_power_section_general', // section
				array(
					'label_for' => 'fc_power_aviso_legal_NNNNN',
					'description'   => 'Nombre por el que se conoce al propietario de la empresa',
				) // args
			);		
			register_setting( 'fc-power-aviso-legal', 'fc_power_aviso_legal_NNNNN' );
			
			add_settings_field(
				'fc_power_aviso_legal_WWWWW', // id
				'URL página (sin http://)', // title
				array( $this, 'input_text' ), // callback
				'fc-power-aviso-legal', // page
				'fc_power_section_general', // section
				array(
					'label_for' => 'fc_power_aviso_legal_WWWWW',
				) // args
			);		
			register_setting( 'fc-power-aviso-legal', 'fc_power_aviso_legal_WWWWW' );

			add_settings_field(
				'fc_power_aviso_legal_QQQQQ', // id
				'Población donde está registrada', // title
				array( $this, 'input_text' ), // callback
				'fc-power-aviso-legal', // page
				'fc_power_section_general', // section
				array(
					'label_for' => 'fc_power_aviso_legal_QQQQQ',
				) // args
			);		
			register_setting( 'fc-power-aviso-legal', 'fc_power_aviso_legal_QQQQQ' );

			add_settings_field(
				'fc_power_aviso_legal_EEEEE', // id
				'Email de contacto', // title
				array( $this, 'input_text' ), // callback
				'fc-power-aviso-legal', // page
				'fc_power_section_general', // section
				array(
					'label_for' => 'fc_power_aviso_legal_EEEEE',
				) // args
			);		
			register_setting( 'fc-power-aviso-legal', 'fc_power_aviso_legal_EEEEE' );

			add_settings_field(
				'fc_power_aviso_legal_CCCC', // id
				'CIF o NIF', // title
				array( $this, 'input_text' ), // callback
				'fc-power-aviso-legal', // page
				'fc_power_section_general', // section
				array(
					'label_for' => 'fc_power_aviso_legal_CCCC',
				) // args
			);		
			register_setting( 'fc-power-aviso-legal', 'fc_power_aviso_legal_CCCC' );
			
			add_settings_field(
				'fc_power_aviso_legal_DDDD', // id
				'Dirección', // title
				array( $this, 'input_text' ), // callback
				'fc-power-aviso-legal', // page
				'fc_power_section_general', // section
				array(
					'label_for' => 'fc_power_aviso_legal_DDDD',
				) // args
			);		
			register_setting( 'fc-power-aviso-legal', 'fc_power_aviso_legal_DDDD' );
			
			add_settings_field(
				'fc_power_aviso_legal_MMMM', // id
				'Datos registro mercantil', // title
				array( $this, 'input_textarea' ), // callback
				'fc-power-aviso-legal', // page
				'fc_power_section_general', // section
				array(
					'label_for' => 'fc_power_aviso_legal_MMMM',
					'description' => 'Ejemplo: Registro Mercantil de ... inscripción ..., Tomo ... , Sección G...., Folio ......, Hoja .....'
				) // args
			);		
			register_setting( 'fc-power-aviso-legal', 'fc_power_aviso_legal_MMMM' );

			$pages = get_pages(); 
			$options = array('0'=>'-- No usar --');
			foreach($pages as $page){
				$options[$page->ID] = $page->post_title;
			}
			add_settings_field(
				'fc_power_aviso_legal_pagina', // id
				'Página a utilizar', // title
				array( $this, 'input_select' ), // callback
				'fc-power-aviso-legal', // page
				'fc_power_section_general', // section
				array(
					'label_for' => 'fc_power_aviso_legal_pagina',
					'options'   => $options,
				) // args
			);
			register_setting( 'fc-power-aviso-legal', 'fc_power_aviso_legal_pagina' );		
			
			
		}
		
		/**
		 * Input text
		 *
		 * @param array $args
		 */
		public function input_text( $args ) {
			$name = $args['label_for'];
	
			$classes = array( 'regular-text' );
			if ( isset( $args['classes'] ) ) {
				$classes = $args['classes'];
			}
			
			$description = '';
			if ( isset( $args['description'] ) ) {
				$description = '<br /><span class="description">'.$args['description'].'</span>';
			}
				
			printf(
				'<input name="%s" id="%s" type="text" class="%s" value="%s" />%s',
				esc_attr( $name ),
				esc_attr( $name ),
				esc_attr( implode( ' ', $classes ) ),
				esc_attr( get_option( $name, '' ) ),
				$description
			);
		}

		/**
		 * Input text
		 *
		 * @param array $args
		 */
		public function input_textarea( $args ) {
			$name = $args['label_for'];
	
			$classes = array( 'regular-text' );
			if ( isset( $args['classes'] ) ) {
				$classes = $args['classes'];
			}
			
			$description = '';
			if ( isset( $args['description'] ) ) {
				$description = '<br /><span class="description">'.$args['description'].'</span>';
			}
				
			printf(
				'<textarea name="%s" id="%s" class="%s">%s</textarea>%s',
				esc_attr( $name ),
				esc_attr( $name ),
				esc_attr( implode( ' ', $classes ) ),
				esc_attr( get_option( $name, '' ) ),
				$description
			);
		}
			
		/**
		 * Input checkbox
		 *
		 * @param array $args
		 */
		public function input_checkbox( $args ) {
			$name = $args['label_for'];
	
			$classes = array();
			if ( isset( $args['classes'] ) ) {
				$classes = $args['classes'];
			}
	
			printf(
				'<input name="%s" id="%s" type="checkbox" class="%s" %s />',
				esc_attr( $name ),
				esc_attr( $name ),
				esc_attr( implode( ' ', $classes ) ),
				checked( 'on', get_option( $name ), false )
			);
		}
	
		/**
		 * Input select
		 *
		 * @param array $args
		 */
		public function input_select( $args ) {
			$name = $args['label_for'];
	
			$classes = array();
			if ( isset( $args['classes'] ) ) {
				$classes = $args['classes'];
			}
	
			$options = array();
			if ( isset( $args['options'] ) ) {
				$options = $args['options'];
			}
	
			$multiple = false;
			if ( isset( $args['multiple'] ) && $args['multiple'] ) {
				$multiple = true;
			}
	
			printf(
				'<select name="%s" id="%s" class="%s" %s>',
				esc_attr( $name ) . ( $multiple ? '[]' : '' ),
				esc_attr( $name ),
				esc_attr( implode( ' ', $classes ) ),
				$multiple ? 'multiple="multiple" size="10"' : ''
			);
			
			$current_value = get_option( $name, '' );
	
			foreach ( $options as $option_key => $option ) {
	
				$selected = ( is_string( $current_value ) && (string)$option_key === $current_value ) ||
							( is_array( $current_value ) && in_array( $option_key, $current_value ) );
				
				printf(
					'<option value="%s" %s>%s</option>',
					esc_attr( $option_key ),
					selected( $selected, true, false ),
					esc_attr( $option )
				);
			}
	
			echo '</select>';
		}
	
        function fc_power_view_configuration() {

           /* wp_enqueue_script(
                    'fc-power-js-script',
                    plugins_url('assets/js/fc-power.js', dirname(__FILE__) ),
                    array( 'jquery' )
            );*/

            wp_enqueue_style(
                    'fc-power-css',
                    plugins_url('assets/css/fc-power.css', dirname(__FILE__) )
            );
            wp_enqueue_style(
                    'fc-power-gridism',
                    plugins_url('assets/css/gridism.css', dirname(__FILE__) )
            );

            include( dirname(__FILE__).'/../views/configuration.php' );
        }

        function fc_power_view_repair() {
			
            include( dirname(__FILE__).'/../views/repair.php' );
			
        }
		
        function fc_power_view_aviso_legal() {
			
            include( dirname(__FILE__).'/../views/aviso-legal.php' );
			
        }		
		
        function fc_power_view_debug() {
			
            include( dirname(__FILE__).'/../views/debug.php' );
			
        }
				
        /**
         * Fired when the plugin is activated.
         *
         * @param    boolean    $network_wide    True if WPMU superadmin uses
         *                                       "Network Activate" action, false if
         *                                       WPMU is disabled or plugin is
         *                                       activated on an individual blog.
         */
        public static function activate( $network_wide ) {

            if ( function_exists( 'is_multisite' ) && is_multisite() ) {

                if ( $network_wide  ) {

                    // Get all blog ids
                    $blog_ids = self::get_blog_ids();

                    foreach ( $blog_ids as $blog_id ) {

                        switch_to_blog( $blog_id );
                        self::single_activate();

                        restore_current_blog();
                    }

                } else {
                    self::single_activate();
                }


            } else {
                self::single_activate();
            }

        }

        /**
         * Fired when the plugin is deactivated.
         * @param    boolean    $network_wide    True if WPMU superadmin uses
         *                                       "Network Deactivate" action, false if
         *                                       WPMU is disabled or plugin is
         *                                       deactivated on an individual blog.
         */
        public static function deactivate( $network_wide ) {

            if ( function_exists( 'is_multisite' ) && is_multisite() ) {

                if ( $network_wide ) {

                    // Get all blog ids
                    $blog_ids = self::get_blog_ids();

                    foreach ( $blog_ids as $blog_id ) {

                        switch_to_blog( $blog_id );
                        self::single_deactivate();

                        restore_current_blog();

                    }

                } else {
                    self::single_deactivate();
                }

            } else {
                self::single_deactivate();
            }

        }

        /**
         * Fired when a new site is activated with a WPMU environment.
         * @param    int    $blog_id    ID of the new blog.
         */
        public function activate_new_site( $blog_id ) {

            if ( 1 !== did_action( 'wpmu_new_blog' ) ) {
                return;
            }

            switch_to_blog( $blog_id );
            self::single_activate();
            restore_current_blog();

        }

        /**
         * Get all blog ids of blogs in the current network that are:
         * - not archived
         * - not spam
         * - not deleted
         *
         * @return   array|false    The blog ids, false if no matches.
         */
        private static function get_blog_ids() {

            global $wpdb;

            // get an array of blog ids
            $sql = "SELECT blog_id FROM $wpdb->blogs
				WHERE archived = '0' AND spam = '0'
				AND deleted = '0'";

            return $wpdb->get_col( $sql );

        }

        /**
         * Fired for each blog when the plugin is activated.
         *
         * @since    1.0.0
         */
        private static function single_activate() {
            //
        }

        /**
         * Fired for each blog when the plugin is deactivated.
         *
         * @since    1.0.0
         */
        private static function single_deactivate() {
            //
        }		
    }

}