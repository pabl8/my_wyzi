<?php
/**
 * Business wall template
 *
 * @package wyz
 */

/**
 * Fired by the shortcode to display businesses wall.
 *
 * @param array $atts shortcode attributes.
 */

global $show_wall_footer;

function wyz_display_wall( $atts ) {

	global $show_wall_footer;

	$atts = shortcode_atts( array( 'posts_pull' => '10', 'pull_method' => 'auto','display_footer' => 0, 'only_fav' => 0, 'category' => '' ), $atts );
	$show_wall_footer = $atts['display_footer'];
	wp_get_current_user();

	wp_enqueue_script( 'wyz_business_post_like' );
	$randomnumber = uniqid();
	wyz_localize_scripts_wall($randomnumber);

	ob_start();
	
	$randomID_head = "postswrapper" . $randomnumber;
	$randomID_footer = "loadmoreajaxloader" . $randomnumber;
	echo '<div id="'.$randomID_head.'" data-token="' . $randomnumber . '">';
	
	wp_localize_script( 'wyz_wall_js', 'walll'. $randomnumber, array( 
		'ind' => isset( $last_index ) ? $last_index : -1,
		'posts_pull' => $atts['posts_pull'],
		'pull_method' => $atts['pull_method'],
		'category' => $atts['category'],
		'onlyFav' => $atts['only_fav'] ? 'yes' : 'no',
		'randomID' => '#' . $randomID_head,
		'randomIDfooter' => '#' . $randomID_footer,
		'divrandomIDfooter' => 'div#' . $randomID_footer,
	) );

	echo '</div>';
	echo '<div id="'.$randomID_footer.'" class="blog-pagination" style="opacity:0;"><div class="loading-spinner"><div class="dot1 wyz-primary-color wyz-prim-color"></div><div class="dot2 wyz-primary-color wyz-prim-color"></div></div></div>';

	return ob_get_clean();
}

function wyz_localize_scripts_wall($randomID) { 

add_action( 'wp_footer', function() use ( $randomID )  {
    

/**
 * Localize wall scripts.
 */

	global $wpdb;
	global $current_user;
	wp_get_current_user();
	
	$args = array(
		'post_type' => 'wyz_business_post',
		'post_status' => 'publish',
		'posts_per_page' => 1,
	);
	$query = new WP_Query( $args );
	$have_posts = $query->have_posts();
	if ( $have_posts ) {
		$po = $query->the_post();
		wp_reset_postdata();
	}

	$wall_data = array(
		'hasPosts'      => $have_posts,
		'postIndx'      => -1,
		'loggedInUser'  => wp_json_encode( is_user_logged_in() ),
		'noPostsMsg' => esc_html__( 'No more posts to show.', 'wyzi-business-finder' ),
		'loadMoreMsg' => esc_html__( 'Load More', 'wyzi-business-finder' ),
		'loginPermalink' => home_url( '/signup/' ),
		'likePostq' => esc_html__( 'Like this post?', 'wyzi-business-finder' ),
		'loginLike' => esc_html__( 'Sign up to make your opinion count', 'wyzi-business-finder' ),
	);
	wp_localize_script( 'wyz_wall_js', 'wall' . $randomID, $wall_data );
	wp_enqueue_script( 'wyz_wall_js' );
	wp_enqueue_script( 'jQuery-inview' );
});
} 
?>
