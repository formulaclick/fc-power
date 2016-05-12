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

            // Add the options page and menu item.
            add_action( 'admin_menu', array( $this, 'add_admin_menus' ) );

            // Añadimos scripts al admin
            add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
			
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

        function add_admin_menus() {

            add_menu_page( 'FCPower Config', 'FCPower', 'install_plugins', 'fc-power', array ( $this, 'fc_power_opciones_generales_view' ), plugins_url('assets/img/icon.png', dirname(__FILE__)), 69.324 );
            add_submenu_page('fc-power', 'Configuraciones generales', 'Configuración', 'install_plugins', 'fc-power', array( $this, 'fc_power_opciones_generales_view' ) );
            add_submenu_page('fc-power', 'Configuración SMTP para enviar correos', 'Envíos SMTP', 'install_plugins', 'fc-power-envios-smtp', array( $this, 'fc_power_envios_smtp_view' ) );
            add_submenu_page('fc-power', 'Configuración automática del aviso legal', 'Aviso legal', 'install_plugins', 'fc-power-aviso-legal', array( $this, 'fc_power_aviso_legal_view' ) );

        }

        function enqueue_admin_scripts() {
            wp_enqueue_style( 'fc-power-style', plugins_url('../assets/css/fc-power.css',__FILE__ ) );
        }

        function fc_power_opciones_generales_view() {
            include( dirname(__FILE__).'/../modules/opciones-generales/edit-view.php' );
        }

        function fc_power_envios_smtp_view() {
            include( dirname(__FILE__).'/../modules/envios-smtp/edit-view.php' );
        }

        function fc_power_aviso_legal_view() {
            include( dirname(__FILE__).'/../modules/aviso-legal/edit-view.php' );
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