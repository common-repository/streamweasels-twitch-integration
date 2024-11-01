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

class Streamweasels_Player_Admin extends Streamweasels_Admin {

	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->options = get_option('swti_options_player', array());				
	}

	/**
	 * Register the admin page.
	 *
	 * @since    1.0.0
	 */
	public function display_admin_page_player() {

		add_submenu_page(
			'streamweasels',
			'[Layout] Player',
			'[Layout] Player',
			'manage_options',
			'streamweasels-player',
			array($this, 'swti_showAdmin')
		);			

		$tooltipArray = array(
			'Welcome Background Colour'=> 'Welcome Background Colour <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="player-welcome-bg-colour=\'\'"></span>',
			'Welcome Image'=> 'Welcome Background Image <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="player-welcome-image=\'\'"></span>',
			'Welcome Logo'=> 'Welcome Logo <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="player-welcome-logo=\'\'"></span>',
			'Welcome Text'=> 'Welcome Text <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="player-welcome-text=\'\'"></span>',
			'Welcome Text 2'=> 'Welcome Text 2 <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="player-welcome-text-2=\'\'"></span>',
			'Welcome Text Colour'=> 'Welcome Text Colour <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="player-welcome-text-colour=\'\'"></span>',
			'Stream List Position'=> 'Stream List Position <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="player-stream-list-position=\'\'"></span>',
		);		

		register_setting( 'swti_options_player', 'swti_options_player', array($this, 'swti_options_validate'));	
		add_settings_section('swti_player_shortcode_settings', 'Shortcode', false, 'swti_player_shortcode_fields');		
		add_settings_section('swti_player_settings', '[Layout] Player Settings', false, 'swti_player_fields');
		add_settings_field('swti_player_welcome_bg_colour', $tooltipArray['Welcome Background Colour'], array($this, 'streamweasels_player_welcome_bg_colour'), 'swti_player_fields', 'swti_player_settings');	
		add_settings_field('swti_player_welcome_logo', $tooltipArray['Welcome Logo'], array($this, 'streamweasels_player_welcome_logo_cb'), 'swti_player_fields', 'swti_player_settings');	
		add_settings_field('swti_player_welcome_image', $tooltipArray['Welcome Image'], array($this, 'streamweasels_player_welcome_image_cb'), 'swti_player_fields', 'swti_player_settings');	
		add_settings_field('swti_player_welcome_text', $tooltipArray['Welcome Text'], array($this, 'streamweasels_player_welcome_text_cb'), 'swti_player_fields', 'swti_player_settings');	
		add_settings_field('swti_player_welcome_text_2', $tooltipArray['Welcome Text 2'], array($this, 'streamweasels_player_welcome_text_2_cb'), 'swti_player_fields', 'swti_player_settings');	
		add_settings_field('swti_player_welcome_text_colour', $tooltipArray['Welcome Text Colour'], array($this, 'streamweasels_player_welcome_text_colour_cb'), 'swti_player_fields', 'swti_player_settings');	
		add_settings_field('swti_player_stream_position', $tooltipArray['Stream List Position'], array($this, 'streamweasels_player_stream_position_cb'), 'swti_player_fields', 'swti_player_settings');	
	}

	public function swti_showAdmin() {
		include ('partials/streamweasels-admin-display.php');
	}		

	public function streamweasels_player_welcome_bg_colour() {
		$welcomeBgColour = ( isset ( $this->options['swti_player_welcome_bg_colour'] ) ) ? $this->options['swti_player_welcome_bg_colour'] : '';
		?>
		
		<input type="text" id="sw-welcome-bg-colour" name="swti_options_player[swti_player_welcome_bg_colour]" size='40' value="<?php echo esc_html($welcomeBgColour); ?>" />

		<p>Choose the background colour of the [Layout] Player.</p>

		<?php
	}	

	public function streamweasels_player_welcome_image_cb() {
		$welcomeImage = ( isset ( $this->options['swti_player_welcome_image'] ) ) ? $this->options['swti_player_welcome_image'] : '';
		?>
		
		<input type="text" id="sw-welcome-image" name="swti_options_player[swti_player_welcome_image]" size='40' value="<?php echo esc_html($welcomeImage); ?>" />
        <input type="button" name="upload-btn" class="upload-btn button-secondary" value="Upload Image">
		<p>Choose to display a welcome background image of the [Layout] Player. Ideal image dimensions are 900 x 480.</p>

		<?php
	}

	public function streamweasels_player_welcome_logo_cb() {
		$welcomeLogo = ( isset ( $this->options['swti_player_welcome_logo'] ) ) ? $this->options['swti_player_welcome_logo'] : '';
		?>
		
		<input type="text" id="sw-welcome-logo" name="swti_options_player[swti_player_welcome_logo]" size='40' value="<?php echo esc_html($welcomeLogo); ?>" />
        <input type="button" name="upload-btn" class="upload-btn button-secondary" value="Upload Image">
		<p>Choose to display a welcome logo inside your [Layout] Player. Ideal image dimensions are 100 x 100.</p>

		<?php
	}	

	public function streamweasels_player_welcome_text_cb() {
		$welcomeText = ( isset ( $this->options['swti_player_welcome_text'] ) ) ? $this->options['swti_player_welcome_text'] : '';
		?>
		
		<input type="text" id="sw-welcome-text" name="swti_options_player[swti_player_welcome_text]" size='40' value="<?php echo esc_html($welcomeText); ?>" />

		<p>Choose the welcome text (line 1) of the [Layout] Player.</p>

		<?php
	}

	public function streamweasels_player_welcome_text_2_cb() {
		$welcomeText2 = ( isset ( $this->options['swti_player_welcome_text_2'] ) ) ? $this->options['swti_player_welcome_text_2'] : '';
		?>
		
		<input type="text" id="sw-welcome-text-2" name="swti_options_player[swti_player_welcome_text_2]" size='40' value="<?php echo esc_html($welcomeText2); ?>" />

		<p>Choose the welcome text (line 2) of the [Layout] Player.</p>

		<?php
	}	

	public function streamweasels_player_welcome_text_colour_cb() {
		$welcomeTextColour = ( isset ( $this->options['swti_player_welcome_text_colour'] ) ) ? $this->options['swti_player_welcome_text_colour'] : '';
		?>
		
		<input type="text" id="sw-welcome-text-colour" name="swti_options_player[swti_player_welcome_text_colour]" size='40' value="<?php echo esc_html($welcomeTextColour); ?>" />

		<p>Choose the welcome text colour of the [Layout] Player.</p>

		<?php
	}	

	public function streamweasels_player_stream_position_cb() {
		$position = ( isset ( $this->options['swti_player_stream_position'] ) ) ? $this->options['swti_player_stream_position'] : '';
		?>
		
		<select id="sw-player-stream-position" name="swti_options_player[swti_player_stream_position]">
            <option value="left" <?php echo selected( $position, 'left', false ); ?>>Left</option>
            <option value="right" <?php echo selected( $position, 'right', false ); ?>>Right</option>
			<option value="none" <?php echo selected( $position, 'none', false ); ?>>None</option>
        </select>
		<p>Choose the position of the list of streamers in your [Layout] Player.</p>

		<?php
	}

	public function swti_options_validate($input) {

		$new_input = [];
		$options = get_option('swti_options_player');

		if( isset( $input['swti_player_welcome_bg_colour'] ) ) {
			$new_input['swti_player_welcome_bg_colour'] = sanitize_text_field( $input['swti_player_welcome_bg_colour'] );
		}

		if( isset( $input['swti_player_welcome_logo'] ) ) {
			$new_input['swti_player_welcome_logo'] = sanitize_text_field( $input['swti_player_welcome_logo'] );
		}		

		if( isset( $input['swti_player_welcome_image'] ) ) {
			$new_input['swti_player_welcome_image'] = sanitize_text_field( $input['swti_player_welcome_image'] );
		}	

		if( isset( $input['swti_player_welcome_text'] ) ) {
			$new_input['swti_player_welcome_text'] = sanitize_text_field( $input['swti_player_welcome_text'] );
		}

		if( isset( $input['swti_player_welcome_text_2'] ) ) {
			$new_input['swti_player_welcome_text_2'] = sanitize_text_field( $input['swti_player_welcome_text_2'] );
		}		
		
		if( isset( $input['swti_player_welcome_text_colour'] ) ) {
			$new_input['swti_player_welcome_text_colour'] = sanitize_text_field( $input['swti_player_welcome_text_colour'] );
		}		
		
		if( isset( $input['swti_player_stream_position'] ) ) {
			$new_input['swti_player_stream_position'] = sanitize_text_field( $input['swti_player_stream_position'] );
		}			

		return $new_input;
	}	

	public function swti_twitch_layout_options_pro( $options ) {

		$options['player'] = 'Player';
	
		return $options;
	}	
}