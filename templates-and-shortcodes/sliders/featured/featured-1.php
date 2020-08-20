<?php
/**
 * WYZI Featured Slider
 *
 * @package wyz
 */

// Block direct requests
if ( ! defined( 'ABSPATH' ) ) {
	wp_die('No cheating');
}

if( ! class_exists( 'WYZIFeaturedSlider' ) ) {

	class WYZIFeaturedSlider {

		private $slider_attr;
		private $classes;

		public function __construct( $attr ) {
			$this->slider_attr = $attr;
			$this->default_image_path = plugin_dir_url( __FILE__ ) . 'images/featured_default_image.png';
			$this->classes = WYZISlidersFactory::get_slider_css_classes( $this->slider_attr );
			add_action( 'wp_footer', array( &$this, 'include_slider_script') , 6 );
		}

		public function the_featured_slider() { 
			ob_start();
			$sticky_posts = get_option( 'sticky_posts' );
			$no_posts = empty( $sticky_posts );
			$edg2edg = $levit = '';
			$count = 0;
			if ( ! $no_posts ) {
			$args = array(
				'posts_per_page' => $this->slider_attr['count'],
				'offset' => 0,
				'orderby' => 'date',
				'order' => 'DESC',
				'post_type' => 'wyz_business',
				'post_status' => 'publish',
				'post__in' => $sticky_posts,
			); 
			$posts = new WP_Query( $args ); 
			$count = 0;

			$edg2edg = ( isset( $this->slider_attr['edg-to-edg'] ) && $this->slider_attr['edg-to-edg'] ? ' edg2edg' : '' );
			$levit =  ( isset( $this->slider_attr['edg-to-edg'] ) && $this->slider_attr['edg-to-edg'] ? ' item-levit' : '' );
			}?>

			<div class="featured-area owl-carousel margin-bottom-50">
					<div class="row"> 
						<!-- Section Title -->
						<div class="section-title col-xs-12 margin-bottom-50">
							<h1><?php echo esc_html( $this->slider_attr['featured_slider_ttl'] );?></h1>
						</div>
						<div class="col-xs-12">
							<!-- Recently Added Slider -->
							<div class="featured-slider<?php echo $edg2edg;?>">
							<?php if ( ! $no_posts ) {
								while ( $posts->have_posts() ) :
									$posts->the_post();
									require( apply_filters( 'wyz_featured_slider_bus_template', plugin_dir_path( __FILE__ ) . 'template/business-1.php' ) ); 
									$count++;
								endwhile;
							}
							wp_reset_postdata();
							?>
							</div>
						</div>
					</div>
			</div>
			<?php $featured_slide_data = array(
				'nav' => $this->slider_attr['nav'],
				'autoplay' => $this->slider_attr['autoplay'],
				'autoplay_timeout' => $this->slider_attr['autoplay_timeout'],
				'loop' => $count > 1 ? $this->slider_attr['loop'] : false,
				'cssClasses' => $this->classes,
			);
			wp_localize_script( 'wyz_featured_script', 'featuredSlide', $featured_slide_data );

			return ob_get_clean();
		}

		public function include_slider_script() {
			wp_enqueue_script( 'wyz_featured_script' );
		}
	}
}