<?php
/**
 * WYZI Recently Added Slider
 *
 * @package wyz
 */

// Block direct requests
if ( ! defined( 'ABSPATH' ) ) {
	wp_die('No cheating');
}

if( ! class_exists( 'WYZIOffersSlider' ) ) {

	class WYZIOffersSlider {

		private $slider_attr;
		private $all_posts;

		public function __construct( $attr ) {
			$this->slider_attr = $attr;
			add_action( 'wp_footer', array( &$this, 'include_slider_script') , 6 );
			$this->setup_offers();
		}

		public function setup_offers() {

			$count = isset( $this->slider_attr['count'] ) ? intval( $this->slider_attr['count'] ) : -1;
			if ( is_nan( $count ) || $count < 1 )
				$count = -1;
			$qry_args = array(
				'post_status' => 'publish',
				'post_type' => 'wyz_offers',
				'posts_per_page' => $count,
			);

			if ( isset( $this->slider_attr['category'] ) && ! empty( $this->slider_attr['category'] ) ) {
				$qry_args['tax_query'] = array(
					array(
						'taxonomy' => 'offer-categories',
						'field' => 'term_id',
						'terms' => array( $this->slider_attr['category'] )
					)
				);
			}

			$this->all_posts = new WP_Query( $qry_args );

			$offer_slide_data = array(
				'nav' => $this->slider_attr['nav'],
				'autoplay' => $this->slider_attr['autoplay'],
				'autoplay_timeout' => $this->slider_attr['autoplay_timeout'],
				'loop' => $this->slider_attr['loop'],
				'autoHeight' => $this->slider_attr['autoheight'],
			);
			wp_localize_script( 'wyz_offers_script', 'offerSlide', $offer_slide_data );
		}
			

		public function the_offers_slider() {
			ob_start();
			$edg2edg = ( isset( $this->slider_attr['edg-to-edg'] ) && $this->slider_attr['edg-to-edg'] ? ' edg2edg' : '' );
			?>

			<div class="our-offer-area margin-bottom-50">
				<div class="row">
					<div class="section-title section-title-search col-xs-12">
						<h1><?php echo esc_html( $this->slider_attr['offer_slider_ttl'] );?></h1>
					</div>
					<div class="col-xs-12">
						<!-- Offer Slider -->
						<?php 
						// Only show the slider if we have more than 1 offer (bug in owl).
						if ( $this->all_posts->post_count > 1 ) { ?>
						<div class="owl-carousel our-offer-slider<?php echo $edg2edg;?>">
						<?php } else { ?>
							<div class="single-owl-carousel">
						<?php }
						if ( class_exists( 'WyzOffer' ) ) {
							while ( $this->all_posts->have_posts() ) {
								$this->all_posts->the_post();
								WyzOffer::wyz_the_offer( get_the_ID(), false );
							}
							wp_reset_postdata();
						}
						?>
						</div>
					</div>
				</div>
			</div>

			<?php 
			return ob_get_clean();
		}

		public function include_slider_script() {
			wp_enqueue_script( 'wyz_offers_script' );
		}
	}
}
