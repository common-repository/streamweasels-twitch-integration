<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://www.streamweasels.com
 * @since      1.0.0
 *
 * @package    Streamweasels
 * @subpackage Streamweasels/admin/partials
 */
?>

<?php 
switch ( get_admin_page_title() ) {
    case '[Layout] Wall':
        $activePage = 'wall';
        break;
    case '[Layout] Player':
        $activePage = 'player';
        break;
    case '[Layout] Rail':
        $activePage = 'rail';
        break;
    case '[Layout] Feature':
        $activePage = 'feature';
        break;
    case '[Layout] Status':
        $activePage = 'status';
        break;
    case '[Layout] Nav':
        $activePage = 'nav';
        break;
    case '[Layout] Vods':
        $activePage = 'vods';
        break;
    case '[Layout] Showcase':
        $activePage = 'showcase';
        break;
    default:
        $activePage = 'wall';
}
?>
<div class="cp-streamweasels wrap">
    <div class="cp-streamweasels__header">
        <div class="cp-streamweasels__header-logo">
            <img src="<?php 
echo esc_url( plugin_dir_url( __FILE__ ) . '../img/sw-full-logo.png' );
?>">
        </div>
        <div class="cp-streamweasels__header-title">
            <h3>StreamWeasels</h3>
            <p>Twitch Integration <?php 
?>for WordPress</p>
        </div>        
    </div>
    <div class="cp-streamweasels__wrapper">
    <div id="poststuff">
        <div id="post-body" class="metabox-holder columns-2">
            <div id="post-body-content">
                <div class="meta-box-sortables ui-sortable">
                    <div class="postbox">
                        <div class="inside">
                            <div class="setup-instructions">
                                <div class="setup-instructions--left">
                                    <h3>Setup Guide</h3>
                                    <p>StreamWeasels plugins now use the <strong>new Twitch API (Helix)</strong>, this unlocks new possibilities, better performance and more reliability!</p>
                                    <h4>Twitch Auth Token</h4>
                                    <p>The new Twitch API requires a valid Twitch Authentication Token sent with every request, in order to pull data from Twitch. In order to get your own authentication token, you must follow a few steps. This process takes only a few minutes and is fairly simple.</p>
                                    <p>To create your token, you can follow along with our text guide: <a href="https://support.streamweasels.com/article/12-how-to-setup-a-client-id-and-client-secret" target="_blank">How to create a Twitch Authentication Token</a>.</p>
                                    <p>You can also check out our instructional video on the <a href="https://www.youtube.com/channel/UCo885jUiOeyhtHDFUbdx8rQ" target="_blank">StreamWeasels YouTube</a>.</p>                  
                                    <h4>StreamWeasels Blocks</h4>
                                    <p>If your site uses the Block Editor (Gutenberg) you can add our Twitch Blocks directly to your page. Look out for the Twitch Integration Block and the Twitch Embed Block, and learn more in our <a href="https://support.streamweasels.com/article/77-twitch-integration-blocks-guide" target="_blank">Twitch Integration Blocks Guide</a>.</p>
                                    <h4>StreamWeasels Shortcodes</h4>
                                    <p>You can simply use the shortcode <code>[sw-twitch]</code> to display your Twitch Integration on your page, using all the settings set here on this page. Learn more in our <a href="https://support.streamweasels.com/article/8-twitch-integration-shortcode-guide" target="_blank">Twitch Integration Shortcodes Guide</a></p> 
                                    <h4>Advanced Shortcodes</h4>
                                    <p>For more complicated integrations, for example if you have more than one Twitch Integration on your site, you can use shortcode attributes to change the settings directly on your shortcode.<br><br><strong>For example</strong>:<br><br>
                                    <?php 
if ( $activePage == 'vods' ) {
    ?>
                                        <code>[sw-twitch layout="<?php 
    echo esc_attr( $activePage );
    ?>" channel="lirik"]</code></p>
                                    <?php 
} else {
    ?>
                                        <code>[sw-twitch layout="<?php 
    echo esc_attr( $activePage );
    ?>" channels="lirik,shroud,sodapoppin" autoplay="1"]</code></p>
                                    <?php 
}
?>
                                    <p>The complete list of shortcode attributes can be viewed in our <a href="https://support.streamweasels.com/article/8-twitch-integration-shortcode-guide" target="_blank">Twitch Integration Shortcode Guide</a>.</p>
                                </div>
                                <div class="setup-instructions--right">
                                    <h3>Video Guide</h3>
                                    <iframe width="560" height="315" src="https://www.youtube.com/embed/JK06TumS6ho" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                                </div>
                            </div>
                        </div>
                    </div>     
                    <form id="sw-form" method="post" action="options.php">
                        <?php 
if ( get_admin_page_title() == 'StreamWeasels' ) {
    ?>
                            <?php 
    settings_fields( 'swti_options' );
    ?>
                            <?php 
    $this->swti_do_settings_sections(
        'swti_api_fields',
        'dashicons-twitch',
        'This plugin requires an active Twitch Auth Token to work. <a href="https://support.streamweasels.com/article/12-how-to-setup-a-client-id-and-client-secret" target="_blank">Click here</a> to learn more about Twitch Auth Tokens.',
        'free'
    );
    ?>
                            <?php 
    $this->swti_do_settings_sections(
        'swti_shortcode_fields',
        'dashicons-shortcode',
        'You can add Twitch Integration to your page with either Twitch Integration <a href="https://support.streamweasels.com/article/77-twitch-integration-blocks-guide" target="_blank">Blocks</a> or <a href="https://support.streamweasels.com/article/8-twitch-integration-shortcode-guide" target="_blank">Shortcodes</a>. For shortcodes, simply use the shortcode <code>[sw-twitch]</code> for your Twitch Integration. For more complicated integrations you can change the attributes directly on your shortcode. <a href="https://support.streamweasels.com/article/8-twitch-integration-shortcode-guide" target="_blank">Click here</a> to view our full list of StreamWeasels shortcode attributes.',
        'free'
    );
    ?>
                            <?php 
    $this->swti_do_settings_sections(
        'swti_main_fields',
        'dashicons-slides',
        'Here you can define the channels to display in your Twitch integration.',
        'free'
    );
    ?>
                            <?php 
    $this->swti_do_settings_sections(
        'swti_layout_fields',
        'dashicons-slides',
        'Here you can select the layout of your Twitch integration. Take a look at our <a href="https://support.streamweasels.com/article/78-twitch-integration-layout-guide" target="_blank">StreamWeasels Layout Guide</a> for more information and our free and PRO layouts.',
        'free'
    );
    ?>
                            <?php 
    $this->swti_do_settings_sections(
        'swti_embed_fields',
        'dashicons-video-alt3',
        'Here you can change the settings for the Twitch embed in your Twitch integration.',
        'pro'
    );
    ?>
                            <?php 
    $this->swti_do_settings_sections(
        'swti_offline_fields',
        'dashicons-slides',
        'Here you can change the settings for the offline channels in your Twitch integration.',
        'pro'
    );
    ?>
                            <?php 
    $this->swti_do_settings_sections(
        'swti_autoplay_fields',
        'dashicons-video-alt3',
        'Here you can change the settings for the autoplay in your Twitch Integration.',
        'pro'
    );
    ?>
                            <?php 
    $this->swti_do_settings_sections(
        'swti_appearance_fields',
        'dashicons-admin-appearance',
        'Here you can change the overall appearance of your Twitch integration.',
        'pro'
    );
    ?>
                            <?php 
    $this->swti_do_settings_sections(
        'swti_tile_fields',
        'dashicons-grid-view',
        'Here you can change the finer appearance details of your Twitch integration. ',
        'pro'
    );
    ?>
                            <?php 
    $this->swti_do_settings_sections(
        'swti_hover_fields',
        'dashicons-search',
        'Here you can change what happens when you hover over channels in your Twitch integration.',
        'pro'
    );
    ?>
                            <?php 
    $this->swti_do_settings_sections(
        'swti_refresh_fields',
        'dashicons-rotate',
        'Here you can configure the auto-refresh settings for your Twitch Integration. Enable this to have your Twitch Integration (currently only works with Wall layout) refresh every 60 seconds.',
        'pro'
    );
    ?>
                            <?php 
    $this->swti_do_settings_sections(
        'swti_debug_fields',
        'dashicons-admin-tools',
        'If your StreamWeasels plugin is encountering errors with the Twitch API, those errors will be output below. You can get in touch with us <a href="https://www.streamweasels.com/contact/" target="_blank">here</a>, please include a copy of any errors that might be relevant from below.',
        'free'
    );
    ?>
                        <?php 
}
?>
                        <?php 
if ( get_admin_page_title() == 'Translations' ) {
    ?>
                            <?php 
    settings_fields( 'swti_translations' );
    ?>
                            <?php 
    $this->swti_do_settings_sections(
        'swti_translations_fields',
        'dashicons-translation',
        'This page allows you to translate strings found within the StreamWeasels plugins.',
        'free'
    );
    ?>
                        <?php 
}
?>                         
                        <?php 
if ( get_admin_page_title() == '[Layout] Wall' ) {
    ?>
                            <?php 
    settings_fields( 'swti_options_wall' );
    ?>
                            <?php 
    $this->swti_do_settings_sections(
        'swti_wall_shortcode_fields',
        'dashicons-twitch',
        'You can simply use the shortcode <span class="advanced-shortcode">[sw-twitch layout="wall"]</span> for your Twitch Integration. For more complicated integrations you can change the settings directly on your shortcode. <a href="https://support.streamweasels.com/article/8-twitch-integration-shortcode-guide" target="_blank">Click here</a> to learn more about StreamWeasels shortcodes.',
        'free'
    );
    ?>
                            <?php 
    $this->swti_do_settings_sections(
        'swti_wall_fields',
        'dashicons-twitch',
        'Custom fields for your Twitch Integration [Layout] Wall.',
        'free'
    );
    ?>
                        <?php 
}
?>                        
                        <?php 
if ( get_admin_page_title() == '[Layout] Player' ) {
    ?>
                            <?php 
    settings_fields( 'swti_options_player' );
    ?>
                            <?php 
    $this->swti_do_settings_sections(
        'swti_player_shortcode_fields',
        'dashicons-twitch',
        'You can simply use the shortcode <span class="advanced-shortcode">[sw-twitch layout="player"]</span> for your Twitch Integration. For more complicated integrations you can change the settings directly on your shortcode. <a href="https://support.streamweasels.com/article/8-twitch-integration-shortcode-guide" target="_blank">Click here</a> to learn more about StreamWeasels shortcodes.',
        'free'
    );
    ?>
                            <?php 
    $this->swti_do_settings_sections(
        'swti_player_fields',
        'dashicons-twitch',
        'Custom fields for your Twitch Integration [Layout] Player.',
        'free'
    );
    ?>
                        <?php 
}
?>
                        <?php 
if ( get_admin_page_title() == '[Layout] Rail' ) {
    ?>
                            <?php 
    settings_fields( 'swti_options_rail' );
    ?>
                            <?php 
    $this->swti_do_settings_sections(
        'swti_rail_shortcode_fields',
        'dashicons-twitch',
        'You can simply use the shortcode <span class="advanced-shortcode">[sw-twitch layout="rail"]</span> for your Twitch Integration. For more complicated integrations you can change the settings directly on your shortcode. <a href="https://support.streamweasels.com/article/8-twitch-integration-shortcode-guide" target="_blank">Click here</a> to learn more about StreamWeasels shortcodes.',
        'free'
    );
    ?>
                            <?php 
    $this->swti_do_settings_sections(
        'swti_rail_fields',
        'dashicons-twitch',
        'Custom fields for your Twitch Integration [Layout] Rail.',
        'free'
    );
    ?>
                        <?php 
}
?>  
                        <?php 
if ( get_admin_page_title() == '[Layout] Feature' ) {
    ?>
                            <?php 
    settings_fields( 'swti_options_feature' );
    ?>
                            <?php 
    $this->swti_do_settings_sections(
        'swti_feature_shortcode_fields',
        'dashicons-twitch',
        'You can simply use the shortcode <span class="advanced-shortcode">[sw-twitch layout="feature"]</span> for your Twitch Integration. For more complicated integrations you can change the settings directly on your shortcode. <a href="https://support.streamweasels.com/article/8-twitch-integration-shortcode-guide" target="_blank">Click here</a> to learn more about StreamWeasels shortcodes.',
        'free'
    );
    ?>
                            <?php 
    $this->swti_do_settings_sections(
        'swti_feature_fields',
        'dashicons-twitch',
        'Custom fields for your Twitch Integration [Layout] Feature.',
        'free'
    );
    ?>
                        <?php 
}
?>    
                        <?php 
if ( get_admin_page_title() == '[Layout] Status' ) {
    ?>
                            <?php 
    settings_fields( 'swti_options_status' );
    ?>
                            <?php 
    $this->swti_do_settings_sections(
        'swti_status_shortcode_fields',
        'dashicons-twitch',
        'You can simply use the shortcode <span class="advanced-shortcode">[sw-twitch layout="status"]</span> for your Twitch Integration. For more complicated integrations you can change the settings directly on your shortcode. <a href="https://support.streamweasels.com/article/8-twitch-integration-shortcode-guide" target="_blank">Click here</a> to learn more about StreamWeasels shortcodes.',
        'free'
    );
    ?>
                            <?php 
    $this->swti_do_settings_sections(
        'swti_status_placement_fields',
        'dashicons-twitch',
        'Custom fields for your Twitch Integration [Layout] Status.',
        'free'
    );
    ?>
                            <?php 
    $this->swti_do_settings_sections(
        'swti_status_appearance_fields',
        'dashicons-twitch',
        'Custom fields for your Twitch Integration [Layout] Status.',
        'free'
    );
    ?>
                            <?php 
    $this->swti_do_settings_sections(
        'swti_status_classic_fields',
        'dashicons-twitch',
        'Custom fields for your Twitch Integration [Layout] Status.',
        'free'
    );
    ?>
                        <?php 
}
?> 
                        <?php 
if ( get_admin_page_title() == '[Layout] Nav' ) {
    ?>
                            <?php 
    settings_fields( 'swti_options_nav' );
    ?>
                            <?php 
    $this->swti_do_settings_sections(
        'swti_nav_shortcode_fields',
        'dashicons-twitch',
        'There is no need to use a shortcode for this plugin. To get Twitch Nav to show on your page, navigate to <code>Appearance -> Menus</code>, look for Twitch Nav and add either a Channel Status or Team Status (or both!) to your navigation.',
        'free'
    );
    ?>
                            <?php 
    $this->swti_do_settings_sections(
        'swti_nav_fields',
        'dashicons-twitch',
        'Custom fields for your Twitch Integration [Layout] Nav.',
        'free'
    );
    ?>
                        <?php 
}
?>
                        <?php 
if ( get_admin_page_title() == '[Layout] Vods' ) {
    ?>
                            <?php 
    settings_fields( 'swti_options_vods' );
    ?>
                            <?php 
    $this->swti_do_settings_sections(
        'swti_vods_shortcode_fields',
        'dashicons-twitch',
        'You can simply use the shortcode <span class="advanced-shortcode">[sw-twitch layout="vods"]</span> for your Twitch Integration. For more complicated integrations you can change the settings directly on your shortcode. <a href="https://support.streamweasels.com/article/8-twitch-integration-shortcode-guide" target="_blank">Click here</a> to learn more about StreamWeasels shortcodes.',
        'free'
    );
    ?>
                            <?php 
    $this->swti_do_settings_sections(
        'swti_vods_fields',
        'dashicons-twitch',
        'Custom fields for your Twitch Integration [Layout] Vods.',
        'free'
    );
    ?>
                        <?php 
}
?> 
                        <?php 
if ( get_admin_page_title() == '[Layout] Showcase' ) {
    ?>
                            <?php 
    settings_fields( 'swti_options_showcase' );
    ?>
                            <?php 
    $this->swti_do_settings_sections(
        'swti_showcase_shortcode_fields',
        'dashicons-twitch',
        'You can simply use the shortcode <span class="advanced-shortcode">[sw-twitch layout="showcase"]</span> for your Twitch Integration. For more complicated integrations you can change the settings directly on your shortcode. <a href="https://support.streamweasels.com/article/8-twitch-integration-shortcode-guide" target="_blank">Click here</a> to learn more about StreamWeasels shortcodes.',
        'free'
    );
    ?>
                            <?php 
    $this->swti_do_settings_sections(
        'swti_showcase_fields',
        'dashicons-twitch',
        'Custom fields for your Twitch Integration [Layout] Showcase.',
        'free'
    );
    ?>
                        <?php 
}
?>                                                                                                                                             
                    </form>
                </div>
            </div>
            <div id="postbox-container-1" class="postbox-container">
                <?php 
include 'streamweasels-admin-sidebar.php';
?>
            </div>
        </div>
    </div>
</div>