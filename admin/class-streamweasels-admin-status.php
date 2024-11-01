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

class Streamweasels_Status_Admin extends Streamweasels_Admin {

	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->options = get_option( 'swti_options_status', array() );
	}

	public function display_admin_page_status() {

		add_submenu_page(
			'streamweasels',
			'[Layout] Status',
			'[Layout] Status',
			'manage_options',
			'streamweasels-status',
			array($this, 'swti_showAdmin')
		);		

		$tooltipArray = array(
			'Show Global'=>'Show on Every Page',
			'Hide Offline'=>'Hide Offline <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="status-hide-offline=\'\'"></span>',
            'Placement'=>'Placement <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="status-placement=\'\'"></span>',
			'Vertical Placement'=>'Vertical Placement <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="status-vertical-placement=\'\'"></span>',
			'Horizontal Placement'=>'Horizontal Placement <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="status-horizontal-placement=\'\'"></span>',
			'Vertical Distance'=>'Vertical Distance <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="status-vertical-distance=\'\'"></span>',
			'Horizontal Distance'=>'Horizontal Distance <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="status-horizontal-distance=\'\'"></span>',
			'Custom Logo'=>'Custom Logo <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="status-custom-logo=\'\'"></span>',
			'Logo Background Colour'=>'Logo Background Colour <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="status-logo-background-colour=\'\'"></span>',
			'Accent Colour'=>'Accent Colour <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="status-accent-colour=\'\'"></span>',
			'Carousel Background Colour'=>'Controls Background Colour <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="status-carousel-background-colour=\'\'"></span>',
			'Carousel Arrow Colour'=>'Controls Arrow Colour <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="status-carousel-arrow-colour=\'\'"></span>',
			'Disable Carousel'=>'Disable Carousel <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="status-disable-carousel=\'\'"></span>',
			'Enable Classic'=>'Enable Classic <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="status-enable-classic=\'\'"></span>',
			'Classic Online Text'=>'Classic Online Text <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="status-classic-online-text=\'\'"></span>',
			'Classic Offline Text'=>'Classic Offline Text <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="status-classic-offine-text=\'\'"></span>',
		);		

		register_setting( 'swti_options_status', 'swti_options_status', array($this, 'sswti_options_validate'));	
		add_settings_section('swti_status_placement_settings', '[Layout] Status Placement Settings', false, 'swti_status_placement_fields');
        add_settings_section('swti_status_appearance_settings', '[Layout] Status Apearance Settings', false, 'swti_status_appearance_fields');
		add_settings_section('swti_status_classic_settings', '[Layout] Status Classic Settings', false, 'swti_status_classic_fields');
		add_settings_section('swti_status_shortcode_settings', 'Shortcode', false, 'swti_status_shortcode_fields');
		add_settings_field('swti_status_show_global', $tooltipArray['Show Global'], array($this, 'swti_status_show_global_cb'), 'swti_status_placement_fields', 'swti_status_placement_settings');				
		add_settings_field('swti_status_hide_when_offline', $tooltipArray['Hide Offline'], array($this, 'swti_status_hide_when_offline_cb'), 'swti_status_placement_fields', 'swti_status_placement_settings');				
        add_settings_field('swti_status_placement', $tooltipArray['Placement'], array($this, 'swti_status_placement_cb'), 'swti_status_placement_fields', 'swti_status_placement_settings');			
		add_settings_field('swti_status_vertical_placement', $tooltipArray['Vertical Placement'], array($this, 'swti_status_vertical_placement_cb'), 'swti_status_placement_fields', 'swti_status_placement_settings');			
		add_settings_field('swti_status_horizontal_placement', $tooltipArray['Horizontal Placement'], array($this, 'swti_status_horizontal_placement_cb'), 'swti_status_placement_fields', 'swti_status_placement_settings');	
		add_settings_field('swti_status_vertical_distance', $tooltipArray['Vertical Distance'], array($this, 'swti_status_vertical_distance_cb'), 'swti_status_placement_fields', 'swti_status_placement_settings');	
		add_settings_field('swti_status_horizontal_distance', $tooltipArray['Horizontal Distance'], array($this, 'swti_status_horizontal_distance_cb'), 'swti_status_placement_fields', 'swti_status_placement_settings');	
		add_settings_field('swti_status_custom_logo', $tooltipArray['Custom Logo'], array($this, 'swti_status_custom_logo_cb'), 'swti_status_appearance_fields', 'swti_status_appearance_settings');						
		add_settings_field('swti_status_logo_background_colour', $tooltipArray['Logo Background Colour'], array($this, 'swti_status_logo_background_colour_cb'), 'swti_status_appearance_fields', 'swti_status_appearance_settings');	
		add_settings_field('swti_status_accent_colour', $tooltipArray['Accent Colour'], array($this, 'swti_status_accent_colour_cb'), 'swti_status_appearance_fields', 'swti_status_appearance_settings');							
		add_settings_field('swti_status_carousel_background_colour', $tooltipArray['Carousel Background Colour'], array($this, 'swti_status_carousel_background_colour_cb'), 'swti_status_appearance_fields', 'swti_status_appearance_settings');							
		add_settings_field('swti_status_carousel_arrow_colour', $tooltipArray['Carousel Arrow Colour'], array($this, 'swti_status_carousel_arrow_colour_cb'), 'swti_status_appearance_fields', 'swti_status_appearance_settings');							
		add_settings_field('swti_status_disable_carousel', $tooltipArray['Disable Carousel'], array($this, 'swti_status_disable_carousel_cb'), 'swti_status_appearance_fields', 'swti_status_appearance_settings');				
		add_settings_field('swti_status_enable_classic', $tooltipArray['Enable Classic'], array($this, 'swti_status_enable_classic_cb'), 'swti_status_classic_fields', 'swti_status_classic_settings');				
		add_settings_field('swti_status_classic_online_text', $tooltipArray['Classic Online Text'], array($this, 'swti_status_classic_online_text_cb'), 'swti_status_classic_fields', 'swti_status_classic_settings');				
		add_settings_field('swti_status_classic_offline_text', $tooltipArray['Classic Offline Text'], array($this, 'swti_status_classic_offline_text_cb'), 'swti_status_classic_fields', 'swti_status_classic_settings');				

	}
	
	public function swti_status_show_global_cb() {
		$showGlobal = ( isset ( $this->options['swti_status_show_global'] ) ) ? $this->options['swti_status_show_global'] : '';
		?>
		
		<input type="hidden" name="swti_options_status[swti_status_show_global]" value="0"/>
		<input type="checkbox" id="sw-show-global" name="swti_options_status[swti_status_show_global]" value="1" <?php checked( 1, $showGlobal, true ); ?>/>
		<p>Choose to display Stream Status on every page, without the use of a shortcode.</p>
		<p>When this is set, Stream Status will display the streams configured in the Twitch Integration <a href="/wp-admin/admin.php?page=streamweasels">settings page</a>.</p>

		<?php
	}

	public function swti_status_hide_when_offline_cb() {
		$hideWhenOffline = ( isset ( $this->options['swti_status_hide_when_offline'] ) ) ? $this->options['swti_status_hide_when_offline'] : '';
		?>
		
		<input type="hidden" name="swti_options_status[swti_status_hide_when_offline]" value="0"/>
		<input type="checkbox" id="sw-hide-offline" name="swti_options_status[swti_status_hide_when_offline]" value="1" <?php checked( 1, $hideWhenOffline, true ); ?>/>
		<p>Choose to hide the Stream Status entirely if no user is online.</p>

		<?php
	}

	public function swti_status_placement_cb() {
		$placement = ( isset ( $this->options['swti_status_placement'] ) ) ? $this->options['swti_status_placement'] : 'top';
		?>
		
		<select id="sw-placement" name="swti_options_status[swti_status_placement]">
			<option value="absolute" <?php echo selected( $placement, 'absolute', false ); ?>>Absolute</option>	
            <option value="static" <?php echo selected( $placement, 'static', false ); ?>>Static</option>
        </select>
		<p>Choose if you want your Status to appear in the corner of the window (Absolute) or stay where it is (Static). Static only works when Stream Status is placed with a block or shortcode.</p>

		<?php
	}

	public function swti_status_vertical_placement_cb() {
		$verticalPlacement = ( isset ( $this->options['swti_status_vertical_placement'] ) ) ? $this->options['swti_status_vertical_placement'] : 'top';
		?>
		
		<select id="sw-vertical-placement" name="swti_options_status[swti_status_vertical_placement]">
			<option value="top" <?php echo selected( $verticalPlacement, 'top', false ); ?>>Top</option>	
            <option value="bottom" <?php echo selected( $verticalPlacement, 'bottom', false ); ?>>Bottom</option>
        </select>
		<p>Choose where you want your Stream Status to display.</p>

		<?php
	}

	public function swti_status_horizontal_placement_cb() {
		$horizontalPlacement = ( isset ( $this->options['swti_status_horizontal_placement'] ) ) ? $this->options['swti_status_horizontal_placement'] : 'left';
		?>
		
		<select id="sw-horizontal-placement" name="swti_options_status[swti_status_horizontal_placement]">
			<option value="left" <?php echo selected( $horizontalPlacement, 'left', false ); ?>>Left</option>	
            <option value="right" <?php echo selected( $horizontalPlacement, 'right', false ); ?>>Right</option>
        </select>	
		<p>Choose where you want your Stream Status to display.</p>

		<?php
	}
	
	public function swti_status_vertical_distance_cb() {
		$verticalDistance = ( isset ( $this->options['swti_status_vertical_distance'] ) ) ? $this->options['swti_status_vertical_distance'] : '';
		?>
		
		<input type="text" id="sw-vertical-distance" name="swti_options_status[swti_status_vertical_distance]" size='40' placeholder="25" value="<?php echo esc_html($verticalDistance); ?>" />
		<p>Choose the distance (in pixels) from the top/bottom. Defaults to 25.</p>

		<?php
	}
	
	public function swti_status_horizontal_distance_cb() {
		$horizontalDistance = ( isset ( $this->options['swti_status_horizontal_distance'] ) ) ? $this->options['swti_status_horizontal_distance'] : '';
		?>
		
		<input type="text" id="sw-horizontal-distance" name="swti_options_status[swti_status_horizontal_distance]" size='40' placeholder="25" value="<?php echo esc_html($horizontalDistance); ?>" />
		<p>Choose the distance (in pixels) from the top/bottom. Defaults to 25.</p>

		<?php
	}	

	public function swti_status_custom_logo_cb() {
		$customLogo = ( isset ( $this->options['swti_status_custom_logo'] ) ) ? $this->options['swti_status_custom_logo'] : '';
		?>
		
		<input type="text" id="sw-custom-logo" name="swti_options_status[swti_status_custom_logo]" size='40' value="<?php echo esc_html($customLogo); ?>" />
        <input type="button" name="upload-btn" class="upload-btn button-secondary" value="Upload Image">
		<p>Choose a custom logo, to replace the Twitch logo in the Stream Status.</p>

		<?php
	}	

	public function swti_status_logo_background_colour_cb() {
		$logoBackgroundColour = ( isset ( $this->options['swti_status_logo_background_colour'] ) ) ? $this->options['swti_status_logo_background_colour'] : '';
		?>
		
		<input type="text" id="sw-logo-background-colour" name="swti_options_status[swti_status_logo_background_colour]" size='40' value="<?php echo esc_html($logoBackgroundColour); ?>" />
		<p>Change the colour of the background of the logo box.</p>

		<?php
	}
	
	public function swti_status_accent_colour_cb() {
		$accentColour = ( isset ( $this->options['swti_status_accent_colour'] ) ) ? $this->options['swti_status_accent_colour'] : '';
		?>
		
		<input type="text" id="sw-accent-colour" name="swti_options_status[swti_status_accent_colour]" size='40' value="<?php echo esc_html($accentColour); ?>" />
		<p>Change the accent colour of the Stream Status.</p>

		<?php
	}

	public function swti_status_carousel_background_colour_cb() {
		$carouselBackgroundColour = ( isset ( $this->options['swti_status_carousel_background_colour'] ) ) ? $this->options['swti_status_carousel_background_colour'] : '';
		?>
		
		<input type="text" id="sw-carousel-background-colour" name="swti_options_status[swti_status_carousel_background_colour]" size='40' value="<?php echo esc_html($carouselBackgroundColour); ?>" />
		<p>Change the background colour of the carousel controls.</p>

		<?php
	}	
	
	public function swti_status_carousel_arrow_colour_cb() {
		$carouselArrowColour = ( isset ( $this->options['swti_status_carousel_arrow_colour'] ) ) ? $this->options['swti_status_carousel_arrow_colour'] : '';
		?>
		
		<input type="text" id="sw-carousel-arrow-colour" name="swti_options_status[swti_status_carousel_arrow_colour]" size='40' value="<?php echo esc_html($carouselArrowColour); ?>" />
		<p>Change the arrow colour of the carousel controls.</p>

		<?php
	}

	public function swti_status_disable_carousel_cb() {
		$disableCarousel = ( isset ( $this->options['swti_status_disable_carousel'] ) ) ? $this->options['swti_status_disable_carousel'] : '';
		?>
		
		<input type="hidden" name="swti_options_status[swti_status_disable_carousel]" value="0"/>
		<input type="checkbox" id="sw-disable-carousel" name="swti_options_status[swti_status_disable_carousel]" value="1" <?php checked( 1, $disableCarousel, true ); ?>/>
		<p>Choose to disable carousel functionality. This will remove the left/right arrows and make Stream Status only ever display one stream.</p>

		<?php
	}

	public function swti_status_enable_classic_cb() {
		$enableClassic = ( isset ( $this->options['swti_status_enable_classic'] ) ) ? $this->options['swti_status_enable_classic'] : '';
		?>
		
		<input type="hidden" name="swti_options_status[swti_status_enable_classic]" value="0"/>
		<input type="checkbox" id="sw-hide-offline" name="swti_options_status[swti_status_enable_classic]" value="1" <?php checked( 1, $enableClassic, true ); ?>/>
		<p>Choose to enable Classic mode for Stream Status, which simply displays a message if the user is online or offline.</p>

		<?php
	}	

	public function swti_status_classic_online_text_cb() {
		$classicOnlineText = ( isset ( $this->options['swti_status_classic_online_text'] ) ) ? $this->options['swti_status_classic_online_text'] : '';
		?>
		
		<input type="text" id="sw-classic-online-text" name="swti_options_status[swti_status_classic_online_text]" size='40' placeholder="example: Live Now! Click to View." value="<?php echo esc_html($classicOnlineText); ?>" />
		<p>If classic mode is enabled, enter text to use when the user is online.</p>

		<?php
	}
	
	public function swti_status_classic_offline_text_cb() {
		$classicOfflineText = ( isset ( $this->options['swti_status_classic_offline_text'] ) ) ? $this->options['swti_status_classic_offline_text'] : '';
		?>
		
		<input type="text" id="sw-classic-offline-text" name="swti_options_status[swti_status_classic_offline_text]" size='40' placeholder="example: Currently Offline." value="<?php echo esc_html($classicOfflineText); ?>" />
		<p>If classic mode is enabled, enter text to use when the user is offline.</p>

		<?php
	}	

	

	public function sswti_options_validate($input) {
		$new_input = [];
		$options = get_option('swti_options_status');

		if( isset( $input['swti_status_show_global'] ) ) {
			$new_input['swti_status_show_global'] = (int) $input['swti_status_show_global'];
		}
		
		if( isset( $input['swti_status_hide_when_offline'] ) ) {
			$new_input['swti_status_hide_when_offline'] = (int) $input['swti_status_hide_when_offline'];
		}

		if( isset( $input['swti_status_vertical_placement'] ) ) {
			$new_input['swti_status_vertical_placement'] = sanitize_text_field( $input['swti_status_vertical_placement'] );
		}	

		if( isset( $input['swti_status_placement'] ) ) {
			$new_input['swti_status_placement'] = sanitize_text_field( $input['swti_status_placement'] );
		}        

		if( isset( $input['swti_status_horizontal_placement'] ) ) {
			$new_input['swti_status_horizontal_placement'] = sanitize_text_field( $input['swti_status_horizontal_placement'] );
		}

		if( isset( $input['swti_status_vertical_distance'] ) ) {
			$new_input['swti_status_vertical_distance'] = sanitize_text_field( $input['swti_status_vertical_distance'] );
		}
		
		if( isset( $input['swti_status_horizontal_distance'] ) ) {
			$new_input['swti_status_horizontal_distance'] = sanitize_text_field( $input['swti_status_horizontal_distance'] );
		}		

		if( isset( $input['swti_status_custom_logo'] ) ) {
			$new_input['swti_status_custom_logo'] = sanitize_text_field( $input['swti_status_custom_logo'] );
		}		

		if( isset( $input['swti_status_logo_background_colour'] ) ) {
			$new_input['swti_status_logo_background_colour'] = sanitize_text_field( $input['swti_status_logo_background_colour'] );
		}
		
		if( isset( $input['swti_status_accent_colour'] ) ) {
			$new_input['swti_status_accent_colour'] = sanitize_text_field( $input['swti_status_accent_colour'] );
		}
		
		if( isset( $input['swti_status_carousel_background_colour'] ) ) {
			$new_input['swti_status_carousel_background_colour'] = sanitize_text_field( $input['swti_status_carousel_background_colour'] );
		}

		if( isset( $input['swti_status_carousel_arrow_colour'] ) ) {
			$new_input['swti_status_carousel_arrow_colour'] = sanitize_text_field( $input['swti_status_carousel_arrow_colour'] );
		}

		if( isset( $input['swti_status_enable_classic'] ) ) {
			$new_input['swti_status_enable_classic'] = (int) $input['swti_status_enable_classic'];
		}

		if( isset( $input['swti_status_disable_carousel'] ) ) {
			$new_input['swti_status_disable_carousel'] = (int) $input['swti_status_disable_carousel'];
		}
		
		if( isset( $input['swti_status_classic_online_text'] ) ) {
			$new_input['swti_status_classic_online_text'] = sanitize_text_field( $input['swti_status_classic_online_text'] );
		}
		
		if( isset( $input['swti_status_classic_offline_text'] ) ) {
			$new_input['swti_status_classic_offline_text'] = sanitize_text_field( $input['swti_status_classic_offline_text'] );
		}	

		return $new_input;
	}		

	public function swti_showAdmin() {
		include ('partials/streamweasels-admin-display.php');
	}	

	public function swti_twitch_layout_options_pro( $options ) {
		$options['status'] = 'Status';
		return $options;
	}
}