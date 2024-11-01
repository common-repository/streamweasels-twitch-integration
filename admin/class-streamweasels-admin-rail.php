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

class Streamweasels_Rail_Admin extends Streamweasels_Admin {


	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->options = get_option( 'swti_options_rail', array() );		
	}

	/**
	 * Register the admin page.
	 *
	 * @since    1.0.0
	 */
	public function display_admin_page_rail() {

		add_submenu_page(
			'streamweasels',
			'[Layout] Rail',
			'[Layout] Rail',
			'manage_options',
			'streamweasels-rail',
			array($this, 'swti_showAdmin')
		);		

		$tooltipArray = array(
			'Controls Background Colour'=> 'Controls Background Colour <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="rail-controls-bg-colour=\'\'"></span>',
			'Controls Arrow Colour'=> 'Controls Arrow Colour <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="rail-controls-arrow-colour=\'\'"></span>',
			'Rail Border Colour'=> 'Rail Border Colour <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="rail-border-colour=\'\'"></span>',
		);			

		register_setting( 'swti_options_rail', 'swti_options_rail', array($this, 'swti_options_validate'));	
		add_settings_section('swti_rail_shortcode_settings', 'Shortcode', false, 'swti_rail_shortcode_fields');		
		add_settings_section('swti_rail_settings', '[Layout] Rail Settings', false, 'swti_rail_fields');
		add_settings_field('swti_rail_controls_bg_colour', $tooltipArray['Controls Background Colour'], array($this, 'swti_rail_controls_bg_colour_cb'), 'swti_rail_fields', 'swti_rail_settings');	
		add_settings_field('swti_rail_controls_arrow_colour', $tooltipArray['Controls Arrow Colour'], array($this, 'swti_rail_controls_arrow_colour_cb'), 'swti_rail_fields', 'swti_rail_settings');	
		add_settings_field('swti_rail_controls_border_colour', $tooltipArray['Rail Border Colour'], array($this, 'swti_rail_controls_border_colour_cb'), 'swti_rail_fields', 'swti_rail_settings');	
	}

	public function swti_showAdmin() {
		include ('partials/streamweasels-admin-display.php');
	}		

	public function swti_rail_controls_bg_colour_cb() {
		$controlsBgColour = ( isset ( $this->options['swti_rail_controls_bg_colour'] ) ) ? $this->options['swti_rail_controls_bg_colour'] : '';
		?>
		
		<input type="text" id="sw-rail-controls-bg-colour" name="swti_options_rail[swti_rail_controls_bg_colour]" size='40' value="<?php echo esc_html($controlsBgColour); ?>" />

		<p>Choose the background colour of the [Layout] Rail carousel controls.</p>

		<?php
	}

	public function swti_rail_controls_arrow_colour_cb() {
		$controlsArrowColour = ( isset ( $this->options['swti_rail_controls_arrow_colour'] ) ) ? $this->options['swti_rail_controls_arrow_colour'] : '';
		?>
		
		<input type="text" id="sw-rail-controls-arrow-colour" name="swti_options_rail[swti_rail_controls_arrow_colour]" size='40' value="<?php echo esc_html($controlsArrowColour); ?>" />

		<p>Choose the arrow colour of the [Layout] Rail carousel controls.</p>

		<?php
	}
	
	public function swti_rail_controls_border_colour_cb() {
		$railBorderColour = ( isset ( $this->options['swti_rail_border_colour'] ) ) ? $this->options['swti_rail_border_colour'] : '';
		?>
		
		<input type="text" id="sw-rail-border-colour" name="swti_options_rail[swti_rail_border_colour]" size='40' value="<?php echo esc_html($railBorderColour); ?>" />

		<p>Choose the border colour of the [Layout] Rail.</p>

		<?php
	}	

	public function swti_options_validate($input) {
		$new_input = [];
		$options = get_option('swti_options_rail');

		if( isset( $input['swti_rail_controls_bg_colour'] ) ) {
			$new_input['swti_rail_controls_bg_colour'] = sanitize_text_field( $input['swti_rail_controls_bg_colour'] );
		}
		
		if( isset( $input['swti_rail_controls_arrow_colour'] ) ) {
			$new_input['swti_rail_controls_arrow_colour'] = sanitize_text_field( $input['swti_rail_controls_arrow_colour'] );
		}
		
		if( isset( $input['swti_rail_border_colour'] ) ) {
			$new_input['swti_rail_border_colour'] = sanitize_text_field( $input['swti_rail_border_colour'] );
		}		

		return $new_input;
	}	


	public function swti_twitch_layout_options_pro( $options ) {
		$options['rail'] = 'Rail';
		return $options;
	}	
}