<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.streamweasels.com
 * @since      1.0.0
 *
 * @package    Streamweasels
 * @subpackage Streamweasels/public
 */
/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Streamweasels
 * @subpackage Streamweasels/public
 * @author     StreamWeasels <admin@streamweasels.com>
 */
class Streamweasels_Public {
    private $plugin_name;

    private $version;

    private $addon_rail_path;

    private $addon_wall_path;

    private $addon_player_path;

    private $addon_status_path;

    private $addon_feature_path;

    private $addon_nav_path;

    private $addon_vods_path;

    private $addon_showcase_path;

    public function __construct( $plugin_name, $version ) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->addon_rail_path = WP_PLUGIN_DIR . '/ttv-easy-embed';
        $this->addon_wall_path = WP_PLUGIN_DIR . '/ttv-easy-embed-wall';
        $this->addon_player_path = WP_PLUGIN_DIR . '/ttv-easy-embed-player';
        $this->addon_status_path = WP_PLUGIN_DIR . '/stream-status-for-twitch';
        $this->addon_feature_path = WP_PLUGIN_DIR . '/streamweasels-feature-pro';
        $this->addon_nav_path = WP_PLUGIN_DIR . '/streamweasels-nav-pro';
        $this->addon_vods_path = WP_PLUGIN_DIR . '/streamweasels-vods-pro';
        $this->addon_showcase_path = WP_PLUGIN_DIR . '/streamweasels-showcase-pro';
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
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
            plugin_dir_url( __FILE__ ) . 'dist/streamweasels-public.min.css',
            array(),
            $this->version,
            'all'
        );
        // The following options are used as CSS variables on the page
        $options = get_option( 'swti_options' );
        $colourTheme = sanitize_text_field( ( $options['swti_colour_theme'] ?? 'light' ?: 'light' ) );
        if ( $colourTheme == 'light' ) {
            $tileBgColourDefault = '#F7F7F8';
            $tileTitleColourDefault = '#1F1F23';
            $tileSubtitleColourDefault = '#53535F';
        } else {
            $tileBgColourDefault = '#0E0E10';
            $tileTitleColourDefault = '#DEDEE3';
            $tileSubtitleColourDefault = '#adb8a8';
        }
        $optionsRail = get_option( 'swti_options_rail' );
        $controlsBgColour = sanitize_text_field( ( $optionsRail['swti_rail_controls_bg_colour'] ?? '#000' ?: '#000' ) );
        $controlsArrowColour = sanitize_text_field( ( $optionsRail['swti_rail_controls_arrow_colour'] ?? '#fff' ?: '#fff' ) );
        $controlsBorderColour = sanitize_text_field( ( $optionsRail['swti_rail_border_colour'] ?? '#fff' ?: '#fff' ) );
        $optionsWall = get_option( 'swti_options_wall' );
        $tileColumnCount = sanitize_text_field( ( $optionsWall['swti_wall_column_count'] ?? '4' ?: '4' ) );
        $tileColumnSpacing = sanitize_text_field( ( $optionsWall['swti_wall_column_spacing'] ?? '10' ?: '10' ) );
        $optionsVods = get_option( 'swti_options_vods' );
        $tileVodsColumnCount = sanitize_text_field( ( $optionsVods['swti_vods_column_count'] ?? '4' ?: '4' ) );
        $tileVodsColumnSpacing = sanitize_text_field( ( $optionsVods['swti_vods_column_spacing'] ?? '10' ?: '10' ) );
        $optionsStatus = get_option( 'swti_options_status' );
        $statusVerticalDistance = sanitize_text_field( ( $optionsStatus['swti_status_vertical_distance'] ?? '25' ?: '25' ) );
        $statusHorizontalDistance = sanitize_text_field( ( $optionsStatus['swti_status_horizontal_distance'] ?? '25' ?: '25' ) );
        $statusLogoBackgroundColour = sanitize_text_field( ( $optionsStatus['swti_status_logo_background_colour'] ?? '#6441A4' ?: '#6441A4' ) );
        $statusLogoAccentColour = sanitize_text_field( ( $optionsStatus['swti_status_accent_colour'] ?? '#6441A4' ?: '#6441A4' ) );
        $statusCarouselBackgroundColour = sanitize_text_field( ( $optionsStatus['swti_status_carousel_background_colour'] ?? '#fff' ?: '#fff' ) );
        $statusCarouselArrowColour = sanitize_text_field( ( $optionsStatus['swti_status_carousel_arrow_colour'] ?? '#000' ?: '#000' ) );
        $logoBgColour = 'transparent';
        $logoBorderColour = 'transparent';
        $maxWidth = 'none';
        $tileBgColour = $tileBgColourDefault;
        $tileTitleColour = $tileTitleColourDefault;
        $tileSubtitleColour = $tileSubtitleColourDefault;
        $tileRoundedCorners = '0';
        $hoverColour = 'transparent';
        $layout = sanitize_text_field( $options['swti_layout'] ?? '' );
        // Overrides based on layout go here
        if ( $layout == 'rail' || $layout == 'feature' ) {
            $hoverColour = '';
        }
        $streamWeaselsCssVars = '
			:root {
				--logo-bg-colour: ' . esc_attr( $logoBgColour ) . ';
				--logo-border-colour: ' . esc_attr( $logoBorderColour ) . ';
				--max-width: ' . esc_attr( $maxWidth ) . ';
				--tile-bg-colour: ' . esc_attr( $tileBgColour ) . ';
				--tile-title-colour: ' . esc_attr( $tileTitleColour ) . ';
				--tile-subtitle-colour: ' . esc_attr( $tileSubtitleColour ) . ';
				--tile-rounded-corners: ' . esc_attr( $tileRoundedCorners ) . ';
				--hover-colour: ' . esc_attr( $hoverColour ) . ';
				--controls-bg-colour: ' . esc_attr( $controlsBgColour ) . ';
				--controls-arrow-colour: ' . esc_attr( $controlsArrowColour ) . ';
				--controls-border-colour: ' . esc_attr( $controlsBorderColour ) . ';
				--tile-column-count: ' . esc_attr( $tileColumnCount ) . ';
				--tile-column-spacing: ' . esc_attr( $tileColumnSpacing ) . ';
				--tile-vods-column-count: ' . esc_attr( $tileVodsColumnCount ) . ';
				--tile-vods-column-spacing: ' . esc_attr( $tileVodsColumnSpacing ) . ';
				--status-vertical-distance: ' . esc_attr( $statusVerticalDistance ) . ';
				--status-horizontal-distance: ' . esc_attr( $statusHorizontalDistance ) . ';
				--status-logo-accent-colour: ' . esc_attr( $statusLogoAccentColour ) . ';
				--status-logo-background-colour: ' . esc_attr( $statusLogoBackgroundColour ) . ';
				--status-carousel-background-colour: ' . esc_attr( $statusCarouselBackgroundColour ) . ';
				--status-carousel-arrow-colour: ' . esc_attr( $statusCarouselArrowColour ) . ';
			}
		';
        wp_add_inline_style( $this->plugin_name, $streamWeaselsCssVars );
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {
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
        wp_enqueue_script(
            $this->plugin_name,
            plugin_dir_url( __FILE__ ) . 'dist/streamweasels-public.min.js',
            '',
            $this->version,
            true
        );
        wp_enqueue_script(
            'twitch-API',
            'https://embed.twitch.tv/embed/v1.js',
            array('jquery'),
            '',
            false
        );
        wp_enqueue_script(
            $this->plugin_name . '-slick',
            plugin_dir_url( __FILE__ ) . 'dist/slick.min.js',
            array('jquery'),
            $this->version,
            true
        );
        $options = get_option( 'swti_options' );
        wp_add_inline_script( $this->plugin_name, 'const streamWeaselsVars = ' . json_encode( array(
            'ajaxUrl'   => admin_url( 'admin-ajax.php' ),
            'thumbnail' => plugin_dir_url( __FILE__ ) . 'img/sw-blank.png',
            'siteUrl'   => esc_url( get_site_url() ),
        ) ), 'before' );
    }

    public function generate_fresh_nonce() {
        $nonce = wp_create_nonce( 'wp_rest' );
        wp_send_json_success( array(
            'nonce' => $nonce,
        ) );
        wp_die();
    }

    public function register_ajax_handler() {
        add_action( 'wp_ajax_get_fresh_nonce', array($this, 'generate_fresh_nonce') );
        add_action( 'wp_ajax_nopriv_get_fresh_nonce', array($this, 'generate_fresh_nonce') );
    }

    public function streamWeasels_shortcode() {
        // Setup the streamweasels shortcode
        add_shortcode( 'streamweasels', array($this, 'get_streamweasels_shortcode') );
        add_shortcode( 'sw-twitch', array($this, 'get_streamweasels_shortcode') );
        add_shortcode( 'sw-twitch-integration', array($this, 'get_streamweasels_shortcode') );
        add_shortcode( 'sw-twitch-embed', array($this, 'get_streamweasels_shortcode_embed') );
    }

    public function get_streamweasels_shortcode_embed( $args ) {
        // random 4-digit number is needed when multiple shortcodes on one page
        $uuid = rand( 1000, 9999 );
        $host = ( isset( $_SERVER['HTTP_HOST'] ) ? esc_js( wp_unslash( $_SERVER['HTTP_HOST'] ) ) : '' );
        // Sanitize inputs and escape for HTML attributes or JS as appropriate
        $width = ( !empty( $args['width'] ) ? esc_attr( sanitize_text_field( $args['width'] ) ) : '100%' );
        $height = ( !empty( $args['height'] ) ? esc_attr( sanitize_text_field( $args['height'] ) ) : '100%' );
        $embed_chat = esc_attr( sanitize_text_field( $args['embed-chat'] ?? 'video' ) );
        // Treat autoplay and muted as boolean values, not text
        $autoplay = ( !empty( $args['autoplay'] ) && ($args['autoplay'] === 'true' || $args['autoplay'] === '1') ? 'true' : 'false' );
        $muted = ( !empty( $args['muted'] ) && ($args['muted'] === 'true' || $args['muted'] === '1') ? 'true' : 'false' );
        $channel = esc_attr( sanitize_text_field( $args['channel'] ?? 'monstercat' ) );
        $theme = esc_attr( sanitize_text_field( $args['theme'] ?? 'dark' ) );
        $output = '<div id="sw-twitch-embed-' . esc_attr( $uuid ) . '" style="aspect-ratio:16/9"></div>';
        $output .= '<script type="text/javascript">
		window.addEventListener("DOMContentLoaded", (event) => {
			new Twitch.Embed("sw-twitch-embed-' . esc_js( $uuid ) . '", {
				width: "' . $width . '",
				height: "' . $height . '",
				layout: "' . $embed_chat . '",
				autoplay: ' . $autoplay . ',
				muted: ' . $muted . ',
				channel: "' . $channel . '",
				theme: "' . $theme . '",
				parent: ["' . esc_js( $host ) . '"]
			});
		});
		</script>';
        return $output;
    }

    public function get_streamweasels_shortcode( $args ) {
        // random 4-digit number is needed when multiple shortcodes on one page
        $uuid = rand( 1000, 9999 );
        $options = get_option( 'swti_options' );
        $layout = sanitize_text_field( $args['layout'] ?? $options['swti_layout'] ?? '' );
        // Call streamweasels_content to set the inline scripts
        $this->streamweasels_content( $args, $uuid );
        // check the desired layout and return HTML
        ob_start();
        if ( $layout == 'wall' ) {
            include plugin_dir_path( __FILE__ ) . 'partials/streamweasels-wall-public-display.php';
        } else {
            if ( $layout == 'player' ) {
                include plugin_dir_path( __FILE__ ) . 'partials/streamweasels-player-public-display.php';
            } else {
                if ( $layout == 'rail' ) {
                    include plugin_dir_path( __FILE__ ) . 'partials/streamweasels-rail-public-display.php';
                } else {
                    if ( $layout == 'status' ) {
                        include plugin_dir_path( __FILE__ ) . 'partials/streamweasels-status-public-display.php';
                    } else {
                        if ( $layout == 'vods' ) {
                            include plugin_dir_path( __FILE__ ) . 'partials/streamweasels-vods-public-display.php';
                        }
                    }
                }
            }
        }
        if ( !$layout || $layout == '' ) {
            include 'partials/streamweasels-public-display.php';
        }
        return ob_get_clean();
    }

    public function streamweasels_content( $args, $uuid ) {
        $options = get_option( 'swti_options' );
        $translations = get_option( 'swti_translations' );
        $optionsFeature = get_option( 'swti_options_feature' );
        $optionsStatus = get_option( 'swti_options_status' );
        $optionsNav = get_option( 'swti_options_nav' );
        if ( empty( $args['game'] ) && empty( $args['channels'] ) && empty( $args['channel'] ) && empty( $args['team'] ) ) {
            $gameName = wp_kses( $options['swti_game'] ?? '', 'post' );
            $channels = sanitize_text_field( $options['swti_channels'] ?? '' );
            $team = sanitize_text_field( $options['swti_team'] ?? '' );
            $titleFilter = sanitize_text_field( $options['swti_title_filter'] ?? '' );
        } else {
            $gameName = wp_kses( $args['game'] ?? '', 'post' );
            $channels = sanitize_text_field( $args['channel'] ?? $args['channels'] ?? '' );
            $team = sanitize_text_field( $args['team'] ?? '' );
            $titleFilter = sanitize_text_field( $args['title-filter'] ?? '' );
        }
        $language = sanitize_text_field( $args['language'] ?? $options['swti_language'] ?? '' );
        $layout = sanitize_text_field( $args['layout'] ?? $options['swti_layout'] ?? '' );
        $limit = sanitize_text_field( $args['limit'] ?? $options['swti_limit'] ?? '' );
        $freeEmbed = 'page';
        if ( sti_fs()->can_use_premium_code() == false ) {
            if ( $limit > 15 ) {
                $limit = 15;
            }
        }
        // Translations
        $translationsLive = sanitize_text_field( ( $translations['swti_translations_live'] ?? 'live' ?: 'live' ) );
        $translationsOffline = sanitize_text_field( ( $translations['swti_translations_offline'] ?? 'offline' ?: 'offline' ) );
        $translationsViewers = sanitize_text_field( ( $translations['swti_translations_viewers'] ?? 'viewers' ?: 'viewers' ) );
        $translationsStreaming = sanitize_text_field( ( $translations['swti_translations_streaming'] ?? 'streaming' ?: 'streaming' ) );
        $translationsFor = sanitize_text_field( ( $translations['swti_translations_for'] ?? 'for' ?: 'for' ) );
        if ( $layout == 'rail' || $layout == 'feature' ) {
            $hoverColour = '';
        }
        if ( $layout == 'feature' ) {
            $embedPosition = ( isset( $optionsFeature['swti_feature_embed_position'] ) ? $optionsFeature['swti_feature_embed_position'] : 'inside' );
            $embedPosition = ( isset( $args['feature-embed-position'] ) ? $args['feature-embed-position'] : $embedPosition );
            if ( $embedPosition == 'inside' ) {
                $embed = 'inside';
                $freeEmbed = 'inside';
            }
        }
        $offlineAddonCheck = 0;
        if ( $layout == 'status' ) {
            $freeEmbed = 'twitch';
            $offlineAddonCheck = 1;
        }
        if ( $layout == 'nav' ) {
            $offlineAddonCheck = 1;
        }
        $SWTI_Twitch_API = new SWTI_Twitch_API();
        if ( isset( $gameName ) && !empty( $gameName ) ) {
            $game = $SWTI_Twitch_API->get_game_id( $gameName );
        } else {
            $game = '';
        }
        if ( !empty( $team ) ) {
            $channels = $SWTI_Twitch_API->get_team_channels( $team );
        }
        // For block themes, register a dummy script to allow inline scripts to be added
        if ( !wp_script_is( $this->plugin_name, 'registered' ) ) {
            wp_register_script( $this->plugin_name . '-blocks', '' );
            wp_enqueue_script( $this->plugin_name . '-blocks' );
            $inlineScriptHandle = $this->plugin_name . '-blocks';
        } else {
            $inlineScriptHandle = $this->plugin_name;
        }
        wp_add_inline_script( $inlineScriptHandle, 'const streamWeaselsVars' . $uuid . ' = ' . json_encode( array(
            'gameName'              => esc_attr( $gameName ),
            'gameid'                => esc_attr( $game ),
            'channels'              => esc_attr( $channels ),
            'team'                  => esc_attr( $team ),
            'titleFilter'           => esc_attr( $titleFilter ),
            'limit'                 => (int) $limit,
            'language'              => esc_attr( $language ),
            'layout'                => esc_attr( $layout ),
            'embed'                 => esc_attr( $freeEmbed ),
            'embedTheme'            => 'light',
            'embedChat'             => 0,
            'embedTitle'            => 0,
            'embedTitlePosition'    => '',
            'embedMuted'            => 0,
            'showOffline'           => (int) $offlineAddonCheck,
            'showOfflineText'       => __( 'No Streams Online!', 'swti' ),
            'showOfflineImage'      => '',
            'autoplay'              => 0,
            'autoplayOffline'       => 0,
            'autoplaySelect'        => '',
            'featured'              => '',
            'title'                 => '',
            'subtitle'              => '',
            'offlineImage'          => '',
            'logoImage'             => '',
            'profileImage'          => '',
            'logoBgColour'          => '',
            'logoBorderColour'      => '',
            'maxWidth'              => '1440',
            'tileLayout'            => 'detailed',
            'tileSorting'           => 'most',
            'tileLive'              => 'viewer',
            'tileBgColour'          => '',
            'tileTitleColour'       => '',
            'tileSubtitleColour'    => '',
            'tileRoundedCorners'    => '',
            'hoverColour'           => '',
            'refresh'               => '',
            'disableScroll'         => 0,
            'translationsLive'      => wp_kses_post( $translationsLive ),
            'translationsOffline'   => wp_kses_post( $translationsOffline ),
            'translationsViewers'   => wp_kses_post( $translationsViewers ),
            'translationsStreaming' => wp_kses_post( $translationsStreaming ),
            'translationsFor'       => wp_kses_post( $translationsFor ),
        ) ) . ';', 'before' );
    }

    public function swti_status_show_global() {
        $options = get_option( 'swti_options' );
        $optionsStatus = get_option( 'swti_options_status' );
        $gameName = wp_kses( ( isset( $options['swti_game'] ) ? $options['swti_game'] : '' ), 'post' );
        $channels = sanitize_text_field( $options['swti_channels'] ?? '' );
        $team = sanitize_text_field( $options['swti_team'] ?? '' );
        $showGlobal = sanitize_text_field( $optionsStatus['swti_status_show_global'] ?? '0' );
        if ( $showGlobal ) {
            echo do_shortcode( '[sw-twitch layout="status" channels="' . esc_attr( $channels ) . '" game="' . esc_attr( $gameName ) . '" team="' . esc_attr( $team ) . '" status-placement="absolute"]' );
        }
    }

    public function show_menu_item_desc( $title, $item ) {
        if ( is_object( $item ) && isset( $item->ID ) ) {
            $channel_status = get_post_meta( $item->ID, '_channel_status', true );
            $team_status = get_post_meta( $item->ID, '_team_status', true );
            if ( !empty( $channel_status ) ) {
                add_action( 'wp_footer', function () use($channel_status) {
                    $this->swti_nav_show_global_channel( $channel_status );
                } );
            }
            if ( !empty( $team_status ) ) {
                add_action( 'wp_footer', function () use($team_status) {
                    $this->swti_nav_show_global_team( $team_status );
                } );
            }
        }
        return $title;
    }

    public function swti_nav_show_global_channel( $channel_status ) {
        $options = get_option( 'swti_options' );
        $optionsNav = get_option( 'swti_options_nav' );
        if ( sti_fs()->is_plan_or_trial( 'premium', true ) || sti_fs()->is_plan_or_trial( 'pro', true ) ) {
            echo do_shortcode( '[streamweasels layout="nav" channels="' . esc_attr( $channel_status ) . '" show-offline="1" show-offline-text="" show-offline-image=""]' );
        }
    }

    public function swti_nav_show_global_team( $team_status ) {
        $options = get_option( 'swti_options' );
        $optionsNav = get_option( 'swti_options_nav' );
        if ( sti_fs()->is_plan_or_trial( 'premium', true ) || sti_fs()->is_plan_or_trial( 'pro', true ) ) {
            echo do_shortcode( '[streamweasels layout="nav" team="' . esc_attr( $team_status ) . '" show-offline="1" show-offline-text="" show-offline-image=""]' );
        }
    }

}
