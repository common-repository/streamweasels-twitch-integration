<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://www.streamweasels.com
 * @since      1.0.0
 *
 * @package    Streamweasels
 * @subpackage Streamweasels/public/partials
 */
?>

<p>
    <?php
        esc_html_e( 'StreamWeasels Twitch Integration layout not set! Please make sure you have a layout selected on the Twitch Integration settings page or defined on your StreamWeasels shortcode like this:', 'swti' );
        echo '<br><code>[sw-twitch layout="wall"]</code>';
    ?>
</p>