<?php
/**
 * WYZI Categories Slider
 *
 * @package wyz
 */

// Block direct requests
if ( ! defined( 'ABSPATH' ) ) {
	wp_die('No cheating');
}

if( ! class_exists( 'WYZICategoriesSlider' ) ) {

	class WYZICategoriesSlider {

		private $cat_attr;
		private static $css_loaded = false;
		private $style;
		private $classes;

		public function __construct( $attr ) {
			$this->cat_attr = $attr;
			$this->style = isset( $this->cat_attr['style'] ) ? $this->cat_attr['style'] : 1;
			$this->classes = WYZISlidersFactory::get_slider_css_classes( $this->cat_attr );
			add_action( 'wp_footer', array( &$this, 'include_cat_script') , 4 );
			$this->setup_categories();
		}

		private function setup_categories() {
			$business_taxonomy = array();
			$taxonomy = 'wyz_business_category';
			$temp_link;
			$tax_terms = get_terms( $taxonomy, array( 'hide_empty' => false ,'exclude' => explode(",",$this->cat_attr['category_exclude'])) );
			$count = 0;
			$length = count( $tax_terms );
			if( isset( $this->cat_attr['child_depth'] ) ) {
				if( empty( $this->cat_attr['child_depth'] ) && 0 != $this->cat_attr['child_depth'] )
					$this->cat_attr['child_depth'] = 4;
				else
					$child_depth = $this->cat_attr['child_depth'];
			} else
				$child_depth = 4 ;
			$max_child_depth = 0;

			for ( $i = 0; $i < $length; $i++ ) {
				if ( ! isset( $tax_terms[ $i ] ) )
					continue;
				$current = $tax_terms[ $i ];
				$tax_id = intval( $current->term_id );

				if ( 0 != $current->parent ) {
					continue;
				}

				$children = array();
				$tx_all_children = array();
				$tax = array();
				$tax['name'] = $current->name;
				$tax['id'] = $current->term_id;
				$tax['has_children'] = false;
				$child_count = 0;
				$total_child_count = 0;
				$tax['color'] = get_term_meta( $tax_id, 'wyz_business_cat_bg_color', true );
				$temp_link = get_term_link( $current, $taxonomy );
				$tax['link'] = ( ! is_wp_error( $temp_link ) ? $temp_link : '' );
				$url = WyzHelpers::get_category_icon( $tax_id );
				$tax['img'] = ( false != $url ? $url : '' );
				$tax['view_all'] = false;

				$length_2 = count( $tax_terms );

				for ( $j = 0; $j<$length_2; $j++ ) {
					if ( ! isset( $tax_terms[ $j ] ) ) continue;
					if ( $tax_terms[ $j ]->parent == $current->term_id ) {
						$child = $tax_terms[ $j ];
						$tax['has_children'] = true;
						$total_child_count++;
						$child_count++;
						if ( $child_count >$child_depth) {
							$tax['view_all'] = true;
							continue;
						}
						if ( $j > $max_child_depth ) $max_child_depth = $child_count;
						$bus_count = ( ( ! isset( $this->cat_attr['hide_count'] ) || !$this->cat_attr['hide_count'] ) ? $child->count : 0 );
						$temp_child = array();
						$temp_link = get_term_link( $child->term_id, $taxonomy );
						$tx_all_children[] = $child->name;
						$temp_child['id'] = $child->term_id;
						$temp_child['name'] = $child->name;
						$temp_child['bus_count'] = $bus_count;
						$temp_child['link'] = ( ! is_wp_error( $temp_link ) ? $temp_link : '' );
						$children[] = $temp_child;

					}
				}
				$tax['children'] = $children;
				$tax['all_children'] = $tx_all_children;
				$tax['child_count'] = $total_child_count;
				$business_taxonomy[] = $tax;
				$count++;
			}

			echo '<style>.category-item{min-height:' . ( 50 + 14 + 32*(1+$max_child_depth ) )  . 'px;}</style>';
			$cat_slide_data = apply_filters( 'wyz_categories_slider_script_localize', array(
				'taxs' => $business_taxonomy,
				'nav' => $this->cat_attr['nav'],
				'autoplay' => $this->cat_attr['autoplay'],
				'autoplay_timeout' => $this->cat_attr['autoplay_timeout'],
				'loop' => $count > 1 ? $this->cat_attr['loop'] : false,
				'rows' => $this->cat_attr['rows'],
				'viewAll' => esc_html__( 'View All', 'wyzi-business-finder' ),
				'columns' => $this->cat_attr['columns'],
				'style' => $this->style,
				'cssClasses' => $this->classes
			), 1 );
			wp_localize_script( 'wyz_categories_script', 'catSlide', $cat_slide_data );

		}


		public function include_cat_script() {
			wp_enqueue_script( 'wyz_categories_script' );
		}

		private function include_inline_style(){
			if ( self::$css_loaded || 2 != $this->style ) return;
			self::$css_loaded = true;
			?>
			<style>
				.category-item ul li a{
					display: block;
					white-space: nowrap;
					overflow: hidden;
					-ms-text-overflow: ellipsis;
					-o-text-overflow: ellipsis;
					text-overflow: ellipsis;
					-webkit-font-smoothing: antialiased;
				}
				#baraCautare {
				    margin-top: -230px;
				    margin-bottom: 150px;
				    background:  white;
				    padding-top: 20px;
				    border-radius: 3px;
				    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.32);
				}
				.category-search-area .owl-stage {
				    margin-top: 10px;
				}
				.category-search-area .sin-cat-item .cat-icon {
				    width: 35px;
				    height: 35px;
				    margin: 10px;
				}
				.category-search-area .sin-cat-item .cat-icon>img{
				    width: 22px;
				    margin: 6px auto;
				}
				 .category-search-area .section-title h1 {
				    margin: 0 auto;
				    float: none;
				    display:  table;
				}
				 .category-search-area .cat-list-cont {
				    margin-left: 0;
				    overflow:  visible;
				    position: relative;
				}
				 .category-search-area .cat-list-cont>h3 {
				    box-shadow: 2px 0px 5px 0 rgba(0,0,0,0.16), 2px 0px 10px 0 rgba(0,0,0,0.12);
				}
				 .category-search-area .cat-list-cont>h3>a{
				    border-left: 4px solid;
				    display: block;
				    padding: 4px 8px;
				}
				 .category-search-area .category-item {
				    overflow:  visible;
				}
				 .category-search-area .cat-list-cont ul {
				    position: absolute;
				    width: 100%;
				    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.32);
				    margin-top: 0;
				    background:  white;
				    border-top: 1px solid rgba(0, 0, 0, 0.14);
				    z-index: 9999;
				    display:none
				}
				 .category-search-area .cat-list-cont ul a{
				    padding: 4px 8px;
				    border-bottom: 1px solid rgba(0, 0, 0, 0.14);
				}
				 .category-search-area .category-item {min-height:auto;margin-bottom: 26px;}
				 .category-search-area .cat-list-cont:hover>ul {
				    display:  block;
				}
				 .category-search-area .owl-item {
				    opacity:  1;
				    padding-right: 5px;
				}
				.category-search-area .wyz-search-form {
					margin-top: -70px;
				}
				.category-search-area .owl-carousel .owl-stage-outer {
					overflow: visible;
				}
				@media screen and (max-width:480px){
				     .category-search-area .cat-list-cont>h3:after{
				     	background-color: #fff;
				        font-family:FontAwesome;
				        content: "\f078";
				        display:inline-block;
			            height: 52px;
				        width: 40px;
				        font-size: 14px;
				        text-align:  center;
				        position: absolute;
				        top: 0;
				        right: 0;
				        border-left: 1px solid rgba(0, 0, 0, 0.12);
				    }
				}
			</style>
			<?php
		}

		public function the_categories_slider() {
			ob_start();
			$edg2edg = ( isset( $this->cat_attr['edg-to-edg'] ) && $this->cat_attr['edg-to-edg'] ? ' edg2edg' : '' );
			$this->include_inline_style();
			?>
			<div class="category-search-area margin-bottom-50">
				<div class="row">
					<!-- Section Title & Search -->
					<div class="section-title section-title-search col-xs-12">
						<h1><?php echo esc_html( $this->cat_attr['cat_slider_ttl'] );?></h1>
					</div>
					<div class="col-xs-12">
						<div class="wyz-search-form float-right">
							<input id="categories-search-text" type="text" placeholder="<?php esc_html_e( 'categories', 'wyzi-business-finder' );?>" name="q" />
							<!-- <button id="categories-search-submit" class="wyz-primary-color wyz-prim-color"><i class="fa fa-search"></i></button> -->
						</div>
						<div class="owl-carousel category-search-slider<?php echo $edg2edg;?>"></div>
					</div>
				</div>
			</div>
			<?php
			return ob_get_clean();
		}
	}
}