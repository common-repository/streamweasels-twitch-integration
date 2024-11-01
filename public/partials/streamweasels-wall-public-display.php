<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://www.streamweasels.com
 * @since      1.0.0
 *
 * @package    Streamweasels_Wall_Pro
 * @subpackage Streamweasels_Wall_Pro/public/partials
 */
?>

<?php

$options            = get_option('swti_options');
$layout             = sanitize_text_field($args['layout'] ?? $options['swti_layout'] ?? '');
$optionsWall        = get_option('swti_options_wall');
$tileColumnCount    = sanitize_text_field($args['wall-column-count'] ?? $optionsWall['swti_wall_column_count'] ?? '4');
$tileColumnSpacing  = sanitize_text_field($args['wall-column-spacing'] ?? $optionsWall['swti_wall_column_spacing'] ?? '10');
$titleMarkup        = '';
$subtitleMarkup     = '';
if (sti_fs()->can_use_premium_code()) {
    $tileLayout         = sanitize_text_field($args['tile-layout'] ?? $options['swti_tile_layout'] ?? '');
    $embedChat          = sanitize_text_field($args['embed-chat'] ?? $options['swti_embed_chat'] ?? '');
    $hoverEffect        = sanitize_text_field($args['hover-effect'] ?? $options['swti_hover_effect'] ?? '');
    $title              = sanitize_text_field($args['title'] ?? $options['swti_title'] ?? '');
    $subtitle           = sanitize_text_field($args['subtitle'] ?? $options['swti_subtitle'] ?? '');
    $embedTitlePosition = sanitize_text_field($args['title-position'] ?? $options['swti_embed_title_position'] ?? '');    
    $showTitleTop       = ($embedTitlePosition == 'top' ? '<div class="cp-streamweasels__title"></div>' : '');
    $showTitleBottom    = ($embedTitlePosition == 'bottom' ? '<div class="cp-streamweasels__title"></div>' : '');
    $maxWidth           = sanitize_text_field($args['max-width'] ?? $options['swti_max_width'] ?? '');
} else {
    $tileLayout         = 'detailed';
    $embedChat          = 0;
    $hoverEffect        = 'play';
    $title 				= '';
    $subtitle 			= '';
    $embedTitlePosition = '';
    $showTitleTop       = '';
    $showTitleBottom    = '';
    $maxWidth           = '1440';
}

if ($title !== '') {
    $titleMarkup = '<h2 class="cp-streamweasels__heading">'.esc_html($title).'</h2>';
}
if ($subtitle !== '') {
    $subtitleMarkup = '<h3 class="cp-streamweasels__subheading">'.esc_html($subtitle).'</h3>';
}
?>

<div class="cp-streamweasels cp-streamweasels--<?php echo esc_attr($uuid); ?> cp-streamweasels--<?php echo esc_attr($layout); ?>" data-uuid="<?php echo esc_attr($uuid); ?>" data-online-list="">
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
        <div class="cp-streamweasels__player cp-streamweasels__player--<?php echo esc_attr($embedChat ? 'video-with-chat' : 'video'); ?>"></div>
        <?php echo wp_kses_post($showTitleBottom); ?>
        <div class="cp-streamweasels__offline-wrapper"></div>
        <div class="cp-streamweasels__streams cp-streamweasels__streams--<?php echo esc_attr($tileLayout); ?> cp-streamweasels__streams--hover-<?php echo esc_attr($hoverEffect); ?>" style="<?php echo ($tileColumnSpacing) ? 'grid-gap:'.esc_attr($tileColumnSpacing).'px;' : ''; echo ($tileColumnCount) ? 'grid-template-columns: repeat('.esc_attr($tileColumnCount).', minmax(100px, 1fr));' : ''; ?>"></div>
    </div>
</div>
