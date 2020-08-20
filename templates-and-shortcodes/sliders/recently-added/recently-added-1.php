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

if( ! class_exists( 'WYZIRecentlyAddedSlider' ) ) {

	class WYZIRecentlyAddedSlider {

		private $slider_attr;
		private $classes;

		public function __construct( $attr ) {
			$this->slider_attr = $attr;
			$this->classes = WYZISlidersFactory::get_slider_css_classes( $this->slider_attr );
			add_action( 'wp_footer', array( &$this, 'include_slider_script') , 6 );
		}

		public function the_rec_added_slider( $ids = array() ) {
			if ( ! empty( $ids ) ) {
				$posts = new WP_Query( array(
					'post__in' => $ids,
					'orderby' => 'post__in',
					'post_type' => 'wyz_business',
					'post_status' => 'publish',
					'posts_per_page' => -1
				));
			} else {
				$args = array(
					'posts_per_page' => $this->slider_attr['count'],
					'offset' => 0,
					'orderby' => 'date',
					'order' => 'DESC',
					'post_type' => 'wyz_business',
					'post_status' => 'publish',
				);
				$posts = new WP_Query( $args );
			}
			$count = 0;
			$edg2edg = ( isset( $this->slider_attr['edg-to-edg'] ) && $this->slider_attr['edg-to-edg'] ? ' edg2edg' : '' );
			$levit =  ( isset( $this->slider_attr['edg-to-edg'] ) && $this->slider_attr['edg-to-edg'] ? ' item-levit' : '' );
			ob_start();
			?>

			<div class="recently-added-area margin-bottom-50">
				<div class="row">
					<!-- Section Title -->
					<?php if ( isset( $this->slider_attr['rec_added_slider_ttl'] ) ) { ?>
					<div class="section-title col-xs-12 margin-bottom-50">
						<h1><?php echo esc_html( $this->slider_attr['rec_added_slider_ttl'] );?></h1>
					</div>
					<?php }?>
					<div class="col-xs-12">
						<!-- Recently Added Slider -->
						<div class="owl-carousel recently-added-slider<?php echo $edg2edg;?>">
						<?php while ( $posts->have_posts() ) :
							$posts->the_post();
							require( apply_filters( 'wyz_rec_added_slider_bus_template', plugin_dir_path( __FILE__ ) . 'template/business-1.php' ) ); 
							$count++;
						endwhile;
						wp_reset_postdata();
						?>
						</div>
					</div>
				</div>
			</div>
			<?php $rec_add_slide_data = array(
				'nav' => $this->slider_attr['nav'],
				'autoplay' => $this->slider_attr['autoplay'],
				'autoplay_timeout' => $this->slider_attr['autoplay_timeout'],
				'loop' => $count > 1 ? $this->slider_attr['loop'] : false,
				'cssClasses' => $this->classes,
			);
			wp_localize_script( 'wyz_rec_added_script', 'recAddSlide', $rec_add_slide_data );
			return ob_get_clean();
		}

		public function include_slider_script() {
			wp_enqueue_script( 'wyz_rec_added_script' );
		}
	}
}
