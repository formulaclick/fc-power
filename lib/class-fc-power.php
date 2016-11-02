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