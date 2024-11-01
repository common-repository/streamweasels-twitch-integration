<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.streamweasels.com
 * @since             1.0.0
 * @package           Streamweasels
 *
 * @wordpress-plugin
 * Plugin Name:       SW Twitch Integration - Blocks and Shortcodes for Embedding Twitch Streams
 * Plugin URI:        https://www.streamweasels.com/
 * Description:       Embed Twitch streams with our collection of Twitch Blocks and Shortcodes.
 * Version:           1.9.0
 * Author:            StreamWeasels
 * Author URI:        https://www.streamweasels.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       streamweasels
 * Domain Path:       /languages
 */
// If this file is called directly, abort.
if ( !defined( 'WPINC' ) ) {
    die;
}
/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'STREAMWEASELS_VERSION', '1.9.0' );
if ( function_exists( 'sti_fs' ) ) {
    sti_fs()->set_basename( false, __FILE__ );
} else {
    // DO NOT REMOVE THIS IF, IT IS ESSENTIAL FOR THE `function_exists` CALL ABOVE TO PROPERLY WORK.
    if ( !function_exists( 'sti_fs' ) ) {
        // Create a helper function for easy SDK access.
        function sti_fs() {
            global $sti_fs;
            if ( !isset( $sti_fs ) ) {
                // Include Freemius SDK.
                require_once dirname( __FILE__ ) . '/freemius/start.php';
                $sti_fs = fs_dynamic_init( array(
                    'id'             => '9086',
                    'slug'           => 'streamweasels-twitch-integration',
                    'premium_slug'   => 'streamweasels-twitch-integration-pro',
                    'type'           => 'plugin',
                    'public_key'     => 'pk_a06ba378682cf2b3168ab5462b900',
                    'is_premium'     => false,
                    'premium_suffix' => 'Pro',
                    'has_addons'     => true,
                    'has_paid_plans' => true,
                    'trial'          => array(
                        'days'               => 10,
                        'is_require_payment' => true,
                    ),
                    'menu'           => array(
                        'slug'    => 'streamweasels',
                        'support' => false,
                        'addons'  => false,
                    ),
                    'is_live'        => true,
                ) );
            }
            return $sti_fs;
        }

        // Init Freemius.
        sti_fs();
        // Signal that SDK was initiated.
        do_action( 'sti_fs_loaded' );
    }
    // Plugin Folder Path
    if ( !defined( 'SWTI_PLUGIN_DIR' ) ) {
        define( 'SWTI_PLUGIN_DIR', plugin_dir_url( __FILE__ ) );
    }
    /**
     * The code that runs during plugin activation.
     * This action is documented in includes/class-streamweasels-activator.php
     */
    function activate_streamweasels() {
        require_once plugin_dir_path( __FILE__ ) . 'includes/class-streamweasels-activator.php';
        Streamweasels_Activator::activate();
    }

    /**
     * The code that runs during plugin deactivation.
     * This action is documented in includes/class-streamweasels-deactivator.php
     */
    function deactivate_streamweasels() {
        require_once plugin_dir_path( __FILE__ ) . 'includes/class-streamweasels-deactivator.php';
        Streamweasels_Deactivator::deactivate();
    }

    register_activation_hook( __FILE__, 'activate_streamweasels' );
    register_deactivation_hook( __FILE__, 'deactivate_streamweasels' );
    /**
     * The core plugin class that is used to define internationalization,
     * admin-specific hooks, and public-facing site hooks.
     */
    require plugin_dir_path( __FILE__ ) . 'includes/class-streamweasels.php';
    /**
     * Begins execution of the plugin.
     *
     * Since everything within the plugin is registered via hooks,
     * then kicking off the plugin from this point in the file does
     * not affect the page life cycle.
     *
     * @since    1.0.0
     */
    function run_streamweasels() {
        $plugin = new Streamweasels();
        $plugin->run();
    }

    run_streamweasels();
}