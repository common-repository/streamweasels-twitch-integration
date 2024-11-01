<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://www.streamweasels.com
 * @since      1.0.0
 *
 * @package    Streamweasels_Status_Pro
 * @subpackage Streamweasels_Status_Pro/public/partials
 */
?>

<?php

$options              = get_option('swti_options');
$layout               = sanitize_text_field($args['layout'] ?? $options['swti_layout']);
$optionsStatus        = get_option('swti_options_status');
$hideOffline          = sanitize_text_field($args['status-hide-offline'] ?? $optionsStatus['swti_status_hide_when_offline'] ?? '0');
$placement            = sanitize_text_field($args['status-placement'] ?? $optionsStatus['swti_status_placement'] ?? 'absolute');
$verticalPlacement    = sanitize_text_field($args['status-vertical-placement'] ?? $optionsStatus['swti_status_vertical_placement'] ?? 'top');
$horizontalPlacement  = sanitize_text_field($args['status-horizontal-placement'] ?? $optionsStatus['swti_status_horizontal_placement'] ?? 'left');
$customLogo           = esc_url_raw($args['status-custom-logo'] ?? $optionsStatus['swti_status_custom_logo'] ?? '');
$logoBackgroundColour = sanitize_hex_color($args['status-logo-background-colour'] ?? $optionsStatus['swti_status_logo_background_colour'] ?? '');
$disableCarousel      = sanitize_text_field($args['status-disable-carousel'] ?? $optionsStatus['swti_status_disable_carousel'] ?? '0');
$enableClassic        = sanitize_text_field($args['status-enable-classic'] ?? $optionsStatus['swti_status_enable_classic'] ?? '0');
$classicOnlineText    = sanitize_text_field($args['status-classic-online-text'] ?? $optionsStatus['swti_status_classic_online_text'] ?? 'Live Now! Click to View.');
$classicOfflineText   = sanitize_text_field($args['status-classic-offline-text'] ?? $optionsStatus['swti_status_classic_offline_text'] ?? 'Currently Offline.');


if (sti_fs()->can_use_premium_code()) {
    $tileLayout  = sanitize_text_field($args['tile-layout'] ?? $options['swti_tile_layout']);
    $embedChat   = sanitize_text_field($args['embed-chat'] ?? $options['swti_embed_chat']);
    $hoverEffect = sanitize_text_field($args['hover-effect'] ?? $options['swti_hover_effect']);
} else {
    $tileLayout         = 'compact';
    $embedChat          = 0;
    $hoverEffect        = 'none';
}
?>

<div class="cp-streamweasels cp-streamweasels--<?php echo esc_attr($uuid); ?> cp-streamweasels--<?php echo esc_attr($layout); ?> cp-streamweasels--hover-<?php echo esc_attr($hoverEffect); ?> cp-streamweasels--placement-<?php echo esc_attr($placement); ?> cp-streamweasels--position-<?php echo esc_attr($verticalPlacement); ?> cp-streamweasels--position-<?php echo esc_attr($horizontalPlacement); ?> cp-streamweasels--hide-<?php echo esc_attr($hideOffline); ?>" data-uuid="<?php echo esc_attr($uuid); ?>" data-enable-classic="<?php echo esc_attr($enableClassic); ?>" data-classic-online-text="<?php echo esc_attr($classicOnlineText); ?>" data-classic-offline-text="<?php echo esc_attr($classicOfflineText); ?>">
    <div class="cp-streamweasels__inner">
        <div class="cp-streamweasels__loader">
            <div class="spinner-item"></div>
            <div class="spinner-item"></div>
            <div class="spinner-item"></div>
            <div class="spinner-item"></div>
            <div class="spinner-item"></div>
        </div>
        <div class="cp-streamweasels__player cp-streamweasels__player--<?php echo esc_attr($embedChat ? 'video-with-chat' : 'video'); ?>"></div>
        <div class="cp-streamweasels__offline-wrapper"></div>
        <div class="cp-streamweasels__twitch-logo cp-streamweasels__twitch-logo--<?php echo esc_attr(!$customLogo ? 'twitch' : 'custom'); ?>" style="background-color:<?php echo esc_attr($logoBackgroundColour); ?>">
            <?php if ($customLogo) : ?>
                <img src="<?php echo esc_url($customLogo); ?>">
            <?php endif; ?>
        </div>
        <div class="cp-streamweasels__streams cp-streamweasels__streams--<?php echo esc_attr($tileLayout); ?> cp-streamweasels__streams--hover-<?php echo esc_attr($hoverEffect); ?> cp-streamweasels__streams--carousel-<?php echo esc_attr($disableCarousel); ?>"></div>
    </div>
</div>