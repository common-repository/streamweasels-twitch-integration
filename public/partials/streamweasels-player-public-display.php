<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://www.streamweasels.com
 * @since      1.0.0
 *
 * @package    Streamweasels_Player_Pro
 * @subpackage Streamweasels_Player_Pro/public/partials
 */
?>

<?php
$options            = get_option('swti_options');
$optionsPlayer      = get_option('swti_options_player');
$layout             = sanitize_text_field($args['layout'] ?? $options['swti_layout']);
$welcomeBgColour    = sanitize_hex_color($args['player-welcome-bg-colour'] ?? $optionsPlayer['swti_player_welcome_bg_colour'] ?? '#fff');
$welcomeLogo        = esc_url_raw($args['player-welcome-logo'] ?? $optionsPlayer['swti_player_welcome_logo'] ?? '');
$welcomeImage       = esc_url_raw($args['player-welcome-image'] ?? $optionsPlayer['swti_player_welcome_image'] ?? '');
$welcomeText        = sanitize_text_field($args['player-welcome-text'] ?? $optionsPlayer['swti_player_welcome_text'] ?? '');
$welcomeText2       = sanitize_text_field($args['player-welcome-text-2'] ?? $optionsPlayer['swti_player_welcome_text_2'] ?? '');
$welcomeTextColour  = sanitize_hex_color($args['player-welcome-text-colour'] ?? $optionsPlayer['swti_player_welcome_text_colour'] ?? '');
$playerStreamPos    = sanitize_text_field($args['player-stream-list-position'] ?? $optionsPlayer['swti_player_stream_position'] ?? 'left');
if (sti_fs()->can_use_premium_code()) {
    $tileLayout         = sanitize_text_field($args['tile-layout'] ?? $options['swti_tile_layout']);
    $hoverEffect        = sanitize_text_field($args['hover-effect'] ?? $options['swti_hover_effect']);
    $title              = sanitize_text_field($args['title'] ?? $options['swti_title']);
    $subtitle           = sanitize_text_field($args['subtitle'] ?? $options['swti_subtitle']);
    $embedChat          = sanitize_text_field($args['embed-chat'] ?? $options['swti_embed_chat']);
    $embedTitlePosition = sanitize_text_field($args['title-position'] ?? $options['swti_embed_title_position']);    
    $showTitleTop       = ($embedTitlePosition == 'top' ? '<div class="cp-streamweasels__title"></div>' : '');
    $showTitleBottom    = ($embedTitlePosition == 'bottom' ? '<div class="cp-streamweasels__title"></div>' : '');
    $maxWidth           = sanitize_text_field($args['max-width'] ?? $options['swti_max_width']);
} else {
    $tileLayout         = 'detailed';
    $hoverEffect        = 'none';
    $title 				= '';
    $subtitle 			= '';
    $embedChat          = 0;
    $embedTitlePosition = '';
    $showTitleTop       = '';
    $showTitleBottom    = '';
    $maxWidth           = '1440';
}

$showStreamsLeft = (($playerStreamPos == '' || $playerStreamPos == 'none' || $playerStreamPos == 'left') ? '<div class="cp-streamweasels__streams cp-streamweasels__streams--'.esc_attr($tileLayout).' cp-streamweasels__streams--hover-'.esc_attr($hoverEffect).' cp-streamweasels__streams--position-'.esc_attr($playerStreamPos).'"></div>' : '');
$showStreamsRight = ($playerStreamPos == 'right' ? '<div class="cp-streamweasels__streams cp-streamweasels__streams--'.esc_attr($tileLayout).' cp-streamweasels__streams--hover-'.esc_attr($hoverEffect).' cp-streamweasels__streams--position-'.esc_attr($playerStreamPos).'"></div>' : '');

$welcomeMarkup = '';
$welcomeTitleMarkup = '';
$loadingMarkup = '';

if ($welcomeText !== '' || $welcomeText2 !== '') {
    $welcomeTitleMarkup =  '<div class="cp-streamweasels__welcome-text  cp-streamweasels__welcome-text--'.($welcomeLogo ? 'with-logo' : 'no-logo').'">'.
                                ($welcomeText ? '<p class="cp-streamweasels__welcome-text--line-1" style="color:'.esc_attr($welcomeTextColour).'">'.esc_html($welcomeText).'</p>' : ''). 
                                ($welcomeText2 ? '<p class="cp-streamweasels__welcome-text--line-2" style="color:'.esc_attr($welcomeTextColour).'">'.esc_html($welcomeText2).'</p>' : '').
                            '</div>';
}

$welcomeMarkup = '<div class="cp-streamweasels__welcome">'.
                    ($welcomeImage ? '<img src="'.esc_url($welcomeImage).'">' : '').
                    '<div class="cp-streamweasels__welcome-wrapper">'.
                        ($welcomeLogo ? '<img src="'.esc_url($welcomeLogo).'">' : '').
                        wp_kses_post($welcomeTitleMarkup).
                    '</div>'.
                '</div>';


if ($welcomeText == '' && $welcomeText2 == '' && $welcomeLogo == '') {
    $loadingMarkup =	'<div class="cp-streamweasels__loader">
                            <div class="spinner-item"></div>
                            <div class="spinner-item"></div>
                            <div class="spinner-item"></div>
                            <div class="spinner-item"></div>
                            <div class="spinner-item"></div>
                        </div>';
}
?>

<div class="cp-streamweasels cp-streamweasels--<?php echo esc_attr($uuid); ?> cp-streamweasels--<?php echo esc_attr($layout); ?>" data-uuid="<?php echo esc_attr($uuid); ?>">
    <div class="cp-streamweasels__inner" style="<?php echo ($maxWidth !== 'none') ? 'max-width:' . esc_attr($maxWidth) . 'px' : ''; ?>">
        <?php echo wp_kses_post($showTitleTop); ?>
        <div class="cp-streamweasels__inner-wrapper">
            <div class="cp-streamweasels__player-wrapper">
                <?php echo wp_kses_post($showStreamsLeft); ?>
                <div class="cp-streamweasels__player cp-streamweasels__player--position-<?php echo esc_attr($playerStreamPos); ?> cp-streamweasels__player--<?php echo (esc_attr($embedChat) ? 'video-with-chat' : 'video'); ?>" style="<?php echo ($welcomeBgColour) ? 'background-color:' . esc_attr($welcomeBgColour) . ';' : ''; ?>">
                    <?php echo wp_kses_post($loadingMarkup); ?>
                    <div class="cp-streamweasels__offline-wrapper">
                        <?php echo wp_kses_post($welcomeMarkup); ?>
                    </div>
                </div>
                <?php echo wp_kses_post($showStreamsRight); ?>
            </div>
        </div>
        <?php echo wp_kses_post($showTitleBottom); ?>
    </div>
</div>