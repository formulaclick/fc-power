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
         *
         * The variable name is used as the text domain when internationalizing strings
         * of text. Its value should match the Text Domain file header in the main
         * plugin file.
         *
         * @since    1.0.0
         *
         * @var      string
         */
        protected $plugin_slug = 'fc-power';

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
         * Initialize the plugin by setting localization and loading public scripts
         * and styles.
         */

        protected $plugin_screen_hook_suffix = null;

        /**
         *
         */
        function __construct() {

            // Activate plugin when new blog is added
            add_action( 'wpmu_new_blog', array( $this, 'activate_new_site' ) );

            // Main functions
            add_action( 'tgmpa_register', array( $this, 'fc_power_register_required_plugins') );

            // Admin stuff
            // Add the options page and menu item.
            add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );

            // Register the settings
            add_action( 'admin_init', array( $this, 'register_settings' ) );


            // Add an action link pointing to the options page.
            $plugin_basename = plugin_basename( plugin_dir_path( realpath( dirname( __FILE__ ) ) ) . $this->plugin_slug . '.php' );
            add_filter( 'plugin_action_links_' . $plugin_basename, array( $this, 'add_action_links' ) );

        }

        /**
         * Return the plugin slug.
         *
         * @return    Plugin slug variable.
         */
        public function get_plugin_slug() {
            return $this->plugin_slug;
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

        /**
         * The main logic to set the recommended plugins
         */
        function fc_power_register_required_plugins() {

			/*
			$fc_power_plugin_list = get_option('fc_power_plugin_list');
			$fc_power_plugin_list = explode(PHP_EOL,trim($fc_power_plugin_list));
			*/		
			
			global $fc_power_plugin_list;//lo cogemos del archivo
			
            if( get_transient( $this->transient_key )){
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
					$plugins[] = array('slug' =>$slug, 'required'=>'0', 'name'=>$plugin_info->name); 
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
                    'menu'         => 'fc-power-install-plugins', // Menu slug.
                    'parent_slug'  => 'plugins.php',            // Parent menu slug.
                    'capability'   => 'install_plugins',    // Capability needed to view plugin install page, should be a capability associated with the parent menu used.
                    'has_notices'  => true,                    // Show admin notices or not.
                    'dismissable'  => true,                    // If false, a user cannot dismiss the nag message.
                    'dismiss_msg'  => '',                      // If 'dismissable' is false, this message will be output at top of nag.
                    'is_automatic' => false,                   // Automatically activate plugins after installation or not.
                    'message'      => '',                      // Message to output right before the plugins table.
                    'strings'      => array(
                            'page_title'                      => 'Instalación de plugins por defecto',
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
        function add_plugin_admin_menu() {

            add_menu_page( 'FCPower Settings', 'FCPower', 'install_plugins', 'fc-power', array ( $this, 'fc_power_options_page' ), plugins_url('assets/img/icon.png', dirname(__FILE__)), 69.324 );
            add_submenu_page('fc-power', 'Manage Keys', 'Manage Keys', 'install_plugins', 'fc-power', array( $this, 'fc_power_options_page' ) );

            /**
             * Enqueue fc-power.js with jQuery dependency
             */
        }

        /**
         * Register the setting for storing keys
         */
        function register_settings() {
            add_option( 'fc_power_plugin_list', '');
            //register_setting( 'default', 'fc_power_plugin_list', array( $this, 'save_keys' ) );
			register_setting( 'default', 'fc_power_plugin_list' );
			
        }

        /**
         * This is the callback for register_setting above
         * @param $input
         * @return mixed
         */
        function save_keys($input){
            return $input;
        }

        /**
         * Render the settings page
         */
        function fc_power_options_page() {

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


            include( dirname(__FILE__).'/../views/settings.php' );
        }

        /**
         * Add settings action link to the plugins page.
         */
        public function add_action_links( $links ) {

            return array_merge(
                    array(
                            'settings' => '<a href="' . admin_url( 'admin.php?page=' . $this->plugin_slug ) . '">' . __( 'Settings', $this->plugin_slug ) . '</a>'
                    ),
                    $links
            );
        }
    }

}