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

class Streamweasels_Wall_Admin extends Streamweasels_Admin {

     public function __construct( $plugin_name, $version ) {
		
		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->options = get_option( 'swti_options_wall', array() );
	}     

    public function display_admin_page_wall() {

        add_submenu_page(
            'streamweasels',
            '[Layout] Wall',
            '[Layout] Wall',
            'manage_options',
            'streamweasels-wall',
            array($this, 'swti_showAdmin')
        );		

        $tooltipArray = array(
            'Column Count'=>'Column Count <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="wall-column-count=\'\'"></span>',
            'Column Spacing'=>'Column Spacing <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="wall-column-spacing=\'\'"></span>',
        );		

        register_setting( 'swti_options_wall', 'swti_options_wall', array($this, 'swti_options_validate'));	
        add_settings_section('swti_wall_settings', '[Layout] Wall Settings', false, 'swti_wall_fields');
        // Shortcode Settings section
        add_settings_section('swti_wall_shortcode_settings', 'Shortcode', false, 'swti_wall_shortcode_fields');		
        add_settings_field('swti_wall_column_count', $tooltipArray['Column Count'], array($this, 'swti_wall_column_count_cb'), 'swti_wall_fields', 'swti_wall_settings');			
        add_settings_field('swti_wall_column_spacing', $tooltipArray['Column Spacing'], array($this, 'swti_wall_column_spacing_cb'), 'swti_wall_fields', 'swti_wall_settings');
    } 

	public function swti_showAdmin() {
		include ('partials/streamweasels-admin-display.php');
	}	    

	public function swti_wall_column_count_cb() {
		$columns = ( isset ( $this->options['swti_wall_column_count'] ) ) ? $this->options['swti_wall_column_count'] : '4';
		?>
		
		<input id="sw-tile-column-count" type="text" name="swti_options_wall[swti_wall_column_count]" value="<?php echo esc_html($columns); ?>">
		<span class="range-bar-value"></span>		
		<p>Choose the number of columns for your [Layout] Wall.</p>

		<?php
	}	

	public function swti_wall_column_spacing_cb() {
		$columnSpacing = ( isset ( $this->options['swti_wall_column_spacing'] ) ) ? $this->options['swti_wall_column_spacing'] : '5';
		?>
		
		<input id="sw-tile-column-spacing" type="text" name="swti_options_wall[swti_wall_column_spacing]" value="<?php echo esc_html($columnSpacing); ?>">
		<span class="range-bar-value"></span>	
		<p>Choose the space between columns for your [Layout] Wall.</p>


		<?php
	}

	public function swti_options_validate($input) {
		$new_input = [];
		$options = get_option('swti_options_wall');
		if( isset( $input['swti_wall_stream_position'] ) ) {
			$new_input['swti_wall_stream_position'] = sanitize_text_field( $input['swti_wall_stream_position'] );
		}	

		if( isset( $input['swti_wall_column_count'] ) ) {
			$new_input['swti_wall_column_count'] = sanitize_text_field( $input['swti_wall_column_count'] );
		}
		
		if( isset( $input['swti_wall_column_spacing'] ) ) {
			$new_input['swti_wall_column_spacing'] = sanitize_text_field( $input['swti_wall_column_spacing'] );
		}
		return $new_input;
	}	     

}