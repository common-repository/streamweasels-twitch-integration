<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://www.streamweasels.com
 * @since      1.0.0
 *
 * @package    Streamweasels_Rail_Pro
 * @subpackage Streamweasels_Rail_Pro/public/partials
 */
?>

<?php
$options = get_option('swti_options');
$layout = sanitize_text_field($args['layout'] ?? $options['swti_layout']);
if (sti_fs()->can_use_premium_code()) {
    $tileLayout         = sanitize_text_field($args['tile-layout'] ?? $options['swti_tile_layout']);
    $hoverEffect        = sanitize_text_field($args['hover-effect'] ?? $options['swti_hover_effect']);
    $logoImage          = esc_url_raw($options['swti_logo_image'] ?? '');
    $title              = sanitize_text_field($args['title'] ?? $options['swti_title']);
    $subtitle           = sanitize_text_field($args['subtitle'] ?? $options['swti_subtitle']);
    $embedChat          = sanitize_text_field($args['embed-chat'] ?? $options['swti_embed_chat']);
    $embedTitlePosition = sanitize_text_field($args['title-position'] ?? $options['swti_embed_title_position']);    
    $showTitleTop       = ($embedTitlePosition == 'top' ? '<div class="cp-streamweasels__title"></div>' : '');
    $showTitleBottom    = ($embedTitlePosition == 'bottom' ? '<div class="cp-streamweasels__title"></div>' : '');
    $maxWidth           = sanitize_text_field($args['max-width'] ?? $options['swti_max_width']);
    $headerMarkup       = '';
    if ($logoImage || $title || $subtitle) {
        $headerMarkup = '<div class="cp-streamweasels__rail-header">'.
            ($logoImage ? '<div class="cp-streamweasels__rail-header-image"><img src="'.esc_url($logoImage).'"></div>' : '').
            '<div class="cp-streamweasels__rail-header-title">'.
                ($title ? '<span class="cp-streamweasels__rail-header-title--line-1">'.esc_html($title).'</span>' : '').
                ($subtitle ? '<span class="cp-streamweasels__rail-header-title--line-2">'.esc_html($subtitle).'</span>' : '').'
            </div>
        </div>';
    }
} else {
    $tileLayout         = 'detailed';
    $hoverEffect        = 'play';
    $logoImage          = '';
    $title 				= '';
    $subtitle 			= '';
    $embedChat          = 0;
    $embedTitlePosition = '';
    $showTitleTop       = '';
    $showTitleBottom    = '';
    $maxWidth           = '1440';
    $headerMarkup       = '';
}
?>

<div class="cp-streamweasels cp-streamweasels--<?php echo esc_attr($uuid); ?> cp-streamweasels--<?php echo esc_attr($layout); ?>" data-uuid="<?php echo esc_attr($uuid); ?>">
    <div class="cp-streamweasels__inner" style="<?php echo ($maxWidth !== 'none') ? 'max-width:' . esc_attr($maxWidth) . 'px' : ''; ?>">
        <?php echo wp_kses_post($showTitleTop); ?>
        <div class="cp-streamweasels__player cp-streamweasels__player--<?php echo ($embedChat ? 'video-with-chat' : 'video'); ?>"></div>
        <?php echo wp_kses_post($showTitleBottom); ?>
        <?php echo wp_kses_post($headerMarkup); ?>
        <div class="cp-streamweasels__loader">
            <div class="spinner-item"></div>
            <div class="spinner-item"></div>
            <div class="spinner-item"></div>
            <div class="spinner-item"></div>
            <div class="spinner-item"></div>
        </div>
        <div class="cp-streamweasels__offline-wrapper"></div>
        <div class="cp-streamweasels__streams cp-streamweasels__streams--<?php echo esc_attr($tileLayout); ?> cp-streamweasels__streams--hover-<?php echo esc_attr($hoverEffect); ?>"></div>
    </div>
</div>