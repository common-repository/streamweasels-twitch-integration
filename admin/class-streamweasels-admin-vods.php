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

class Streamweasels_Vods_Admin extends Streamweasels_Admin {

	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->options = get_option( 'swti_options_vods', array() );
	}

	public function display_admin_page_vods() {

		add_submenu_page(
			'streamweasels',
			'[Layout] Vods',
			'[Layout] Vods',
			'manage_options',
			'streamweasels-vods',
			array($this, 'swti_showAdmin')
		);		

		$tooltipArray = array(
			'Vods Channel'=>'Vods Channel <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="vods-channel=\'\'"></span>',
			'Creator Filter'=>'Creator Filter <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="creator-filter=\'\'"></span>',
			'Clip Type'=>'Clip Type <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="vods-clip-type=\'\'"></span>',
			'Clip Period'=>'Clip Period <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="vods-clip-period=\'\'"></span>',
            'Column Count'=>'Column Count <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="vods-column-count=\'\'"></span>',
            'Column Spacing'=>'Column Spacing <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="vods-column-spacing=\'\'"></span>'
		);		

		register_setting( 'swti_options_vods', 'swti_options_vods', array($this, 'swti_options_validate'));	
		add_settings_section('swti_vods_shortcode_settings', 'Shortcode', false, 'swti_vods_shortcode_fields');		
		add_settings_section('swti_vods_settings', '[Layout] Vods Settings', false, 'swti_vods_fields');
		add_settings_field('swti_vods_channel', $tooltipArray['Vods Channel'], array($this, 'swti_vods_channel_cb'), 'swti_vods_fields', 'swti_vods_settings');
		add_settings_field('swti_vods_creator_filter', $tooltipArray['Creator Filter'], array($this, 'swti_vods_creator_filter_cb'), 'swti_vods_fields', 'swti_vods_settings');
		add_settings_field('swti_vods_clip_type', $tooltipArray['Clip Type'], array($this, 'swti_vods_clip_type_cb'), 'swti_vods_fields', 'swti_vods_settings');
		add_settings_field('swti_vods_clip_period', $tooltipArray['Clip Period'], array($this, 'swti_vods_clip_period_cb'), 'swti_vods_fields', 'swti_vods_settings');
		add_settings_field('swti_vods_column_count', $tooltipArray['Column Count'], array($this, 'swti_vods_column_count_cb'), 'swti_vods_fields', 'swti_vods_settings');			
        add_settings_field('swti_vods_column_spacing', $tooltipArray['Column Spacing'], array($this, 'swti_vods_column_spacing_cb'), 'swti_vods_fields', 'swti_vods_settings');
	}

	public function swti_showAdmin() {
		include ('partials/streamweasels-admin-display.php');
	}	

	public function swti_twitch_layout_options_pro( $options ) {
		$options['vods'] = 'Vods';
		return $options;
	}	

	public function swti_vods_channel_cb() {
		$channels = ( isset ( $this->options['swti_vods_channel'] ) ) ? $this->options['swti_vods_channel'] : '';
		?>
		
		<div>
			<input type="text" id="sw-vods-channel" name="swti_options_vods[swti_vods_channel]" size='40' placeholder="example: monstercat" value="<?php echo esc_html($channels); ?>" />
			<?php if (!empty($channels)) { ?>
				<p class="sw-success"><span class="dashicons dashicons-yes-alt"></span>Channels: <?php echo (int)count(explode(',',$channels)); ?></p>
			<?php } ?>	
		</div>

		<p>Enter a single or a list of channel names, with each channel name seperated by a comma.</p>

		<?php
	}
	
	public function swti_vods_creator_filter_cb() {
		$creatorFilter = ( isset ( $this->options['swti_vods_creator_filter'] ) ) ? $this->options['swti_vods_creator_filter'] : '';
		?>
		
		<div>
			<input type="text" id="sw-vods-creator-filter" name="swti_options_vods[swti_vods_creator_filter]" size='40' placeholder="example: clipman101" value="<?php echo esc_html($creatorFilter); ?>" />			
		</div>

		<p>Enter a Twitch username and we will only only show clips created by that user. Seperate multiple usernames with a comma.</p>

		<?php
	}	

	public function swti_vods_clip_type_cb() {
		$clipType = ( isset ( $this->options['swti_vods_clip_type'] ) ) ? $this->options['swti_vods_clip_type'] : '';
		?>
		
		<select id="sw-vods-clip-type" name="swti_options_vods[swti_vods_clip_type]">
			<option value="clips" <?php echo selected( $clipType, 'clips', false ); ?>>Clips</option>
            <option value="highlights" <?php echo selected( $clipType, 'highlights', false ); ?>>Highlights</option>
            <option value="past-broadcasts" <?php echo selected( $clipType, 'past-broadcasts', false ); ?>>Past Broadcasts</option>
        </select>
		<p>Choose the type of VOD to display.</p>
		<?php
	}	

	public function swti_vods_clip_period_cb() {
		$clipPeriod = ( isset ( $this->options['swti_vods_clip_period'] ) ) ? $this->options['swti_vods_clip_period'] : '';
		?>
		
		<select id="sw-vods-clip-period" name="swti_options_vods[swti_vods_clip_period]">
			<option value="all" <?php echo selected( $clipPeriod, 'all', false ); ?>>All</option>
			<option value="day" <?php echo selected( $clipPeriod, 'day', false ); ?>>Day</option>
            <option value="week" <?php echo selected( $clipPeriod, 'week', false ); ?>>Week</option>
            <option value="month" <?php echo selected( $clipPeriod, 'month', false ); ?>>Month</option>
        </select>
		<p>Choose the Clip Period. This field only works when CLIPS is selected above.</p>
		<?php
	}	

	public function swti_vods_column_count_cb() {
		$columns = ( isset ( $this->options['swti_vods_column_count'] ) ) ? $this->options['swti_vods_column_count'] : '4';
		?>
		
		<input id="sw-tile-column-count" type="text" name="swti_options_vods[swti_vods_column_count]" value="<?php echo esc_html($columns); ?>">
		<span class="range-bar-value"></span>		
		<p>Choose the number of columns for your [Layout] Vods.</p>

		<?php
	}	

	public function swti_vods_column_spacing_cb() {
		$columnSpacing = ( isset ( $this->options['swti_vods_column_spacing'] ) ) ? $this->options['swti_vods_column_spacing'] : '5';
		?>
		
		<input id="sw-tile-column-spacing" type="text" name="swti_options_vods[swti_vods_column_spacing]" value="<?php echo esc_html($columnSpacing); ?>">
		<span class="range-bar-value"></span>	
		<p>Choose the space between columns for your [Layout] Vods.</p>


		<?php
	}	

	public function swti_options_validate($input) {
		$new_input = [];
		$options = get_option('swti_options_vods');

		if( isset( $input['swti_vods_channel'] ) ) {
			if (substr($input['swti_vods_channel'], -1) == ',') {
				$input['swti_vods_channel'] = substr($input['swti_vods_channel'], 0, -1);
			}
			$input['swti_vods_channel'] = str_replace(' ', '', $input['swti_vods_channel']);
			$input['swti_vods_channel'] = strtolower($input['swti_vods_channel']);
			$new_input['swti_vods_channel'] = sanitize_text_field( $input['swti_vods_channel'] );
		}	

		if( isset( $input['swti_vods_creator_filter'] ) ) {

			if (substr($input['swti_vods_creator_filter'], -1) == ',') {
				$input['swti_vods_creator_filter'] = substr($input['swti_vods_creator_filter'], 0, -1);
			}
			$input['swti_vods_creator_filter'] = str_replace(' ', '', $input['swti_vods_creator_filter']);
			$input['swti_vods_creator_filter'] = strtolower($input['swti_vods_creator_filter']);
			$new_input['swti_vods_creator_filter'] = sanitize_text_field( $input['swti_vods_creator_filter'] );
		}		

		if( isset( $input['swti_vods_clip_type'] ) ) {
			$new_input['swti_vods_clip_type'] = sanitize_text_field( $input['swti_vods_clip_type'] );
		}
		
		if( isset( $input['swti_vods_clip_period'] ) ) {
			$new_input['swti_vods_clip_period'] = sanitize_text_field( $input['swti_vods_clip_period'] );
		}		

		if( isset( $input['swti_vods_column_count'] ) ) {
			$new_input['swti_vods_column_count'] = sanitize_text_field( $input['swti_vods_column_count'] );
		}
		
		if( isset( $input['swti_vods_column_spacing'] ) ) {
			$new_input['swti_vods_column_spacing'] = sanitize_text_field( $input['swti_vods_column_spacing'] );
		}		

		return $new_input;
	}	

}