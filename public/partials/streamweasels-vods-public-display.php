<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://www.streamweasels.com
 * @since      1.0.0
 *
 * @package    Streamweasels_Vods_Pro
 * @subpackage Streamweasels_Vods_Pro/public/partials
 */
?>

<?php
$options            = get_option('swti_options');
$layout             = sanitize_text_field($args['layout'] ?? $options['swti_layout']);
$optionsVods        = get_option('swti_options_vods');
$vodsChannel = sanitize_text_field($args['vods-channel'] ?? $args['channel'] ?? $args['channels'] ?? $options['swti_channels'] ?? $optionsVods['swti_vods_channel'] ?? '');

if (isset($vodsChannel) && !empty($vodsChannel)) {
    $channels = explode(',', $vodsChannel);
    $SWTI_VODS_Twitch_API = new SWTI_Twitch_API();
    if (count($channels) > 1) {
        $vodsChannelId = $SWTI_VODS_Twitch_API->get_channel_ids($channels);
    } else {
        $vodsChannelId = $SWTI_VODS_Twitch_API->get_channel_id($channels[0]);
    }
} else {
    $vodsChannelId = '23161357';
}
$clipType = sanitize_text_field($args['vods-clip-type'] ?? $optionsVods['swti_vods_clip_type'] ?? 'clips');
$creatorFilter = sanitize_text_field($args['vods-creator-filter'] ?? $optionsVods['swti_vods_creator_filter'] ?? '');
$clipPeriod = sanitize_text_field($args['vods-clip-period'] ?? $optionsVods['swti_vods_clip_period'] ?? 'all');
if (sti_fs()->can_use_premium_code()) {
    $tileLayout         = sanitize_text_field($args['tile-layout'] ?? $options['swti_tile_layout'] ?? '');
    $hoverEffect        = sanitize_text_field($args['hover-effect'] ?? $options['swti_hover_effect'] ?? '');
    $title              = sanitize_text_field($args['title'] ?? $options['swti_title'] ?? '');
    $subtitle           = sanitize_text_field($args['subtitle'] ?? $options['swti_subtitle'] ?? '');
    $embedTitlePosition = sanitize_text_field($args['title-position'] ?? $options['swti_embed_title_position'] ?? '');    
    $showTitleTop       = ($embedTitlePosition == 'top' ? '<div class="cp-streamweasels__title"></div>' : '');
    $showTitleBottom    = ($embedTitlePosition == 'bottom' ? '<div class="cp-streamweasels__title"></div>' : '');
    $maxWidth           = sanitize_text_field($args['max-width'] ?? $options['swti_max_width'] ?? '');
} else {
    $creatorFilter      = '';
    $tileLayout         = 'detailed';
    $hoverEffect        = 'none';
    $title 				= '';
    $subtitle 			= '';
    $embedTitlePosition = '';
    $showTitleTop       = '';
    $showTitleBottom    = '';
    $maxWidth           = '1440';
}

$titleMarkup = $subtitleMarkup = '';
if ($title !== '') {
    $titleMarkup = '<h2 class="cp-streamweasels__heading">'.esc_html($title).'</h2>';
}
if ($subtitle !== '') {
    $subtitleMarkup = '<h3 class="cp-streamweasels__subheading">'.esc_html($subtitle).'</h3>';
}
?>

<div class="cp-streamweasels cp-streamweasels--<?php echo esc_attr($uuid); ?> cp-streamweasels--<?php echo esc_attr($layout); ?>" data-uuid="<?php echo esc_attr($uuid); ?>" data-vod-type="<?php echo esc_attr($clipType); ?>" data-vod-channel="<?php echo esc_attr($vodsChannelId); ?>" data-vod-period="<?php echo esc_attr($clipPeriod); ?>" data-vod-creator-filter="<?php echo esc_attr($creatorFilter); ?>" data-online="0">
    <div class="cp-streamweasels__inner" style="<?php echo ($maxWidth !== 'none') ? 'max-width:'.esc_attr($maxWidth).'px' : ''; ?>">
        <?php echo wp_kses_post($titleMarkup); ?>
        <?php echo wp_kses_post($subtitleMarkup); ?>            
        <div class="cp-streamweasels__loader">
            <div class="spinner-item"></div>
            <div class="spinner-item"></div>
            <div class="spinner-item"></div>
            <div class="spinner-item"></div>
            <div class="spinner-item"></div>
        </div>
        <?php echo wp_kses_post($showTitleTop); ?>
        <div class="cp-streamweasels__player"></div>
        <?php echo wp_kses_post($showTitleBottom); ?>
        <div class="cp-streamweasels__offline-wrapper"></div>
        <div class="cp-streamweasels__streams cp-streamweasels--<?php echo esc_attr($tileLayout); ?> cp-streamweasels--hover-<?php echo esc_attr($hoverEffect); ?>"></div>
    </div>
</div>