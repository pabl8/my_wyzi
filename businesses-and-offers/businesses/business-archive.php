<?php
/**
 * Template Name: Business Archive
 *
 * @package wyz
 */
get_header();


if ( 'on' != get_option( 'wyz_business_map_hide_in_archives' ) ) {
	WyzMap::wyz_get_archives_map();
}

function validDate( $date ) {
	$d = DateTime::createFromFormat('m_d_Y', $date);
	if($d)return true;
	return false;
}

$business_filters = get_option( 'wyz_business_filters', array() );
$filters = array();

if ( ! empty( $business_filters ) && is_array( $business_filters ) ) {
	foreach( $business_filters as $key => $value ) {
		if($key=='custom_fields')
			foreach ($value as $k => $v) {
				$filters[ $k ] = $v;
			}
		else
			$filters[ $key ] = $value['metadata'];
	}
}

$location = -1;
$days_get = array();
global $wp_query;
global $template_type;
$temp_query = '';
if ( isset( $_GET['location'] ) && 0 < $_GET['location'] ) {
	$location = $_GET['location'];
}
if ( isset( $_GET['open_days'] ) ) {
	$days_get = $_GET['open_days'];
}

$date = false;
$have_booking_filter = false;
if ( isset( $_GET['booking-date'] ) && validDate( $_GET['booking-date'] ) ) {
	$date = $_GET['booking-date'];
	$have_booking_filter = true;
}

$map_skin = get_option( 'wyz_business_archives_map_skin' );
if ( '' == $map_skin ) $map_skin = 0;
$def_arch_co = WyzHelpers::get_default_archive_map_coordinates();


$wyz_primary_color = function_exists('wyz_get_option')? wyz_get_option( 'primary-color' ):'';

if ( '' == $wyz_primary_color ) {
	$wyz_primary_color = '#00aeff';
}

$js_data = array(
	'GPSLocations' => array(),
	'markersWithIcons' => array(),
	'businessNames' => array(),
	'businessLogoes' => array(),
	'businessPermalinks' => array(),
	'businessIds' => array(),
	'businessCategories' => array(),
	'businessCategoriesColors' => array(),
	'favorites' => array(),
	'radFillColor'=>$wyz_primary_color,
	'radStrokeColor'=>$wyz_primary_color,
	'defLogo' => WyzHelpers::get_default_image( 'business' ),
	'defCoor' => array(
		'latitude' => $def_arch_co[0],
		'longitude' => $def_arch_co[1],
		'zoom' => get_option( 'wyz_archives_map_zoom', 12 ),
	),
	'query_args' => array(),
	'favEnabled' => 'on' == get_option( 'wyz_enable_favorite_business' ),
	'tabs' => WyzMap::get_map_tabs(),
	'mapSkin' => WyzMap::get_map_skin( $map_skin, -1 ),
	'translations' => array(
		'viewDetails' => esc_html__( 'View Details', 'wyzi-business-finder' ),
		'viewAll' => esc_html__( 'View All', 'wyzi-business-finder' ),
		'geoFail' => esc_html__( 'The Geolocation service failed. You can still search by location radius, but not by your location.', 'wyzi-business-finder' ),
		'geoBrowserFail' => esc_html__( 'Error: Your browser doesn\'t support geolocation.', 'wyzi-business-finder' ),
	),
	'myLocationMarker' => '',
	'templateType' => $template_type,
	'range_radius' => array(),
);
if ( 'on' == get_option( 'wyz_archives_map_my_loction' ) ) {
	$js_data['myLocationMarker'] = WYZI_PLUGIN_URL . 'templates-and-shortcodes/images/iamhere.png';
}

$js_data = apply_filters( 'wyz_archives_map_js_data', $js_data );
add_action( 'wp_footer', function(){
	global $js_data;
	wp_localize_script( 'wyz_archives_map', 'archivesMap', $js_data );
}, 11 );
?>

<div class="wall-collection-area margin-bottom-100 margin-top-50">
	<div class="container">
		<div class="row">

			<!-- Left sidebar. -->
			<?php if ( 'right-sidebar' !== wyz_get_option( 'sidebar-layout' ) && 'full-width' !== wyz_get_option( 'sidebar-layout' ) ) :?>
				
				<div class="sidebar-container<?php if ( 'on' === wyz_get_option( 'resp' ) ) { ?> col-lg-3 col-md-4 col-xs-12<?php } else { ?>col-xs-4 <?php } ?>">
						
					<?php if ( is_active_sidebar( 'wyz-business-listing-sb' ) ) : ?>

						<div class="widget-area sidebar-widget-area" role="complementary">
							
							<?php dynamic_sidebar( 'wyz-business-listing-sb' ); ?>
						
						</div>

					<?php endif; ?>

				</div>
			<?php endif; ?>

			<!-- Wall Collection -->
			<div class="wall-collections<?php if ( 'full-width' === wyz_get_option( 'sidebar-layout' ) ) { ?> col-lg-12 col-md-12 col-xs-12"<?php } elseif ( 'on' === wyz_get_option( 'resp' ) ) { ?> col-lg-9 col-md-8 col-xs-12<?php } else { ?> col-xs-8<?php } ?>">

				<?php 
				$count = 0;
				$paged = 1;
				if ( get_query_var( 'paged' ) ) {
					$paged = get_query_var( 'paged' );
				}

				$args = WyzBusinessFiltersFactory::filter_query();

				wyz_add_archives_js( $args );

				do_action( 'wyz_before_business_search_display' );

				$before_search_content = get_option('wyz_business_archive_before_listings');
				if ( ! empty( $before_search_content ) ) {
					echo '<div class="before-list-cont">' . do_shortcode( $before_search_content ) . '</div>';
				}

				$post_ids = array();

				if ( ! empty( $args ) ) {
					$args['post_type'] = 'wyz_business';
					$args['paged'] = $paged;
					$the_query = WyzHelpers::query_businesses( $args );

					$temp_query = $wp_query;
					$wp_query = $the_query;
					
					if ( $the_query->have_posts() ) :
					
						while ( $the_query->have_posts() ) :
							$the_query->the_post();

							if ( ! WyzBusinessFiltersFactory::has_booking_in_timeframe( get_the_author_meta( 'ID' ), get_the_ID(), $date ) ){
								continue;
							}

				 			wyz_get_archives_filtered();
				 			$post_ids[] = get_the_ID();
				 			$count++;

						endwhile;
						
					endif;
				} else {
					$the_query = WyzHelpers::query_businesses( array( 'paged' => $paged,'post_type'=>'wyz_business','post_status'=>'publish') );

					$post_ids = array();
					if ( $the_query->have_posts() ) :
						//$post_ids = wp_list_pluck( $the_query->posts, 'ID' );
						while ( $the_query->have_posts() ) :
							$the_query->the_post();

							if ( ! WyzBusinessFiltersFactory::has_booking_in_timeframe( get_the_author_meta( 'ID' ), get_the_ID(), $date ) )
								continue;

				 			wyz_get_archives_filtered();
				 			$post_ids[] = get_the_ID();

				 			$count++;

						endwhile;
					endif;
				}
				if( 0 === $count ) {
					if ( $location < 1 ) {
						WyzHelpers::wyz_info( esc_html__( 'No Businesses to show', 'wyzi-business-finder' ) );
					} else {
						WyzHelpers::wyz_info( esc_html__( 'No Businesses match your search', 'wyzi-business-finder' ) );
					}
				} else {
					if ( 2 == $template_type ) {
						if ( function_exists( 'wyz_get_option' ) ) {
							$grid_alias = wyz_get_option( 'listing_archives_ess_grid' );
							if ( '' != $grid_alias )
								echo do_shortcode( '[ess_grid alias="' . $grid_alias .'" posts='.implode(',',$post_ids).']' );
						}
					}
				}

				do_action( 'wyz_after_business_search_display' );

				if ( function_exists( 'wyz_pagination' ) ) wyz_pagination();
				$wp_query = $temp_query;
				wp_reset_postdata();?>
			</div>

			<!-- Right sidebar. -->
			<?php if ( 'right-sidebar' === wyz_get_option( 'sidebar-layout' ) ) :;?>
				
				<div class="sidebar-container<?php if ( 'on' === wyz_get_option( 'resp' ) ) { ?> col-lg-3 col-md-4 col-xs-12<?php } else { ?>col-xs-4 <?php } ?>">
						
					<?php if ( is_active_sidebar( 'wyz-business-listing-sb' ) ) : ?>

						<div class="widget-area sidebar-widget-area" role="complementary">
							
							<?php dynamic_sidebar( 'wyz-business-listing-sb' ); ?>
						
						</div>

					<?php endif; ?>

				</div>
			<?php endif; ?>
		</div>
	</div>
</div>

<?php 
function wyz_get_archives_filtered() {
	global $days_get;
	global $count;
	global $template_type;
	if ( ! empty( $days_get ) ) {
		$keys = array( 'wyz_open_close_monday', 'wyz_open_close_tuesday', 'wyz_open_close_wednesday', 'wyz_open_close_thursday'
				, 'wyz_open_close_friday', 'wyz_open_close_saturday', 'wyz_open_close_sunday');
		$ds = array( 'mon','tue','wed','thur','fri','sat','sun' );
		$day = '';
		$len = count( $days_get );
		$c = 0;
		for ( $i = 0; $i<7; $i++ ) {
			if ( in_array( $ds[ $i ], $days_get ) ) {
				$day = get_post_meta( get_the_ID(), $keys[ $i ], true );
				if ( ! empty( $day[0] ) ) {
					$c++;
				}
			}
		}
		if( $c == $len ){
			if ( 1 == $template_type )
				echo WyzBusinessPost::wyz_create_business(true);
			$count++;
			//wyz_add_archives_js();
		}
	} else {
		if ( 1 == $template_type )
			echo WyzBusinessPost::wyz_create_business(true);
		$count++;
		//wyz_add_archives_js();
	}	
}

function wyz_add_archives_js( $args ) {
	global $js_data;
	global $template_type;

	$args['post_type'] = 'wyz_business';
	$args['post_status'] = 'publish';
	$args['posts_per_page'] = get_option( 'wyz_map_max_ajax_load',400 );

	$js_data['query_args'] = $args;
	return;
	$quer = new WP_Query( $args );

	$def_arch_co = WyzHelpers::get_default_archive_map_coordinates();

	$def_marker_coor = array('latitude' => $def_arch_co[0], 'longitude' => $def_arch_co[1] );

	while ( $quer->have_posts() ) {
		$quer->the_post();

		$temp_loc = get_post_meta( get_the_ID(), 'wyz_business_location', true );

		if ( empty( $temp_loc ) || !isset( $temp_loc['latitude'] ) || '' == $temp_loc['latitude'] || '' == $temp_loc['longitude'] ) {
			$temp_loc = array(
				'latitude' => $def_marker_coor['latitude'],
				'longitude' => $def_marker_coor['longitude'],
			);
		}

		array_push( $js_data['GPSLocations'], $temp_loc );

		array_push( $js_data['businessNames'], get_the_title() );

		array_push( $js_data['businessPermalinks'], esc_url( get_the_permalink() ) );

		array_push( $js_data['businessLogoes'], WyzHelpers::get_post_thumbnail( get_the_ID(), 'business', 'medium', array( 'class' => 'business-logo-marker' ) ) );
		
		array_push( $js_data['range_radius'], WyzHelpers::get_business_range_radius_in_meters( get_the_ID() ) );
		
		$temp_term_id = WyzHelpers::wyz_get_representative_business_category_id( get_the_ID() );

		if ( '' != $temp_term_id ) {
			$icon_meta_key = 'map_icon';
			if(2==$template_type) $icon_meta_key .= '2';
			$holder = wp_get_attachment_url( get_term_meta( $temp_term_id, $icon_meta_key, true ) );
			$col = get_term_meta( $temp_term_id, 'wyz_business_cat_bg_color', true );
			array_push( $js_data['businessCategories'], intval( $temp_term_id ) );
			array_push( $js_data['businessCategoriesColors'], $col );

			if ( ! isset( $holder ) || false == $holder || '' == $holder ) {
				array_push( $js_data['markersWithIcons'], '' );
				array_push( $js_data['businessCategories'], -1 );
				array_push( $js_data['businessCategoriesColors'], '' );
			} else {
				array_push( $js_data['markersWithIcons'], $holder );
			}
		} else {
			array_push( $js_data['markersWithIcons'], '' );
			array_push( $js_data['businessCategories'], -1 );
			array_push( $js_data['businessCategoriesColors'], '' );
		}
	}
	wp_reset_postdata();
}
?>
<?php get_footer();?>
