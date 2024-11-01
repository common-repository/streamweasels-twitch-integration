<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.streamweasels.com
 * @since      1.0.0
 *
 * @package    Streamweasels
 * @subpackage Streamweasels/admin
 */
class Streamweasels_Admin {
    public $plugin_name;

    public $version;

    public $uuid;

    public $baseOptions;

    public $options;

    public $translations;

    public function __construct( $plugin_name, $version, $uuid ) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->uuid = $uuid;
        $this->baseOptions = $this->swti_get_options();
        $this->options = $this->swti_get_options();
        $this->translations = $this->swti_get_translations();
    }

    public function enqueue_blocks() {
        // Register your custom block using register_block_type
        $blocks_json_path = plugin_dir_path( dirname( __FILE__ ) ) . 'build/';
        register_block_type( $blocks_json_path . 'twitch-integration/block.json', array(
            'render_callback' => array($this, 'enqueue_twitch_integration_cb'),
        ) );
        register_block_type( $blocks_json_path . 'twitch-embed/block.json', array(
            'render_callback' => array($this, 'enqueue_twitch_embed_cb'),
        ) );
    }

    public function enqueue_twitch_integration_cb( $attr ) {
        $output = '<div ' . get_block_wrapper_attributes() . '>';
        $output .= do_shortcode( '[sw-twitch-integration layout="' . esc_attr( $attr['layout'] ) . '" channels="' . esc_attr( $attr['channels'] ) . '" team="' . esc_attr( $attr['team'] ) . '" game="' . esc_attr( $attr['game'] ) . '" limit="' . esc_attr( $attr['limit'] ) . '"]' );
        $output .= '</div>';
        return $output;
    }

    public function enqueue_twitch_embed_cb( $attr ) {
        $attr['autoplay'] = ( isset( $attr['autoplay'] ) && !empty( $attr['autoplay'] ) ? $attr['autoplay'] : 'false' );
        $attr['muted'] = ( isset( $attr['muted'] ) && !empty( $attr['muted'] ) ? $attr['muted'] : 'false' );
        $attr['embedChat'] = ( isset( $attr['embedChat'] ) && !empty( $attr['embedChat'] ) ? 'video-with-chat' : 'video' );
        $attr['width'] = ( isset( $attr['width'] ) && !empty( $attr['width'] ) ? $attr['width'] : '100%' );
        $attr['height'] = ( isset( $attr['height'] ) && !empty( $attr['height'] ) ? $attr['height'] : '100%' );
        if ( substr( $attr['width'], -2 ) !== 'px' && substr( $attr['width'], -1 ) !== '%' ) {
            $attr['width'] .= 'px';
        }
        if ( substr( $attr['height'], -2 ) !== 'px' && substr( $attr['height'], -1 ) !== '%' ) {
            $attr['height'] .= 'px';
        }
        $output = '<div ' . get_block_wrapper_attributes() . '>';
        $output .= do_shortcode( '[sw-twitch-embed channel="' . esc_attr( $attr['channel'] ) . '" autoplay="' . esc_attr( $attr['autoplay'] ) . '" muted="' . esc_attr( $attr['muted'] ) . '" embed-chat="' . esc_attr( $attr['embedChat'] ) . '" theme="' . esc_attr( $attr['theme'] ) . '" width="' . esc_attr( $attr['width'] ) . '" height="' . esc_attr( $attr['height'] ) . '"]' );
        $output .= '</div>';
        return $output;
    }

    public function add_rest_endpoints() {
        $fetchData = new SWTI_Twitch_API();
        // rest route for data for blocks
        register_rest_route( 'streamweasels/v1', '/data/', array(
            'methods'             => 'GET',
            'callback'            => array($this, 'swti_rest_endpoints'),
            'permission_callback' => function () {
                return current_user_can( 'manage_options' );
            },
        ) );
        // rest route for fetching streams
        register_rest_route( 'streamweasels/v1', '/fetch-streams', array(
            'methods'             => 'GET',
            'callback'            => array($fetchData, 'swti_fetch_streams'),
            'permission_callback' => '__return_true',
        ) );
        // rest route for fetching videos
        register_rest_route( 'streamweasels/v1', '/fetch-video', array(
            'methods'             => 'GET',
            'callback'            => array($fetchData, 'swti_fetch_video'),
            'permission_callback' => '__return_true',
        ) );
        // rest route for fetching users
        register_rest_route( 'streamweasels/v1', '/fetch-users', array(
            'methods'             => 'GET',
            'callback'            => array($fetchData, 'swti_fetch_users'),
            'permission_callback' => '__return_true',
        ) );
        // rest route for fetching games
        register_rest_route( 'streamweasels/v1', '/fetch-games', array(
            'methods'             => 'GET',
            'callback'            => array($fetchData, 'swti_fetch_games'),
            'permission_callback' => '__return_true',
        ) );
    }

    public function swti_rest_endpoints( $data ) {
        // Check if user is logged in
        if ( !is_user_logged_in() || !current_user_can( 'manage_options' ) ) {
            return new WP_Error('rest_not_logged_in', 'You must be logged in to access this data.', array(
                'status' => 401,
            ));
        }
        $weaselsData = array();
        $weaselsData['connectionStatus'] = esc_attr( $this->options['swti_api_connection_status'] ?? '' );
        $weaselsData['connectionExpires'] = esc_attr( $this->options['swti_api_access_token_expires'] ?? '' );
        $weaselsData['accessToken'] = esc_attr( $this->options['swti_api_access_token'] ?? '' );
        $weaselsData['accessTokenErrorCode'] = esc_attr( $this->options['swti_api_access_token_error_code'] ?? '' );
        $weaselsData['accessTokenErrorMessage'] = esc_attr( $this->options['swti_api_access_token_error_message'] ?? '' );
        $weaselsData['proStatus'] = sti_fs()->can_use_premium_code();
        if ( empty( $weaselsData ) ) {
            return new WP_Error('no_streamweasels_data', 'StreamWeasels Data Missing', array(
                'status' => 404,
            ));
        }
        return new WP_REST_Response($weaselsData, 200);
    }

    public function swti_custom_block_category( $categories, $post ) {
        $existingCategorySlugs = wp_list_pluck( $categories, 'slug' );
        $desiredCategorySlug = 'streamweasels';
        if ( !in_array( $desiredCategorySlug, $existingCategorySlugs ) ) {
            array_unshift( $categories, array(
                'slug'  => $desiredCategorySlug,
                'title' => 'StreamWeasels',
            ) );
        }
        return $categories;
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {
        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Streamweasels_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Streamweasels_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        wp_enqueue_style(
            $this->plugin_name,
            plugin_dir_url( __FILE__ ) . 'dist/streamweasels-admin.min.css',
            array(),
            $this->version,
            'all'
        );
        wp_enqueue_style(
            $this->plugin_name . '-powerange',
            plugin_dir_url( __FILE__ ) . 'dist/powerange.min.css',
            array(),
            $this->version,
            'all'
        );
        wp_enqueue_style(
            $this->plugin_name . '-addons',
            plugin_dir_url( __FILE__ ) . '../freemius/assets/css/admin/add-ons.css',
            array(),
            $this->version,
            'all'
        );
        wp_enqueue_style( 'wp-color-picker' );
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {
        wp_enqueue_script(
            $this->plugin_name,
            plugin_dir_url( __FILE__ ) . 'dist/streamweasels-admin-free.min.js',
            array('jquery', 'wp-color-picker'),
            $this->version,
            false
        );
        wp_enqueue_script(
            $this->plugin_name . '-powerange',
            plugin_dir_url( __FILE__ ) . 'dist/powerange.min.js',
            array('jquery'),
            $this->version,
            false
        );
        wp_enqueue_media();
    }

    public function addon_cron_setup() {
        add_action( 'swti_cron', array($this, 'swti_run_cron') );
        if ( !wp_next_scheduled( 'swti_cron' ) ) {
            wp_schedule_event( time(), 'daily', 'swti_cron' );
        }
    }

    public function swti_run_cron() {
        $expires = get_transient( 'swti_twitch_token_expires' );
        $clientId = $this->options['swti_client_id'];
        $clientSecret = $this->options['swti_client_secret'];
        if ( empty( $expires ) ) {
            $this->swti_twitch_debug_field( 'cron - swti_twitch_token_expires not found.' );
            if ( !empty( $clientId ) && !empty( $clientSecret ) ) {
                $this->swti_twitch_debug_field( 'cron - client ID and client secret ready.' );
                $SWTI_Cron_Twitch_API = new SWTI_Twitch_API();
                $SWTI_Cron_Twitch_API->refresh_token();
                $result = $SWTI_Cron_Twitch_API->get_token( $this->options['swti_client_id'], $this->options['swti_client_secret'] );
                $swti_options = get_option( 'swti_options' );
                $swti_options['swti_api_access_token'] = $result[0];
                $swti_options['swti_api_access_token_expires'] = $result[1];
                update_option( 'swti_options', $swti_options );
                $this->swti_twitch_debug_field( 'cron - auth token generated - ' . $result[0] );
            } else {
                $this->swti_twitch_debug_field( 'cron - client ID or client secret missing.' );
            }
        }
    }

    public function display_admin_upsell() {
        $display_status = get_transient( 'swti_notice_closed5' );
        $display_status2 = ( isset( $this->options['swti_dismiss_for_good5'] ) ? $this->options['swti_dismiss_for_good5'] : '' );
        if ( !$display_status ) {
            if ( !$display_status2 ) {
                echo '<div class="notice is-dismissible swti-notice">';
                echo '<div class="swti-notice__content">';
                echo '<h2>Introducing StreamWeasels Status Bar!</h2>';
                echo '<img src="' . plugin_dir_url( __FILE__ ) . '../admin/img/status-bar-example.png">';
                echo '<p>Add a sticky, customisable Status Bar to the top of your website and let your users know when you\'re live on Twitch, Kick or YouTube!</p>';
                echo '<p>Check out <strong>StreamWeasels Status Bar</strong> for WordPress - <a href="/wp-admin/plugin-install.php?s=streamweasels status bar&tab=search&type=term" target="_blank"><strong>Download for free now</strong></a>.</p>';
                echo '<p><a class="dismiss-for-good" href="#">Don\'t show this again!</a></p>';
                echo '</div>';
                echo '</div>';
            }
        }
    }

    public function swti_admin_notice_dismiss() {
        set_transient( 'swti_notice_closed5', true, 604800 );
        wp_die();
    }

    public function swti_admin_notice_dismiss_for_good() {
        $swti_options = get_option( 'swti_options' );
        $swti_options['swti_dismiss_for_good5'] = true;
        update_option( 'swti_options', $swti_options );
        wp_die();
    }

    /**
     * Register the admin page.
     *
     * @since    1.0.0
     */
    public function display_admin_page() {
        add_menu_page(
            'StreamWeasels',
            'Twitch Integration',
            'manage_options',
            'streamweasels',
            array($this, 'swti_showAdmin'),
            'dashicons-twitch'
        );
        add_submenu_page(
            'streamweasels',
            'Translations',
            'Translations',
            'manage_options',
            'streamweasels-translations',
            array($this, 'swti_showAdmin')
        );
        $tooltipArray = array(
            'Game'                   => 'Game <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="game=\'\'"></span>',
            'Language'               => 'Language <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="language=\'\'"></span>',
            'Channels'               => 'Channels <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="channels=\'\'"></span>',
            'Team'                   => 'Team <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="team=\'\'"></span>',
            'Title Filter'           => 'Title Filter <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="title-filter=\'\'"></span>',
            'Limit'                  => 'Limit <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="limit=\'\'"></span>',
            'Colour Theme'           => 'Colour Theme <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="colour-theme=\'\'"></span>',
            'Layout'                 => 'Layout <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="layout=\'\'"></span>',
            'Embed'                  => 'Embed <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="embed=\'\'"></span>',
            'Embed Colour Scheme'    => 'Embed Colour Scheme <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="embed-theme=\'\'"></span>',
            'Display Chat'           => 'Display Chat <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="embed-chat=\'\'"></span>',
            'Display Title'          => 'Display Title <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="embed-title=\'\'"></span>',
            'Title Position'         => 'Title Position <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="title-position=\'\'"></span>',
            'Start Muted'            => 'Start Muted <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="embed-muted=\'\'"></span>',
            'Show Offline Streams'   => 'Show Offline Streams <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="show-offline=\'\'"></span>',
            'Offline Message'        => 'Offline Message <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="show-offline-text=\'\'"></span>',
            'Show Offline Image'     => 'Offline Image <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="show-offline-image=\'\'"></span>',
            'Autoplay Stream'        => 'Autoplay Stream <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="autoplay=\'\'"></span>',
            'Autoplay Offline'       => 'Autoplay Offline <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="autoplay-offline=\'\'"></span>',
            'Autoplay Select'        => 'Autoplay Select <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="autoplay-select=\'\'"></span>',
            'Featured Streamer'      => 'Featured Streamer <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="featured-stream=\'\'"></span>',
            'Title'                  => 'Title <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="title=\'\'"></span>',
            'Subtitle'               => 'Subtitle <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="subtitle=\'\'"></span>',
            'Offline Image'          => 'Offline Image <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="offline-image=\'\'"></span>',
            'Logo'                   => 'Custom Logo <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="logo-image=\'\'"></span>',
            'Profile'                => 'Profile Image <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="profile-image=\'\'"></span>',
            'Logo Background Colour' => 'Logo Background Colour <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="logo-bg-colour=\'\'"></span>',
            'Logo Border Colour'     => 'Logo Border Colour <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="logo-border-colour=\'\'"></span>',
            'Max Width'              => 'Max Width <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="max-width=\'\'"></span>',
            'Tile Layout'            => 'Tile Layout <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="tile-layout=\'\'"></span>',
            'Tile Sorting'           => 'Tile Sorting <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="tile-sorting=\'\'"></span>',
            'Tile Live'              => 'Live Info <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="live-info=\'\'"></span>',
            'Background Colour'      => 'Background Colour <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="tile-bg-colour=\'\'"></span>',
            'Title Colour'           => 'Title Colour <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="tile-title-colour=\'\'"></span>',
            'Subtitle Colour'        => 'Subtitle Colour <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="tile-subtitle-colour=\'\'"></span>',
            'Rounded Corners'        => 'Rounded Corners <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="tile-rounded-corners=\'\'"></span>',
            'Hover Effect'           => 'Hover Effect <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="hover-effect=\'\'"></span>',
            'Hover Colour'           => 'Hover Colour <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="hover-colour=\'\'"></span>',
            'Refresh'                => 'Refresh <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="refresh=\'\'"></span>',
        );
        // register settings
        register_setting( 'swti_options', 'swti_options', array($this, 'swti_options_validate') );
        // translation settings
        register_setting( 'swti_translations', 'swti_translations', array($this, 'swti_translations_validate') );
        // License Settings section
        add_settings_section(
            'swti_license_settings',
            'License Key',
            false,
            'swti_license_fields'
        );
        // API Settings section
        add_settings_section(
            'swti_api_settings',
            'Twitch API Settings',
            false,
            'swti_api_fields'
        );
        // Shortcode Settings section
        add_settings_section(
            'swti_shortcode_settings',
            'Shortcode',
            false,
            'swti_shortcode_fields'
        );
        // Shortcode Settings section
        add_settings_section(
            'swti_translations_settings',
            'Translations',
            false,
            'swti_translations_fields'
        );
        // Main Settings section
        add_settings_section(
            'swti_main_settings',
            'Main Settings',
            false,
            'swti_main_fields'
        );
        // Main Settings section
        add_settings_section(
            'swti_layout_settings',
            'Layout Settings',
            false,
            'swti_layout_fields'
        );
        // Embed Settings section
        add_settings_section(
            'swti_embed_settings',
            'Embed Settings',
            false,
            'swti_embed_fields'
        );
        // Offline Settings section
        add_settings_section(
            'swti_offline_settings',
            'Offline Settings',
            false,
            'swti_offline_fields'
        );
        // Autoplay Settings section
        add_settings_section(
            'swti_autoplay_settings',
            'Autoplay Settings',
            false,
            'swti_autoplay_fields'
        );
        // Appearance Settings section
        add_settings_section(
            'swti_appearance_settings',
            'Appearance Settings',
            false,
            'swti_appearance_fields'
        );
        // Tile Settings section
        add_settings_section(
            'swti_tile_settings',
            'Tile Settings',
            false,
            'swti_tile_fields'
        );
        // Hover Settings section
        add_settings_section(
            'swti_hover_settings',
            'Hover Settings',
            false,
            'swti_hover_fields'
        );
        // Refresh section
        add_settings_section(
            'swti_refresh_settings',
            'Refresh Settings',
            false,
            'swti_refresh_fields'
        );
        // Debug Settings section
        add_settings_section(
            'swti_debug_settings',
            'Debug Settings',
            false,
            'swti_debug_fields'
        );
        // License Key Fields
        // Twitch API Fields
        add_settings_field(
            'swti_api_connection_status',
            'Connection Status',
            array($this, 'swti_api_connection_status_cb'),
            'swti_api_fields',
            'swti_api_settings'
        );
        add_settings_field(
            'swti_client_token',
            'Auth Token',
            array($this, 'swti_client_token_cb'),
            'swti_api_fields',
            'swti_api_settings'
        );
        add_settings_field(
            'swti_client_id',
            'Client ID',
            array($this, 'swti_client_id_cb'),
            'swti_api_fields',
            'swti_api_settings'
        );
        add_settings_field(
            'swti_client_secret',
            'Client Secret',
            array($this, 'swti_client_secret_cb'),
            'swti_api_fields',
            'swti_api_settings'
        );
        // Shortcode Fields
        add_settings_field(
            'swti_shortcode',
            'Shortcode',
            array($this, 'swti_shortcode_cb'),
            'swti_shortcode_fields',
            'swti_shortcode_settings'
        );
        // Translation Fields
        add_settings_field(
            'swti_translations_live',
            'Live',
            array($this, 'swti_translations_live_cb'),
            'swti_translations_fields',
            'swti_translations_settings'
        );
        add_settings_field(
            'swti_translations_offline',
            'Offline',
            array($this, 'swti_translations_offline_cb'),
            'swti_translations_fields',
            'swti_translations_settings'
        );
        add_settings_field(
            'swti_translations_streaming',
            'Streaming',
            array($this, 'swti_translations_streaming_cb'),
            'swti_translations_fields',
            'swti_translations_settings'
        );
        add_settings_field(
            'swti_translations_for',
            'For',
            array($this, 'swti_translations_for_cb'),
            'swti_translations_fields',
            'swti_translations_settings'
        );
        add_settings_field(
            'swti_translations_viewers',
            'Viewers',
            array($this, 'swti_translations_viewers_cb'),
            'swti_translations_fields',
            'swti_translations_settings'
        );
        // Main Settings
        add_settings_field(
            'swti_game',
            $tooltipArray['Game'],
            array($this, 'swti_game_cb'),
            'swti_main_fields',
            'swti_main_settings'
        );
        add_settings_field(
            'swti_langauge',
            $tooltipArray['Language'],
            array($this, 'swti_language_cb'),
            'swti_main_fields',
            'swti_main_settings'
        );
        add_settings_field(
            'swti_channels',
            $tooltipArray['Channels'],
            array($this, 'swti_channels_cb'),
            'swti_main_fields',
            'swti_main_settings'
        );
        add_settings_field(
            'swti_team',
            $tooltipArray['Team'],
            array($this, 'swti_team_cb'),
            'swti_main_fields',
            'swti_main_settings'
        );
        add_settings_field(
            'swti_title_filter',
            $tooltipArray['Title Filter'],
            array($this, 'swti_title_filter_cb'),
            'swti_main_fields',
            'swti_main_settings'
        );
        add_settings_field(
            'swti_limit',
            $tooltipArray['Limit'],
            array($this, 'swti_limit_cb'),
            'swti_main_fields',
            'swti_main_settings'
        );
        if ( !sti_fs()->is__premium_only() || sti_fs()->is_free_plan() ) {
            add_settings_field(
                'swti_colour_theme',
                $tooltipArray['Colour Theme'],
                array($this, 'swti_colour_theme_cb'),
                'swti_main_fields',
                'swti_main_settings'
            );
        }
        // Layout Settings
        add_settings_field(
            'swti_layout',
            $tooltipArray['Layout'],
            array($this, 'swti_layout_cb'),
            'swti_layout_fields',
            'swti_layout_settings'
        );
        // Embed Settings
        add_settings_field(
            'swti_embed',
            $tooltipArray['Embed'],
            array($this, 'swti_embed_cb'),
            'swti_embed_fields',
            'swti_embed_settings'
        );
        add_settings_field(
            'swti_embed_theme',
            $tooltipArray['Embed Colour Scheme'],
            array($this, 'swti_embed_theme_cb'),
            'swti_embed_fields',
            'swti_embed_settings'
        );
        add_settings_field(
            'swti_embed_chat',
            $tooltipArray['Display Chat'],
            array($this, 'swti_embed_chat_cb'),
            'swti_embed_fields',
            'swti_embed_settings'
        );
        add_settings_field(
            'swti_embed_title',
            $tooltipArray['Display Title'],
            array($this, 'swti_embed_title_cb'),
            'swti_embed_fields',
            'swti_embed_settings'
        );
        add_settings_field(
            'swti_embed_title_position',
            $tooltipArray['Title Position'],
            array($this, 'swti_embed_title_position_cb'),
            'swti_embed_fields',
            'swti_embed_settings'
        );
        add_settings_field(
            'swti_embed_muted',
            $tooltipArray['Start Muted'],
            array($this, 'swti_embed_muted_cb'),
            'swti_embed_fields',
            'swti_embed_settings'
        );
        // Extra Settings
        add_settings_field(
            'swti_show_offline',
            $tooltipArray['Show Offline Streams'],
            array($this, 'swti_show_offline_cb'),
            'swti_offline_fields',
            'swti_offline_settings'
        );
        add_settings_field(
            'swti_show_offline_text',
            $tooltipArray['Offline Message'],
            array($this, 'swti_show_offline_text_cb'),
            'swti_offline_fields',
            'swti_offline_settings'
        );
        add_settings_field(
            'swti_show_offline_image',
            $tooltipArray['Show Offline Image'],
            array($this, 'swti_show_offline_image_cb'),
            'swti_offline_fields',
            'swti_offline_settings'
        );
        // Offline Settings
        add_settings_field(
            'swti_autoplay',
            $tooltipArray['Autoplay Stream'],
            array($this, 'swti_autoplay_cb'),
            'swti_autoplay_fields',
            'swti_autoplay_settings'
        );
        add_settings_field(
            'swti_autoplay_offline',
            $tooltipArray['Autoplay Offline'],
            array($this, 'swti_autoplay_offline_cb'),
            'swti_autoplay_fields',
            'swti_autoplay_settings'
        );
        add_settings_field(
            'swti_autoplay_select',
            $tooltipArray['Autoplay Select'],
            array($this, 'swti_autoplay_select_cb'),
            'swti_autoplay_fields',
            'swti_autoplay_settings'
        );
        add_settings_field(
            'swti_featured_stream',
            $tooltipArray['Featured Streamer'],
            array($this, 'swti_featured_stream_cb'),
            'swti_autoplay_fields',
            'swti_autoplay_settings'
        );
        // Appearance Settings
        add_settings_field(
            'swti_title',
            $tooltipArray['Title'],
            array($this, 'swti_title_cb'),
            'swti_appearance_fields',
            'swti_appearance_settings'
        );
        add_settings_field(
            'swti_subtitle',
            $tooltipArray['Subtitle'],
            array($this, 'swti_subtitle_cb'),
            'swti_appearance_fields',
            'swti_appearance_settings'
        );
        add_settings_field(
            'swti_offline_image',
            $tooltipArray['Offline Image'],
            array($this, 'swti_offline_image_cb'),
            'swti_appearance_fields',
            'swti_appearance_settings'
        );
        add_settings_field(
            'swti_logo_image',
            $tooltipArray['Logo'],
            array($this, 'swti_logo_image_cb'),
            'swti_appearance_fields',
            'swti_appearance_settings'
        );
        add_settings_field(
            'swti_profile_image',
            $tooltipArray['Profile'],
            array($this, 'swti_profile_image_cb'),
            'swti_appearance_fields',
            'swti_appearance_settings'
        );
        add_settings_field(
            'swti_logo_bg_colour',
            $tooltipArray['Logo Background Colour'],
            array($this, 'swti_logo_bg_colour_cb'),
            'swti_appearance_fields',
            'swti_appearance_settings'
        );
        add_settings_field(
            'swti_logo_border_colour',
            $tooltipArray['Logo Border Colour'],
            array($this, 'swti_logo_border_colour_cb'),
            'swti_appearance_fields',
            'swti_appearance_settings'
        );
        add_settings_field(
            'swti_max_width',
            $tooltipArray['Max Width'],
            array($this, 'swti_max_width_cb'),
            'swti_appearance_fields',
            'swti_appearance_settings'
        );
        // Tile Settings
        add_settings_field(
            'swti_tile_layout',
            $tooltipArray['Tile Layout'],
            array($this, 'swti_tile_layout_cb'),
            'swti_tile_fields',
            'swti_tile_settings'
        );
        add_settings_field(
            'swti_tile_sorting',
            $tooltipArray['Tile Sorting'],
            array($this, 'swti_tile_sorting_cb'),
            'swti_tile_fields',
            'swti_tile_settings'
        );
        add_settings_field(
            'swti_tile_live_select',
            $tooltipArray['Tile Live'],
            array($this, 'swti_tile_live_select_cb'),
            'swti_tile_fields',
            'swti_tile_settings'
        );
        add_settings_field(
            'swti_tile_bg_colour',
            $tooltipArray['Background Colour'],
            array($this, 'swti_tile_bg_colour_cb'),
            'swti_tile_fields',
            'swti_tile_settings'
        );
        add_settings_field(
            'swti_tile_title_colour',
            $tooltipArray['Title Colour'],
            array($this, 'swti_tile_title_colour_cb'),
            'swti_tile_fields',
            'swti_tile_settings'
        );
        add_settings_field(
            'swti_tile_subtitle_colour',
            $tooltipArray['Subtitle Colour'],
            array($this, 'swti_tile_subtitle_colour_cb'),
            'swti_tile_fields',
            'swti_tile_settings'
        );
        add_settings_field(
            'swti_tile_rounded_corners',
            $tooltipArray['Rounded Corners'],
            array($this, 'swti_tile_rounded_corners_cb'),
            'swti_tile_fields',
            'swti_tile_settings'
        );
        // Hover  Settings
        add_settings_field(
            'swti_hover_effect',
            $tooltipArray['Hover Effect'],
            array($this, 'swti_hover_effect_cb'),
            'swti_hover_fields',
            'swti_hover_settings'
        );
        add_settings_field(
            'swti_hover_colour',
            $tooltipArray['Hover Colour'],
            array($this, 'swti_hover_colour_cb'),
            'swti_hover_fields',
            'swti_hover_settings'
        );
        // Refresh  Settings
        add_settings_field(
            'swti_refresh',
            $tooltipArray['Refresh'],
            array($this, 'swti_refresh_cb'),
            'swti_refresh_fields',
            'swti_refresh_settings'
        );
        // Error  Settings
        add_settings_field(
            'swti_nonce_check',
            'Nonce Check',
            array($this, 'swti_nonce_check_cb'),
            'swti_debug_fields',
            'swti_debug_settings'
        );
        add_settings_field(
            'swti_debug',
            'Error Log',
            array($this, 'swti_debug_cb'),
            'swti_debug_fields',
            'swti_debug_settings'
        );
    }

    public function swti_showAdmin() {
        include 'partials/streamweasels-admin-display.php';
    }

    public function swti_api_connection_status_cb() {
        $connection_status = ( isset( $this->options['swti_api_connection_status'] ) ? $this->options['swti_api_connection_status'] : '' );
        $connection_token = ( isset( $this->options['swti_api_access_token'] ) ? $this->options['swti_api_access_token'] : '' );
        $connection_expires = ( isset( $this->options['swti_api_access_token_expires'] ) ? $this->options['swti_api_access_token_expires'] : '' );
        $connection_error_code = ( isset( $this->options['swti_api_access_token_error_code'] ) ? $this->options['swti_api_access_token_error_code'] : '' );
        $connection_error_message = ( isset( $this->options['swti_api_access_token_error_message'] ) ? $this->options['swti_api_access_token_error_message'] : '' );
        $connection_expires_meta = '';
        $dateTimestamp1 = '';
        $dateTimestamp2 = '';
        if ( $connection_token !== '' ) {
            $license_status_colour = 'green';
            $license_status_label = 'Twitch API Connected!';
        } else {
            $license_status_colour = 'gray';
            $license_status_label = 'Not Connected';
        }
        if ( $connection_expires !== '' ) {
            $connection_expires_meta = '(expires on ' . $connection_expires . ')';
            $dateTimestamp1 = strtotime( $connection_expires );
            $dateTimestamp2 = strtotime( date( 'Y-m-d' ) );
        }
        if ( $connection_expires !== '' && $dateTimestamp2 > $dateTimestamp1 ) {
            $license_status_colour = 'red';
            $license_status_label = 'Twitch API Connection Expired!';
            $connection_expires_meta = '(expired on ' . $connection_expires . ')';
        }
        if ( $connection_error_code !== '' ) {
            $license_status_colour = 'red';
            $license_status_label = 'Twitch API Connection Error!';
            $connection_expires_meta = '(' . $connection_error_message . ')';
        }
        ?>
		<span style="color: <?php 
        echo esc_html( $license_status_colour );
        ?>; font-weight: bold;"><?php 
        echo esc_html( $license_status_label ) . ' ' . esc_html( $connection_expires_meta );
        ?></span>
		<div class="sw-debug-fields">
			<br>		
			<input type="hidden"  id="sw-access-token" name="swti_options[swti_api_access_token]" value="<?php 
        echo esc_html( $connection_token );
        ?>" />
			<input type="hidden"  id="sw-access-token-expires" name="swti_options[swti_api_access_token_expires]" value="<?php 
        echo esc_html( $connection_expires );
        ?>" />
			<input type="hidden"  id="sw-access-token-error-code" name="swti_options[swti_api_access_token_error_code]" value="<?php 
        echo esc_html( $connection_error_code );
        ?>" />
			<input type="hidden"  id="sw-access-token-error-message" name="swti_options[swti_api_access_token_error_message]" value="<?php 
        echo esc_html( $connection_error_message );
        ?>" />
		</div>
		<?php 
    }

    public function swti_client_id_cb() {
        $connection_token = ( isset( $this->options['swti_api_access_token'] ) ? $this->options['swti_api_access_token'] : '' );
        $client_id = ( isset( $this->options['swti_client_id'] ) ? $this->options['swti_client_id'] : '' );
        ?>

		<?php 
        if ( !empty( $connection_token ) && empty( $client_id ) ) {
            ?>
			<div class="sw-notice notice-error"><p><strong>Error. Client ID cannot be empty!</strong></p></div>
		<?php 
        }
        ?>		

		<input type="" id="sw-client-id" name="swti_options[swti_client_id]" size='40' value="<?php 
        echo esc_html( $client_id );
        ?>" />

		<?php 
    }

    public function swti_client_secret_cb() {
        $client_secret = ( isset( $this->options['swti_client_secret'] ) ? $this->options['swti_client_secret'] : '' );
        ?>

		<input type="" id="sw-client-secret" name="swti_options[swti_client_secret]" size='40' value="<?php 
        echo esc_html( $client_secret );
        ?>" />

		<?php 
    }

    public function swti_client_token_cb() {
        $token = ( isset( $this->options['swti_api_access_token'] ) ? $this->options['swti_api_access_token'] : '' );
        ?>
		
		<input type="text" disabled id="sw-client-token" name="" size='40' value="<?php 
        echo esc_html( $token );
        ?>" />

		<input type="hidden" id="sw-refresh-token" name="swti_options[swti_refresh_token]" value="0" />
		<?php 
        submit_button(
            'Refresh Token',
            'delete button-secondary',
            'sw-refresh-token-submit',
            false,
            array(
                'style' => '',
            )
        );
        ?>

		<?php 
    }

    /**
     * Shortcode Settings
     *
     */
    public function swti_shortcode_cb() {
        ?>
		<div class="postbox-half-wrapper">
			<div class="postbox-half">
				<h3>Simple Shortcode (for one Twitch Integration)</h3>
				<p>If you are simply using one Twitch Integration on your site, you can fill in the settings on this page and use this simple shortcode:</p>
				<span class="swti-shortcode simple-shortcode">[sw-twitch]</span>
				<br>
				<br>
				<a class="button-secondary tooltipped-n" id="sw-copy-shortcode" data-done="section copied" data-clipboard-target=".simple-shortcode" aria-label="Copied!" >Copy Simple Shortcode</a>
			</div>
			<div class="postbox-half">
				<h3>Advanced Shortcode (for many Twitch Integrations)</h3>
				<p>If you are using more than one Twitch Integration on your site, and you need to change the settings on each, use our advanced shortcode:</p>
				<span class="swti-shortcode advanced-shortcode">[sw-twitch]</span>
				<br>
				<br>
				<a class="button-secondary tooltipped-n" id="sw-copy-shortcode" data-done="section copied" data-clipboard-target=".advanced-shortcode" aria-label="Copied!" >Copy Advanced Shortcode</a>
			</div>	
		</div>
		<?php 
    }

    /**
     * Shortcode Settings
     *
     */
    public function swti_translations_live_cb() {
        $live = ( isset( $this->translations['swti_translations_live'] ) ? $this->translations['swti_translations_live'] : '' );
        ?>
		
		<input type="text" id="sw-translations-live" name="swti_translations[swti_translations_live]" size='40' placeholder="live" value="<?php 
        echo esc_html( $live );
        ?>" />
		<?php 
    }

    public function swti_translations_offline_cb() {
        $offline = ( isset( $this->translations['swti_translations_offline'] ) ? $this->translations['swti_translations_offline'] : '' );
        ?>
		
		<input type="text" id="sw-translations-offline" name="swti_translations[swti_translations_offline]" size='40' placeholder="offline" value="<?php 
        echo esc_html( $offline );
        ?>" />
		<?php 
    }

    public function swti_translations_for_cb() {
        $for = ( isset( $this->translations['swti_translations_for'] ) ? $this->translations['swti_translations_for'] : '' );
        ?>
		
		<input type="text" id="sw-translations-for" name="swti_translations[swti_translations_for]" size='40' placeholder="for" value="<?php 
        echo esc_html( $for );
        ?>" />
		<?php 
    }

    public function swti_translations_viewers_cb() {
        $viewers = ( isset( $this->translations['swti_translations_viewers'] ) ? $this->translations['swti_translations_viewers'] : '' );
        ?>
		
		<input type="text" id="sw-translations-viewers" name="swti_translations[swti_translations_viewers]" size='40' placeholder="viewers" value="<?php 
        echo esc_html( $viewers );
        ?>" />
		<?php 
    }

    public function swti_translations_streaming_cb() {
        $streaming = ( isset( $this->translations['swti_translations_streaming'] ) ? $this->translations['swti_translations_streaming'] : '' );
        ?>
		
		<input type="text" id="sw-translations-streaming" name="swti_translations[swti_translations_streaming]" size='40' placeholder="streaming" value="<?php 
        echo esc_html( $streaming );
        ?>" />
		<?php 
    }

    /**
     * Main Settings
     *
     */
    public function swti_game_cb() {
        $game = ( isset( $this->options['swti_game'] ) ? $this->options['swti_game'] : '' );
        $gameId = ( isset( $this->options['swti_game_id'] ) ? $this->options['swti_game_id'] : '' );
        ?>
		
		<?php 
        if ( !empty( $game ) && empty( $gameId ) ) {
            ?>
			<div class="sw-notice notice-error"><p><strong>Error. Game not found in the <a href="https://www.twitch.tv/directory/">Twitch Directory</a>. Are you sure it's spelt correctly? <a href="#">Get help!</a></strong></p></div>
		<?php 
        }
        ?>

		<div>
			<input type="text" id="sw-game" name="swti_options[swti_game]" size='40' placeholder="example: Hearthstone" value="<?php 
        echo esc_html( $game );
        ?>" />
			<?php 
        if ( !empty( $game ) && !empty( $gameId ) ) {
            ?>
				<p class="sw-success"><span class="dashicons dashicons-yes-alt"></span>Game ID: <?php 
            echo esc_html( $gameId );
            ?></p>
			<?php 
        }
        ?>
		</div>

		<input type="hidden" id="sw-game-id" name="swti_options[swti_game_id]" size='40' value="<?php 
        echo esc_html( $gameId );
        ?>" />
		<p>Enter the game name exactly as it appears on <a href="https://www.twitch.tv/directory/gaming">Twitch</a>.</p>

		<?php 
    }

    public function swti_language_cb() {
        $language = ( isset( $this->options['swti_language'] ) ? $this->options['swti_language'] : '' );
        ?>
		
		<input type="text" id="sw-language" name="swti_options[swti_language]" size='40' placeholder="example: en" value="<?php 
        echo esc_html( $language );
        ?>" />
		<p>If you would like to limit your streams to a certain language, enter the ISO 639-1 two-letter <a href="https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes">language code</a>.</p>

		<?php 
    }

    public function swti_channels_cb() {
        $channels = ( isset( $this->options['swti_channels'] ) ? $this->options['swti_channels'] : '' );
        ?>
		
		<div>
			<input type="text" id="sw-channels" name="swti_options[swti_channels]" size='40' placeholder="example: lirik,shroud,sodapoppin" value="<?php 
        echo esc_html( $channels );
        ?>" />
			<?php 
        if ( !empty( $channels ) ) {
            ?>
				<p class="sw-success"><span class="dashicons dashicons-yes-alt"></span>Channels: <?php 
            echo (int) count( explode( ',', $channels ) );
            ?></p>
			<?php 
        }
        ?>			
		</div>
		<p>Enter a list of channel names, with each channel name seperated by a comma.</p>

		<?php 
    }

    public function swti_team_cb() {
        $team = ( isset( $this->options['swti_team'] ) ? $this->options['swti_team'] : '' );
        $teamChannels = ( isset( $this->options['swti_team_channels'] ) ? $this->options['swti_team_channels'] : '' );
        ?>
		
		<?php 
        if ( !empty( $team ) && empty( $teamChannels ) ) {
            ?>
			<div class="sw-notice notice-error"><p><strong>Error. Team not found in the <a href="https://www.twitchtools.com/teams">Twitch Directory</a>. Are you sure it's spelt correctly? <a href="#">Get help!</a></strong></p></div>
		<?php 
        }
        ?>

		<div>
			<input type="text" id="sw-team" name="swti_options[swti_team]" size='40' placeholder="example: ths" value="<?php 
        echo esc_html( $team );
        ?>" />
			<?php 
        if ( !empty( $team ) && !empty( $teamChannels ) ) {
            ?>
				<p class="sw-success"><span class="dashicons dashicons-yes-alt"></span>Members: <?php 
            echo (int) count( explode( ',', $teamChannels ) );
            ?> (<a id="sw-show-hide-members" href="#">show members</a>)</p>
			<?php 
        }
        ?>
		</div>
		<textarea id="sw-team-channels" name="swti_options[swti_team_channels]" rows="6" style="width: 100%;display:none;"><?php 
        echo esc_textarea( $teamChannels );
        ?></textarea>
		<p>Enter the name of a <a href="https://help.twitch.tv/s/article/twitch-teams?language=en_US">Twitch team</a>.</p>

		<?php 
    }

    public function swti_title_filter_cb() {
        $titleFilter = ( isset( $this->options['swti_title_filter'] ) ? $this->options['swti_title_filter'] : '' );
        $channels = ( isset( $this->options['swti_channels'] ) ? $this->options['swti_channels'] : '' );
        $team = ( isset( $this->options['swti_team'] ) ? $this->options['swti_team'] : '' );
        $game = ( isset( $this->options['swti_game'] ) ? $this->options['swti_game'] : '' );
        ?>
		
		<?php 
        if ( !empty( $titleFilter ) && empty( $channels ) && empty( $team ) && empty( $game ) ) {
            ?>
			<div class="sw-notice notice-error"><p><strong>Error. Title Filter field must be combined with either Game, Channels or Team.</strong></p></div>
		<?php 
        }
        ?>

		<div>
			<input type="text" id="sw-title-filter" name="swti_options[swti_title_filter]" size='40' placeholder="example: NoPixel" value="<?php 
        echo esc_html( $titleFilter );
        ?>" />
		</div>
		<p>Enter a specific tag and we will only only show streams which include that tag in the stream title.</p>

		<?php 
    }

    public function swti_limit_cb() {
        $limit = ( isset( $this->options['swti_limit'] ) ? $this->options['swti_limit'] : '' );
        ?>
		
		<input type="text" id="sw-limit" name="swti_options[swti_limit]" size='40' placeholder="example: 15" value="<?php 
        echo esc_html( $limit );
        ?>" />
		<p>Limit the maximum number of streams to display.</p>
		<?php 
        if ( sti_fs()->can_use_premium_code() == false ) {
            ?>
				<p>Current Plan: Free</p>
				<p>Stream Limit: 15</p>
				<p><a href="admin.php?page=streamweasels-pricing">Unlock more streams</a></p>
		<?php 
        }
        ?>		
		<?php 
        if ( sti_fs()->is_plan_or_trial( 'essentials', true ) ) {
            ?>
				<p>Current Plan: Essentials</p>
				<p>Stream Limit: 50</p>
				<p><a href="admin.php?page=streamweasels-pricing">Unlock more streams</a></p>
		<?php 
        }
        ?>
		<?php 
        if ( sti_fs()->is_plan_or_trial( 'premium', true ) ) {
            ?>
				<p><strong>Current Plan: Premium</strong></p>
				<p><strong>Stream Limit: 100</strong></p>	
				<p><a href="admin.php?page=streamweasels-pricing">Unlock more streams</a></p>
		<?php 
        }
        ?>
		<?php 
        if ( sti_fs()->is_plan_or_trial( 'pro', true ) ) {
            ?>
				<p>Current Plan: Pro</p>
				<p>Stream Limit: Unlimited</p>	
		<?php 
        }
    }

    public function swti_colour_theme_cb() {
        $colourTheme = ( isset( $this->options['swti_colour_theme'] ) ? $this->options['swti_colour_theme'] : '' );
        ?>
		
		<select id="sw-colour-theme" name="swti_options[swti_colour_theme]">
			<option value="light" <?php 
        echo selected( $colourTheme, 'light', false );
        ?>>Light Theme</option>	
            <option value="dark" <?php 
        echo selected( $colourTheme, 'dark', false );
        ?>>Dark Theme</option>
        </select>
		<p>Select the colour theme for your Twitch content. These colours match Twitch's own Light and Dark mode.</p>

		<?php 
    }

    /**
     * Layout Settings
     *
     */
    public function swti_layout_cb() {
        $swti_layout_options = $this->swti_twitch_get_layout_options();
        $layout = ( isset( $this->options['swti_layout'] ) ? $this->options['swti_layout'] : '' );
        ?>

		<select id="sw-layout" name="swti_options[swti_layout]">
			<?php 
        foreach ( $swti_layout_options as $key => $label ) {
            ?>
				<option value="<?php 
            echo esc_html( $key );
            ?>" <?php 
            selected( $layout, $key );
            ?>><?php 
            echo esc_html( $label );
            ?></option>
			<?php 
        }
        ?>
		</select>		
		
		<div id="fs_addons" class="wrap fs-section">
			<h3>Free Layouts</h3>
			<p>StreamWeasels Twitch Integration comes with <strong>five free layouts</strong> for you to choose from below.</p>		
			<br>	
			<ul class="fs-cards-list">
				<li class="fs-card fs-addon" data-slug="ttv-easy-embed-wall">
					<a href="https://support.streamweasels.com/article/78-twitch-integration-layout-guide" aria-label="More information about Twitch Wall" data-title="Twitch Wall" class="fs-overlay"></a>
					<div class="fs-inner">
						<ul>
							<li class="fs-card-banner" style="background-image: url('//s3-us-west-2.amazonaws.com/freemius/plugins/9087/card_banner.jpg');">
								<?php 
        echo ( in_array( "Wall", $swti_layout_options ) ? '<span class="fs-badge fs-installed-addon-badge">Active</span>' : '' );
        ?>           
							</li>
							<li class="fs-title">Twitch Wall</li>
							<li class="fs-offer">
							<span class="fs-price">Free Layout</span>
							</li>
							<li class="fs-description">Classic Twitch layout for displaying many streams at once.</li>
							<li class="fs-cta"><a class="button">View Details</a></li>
						</ul>
					</div>
				</li>
				<li class="fs-card fs-addon" data-slug="ttv-easy-embed-player">
					<a href="https://support.streamweasels.com/article/78-twitch-integration-layout-guide" aria-label="More information about Twitch Player" data-title="Twitch Player" class="fs-overlay"></a>
					<div class="fs-inner">
						<ul>
							<li class="fs-card-banner" style="background-image: url('//s3-us-west-2.amazonaws.com/freemius/plugins/9088/card_banner.jpg');">
								<?php 
        echo ( in_array( "Player", $swti_layout_options ) ? '<span class="fs-badge fs-installed-addon-badge">Active</span>' : '' );
        ?>
							</li>
							<!-- <li class="fs-tag"></li> -->
							<li class="fs-title">Twitch Player</li>
							<li class="fs-offer">
							<span class="fs-price">Free Layout</span>
							</li>
							<li class="fs-description">Compact, cinema-style layout, great for embedded streams.</li>
							<li class="fs-cta"><a class="button">View Details</a></li>
						</ul>
					</div>
				</li>
				<li class="fs-card fs-addon" data-slug="ttv-easy-embed">
					<a href="https://support.streamweasels.com/article/78-twitch-integration-layout-guide" aria-label="More information about Twitch Rail" data-title="Twitch Rail" class="fs-overlay"></a>
					<div class="fs-inner">
						<ul>
							<li class="fs-card-banner" style="background-image: url('//s3-us-west-2.amazonaws.com/freemius/plugins/9089/card_banner.jpg');">
								<?php 
        echo ( in_array( "Rail", $swti_layout_options ) ? '<span class="fs-badge fs-installed-addon-badge">Active</span>' : '' );
        ?>
							</li>
							<!-- <li class="fs-tag"></li> -->
							<li class="fs-title">Twitch Rail</li>
							<li class="fs-offer">
							<span class="fs-price">Free Layout</span>
							</li>
							<li class="fs-description">Horizontal scrolling layout, display many streamers in a small space.</li>
							<li class="fs-cta"><a class="button">View Details</a></li>
						</ul>
					</div>
				</li>
				<li class="fs-card fs-addon" data-slug="stream-status-for-twitch">
					<a href="https://support.streamweasels.com/article/78-twitch-integration-layout-guide" aria-label="More information about Stream Status for Twitch" data-title="Stream Status for Twitch" class="fs-overlay"></a>
					<div class="fs-inner">
						<ul>
							<li class="fs-card-banner" style="background-image: url('//s3-us-west-2.amazonaws.com/freemius/plugins/9851/card_banner.jpg');">
								<?php 
        echo ( in_array( "Status", $swti_layout_options ) ? '<span class="fs-badge fs-installed-addon-badge">Active</span>' : '' );
        ?>
							</li>
							<!-- <li class="fs-tag"></li> -->
							<li class="fs-title">Twitch Status</li>
							<li class="fs-offer">
							<span class="fs-price">Free Layout</span>
							</li>
							<li class="fs-description">Simply display Twitch live status on every page of your website.</li>
							<li class="fs-cta"><a class="button">View Details</a></li>
						</ul>
					</div>
				</li>
				<li class="fs-card fs-addon" data-slug="streamweasels-vods-pro">
					<a href="https://support.streamweasels.com/article/78-twitch-integration-layout-guide" aria-label="More information about Twitch Vods" data-title="Twitch Vods" class="fs-overlay"></a>
					<div class="fs-inner">
						<ul>
							<li class="fs-card-banner" style="background-image: url('//s3-us-west-2.amazonaws.com/freemius/plugins/9974/card_banner.png');">
								<?php 
        echo ( in_array( "Vods", $swti_layout_options ) ? '<span class="fs-badge fs-installed-addon-badge">Active</span>' : '' );
        ?>
							</li>
							<!-- <li class="fs-tag"></li> -->
							<li class="fs-title">Twitch Vods</li>
							<li class="fs-offer">
							<span class="fs-price">Free Layout</span>
							</li>
							<li class="fs-description">Display clips, highlights and past broadcasts from Twitch.</li>
							<li class="fs-cta"><a class="button">View Details</a></li>
						</ul>
					</div>				
				</li>					
			</ul>
			<h3 id="paid-layouts">PRO Layouts</h3>
			<p>Looking for more options? We have <strong>three more professional layouts</strong> for you to choose from below.</p>		
			<br>			
			<ul class="fs-cards-list">
				<li class="fs-card fs-addon" data-slug="streamweasels-feature-pro">
					<a href="admin.php?page=streamweasels-pricing" aria-label="More information about Twitch Feature" data-title="Twitch Feature" class="fs-overlay"></a>
					<div class="fs-inner">
						<ul>
							<li class="fs-card-banner" style="background-image: url('//s3-us-west-2.amazonaws.com/freemius/plugins/9163/card_banner.jpg');">
								<?php 
        echo ( in_array( "Feature", $swti_layout_options ) ? '<span class="fs-badge fs-installed-addon-badge">Active</span>' : '' );
        ?>
							</li>
							<!-- <li class="fs-tag"></li> -->
							<li class="fs-title">Twitch Feature</li>
							<li class="fs-offer">
							<span class="fs-price">PRO Layout</span>
							</li>
							<li class="fs-description">Slick, professional layout inspired by the Twitch homepage.</li>
							<li class="fs-cta"><a class="button" href="admin.php?page=streamweasels-pricing">View Demo</a></li>
						</ul>
					</div>
					<div class="fs-extras">
						<a href="admin.php?page=streamweasels-pricing"><?php 
        echo ( in_array( "Feature", $swti_layout_options ) ? 'Layout Unlocked!' : ' Unlock Layout' );
        ?></a> | 
						<a href="https://www.streamweasels.com/twitch-wordpress-plugins/twitch-feature/?utm_source=wordpress&utm_medium=twitch-integration&utm_campaign=view-demo" target="_blank">View Demo</a>
					</div>
				</li>
				<li class="fs-card fs-addon" data-slug="streamweasels-showcase-pro">
					<a href="admin.php?page=streamweasels-pricing" aria-label="More information about Twitch Showcase" data-title="Twitch Showcase" class="fs-overlay"></a>
					<div class="fs-inner">
						<ul>
							<li class="fs-card-banner" style="background-image: url('//s3-us-west-2.amazonaws.com/freemius/plugins/10378/card_banner.jpg');">
								<?php 
        echo ( in_array( "Showcase", $swti_layout_options ) ? '<span class="fs-badge fs-installed-addon-badge">Active</span>' : '' );
        ?>
							</li>
							<!-- <li class="fs-tag"></li> -->
							<li class="fs-title">Twitch Showcase</li>
							<li class="fs-offer">
							<span class="fs-price">PRO Layout</span>
							</li>
							<li class="fs-description">Professional eSports-inspired layout inspired by FaZe Clan.</li>
							<li class="fs-cta"><a class="button">View Demo</a></li>
						</ul>
					</div>
					<div class="fs-extras">
						<a href="admin.php?page=streamweasels-pricing"><?php 
        echo ( in_array( "Showcase", $swti_layout_options ) ? 'Layout Unlocked!' : ' Unlock Layout' );
        ?></a> | 
						<a href="https://www.streamweasels.com/twitch-wordpress-plugins/twitch-showcase/?utm_source=wordpress&utm_medium=twitch-integration&utm_campaign=view-demo" target="_blank">View Demo</a>
					</div>					
				</li>						
				<li class="fs-card fs-addon" data-slug="streamweasels-nav-pro">
					<a href="admin.php?page=streamweasels-pricing" aria-label="More information about Twitch Nav" data-title="Twitch Nav" class="fs-overlay"></a>
					<div class="fs-inner">
						<ul>
							<li class="fs-card-banner" style="background-image: url('//s3-us-west-2.amazonaws.com/freemius/plugins/9896/card_banner.jpg');">
								<?php 
        echo ( in_array( "Nav", $swti_layout_options ) ? '<span class="fs-badge fs-installed-addon-badge">Active</span>' : '' );
        ?>
							</li>
							<!-- <li class="fs-tag"></li> -->
							<li class="fs-title">Twitch Nav</li>
							<li class="fs-offer">
							<span class="fs-price">PRO Layout</span>
							</li>
							<li class="fs-description">The easiest way to display Twitch status in your main navigation.</li>
							<li class="fs-cta"><a class="button">View Demo</a></li>
						</ul>
					</div>
					<div class="fs-extras">
						<a href="admin.php?page=streamweasels-pricing"><?php 
        echo ( in_array( "Nav", $swti_layout_options ) ? 'Layout Unlocked!' : ' Unlock Layout' );
        ?></a> | 
						<a href="https://www.streamweasels.com/twitch-wordpress-plugins/twitch-nav/?utm_source=wordpress&utm_medium=twitch-integration&utm_campaign=view-demo" target="_blank">View Demo</a>
					</div>					
				</li>												
			</ul>
		</div>
		<?php 
    }

    public function swti_embed_cb() {
        $embed = ( isset( $this->options['swti_embed'] ) ? $this->options['swti_embed'] : '' );
        ?>
		
		<select id="sw-embed" name="swti_options[swti_embed]">
            <option value="page" <?php 
        echo selected( $embed, 'page', false );
        ?>>Embed on page</option>
            <option value="popup" <?php 
        echo selected( $embed, 'popup', false );
        ?>>Embed in a popup</option>
			<option value="twitch" <?php 
        echo selected( $embed, 'twitch', false );
        ?>>Link to Twitch</option>
        </select>
		<p>When users interact with your Twitch integration, you can choose how to display the embedded content.</p>

		<?php 
    }

    public function swti_embed_theme_cb() {
        $embedColour = ( isset( $this->options['swti_embed_theme'] ) ? $this->options['swti_embed_theme'] : '' );
        ?>
		
		<select id="sw-embed-theme" name="swti_options[swti_embed_theme]">
            <option value="dark" <?php 
        echo selected( $embedColour, 'dark', false );
        ?>>Dark Theme</option>
            <option value="light" <?php 
        echo selected( $embedColour, 'light', false );
        ?>>Light Theme</option>
        </select>
		<p>Select the colour scheme for your embedded Twitch content.</p>

		<?php 
    }

    public function swti_embed_chat_cb() {
        $chat = ( isset( $this->options['swti_embed_chat'] ) ? $this->options['swti_embed_chat'] : '' );
        ?>
		
		<input type="hidden" name="swti_options[swti_embed_chat]" value="0"/>
		<input type="checkbox" id="sw-embed-chat" name="swti_options[swti_embed_chat]" value="1" <?php 
        checked( 1, $chat, true );
        ?>/>
		<p>Choose to display chat for your embedded Twitch content.</p>

		<?php 
    }

    public function swti_embed_muted_cb() {
        $muted = ( isset( $this->options['swti_embed_muted'] ) ? $this->options['swti_embed_muted'] : '' );
        ?>
		
		<input type="hidden" name="swti_options[swti_embed_muted]" value="0"/>
		<input type="checkbox" id="sw-embed-muted" name="swti_options[swti_embed_muted]" value="1" <?php 
        checked( 1, $muted, true );
        ?>/>
		<p>Choose to start your embedded Twitch content muted.</p>

		<?php 
    }

    public function swti_embed_title_cb() {
        $title = ( isset( $this->options['swti_embed_title'] ) ? $this->options['swti_embed_title'] : '' );
        ?>
		
		<input type="hidden" name="swti_options[swti_embed_title]" value="0"/>
		<input type="checkbox" id="sw-embed-title" name="swti_options[swti_embed_title]" value="1" <?php 
        checked( 1, $title, true );
        ?>/>
		<p>Choose to display the title for your embedded Twitch content.</p>

		<?php 
    }

    public function swti_embed_title_position_cb() {
        $titlePosition = ( isset( $this->options['swti_embed_title_position'] ) ? $this->options['swti_embed_title_position'] : '' );
        ?>
		
		<select id="sw-embed-title-position" name="swti_options[swti_embed_title_position]">
            <option value="top" <?php 
        echo selected( $titlePosition, 'top', false );
        ?>>Top</option>
            <option value="bottom" <?php 
        echo selected( $titlePosition, 'bottom', false );
        ?>>Bottom</option>
        </select>
		<p>Change the position of the title for your embedded Twitch content.</p>

		<?php 
    }

    /**
     * Extra Settings
     *
     */
    public function swti_show_offline_cb() {
        $offline = ( isset( $this->options['swti_show_offline'] ) ? $this->options['swti_show_offline'] : '' );
        ?>
		
		<input type="hidden" name="swti_options[swti_show_offline]" value="0"/>
		<input type="checkbox" id="sw-show-offline" name="swti_options[swti_show_offline]" value="1" <?php 
        checked( 1, $offline, true );
        ?>/>
		<p>Choose to show all streams, even if they're offline.</p>

		<?php 
    }

    public function swti_show_offline_text_cb() {
        $offlineText = ( isset( $this->options['swti_show_offline_text'] ) ? $this->options['swti_show_offline_text'] : '' );
        ?>
		
		<input type="text" id="sw-show-offline-text" name="swti_options[swti_show_offline_text]" size='40' value="<?php 
        echo esc_html( $offlineText );
        ?>" />
		<p>Choose to display a custom message at the top when ALL streams are offline.</p>

		<?php 
    }

    public function swti_show_offline_image_cb() {
        $showOfflineImage = ( isset( $this->options['swti_show_offline_image'] ) ? $this->options['swti_show_offline_image'] : '' );
        ?>
		
		<input type="text" id="sw-show-offline-image" name="swti_options[swti_show_offline_image]" size='40' value="<?php 
        echo esc_html( $showOfflineImage );
        ?>" />
        <input type="button" name="upload-btn" class="upload-btn button-secondary" value="Upload Image">
		<p>Choose to display a custom image at the top when ALL streams are offline.</p>

		<?php 
    }

    public function swti_autoplay_cb() {
        $autoplay = ( isset( $this->options['swti_autoplay'] ) ? $this->options['swti_autoplay'] : 0 );
        ?>
		
		<input type="hidden" name="swti_options[swti_autoplay]" value="0"/>
		<input type="checkbox" id="sw-autoplay" name="swti_options[swti_autoplay]" value="1" <?php 
        checked( 1, $autoplay, true );
        ?> />
		<p>Choose to autoplay the top stream.</p>


		<?php 
    }

    public function swti_autoplay_offline_cb() {
        $autoplayOffline = ( isset( $this->options['swti_autoplay_offline'] ) ? $this->options['swti_autoplay_offline'] : 0 );
        ?>
		
		<input type="hidden" name="swti_options[swti_autoplay_offline]" value="0"/>
		<input type="checkbox" id="sw-autoplay" name="swti_options[swti_autoplay_offline]" value="1" <?php 
        checked( 1, $autoplayOffline, true );
        ?> />
		<p>Choose to autoplay the top stream, even if all streams are offline.</p>


		<?php 
    }

    public function swti_autoplay_select_cb() {
        $select = ( isset( $this->options['swti_autoplay_select'] ) ? $this->options['swti_autoplay_select'] : '' );
        ?>
		
		<select id="sw-autoplay-select" name="swti_options[swti_autoplay_select]">
			<option value="most" <?php 
        echo selected( $select, 'most', false );
        ?>>Most Viewers</option>
			<option value="least" <?php 
        echo selected( $select, 'least', false );
        ?>>Least Viewers</option>
			<option value="random" <?php 
        echo selected( $select, 'random', false );
        ?>>Random</option>
        </select>
		<p>Choose which stream to autoplay.</p>


		<?php 
    }

    public function swti_featured_stream_cb() {
        $featured = ( isset( $this->options['swti_featured_stream'] ) ? $this->options['swti_featured_stream'] : '' );
        ?>
		
		<input type="text" id="sw-featured-stream" name="swti_options[swti_featured_stream]" size='40' value="<?php 
        echo esc_html( $featured );
        ?>" />
		<p>Choose to autoplay a featured streamer, only if that streamer is online.</p>

		<?php 
    }

    /**
     * Appearance Settings
     *
     */
    public function swti_title_cb() {
        $title = ( isset( $this->options['swti_title'] ) ? $this->options['swti_title'] : '' );
        ?>
		
		<input type="text" id="sw-title" name="swti_options[swti_title]" size='40' value="<?php 
        echo esc_html( $title );
        ?>" />
		<p>Add your own title.</p>

		<?php 
    }

    public function swti_subtitle_cb() {
        $subtitle = ( isset( $this->options['swti_subtitle'] ) ? $this->options['swti_subtitle'] : '' );
        ?>
		
		<input type="text" id="sw-subtitle" name="swti_options[swti_subtitle]" size='40' value="<?php 
        echo esc_html( $subtitle );
        ?>" />
		<p>Add your own subtitle.</p>

		<?php 
    }

    public function swti_offline_image_cb() {
        $offline_image = ( isset( $this->options['swti_offline_image'] ) ? $this->options['swti_offline_image'] : '' );
        ?>
		
		<input type="text" id="sw-offline-image" name="swti_options[swti_offline_image]" size='40' value="<?php 
        echo esc_html( $offline_image );
        ?>" />
        <input type="button" name="upload-btn" class="upload-btn button-secondary" value="Upload Image">
		<p>Choose to display a custom image when there are no streams online. Ideal image dimensions are 440 x 248 or 880 x 496.</p>

		<?php 
    }

    public function swti_logo_image_cb() {
        $logo = ( isset( $this->options['swti_logo_image'] ) ? $this->options['swti_logo_image'] : '' );
        ?>
		
		<input type="text" id="sw-logo-image" name="swti_options[swti_logo_image]" size='40' value="<?php 
        echo esc_html( $logo );
        ?>" />
        <input type="button" name="upload-btn" class="upload-btn button-secondary" value="Upload Image">
		<p>Add your own logo. This should be a small square image, Ideal image dimensions are 80 x 80.</p>

		<?php 
    }

    public function swti_profile_image_cb() {
        $profileImage = ( isset( $this->options['swti_profile_image'] ) ? $this->options['swti_profile_image'] : '' );
        ?>
		
		<input type="hidden" name="swti_options[swti_profile_image]" value="0"/>
		<input type="checkbox" id="sw-profile-image" name="swti_options[swti_profile_image]" value="1" <?php 
        checked( 1, $profileImage, true );
        ?> />
		<p>Choose to display the users profile image from Twitch. This currently only works for offline streamers.</p>

		<?php 
    }

    public function swti_logo_bg_colour_cb() {
        $logoBg = ( isset( $this->options['swti_logo_bg_colour'] ) ? $this->options['swti_logo_bg_colour'] : '' );
        ?>
		
		<input type="text" id="sw-logo-bg-colour" name="swti_options[swti_logo_bg_colour]" size='40' value="<?php 
        echo esc_html( $logoBg );
        ?>" />
		<p>Add a background colour for your logo.</p>

		<?php 
    }

    public function swti_logo_border_colour_cb() {
        $logoBorder = ( isset( $this->options['swti_logo_border_colour'] ) ? $this->options['swti_logo_border_colour'] : '' );
        ?>
		
		<input type="text" id="sw-logo-border-colour" name="swti_options[swti_logo_border_colour]" size='40' value="<?php 
        echo esc_html( $logoBorder );
        ?>" />
		<p>Add a border colour for your logo.</p>


		<?php 
    }

    public function swti_max_width_cb() {
        $width = ( isset( $this->options['swti_max_width'] ) ? $this->options['swti_max_width'] : '' );
        ?>
		
		<select id="sw-max-width" name="swti_options[swti_max_width]">
            <option value="none" <?php 
        echo selected( $width, 'none', false );
        ?>>None</option>
            <option value="1920" <?php 
        echo selected( $width, '1920', false );
        ?>>1920px</option>
            <option value="1680" <?php 
        echo selected( $width, '1680', false );
        ?>>1680px</option>
            <option value="1440" <?php 
        echo selected( $width, '1440', false );
        ?>>1440px</option>
            <option value="1280" <?php 
        echo selected( $width, '1280', false );
        ?>>1280px</option>
            <option value="1024" <?php 
        echo selected( $width, '1024', false );
        ?>>1024px</option>
            <option value="768" <?php 
        echo selected( $width, '768', false );
        ?>>768px</option>
        </select>
		<p>Add a max width to your Twitch integration.</p>


		<?php 
    }

    /**
     * Tile Settings
     *
     */
    public function swti_tile_layout_cb() {
        $layout = ( isset( $this->options['swti_tile_layout'] ) ? $this->options['swti_tile_layout'] : '' );
        ?>
		
		<select id="sw-tile-layout" name="swti_options[swti_tile_layout]">
            <option value="detailed" <?php 
        echo selected( $layout, 'detailed', false );
        ?>>Detailed</option>
            <option value="compact" <?php 
        echo selected( $layout, 'compact', false );
        ?>>Compact</option>
        </select>
		<p>Choose the layout mode for your Twitch stream tiles.</p>

		<?php 
    }

    public function swti_tile_sorting_cb() {
        $sorting = ( isset( $this->options['swti_tile_sorting'] ) ? $this->options['swti_tile_sorting'] : '' );
        ?>
		
		<select id="sw-tile-sorting" name="swti_options[swti_tile_sorting]">
			<option value="most" <?php 
        echo selected( $sorting, 'most', false );
        ?>>Most Viewers</option>
			<option value="least" <?php 
        echo selected( $sorting, 'least', false );
        ?>>Least Viewers</option>
			<option value="alpha" <?php 
        echo selected( $sorting, 'alpha', false );
        ?>>Alphabetical</option>
			<option value="random" <?php 
        echo selected( $sorting, 'random', false );
        ?>>Random</option>
        </select>
		<p>Choose the sorting of the Twitch stream tiles.</p>

		<?php 
    }

    public function swti_tile_live_select_cb() {
        $live = ( isset( $this->options['swti_tile_live_select'] ) ? $this->options['swti_tile_live_select'] : '' );
        ?>
		
		<select id="sw-live-info" name="swti_options[swti_tile_live_select]">
			<option value="viewer" <?php 
        echo selected( $live, 'viewer', false );
        ?>>Viewer Count</option>
			<option value="online" <?php 
        echo selected( $live, 'online', false );
        ?>>Online / Offline dot</option>
			<option value="live" <?php 
        echo selected( $live, 'live', false );
        ?>>LIVE</option>
			<option value="none" <?php 
        echo selected( $live, 'none', false );
        ?>>None</option>
        </select>
		<p>Choose the live information to display in the top-left of each live stream.</p>

		<?php 
    }

    public function swti_tile_bg_colour_cb() {
        $bgColour = ( isset( $this->options['swti_tile_bg_colour'] ) ? $this->options['swti_tile_bg_colour'] : '' );
        ?>
		
		<input type="text" id="sw-tile-bg-colour" name="swti_options[swti_tile_bg_colour]" size='40' value="<?php 
        echo esc_html( $bgColour );
        ?>" />
		<p>Change the background colour for your Twitch stream tiles.</p>


		<?php 
    }

    public function swti_tile_title_colour_cb() {
        $titleColour = ( isset( $this->options['swti_tile_title_colour'] ) ? $this->options['swti_tile_title_colour'] : '' );
        ?>
		
		<input type="text" id="sw-tile-title-colour" name="swti_options[swti_tile_title_colour]" size='40' value="<?php 
        echo esc_html( $titleColour );
        ?>" />
		<p>Change the title colour for your Twitch stream tiles.</p>

		<?php 
    }

    public function swti_tile_subtitle_colour_cb() {
        $subtitleColour = ( isset( $this->options['swti_tile_subtitle_colour'] ) ? $this->options['swti_tile_subtitle_colour'] : '' );
        ?>
		
		<input type="text" id="sw-tile-subtitle-colour" name="swti_options[swti_tile_subtitle_colour]" size='40' value="<?php 
        echo esc_html( $subtitleColour );
        ?>" />
		<p>Change the subtitle colour for your Twitch stream tiles.</p>


		<?php 
    }

    public function swti_tile_rounded_corners_cb() {
        $roundedCorners = ( isset( $this->options['swti_tile_rounded_corners'] ) ? $this->options['swti_tile_rounded_corners'] : '5' );
        ?>

		<input id="sw-tile-rounded-corners" type="text" name="swti_options[swti_tile_rounded_corners]" value="<?php 
        echo esc_html( $roundedCorners );
        ?>">
		<span class="range-bar-value"></span>
		<p>Add rounded corners to your Twitch stream tiles.</p>


		<?php 
    }

    public function swti_hover_effect_cb() {
        $hoverEffect = ( isset( $this->options['swti_hover_effect'] ) ? $this->options['swti_hover_effect'] : '' );
        ?>
		
		<select id="sw-hover-effect" name="swti_options[swti_hover_effect]">
            <option value="none" <?php 
        echo selected( $hoverEffect, 'none', false );
        ?>>none</option>
            <option value="twitch" <?php 
        echo selected( $hoverEffect, 'twitch', false );
        ?>>Twitch Style</option>
			<option value="play" <?php 
        echo selected( $hoverEffect, 'play', false );
        ?>>Play Button</option>
        </select>
		<p>Change the hover effect for your Twitch stream tiles.</p>


		<?php 
    }

    public function swti_hover_colour_cb() {
        $hoverColour = ( isset( $this->options['swti_hover_colour'] ) ? $this->options['swti_hover_colour'] : '' );
        ?>
		
		<input type="text" id="sw-hover-colour" name="swti_options[swti_hover_colour]" size='40' value="<?php 
        echo esc_html( $hoverColour );
        ?>" />
		<p>Change the hover colour for your Twitch stream tiles.</p>

		<?php 
    }

    public function swti_refresh_cb() {
        $refresh = ( isset( $this->options['swti_refresh'] ) ? $this->options['swti_refresh'] : '' );
        ?>
		
		<input type="hidden" name="swti_options[swti_refresh]" value="0"/>
		<input type="checkbox" id="sw-refresh" name="swti_options[swti_refresh]" value="1" <?php 
        checked( 1, $refresh, true );
        ?> />
		<p>Choose to auto-refresh your streams at a set interval.</p>

		<?php 
    }

    /**
     * Debug Settings
     *
     */
    public function swti_nonce_check_cb() {
        $nonceCheck = ( isset( $this->options['swti_nonce_check'] ) ? $this->options['swti_nonce_check'] : '' );
        ?>
		
		<input type="hidden" name="swti_options[swti_nonce_check]" value="0"/>
		<input type="checkbox" id="sw-nonce-check" name="swti_options[swti_nonce_check]" value="1" <?php 
        checked( 1, $nonceCheck, true );
        ?> />
		<p>Disable nonce checking, useful if page caching is breaking your Twitch Integration.</p>

		<?php 
    }

    public function swti_debug_cb() {
        $dismissForGood5 = ( isset( $this->options['swti_dismiss_for_good5'] ) ? $this->options['swti_dismiss_for_good5'] : 0 );
        ?>
		
		<p>
			<textarea rows="6" style="width: 100%;"><?php 
        echo esc_textarea( get_option( 'swti_debug_log', '' ) );
        ?></textarea>
		</p>
		<p>
			<input type="hidden" id="sw-delete-log" name="swti_options[swti_delete_log]" value="0" />
			<input type="hidden" id="sw-dismiss-for-good5" name="swti_options[swti_dismiss_for_good5]" value="<?php 
        echo esc_html( $dismissForGood5 );
        ?>" />
			<?php 
        submit_button(
            'Clear logs',
            'delete button-secondary',
            'sw-delete-log-submit',
            false
        );
        ?>
		</p>

		<?php 
    }

    /**
     * Field Validation
     *
     */
    public function swti_options_validate( $input ) {
        $new_input = [];
        $options = get_option( 'swti_options' );
        if ( isset( $input['swti_client_id'] ) ) {
            $new_input['swti_client_id'] = sanitize_text_field( $input['swti_client_id'] );
        }
        if ( isset( $input['swti_client_secret'] ) ) {
            $new_input['swti_client_secret'] = sanitize_text_field( $input['swti_client_secret'] );
        }
        if ( isset( $input['swti_api_access_token'] ) ) {
            $new_input['swti_api_access_token'] = sanitize_text_field( $input['swti_api_access_token'] );
        }
        if ( isset( $input['swti_api_access_token_expires'] ) ) {
            $new_input['swti_api_access_token_expires'] = sanitize_text_field( $input['swti_api_access_token_expires'] );
        }
        if ( isset( $input['swti_api_access_token_meta'] ) ) {
            $new_input['swti_api_access_token_meta'] = sanitize_text_field( $input['swti_api_access_token_meta'] );
        }
        // oAUTH with Twitch
        if ( ($input['swti_api_access_token'] == '' || $input['swti_refresh_token'] == 1) && isset( $input['swti_client_id'] ) && isset( $input['swti_client_secret'] ) ) {
            $SWTI_Twitch_API = new SWTI_Twitch_API();
            if ( $input['swti_refresh_token'] == 1 ) {
                $SWTI_Twitch_API->refresh_token();
            }
            $result = $SWTI_Twitch_API->get_token( $input['swti_client_id'], $input['swti_client_secret'] );
            if ( $result[0] !== 'error' ) {
                $new_input['swti_api_access_token'] = $result[0];
                $new_input['swti_api_access_token_expires'] = $result[1];
                $new_input['swti_api_access_token_error_code'] = '';
                $new_input['swti_api_access_token_error_message'] = '';
            } else {
                $new_input['swti_api_access_token'] = '';
                $new_input['swti_api_access_token_expires'] = '';
                $new_input['swti_api_access_token_error_code'] = '403';
                $new_input['swti_api_access_token_error_message'] = $result[1];
            }
        }
        // Main Settings
        if ( isset( $input['swti_game'] ) ) {
            $new_input['swti_game'] = wp_kses( $input['swti_game'], 'post' );
            if ( isset( $input['swti_game'] ) && !empty( $input['swti_game'] ) ) {
                $SWTI_Twitch_API = new SWTI_Twitch_API();
                $new_input['swti_game_id'] = $SWTI_Twitch_API->get_game_id( $input['swti_game'] );
            }
        }
        if ( isset( $input['swti_language'] ) ) {
            $new_input['swti_language'] = sanitize_text_field( $input['swti_language'] );
        }
        if ( isset( $input['swti_channels'] ) ) {
            if ( substr( $input['swti_channels'], -1 ) == ',' ) {
                $input['swti_channels'] = substr( $input['swti_channels'], 0, -1 );
            }
            $input['swti_channels'] = str_replace( ' ', '', $input['swti_channels'] );
            $input['swti_channels'] = strtolower( $input['swti_channels'] );
            $new_input['swti_channels'] = sanitize_text_field( $input['swti_channels'] );
        }
        if ( isset( $input['swti_team'] ) ) {
            $new_input['swti_team'] = sanitize_text_field( $input['swti_team'] );
            if ( isset( $input['swti_team'] ) && !empty( $input['swti_team'] ) ) {
                $SWTI_Twitch_API = new SWTI_Twitch_API();
                $teamChannels = $SWTI_Twitch_API->get_team_channels( $input['swti_team'] );
                $new_input['swti_team_channels'] = $teamChannels;
            }
        }
        if ( isset( $input['swti_title_filter'] ) ) {
            $new_input['swti_title_filter'] = sanitize_text_field( $input['swti_title_filter'] );
        }
        if ( !empty( $input['swti_limit'] ) ) {
            $new_input['swti_limit'] = absint( $input['swti_limit'] );
            if ( sti_fs()->can_use_premium_code() == false && $input['swti_limit'] > 15 ) {
                $new_input['swti_limit'] = 15;
            }
            if ( sti_fs()->is_plan( 'essentials', true ) && $input['swti_limit'] > 50 ) {
                $new_input['swti_limit'] = 50;
            }
            if ( sti_fs()->is_plan( 'premium', true ) && $input['swti_limit'] > 100 ) {
                $new_input['swti_limit'] = 100;
            }
        } else {
            $new_input['swti_limit'] = '15';
        }
        if ( isset( $input['swti_colour_theme'] ) ) {
            $new_input['swti_colour_theme'] = sanitize_text_field( $input['swti_colour_theme'] );
        }
        // Layout Settings
        if ( isset( $input['swti_layout'] ) ) {
            $new_input['swti_layout'] = sanitize_text_field( $input['swti_layout'] );
        }
        if ( isset( $input['swti_nonce_check'] ) ) {
            $new_input['swti_nonce_check'] = (int) $input['swti_nonce_check'];
        }
        if ( isset( $input['swti_dismiss_for_good5'] ) ) {
            $new_input['swti_dismiss_for_good5'] = (int) $input['swti_dismiss_for_good5'];
        }
        if ( isset( $input['swti_delete_log'] ) && $input['swti_delete_log'] == 1 ) {
            $new_input['swti_dismiss_for_good5'] = 0;
            delete_option( 'swti_debug_log' );
        }
        return $new_input;
    }

    /**
     * Field Validation
     *
     */
    public function swti_translations_validate( $input ) {
        $new_input = array();
        // Translation Settings
        $default_translations = [
            'swti_translations_live'      => 'live',
            'swti_translations_offline'   => 'offline',
            'swti_translations_viewers'   => 'viewers',
            'swti_translations_streaming' => 'streaming',
            'swti_translations_for'       => 'for',
        ];
        foreach ( $default_translations as $key => $default ) {
            if ( isset( $input[$key] ) && !empty( $input[$key] ) ) {
                // Sanitize the input using sanitize_text_field to ensure safe text
                $new_input[$key] = sanitize_text_field( $input[$key] );
            } else {
                $new_input[$key] = $default;
            }
        }
        return $new_input;
    }

    function swti_twitch_debug_log( $message ) {
        if ( defined( 'WP_DEBUG' ) && WP_DEBUG === true ) {
            if ( is_array( $message ) || is_object( $message ) ) {
                error_log( print_r( $message, true ) );
            } else {
                error_log( $message );
            }
        }
    }

    function swti_twitch_debug_field( $message ) {
        if ( is_array( $message ) ) {
            $message = print_r( $message, true );
        }
        $log = get_option( 'swti_debug_log', '' );
        $string = date( 'd.m.Y H:i:s' ) . " : " . $message . "\n";
        $log .= $string;
        // Limit the log to the last 100 lines to prevent it from growing too large.
        $log_lines = explode( "\n", $log );
        if ( count( $log_lines ) > 100 ) {
            $log_lines = array_slice( $log_lines, -100, 100 );
        }
        $log = implode( "\n", $log_lines );
        update_option( 'swti_debug_log', $log );
    }

    function swti_get_options() {
        return get_option( 'swti_options', array() );
    }

    function swti_get_translations() {
        return get_option( 'swti_translations', array() );
    }

    function swti_do_settings_sections(
        $page,
        $icon,
        $desc,
        $status
    ) {
        global $wp_settings_sections, $wp_settings_fields;
        if ( !isset( $wp_settings_sections[$page] ) ) {
            return;
        }
        $allowed_html = [
            'h3'     => [
                'class' => [],
            ],
            'span'   => [
                'class' => [],
            ],
            'p'      => [],
            'table'  => [
                'class' => [],
            ],
            'div'    => [
                'class' => [],
            ],
            'a'      => [
                'href'   => [],
                'target' => [],
                'type'   => [],
                'class'  => [],
            ],
            'button' => [
                'class' => [],
            ],
        ];
        foreach ( (array) $wp_settings_sections[$page] as $section ) {
            $premium_status = ( sti_fs()->can_use_premium_code__premium_only() ? 'free' : $status );
            $title = ( !empty( $section['title'] ) ? "<h3 class='hndle'><span class='dashicons {$icon}'></span>{$section['title']}</h3>" : '' );
            $description = ( $desc ? "<p>{$desc}</p>" : '' );
            echo '<div class="postbox postbox-' . esc_attr( str_replace( ' ', '-', strtolower( $section['title'] ) ) ) . ' postbox-' . esc_attr( $premium_status ) . '">';
            echo wp_kses( $title, $allowed_html );
            echo '<div class="inside">';
            echo wp_kses( $description, $allowed_html );
            if ( !empty( $section['callback'] ) ) {
                call_user_func( $section['callback'], $section );
            }
            echo '<table class="form-table">';
            do_settings_fields( $page, $section['id'] );
            echo '</table>';
            if ( $section['title'] !== 'Shortcode' ) {
                submit_button();
            }
            if ( !sti_fs()->is__premium_only() || sti_fs()->is_free_plan() ) {
                if ( $premium_status == 'pro' ) {
                    echo '<div class="postbox-trial-wrapper"><a href="admin.php?page=streamweasels-pricing" target="_blank" type="button" class="button button-primary">Buy Now</a></div>';
                }
            }
            echo '</div></div>';
        }
    }

    function swti_action_links( $links, $file ) {
        if ( $file !== 'streamweasels-twitch-integration/streamweasels.php' && $file !== 'streamweasels-twitch-integration-pro/streamweasels.php' && $file !== 'streamweasels-twitch-integration-git/streamweasels.php' ) {
            return $links;
        }
        $settings_link = '<a href="' . admin_url( 'options-general.php?page=streamweasels' ) . '">Settings</a>';
        array_unshift( $links, $settings_link );
        return $links;
    }

    function swti_twitch_get_layout_options() {
        $options['none'] = 'None';
        $options['wall'] = 'Wall';
        $options['player'] = 'Player';
        $options['rail'] = 'Rail';
        $options['status'] = 'Status';
        $options['vods'] = 'Vods';
        $options = apply_filters( 'swti_twitch_layout_options', $options );
        return $options;
    }

}
