<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

class WyzBusinessFiltersFactory {
	private static $locations;
	private static $radius;
	private static $categories;
	private static $keyword;
	private static $days;
	private static $submit;
	private static $bookings;
	private static $verified;
	private static $custom_fields;

	private static $template_type;


	private static $css_displayed;
	
	public static function get_filter( $name ) {
		$filter_applied = apply_filters( 'wyz_extra_add_filter', false, $name );
		if ( false !== $filter_applied)
			return $filter_applied;
		switch( $name ) {
			case 'locations':
				return self::get_locations();
			case 'radius':
				return self::get_radius();
			case 'category':
				return self::get_categories();
			case 'keyword':
				return self::get_keyword();
			case 'open_days':
				return self::get_days();
			case 'bookings':
				return self::get_bookings();
			case 'submit':
				return self::get_submit();
			case 'verified':
				return self::get_verified();
			default:
				return self::get_custom_fields( $name );
		}
	}

	public static function get_filters( $filters_order ) {
		if ( empty( $filters_order ) ) return '';
		ob_start();
		$filters_order = str_replace("``", '', $filters_order);
		$fields = explode( '::', $filters_order );

		$fields_count = count( $fields );

		self::css( $fields_count );
		
		usort( $fields, function( $a, $b ) {
			$field_a = explode( ':', $a, 2 );
			$field_b = explode( ':', $b, 2 );
			$field_a_coor = explode( ',', substr( $field_a[1], 1, -1), 3 );
			$field_b_coor = explode( ',', substr( $field_b[1], 1, -1), 3 );
			return ( ( intval( $field_a_coor[0] ) + intval( $field_a_coor[1]*12 ) ) > ( intval( $field_b_coor[0] ) + intval( $field_b_coor[1]*12 ) ) ? 1 : -1 );
		});
		
		$i=1;
		?>
		<div class="bus-filters-cont bus-filter-custom">
			<form method="GET" action="<?php echo get_post_type_archive_link( 'wyz_business' );?>">
			<?php
			foreach ( $fields as $field ) {
				$data = explode( ':', $field, 2 );
				if( count( $data ) != 2 )
					continue;
				$filter = self::get_filter( $data[0] );
				echo $filter->content( $data[1], array($i%5,$fields_count) );
				$i++;
			}?>
			</form>
		</div>
		<?php
		add_action( 'wp_footer', function(){self::scripts();} );
		return ob_get_clean();
	}

	private static function css( $fields_count ) {
		if ( self::$css_displayed )
			return;
		self::$css_displayed = true;

		self::$template_type = 1;

			self::$template_type = wyz_get_theme_template();
		if ( function_exists( 'wyz_get_theme_template' ) )

		echo '<style>';
		self::css_dyn( array(12,10), $fields_count, 12 );
		self::css_dyn( array(9,8), $fields_count, 12 );
		self::css_dyn( array(6,4), $fields_count, 12 );
		self::css_dyn_sml( array(3,2), $fields_count, 12 );?>
		@media only screen and (max-width: 991px) {
			<?php self::css_dyn_sml( array(4), $fields_count, 12 );?>
		}

		@media only screen and (max-width: 767px) {
			<?php self::css_small( array(12,10,9,8,6,4,3,2), $fields_count, 1 );?>
		}
		<?php echo '</style>';
	}


	private static function css_dyn( $columns, $fields_count, $count ) {
		$perc = 100.0/$count;
		for ( $k = 1; $k < 13; $k++ ) {
			for($i=0;$i<count($columns);$i++) {
				echo '.vc_col-sm-' . $columns[ $i ].' .bus-filter.width-' . $k;
				if ( $i != count($columns)-1)
					echo ',';
			}
			echo "{width:" . ($k*$perc) . "%;}";

			for($i=0;$i<count($columns);$i++) {
				echo '.vc_col-sm-' . $columns[ $i ].' .bus-filter';
				if ( $i != count($columns)-1)
					echo ',';
			}
			echo "{padding-right: 4px;}";
		}
	}

	private static function css_dyn_sml( $columns, $fields_count, $count ) {
		for ( $k = 1; $k < 13; $k++ ) {
			for($i=0;$i<count($columns);$i++) {
				echo '.vc_col-sm-' . $columns[ $i ].' .bus-filter.width-' . $k.',';
				echo '.vc_col-sm-' . $columns[ $i ].' .input-submit';
				if ( $i != count($columns)-1)
					echo ',';
			}
			echo "{width:100%;margin-bottom:10px;}";
		}

		if(2!=self::$template_type)return;

		for($i=0;$i<count($columns);$i++) {
			echo '.vc_col-sm-' . $columns[ $i ].' .bus-filters-cont form';
			if ( $i != count($columns)-1)
				echo ',';
		}
		echo "{border-radius: 0;padding: 20px;}";

		for($i=0;$i<count($columns);$i++) {
			echo '.vc_col-sm-' . $columns[ $i ].' .bus-filters-cont .input-submit button';
			if ( $i != count($columns)-1)
				echo ',';
		}
		echo "{border-radius: 0;float:none;margin:0;}";

	}

	private static function css_small( $columns ) {
		for ( $k = 1; $k < 13; $k++ ) {
			for($i=0;$i<count($columns);$i++) {
				echo '.vc_col-sm-' . $columns[ $i ].' .bus-filter.width-' . $k;
				if ( $i != count($columns)-1)
					echo ',';
			}
			echo "{width:100%;}";

			 echo '.vc_column_container .input-submit{width:100%}';

			for($i=0;$i<count($columns);$i++) {
				echo '.vc_col-sm-' . $columns[ $i ].' .bus-filter';
				if ( $i != count($columns)-1)
					echo ',';
			}
			echo "{padding-right: 0;margin-bottom:10px;}";
		}

		if(2!=self::$template_type)return;

		for($i=0;$i<count($columns);$i++) {
			echo '.vc_col-sm-' . $columns[ $i ].' .bus-filters-cont form';
			if ( $i != count($columns)-1)
				echo ',';
		}
		echo "{border-radius: 0;padding: 20px;}";

		for($i=0;$i<count($columns);$i++) {
			echo '.vc_col-sm-' . $columns[ $i ].' .bus-filters-cont .input-submit button';
			if ( $i != count($columns)-1)
				echo ',';
		}
		echo "{border-radius: 0;float:none;margin:0;}";
	}


	private static function scripts() {
		$language = get_bloginfo( 'language' );

		$includes = array('jquery', 'jquery-ui-datepicker');
		/*if ( ! wp_script_is( 'wyz_map_api', 'enqueued' ) ) {
			wp_register_script( 'wyz_map_api', '//maps.googleapis.com/maps/api/js?libraries=places&language='.$language.'&key=' . get_option( 'wyz_map_api_key' ) . '&callback=wyz_init_load_filter_text_field#asyncload', array( 'jquery' ) );
			$includes[] = 'wyz_map_api';
		}*/
		$includes[] = 'wyz_map_api';
		wp_enqueue_script( 'range_slider', WYZI_PLUGIN_URL . 'templates-and-shortcodes/js/range-slider.js', array( 'jquery' ) );
		wp_enqueue_script( 'bookings_filter_scripts', WYZI_PLUGIN_URL . 'templates-and-shortcodes/js/frontend-business-filters.js', $includes );
	}

	private static function get_locations() {
		if( ! isset( self::$locations ) ) {
			require_once( plugin_dir_path( __FILE__ ) . 'locations.php' );
			self::$locations = new WyzLocationsFilter();
		}
		return self::$locations;
	}

	private static function get_radius() {
		if( ! isset( self::$radius ) ) {
			require_once( plugin_dir_path( __FILE__ ) . 'locations-radius.php' );
			self::$radius = new WyzLocationRadiusFilter();
		}
		return self::$radius;
	}

	private static function get_categories() {
		if( ! isset( self::$categories ) ) {
			require_once( plugin_dir_path( __FILE__ ) . 'categories.php' );
			self::$categories = new WyzCategoriesFilter();
		}
		return self::$categories;
	}

	private static function get_keyword() {
		if( ! isset( self::$keyword ) ) {
			require_once( plugin_dir_path( __FILE__ ) . 'keyword.php' );
			self::$keyword = new WyzKeywordFilter();
		}
		return self::$keyword;
	}

	private static function get_days() {
		if( ! isset( self::$days ) ) {
			require_once( plugin_dir_path( __FILE__ ) . 'days.php' );
			self::$days = new WyzDaysFilter();
		}
		return self::$days;
	}

	private static function get_submit() {
		if( ! isset( self::$submit ) ) {
			require_once( plugin_dir_path( __FILE__ ) . 'submit.php' );
			self::$submit = new WyzFilterSubmit();
		}
		return self::$submit;
	}

	private static function get_bookings() {
		if( ! isset( self::$bookings ) ) {
			require_once( plugin_dir_path( __FILE__ ) . 'bookings.php' );
			self::$bookings = new WyzBookingsFilter();
		}
		return self::$bookings;
	}

	private static function get_custom_fields( $name ) {
		if( ! isset( self::$custom_fields ) ) {
			require_once( plugin_dir_path( __FILE__ ) . 'custom-field.php' );
			self::$custom_fields = array();
		}
		self::$custom_fields[ $name ] = new WyzCustomFieldFilter( $name );
		return self::$custom_fields[ $name ];
	}

	private static function get_verified() {
		if( ! isset( self::$verified ) ) {
			require_once( plugin_dir_path( __FILE__ ) . 'verified.php' );
			self::$verified = new WyzVerifiedFilter();
		}
		return self::$verified;
	}

	private static $calendars;
	public static function has_booking_in_timeframe( $author_id, $id, $date ) {
		if ( !$date ) return true;

		if ( ! isset( self::$calendars[ $author_id ] ) ){
			self::$calendars[ $author_id ] = get_user_meta( $author_id, 'wyz_business_calendars', true );
		}

		$can_bookings = 'off' != get_option( 'wyz_users_can_booking' ) && WyzHelpers::wyz_sub_can_bus_owner_do($author_id,'wyzi_sub_business_can_create_bookings') &&
						class_exists( 'WooCommerce' ) && isset( self::$calendars[ $author_id ][ $id ] );

		if ( ! $can_bookings ) return false;


		$date = explode( '_', $date );

		$day = $date[1];
		$month = $date[0];
		$year = $date[2];

		return booked_appointments_available($year,$month,$day,self::$calendars[ $author_id ][ $id ]);
	}


	public static function filter_query() {
		$args = array();
		if ( ! is_tax('wyz_business_tag') && ! is_tax('wyz_business_category') ) {
			$args = self::no_tax_query( $args );
		} else if ( is_tax('wyz_business_tag') ) {
			$args = self::tags_query( $args );
		} elseif ( is_tax('wyz_business_category') ) {
			$args = self::categories_query( $args );
		} else {
			$args = self::default_query( $args );
		}

		return apply_filters( 'wyz_filter_query_args', $args );
	}

	private static function no_tax_query( $args ) {

		$location = isset( $_GET['location'] ) ? $_GET['location'] : -1;
		$category = isset( $_GET['category'] ) ? $_GET['category'] : -1;
		$keyword = isset($_GET['keyword'])?$_GET['keyword']:'';
		$keyword = trim( $keyword );
		$loc_txt = isset( $_GET['wyz-loc-filter-txt'] ) ? $_GET['wyz-loc-filter-txt'] : '';
		$lat = '';
		$lon = '';
		if ( ! empty( $loc_txt ) ) {
			$lat = isset($_GET['loc-filter-lat'])?floatval($_GET['loc-filter-lat']):'';
			$lon = isset($_GET['loc-filter-lng'])?floatval($_GET['loc-filter-lng']):'';
		}
		$rad = isset($_GET['radius'])?$_GET['radius']:10;


		$custom_meta = array();
		foreach ($_GET as $key => $value) {
			if( preg_match("/^wyzi_claim_fields/", $key) && ! empty( $_GET[ $key ] ) ){
				$type = WyzHelpers::get_custom_field_type( $key );
				$tmp_vlaues = array();
				if ( is_array( $type ) && 'selectbox' == $type['type'] ) {
					switch ( $type['selecttype'] ) {
						case 'checkboxes':
							 if ( ! is_array( $value ) || count( $value ) == 1) {
							 	if(is_array( $value ) && count( $value ) == 1)$value=$value[0];
								$tmp_vlaues['comp'] = 'LIKE';
								$tmp_vlaues['val'] = trim( $value );
							} else {
								$tmp_vlaues['comp'] = 'ARR_LIKE';
								foreach ($value as $s)
									$tmp_vlaues['val'][] = trim( $s );
							}
							break;
						default:
							$tmp_vlaues['comp'] = '=';
							$tmp_vlaues['val'] = trim( $value );
							break;
					}
					
				} else {
					$strings = explode(' ', $value);
					foreach ($strings as $s) {
						$tmp_vlaues['val'][] = trim( $s );
					}
					$tmp_vlaues['comp'] = 'LIKE';
				}
				$custom_meta[ $key ] = $tmp_vlaues;
			}
		}

		if ( '' != $keyword )
			$keywords = explode( ' ', $keyword );
		else
			$keywords = array();

		$meta_query = array();
		$loc_query = array();


		if ( '' != $location && 0 < $location ) {
			$children = WyzHelpers::get_location_children_grand_children( $location );
			$coor = get_post_meta( $location, 'wyz_location_coordinates', true );
			// Check if Location has Coordinates and Raduis is set, then we draw a circle on map around that location
			if(is_array($coor) && isset($_GET['radius']) && $rad != 0 ) {
				$lat = $coor['latitude'];
				$lon = $coor['longitude'];
				$args['post__in'] = self::get_businesses_within_radius( $lat, $lon, $rad );
			}
			/*$children = get_children( array(
				'post_parent' => $location,
				'post_status' => 'publish',
				'post_type' => 'wyz_location',
				'numberposts' => -1,
			));*/
			if ( ! empty( $children ) ) {
				$children = array_values( $children );
				/*$loc_query = array( $location );
				foreach ($children as $child) {
					$loc_query[] = $child->ID;
				}*/
				/*$loc_query = array( 'key' => 'wyz_business_country', 'value' => $children, 'compare' => 'IN' );*/
				$loc_query = $children;
			}else
				/*$loc_query = array(
					array( 'key' => 'wyz_business_country', 'value' => $location )
				);*/
				$loc_query = $location;
			//self::print_business_location_radius_info( $location, $rad );
		} elseif ( ! empty( $lat ) && ! empty( $lon ) ) {
			$args['post__in'] = self::get_businesses_within_radius( $lat, $lon, $rad );
		}

		if( ! empty( $keywords ) ) {
			$meta_query = array( // Include excerpt and slogan in global map search.
				'relation' => 'OR',
				array( 'key' => 'wyz_business_excerpt', 'value' => $keyword, 'compare' => 'LIKE' ),
				array( 'key' => 'wyz_business_slogan', 'value' => $keyword, 'compare' => 'LIKE' ),
			);
			if ( '' != $location && 0 < $location ) {
				$children = get_children( array(
					'post_parent' => $location,
					'post_status' => 'publish',
					'post_type' => 'wyz_location',
					'numberposts' => -1,
				));
				if ( ! empty( $children ) ) {
					$location_arr = array( $location );
					foreach ($children as $child) {
						$location_arr[] = $child->ID;
					}
				}
				$args['loc_query'] = $location_arr;
			}
		} elseif ( '' != $location && 0 < $location ) {
			$children = WyzHelpers::get_location_children_grand_children( $location );
			if ( ! empty( $children ) ) {
				$children = array_values( $children );
				$meta_query[] = array( 'key' => 'wyz_business_country', 'value' => $children, 'compare' => 'IN' );
			}else {
				/*$loc_query = array(
					array( 'key' => 'wyz_business_country', 'value' => $location )
				);*/
				$loc_query = $location;
				$meta_query[] = array( 'key' => 'wyz_business_country', 'value' => $location );
			}
			self::print_business_location_radius_info( $location, $rad );
		}

		

		if ( ! empty( $custom_meta ) || isset( $_GET['verified'] ) ) {
			$tmp = array();
			if(count($custom_meta)>1)
				$tmp['relation'] = 'AND';
			foreach ($custom_meta as $key => $value) {
				if($value['comp'] == 'ARR_LIKE' && count($value['val'])>1) {
					$sub_meta = array( 'relation' => 'OR' );
					foreach ($value['val'] as $v) {
						$sub_meta[] = array( 'key' => $key, 'value' => $v, 'compare' => 'LIKE' );	
					}
					$tmp[] = $sub_meta;
				} elseif( count( $value['val'] ) > 1) {
					$sub_meta = array( 'relation' => 'OR' );
					foreach ($value['val'] as $v) {
						$sub_meta[] = array( 'key' => $key, 'value' => $v, 'compare' => $value['comp'] );	
					}
					$tmp[] = $sub_meta;
				} else {
					$tmp[] = array( 'key' => $key, 'value' => ( is_array( $value['val'] ) ? $value['val'][0] : $value['val'] ), 'compare' => $value['comp'] );	
				}
			}

			if( isset( $_GET['verified'] ) ) {
				$tmp[] = array(
					array( 'key' => 'wyz_business_verified', 'value' => 'yes' ),
				);
				if ( ! isset($tmp['relation'] ) )
					$tmp['relation'] = 'AND';
			}

			$meta_query[] = $tmp;
		}


		$tax_query = array();

		if ( ! empty( $keywords ) ) {

			$tag_ids = WyzHelpers::get_tax_like_ids( 'wyz_business_tag', $keywords );

			if ( ! empty( $tag_ids ) ) {
				$tax_query = array(
					array(
						'taxonomy' => 'wyz_business_tag',
						'field'    => 'term_id',
						'terms' => $tag_ids,
					),
				);
			}
			if ( '' !== $category && 0 < $category ){
				$args['cat_query'] = $category;
			}
		} elseif ( '' !== $category && 0 < $category ) {
			$tax_query = array(
				array(
					'taxonomy' => 'wyz_business_category',
					'field'    => 'term_id',
					'terms' => $category,
				),
			);
		}

		if ( '' != $meta_query ) {
			$args['meta_query'] = $meta_query;
		}
		

		if ( ! empty( $keywords ) ) {

			$args['_meta_or_title'] = $keywords;
			$args['my_tax_query'] = $tax_query;
			$args['_meta_or_tax'] = true;
			$args['loc_query'] = $loc_query;
		} elseif ( isset( $tax_query ) && ! empty( $tax_query) ) {
			$args['tax_query'] = $tax_query;
		}
		return $args;
	}

	private static function tags_query( $args ) {
		$tag = single_tag_title( '', false );
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'wyz_business_tag',
				'field'    => 'slug',
				'terms'    => $tag,
			),
		);
		return $args;
	}


	private static function categories_query( $args ) {
		$qo =get_queried_object();
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'wyz_business_category',
				'field'    => 'slug',
				'terms'    => $qo->slug,
			),
		);
		return $args;
	}

	private static function default_query( $args ) {
		$location = -1;
		$category = -1;

		$category = isset( $_GET['category'] ) ? $_GET['category'] : -1;

		if ( is_tax( 'wyz_business_category' ) ) {
			$category = get_queried_object_id();
		}

		$keyword = isset($_GET['keyword'])?$_GET['keyword']:'';

		if ( isset( $_GET['location'] ) && 0 < $_GET['location'] ) {
			$location = $_GET['location'];
		}
		if ( '' != $keyword )
			$keywords = explode( ' ', $keyword );
		else
			$keywords = array();


		if ( ! empty( $keyword ) ) {
			$args['post_title_like'] = $keyword;
		}
		if ( 0 < $category ) {
			if ( ! isset( $args['tax_query'] ) ) {
				$args['tax_query'] = array();
			}
			$args['tax_query'][] = array(
				'taxonomy' => 'wyz_business_category',
				'field'    => 'term_id',
				'terms'    => $category,
			);
		}
		if ( 0 < $location ) {
			$args['meta_query'] = array(
				array( 'key' => 'wyz_business_country', 'value' => $location ),
			);
		}
		if( isset( $_GET['verified'] ) ) {
			$args['meta_query'][] = array(
				array( 'key' => 'wyz_business_verified', 'value' => 'yes' ),
			);
			$args['meta_query']['relation'] = 'and';
		}
		return $args;
	}

	public static function get_businesses_within_radius( $lat, $lon, $rad ) {
		$ids = new WP_Query( array(
			'post_type' => 'wyz_business',
			'posts_per_page' => -1,
			'post_status' => 'publish',
			'fields' => 'ids',
		));

		$result = array();
		if ($rad == 0 || empty($rad) ) return $result;
		$p1 = array( 'lat' => $lat, 'lon' => $lon );
		foreach ( $ids->posts as $id ) {
			$temp_loc = get_post_meta( $id, 'wyz_business_location', true );
			if ( ! isset( $temp_loc['latitude'] ) || ! isset( $temp_loc['longitude'] ) || empty( $temp_loc['latitude'] ) || empty( $temp_loc['longitude'] ) )
				continue;

			$p2 = array( 'lat' => $temp_loc['latitude'], 'lon' => $temp_loc['longitude'] );
			if ( WyzHelpers::get_distance_between( $p1, $p2 ) <= $rad )
				$result[] = $id;
		}


		if ( empty( $result ) )
			$result[] = -1;

		wp_reset_postdata();
		?>
		<script>
		var wyzFilterMapLatitude = <?php echo $lat;?>;
		var wyzFilterMapLongitude = <?php echo $lon;?>;
		var wyzFilterMapRadius = <?php echo $rad;?>;
		var wyzFilterMapRadUnit = '<?php echo get_option( 'wyz_business_map_radius_unit', 'km' );?>';
		</script>
		<?php

		return $result;
	}

	public static function print_business_location_radius_info( $loc_id, $rad ) {
		$coor = get_post_meta( $loc_id, 'wyz_location_coordinates', true );
		if(!is_array($coor))$coor=array('latitude'=>'','longitude'=>'');
		?>
		<script>
		var wyzFilterMapLatitude = <?php echo $coor['latitude'];?>;
		var wyzFilterMapLongitude = <?php echo $coor['longitude'];?>;
		var wyzFilterMapRadius = <?php echo $rad;?>;
		var wyzFilterMapRadUnit = '<?php echo get_option( 'wyz_business_map_radius_unit', 'km' );?>';
		</script>
		<?php
	}
}