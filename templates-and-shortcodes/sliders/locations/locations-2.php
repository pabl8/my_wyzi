<?php
/**
 * WYZI Locations Slider
 *
 * @package wyz
 */

// Block direct requests
if ( ! defined( 'ABSPATH' ) ) {
	wp_die('No cheating');
}

if( ! class_exists( 'WYZILocationsSlider' ) ) {

	class WYZILocationsSlider {

		private $loc_attr;

		public function __construct( $attr ) {
			$this->loc_attr = $attr;
			add_action( 'wp_footer', array( &$this, 'include_loc_script') , 6 );
			$this->setup_locations();
		}

		private function setup_locations() {
			$links = array();
			$names = array();
			$images = array();

			$qry_args = array(
				'post_status' => 'publish',
				'post_type' => 'wyz_location',
				'posts_per_page' => - 1,
				'orderby' => 'title',
				'order' => 'ASC',
				'post_parent' => 0,
			);

			$all_posts = new WP_Query( $qry_args );
			$business_permalink = get_post_type_archive_link( 'wyz_business' );

			$bus_count = array();
			$c;
			$count = 0;

			while ( $all_posts->have_posts() ) :
				$all_posts->the_post();

				// Locations May have Sub Locations so we need to get ID of Sub Locations to include them in search for Businesses
				$args_locations_sub = WyzHelpers::get_location_children_grand_children( get_the_ID() );
				/*$args_locations_sub = array(
					'post_status' => 'publish',
					'post_type' => 'wyz_location',
					'posts_per_page' => -1,
				    'fields' => 'ids',
				    'post_parent' => get_the_ID(),
				);*/
				/*$locations_sub_query = new WP_Query( $args_locations_sub);
			
				array_push($locations_sub_query->posts,get_the_ID());*/

				$args = array(
					'post_type' => 'wyz_business',
					'posts_per_page' => '-1',
					'post_status' => 'publish',
					'fields' => 'ids',
					'meta_query' => array(
						array(
							'key' => 'wyz_business_country',
							'value' => $args_locations_sub,//$locations_sub_query->posts,
							'compare' => 'IN'
						),
					),
				);

				if ( $this->loc_attr['linking'] ) {
					$links[] = get_the_permalink();
				} else {
					$links[] = $business_permalink . '?location=' . get_the_ID();
				}

				$names[] = get_the_title();
				/*if (  has_post_thumbnail() ) {
					$thumb_id = get_post_thumbnail_id();
					$thumb_url = wp_get_attachment_image_src( $thumb_id,'medium', true );
					$tmp_img = $thumb_url[0];
				} else {
					$tmp_img = WyzHelpers::get_default_image( 'location' );
				}*/

				$tmp_img = WyzHelpers::get_post_thumbnail_url( get_the_ID(), 'location' );

				$images[] = $tmp_img;

				$query = new WP_Query( $args );
				$c = $query->found_posts;
				$bus_count[] = $c;
				$count++;
			endwhile;

			$loc_slide_data = array(
				'names' => $names,
				'images' => $images,
				'links' => $links,
				'nav' => $this->loc_attr['nav'],
				'rows' => $this->loc_attr['rows'],
				'autoplay' => $this->loc_attr['autoplay'],
				'autoplay_timeout' => $this->loc_attr['autoplay_timeout'],
				'loop' => $count > 1 ? $this->loc_attr['loop'] : false,
				'busCount' => ( isset( $bus_count ) ? $bus_count : false ),
				'translations' => array(
					'location' => esc_html__( 'Location', 'wyzi-business-finder' ),
					'locations' => esc_html__( 'Locations', 'wyzi-business-finder' ),
				),
			);
			wp_localize_script( 'wyz_locations_script', 'locSlide', $loc_slide_data );
		}


		public function the_locations_slider() {
			?>


			<div class="popular-location-area section">
				<div class="container-rev">
					<?php if ( '' != $this->loc_attr['loc_slider_ttl'] ) {?>
					<div class="section-title section-title-search mb-40">
						<h3><?php echo esc_html( $this->loc_attr['loc_slider_ttl'] );?></h3>
					</div>
					<?php }?>
					<div class="row">
						<div class="col-xs-12">
							<!-- Popular Location Slider -->
							<div class="owl-carousel location-search-slider mb-50">
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php
		}

		public function include_loc_script() {
			wp_enqueue_script( 'wyz_locations_script' );
		}
	}
}
?>