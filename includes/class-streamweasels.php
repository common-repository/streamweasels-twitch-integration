<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://www.streamweasels.com
 * @since      1.0.0
 *
 * @package    Streamweasels
 * @subpackage Streamweasels/includes
 */
/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Streamweasels
 * @subpackage Streamweasels/includes
 * @author     StreamWeasels <admin@streamweasels.com>
 */
class Streamweasels {
    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Streamweasels_Loader    $loader    Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $plugin_name    The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $version    The current version of the plugin.
     */
    protected $version;

    /**
     * The kernl uuid of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $uuid    The current version of the plugin.
     */
    protected $uuid;

    /**
     * The plugin versions
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $uuid    The current version of the plugin.
     */
    protected $player_data_version;

    protected $rail_data_version;

    protected $wall_data_version;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function __construct() {
        if ( defined( 'STREAMWEASELS_VERSION' ) ) {
            $this->version = STREAMWEASELS_VERSION;
        } else {
            $this->version = '1.0.0';
        }
        $this->plugin_name = 'streamweasels';
        $this->uuid = '5b69568f49c7721f6883e6fe';
        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - Streamweasels_Loader. Orchestrates the hooks of the plugin.
     * - Streamweasels_i18n. Defines internationalization functionality.
     * - Streamweasels_Admin. Defines all hooks for the admin area.
     * - Streamweasels_Public. Defines all hooks for the public side of the site.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies() {
        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-streamweasels-loader.php';
        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-streamweasels-i18n.php';
        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-streamweasels-admin.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-streamweasels-admin-player.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-streamweasels-admin-rail.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-streamweasels-admin-wall.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-streamweasels-admin-status.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-streamweasels-admin-vods.php';
        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-streamweasels-public.php';
        /**
         * Twitch
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-streamweasels-twitch.php';
        $this->loader = new Streamweasels_Loader();
    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the Streamweasels_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function set_locale() {
        $plugin_i18n = new Streamweasels_i18n();
        $this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks() {
        $plugin_admin = new Streamweasels_Admin($this->get_plugin_name(), $this->get_version(), $this->get_uuid());
        $this->loader->add_action( 'admin_notices', $plugin_admin, 'display_admin_upsell' );
        $this->loader->add_action( 'admin_menu', $plugin_admin, 'display_admin_page' );
        $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
        $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
        $this->loader->add_action( 'init', $plugin_admin, 'addon_cron_setup' );
        $this->loader->add_action( 'init', $plugin_admin, 'enqueue_blocks' );
        $this->loader->add_action( 'rest_api_init', $plugin_admin, 'add_rest_endpoints' );
        $this->loader->add_action( 'wp_ajax_swti_admin_notice_dismiss', $plugin_admin, 'swti_admin_notice_dismiss' );
        $this->loader->add_action( 'wp_ajax_swti_admin_notice_dismiss_for_good', $plugin_admin, 'swti_admin_notice_dismiss_for_good' );
        $this->loader->add_filter(
            'block_categories_all',
            $plugin_admin,
            'swti_custom_block_category',
            10,
            2
        );
        $this->loader->add_filter(
            'plugin_action_links',
            $plugin_admin,
            'swti_action_links',
            10,
            2
        );
        $rail_path = 'ttv-easy-embed/twitch-tv-easy-embed.php';
        $wall_path = 'ttv-easy-embed-wall/twitch-wall.php';
        $player_path = 'ttv-easy-embed-player/twitch-player.php';
        $status_path = 'stream-status-for-twitch/stream-status-for-twitch.php';
        $vods_path = 'streamweasels-vods-pro/streamweasels-vods-pro.php';
        $feature_path = 'streamweasels-feature-pro/streamweasels-feature-pro.php';
        $showcase_path = 'streamweasels-showcase-pro/streamweasels-showcase-pro.php';
        $nav_path = 'streamweasels-nav-pro/streamweasels-nav-pro.php';
        $rail_active = $wall_active = $player_active = $status_active = $vods_active = $feature_active = $showcase_active = $nav_active = false;
        $rail_data = $wall_data = $player_data = $status_data = $vods_data = $feature_data = $showcase_data = $nav_data = false;
        if ( in_array( $rail_path, apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
            $rail_active = true;
            $rail_data = get_plugin_data( WP_PLUGIN_DIR . '/' . $rail_path );
        }
        if ( in_array( $wall_path, apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
            $wall_active = true;
            $wall_data = get_plugin_data( WP_PLUGIN_DIR . '/' . $wall_path );
        }
        if ( in_array( $player_path, apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
            $player_active = true;
            $player_data = get_plugin_data( WP_PLUGIN_DIR . '/' . $player_path );
        }
        if ( in_array( $status_path, apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
            $status_active = true;
            $status_data = get_plugin_data( WP_PLUGIN_DIR . '/' . $status_path );
        }
        if ( in_array( $vods_path, apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
            $vods_active = true;
            $vods_data = get_plugin_data( WP_PLUGIN_DIR . '/' . $vods_path );
        }
        if ( in_array( $feature_path, apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
            $feature_active = true;
            $feature_data = get_plugin_data( WP_PLUGIN_DIR . '/' . $feature_path );
        }
        if ( in_array( $showcase_path, apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
            $showcase_active = true;
            $showcase_data = get_plugin_data( WP_PLUGIN_DIR . '/' . $showcase_path );
        }
        if ( in_array( $nav_path, apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
            $nav_active = true;
            $nav_data = get_plugin_data( WP_PLUGIN_DIR . '/' . $nav_path );
        }
        $player_data_version = ( $player_data ? $player_data['Version'] : false );
        $wall_data_version = ( $wall_data ? $wall_data['Version'] : false );
        $rail_data_version = ( $rail_data ? $rail_data['Version'] : false );
        $status_data_version = ( $status_data ? $status_data['Version'] : false );
        $vods_data_version = ( $vods_data ? $vods_data['Version'] : false );
        $feature_data_version = ( $feature_data ? $feature_data['Version'] : false );
        $showcase_data_version = ( $showcase_data ? $showcase_data['Version'] : false );
        $nav_data_version = ( $nav_data ? $nav_data['Version'] : false );
        if ( !$player_active || version_compare( $player_data_version, '2.1.2', '>=' ) ) {
            $plugin_admin_player = new Streamweasels_Player_Admin($this->get_plugin_name(), $this->get_version(), $this->get_uuid());
            $this->loader->add_action(
                'admin_menu',
                $plugin_admin_player,
                'display_admin_page_player',
                20
            );
        }
        if ( !$rail_active || version_compare( $rail_data_version, '2.1.3', '>=' ) ) {
            $plugin_admin_rail = new Streamweasels_Rail_Admin($this->get_plugin_name(), $this->get_version(), $this->get_uuid());
            $this->loader->add_action(
                'admin_menu',
                $plugin_admin_rail,
                'display_admin_page_rail',
                20
            );
        }
        if ( !$wall_active || version_compare( $wall_data_version, '2.1.4', '>=' ) ) {
            $plugin_admin_wall = new Streamweasels_Wall_Admin($this->get_plugin_name(), $this->get_version(), $this->get_uuid());
            $this->loader->add_action(
                'admin_menu',
                $plugin_admin_wall,
                'display_admin_page_wall',
                20
            );
        }
        if ( !$status_active || version_compare( $status_data_version, '2.0.3', '>=' ) ) {
            $plugin_admin_status = new Streamweasels_Status_Admin($this->get_plugin_name(), $this->get_version(), $this->get_uuid());
            $this->loader->add_action(
                'admin_menu',
                $plugin_admin_status,
                'display_admin_page_status',
                20
            );
        }
        if ( !$vods_active || version_compare( $vods_data_version, '1.0.4', '>=' ) ) {
            $plugin_admin_vods = new Streamweasels_Vods_Admin($this->get_plugin_name(), $this->get_version(), $this->get_uuid());
            $this->loader->add_action(
                'admin_menu',
                $plugin_admin_vods,
                'display_admin_page_vods',
                20
            );
        }
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_public_hooks() {
        $plugin_public = new Streamweasels_Public($this->get_plugin_name(), $this->get_version());
        $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
        $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
        $this->loader->add_action( 'init', $plugin_public, 'register_ajax_handler' );
        $this->loader->add_action( 'init', $plugin_public, 'streamWeasels_shortcode' );
        $this->loader->add_action( 'wp_footer', $plugin_public, 'swti_status_show_global' );
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run() {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     1.0.0
     * @return    string    The name of the plugin.
     */
    public function get_plugin_name() {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since     1.0.0
     * @return    Streamweasels_Loader    Orchestrates the hooks of the plugin.
     */
    public function get_loader() {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     1.0.0
     * @return    string    The version number of the plugin.
     */
    public function get_version() {
        return $this->version;
    }

    /**
     * Retrieve the uuid of the plugin.
     *
     * @since     1.0.0
     * @return    string    The uuid of the plugin.
     */
    public function get_uuid() {
        return $this->uuid;
    }

}
