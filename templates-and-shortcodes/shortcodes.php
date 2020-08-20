<?php
/**
 * WIZY Shortcodes
 *
 * @package wyz
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}


static $locations = array();
static $categories = array();
static $taxonomies = array();
static $offer_categories = array();

global $template_type;
function wyz_register_shortcodes() {
	global $template_type;

	$template_type = 1;
	if ( function_exists( 'wyz_get_theme_template' ) )
		$template_type = wyz_get_theme_template();

	require_once( plugin_dir_path( __FILE__ ) . 'map-shortcode.php' );
	add_shortcode( 'wyz_offers', 'wyz_get_offers' );
	add_shortcode( 'wyz_locations', 'wyz_get_locations' );
	add_shortcode( 'wyz_recently_added', 'wyz_get_recently_added' );
	add_shortcode( 'wyz_featured', 'wyz_get_featured' );
	add_shortcode( 'wyz_categories', 'wyz_get_categories' );
	add_shortcode( 'wyz_all_businesses', 'wyz_get_all_businesses' );
	add_shortcode( 'wyz_all_offers', 'wyz_get_all_offers' );
	add_shortcode( 'wyz_iframe', 'wyz_iframe' );
	//add_shortcode( 'wyz_info_section', 'wyz_info_section' );
	add_shortcode( 'wyz_claim_form_display', 'wyz_claim_form_func' );
	add_shortcode( 'wyz_header_filters','wyz_get_header_filters'  );
	add_shortcode( 'wyz_header_filters2','wyz_get_header_filters2'  );

}
add_action( 'init', 'wyz_register_shortcodes' );

global $map_enqueued;
add_action('wp_enqueue_scripts', function(){
	//die('enqueue');
	global $map_enqueued;
	if ($map_enqueued)return;
	$map_enqueued = true;
	global $post;
	if (!is_object($post) ) return;
	$language = get_bloginfo( 'language' );
	//wp_enqueue_script( 'wyz_map_shortcode_api', '//maps.googleapis.com/maps/api/js?libraries=places&language='.$language.'&key=' . get_option( 'wyz_map_api_key' ) . '&callback=wyz_init_load_map_shortcode_callback#asyncload', array( 'jquery' ) );
});



function wyz_get_categories( $wyz_cat_attr ) {
	global $template_type;
	$attr = array( 'cat_slider_ttl' => '', 'nav' => false, 'autoplay' => false, 'edg-to-edg'=>false, 'autoplay_timeout' => 2000, 'rows' => 1, 'columns' => 4, 'child_depth' => 4, 'loop' => false, 'hide_count' => false,'category_exclude' => '' );
	if ( 1 == $template_type )
		$attr['style'] = 1;

	$attr['lg-desktop-col'] = '4';
	$attr['md-desktop-col'] = '3';
	$attr['tablet-col'] = '2';
	$attr['mobile-col'] = '1';

	$wyz_cat_attr = shortcode_atts( $attr, $wyz_cat_attr );
	return WYZISlidersFactory::the_categories_slider( $wyz_cat_attr );
}

function wyz_get_locations( $wyz_loc_attr ) {

	global $template_type;
	$attr = array( 'loc_slider_ttl' => '', 'nav' => false, 'autoplay' => false, 'edg-to-edg'=>false, 'autoplay_timeout' => 2000, 'rows' => 1, 'loop' => false, 'linking' => false );
	if ( 1 == $template_type ) {
		$attr['lg-desktop-col'] = '4';
		$attr['md-desktop-col'] = '3';
		$attr['tablet-col'] = '2';
		$attr['mobile-col'] = '1';
	}
	$wyz_loc_attr = shortcode_atts( $attr, $wyz_loc_attr );

	return WYZISlidersFactory::the_locations_slider( $wyz_loc_attr );
}

function wyz_get_offers( $wyz_offrs_attr ) {

	$wyz_offrs_attr = shortcode_atts( array( 'offer_slider_ttl' => '', 'nav' => false, 'autoplay' => false, 'edg-to-edg'=>false, 'autoplay_timeout' => 2000, 'count' => '', 'loop' => false, 'autoheight' => true, 'category' => '' ), $wyz_offrs_attr );
	
	return WYZISlidersFactory::the_offers_slider( $wyz_offrs_attr );
}

function wyz_get_recently_added( $wyz_rec_add_attr ) {

	global $template_type;
	$attr = array( 'rec_added_slider_ttl' => '', 'nav' => false, 'autoplay' => false, 'edg-to-edg'=>false, 'autoplay_timeout' => 2000, 'rows' => 1, 'loop' => false, 'count' => 10 );
	if ( 1 == $template_type ) {
		$attr['lg-desktop-col'] = '4';
		$attr['md-desktop-col'] = '3';
		$attr['tablet-col'] = '2';
		$attr['mobile-col'] = '1';
	}

	$wyz_rec_add_attr = shortcode_atts( $attr, $wyz_rec_add_attr );

	return WYZISlidersFactory::the_rec_added_slider( $wyz_rec_add_attr );
}

function wyz_get_featured( $wyz_featured_attr ) {
	global $template_type;
	$attr = array( 'featured_slider_ttl' => '', 'nav' => false, 'autoplay' => false, 'edg-to-edg'=>false, 'autoplay_timeout' => 2000, 'rows' => 1, 'loop' => false, 'count' => 10 );
	$attr['lg-desktop-col'] = '3';
	$attr['md-desktop-col'] = '3';
	$attr['tablet-col'] = '2';
	$attr['mobile-col'] = '1';

	$wyz_featured_attr = shortcode_atts( $attr, $wyz_featured_attr );
	
	return WYZISlidersFactory::the_featured_slider( $wyz_featured_attr );
}

function wyz_get_all_businesses( $wyz_all_bus_attr ) {

	global $template_type;

	$wyz_all_bus_attr = shortcode_atts( array( 'ess_grid' => '','count' => 10, 'country' => '', 'category' => '' ), $wyz_all_bus_attr );
	$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;

	if ( empty( $wyz_all_bus_attr['ess_grid'] ) && 2==$template_type && function_exists( 'wyz_get_option' ) )
		$wyz_all_bus_attr['ess_grid'] = wyz_get_option( 'listing_archives_ess_grid' );

	$args = array(
		'post_type' => 'wyz_business',
		'posts_per_page' => $wyz_all_bus_attr['count'],
		'orderby'=> 'menu_order',
		'post_status' => array( 'publish' ),
		'paged'=>$paged,

	);

	if ( ! empty( $wyz_all_bus_attr['country'] ) ) {
		$args['meta_query'] = array(
			array(
				'key' => 'wyz_business_country',
				'value' => $wyz_all_bus_attr['country']
			)
		);
	}

	if ( ! empty( $wyz_all_bus_attr['category'] ) ) {
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'wyz_business_category',
				'field' => 'term_id',
				'terms' => array( $wyz_all_bus_attr['category'] )
			)
		);
	}
	global $wp_query;

	$wp_query = WyzHelpers::query_businesses( $args, true );

	$content = '';

	if ( ! empty( $wyz_all_bus_attr['ess_grid'] ) ) {
		if ( ! empty( $wyz_all_bus_attr['ess_grid']  ) && $wp_query->have_posts() ) {

			if ( function_exists( 'wyz_get_option' ) ) {
				$ids = array();
				foreach ($wp_query->posts as $post) {
					$ids[] = $post->ID;
				}
				
				$content = do_shortcode( '[ess_grid alias="' . $wyz_all_bus_attr['ess_grid'] .'" posts='.implode(',',$ids).']' );
			}
		}
	} elseif( 1 == $template_type ) {

		while( $wp_query->have_posts() ):
			$wp_query->the_post();
			$content .= WyzBusinessPost::wyz_create_business();
		endwhile;
	}
	wp_reset_postdata();
	if ( function_exists( 'wyz_pagination' ) ) $content .= wyz_pagination( true );

	return $content;

}


function wyz_iframe( $attr ) {
	$attr = shortcode_atts( array( 'src'=>'', 'allowfullscreen' => '' ), $attr );
	echo '<div class="wyz_iframe-container"><iframe src="' . $attr['src'] . '" ';
	if ( $attr['allowfullscreen'] )
		echo ' allowfullscreen';
	echo '></iframe></div>';
}

function wyz_get_all_offers( $wyz_all_offers_attr ) {

	$wyz_all_offers_attr = shortcode_atts( array( 'count' => 10 ), $wyz_all_offers_attr );
	$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;

	$args = array(
		'post_type' => 'wyz_offers',
		'posts_per_page' => $wyz_all_offers_attr['count'],
		'orderby'=> 'menu_order',
		'post_status' => array( 'publish' ),
		'paged'=>$paged,
	);
	$args = apply_filters( 'wyz_all_offers_shortcode_query', $args );

	global $wp_query;

	$wp_query = new WP_Query( $args );

	do_action( 'wyz_before_all_offers_shortcode' );

	while( $wp_query->have_posts() ):
		$wp_query->the_post();
		echo WyzOffer::wyz_the_offer( get_the_ID(), true );
	endwhile;?>
	<?php if ( function_exists( 'wyz_pagination' ) ) wyz_pagination();?>
	<?php wp_reset_postdata();
}

function wyz_info_section( $attr ) {
	$attr = shortcode_atts( array( 'title' => '', 'content' => '' ), $attr );
	?>
	<div class="mb-50">
		<div class="container">
			<!-- Section Title -->
			<div class="row">
				<div class="section-title text-center col-xs-12 mb-50">
					<h2><?php echo $attr['title'];?></h2>
					<p><?php echo $attr['content'];?></p>
				</div>
			</div>
		</div>
	</div>
	<?php
}

function wyz_get_header_filters ( $attr ) {
	$attr = shortcode_atts( array( 'filters_order' => "1,2,3,4" ), $attr );
	$indexes = explode( ',', $attr['filters_order'] );
	for( $i=0;$i<count($indexes);$i++){
		$indexes[$i] = intval($indexes[$i]);
	}
	return WyzHelpers::wyz_get_business_filters( $indexes, false );
}

function wyz_get_header_filters2 ( $attr ) {
	$attr = shortcode_atts( array( 'filters_order' => "" ), $attr );
	return WyzBusinessFiltersFactory::get_filters( $attr['filters_order'] );
}


function wyz_claim_form_func() {
	$wyz_claim_registration_form_data = get_option( 'wyz_claim_registration_form_data' );
	require_once( plugin_dir_path( __FILE__ ) . '../claim/claim_registration_form_front_end.php' );
}

// Visual composer shortcodes map additions.
function wyz_vc_shortcodes_integrate() {
	global $locations;
	global $categories;
	global $template_type;
	global $offer_categories;

	$template_type = 1;
	if ( function_exists( 'wyz_get_theme_template' ) )
		$template_type = wyz_get_theme_template();

	if( empty( $locations ) ) {
		$locations = WyzHelpers::get_business_locations_dropdown_format(true);
	}
	
	if( empty( $taxonomies ) ) {

		$taxonomies = WyzHelpers::get_business_categories();
		$all_tax = array();
		foreach ( $taxonomies as $tax ) {
			$all_tax[] = array(
				'label' => $tax['name'],
				'value' => $tax['id']
			);
			foreach ($tax['children'] as $child) {
				$all_tax[] = array(
					'label' => $child['name'],
					'value' => $child['id']
				);
			}
		}


		$categories = WyzHelpers::get_business_categories_dropdown_format(true);
		$offer_categories = WyzHelpers::get_offers_categories_dropdown_format(true);
		wp_reset_postdata();
	}


	
	$offers_vc_map = array(
		"name" => WYZ_OFFERS_CPT . ' ' . esc_html__( "Slider", 'wyzi-business-finder' ), 
		"base" => "wyz_offers", 
		"class" => "", 
		"category" => esc_html__( "Wyzi Content", 'wyzi-business-finder' ), 
		'admin_enqueue_js' => array(), 
		'admin_enqueue_css' => array(), 
		"description" => '', 
		"params" => array(
			array( 
				"type" => "textfield", 
				"holder" => "div", 
				"class" => "", 
				"heading" => esc_html__( "Slider Title", 'wyzi-business-finder' ), 
				"param_name" => "offer_slider_ttl", 
				"value" => '', 
				"description" => esc_html__( "Defaults to ''", 'wyzi-business-finder' ) 
				),
			array( 
				"type" => "checkbox", 
				"holder" => "div", 
				"class" => "", 
				"heading" => esc_html__( "Navigation", 'wyzi-business-finder' ), 
				"param_name" => "nav", 
				"value" => '', 
				"description" => esc_html__( "Display navigation arrows or not.", 'wyzi-business-finder' ) 
				), 
			array( 
				"type" => "checkbox", 
				"holder" => "div", 
				"class" => "", 
				"heading" => esc_html__( "Autoplay", 'wyzi-business-finder' ), 
				"param_name" => "autoplay", 
				"value" => '', 
				"description" => esc_html__( "Enable Silder Autoplay", 'wyzi-business-finder' ) 
				),
			array( 
			 	"type" => "textfield", 
			 	"holder" => "div", 
			 	"class" => "", 
			 	"heading" => esc_html__( "Autoplay Timeout", 'wyzi-business-finder' ), 
			 	"param_name" => "autoplay_timeout", 
			 	"value" => 2000, 
			 	"description" => esc_html__( "In case 'Autoplay' is enabled, how much time between animations (millisecond)", 'wyzi-business-finder' ) 
			 	),
			array( 
			 	"type" => "textfield", 
			 	"holder" => "div", 
			 	"class" => "", 
			 	"heading" => esc_html__( "Count", 'wyzi-business-finder' ), 
			 	"param_name" => "count", 
			 	"value" => 0, 
			 	"description" => esc_html__( "How many Offers to display (0 to display all of them)", 'wyzi-business-finder' ) 
			 	),
			 array( 
					"type" => "dropdown", 
					"value" => $offer_categories, 
					"holder" => "div", 
					"class" => "", 
					"heading" => esc_html__( "Of specific Category", 'wyzi-business-finder' ), 
					"param_name" => "category", 
					"description" => ''
				),
			array( 
				"type" => "checkbox", 
				"holder" => "div", 
				"class" => "", 
				"heading" => esc_html__( "Loop", 'wyzi-business-finder' ), 
				"param_name" => "loop",
				"value" => '', 
				"description" => esc_html__( "Loop through Offers", 'wyzi-business-finder' )
				), 
			array( "type" => "checkbox", 
				"holder" => "div", 
				"class" => "", 
				"heading" => esc_html__( "Auto Height", 'wyzi-business-finder' ), 
				"param_name" => "autoheight", 
				"value" => '', 
				"description" => esc_html__( "Slider height automatically adjusts to content height.", 'wyzi-business-finder' ) 
				) 
			) 
	);

	if ( 1 == $template_type )
		$offers_vc_map['params'][] = array( 
			"type" => "checkbox", 
			"holder" => "div", 
			"class" => "", 
			"heading" => esc_html__( "Edge to Edge", 'wyzi-business-finder' ), 
			"param_name" => "edg-to-edg", 
			"value" => '',
			"description" => esc_html__( "Shows upcomming elements faded. Requires the parent row stretch mode to be set to \"stretch row\". Preferable to have \"Loop\" setting checked for this slider,", 'wyzi-business-finder' ) 
			);
	vc_map( $offers_vc_map );

	$locations_vc_map = array( 
		"name" => LOCATION_CPT . esc_html__( " Slider", 'wyzi-business-finder' ), 
		"base" => "wyz_locations", 
		"class" => "", 
		"category" => esc_html__( "Wyzi Content", 'wyzi-business-finder' ), 
		'admin_enqueue_js' => array(), 
		'admin_enqueue_css' => array(), 
		"description" => '', 
		"params" => array( 
			array( 
				"type" => "textfield", 
				"holder" => "div", 
				"class" => "", 
				"heading" => esc_html__( "Slider Title", 'wyzi-business-finder' ), 
				"param_name" => "loc_slider_ttl", 
				"value" => '', 
				"description" => esc_html__( "Defaults to ''", 'wyzi-business-finder' ) 
				),
			array("type" => "checkbox", 
				"holder" => "div", 
				"class" => "", 
				"heading" => esc_html__( "Navigation", 'wyzi-business-finder' ),
				"param_name" => "nav", 
				"value" => '', 
				"description" => esc_html__( "Display navigation arrows or not.", 'wyzi-business-finder' ) 
				),
			array( 
				"type" => "checkbox", 
				"holder" => "div", 
				"class" => "", 
				"heading" => esc_html__( "Autoplay", 'wyzi-business-finder' ), 
				"param_name" => "autoplay", 
				"value" => '', 
				"description" => esc_html__( "Enable Silder Autoplay", 'wyzi-business-finder' ) 
				), 
			array( 
				"type" => "textfield", 
				"holder" => "div", 
				"class" => "", 
				"heading" => esc_html__( "Aytoplay Timeout", 'wyzi-business-finder' ), 
				"param_name" => "autoplay_timeout", 
				"value" => 2000, 
				"description" => esc_html__( "In case 'Autoplay' is enabled, how much time between animations (millisecond)", 'wyzi-business-finder' ) 
				),
			array(
				"type" => "checkbox", 
				"holder" => "div", 
				"class" => "", 
				"heading" => esc_html__( "Loop", 'wyzi-business-finder' ), 
				"param_name" => "loop", 
				"value" => '',
				"description" => esc_html__( "Loop through Locations.", 'wyzi-business-finder' ) 
				),
			array(
				"type" => "checkbox", 
				"holder" => "div", 
				"class" => "", 
				"heading" => esc_html__( "Linking", 'wyzi-business-finder' ), 
				"param_name" => "linking", 
				"value" => '', 
				"description" => esc_html__( "Each slide links to the corresponding Location CPT archives page.", 'wyzi-business-finder' )
				 ), 
			array( 
				"type" => "textfield", 
				"holder" => "div", 
				"class" => "", 
				"heading" => esc_html__( "Rows", 'wyzi-business-finder' ), 
				"param_name" => "rows", 
				"value" => 1, 
				"description" => esc_html__( "The number of rows you want this slider to have.", 'wyzi-business-finder' ) 
			) 
		) 
	);

	if ( 1 == $template_type ) {
		$locations_vc_map['params'][] = array( 
			"type" => "checkbox", 
			"holder" => "div", 
			"class" => "", 
			"heading" => esc_html__( "Edge to Edge", 'wyzi-business-finder' ), 
			"param_name" => "edg-to-edg", 
			"value" => '',
			"description" => esc_html__( "Shows upcomming elements faded. Requires the parent row stretch mode to be set to \"stretch row\". Preferable to have \"Loop\" setting checked for this slider,", 'wyzi-business-finder' ) 
			);
		$locations_vc_map = wyz_add_columns_param( $locations_vc_map );
	}

	vc_map( $locations_vc_map );


	$rec_added_vc_map = array( 
		"name" => esc_html__( "Recently Added Businesses Slider", 'wyzi-business-finder' ), 
		"base" => "wyz_recently_added", 
		"class" => "", 
		"category" => esc_html__( "Wyzi Content", 'wyzi-business-finder' ), 
		'admin_enqueue_js' => array(), 
		'admin_enqueue_css' => array(), 
		"description" => '', 
		"params" => array( 
			array( 
				"type" => "textfield", 
				"holder" => "div", 
				"class" => "", 
				"heading" => esc_html__( "Slider Title", 'wyzi-business-finder' ), 
				"param_name" => "rec_added_slider_ttl", 
				"value" => '', 
				"description" => esc_html__( "Defaults to ''", 'wyzi-business-finder' ) 
				), 
			array(
				"type" => "checkbox", 
				"holder" => "div", 
				"class" => "", 
				"heading" => esc_html__( "Navigation", 'wyzi-business-finder' ), 
				"param_name" => "nav", 
				"value" => '', 
				"description" => esc_html__( "Display navigation arrows or not.", 'wyzi-business-finder' ) 
				),
			array( 
				"type" => "checkbox", 
				"holder" => "div", 
				"class" => "", 
				"heading" => esc_html__( "Autoplay", 'wyzi-business-finder' ), 
				"param_name" => "autoplay", 
				"value" => '', 
				"description" => esc_html__( "Enable Silder Autoplay", 'wyzi-business-finder' ) 
				), 
			array( 
			 	"type" => "textfield", 
			 	"holder" => "div", 
			 	"class" => "", 
			 	"heading" => esc_html__( "Aytoplay Timeout", 'wyzi-business-finder' ), 
			 	"param_name" => "autoplay_timeout", 
			 	"value" => 2000, 
			 	"description" => esc_html__( "In case 'Autoplay' is enabled, how much time between animations (millisecond)", 'wyzi-business-finder' ) 
			 	),
			array(
				"type" => "checkbox", 
				"holder" => "div", 
				"class" => "", 
				"heading" => esc_html__( "Loop", 'wyzi-business-finder' ), 
				"param_name" => "loop", 
				"value" => '', 
				"description" => esc_html__( "Loop through Businesses.", 'wyzi-business-finder' ) 
				),
			array( 
				"type" => "textfield", 
				"holder" => "div", 
				"class" => "", 
				"heading" => esc_html__( "Count", 'wyzi-business-finder' ), 
				"param_name" => "count", 
				"value" => 10, 
				"description" => esc_html__( "The maximum number of businesses this slider has.", 'wyzi-business-finder' ) 
			) 
		) 
	);

	if ( 1 == $template_type ) {
		$rec_added_vc_map['params'][] = array( 
			"type" => "checkbox", 
			"holder" => "div", 
			"class" => "", 
			"heading" => esc_html__( "Edge to Edge", 'wyzi-business-finder' ), 
			"param_name" => "edg-to-edg", 
			"value" => '',
			"description" => esc_html__( "Shows upcomming elements faded. Requires the parent row stretch mode to be set to \"stretch row\". Preferable to have \"Loop\" setting checked for this slider,", 'wyzi-business-finder' ) 
			);
		$rec_added_vc_map = wyz_add_columns_param( $rec_added_vc_map );
	}

	vc_map( $rec_added_vc_map );


	$featured_vc_map = array( 
		"name" => esc_html__( "Featured Businesses Slider", 'wyzi-business-finder' ), 
		"base" => "wyz_featured", 
		"class" => "", 
		"category" => esc_html__( "Wyzi Content", 'wyzi-business-finder' ), 
		'admin_enqueue_js' => array(), 
		'admin_enqueue_css' => array(), 
		"description" => '', 
		"params" => array( 
			array( 
				"type" => "textfield", 
				"holder" => "div", 
				"class" => "", 
				"heading" => esc_html__( "Slider Title", 'wyzi-business-finder' ), 
				"param_name" => "featured_slider_ttl", 
				"value" => '', 
				"description" => esc_html__( "Defaults to ''", 'wyzi-business-finder' ) 
				), 
			array(
				"type" => "checkbox", 
				"holder" => "div", 
				"class" => "", 
				"heading" => esc_html__( "Navigation", 'wyzi-business-finder' ), 
				"param_name" => "nav", 
				"value" => '', 
				"description" => esc_html__( "Display navigation arrows or not.", 'wyzi-business-finder' ) 
				),
			array( 
				"type" => "checkbox", 
				"holder" => "div", 
				"class" => "", 
				"heading" => esc_html__( "Autoplay", 'wyzi-business-finder' ), 
				"param_name" => "autoplay", 
				"value" => '', 
				"description" => esc_html__( "Enable Silder Autoplay", 'wyzi-business-finder' ) 
				), 
			array( 
			 	"type" => "textfield", 
			 	"holder" => "div", 
			 	"class" => "", 
			 	"heading" => esc_html__( "Aytoplay Timeout", 'wyzi-business-finder' ), 
			 	"param_name" => "autoplay_timeout", 
			 	"value" => 2000, 
			 	"description" => esc_html__( "In case 'Autoplay' is enabled, how much time between animations (millisecond)", 'wyzi-business-finder' ) 
			 	),
			array(
				"type" => "checkbox", 
				"holder" => "div", 
				"class" => "", 
				"heading" => esc_html__( "Loop", 'wyzi-business-finder' ), 
				"param_name" => "loop", 
				"value" => '', 
				"description" => esc_html__( "Loop through Businesses.", 'wyzi-business-finder' ) 
				), 
			array( 
				"type" => "textfield", 
				"holder" => "div", 
				"class" => "", 
				"heading" => esc_html__( "Count", 'wyzi-business-finder' ), 
				"param_name" => "count", 
				"value" => 10, 
				"description" => esc_html__( "The maximum number of businesses this slider has.", 'wyzi-business-finder' ) 
			) 
		) 
	);

	if ( 1 == $template_type )
		$featured_vc_map['params'][] = array(
			"type" => "checkbox", 
			"holder" => "div", 
			"class" => "", 
			"heading" => esc_html__( "Edge to Edge", 'wyzi-business-finder' ), 
			"param_name" => "edg-to-edg", 
			"value" => '',
			"description" => esc_html__( "Shows upcomming elements faded. Requires the parent row stretch mode to be set to \"stretch row\". Preferable to have \"Loop\" setting checked for this slider,", 'wyzi-business-finder' ) 
		);



	$featured_vc_map = wyz_add_columns_param( $featured_vc_map );
	
	vc_map( $featured_vc_map );

	$categories_params = array( 
		array( 
			"type" => "textfield", 
			"holder" => "div", 
			"class" => "", 
			"heading" => esc_html__( "Slider Title", 'wyzi-business-finder' ), 
			"param_name" => "cat_slider_ttl", 
			"value" => '', 
			"description" => esc_html__( "Defaults to ''", 'wyzi-business-finder' ) 
			), 
		array( 
		 	"type" => "autocomplete", 
		 	"holder" => "div", 
		 	"class" => "", 
	 		'settings' => array(
	 			'multiple' => true,
				'values' => $all_tax
		 	),
		 	"heading" => esc_html__( "Exclude", 'wyzi-business-finder' ), 
		 	"param_name" => "category_exclude",
		 	"description" => __( 'Exculde Certain Categories, type three letters at least from a Category Name (leave empty to display all)', 'wyzi-business-finder' ) 
	 	),
		array( 
			"type" => "checkbox", 
			"holder" => "div", 
			"class" => "", 
			"heading" => esc_html__( "Navigation", 'wyzi-business-finder' ), 
			"param_name" => "nav", 
			"value" => '', 
			"description" => esc_html__( "Display navigation arrows or not.", 'wyzi-business-finder' ) 
			),
		array( 
			"type" => "checkbox", 
			"holder" => "div", 
			"class" => "", 
			"heading" => esc_html__( "Autoplay", 'wyzi-business-finder' ), 
			"param_name" => "autoplay", 
			"value" => '', 
			"description" => esc_html__( "Enable Silder Autoplay", 'wyzi-business-finder' ) 
			),
		array( 
		 	"type" => "textfield", 
		 	"holder" => "div", 
		 	"class" => "", 
		 	"heading" => esc_html__( "Aytoplay Timeout", 'wyzi-business-finder' ), 
		 	"param_name" => "autoplay_timeout", 
		 	"value" => 2000, 
		 	"description" => esc_html__( "In case 'Autoplay' is enabled, how much time between animations (millisecond)", 'wyzi-business-finder' ) 
		 	),
		array( 
			"type" => "checkbox", 
			"holder" => "div", 
			"class" => "", 
			"heading" => esc_html__( "Loop", 'wyzi-business-finder' ), 
			"param_name" => "loop", 
			"value" => '', 
			"description" => esc_html__( "Loop through Categories.", 'wyzi-business-finder' ) 
			), 
		array( 
			"type" => "textfield", 
			"holder" => "div", 
			"class" => "", 
			"heading" => esc_html__( "Rows", 'wyzi-business-finder' ), 
			"param_name" => "rows", 
			"value" => 1, 
			"description" => esc_html__( "The number of rows you want this slider to have.", 'wyzi-business-finder' ) 
			),
		array( 
			"type" => "textfield", 
			"holder" => "div", 
			"class" => "", 
			"heading" => esc_html__( "Columns", 'wyzi-business-finder' ), 
			"param_name" => "columns", 
			"value" => 4, 
			"description" => esc_html__( "The number of columns for wide screen", 'wyzi-business-finder' ) 
			),
		array( 
			"type" => "textfield", 
			"holder" => "div", 
			"class" => "", 
			"heading" => esc_html__( "Children count", 'wyzi-business-finder' ), 
			"param_name" => "child_depth", 
			"value" => 4, 
			"description" => esc_html__( "Maximum number of child categories to show per category before displaying \"View More\"", 'wyzi-business-finder' ) 
			),
		array( 
			"type" => "checkbox", 
			"holder" => "div", 
			"class" => "", 
			"heading" => esc_html__( "Hide Businesses Count", 'wyzi-business-finder' ), 
			"param_name" => "hide_count", 
			"value" => '', 
			"description" => '' 
			), 
		);
	if ( 1 == $template_type )
		$categories_params[] = array( 
			"type" => "dropdown", 
			"value" => array(1,2), 
			"holder" => "div", 
			"class" => "", 
			"heading" => esc_html__( "Style", 'wyzi-business-finder' ), 
			"param_name" => "style", 
			"description" => esc_html__( 'Choose slider style. Requires "loop" to be unchecked', 'wyzi-business-finder' ),
		);
	$categories_vc_map = array( 
			"name" => esc_html__( "Business Categories Slider", 'wyzi-business-finder' ), 
			"base" => "wyz_categories", 
			"class" => "", 
			"category" => esc_html__( "Wyzi Content", 'wyzi-business-finder' ), 
			'admin_enqueue_js' => array(), 
			'admin_enqueue_css' => array(), 
			"description" => '', 
			"params" => $categories_params 
			);

	if ( 1 == $template_type )
		$categories_vc_map['params'][] = array(
			"type" => "checkbox", 
			"holder" => "div", 
			"class" => "", 
			"heading" => esc_html__( "Edge to Edge", 'wyzi-business-finder' ), 
			"param_name" => "edg-to-edg", 
			"value" => '',
			"description" => esc_html__( "Shows upcomming elements faded. Requires the parent row stretch mode to be set to \"stretch row\". Preferable to have \"Loop\" setting checked for this slider,", 'wyzi-business-finder' ) 
		);

	$categories_vc_map = wyz_add_columns_param( $categories_vc_map );

	vc_map( $categories_vc_map );

	vc_map( 
		array( 
			"name" => esc_html__( "Registration Form", 'wyzi-business-finder' ), 
			"base" => "wyz_signup_form", 
			"class" => "", 
			"category" => esc_html__( "Wyzi Content", 'wyzi-business-finder' ), 
			'admin_enqueue_js' => array(), 
			'admin_enqueue_css' => array(), 
			"description" => esc_html__( "Display a registration form.", 'wyzi-business-finder' ), 
			"show_settings_on_create" => true 
		) );

	
	$taxonomies = WyzHelpers::get_business_categories();
	$all_tax = array();
	foreach ( $taxonomies as $tax ) {
		$all_tax[] = array(
			'label' => $tax['name'],
			'value' => $tax['id']
		);
		foreach ($tax['children'] as $child) {
			$all_tax[] = array(
				'label' => $child['name'],
				'value' => $child['id']
			);
		}
	}

	vc_map( 
		array( 
			"name" => esc_html__( "Wall", 'wyzi-business-finder' ), 
			"base" => "wyz_business_wall", 
			"class" => "", 
			"category" => esc_html__( "Wyzi Content", 'wyzi-business-finder' ), 
			'admin_enqueue_js' => array(), 
			'admin_enqueue_css' => array(), 
			"description" => esc_html__( "Display businesses wall on this page.", 'wyzi-business-finder' ),
			"params" => array( 
				array( 
					"type" => "textfield", 
					"holder" => "div", 
					"class" => "", 
					"heading" => esc_html__( "Posts pull per page load", 'wyzi-business-finder' ), 
					"param_name" => "posts_pull",
					"value" => '10', 
					"description" => esc_html__( "The number of business posts to pull on each page load.", 'wyzi-business-finder' ) 
				),
				array( 
					"type" => "dropdown", 
					"value" => array('auto','manual'), 
					"holder" => "div", 
					"class" => "", 
					"heading" => esc_html__( "Posts pull method", 'wyzi-business-finder' ), 
					"param_name" => "pull_method", 
					"description" => esc_html__( "Auto: posts are pulled when the end of the list is reached, Manual: user clicks 'Load More' to pull more posts", 'wyzi-business-finder' ),
				),
				array( 
					"type" => "checkbox", 
					"holder" => "div", 
					"class" => "", 
					"heading" => esc_html__( "Display footer", 'wyzi-business-finder' ),
					"value" => '',
					"param_name" => "display_footer", 
					"description" => '', 
				),
				array( 
					"type" => "checkbox", 
					"holder" => "div", 
					"class" => "", 
					"heading" => esc_html__( "Display Only Favorites", 'wyzi-business-finder' ),
					"value" => '',
					"param_name" => "only_fav", 
					"description" => '', 
				),
				array( 
				 	"type" => "autocomplete", 
				 	"holder" => "div", 
				 	"class" => "", 
			 		'settings' => array(
			 			'multiple' => true,
						'values' => $all_tax
				 	),
				 	"heading" => esc_html__( "Category", 'wyzi-business-finder' ), 
				 	"param_name" => "category",
				 	"description" => __( 'Display only posts belonging to businesses of these categories (leave empty to display all)', 'wyzi-business-finder' ) 
			 	),
			),
			"show_settings_on_create" => true 
		) );

	vc_map( 
		array( 
			"name" => esc_html__( "All Businesses", 'wyzi-business-finder' ), 
			"base" => "wyz_all_businesses", 
			"class" => "", 
			"category" => esc_html__( "Wyzi Content", 'wyzi-business-finder' ), 
			'admin_enqueue_js' => array(), 
			'admin_enqueue_css' => array(), 
			"description" => esc_html__( "Display all businesses on this page.", 'wyzi-business-finder' ), 
			"params" => array(
				array( 
					"type" => "textfield", 
					"holder" => "div", 
					"class" => "", 
					"heading" => esc_html__( "Essentil Grid Alias", 'wyzi-business-finder' ), 
					"param_name" => "ess_grid", 
					"value" => '', 
					"description" => esc_html__( "Display your businesses in essential grid format", 'wyzi-business-finder' ) 
				),
				array( 
					"type" => "textfield", 
					"holder" => "div", 
					"class" => "", 
					"heading" => esc_html__( "Posts per page", 'wyzi-business-finder' ), 
					"param_name" => "count", 
					"value" => '10', 
					"description" => esc_html__( "The number of businesses to display per page.", 'wyzi-business-finder' ) 
				),
				array( 
					"type" => "dropdown", 
					"value" => $locations, 
					"holder" => "div", 
					"class" => "", 
					"heading" => esc_html__( "Of specific Country", 'wyzi-business-finder' ), 
					"param_name" => "country", 
					"description" => '',
				),
				array( 
					"type" => "dropdown", 
					"value" => $categories, 
					"holder" => "div", 
					"class" => "", 
					"heading" => esc_html__( "Of specific Category", 'wyzi-business-finder' ), 
					"param_name" => "category", 
					"description" => ''
				),
			), 
			"show_settings_on_create" => true 
		) );


	vc_map( 
		array( 
			"name" => esc_html__( "All", 'wyzi-business-finder' ) . ' ' . WYZ_OFFERS_CPT, 
			"base" => "wyz_all_offers", 
			"class" => "", 
			"category" => esc_html__( "Wyzi Content", 'wyzi-business-finder' ), 
			'admin_enqueue_js' => array(), 
			'admin_enqueue_css' => array(), 
			"description" => sprintf( esc_html__( "Display all %s on this page.", 'wyzi-business-finder' ), WYZ_OFFERS_CPT ),
			"params" => array( 
				array( 
					"type" => "textfield", 
					"holder" => "div", 
					"class" => "", 
					"heading" => esc_html__( "Posts per page", 'wyzi-business-finder' ), 
					"param_name" => "count", 
					"value" => '10', 
					"description" => sprintf( esc_html__( "The number of %s to display per page.", 'wyzi-business-finder' ), WYZ_OFFERS_CPT ) 
				)
			), 
			"show_settings_on_create" => true 
		) );

	vc_map( 
		array( 
			"name" => esc_html__( "Subscription Tables", 'wyzi-business-finder' ), 
			"base" => "pmpro_advanced_levels", 
			"class" => "", 
			"category" => esc_html__( "Wyzi Content", 'wyzi-business-finder' ), 
			'admin_enqueue_js' => array(), 
			'admin_enqueue_css' => array(), 
			"description" => esc_html__( "Display Pricing Tables of Paid Membership Pro", 'wyzi-business-finder' ), 
			"params" => array(
				array( 
					"type" => "textfield", 
					"holder" => "div", 
					"class" => "", 
					"heading" => esc_html__( "Levels", 'wyzi-business-finder' ), 
					"param_name" => "levels", 
					"value" => '', 
					"description" => esc_html__( "Membership Levels Ids, comma Sparated", 'wyzi-business-finder' ) 
				),
				array( 
					"type" => "dropdown", 
					"value" => array('bootstrap'), 
					"holder" => "div", 
					"class" => "", 
					"heading" => esc_html__( "Template", 'wyzi-business-finder' ), 
					"param_name" => "template", 
					"description" => '',
					'save_always'=>true,
				),
				array( 
					"type" => "dropdown", 
					"value" => array('div','table','2col','3col','4col'), 
					"holder" => "div", 
					"class" => "", 
					"heading" => esc_html__( "Layout", 'wyzi-business-finder' ), 
					"param_name" => "layout", 
					"description" => esc_html__( "Tables Layout", 'wyzi-business-finder' ),
				),
				array( 
					"type" => "checkbox", 
					"holder" => "div", 
					"class" => "", 
					"heading" => esc_html__( "Description", 'wyzi-business-finder' ), 
					"param_name" => "description", 
					"value" => "", 
					"description" => esc_html__( "Display Description", 'wyzi-business-finder' ) 
				),
				array( 
					"type" => "textfield", 
					"holder" => "div", 
					"class" => "", 
					"heading" => esc_html__( "Checkout Button", 'wyzi-business-finder' ), 
					"param_name" => "checkout_button", 
					"value" => '', 
					"description" => esc_html__( "Lable for checkout button", 'wyzi-business-finder' ) 
				),
				array( 
					"type" => "dropdown", 
					"value" => array(
						'full',
						'short',
						'hide'
						), 
					"holder" => "div", 
					"class" => "", 
					"heading" => esc_html__( "Price", 'wyzi-business-finder' ), 
					"param_name" => "price", 
					"description" => esc_html__( "How to display the level cost text", 'wyzi-business-finder' ),
				),
			), 
			"show_settings_on_create" => true , 
		) );

	vc_map( 
		array( 
			"name" => esc_html__( "Global Map", 'wyzi-business-finder' ), 
			"base" => "wyz_map", 
			"class" => "", 
			"category" => esc_html__( "Wyzi Content", 'wyzi-business-finder' ), 
			'admin_enqueue_js' => array(), 
			'admin_enqueue_css' => array(), 
			"description" => esc_html__( "Display a map of all businesses", 'wyzi-business-finder' ), 
			"params" => array(
				array( 
					"type" => "textfield",
					"holder" => "div", 
					"class" => "", 
					"heading" => esc_html__( "Height", 'wyzi-business-finder' ), 
					"param_name" => "height", 
					"value" => '', 
					"description" => esc_html__( "Map height", 'wyzi-business-finder' ) 
				),
				array( 
					"type" => "dropdown",
					"value" => array('false','true'), 
					"holder" => "div", 
					"class" => "", 
					"heading" => esc_html__( "Load my location", 'wyzi-business-finder' ), 
					"param_name" => "load-local", 
					"description" => 'Load current user location on page load',
					'save_always'=>true,
				),
				array( 
					"type" => "dropdown",
					"value" => array('Dropdown'=>'dropdown','Google auto complete'=>'text'), 
					"holder" => "div", 
					"class" => "", 
					"heading" => esc_html__( "Location Filter Type", 'wyzi-business-finder' ), 
					"param_name" => "location-filter-type", 
					"description" => '',
					'save_always'=>true,
				),
				array( 
					"type" => "textfield",
					"holder" => "div", 
					"class" => "", 
					"heading" => esc_html__( "Radius Max Value", 'wyzi-business-finder' ), 
					"param_name" => "radius-max-value", 
					"value" => '', 
					"description" => "Radius filter maximum selectable value"
				),
				array( 
					"type" => "textfield",
					"holder" => "div", 
					"class" => "", 
					"heading" => esc_html__( "Radius Step", 'wyzi-business-finder' ), 
					"param_name" => "radius-step", 
					"value" => '1', 
					"description" => "Radius filter step"
				),
				array( 
					"type" => "dropdown",
					"value" => array('false','true'), 
					"holder" => "div", 
					"class" => "", 
					"heading" => esc_html__( "Map Sidebar", 'wyzi-business-finder' ), 
					"param_name" => "sidebar", 
					"description" => '',
					'save_always'=>true,
				),
				array( 
					"type" => "dropdown",
					"value" => array('Standard'=>0,'Silver'=>1,'Retro'=>2,'Dark'=>3,'Night'=>4,'Aubergine'=>5),
					"holder" => "div", 
					"class" => "", 
					"heading" => esc_html__( "Map Skin", 'wyzi-business-finder' ), 
					"param_name" => "skin", 
					"description" => '',
					'save_always'=>true,
				),
				array( 
					"type" => "dropdown",
					"value" => array('false','true'),
					"holder" => "div", 
					"class" => "", 
					"heading" => esc_html__( "Hide POI", 'wyzi-business-finder' ), 
					"param_name" => "hide-poi", 
					"description" => 'Hide map\'s points of interest',
					'save_always'=>true,
				),
			),
			"show_settings_on_create" => true , 
		) );

	vc_map( array( 
		"name" =>  esc_html__( "Header Filters", 'wyzi-business-finder' ), 
		"base" => "wyz_header_filters", 
		"class" => "", 
		"category" => esc_html__( "Wyzi Content", 'wyzi-business-finder' ), 
		'admin_enqueue_js' => array(), 
		'admin_enqueue_css' => array(), 
		"description" => '',
		"params" => array(
			array( 
			 	"type" => "textfield", 
			 	"holder" => "div", 
			 	"class" => "", 
			 	"heading" => esc_html__( "Filters Order", 'wyzi-business-finder' ), 
			 	"param_name" => "filters_order",
			 	"value" => '1,2,3,4',
			 	"description" => __( 'Enter the order of indexes to display the filters in, comma separated.<br/> 1: Keyword filer <br/> 2: Location filter <br/> 3: Category filter.<br/> 4: Open Days Filter.', 'wyzi-business-finder' ) 
		 	),
		 ),
			"show_settings_on_create" => true 
		) );
	vc_map( array( 
		"name" =>  esc_html__( "Custom Search", 'wyzi-business-finder' ), 
		"base" => "wyz_header_filters2", 
		"class" => "", 
		"category" => esc_html__( "Wyzi Content", 'wyzi-business-finder' ), 
		'admin_enqueue_js' => array(), 
		'admin_enqueue_css' => array(), 
		"description" => '',
		"params" => array(
			array( 
			 	"type" => "wyz_business_filter", 
			 	"holder" => "div", 
			 	"class" => "", 
			 	"heading" => esc_html__( "Filters Order", 'wyzi-business-finder' ), 
			 	"param_name" => "filters_order",
			 	"value" => '',
		 	),
		 ),
			"show_settings_on_create" => true 
		) );

}
add_action( 'vc_before_init', 'wyz_vc_shortcodes_integrate', 100000000 );

function wyz_add_columns_param( $arr ) {
	$params = array(
		'lg-desktop-col' => esc_html__( "Number of columns for large desktops", 'wyzi-business-finder' ),
		'md-desktop-col' => esc_html__( "Number of columns for medium desktops", 'wyzi-business-finder' ),
		'tablet-col' => esc_html__( "Number of columns for tablets", 'wyzi-business-finder' ),
		'mobile-col' => esc_html__( "Number of columns for mobile", 'wyzi-business-finder' ),
	);
	foreach ($params as $key => $value) {
		$arr['params'][] = array( 
			"type" => "dropdown", 
			"value" => array(6,4,3,2,1), 
			"holder" => "div", 
			"class" => "", 
			"heading" => $value, 
			"param_name" => $key,
		);
	}

	return $arr;
}

/*
 * add custom business filter param type
 */
function wyz_load_business_filter_type() {
	vc_add_shortcode_param( 'wyz_business_filter' , 'wyz_business_filter_settings_field', plugin_dir_url( __FILE__ ) . 'js/business-filters.js' );

}
add_action( 'vc_load_default_params', 'wyz_load_business_filter_type', 100 );

function wyz_business_filter_settings_field( $settings, $val ) {

	$business_filters = get_option( 'wyz_business_filters', array() );
	if ( empty( $business_filters ) || ! is_array( $business_filters ) ) return;

	ob_start();
	?>
	

	<div id="wyz-business-filter-keys" class="dropdown-check-list" tabindex="100">
        <span class="anchor">Select One</span>
        <ul class="items">
	<?php
	foreach ( $business_filters as $key => $value ) {
		if( $key == 'custom_fields' ) {
			foreach ($value as $key => $v) {
				echo '<li><a id="wyz-filter-' . $key . '" data-label="' . $v['label'] . '" data-value="' . $key . '">' . $v['label'] . '</a></li>';
			}
		}else
			echo '<li><a id="wyz-filter-' . $key . '" data-label="' . $value['label'] . '" data-value="' . $key . '">' . $value['label'] . '</a></li>';
	}?>
		</ul>
    </div>

    <br>
    

    <div id="app" style="width: 100%;">
        <div>
            <div class="layoutJSON">
                <div class="columns">
                    <span class="layoutItem" v-for="item in layout">{{item.i}}:({{item.x}},{{item.y}},{{item.w}},{{item.h}},{{item.o}},{{item.n}})</span>
                </div>
            </div>
        </div>
        <div id="content">
            <!--<button @click="decreaseWidth">Decrease Width</button>
            <button @click="increaseWidth">Increase Width</button>
            <button @click="addItem">Add an item</button>-->
            <grid-layout :layout="layout"
                         :col-num="12"
                         :row-height="30"
                         :is-draggable="true"
                         :is-resizable="true"
                         :vertical-compact="true"
                         :use-css-transforms="true"
            >
                <grid-item v-for="item in layout"
                           :x="item.x"
                           :y="item.y"
                           :w="item.w"
                           :h="2"
                           :i="item.i"
                           :n="item.n"
                           :o="item.o"
                           @resize="resizeEvent"
                           @move="moveEvent"
                           @resized="resizedEvent"
                           @moved="movedEvent"
                >
                	<span class="filter-name">{{item.n}}</span>
                	<button class="remove-filter">X</button>
                    <span class="filter-id">{{item.i}}</span>
                </grid-item>
            </grid-layout>
        </div>

        <?php echo '<div class="my_param_block">'
             .'<input name="' . esc_attr( $settings['param_name'] ) . '" class="wpb_vc_param_value wpb-textinput ' .
             esc_attr( $settings['param_name'] ) . ' ' .
             esc_attr( $settings['type'] ) . '_field" type="text" value="' . esc_attr( $val ) . '" />'
         .'</div>';?>

         <div class="wyz-filter-options-cont"></div>
        
    </div>
	<?php 
	return ob_get_clean();
}

function wyz_require_filters_parent() {
	require_once( plugin_dir_path( __FILE__ ) . 'business-filters/business-filters.php' );
}