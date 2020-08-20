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

		private static $default_image_path = '';

		public function __construct( $attr ) {
			$this->slider_attr = $attr;
			self::$default_image_path = plugin_dir_url( __FILE__ ) . 'images/featured_default_image.png';
			add_action( 'wp_footer', array( &$this, 'include_slider_script') , 6 );
		}

		public function the_featured_slider() {
			ob_start();
			$sticky_posts = get_option( 'sticky_posts' );
			$no_posts = empty( $sticky_posts );
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
				?>
			<!-- Featured Places Area Start -->
			<div class="featured-place-area mb-50 section">
				<div class="container">
					<div class="row featured-masonry-grid">
					<?php if ( ! $no_posts ) {
						$class_conv = array(
							'6' => '2',
							'4' => '3',
							'3' => '4',
							'2' => '6',
							'1' => '12',
						);
						$classes = WYZISlidersFactory::get_slider_css_classes( $this->slider_attr );

						$class = "col-lg-" . $class_conv[ $classes['lg'] ] . " col-md-" . $class_conv[ $classes['md'] ] . " col-sm-" . $class_conv[ $classes['sm'] ] . " col-xs-" . $class_conv[ $classes['xs'] ];
						while ( $posts->have_posts() ) :
							$posts->the_post();
							require( apply_filters( 'wyz_featured_slider_bus_template', plugin_dir_path( __FILE__ ) . 'template/business-2.php' ) ); 
							$count++;
						endwhile;
					}
					?>
					</div>
				</div>
			</div>

			<?php wp_reset_postdata();
			}
			return ob_get_clean();
		}

		public function include_slider_script() {
			wp_enqueue_script( 'wyz_featured_script' );
		}
	}
}
