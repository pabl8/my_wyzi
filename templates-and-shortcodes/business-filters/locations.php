<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

require_once( plugin_dir_path( __FILE__ ) . 'business-filters.php' );

class WyzLocationsFilter extends WyzBusinessFilter {

	public function __construct() {
		parent::__construct( 'locations', esc_html__('Locations', 'wyzi-business-finder'), '' );
	}

	private static $google_loaded = false;
	private static $i;
	/*
	 *
	 * @Override
	 */
	public function content( $attr, $count_attr ) {

		$this->parse_params( $attr );
		$this->count_attr = $count_attr;
		$value = $this->get_value();


		ob_start();

		if( isset( $this->params['attributes']['filter-type'] ) && 'text' == $this->params['attributes']['filter-type'] ) {
			$this->get_location_field_text();
		} else {
			$this->get_location_field_dropdown();
		}
		return ob_get_clean();
	}


	private function get_location_field_text() {
		$loc_txt = isset( $_GET['wyz-loc-filter-txt'] ) ? $_GET['wyz-loc-filter-txt'] : '';
		$txt = '';
		$lat = '';
		$lon = '';
		if ( ! empty( $loc_txt ) ) {
			$txt = isset( $_GET['loc-filter-txt'] ) ? $_GET['loc-filter-txt'] : '';
			$lat = isset( $_GET['loc-filter-lat'] ) ? $_GET['loc-filter-lat'] : '';
			$lon = isset( $_GET['loc-filter-lng'] ) ? $_GET['loc-filter-lng'] : '';
		}
		?>
		<div class="<?php $this->css_classes();?>">
			<input type="text" name="wyz-loc-filter-txt" class="bus-filter-locations-text bus-filter-locations-text-<?php echo self::$i++;?>" autocomplete="on" placeholder="<?php 
				echo apply_filters( 'wyz_locations_filter_placeholder', esc_html__( 'locations...', 'wyzi-business-finder' ) );?>" value="<?php echo $loc_txt;?>"/>
			<input type="hidden" class="loc-filter-txt" name="loc-filter-txt" value="<?php echo $txt;?>"/>
			<input type="hidden" class="loc-filter-lat" name="loc-filter-lat" value="<?php echo $lat;?>"/>
			<input type="hidden" class="loc-filter-lon" name="loc-filter-lng" value="<?php echo $lon;?>"/>
		</div>
		<?php
	}


	private function get_location_field_dropdown() {
		$qry_args = array(
			'post_status' => 'publish',
			'post_type' => 'wyz_location',
			'orderby' => 'title',
			'post_parent' => 0,
			'order' => 'ASC',
			'posts_per_page' => - 1,
		);

		$location = isset( $_GET['location'] ) ? $_GET['location'] : '';

		//$this->get_value( 0 );

		/*$def_image = plugins_url( 'img/default-location.png', __FILE__ );
		$img = '';
		if ( function_exists( 'wyz_get_option') ) {
			$img = wyz_get_option( 'default-location-logo' );
		}

		if ( ! empty( $img ) )
			$def_image = $img;*/
		
		$all_posts = new WP_Query( $qry_args );?>

		<div class="<?php $this->css_classes();?>">

		<select name="location" class="wyz-input wyz-select bus-filter-locations-dropdown">
			<option value=""><?php echo apply_filters( 'wyz_locations_filter_placeholder', esc_html__( 'location...', 'wyzi-business-finder' ) );?></option>

		<?php 
		$def_loc_id = get_post_meta( get_the_ID(), 'wyz_def_image_location', true );
		if ( $def_loc_id == '' || $def_loc_id < 1 )
			$def_loc_id = -1;

		$selected =  get_post_meta( get_the_ID(), 'wyz_def_map_location', true );
		$def_loc_id = ( -1 == $selected ) ? $def_loc_id : $selected;

		while ( $all_posts->have_posts() ) {
			$all_posts->the_post();
			$l_id = get_the_ID();
			$img = WyzHelpers::get_post_thumbnail( $l_id, 'location', array( 50, 50 ) );
			echo '<option value="'.$l_id.'" ' . ( ( $location == $l_id || $def_loc_id == $l_id ) ? 'selected' : '' ) . ' data-left=\'' . $img . '\'>' . get_the_title() . '</option>';
			$children = get_children( array(
				'post_parent' => $l_id,
				'post_status' => 'publish',
				'post_type' => 'wyz_location',
				'numberposts' => -1,
			));
			if ( ! empty( $children ) ) {
				foreach ($children as $child) {
					echo '<option value="'.$child->ID.'" ' . ( ( $location == $child->ID || $def_loc_id == $child->ID ) ? 'selected' : '' ) . '>&nbsp;&nbsp;&nbsp;&nbsp;' . $child->post_title . '</option>';
				}
			}
			
		}?>
		</select>

		</div>

		<?php
		wp_reset_postdata();
	}


	/*
	 *
	 * @Override
	 */
	public function options() {
		ob_start();?>
		<script type="text/javascript">
		jQuery('#wyz-location-filter-type').on('change',function(){
			var val = jQuery(this).val();
			var key = jQuery(this).data('key');
			jQuery(document).trigger( "wyz-filter-option-change", [ 'locations', key, val ] );
		});
		</script>
		<div class="filter-options">
			<label>Filter type:</label>
			<select id="wyz-location-filter-type" data-key="filter-type">
				<option value="dropdown">Dropdown</option>
				<option value="text">Google Loction Autocomplete</option>
			</select>
		</div>

		<?php
		return ob_get_clean();
	}

	public function options_values() {
		//data-key => default_value
		return array(
			'filter-type' => 'dropdown'
		);
	}
}