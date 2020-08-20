<?php
/**
 * Contains most hooks of the plugin.
 *
 * @package wyz
 */
/*
 * Global variables.
 */
// Missing pages for page creation function.
$missing_pages;

add_action('wp_head',function(){
	if ( ! is_user_logged_in() || ! WyzHelpers::FCM_is_on() ) return;
	echo '<script>var fcm_user_="' . WyzHelpers::encrypt(rand(10, 30) . ':::' . get_current_user_id()) . '";</script>';
});

/*************************/
/*    Schedualed events  */
/************************/

/**
 * Check for offers expiry date.
 */
function wyz_offers_expiry_check() {


	if ( ! function_exists( 'wyz_get_option' ) || 'on' != wyz_get_option( 'new-wall-post-mail-onoff' )  ) return;
		wyz_fav_wall_posts_notify();

	$ex_date = get_option( 'wyz_offer_expiry_date' );
	if ( 1 > $ex_date ) {
		return;
	}
	global $wpdb;
	$date = date( 'Y-m-d', strtotime( '-' . $ex_date . 'days' ) );

	$post_type = 'wyz_offers';

	$query = '
	SELECT ID FROM ' . $wpdb->posts . ' WHERE post_type = \'' . $post_type . '\' AND post_status = \'publish\' AND post_date < \'' . $date . '\'';
	$results = $wpdb->get_results( $wpdb->prepare( 'SELECT ID FROM ' . $wpdb->posts . ' WHERE post_type = \'%s\' AND post_status = \'publish\' AND post_date < \'%s\'', $post_type, $date ) ); // Db call ok; no-cache ok.
	$deleted_posts = $query;
	$deleted_posts .= 'deleted offers: ';

	$results_ids = array();
	foreach ( $results as $post ) {
		$results_ids[] = $post->ID;
		$deleted_posts .= $post->ID . '  ';
		wp_trash_post( $post->ID );
	}
	wyz_delete_offers_wall_posts( $deleted_posts );
}
add_action( 'wyz_daily_event','wyz_offers_expiry_check' );


function wyz_delete_offers_wall_posts( $offers_ids ) {
	if ( ! is_array( $offers_ids ) )
		$offers_ids = array( $offers_ids );

	$offer_posts_query = new WP_Query(array(
		'post_type' => 'wyz_business_post',
		'posts_per_page' => -1,
		'fields' => 'ids',
		'post_status' => array('pending', 'publish'),
		'meta_query' => array(
			array(
				'key' => 'post_offer_id',
				'value' => $offers_ids,
				'compare' => 'IN'
			)
		)
	));

	foreach ( $offer_posts_query as $post_id ) {
		wp_trash_post( $post_id );
	}
}

function wyz_handle_hourly_events() {
	wyz_check_pending_comment_services();
	wyz_check_running_comment_services();
}
add_action( 'wyz_hourly_event', 'wyz_handle_hourly_events' );

function wyz_check_pending_comment_services() {
    $pending_services = get_option( 'wyz_pending_comment_services', array() );
    $running_services = get_option( 'wyz_running_comment_services', array() );

    $current_time = current_time( 'timestamp' );
    for ( $i = 0; $i < count( $pending_services ); $i++ ) {
    	if ( $pending_services[ $i ]['timestamp'] <= $current_time ) {
    		$running_services[] = $pending_services[ $i ];

    		 $comment = get_comment( $pending_services[ $i ]['id'] );
    		 if ( ! $comment )continue;

    		$timestamp = $pending_services[ $i ]['timestamp'];

    		$data = array(
                'comment_post_ID' => $comment->comment_post_ID,
                'comment_content' => '',
                'user_id' => get_post_meta( $comment->comment_post_ID, 'message_sender_id', true ),
                'comment_date' => date( 'Y-m-d H:i:s', $timestamp),
            );

            $serv_start_comment = wp_insert_comment($data);
            update_comment_meta( $serv_start_comment, 'is_serv_start_comment', 'yes' );
    		unset( $pending_services[ $i ] );
    	} else {
    		break;
    	}
    }
    update_option( 'wyz_pending_comment_services', $pending_services );
	update_option( 'wyz_running_comment_services', $running_services );
}


function wyz_check_running_comment_services() {
    $running_services = get_option( 'wyz_running_comment_services', array() );
    $completed_services = get_option( 'wyz_completed_comment_services', array() );

    $current_time = current_time( 'timestamp' );
    for ( $i = 0; $i < count( $running_services ); $i++ ) {
    	if ( get_comment_meta( $running_services[ $i ]['id'], 'service_due_date', true ) <= $current_time ) {
    		$completed_services[] = $running_services[ $i ];
    		unset( $running_services[ $i ] );
    	} else {
    		break;
    	}
    }
    update_option( 'wyz_running_comment_services', $running_services );
	update_option( 'wyz_completed_comment_services', $completed_services );
}

/**
 * Check for offers expiry date.
 */
function wyz_fav_wall_posts_notify() {
	$all_users = get_users( array(
		'meta_query'   => array(
			array(
				'key' => 'wyz_user_favorites',
				'compare' => 'EXISTS',
			)
		),
		'fields' => array( 'ID', 'user_email', 'display_name' )
	));
	foreach ( $all_users as $user ) {
		$output = '';
		$fav = get_user_meta($user->ID, 'wyz_user_favorites', true);
		if ( ! empty( $fav ) ) {
			$args = array(
				'post_type' => 'wyz_business_post',
				'meta_query' => array(
				    array(
				        'key' => 'business_id',
				        'value' => $fav,
				        'compare' => 'IN'
				    )
				),
				'posts_per_page' => -1,
				'orderby' => 'date',
				'order' => 'DESC',
				'date_query' => array(
					array(
						'after' => '1 day ago'
					)
				)
			);
			$query = new WP_Query( $args );
			while ( $query->have_posts() ) {
				$query->the_post();
				$post_id = get_the_ID();
				$post = array();
				$post['ID'] = $post_id;
				$post['business_ID'] = get_post_meta( $post_id, 'business_id', true );
				$post['name'] = get_the_title( $post['business_ID'] );
				$post['post'] = get_the_content();
				$post['likes'] = intval( get_post_meta( $post_id, 'wyz_business_post_likes_count', true ) );
				$post['user_likes'] = get_post_meta( $post_id, 'wyz_business_post_likes', true );
				$post['time'] = get_the_date( get_option( 'date_format' ) );
				$output .= WyzBusinessPost::wyz_create_post_for_email( $post );
			}
			wp_reset_postdata();

			if ( empty( $output ) ) continue;
			$subject = wyz_get_option( 'new-wall-post-email-subject' );
			if ( empty($subject))
				$subject = (esc_html__( 'You got a new Email from', 'wyzi-business-finder' ) . ' {' . home_url() . '}');
			$message = wyz_get_option( 'new-wall-post-email' );

			if ( empty( $message ) )
				$message = 'Dear %USERNAME%, here are the latest posts from your favourite businesses:<br> %NEW_WALL_POSTS%';


			$message = str_replace( '%USERNAME%', $user->display_name , $message );
			$message = str_replace( '%NEW_WALL_POSTS%', $output , $message );

			$mail_sent = WyzHelpers::wyz_mail( $user->user_email, $subject, $message, 'new_wall_post_email' );
		}
	}
}


add_action('wyz_rating_reminder',function(){

	if ( ! function_exists( 'wyz_get_option' ) || 'on' != wyz_get_option( 'rate-reminder-mail-onoff' )  ) return;

	$number_of_days_to_wait_before_sending = 2;

	$end_timestamp = strtotime(date_i18n('Y-m-d H:i:s',current_time('timestamp')).' - '.$number_of_days_to_wait_before_sending.' days');

	$args = array(
		'post_type' => 'booked_appointments',
		'posts_per_page' => -1,
		'post_status' => array('publish'),
		'meta_query' => array(
			'relation' => 'AND',
			array(
				'key'     => '_appointment_timestamp',
				'value'   =>  $end_timestamp,
				'compare' => '<',
			),
			array(
				'key'     => '_appointment_user_rating',
				'compare' => 'NOT EXISTS',
			)
		)
	);


	$bookedAppointments = new WP_Query($args);
	if($bookedAppointments->have_posts()):
		while ($bookedAppointments->have_posts()):
			
			$bookedAppointments->the_post();
			global $post;
			
			$appt_id = $post->ID;
			
			$url = '';
			
			$post_time_stamp = get_post_meta($appt_id,'_appointment_timestamp',true);
			
			$reminder_rating_sent = get_post_meta($appt_id,'_appointment_user_rating',true);
			
			
			$calendar_ids = wp_get_post_terms( $appt_id, 'booked_custom_calendars', array('fields'=>'ids') );
			
			if( ! is_wp_error( $calendar_ids ) ) {
			
				$calendar_id = $calendar_ids[0];
				
				$user_email = get_option("taxonomy_$calendar_id")['notifications_user_id'];
				$user = get_user_by( 'email', $user_email );

				if ( ! $user )
					continue;

				$calendars = get_user_meta( $user->ID, 'wyz_business_calendars', true );
				
				$business_id = -1;
				
				foreach ($calendars as $b_id => $c_id) {
					if ( $c_id == $calendar_id ) {
						$business_id = $b_id;
						break;
					}
				}
				
				if ( $business_id > 0 && FALSE !== get_post_status( $business_id ) ) {
					$tab_data = get_option( 'wyz_business_tabs_order_data' );
					$count = count( $tab_data );
					
					for ( $i=0; $i<$count; $i++ ) {
						if ( $tab_data[ $i ]['type'] == 'ratings' ) {
							if ( ! isset( $tab_data[ $i ]['urlid'] ) )
								$tab_data[ $i ]['urlid'] = urlencode( $tab_data[ $i ]['type'] );

							$url = get_post_permalink( $business_id ) . '#' . $tab_data[ $i ]['urlid'];
						}

					}

				}
			}		
					
			if ( ! $reminder_rating_sent && !empty($url)) {
				if ( function_exists( 'wyz_get_option' ) ) {
					$rate_email = wyz_get_option( 'rate-reminder-mail' );

					$rate_email = str_replace( '%BUSINESSNAME%', get_the_title( $business_id ), $rate_email );
					$rate_email = str_replace( '%URL%', $url, $rate_email );

					$subject = esc_html__( 'You got a new Email from', 'wyzi-business-finder' ) . ' {' . home_url() . '}';

					do_action( 'booked_reminder_email', get_the_author_meta('user_email'), $subject, $rate_email );
					update_post_meta($appt_id,'_appointment_user_rating',true);
				}
			}
		endwhile;

	endif;

	wp_reset_postdata();

}, 999);
/**
 * Delete all offers, ratings and posts related to a business upon deleting it
 *
 * @param integer $post_id deleted post id.
 */

function wyz_trash_action( $post_id ) {

	if ( 'wyz_offers' == get_post_type( $post_id ) ) {
		$query = new WP_Query( array(
			'post_type' => 'wyz_business_post',
			'posts_per_page' => -1,
			'post_status' => array( 'pending', 'publish' ),
			'meta_query' => array(
				array(
					'key' => 'post_offer_id',
					'value' => $post_id,
				)
			),
			'fields' => 'ids'
		));
		if ( $query->posts && ! empty( $query->posts ) )
			foreach ( $query->posts as $post )
				wp_trash_post( $post );
	}

	if ( 'wyz_business' !== get_post_type( $post_id ) ) {
		return;
	}

	$post = get_post( $post_id );
	$author_id = $post->post_author;

	$user_businesses = get_user_meta( $author_id, 'wyz_user_businesses', true );
	$deleted = false;

	if ( isset( $user_businesses['pending'][ $post_id ] ) ) {
		unset( $user_businesses['pending'][ $post_id ] );
		$deleted = true;
	} elseif ( isset( $user_businesses['published'][ $post_id ] ) ) {
		unset( $user_businesses['published'][ $post_id ] );
		$deleted = true;
	}

	if ( $deleted) {
		$count = intval( get_user_meta( $author_id, 'wyz_user_businesses_count', true ) );
		if ( 0 < $count ) $count--;
		else $count = 0;
		update_user_meta( $author_id, 'wyz_user_businesses_count', $count );
		if ( 0 == $count )
			update_user_meta( $author_id, 'has_business', 0 );
	}

	update_user_meta( $author_id, 'wyz_user_businesses', $user_businesses );

	$args = array(
		'post_type'  => array( 'wyz_offers', 'wyz_business_post', 'wyz_business_rating' ),
		'post_status' => array( 'publish', 'pending' ),
		'meta_query' => array(
			array(
				'key'     => 'business_id',
				'value'   => $post_id,
				'compare' => 'IN',
			),
		),
	);
	$query = new WP_Query( $args );
	while ( $query->have_posts() ) {
		$query->the_post();
		wp_trash_post( get_the_ID() );
	}
	wp_reset_postdata();
}
add_action( 'trashed_post', 'wyz_trash_action' );


/**
 * Publish pending offers and posts for published pending businesses
 *
 * @param integer $business_id the business id.
 */
function wyz_publish_pending_business_posts_offers( $post ) {

	if ( 'job_listing' == $post->post_type ) {
		wyz_job_listing_publish( $post->ID, $post );
		return;
	}

	$user_id = $post->post_author;
	if ( 'wyz_business' == $post->post_type ) {
		$user_businesses = WyzHelpers::get_user_businesses( $user_id );

		if( isset( $user_businesses['pending'][ $post->ID ] ) ) {
			unset( $user_businesses['pending'][ $post->ID ] );
		}
		
		$user_businesses['published'][ $post->ID ] = $post->ID;

		update_user_meta( $user_id, 'wyz_user_businesses', $user_businesses );
		return;
	}

	if ( 'wyz_offers' == $post->post_type && 'pending' == get_post_meta( $post->ID, 'wyz_offer_pending_for_post', true ) )
		WyzHelpers::add_new_offer_post( $post->ID, $user_id );

	$args = array(
		'post_type' => array( 'wyz_offers', 'wyz_business_post' ),
		'post_status' => 'pending',
		'meta_query' => array(
			array(
				'key' => 'business_id',
				'value' => $post->ID,
			),
		),
	);

	$query = new WP_Query( $args );
	while ( $query->have_posts() ) {
		$query->the_post();
		wp_publish_post( get_the_ID() );
	}
	wp_reset_postdata();

	$points_available = 0;
	$points_available = intval( get_the_author_meta( 'points_available', $post->post_author ) );
	if ( $points_available < intval( get_option( 'wyz_offer_point_price' ) ) ) {
		// Nothing to see here.
	} else {
		$points_left = intval( $points_available - intval( get_option( 'wyz_offer_point_price' ) ) );
		update_user_meta( $post->post_author, 'points_available', $points_left );
	}

	if ( function_exists( 'wyz_get_option' ) ) {

		$subject = wyz_get_option( 'business-publish-confirmation-email-subject' );
		if ( empty( $subject ) )
			$subject = esc_html__( 'You got a new Email from', 'wyzi-business-finder' ) . ' {' . home_url() . '}';

		$conf_email = wyz_get_option( 'business-publish-confirmation-email' );

		$user = get_userdata( $user_id );
		$conf_email = str_replace( '%FIRST_NAME%', $user->first_name, $conf_email );
		$conf_email = str_replace( '%LAST_NAME%', $user->last_name, $conf_email );
		$conf_email = str_replace( '%BUSINESSNAME%', $post->post_title, $conf_email );
		$to = $user->user_email;

		WyzHelpers::wyz_mail( $to, $subject, $conf_email, 'business_approve' );
	}
}
add_action( 'pending_to_publish',  'wyz_publish_pending_business_posts_offers', 10, 1 );


/**
 * Offers published, add posts
 */
function wyz_offers_publish_add_posts( $new_submission_id, $user_id ) {
	if ( 'on' == get_option( 'wyz_offer_immediate_publish' ) )
		WyzHelpers::add_new_offer_post( $new_submission_id, $user_id );
	else
		update_post_meta( $new_submission_id, 'wyz_offer_pending_for_post', 'pending' );
}

add_action( 'wyz_frontend_offer_submit' , 'wyz_offers_publish_add_posts', 10, 2 );

/**
 * Customize Wp Query in global map search.
 */
add_action( 'pre_get_posts', function( $q ) {

	$titles = $q->get( '_meta_or_title' );
	if ( '' == $titles || empty( $titles ) ) {
		return;
	}

	if ( ! is_array( $titles ) ) {
		$titles = array( $titles );
	}

	add_filter( 'get_meta_sql', function( $sql ) use ( $titles, $q ) {
		global $wpdb;

		// Only run once:
		static $nr = 0; 

		
		if ( 0 != $nr++ ) {
			return $sql;
		}

		$tax_args = $q->get('my_tax_query');
		$meta_or_tax = $q->get('_meta_or_tax');
		$meta_or_tax = wp_validate_boolean( $meta_or_tax ) ? $meta_or_tax : false;


		$loc_query = '';
		$cat_query = '';
		$tax_query_ids = array();
		$tax_query = '';


		if( $meta_or_tax /*&& is_array( $tax_args ) && ! empty( $tax_args )*/ ) {

			$sql['join'] .= " INNER JOIN {$wpdb->term_relationships} ON ( {$wpdb->posts}.ID = {$wpdb->term_relationships}.object_id ) INNER JOIN {$wpdb->term_taxonomy} ON ( {$wpdb->term_relationships}.term_taxonomy_id =  {$wpdb->term_taxonomy}.term_taxonomy_id )"; 

			if(is_array( $tax_args ) && ! empty( $tax_args )){
				$sql_tax  = get_tax_sql(  $tax_args,  $wpdb->posts, "ID" );

				if( isset( $sql_tax['where'] ) ) {

					$sql['where']  = str_replace( $sql_tax['where'], '', $sql['where'] );
					//$sql['where'] .= sprintf( ' OR  %s ', substr( trim( $sql_tax['where']  ), 4 ) );

					//for when we have category and tag search, add the tax id to list of taxonomy ids,
					//so we don't have a conflict between category ids and tax ids, as all are taxonomies
					$tax_query .= sprintf( ' OR  %s ', substr( trim( $sql_tax['where']  ), 4 ) );
					preg_match( '/\(.*\)/', sprintf( ' OR  %s ', substr( trim( $sql_tax['where']  ), 4 ) ), $tax_query_ids );
					if ( ! empty( $tax_query_ids ) && '' != $tax_query_ids[0] ) {
						$tax_query_ids = substr( $tax_query_ids[0], 1, strlen( $tax_query_ids[0] ) -2 );
					}
				}
			}
		}
		if ( ! empty( $tax_query_ids ) ) {
			$tax_query_ids = explode( ',', $tax_query_ids );
		}

		$cat = $q->get('cat_query');
		$loc = $q->get('loc_query');


		if ( ! empty( $cat ) || ! empty( $loc ) ) {
			if ( ! empty( $cat ) ) {
				if( is_array( $cat ) ) {
					$children = array();
					foreach ($cat as $c) {
						$children[] = get_term_children( $c, 'wyz_business_category' );
						$children[] = $c;
					}
				} else {
					$children = get_term_children( $cat, 'wyz_business_category' );
					$children[] = $cat;
				}
				
				$ls = '';
				foreach ($children as $l ) {
					$ls .= "'$l',";
				}
				/*if ( ! empty( $tax_query_ids ) ) {
					foreach ($tax_query_ids as $l ) {
						$ls .= "'$l',";
					}
				}*/
				$ls = substr( $ls, 0, strlen( $ls ) -1 );
				$cat_query = "( {$wpdb->term_relationships}.term_taxonomy_id IN ($ls) ) AND ";
			}
			if ( ! empty( $loc ) ) {
				$loc_query = "( {$wpdb->postmeta}.meta_key = 'wyz_business_country' AND {$wpdb->postmeta}.meta_value " . ( ( is_array( $loc ) ? 'IN (' . implode(',', $loc) . ')' : "= $loc" ) ) . " ) AND";
			}
		}else if( '' != $tax_query ) {
			$sql['where'] .= $tax_query;
		}


		$quer = array();



		foreach ( $titles as $title ) {
			if ( '' != $title ) {
				array_push( $quer, $wpdb->prepare( "{$wpdb->posts}.post_title like '%s'", '%' . esc_sql( $wpdb->esc_like( $title ) ) . '%' ) );
				array_push( $quer, $wpdb->prepare( "{$wpdb->posts}.post_content like '%s'", '%' . esc_sql( $wpdb->esc_like( $title ) ) . '%' ) );
			}
		}
		if ( ! empty( $quer ) ) {
			$cond = implode(' OR ', $quer ) . ' OR ' . substr( trim( $sql['where']  ), 4 ) . ' OR';
		} else {
			$cond = substr( trim( $sql_tax['where']  ), 4 ) . ' OR';
		}


		$sql['where'] = sprintf(
			" AND %s %s( %s %s ",
			$cat_query,
			$loc_query,
			$cond,
			mb_substr( $sql['where'], 5, mb_strlen( $sql['where'] ) )
		);

		$sql['where'] .= ' )';

		return $sql;
	});
});


/**
 * Add login/sign up/cart menu items to header right meu.
 *
 * @param string $items current menu items.
 * @param object $args used here to know theme location.
 */

function wyz_add_login_out_cart_item_to_menu( $items, $args ) {
	// Change theme location with your them location name.
	global $template_type;

	if ( is_admin() ) {
		return $items;
	}

	$log_ttl = ( 1 == $template_type ? esc_html__( 'Sign in', 'wyzi-business-finder' ) : esc_html__( 'Login', 'wyzi-business-finder' ) );
	$logout_ttl = esc_html__( 'Logout', 'wyzi-business-finder' );
	$reg_ttl = ( 1 == $template_type ? esc_html__( 'Sign up', 'wyzi-business-finder' ) : esc_html__( 'Register', 'wyzi-business-finder' ) );
	$reg_ttl = apply_filters( 'wyz_register_btn_txt', $reg_ttl );
	$log_ttl = apply_filters( 'wyz_login_btn_txt', $log_ttl );
	$logout_ttl = apply_filters( 'wyz_logout_btn_txt', $logout_ttl );

	//add the login menu items to the mobile menu
	if ( 'mobile-main-menu' == $args->menu_id && ( $template_type ==  1 || ( function_exists( 'wyz_get_option' ) && 'on' == wyz_get_option( 'header-login-menu' ) ) ) ) {

		$menu_locations = get_nav_menu_locations();
		if ( isset( $menu_locations['login'] ) ) {
			$menu_id = $menu_locations['login'];
			$login_nav = wp_get_nav_menu_items( $menu_id );

			foreach ( $login_nav as $nav_item )
				$items .= '<li id="menu-item-' . $nav_item->ID . '" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-' . $nav_item->ID . '"><a href="' . $nav_item->url . '">' . $nav_item->title .'</a></li>';
		}
	}




	//if this is main menu and we are in template 2
	if ( 'primary' === $args->theme_location ) {
		$pref = is_user_logged_in() ? '' :'non-';

		//what to display in the 'My Account' button
		if ( is_user_logged_in() ) {
			$user_id = get_current_user_id();
			$macc_title =  esc_html__( 'my account', 'wyzi-business-finder' );
			$cur_mnu_itm = ( is_page( 'user-account' ) ? ' current-menu-item' : '' );
			$macc_select = function_exists( 'wyz_get_option' ) ? wyz_get_option( 'login-btn-content-type' ):'';
			$current_user = get_userdata($user_id);
			switch ( $macc_select ) {
				case 'firstname':
					$macc_title = $current_user->first_name;
					break;
				case 'lastname':
					$macc_title = $current_user->last_name;
					break;
				case 'username':
					$macc_title = $current_user->user_login;
					break;
				case 'custom-text':
					$macc_title = esc_html( wyz_get_option( 'login-btn-custom-text' ) );
					break;
			}

			$macc_title = apply_filters( 'wyz_my_account_btn_title', $macc_title );

    		$items = '<li id="mobile-account-link"><a href="'. esc_url( home_url( '/user-account/' ) ) . '" class="user-logged-in"><span>' . sprintf( esc_html__( 'Welcome %s', 'wyzi-business-finder' ), $macc_title ) . '</span></a></li>'. $items;
    	}


    	//the 'Submit a Place' button
		if ( function_exists( 'wyz_get_option' ) && 'on' == wyz_get_option( $pref.'logged-menu-right-link') && ( 2 == $template_type || 'header4' ==  wyz_get_option( 'header-layout' ) ) ){
			$link_to = function_exists( 'wyz_get_option' ) ? wyz_get_option($pref.'logged-menu-right-link-to') : '';
			$link = '';
			if ( 'page' == $link_to ){
				$link = get_permalink( wyz_get_option($pref.'logged-menu-right-link-page'));
				if (!$link)$link='#';
			}elseif('link'==$link_to)
				$link = esc_url(wyz_get_option($pref.'logged-menu-right-link-link'));
			elseif(is_user_logged_in()&&'add-business'==$link_to && class_exists('WyzQueryVars'))
				$link = add_query_arg( WyzQueryVars::AddNewBusiness, true, home_url( '/user-account') );
			$link_label = function_exists( 'wyz_get_option' ) ? wyz_get_option($pref.'logged-menu-right-link-label'):'';
		
			$items .= '<li id="mobile-right-link"><a href="' . $link . '">' . $link_label . '<i class="fa fa-paper-plane-o"></i></a></li>';
		}

		if ( ! is_user_logged_in() ) {
			$items .= '<li id="mobile-login-link"><a href="' .  home_url( '/signup/?action=login' ) . '">' . $log_ttl . '</a></li>' .
			'<li id="mobile-register-link"><a href="' .  home_url( '/signup/' ) . '">' . $reg_ttl . '</a></li>';
		} else {
			$items .= '<li id="mobile-logout-link"><a href="' . wp_logout_url( home_url() ) . '">' . $logout_ttl . '</a></li>';
		}

		if ( class_exists( 'WooCommerce' ) && 'on' != get_option( 'wyz_woocommerce_hide_menu_cart' ) ) {
			global $woocommerce;
			$viewing_cart = esc_html__('View your shopping cart', 'your-theme-slug');
			$start_shopping = esc_html__('Start shopping', 'your-theme-slug');
			$cart_url = wc_get_cart_url();
			//woocommerce 3.0 compatibility
			if ( function_exists( 'wc_get_page_id' ) ) {
				$shop_page_url = get_permalink( wc_get_page_id( 'shop' ) );
			} else {
				$shop_page_url = get_permalink( woocommerce_get_page_id( 'shop' ) );
			}
			$cart_contents_count = $woocommerce->cart->cart_contents_count;
			$cart_total = $woocommerce->cart->get_cart_total();
			if ($cart_contents_count == 0) {
				$items .= '<li class="wcmenucart' . ( 'on' != get_option( 'wyz_woocommerce_hide_menu_cart_if_empty' ) ? '' : ' hdn' ) . '"><a class="wcmenucart-contents" href="'. $shop_page_url .'" title="'. $start_shopping .'">';
			} else {
				$items .= '<li class="wcmenucart"><a class="wcmenucart-contents" href="'. $cart_url .'" title="'. $viewing_cart .'">';
			}

			$items .= '<i class="fa fa-shopping-cart"></i> ';

			$items .= '<span class="count">' . $cart_contents_count . '</span>';
			$items .= '</a></li>';
		}

		return $items;
	}

	//Here, we are in primary menu and template 1, nothing to add
	if ('login' !== $args->theme_location && 1 == $template_type ) {
		return $items;
	}

	$link = '';

	if ( 2 != $template_type &&  ( function_exists( 'wyz_get_option' ) && 'header3' != wyz_get_option( 'header-layout' ) ) ) {
		$redirect = is_home() ? false : get_permalink();
		if ( is_user_logged_in() ) {
			$macc_title =  esc_html__( 'my account', 'wyzi-business-finder' );
			$cur_mnu_itm = ( is_page( 'user-account' ) ? ' current-menu-item' : '' );
			if ( function_exists( 'wyz_get_option' ) ) {
				$macc_select = wyz_get_option( 'login-btn-content-type' );
				$current_user = get_userdata(get_current_user_id());
				switch ( $macc_select ) {
					case 'firstname':
						$macc_title = $current_user->first_name;
						break;
					case 'lastname':
						$macc_title = $current_user->last_name;
						break;
					case 'username':
						$macc_title = $current_user->user_login;
						break;
					case 'custom-text':
						$macc_title = esc_html( wyz_get_option( 'login-btn-custom-text' ) );
						break;
				}
			}

			$macc_title = apply_filters( 'wyz_my_account_btn_title', $macc_title );
			
			$link = '<a href="' . esc_url( home_url( '/user-account/' ) ) . '" id="my-account-btn" class="wyz-button wyz-primary-color icon" title="' . esc_attr__( 'My Account', 'wyzi-business-finder' ) . '">' . $macc_title . '<i class="fa fa-angle-right"></i></a>';
			$link .= WyzHelpers::get_myaccount_btn_dropdown();
			$link .= '<a href="' . wp_logout_url( home_url() ) . '" class="wyz-button" title="' . $logout_ttl . '">' . $logout_ttl . '</a>';
		} else {
			$link = '<a href="' . home_url( '/signup/' ) . '" class="wyz-button blue icon wyz-primary-color" title="' . $reg_ttl . '">' . $reg_ttl . '<i class="fa fa-angle-right"></i></a>';
			$link .= '<a href="' . home_url( '/signup/?action=login' ) . '" class="wyz-button" title="' . $log_ttl . '">' . $log_ttl . '</a>';
		}
	}


	// Check if WooCommerce is active
	if ( class_exists( 'WooCommerce' ) && 'on' != get_option( 'wyz_woocommerce_hide_menu_cart' ) ) {
		global $woocommerce;
		$viewing_cart = esc_html__('View your shopping cart', 'your-theme-slug');
		$start_shopping = esc_html__('Start shopping', 'your-theme-slug');
		$cart_url = wc_get_cart_url();
		//woocommerce 3.0 compatibility
		if ( function_exists( 'wc_get_page_id' ) ) {
			$shop_page_url = get_permalink( wc_get_page_id( 'shop' ) );
		} else {
			$shop_page_url = get_permalink( woocommerce_get_page_id( 'shop' ) );
		}
		$cart_contents_count = $woocommerce->cart->cart_contents_count;
		$cart_total = $woocommerce->cart->get_cart_total();
		if ($cart_contents_count == 0) {
			$link .= '<li class="right wcmenucart' . ( 'on' != get_option( 'wyz_woocommerce_hide_menu_cart_if_empty' ) ? '' : ' hdn' ) . '"><a class="wcmenucart-contents" href="'. $shop_page_url .'" title="'. $start_shopping .'">';
		} else {
			$link .= '<li class="right wcmenucart"><a class="wcmenucart-contents" href="'. $cart_url .'" title="'. $viewing_cart .'">';
		}

		$link .= '<i class="fa fa-shopping-cart"></i> ';

		$link .= '<span class="count">' . $cart_contents_count . '</span>';
		$link .= '</a></li>';
	}
	$items .= $link;
	$items = apply_filters( 'wyz_login_menu_items', $items );
	return $items;
}
add_filter( 'wp_nav_menu_items', 'wyz_add_login_out_cart_item_to_menu', 50, 2 );




add_action( 'wp_logout', 'wyz_auto_redirect_external_after_logout');
function wyz_auto_redirect_external_after_logout(){
  wp_redirect( home_url() );
  exit();
}


/*
 * Extend the search functionality to include businesses' metadata, tags and categories
 */
add_filter( 'posts_search', function( $search, $wp_query ) {
	global $wpdb;

	if ( empty( $search ) || !empty($wp_query->query_vars['suppress_filters']) ) {
		return $search; // skip processing - If no search term in query or suppress_filters is true
	}

	$settings = array(
		'title'             =>  true,
		'content'           =>  true,
		'meta_keys'         =>  array( 'wyz_business_description', 'wyz_business_excerpt', 'wyz_business_slogan' ),
		'taxonomies'        =>  array( 'wyz_business_category', 'wyz_business_tag' ),
		'post_types'        =>  array('post', 'page', 'attachment', 'wyz_business', 'wyz_business_post', 'wyz_offers', 'wyz_location' ),
		'terms_relation'    => 1
	);


	$q = $wp_query->query_vars;
	$exclude_businesses = 'on' == get_option( 'wyz_exclude_businesses_from_search', 'off' );
	$n = !empty($q['exact']) ? '' : '%';
	$search = $searchand = '';
	$terms_relation_type = 'OR';
	foreach ((array)$q['search_terms'] as $term ) {

		$term = $n . $wpdb->esc_like( $term ) . $n;

		// change query as per plugin settings
		$OR = '';
		if (true ||!$exclude_businesses) {
			$search .= "{$searchand} (";

			// post title search
			$search .= $wpdb->prepare("($wpdb->posts.post_title LIKE '%s')", $term);
			$OR = ' OR ';


			// content search
			$search .= $OR;
			$search .= $wpdb->prepare("($wpdb->posts.post_content LIKE '%s')", $term);

			// post meta search
			$meta_key_OR = '';

			foreach ($settings['meta_keys'] as $key_slug) {
				$search .= $OR;
				$search .= $wpdb->prepare("$meta_key_OR (pm.meta_key = '%s' AND pm.meta_value LIKE '%s')", $key_slug, $term);
				$OR = '';
				$meta_key_OR = ' OR ';
			}
			$OR = ' OR ';


			// taxonomies search
			$tax_OR = '';

			foreach ($settings['taxonomies'] as $tax) {
				$search .= $OR;
				$search .= $wpdb->prepare("$tax_OR (tt.taxonomy = '%s' AND t.name LIKE '%s')", $tax, $term);
				$OR = '';
				$tax_OR = ' OR ';
			}

			$search .= ")";
		} else {
			// If plugin settings not available return the default query
			$search .= $wpdb->prepare("{$searchand} (($wpdb->posts.post_title LIKE '%s') OR ($wpdb->posts.post_content LIKE '%s'))", $term, $term);
		}

		$searchand = " $terms_relation_type ";
	}

	if ( ! empty( $search ) ) {
		$search = " AND ({$search}) ";
		if ( ! is_user_logged_in() )
			$search .= " AND ($wpdb->posts.post_password = '') ";
	}

	/* Join Table */
	add_filter('posts_join_request', 'wyz_join_search_table' );

	/* Request distinct results */
	add_filter('posts_distinct_request', 'wyz_search_distinct');

	return $search;
	
}, 500, 2 );

function wyz_join_search_table($join){
	global $wpdb;

	//join post meta table
	$join .= " LEFT JOIN $wpdb->postmeta pm ON ($wpdb->posts.ID = pm.post_id) ";

	//join taxonomies table
	$join .= " LEFT JOIN $wpdb->term_relationships tr ON ($wpdb->posts.ID = tr.object_id) ";
	$join .= " LEFT JOIN $wpdb->term_taxonomy tt ON (tr.term_taxonomy_id = tt.term_taxonomy_id) ";
	$join .= " LEFT JOIN $wpdb->terms t ON (tt.term_id = t.term_id) ";

	return $join;
}
function wyz_search_distinct($distinct) {
	$distinct = 'DISTINCT';
	return $distinct;
}


//Add 'to' email as hidden field in contact form
add_filter( 'wpcf7_form_hidden_fields', function( $field ){
	if ( ! is_singular( 'wyz_business' ) )
		return $field;
	global $post;
	$user_info = get_userdata( $post->post_author );
	$field['bus_auth_email'] = $user_info->user_email;
	return $field;
});

//change the recipient of the email sent from single business page
add_filter( 'wpcf7_mail_components',function($components, $form){

	if ( $form->title != 'Business Wall Contact Form' && $form->title != 'Business Wall Contact Form 2' ) {
		return $components;
	}
	if ( isset( $_POST['bus_auth_email'] ) && ! empty( $_POST['bus_auth_email'] ) ) {
		$components['recipient'] .= ',' . $_POST['bus_auth_email'];
	}
	return $components;
},10,2);


/**
 * Global map search filter.
 *
 * @param string $where the query string.
 * @param object $wp_query the query.
 */
function wyz_title_like_posts_where( $where, $wp_query ) {
	global $wpdb;
	$post_title_like = $wp_query->get( 'post_title_like' );
	if ( is_array( $post_title_like ) ) {

		if ( ! empty( $post_title_like ) ) {
			$where .= ' AND ( ';
			foreach ( $post_title_like as $ttl ) {

				$where .= $wpdb->posts . '.post_title LIKE \'%' . esc_sql( $wpdb->esc_like( $ttl ) ) . '%\' OR ';
			}
			$where = substr( $where, 0, -3 );
			$where .= ' )';
		}
	} elseif ( '' != $post_title_like ) {
		$where .= ' AND ' . $wpdb->posts . '.post_title LIKE \'%' . esc_sql( $wpdb->esc_like( $post_title_like ) ) . '%\'';
	}
	return $where;
}
add_filter( 'posts_where', 'wyz_title_like_posts_where', 10, 2 );



/**
 * Hide admin bars for non admin users
 */
function wyz_show_hide_admin_bar() {
	global $current_user;
	if ( ! is_user_logged_in() )
		return false;

	$user_roles = $current_user->roles;
	
	if ( ( function_exists( 'is_user_wcmp_vendor' ) && is_user_wcmp_vendor( $current_user->ID ) ) ) {
		return 'on' != get_option( 'wyz_vendor_restrict_backend_access' );
	}

	if ( 'on' == get_option( 'wyz_businesses_hide_admin_bar', 'on' ) && WyzHelpers::is_user_wyzi_user( $user_roles ) )
		return false;

	return true;
}
add_filter( 'show_admin_bar', 'wyz_show_hide_admin_bar' );

/**
 * Prevent unauthorized users from entering backend
 */
function wyz_admin_block_unauthorized_users() {
	global $current_user;
	$user_roles = $current_user->roles;

	if ( function_exists( 'is_user_wcmp_vendor' ) && is_user_wcmp_vendor( $current_user ) && 'on' != get_option( 'wyz_vendor_restrict_backend_access' ) )
		return;

	if ( ( ! defined( 'DOING_AJAX' )  || ! DOING_AJAX ) && ( 'on' == get_option( 'wyz_businesses_restrict_backend_access' , 'on' ) && ( WyzHelpers::is_user_wyzi_user( $user_roles ) ) ) && ( ! filter_input( INPUT_GET, 'action' ) || 'trash' !== filter_input( INPUT_GET, 'action' ) ) ) {
		wp_die( esc_html__( 'You are not allowed to access this part of the site', 'wyzi-business-finder' ) );
		exit();
	}
}
add_action( 'admin_init', 'wyz_admin_block_unauthorized_users' );


/**
 * Filter to display attachments only for current authors.
 *
 * @param object $query wp query.
 */
function wyz_show_users_own_attachments( $query ) {
	$id = get_current_user_id();
	if ( ! current_user_can( 'manage_options' ) ) {
		$query['author'] = $id;
	}
	return $query;
}
add_filter( 'ajax_query_attachments_args', 'wyz_show_users_own_attachments', 1, 1 );


/**
 * Check if wyzi pages exist or not.
 * If not, nag the admin to create them.
 */
function wyz_check_pages() {

	global $missing_pages;
	$missing_pages = array();

	if ( isset( $_REQUEST['wyz_create_pages'] ) && ( isset( $_REQUEST['null'] ) || isset( $_REQUEST['trash'] ) || isset( $_REQUEST['draft'] ) ) ) {
		$pages_data = array(
			'signup' => array(
				'content' => '[wyz_signup_form]',
				'title' => 'Sign Up',
			),
			'user-account' => array(
				'content' => '[wyz_my_account]',
				'title' => 'User Account',
			),
			'claim' => array(
				'content' => '[wyz_claim_form_display]',
				'title' => 'Claim The Business',
			),
			'terms-and-conditions' => array(
				'content' => 'The terms and conditions page',
				'title' => 'Terms and Conditions',
			),
		);

		if ( isset( $_REQUEST['null'] ) ) {
			$pages = explode( ',', $_REQUEST['null'] );
			foreach ( $pages as $page ) {
				$p = array(
					'post_content' => $pages_data[ $page ]['content'],
					'post_name' => $page,
					'post_title' => $pages_data[ $page ]['title'],
					'post_status' => 'publish',
					'post_type' => 'page',
					'page_template' => 'templates/full-width-page.php',
					'comment_status' => 'closed',
				);
				wp_insert_post( $p );
			}
		}

		if ( isset( $_REQUEST['trash'] ) ) {
			$pages = explode( ',', $_REQUEST['trash'] );
			foreach ( $pages as $page ) {

				$p = get_page_by_path( $page );
				if ( null !== $p ) {
					$p = $p->ID;
					wp_publish_post( $p );
				}
			}
		}

		wp_redirect( 'edit.php?post_type=page' );
		exit;
	}

	$pages = array(
		'signup' => 'Sign Up',
		'claim' => 'Claim The Business',
		'user-account' => 'User Account',
		'terms-and-conditions' => 'Terms and Conditions',
	);

	foreach ( $pages as $key => $value ) {
		$page = get_page_by_path( $key );
		if ( null === $page ) {
			$missing_pages['slug']['null'][] = $key;
			$missing_pages['title']['null'][] = $value;
		} elseif( 'draft' === $page->post_status ) {
			$missing_pages['slug']['draft'][] = $key;
			$missing_pages['title']['draft'][] = $value;
		} elseif( 'trash' === $page->post_status ) {
			$missing_pages['slug']['trash'][] = $key;
			$missing_pages['title']['trash'][] = $value;
		}
	}

	if ( ! empty( $missing_pages ) ) {
		add_action( 'admin_notices', 'wyz_show_create_pages_nag' );
	}
}
add_action( 'admin_init', 'wyz_check_pages' );



// Localization
function wyz_load_plugin_textdomain() {
	load_plugin_textdomain( 'wyzi-business-finder', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}
add_action( 'after_setup_theme', 'wyz_load_plugin_textdomain' );


/**
 * Create theme required pages.
 */
function wyz_show_create_pages_nag() {
	global $missing_pages;
	$page_query = '?wyz_create_pages';?>
	<div class="notice update-nag is-dismissible" style="display:block;"><strong>
		<p><?php esc_html_e( 'WIZY ToolKit needs the following pages to be created for proper plugin functionality', 'wyzi-business-finder' )?>:
		<?php if ( isset( $missing_pages['slug']['trash'] ) ) {?>	
			<span style="display: block; margin: 0.5em 0.5em 0 0; clear: both;"><em><?php echo implode( ', ', $missing_pages['title']['trash'] ) . '(' . esc_html__( 'trashed', 'wyzi-business-finder' ) . ')';?></em></span>
		<?php 
			$page_query .= '&trash=' . implode( ',', $missing_pages['slug']['trash'] );
			if ( isset( $missing_pages['slug']['draft'] ) ) {
				$page_query .= ',' . implode( ',', $missing_pages['slug']['draft'] );
			}
		}
		if ( isset( $missing_pages['slug']['draft'] ) ) {?>	
			<span style="display: block; margin: 0.5em 0.5em 0 0;"><em><?php echo implode( ', ', $missing_pages['title']['draft'] ) . '(' . esc_html__( 'drafted', 'wyzi-business-finder' ) . ')';?></em></span>
		<?php 
		}
		if ( isset( $missing_pages['slug']['null'] ) ) {?>	
			<span style="display: block; margin: 0.5em 0.5em 0 0;"><em><?php echo implode( ', ', $missing_pages['title']['null'] );?></em></span>
		<?php 
			$page_query .= '&null=' . implode( ',', $missing_pages['slug']['null'] );
		}?>
		<span style="display: block; margin: 0.5em 0.5em 0 0;"><a href="<?php echo esc_url( $page_query );?>"><?php esc_html_e( 'Create Pages', 'wyzi-business-finder' );?></a></span>
		</p>
	</strong></div>
	<?php
}


/*
 * Backend Business owner transfer metaboxes
 */
function wyz_business_owner_metabox_markup() {
	wp_nonce_field( basename( __FILE__ ), 'wyz-business-owner-meta-box-nonce' );

	global $post;
	$post_author = $post->post_author;

	$current_owner = get_userdata( $post_author );

	// Display business's current owner
	if ( false !== $current_owner ) {?>
	<div class="misc-pub-section">
		<?php esc_html_e( 'Current Owner', 'wyzi-business-finder' );?>: <span><b><?php echo $current_owner->user_login;?></b></span>
	</div>
	<?php
	}

	$users = get_users( array(
			'role__in' => array( 'business_owner', 'administrator' ),
			'fields' => array( 'ID', 'user_login' ),
		)
	);?>

	<div class="misc-pub-section">
		<lable><b>*</b>: <?php esc_html_e( 'User already has a business', 'wyzi-business-finder' );?></lable>
	</div>

	<select name="wyz-business-owner-meta-box" id="wyz-business-owner-meta-box">
		<option></option>
		<?php foreach ( $users as $user ) {
			echo "<option value=\"{$user->ID}\"" . ( $user->ID == $post_author ? 'selected="selected"' : '' ) . ">{$user->user_login}" . ( true == get_user_meta( $user->ID, 'has_business',true ) ? ' <b>*</b>' : '' ) . "</option>";
		}?>
	</select>
	<?php
}


/*
 * Backend Business verified metaboxes
 */
function wyz_business_verified_metabox_markup () {
	wp_nonce_field( basename( __FILE__ ), 'wyz-business-verified-meta-box-nonce' );

	global $post;
	$verified = get_post_meta( $post->ID, 'wyz_business_verified', true );
	$claimed = get_post_meta( $post->ID, 'wyz_business_claimed', true );
	$expiry = get_post_meta( $post->ID, 'wyz_business_verify_expiry', true );
	?>
	<div>
		<label style="margin-right:10px;">Verified</label>
		<input type="checkbox" id="wyz_business_verified" value="yes"<?php echo ( 'yes' == $verified ? ' checked' : '' ); ?> name="wyz_business_verified"/>
	</div><div>
		<label style="margin-right:10px;">Expires</label>
		<input type="text" id="expiry-datepicker" value="<?php echo $expiry;?>" name="wyz_business_verify_expiry"/>
	</div>
	<div>
		<label style="margin-right:10px;">Claimed</label>
		<input type="checkbox" id="wyz_business_claimed" value="yes"<?php echo ( 'yes' == $claimed ? ' checked' : '' ); ?> name="wyz_business_claimed"/>
	</div>
	<?php
}


/*
 * Backend Business verified metaboxes
 */

function wyz_business_category_icon_metabox_markup() {
	wyz_tags_enqueue();

	wp_nonce_field( basename( __FILE__ ), 'wyz-business-category-icon-meta-box-nonce' );

	global $post;

	$taxonomies = array();
	$taxonomy = 'wyz_business_category';
	$tax_terms = get_terms( $taxonomy, array( 'hide_empty' => false ) );

	$object_terms = get_the_terms( $post->ID, 'wyz_business_category' );
	$post_cats = array();
	if ( ! $object_terms || is_wp_error( $object_terms ) ) {
		$object_terms = array();
	}
	foreach ( $object_terms as $cat ) {
		if ( $cat->parent ) {
			$post_cats[ $cat->parent ] = true;
		}
	}

	$length = count( $tax_terms );
	$js_data = array();
	$len = 0;
	for ( $i = 0; $i < $length; $i++ ) {
		if ( ! isset( $tax_terms[ $i ] ) ) {
			continue;
		}
		$temp_tax = array();
		$obj = $tax_terms[ $i ];
		if ( 0 == $obj->parent ) {
			$temp_icon = wp_get_attachment_url( get_term_meta( $obj->term_id, 'wyz_business_icon_upload', true ) );
			if ( '' == $temp_icon ) {
				continue;
			}
			$len++;
			$temp_tax['id'] = $obj->term_id;
			$temp_tax['icon'] = $temp_icon;
			$temp_tax['bg'] = get_term_meta( $obj->term_id, 'wyz_business_cat_bg_color', true );
			$temp_tax['name'] =  $obj->name;
			$taxonomies[] = $temp_tax;
			unset( $tax_terms[ $i ] );
		}
	}

	$cat_icon_id = get_post_meta( $post->ID, 'wyz_business_category_icon', true );
	?>


	<select id="wyz-business-category-icon" name="wyz-business-category-icon" class="wyz-input">
		<option value="">Select</option>
		<?php for ( $i = 0; $i < $len; $i++ ) {
			$url = wp_get_attachment_image_src(  get_term_meta( $taxonomies[ $i ]['id'], 'wyz_business_icon_upload', true ), 'thumbnail', true )[0];
			//if($url)$url=$url['url'];
			$bgc = get_term_meta( $taxonomies[ $i ]['id'], 'wyz_business_cat_bg_color', true );
			echo '<option value="'.$taxonomies[ $i ]['id'].'" ' . ( $cat_icon_id == $taxonomies[ $i ]['id'] ? 'selected ' : '' ) . ( false != $url ? 'data-left="<div class=\'cat-prnt-icn\' ' . ( '' != $bgc ? 'style=\'background-color:'.$bgc.';\' ' : '' ) .'><img src=\''.$url.'\'/></div>"' : '') . ' >' . $taxonomies[ $i ]['name'] . '</option>';
		}?>
	</select>

<?php

}


/*
 * Add backend Business/Offers owner transfer metaboxes
 */
function wyz_add_business_owner_meta_box() {
	add_meta_box( 'wyz_business_owner_metabox', esc_html__( 'Transfer Business Owner', 'wyzi-business-finder' ) , 'wyz_business_owner_metabox_markup', 'wyz_business', 'side', 'high', null );

	add_meta_box( 'wyz_business_verified', esc_html__( 'Business verified', 'wyzi-business-finder' ) , 'wyz_business_verified_metabox_markup', 'wyz_business', 'side', 'high', null );

	//business categories icons
	add_meta_box( 'wyz_business_category_icon', esc_html__( 'Business category icon', 'wyzi-business-finder' ), 'wyz_business_category_icon_metabox_markup', 'wyz_business', 'side', 'high', null );
}
add_action( 'add_meta_boxes', 'wyz_add_business_owner_meta_box' );

/**
 * Enqueue datepicker for expiration on verified business
 */
add_action( 'admin_print_scripts',function () {
	global $typenow;
	if( 'wyz_business' === $typenow ) {
		?>
		<style>
		.selectator_element.single {
			height: 42px !important;
			width: 100%;
		}
		.selectator_element .selectator_options .selectator_option .selectator_option_left img {
			width: 20px;
		}
		</style>
		<script type="text/javascript">
			jQuery(document).ready(function(){
				jQuery('#expiry-datepicker').datepicker({
					dateFormat : 'm/d/yy'
				});

				jQuery('#wyz-business-category-icon').selectator({
					useSearch: false,
					useDimmer: false
				});
			});
		</script><?php
	}
},1000 );
add_action( 'admin_enqueue_scripts', function ( $hook_suffix ) {
	global $typenow;
	if( 'wyz_business' === $typenow ) {
		wp_enqueue_script( 'jquery-ui-datepicker' );
		wp_enqueue_style('jquery-ui-core');
		wp_enqueue_style('jquery-ui-datepicker');
		wp_enqueue_script( 'jQuery_select', plugin_dir_url( __FILE__ ) . 'templates-and-shortcodes/js/fm.selectator.jquery.js', array( 'jquery' ), false, true );
		wp_enqueue_style( 'jQuery_select_css', plugin_dir_url( __FILE__ ) . 'templates-and-shortcodes/css/fm.selectator.jquery.css' );
	}
});

/*
 * add Admin Dashbaord admin RTL CSS to fix some pages problems
 */
 
add_action( 'admin_enqueue_scripts',  function($screenid) {

    if ( !is_rtl() || ($screenid != 'toplevel_page_ot-theme-options' && $screenid != 'toplevel_page_wyzi-toolkit-options') ) return;

	wp_enqueue_style( 'wyz_admin_css_rtl', plugin_dir_url( __FILE__ ) . 'templates-and-shortcodes/css/rtl-admin.css' );

});







/**
 * Transfer Business owner upon business save.
 *
 * @param int $post_id The post ID.
 * @param post $post The post object.
 * @param bool $update Whether this is an existing post being updated or not.
 */
function wyz_transfer_business_new_owner( $post_id, $post, $update ) {

	$post_type = $post->post_type;

	if ( ! isset( $_POST['wyz-business-owner-meta-box-nonce'] ) || ! wp_verify_nonce( $_POST['wyz-business-owner-meta-box-nonce'], basename( __FILE__ ) ) || 'wyz_business' != $post_type || ! current_user_can( 'edit_businesses', $post_id ) || ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) ) {
		return $post_id;
	}

	if ( isset( $_POST['wyz-business-owner-meta-box'] ) ) {

		$user_id = intval( $_POST['wyz-business-owner-meta-box'] );

		// if user is already the business owner
		if ( $post->post_author == $user_id ) {
			return $post_id;
		}
		$user_data = get_userdata( $user_id );

		// if supplied user id is not a real registered user
		if ( false === $user_data ) {
			return $post_id;
		}

		//if supplied user id doesn't belong to a business owner/administrator
		if ( ! in_array( 'business_owner', $user_data->roles ) && ! in_array( 'administrator', $user_data->roles ) ) {
			return $post_id;
		}

		$old_owner_id = $post->post_author;

		//transfer post thumbnail
		$thumb_id =  get_post_thumbnail_id( $post_id );

		if ( ( bool ) $thumb_id ) {
			wp_update_post( array( 'ID' => $thumb_id, 'post_author' => $user_id ) );
		}

		//remove business from old user's list of businesses
		WyzHelpers::remove_business_from_user( $old_owner_id, $post_id );


		//add business to new user
		WyzHelpers::add_business_to_user( $user_id, $post_id, $post->post_status );

		//change business owner to selected user
		wp_update_post( array( 'ID' => $post_id, 'post_author' => $user_id ) );

		/* Transfer Offers */

		//get all offers related to current business
		$args = array(
			'post_type' => 'wyz_offers',
			'post_status' => array( 'publish', 'pending' ),
			'posts_per_page' =>'-1',
			'meta_query' => array(
				array(
					'key' => 'business_id',
					'value' => $post_id,
				),
			),
		);

		$query = new WP_Query( $args );
		while ( $query->have_posts() ) {
			$query->the_post();
			$offer_id = get_the_ID();

			//update offer to new owner
			wp_update_post( array( 'ID' => $offer_id, 'post_author' => $user_id ) );

			//update offer thumbnail to new owner
			$thumb_id =  get_post_thumbnail_id( $offer_id );

			if ( ( bool ) $thumb_id ) {
				wp_update_post( array( 'ID' => $thumb_id, 'post_author' => $user_id ) );
			}
		}
		wp_reset_postdata();


		/*Transfer business posts*/
		//get all business posts related to current business
		$args = array(
			'post_type' => 'wyz_business_post',
			'post_status' =>'publish',
			'posts_per_page' =>'-1',
			'meta_query' => array(
				array(
					'key' => 'business_id',
					'value' => $post_id,
				),
			),
		);
		$query = new WP_Query( $args );
		while ( $query->have_posts() ) {
			$query->the_post();

			$b_post_id = get_the_ID();

			//update business post to new owner
			wp_update_post( array( 'ID' => $b_post_id, 'post_author' => $user_id ) );

			//update offer business post to new owner
			$thumb_id =  get_post_thumbnail_id( $b_post_id );

			if ( ( bool ) $thumb_id ) {
				wp_update_post( array( 'ID' => $thumb_id, 'post_author' => $user_id ) );
			}
		}

		//transfer business gallery images
		$attachments = get_post_meta( $post_id, 'business_gallery_image', true );
		if ( ! empty( $attachments ) ) {
			$attachments = array_keys( $attachments );
			if ( ! is_array( $attachments ) ) {
				$attachments = array( $attachments );
			}
			foreach ( $attachments as $attachment ) {
				wp_update_post( array( 'ID' => $attachment, 'post_author' => $user_id ) );
			}
		}
		wp_reset_postdata();


		/* Transfer Products */

		//get all products related to current business
		$args = array(
			'post_type' => 'product',
			'post_status' => array( 'publish', 'pending' ),
			'posts_per_page' =>-1,
			'author' => $post->post_author,
		);

		$query = new WP_Query( $args );
		while ( $query->have_posts() ) {
			$query->the_post();
			$product_id = get_the_ID();

			if ( function_exists( 'get_wcmp_product_vendors' ) && function_exists( 'wc_get_product' ) ) {

				global $WCMp;
				$vendor = get_wcmp_product_vendors($product_id);
				$_product = wc_get_product($product_id);
				$orders = array();

				if ( is_a( $vendor, 'WCMp_Vendor' ) ) {
					if ($_product->is_type('variable')) {
						$get_children = $_product->get_children();
						if ( ! empty( $get_children ) ) {
							foreach ($get_children as $child) {
								$orders = array_merge($orders, $vendor->get_vendor_orders_by_product($vendor->term_id, $child));
							}
							$orders = array_unique($orders);
						}
					} else {
						$orders = array_unique($vendor->get_vendor_orders_by_product($vendor->term_id, $product_id));
					}
				}
				foreach ($orders as $order_id) {
					$order = new WC_Order($order_id);
					$items = $order->get_items('line_item');
					foreach ($items as $item_id => $item) {
						wc_add_order_item_meta($item_id, '_vendor_id', $vendor->id);
					}
				}
				wp_delete_object_term_relationships($product_id, 'dc_vendor_shop');
				wp_delete_object_term_relationships($product_id, 'product_shipping_class');
				wp_update_post(array('ID' => $product_id, 'post_author' => $user_id));
				$thumb_id =  get_post_thumbnail_id( $product_id );
				if ( ( bool ) $thumb_id ) {
					wp_update_post( array( 'ID' => $thumb_id, 'post_author' => $user_id ) );
				}
				update_post_meta( $product_id, 'business_id', $post_id );
				delete_post_meta($product_id, '_commission_per_product');

				if ( class_exists( 'WC_product' ) ) {
					$product = new WC_product($product_id);
					$attachment_ids = $product->get_gallery_image_ids();

					foreach( $attachment_ids as $attachment_id ) 
					{
						wp_update_post( array( 'ID' => $attachment_id, 'post_author' => $user_id ) );
					}
				}
				add_woocommerce_term_meta( $product_id, '_vendor_user_id', $user_id );

				 $term = get_term($user_data->user_login, $WCMp->taxonomy->taxonomy_name);
				 if (is_wp_error($term))
				 	$term = wp_insert_term($user_data->user_login, $WCMp->taxonomy->taxonomy_name);
				if (!is_wp_error($term)) {
					update_user_meta($user_id, '_vendor_term_id', $term['term_id']);
					update_woocommerce_term_meta($term['term_id'], '_vendor_user_id', $user_id);
				}
			}
		}
		wp_reset_postdata();

		do_action('wyz_business_new_owner_transferred',$post_id, $post, $update, $old_owner_id);
	}

}
add_action( 'save_post', 'wyz_transfer_business_new_owner', 101, 3 );



/**
 * make business verified or not upon business save.
 *
 * @param int $post_id The post ID.
 * @param post $post The post object.
 * @param bool $update Whether this is an existing post being updated or not.
 */
function wyz_business_verified_save( $post_id, $post, $update ) {
	$post_type = $post->post_type;

	if ( ! isset( $_POST['wyz-business-verified-meta-box-nonce'] ) || ! wp_verify_nonce( $_POST['wyz-business-verified-meta-box-nonce'], basename( __FILE__ ) ) || 'wyz_business' != $post_type || ! current_user_can( 'edit_businesses', $post_id ) ) {
		return $post_id;
	}

	$verified = '';

	if ( isset( $_POST['wyz_business_verified'] ) ) {

		$verified = 'yes' == $_POST['wyz_business_verified'] ? 'yes' : '';

	}
	update_post_meta( $post_id, 'wyz_business_verified', $verified );


	$claimed = '';

	if ( isset( $_POST['wyz_business_claimed'] ) ) {

		$claimed = ('yes' == $_POST['wyz_business_claimed'] ? 'yes' : '');

	}
	update_post_meta( $post_id, 'wyz_business_claimed', $claimed );

	if ( isset( $_POST['wyz_business_verify_expiry'] ) )
		update_post_meta( $post_id, 'wyz_business_verify_expiry', $_POST['wyz_business_verify_expiry'] );

	//update open cluse custom dates
	$dates = array( 'wyz_open_close_monday_status','wyz_open_close_tuesday_status','wyz_open_close_wednesday_status','wyz_open_close_thursday_status','wyz_open_close_friday_status','wyz_open_close_saturday_status','wyz_open_close_sunday_status' );
	foreach ($dates as $date) {
		if ( isset( $_POST[ $date ] ) ) {
			update_post_meta( $post_id, $date, $_POST[ $date ] );
		}
	}
}
add_action( 'save_post', 'wyz_business_verified_save', 10, 3 );



/**
 * save business category icon
 *
 * @param int $post_id The post ID.
 * @param post $post The post object.
 * @param bool $update Whether this is an existing post being updated or not.
 */
function wyz_business_category_icon_save( $post_id, $post, $update ) {
	$post_type = $post->post_type;

	if ( ! isset( $_POST['wyz-business-category-icon-meta-box-nonce'] ) || ! wp_verify_nonce( $_POST['wyz-business-category-icon-meta-box-nonce'], basename( __FILE__ ) ) || 'wyz_business' != $post_type || ! current_user_can( 'edit_businesses', $post_id ) ) {
		return $post_id;
	}

	$icon_id = isset( $_POST['wyz-business-category-icon'] ) ? $_POST['wyz-business-category-icon'] : '';


	update_post_meta( $post_id, 'wyz_business_category_icon', $icon_id );

}
add_action( 'save_post', 'wyz_business_category_icon_save', 10, 3 );



/*
 * Backend Offer's transfer related Business metaboxes
 */
function wyz_offer_related_business_metabox_markup() {
	wp_nonce_field( basename( __FILE__ ), 'wyz-offer-related-nonce' );

	global $post;

	$temp_post = $post;


	$args = array(
		'post_type' => 'wyz_business',
		'post_status' => 'publish',
		'posts_per_page' => -1,
		'fields' => 'ids',
	);

	$curr_owner_bus_id = get_post_meta( $post->ID, 'business_id', true );
	if ( '' == $curr_owner_bus_id || 1 > $curr_owner_bus_id ) {
		$curr_owner_bus_id = -1;
	}

	// Display offer's current business
	if ( 0 < $curr_owner_bus_id ) {?>
	<div class="misc-pub-section">
		<?php esc_html_e( 'Currently belongs to', 'wyzi-business-finder' );?>: <span><b><?php echo get_the_title( $curr_owner_bus_id );?></b></span>
	</div>
	<?php
	}

	$query = new WP_Query( $args );

	// Loop through all available published businesses
	if ( $query->have_posts() ) {?>

		<select name="wyz-offer-related-business-meta-box" id="wyz-offer-related-business-meta-box">
			<option></option>
			<?php while ( $query->have_posts() ) {
				$query->the_post();
				$bus_id = get_the_ID();
				echo '<option value="' . $bus_id . '" ' . ( $curr_owner_bus_id == $bus_id ? 'selected="selected"' : '' ) . '>' . get_the_title() . " - $bus_id</option>";
			} ?>
		</select>
	<?php
	}
	wp_reset_postdata();
	
	$post = $temp_post;
}

/*
 * Add backend Buusiness/Offers owner transfer metaboxes
 */
function wyz_add_offer_related_business_meta_box() {
	add_meta_box( 'wyz_offer_related_business_metabox', esc_html__( 'Transfer Offer\'s related Business', 'wyzi-business-finder' ) , 'wyz_offer_related_business_metabox_markup', 'wyz_offers', 'side', 'high', null );
}
add_action( 'add_meta_boxes', 'wyz_add_offer_related_business_meta_box' );


/**
 * Transfer Offer owner upon offer save.
 *
 * @param int $post_id The post ID.
 * @param post $post The post object.
 * @param bool $update Whether this is an existing post being updated or not.
 */
function wyz_transfer_offer_new_owner( $post_id, $post, $update ) {

	$post_type = get_post_type( $post_id );

	if ( ! isset( $_POST['wyz-offer-related-nonce'] ) || ! wp_verify_nonce( $_POST['wyz-offer-related-nonce'], basename( __FILE__ ) ) || 'wyz_offers' != $post_type || ! current_user_can( 'edit_offers', $post_id ) || ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) ) {
		return $post_id;
	}

	if ( isset( $_POST['wyz-offer-related-business-meta-box'] ) ) {

		$new_bus_id = intval( $_POST['wyz-offer-related-business-meta-box'] );
		$old_bus_id = intval( get_post_meta( $post_id, 'business_id', true ) );

		//if no change was made, do nothing
		if ( $new_bus_id == $old_bus_id ) {
			return $post_id;
		}

		// check if the provided id belongs to an existing published business
		if ( 'publish' != get_post_status( $new_bus_id ) )
			return $post_id;
		

		$new_business = get_post( $new_bus_id );

		//transfer post thumbnail
		$thumb_id =  get_post_thumbnail_id( $post_id );

		if ( ( bool ) $thumb_id ) {
			wp_update_post( array( 'ID' => $thumb_id, 'post_author' => $new_business->post_author ) );
		}

		//transfer offer to the new business
		update_post_meta( $post_id, 'business_id', $new_bus_id );

		//transfer offer to the author of the new business
		wp_update_post( array( 'ID' => $post_id, 'post_author' => $new_business->post_author ) );
	}

}
add_action( 'save_post', 'wyz_transfer_offer_new_owner', 10, 3 );



/*
 * Add backend product owner business transfer metaboxes
 */
function wyz_add_product_owner_business_meta_box() {
	add_meta_box( 'wyz_product_owner_business_metabox', esc_html__( 'Product\'s owner business', 'wyzi-business-finder') , 'wyz_product_owner_business_metabox_markup', 'product', 'side', 'high', null );
}
add_action( 'add_meta_boxes', 'wyz_add_product_owner_business_meta_box' );


/**
 * Set the owner business of the product
 *
 * @param int $post_id The post ID.
 * @param post $post The post object.
 * @param bool $update Whether this is an existing post being updated or not.
 */
function wyz_set_product_owner_business( $post_id, $post, $update ) {

	$post_type = get_post_type( $post_id );

	if ( ! isset( $_POST['wyz-product-owner-business-nonce'] ) || ! wp_verify_nonce( $_POST['wyz-product-owner-business-nonce'], basename( __FILE__ ) ) || 'product' != $post_type || !class_exists( 'BOOKED_WC' )  || ! current_user_can( 'edit_products', $post_id ) || ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) ) {
		return $post_id;
	}

	if ( isset( $_POST['wyz-product-owner-business-meta-box'] ) ) {

		$new_bus_id = intval( $_POST['wyz-product-owner-business-meta-box'] );
		$old_bus_id = intval( get_post_meta( $post_id, 'business_id', true ) );

		//if no change was made, do nothing
		if ( $new_bus_id == $old_bus_id ) {
			return $post_id;
		}

		// check if the provided id belongs to an existing published business owned by the product's owner
		if ( ! current_user_can( 'manage_options' ) ) {
			$args = array(
				'post_type' => 'wyz_business',
				'post_status' => 'publish',
				'p' => $new_bus_id,
				'author' => get_current_user_id(),
			);
			$query = new WP_Query( $args );
			if ( ! $query->have_posts() ) {
				return $post_id;
			}
			wp_reset_postdata();
		}

		//transfer product to the new business
		update_post_meta( $post_id, 'business_id', $new_bus_id );
	}

}
add_action( 'save_post', 'wyz_set_product_owner_business', 10, 3 );

add_action( 'init', 'wyz_check_import_flush' );

function wyz_check_import_flush() {
	if ( 'WYZ_JUST_IMPORTED_FLAG' == get_option('wyz_just_imported') ) {
		update_option( 'wyz_just_imported', '' );
		flush_rewrite_rules();
	}
}
/**
 * Resolve the issue of products not having visibility
 *
 * @param int $post_id The post ID.
 * @param post $post The post object.
 * @param bool $update Whether this is an existing post being updated or not.
 */
function wyz_set_product_visibility( $post_id, $post, $update ) {

	if ( $post->post_type == 'product' && $post->post_status == 'publish'
		&& '' == get_post_meta( $post_id, '_visibility', true ) ) {
		update_post_meta( $post_id, '_visibility', 'visible' );
	}
	elseif( $post->post_type == 'wyz_business' && is_admin() ) {
		if ( $post->post_status == 'publish') {
			WyzHelpers::add_business_to_user( $post->post_author, $post_id, 'published' );
		}
		update_post_meta( $post_id, 'wyz_business_description', $post->post_content );
	}
	elseif( $post->post_type == 'wyz_offers' && is_admin() ) {
		update_post_meta( $post_id, 'wyz_offers_description', $post->post_content );
	}
}
add_action( 'save_post', 'wyz_set_product_visibility', 100, 3 );


/*
 * Backend Product owner business metaboxes markup
 */
function wyz_product_owner_business_metabox_markup() {
	wp_nonce_field( basename( __FILE__ ), 'wyz-product-owner-business-nonce' );

	global $post;

	$temp_post = $post;


	$args = array(
		'post_type' => 'wyz_business',
		'post_status' => 'publish',
		'posts_per_page' => -1,
		'fields' => 'ids',
	);

	if ( ! current_user_can( 'manage_options' ) ) {
		$args['author'] = get_current_user_id();
	}

	$curr_owner_bus_id = get_post_meta( $post->ID, 'business_id', true );
	if ( '' == $curr_owner_bus_id || 1 > $curr_owner_bus_id ) {
		$curr_owner_bus_id = -1;
	}


	// Display product's current business
	if ( 0 < $curr_owner_bus_id ) {?>
	<div class="misc-pub-section">
		<?php esc_html_e( 'Currently belongs to', 'wyzi-business-finder' );?>: <span><b><?php echo get_the_title( $curr_owner_bus_id );?></b></span>
	</div>
	<?php
	}

	$query = new WP_Query( $args );

	// Loop through all available published businesses
	if ( $query->have_posts() ) {?>

		<select name="wyz-product-owner-business-meta-box" id="wyz-product-owner-business-meta-box">
			<option></option>
			<?php while ( $query->have_posts() ) {
				$query->the_post();
				$bus_id = get_the_ID();
				echo '<option value="' . $bus_id . '" ' . ( $curr_owner_bus_id == $bus_id ? 'selected="selected"' : '' ) . '>' . get_the_title() . " - $bus_id</option>";
			} ?>
		</select>
	<?php
	}
	wp_reset_postdata();
	
	$post = $temp_post;
}


/**
 * Draft a business when a business owner membership plan expires
 */

add_action( 'init', function() {
	if ( 'on' == get_option( 'wyz_businesses_draft_on_expiry', 'off' ) )
		add_action( 'pmpro_after_change_membership_level', 'wyz_remove_listing_to_expired_users', 10, 3 );
});

function wyz_remove_listing_to_expired_users( $level_id, $user_id, $cancel_level ) {
   if ($level_id == 0 ) {
    	
    	//Draft businesses
		wyz_update_post_expiry('wyz_business', $user_id, 'publish', 'expired');


		wyz_update_post_expiry('wyz_offers', $user_id, 'publish', 'expired');

		//jobs
		wyz_update_post_expiry('job_listing', $user_id, 'publish', 'expired');

	    //expire products
		wyz_update_post_expiry('product', $user_id, 'publish', 'expired');

   }
}

function wyz_return_listing_to_expired_users( $level_id, $user_id, $cancel_level ) {
   if ($level_id != 0 ) {
   
		//return businesses
		wyz_update_post_expiry('wyz_business', $user_id, 'expired', 'publish');

		wyz_update_post_expiry('wyz_offers', $user_id, 'expired', 'publish');

		//return products
		wyz_update_post_expiry('product', $user_id, 'expired', 'publish');

		//jobs
		wyz_update_post_expiry('job_listing', $user_id, 'expired', 'publish');
	}
}
add_action( 'pmpro_after_change_membership_level', 'wyz_return_listing_to_expired_users', 10, 3 );


function wyz_update_post_expiry($post_type, $user_id, $old_status,$new_status){
	$args = array(
		'post_type' => $post_type,
		'post_status' => array($old_status),
		'author' => $user_id,
		'posts_per_page' => -1,
		'fields' => 'ids',
	);

	$query = new WP_Query( $args );
	foreach($query->posts as $id){
		$query = array(
			'ID' => $id,
			'post_status' => $new_status,
		);
		wp_update_post( $query, true );  
	}
}

/**
 * Add default user points for newly registerd business owners
 */
function wyz_add_default_points( $user_id ) {
	WyzHelpers::add_extra_points( $user_id );
	if( 'on' == get_option( 'wyz_auto_vendorship' ) && 'on' == get_option( 'wyz_can_become_vendor' ) && user_can( $user_id,'publish_businesses') && 'on' != get_option( 'wyz_sub_mode_on_off' ) ) {
		$user = new WP_User( $user_id );
		WyzHelpers::make_user_vendor( $user );
	}
}
add_action( 'wyz_after_user_register', 'wyz_add_default_points', 1 );


/**
 * Allow admin to publish pending offers.
 */
function wyz_admin_publish( $post_type ) {
	if ( ! is_admin() ) {
		return;
	}

	global $current_screen;
	if ( 'wyz_offers' === $current_screen->post_type && isset( $_GET['action'] ) && 'edit' === $_GET['action'] ) {
		$post = get_post( $_GET['post'] );
		if ( ! $post ) {
			return;
		}
		$points_available = get_the_author_meta( 'points_available', $post->post_author );

		if ( $points_available < get_option( 'wyz_offer_point_price' ) ) {
			echo '<div class="error"><p>' . sprintf( esc_html__( 'This user doesn\'t have enough points to publish %s', 'wyzi-business-finder' ), WYZ_OFFERS_CPT ) . '</p></div>';
		} else {
			$can_publish = true;
			echo '<div class="updated"><p>' . sprintf( esc_html__( 'This user has enough points to publish %s', 'wyzi-business-finder' ), WYZ_OFFERS_CPT ) . '</p></div>';
		}
	}
}
add_action( 'edit_form_after_title', 'wyz_admin_publish' );

function wyz_publish_override( $post_ID, $post ) {
	if ( ! is_admin() ) {
		return;
	}

	// A function to perform actions when a post is published.
	$points_available = 0;
	$points_available = intval( get_the_author_meta( 'points_available', $post->post_author ) );
	if ( $points_available < intval( get_option( 'wyz_offer_point_price' ) ) ) {
		// Nothing to see here.
	} else {
		$points_left = intval( $points_available - intval( get_option( 'wyz_offer_point_price' ) ) );
		update_user_meta( $post->post_author, 'points_available', $points_left );
	}
}
add_action( 'publish_offers', 'wyz_publish_override', 10, 2 );



/**
 * Make Job submition by points.
 */
function wyz_job_listing_publish( $post_ID, $post ) {
	$user_id = $post->post_author;
	if ( user_can( $user_id, 'manage_options' ) ) {
		return;
	}

	if ( get_post_meta( $post->ID, 'published_before', true ) )return;


	$cost = intval( get_option( 'wyz_job_submit_cost', 0 ) );
	if ( $cost < 1 ) return;


	// A function to perform actions when a post is published.
	$points_available = 0;
	$points_available = intval( get_the_author_meta( 'points_available', $user_id ) );
	/*if ( $points_available < $cost ) {
		$post_vars = array(
            'ID' => $post->ID,
            'post_status' => 'pending'
        );
        wp_update_post( $post_vars );
        return;
	} */

	$points_left = intval( $points_available - $cost );

	update_user_meta( $user_id, 'points_available', $points_left );
	update_post_meta( $post->ID, 'published_before', true );
	return true;
}
add_action( 'publish_job_listing', 'wyz_job_listing_publish', 10, 2 );

/**
 * Handle point deduction when pending jobs are created
 */
function wyz_handle_job_pending_publishment( $job_id ) {
	if ( get_post_meta( $job_id, 'published_before', true ) )return;
	if ( wyz_job_listing_publish($job_id, get_post( $job_id ) ) ) {
		$cost = intval( get_option( 'wyz_job_submit_cost', 0 ) );
		update_post_meta( $job_id, 'wyz_job_cost_payed', $cost );
	}
}
add_action( 'job_manager_job_submitted', 'wyz_handle_job_pending_publishment' );

/**
 * Handle returning points when pending jobs are deleted
 */
function wyz_handle_job_pending_delete( $post_ID, $post_after, $post_before ) {
	if ( 'job_listing' != $post_before->post_type )return;
	if ( 'pending' != $post_before->post_status || 'trash' != $post_after->post_status )return;
	$payed_points = intval( get_post_meta( $post_ID, 'wyz_job_cost_payed', true ) );
	if ( $payed_points > 0 ) {
		$points_available = 0;
		$points_available = intval( get_the_author_meta( 'points_available', $post_after->post_author ) ) + $payed_points;
		update_user_meta( $post_after->post_author, 'points_available', $points_available );
		update_post_meta( $post_ID, 'wyz_job_cost_payed', 0 );
	}
}
add_action( 'post_updated', 'wyz_handle_job_pending_delete', 10, 3);

/**
 * Filter the display of backend business so non admins can't see trash and drafts.
 *
 * @param array $views cpt views array.
 */
function wyz_business_meta_views( $views ) {
	if ( is_admin() && ! current_user_can( 'manage_options' ) ) {
		$views['trash']  = '';
		$views['drafts']  = '';
	}
	return $views;
}
add_filter( 'views_edit-wyz_business', 'wyz_business_meta_views', 10, 1 );

/**
 * Filter the display of backend offers so non admins can't see trash and drafts.
 *
 * @param array $views cpt views array.
 */
function wyz_offer_meta_views( $views ) {
	if ( is_admin() && ! current_user_can( 'manage_options' ) ) {
		$views['trash']  = '';
		$views['drafts']  = '';
	}
	return $views;
}
add_filter( 'views_edit-wyz_offers', 'wyz_offer_meta_views', 10, 1 );


/**
 * Prevent users from exceeding the maximum allowed number of businesses
 */
function wyz_no_exceed_businesses() {
	if ( is_admin() ) {
		$exceeds_businesses = !WyzHelpers::user_can_create_business( get_current_user_id() );
		$curr_screen = get_current_screen();
		if ( ! current_user_can( 'manage_options' ) && 'wyz_business' == $curr_screen->post_type && 'add' == $curr_screen->action && $exceeds_businesses ) {
			wp_die( esc_html__( 'You already have the maximum allowed number of Businesses', 'wyzi-business-finder' ));
		}
	}
}
add_action( 'admin_head-post-new.php','wyz_no_exceed_businesses' );


/**
 * Send users an email on registration.
 *
 * @param integer $user_id the id of the user just registered.
 */
function wyz_check_if_user_greeting_mail( $user_id ) {

	if ('on' == get_option( 'wyz_user_email_verification' ))return;

	wyz_user_greeting_mail( $user_id );	
}
add_action('user_register', 'wyz_check_if_user_greeting_mail');

function wyz_user_greeting_mail($user_id){

	$user = get_user_by( 'id', $user_id );

	$user_info = get_userdata( $user_id );

	if ( in_array( 'business_owner', (array) $user_info->roles ) ) {
	    $subject = function_exists( 'wyz_get_option' ) ? wyz_get_option( 'business-owner-greeting-mail-subject' ) : '';
		$message = function_exists( 'wyz_get_option' ) ? wyz_get_option( 'business-owner-greeting-mail' ) : '';
		$subscribtion = function_exists( 'wyz_get_option' ) ? esc_html( get_option( 'wyz_businesses_user_owner' ) ):'';
	} else {
		$subject = function_exists( 'wyz_get_option' ) ? wyz_get_option( 'user-greeting-mail-subject' ) : '';
		$message = function_exists( 'wyz_get_option' ) ? wyz_get_option( 'user-greeting-mail' ) : '';
		$subscribtion = esc_html( get_option( 'wyz_businesses_user_client' ) );
	}

	if ( empty( $subject ) )
		$subject = esc_html__( 'Registration Complete', 'wyzi-business-finder' );
	
	$username = $user_info->user_login;
	$email = $user_info->user_email;
	$fname = $user_info->first_name;
	$lname = $user_info->last_name;

	$message = str_replace( '%USERNAME%', $username, $message );
	$message = str_replace( '%EMAIL%', $email, $message );
	$message = str_replace( '%FIRSTNAME%', $fname, $message );
	$message = str_replace( '%LASTNAME%', $lname, $message );
	$message = str_replace( '%SUBSCRIBTION%', $subscribtion, $message );
	WyzHelpers::wyz_mail( $email, $subject, $message );
}

/*
 * Hide admin CPT menu elements from non admin users
 */
function wyz_remove_menu_items() {
	if ( ! current_user_can( 'administrator' ) ) {
		remove_menu_page( 'edit.php?post_type=wyz_business' );
		remove_menu_page( 'edit.php?post_type=wyz_business_post' );
		remove_menu_page( 'edit.php?post_type=wyz_business_rating' );
		remove_menu_page( 'edit.php?post_type=wyz_location' );
		remove_submenu_page( 'edit.php?post_type=wyz_offers', 'post-new.php?post_type=wyz_offers' );
		remove_menu_page( 'wpcf7' );
		remove_menu_page( 'vc-welcome' );
	}
}
add_action( 'admin_menu', 'wyz_remove_menu_items' );


function wyz_override_vendor_text() {
	return '';
}
add_filter( 'wcmp_vendor_registration_header_text', 'wyz_override_vendor_text' );


/*
 * Hide 'add new offer' button from non admin users
 */
function wyz_hide_add_new_offers() {
	global $pagenow;

	if ( ! current_user_can( 'administrator' ) && ( 'edit.php' == $pagenow || 'post.php' == $pagenow ) ) {
		echo '<style>a.page-title-action{display: none;}</style>';  
	}
}
add_action('admin_head','wyz_hide_add_new_offers'); 

/*
 * Hide admin CPT menu elements from non admin users
 */
function wyz_restrict_menu_items() {
	global $pagenow;

	if ( ! current_user_can( 'administrator' ) && ( ( isset( $_GET['post_type'] ) && 
	  ( 'wyz_business_rating' == $_GET['post_type'] || 'wyz_business' == $_GET['post_type'] || ( 'post-new.php' == $pagenow && isset( $_GET['post_type'] ) && 'wyz_offers' == $_GET['post_type'] ) || 'wyz_business_post' == $_GET['post_type'] || 'wyz_location' == $_GET['post_type'] ) ) || 
	  	 isset( $_GET['page'] ) && 'wpcf7' == $_GET['page'] ) ) {
		wp_die( esc_html__( 'You don\'t have the right to acces this part of the site', 'wyzi-business-finder' ) );
	}
}
add_action( 'admin_init', 'wyz_restrict_menu_items' );


/*
 * Add the ability to embed videos from youtube using https.
 */
function wyz_youtube_ssl_oembed(){

	wp_oembed_add_provider( '#https://(www\.)?youtube.com/watch.*#i', 'http://youtube.com/oembed?scheme=https', true );
	wp_oembed_add_provider( 'https://youtu.be/*', 'http://youtube.com/oembed?scheme=https', false );
}
add_action( 'init', 'wyz_youtube_ssl_oembed' );



function wyz_publish_pending_business() {
	if( isset( $_GET['publish-business'] ) ) {

		$user_id = get_current_user_id();
		$user_businesses = WyzHelpers::get_user_businesses( $user_id );
		if ( isset( $user_businesses['pending'][ $_GET['publish-business'] ] ) && get_post_status( $_GET['publish-business'] ) ) {
			if ( WyzHelpers::wyz_current_user_affords_business_registry() ) {

				$post = array(
					'ID' => $_GET['publish-business'],
					'post_status' => 'publish',
				);
				wp_update_post( $post );

				unset( $user_businesses['pending'][ $_GET['publish-business'] ] );
				$user_businesses['published'][ $_GET['publish-business'] ] = $_GET['publish-business'];

				update_user_meta( $user_id, 'wyz_user_businesses', $user_businesses );

				$points_left = intval( get_the_author_meta( 'points_available', $user_id ) ) - intval( get_option( 'wyz_businesses_registery_price' ) );
				update_user_meta( $user_id, 'points_available', $points_left );
				$url = '?business_created=' . $_GET['publish-business'];
				wp_redirect( esc_url_raw( $url ) );
				exit;
			}
		}
	}
}
add_action( 'wp_loaded', 'wyz_publish_pending_business' );


/*
 * fix business registration builder issue on import malfunction
 */
add_action( 'import_end', function () {
	$wyz_business_form_builder_data = get_option( 'wyz_business_form_builder_data', array() );

	if ( ! is_array( $wyz_business_form_builder_data ) )
		update_option( 'wyz_business_form_builder_data', array() );


	$wyz_business_filters = get_option( 'wyz_business_filters', array() );

	if ( ! is_array( $wyz_business_filters ) )
		update_option( 'wyz_business_filters', array() );

});

/*---------------------------------------------
				WOOCOMMERCE
---------------------------------------------*/

/**
 * Add Woocommerce price attribute to products
 *
 * @param array $attribute current attribute
 */
function wyz_process_add_attribute( $attribute ) {
	global $wpdb;

	$wpdb->insert( $wpdb->prefix . 'woocommerce_attribute_taxonomies', $attribute );

	do_action( 'woocommerce_attribute_added', $wpdb->insert_id, $attribute );

	flush_rewrite_rules();
	delete_transient( 'wc_attribute_taxonomies' );

	return true;
}


/**
 * Load js asynchronously
 */
function wyz_async_scripts( $url ) {
    if ( strpos( $url, '#asyncload') === false )
        return $url;
    if ( is_admin() )
        return str_replace( '#asyncload', '', $url );
    
	return str_replace( '#asyncload\'', '', $url )."' async defer"; 
}
add_filter( 'clean_url', 'wyz_async_scripts', 11, 1 );


/**
 * Create user calendars from user account page
 *
 */
function wyz_create_user_calendar() {
	if ( ! isset( $_POST['wyz_calendar_nonce_field'] ) || ! isset( $_POST['calendar_business_id'] ) )
		return;
	$user_id = get_current_user_id();
	if ( ! wp_verify_nonce( $_POST['wyz_calendar_nonce_field'], 'wyz_create_user_calendar' ) )
		wp_die( 'Security Violation' );
	$can_create_calendar = WyzHelpers::wyz_sub_can_bus_owner_do( $user_id,'wyzi_sub_business_can_create_bookings') &&
						class_exists( 'WooCommerce' );
	if ( ! $can_create_calendar )
		wp_die( esc_html__( 'You don\'t have the rights to create a booking calendar' ) );

	$business_id = $_POST['calendar_business_id'];
	$calendar = WyzHelpers::get_user_calendar( $user_id, $business_id );
	if ( $calendar )
		wp_die( esc_html__( 'You already have a calendar for this business', 'wyzi-business-finder' ) );

	$term_name = esc_html( $_POST['wyz_calendar_name'] );
	if ( '' == $term_name )
		$term_name = 'calendar_' . $user_id . '_' . $business_id;
	$term = term_exists( $term_name, 'booked_custom_calendars' );
	if ( $term ) {
		$_POST['wyz_calendar_name_taken'] = true;
		return;
		/*if ( is_array( $term ) ) $term = $term['term_id'];
		WyzHelpers::set_user_calendar( $business_id, $term, $user_id );*/
	} else {
		$term = wp_insert_term( $term_name, 'booked_custom_calendars' );
		WyzHelpers::set_user_calendar( $business_id, $term['term_id'], $user_id );
	}
	if ( is_array( $term ) ) $term = $term['term_id'];
	$user_data = get_userdata( $user_id );
	$email = $user_data->user_email;
	$term_meta = get_option( "taxonomy_$term" );
	$term_meta['notifications_user_id'] = $email;
	update_option( "taxonomy_$term", $term_meta );
	$_POST['wyz_calendar_created'] = true;
}
add_action( 'init', 'wyz_create_user_calendar', 999 );

/*
 * fix the error where paid service doesn't appear
 */
add_action('init', function(){
	class_exists( 'WyzHelpers' ) && WyzHelpers::is_calendar_page(true);

	if ( 'design_2' == get_option( 'wyz_user_dashboard_template' ) ) {
		add_filter( 'wyz_account_tab_link', function( $link, $is_user_account_page ) {
			if ( $is_user_account_page )
				return $link;
			switch ($link) {
				case 'my-business':
					$link = '?page=businesses';
					break;
				case 'profile':
					$link = '?page=profile';
					break;
				case 'favorite':
					$link = '?page=favorite';
					break;
				case 'woo-profile':
					$link = '?page=shop';
					break;
				case 'subscription':
					$link = '?page=subscription';
					break;
				case 'jobs':
					$link = '?page=jobs';
					break;
				case 'booking':
					$link = '?page=appointments';
					break;
				case 'inbox':
					$link = '?page=inbox';
					break;
				case 'vendor-form':
					$link = '?page=vendor-form';
					break;
				case 'products':
					$link = '';
					break;
			}
			return $link;
		}, 10, 2);

		add_filter( 'wyz_account_tab_link_pre', function( $link_pre, $link, $is_user_account_page ) {
			if ( $is_user_account_page )
				return $link_pre;
			switch ( $link ) {
				case 'products':
					if ( function_exists( 'wcmp_get_vendor_dashboard_endpoint_url' ) )
						$link_pre = esc_url( wcmp_get_vendor_dashboard_endpoint_url( get_wcmp_vendor_settings( 'wcmp_products_endpoint', 'vendor', 'general', 'products' ) ) );
					break;
			}
			return $link_pre;
		}, 10, 3);



		// user dashboard redirects
		add_filter( 'wyz_frontend_business_submit_redirect_url', 'wyz_create_dashboard_business_redirect', 10, 2 );
		add_filter( 'wyz_frontend_business_edit_redirect_url', 'wyz_create_dashboard_business_redirect', 10, 2 );
		function wyz_create_dashboard_business_redirect( $url, $attrs ) {
			$url .= '&page=' . WyzQueryVars::Businesses;
			return $url;
		}


		add_filter( 'wyz_frontend_offer_submit_redirect_url', 'wyz_create_dashboard_offer_redirect', 10, 2 );
		add_filter( 'wyz_frontend_offer_edit_redirect_url', 'wyz_create_dashboard_offer_redirect', 10, 2 );
		function wyz_create_dashboard_offer_redirect( $url, $attrs ) {
			$url .= '&page=' . WyzQueryVars::Offers;
			return $url;
		}

		add_action( 'job_manager_job_submitted', function( $job_id ){
			wp_redirect( home_url( '/user-account/?page=jobs' ) );
		});


	} else {
		add_filter( 'wyz_account_tab_link', function( $link, $is_user_account_page ) {
			return "#$link";
		}, 10, 2 );
	}
});



/**
 * Fix for location pagination
 */
add_filter('redirect_canonical','wyz_disable_location_redirect_canonical');

function wyz_disable_location_redirect_canonical($redirect_url) {
    if (is_singular('wyz_location'))
        $redirect_url = false;
    return $redirect_url;
}

/**
  redirect to subscription page 
  if subscription is on
  */
add_filter( 'wyz_after_register_redirect', function( $url, $user ){
	return wyz_redirect_user_to_subscription_after_login_register( $url, $user, false );
}, 10, 2 );
add_filter( 'wyz_after_login_redirect', 'wyz_redirect_user_to_subscription_after_login_register', 10, 3 );
function wyz_redirect_user_to_subscription_after_login_register( $url, $user, $login_after_register ){
	if ( $login_after_register || get_option( 'wyz_sub_mode_on_off' ) != 'on' || ! user_can( $user->ID, 'publish_businesses' ) )
		return $url;
	$user_is_subscribed = false;
	$wyzi_subscription_options = get_option ('wyzi_pmpro_subscription_options','not_found');
	if ( 'not_found' == $wyzi_subscription_options ) {
		$user_is_subscribed = true;
	}else {
		$membership_level = pmpro_getMembershipLevelForUser( $user->ID );
		if ( ! is_object( $membership_level ) || ! $membership_level->id || ! isset ( $wyzi_subscription_options[ $membership_level->id ] ) ) {
			$user_is_subscribed = false;
		}else{
			$user_is_subscribed = true;
		}
	}
	if ( ! $user_is_subscribed ) {
		$url .= ( 'design_2' == get_option( 'wyz_user_dashboard_template' ) ) ? '?page=subscription' : '#subscription';
	}
	return $url;
}

/**
 * Add note field on appointment approval/rejection
 */
add_filter('booked_fea_shortcode_appointments_buttons', function( $default_button_html, $ap_id){
	return $default_button_html . WyzHelpers::get_appointment_note_form();
},10,2);



/**
 * Prevent non-admins from creating products with points
 *
 * @param array $attributes all available custom attributes
 */
function wyz_restrict_product_points_to_admin( $attributes ){
	if ( current_user_can( 'manage_options' ) || ! $attributes || empty( $attributes ) )
		return $attributes;
	$new_tax = array();
	foreach ( $attributes as $attr ) {
		if ( 'pa_points_value' !=  $attr->attribute_name )
			$new_tax[] = $attr;
	}
	return $new_tax;
}
add_filter( 'woocommerce_attribute_taxonomies', 'wyz_restrict_product_points_to_admin', 1);



/**
 * Hide Points category from non admins
 *
 */
function wyz_list_terms_exclusions( $exclusions, $args ) {
  global $pagenow;
  if (in_array($pagenow,array('post.php','post-new.php')) && 
     !current_user_can('manage_options')) {
    $exclusions = " {$exclusions} AND t.slug NOT IN ('points-category')";
  }
  return $exclusions;
}
add_filter('list_terms_exclusions', 'wyz_list_terms_exclusions', 10, 2);

 /**
 * Hide Points category from vendor category form
 *
 */
add_filter( 'wcmp_vendor_product_classify_1_level_categories', function( $categories ){
	$points_cat =  get_term_by( 'slug', 'points-category', 'product_cat' );
	if ( ! empty( $points_cat ) ) {
		if ( ! isset( $categories['exclude'] ) || empty( $categories['exclude'] ) )
			$categories['exclude'] = array();
		$categories['exclude'][] = $points_cat->term_id;
	}
	return $categories;
});

/**
 * Add the price attribute to wocommerce available attributes
 *
 */
function wyz_add_woocommerce_points_attribute() {
	if ( ! class_exists( 'WooCommerce' ) )
		return;

	global $wpdb;
	$tax = $wpdb->get_results( "SELECT attribute_name FROM " . $wpdb->prefix . "woocommerce_attribute_taxonomies WHERE attribute_name = 'pa_points_value';" );

	if ( (! empty( $tax ) && $tax[0]->attribute_name == 'pa_points_value') || 'on' == get_option( 'wyz_hide_points','off') )
		return;

	$insert = wyz_process_add_attribute( array(
		'attribute_name' => 'pa_points_value',
		'attribute_label' => 'Points value', 
		'attribute_type' => 'text', 'attribute_orderby' => 'menu_order', 'attribute_public' => true ) );
	if ( is_wp_error( $insert ) ) { return; }

	if ( '' != get_option( 'woocommerce_myaccount_edit_account_endpoint' ) ) {
		update_option( 'woocommerce_myaccount_edit_account_endpoint', '');
		update_option( 'woocommerce_logout_endpoint', '' );
	}
}
add_action( 'init','wyz_add_woocommerce_points_attribute', 20 );
/* </ WOOCOMMERCE ADD PRICE ATTRIBUTE > */



/**
 * Add the points category to the woocommers's categories
 */
function wyz_add_woocommerce_points_category(){
	if ( ! class_exists( 'WooCommerce' ) || term_exists( 'Points Category', 'product_cat' ) || 'on' == get_option( 'wyz_hide_points' ) ) {
		return;
	}

	$cat = array(
		array(
			'thumb' => WYZ_THEME_URI . '/images/points-cat.png',
			'name' => 'Points Category',
			'description' => esc_html__( 'Holds products that offer additional points for the user on purchase. Displayed on the "Buy Points" page.', 'wyzi-business-finder' ),
			'slug' => 'points-category'
		),
	);

	foreach ( $cat as $data ) {
		$file = $data['thumb'];
		$filename = basename( $file );
		$upload_file = wp_upload_bits( $filename, null, file_get_contents( $file ) );
		if ( ! $upload_file['error'] ) {
			$wp_filetype = wp_check_filetype( $filename, null );
			$attachment = array(
				'post_mime_type' => $wp_filetype['type'],
				'post_title' => preg_replace( '/\.[^.]+$/', '', $filename ),
				'post_content' => '',
				'post_status' => 'inherit'
			);
			$attachment_id = wp_insert_attachment( $attachment, $upload_file['file'] );
			if ( ! is_wp_error( $attachment_id ) ) {
				require_once( ABSPATH . 'wp-admin' . '/includes/image.php' );
				$attachment_data = wp_generate_attachment_metadata( $attachment_id, $upload_file['file'] );
				wp_update_attachment_metadata( $attachment_id,  $attachment_data );
			}
		}
		$cid = wp_insert_term(
			$data['name'], // the term 
			'product_cat', // the taxonomy
			array(
				'description'=> $data['description'],
				'slug' => $data['slug'],
			)
		);
	    if ( is_wp_error ( $cid ) ) {
	    	return;
	    }

	    $cat_id = isset( $cid['term_id'] ) ? $cid['term_id'] : 0;
	    update_woocommerce_term_meta( $cid['term_id'], 'thumbnail_id', absint( $attachment_id ) );
	}

}
add_action( 'init', 'wyz_add_woocommerce_points_category' );



/**
 * limit allowed number of file uploads per user
 */
/*add_filter( 'wp_handle_upload', 'wyz_update_upload_stats' );
function wyz_update_upload_stats( $args ) {
	$file = $args['file'];
	$size = filesize( $file ); // bytes

	$user_id = get_current_user_id();

	$upload_count = get_user_meta( $user_id, 'upload_count', $single = true );
	$upload_bytes = get_user_meta( $user_id, 'upload_bytes', $single = true );

	update_user_meta( $user_id, 'upload_count', $upload_count + 1 );
	update_user_meta( $user_id, 'upload_bytes', $upload_bytes + $size );
}
*/
add_filter( 'wp_handle_upload_prefilter', 'wyz_check_upload_limits' );
function wyz_check_upload_limits( $file ) {
	
	if ( current_user_can( 'manage_options' ) )
		return $file;

	$user_id = get_current_user_id();
	$sub_mode = get_option( 'wyz_sub_mode_on_off', 'off');
	$max_imgs_opt = intval( get_option( 'wyz_max_attchmtn_count', -1 ) );
	$max_imgs_sub = WyzHelpers::wyz_sub_can_bus_owner_do( $user_id, 'wyzi_max_attchmtn_count' );
	if ( false === $max_imgs_sub || true === $max_imgs_sub ) $max_imgs_sub = -1;
	$max_imgs_sub = intval( $max_imgs_sub );

	if ( 'off' == $sub_mode && 0 > $max_imgs_opt )
		return $file;

	if ( 'on' == $sub_mode ) {
		if ( is_nan( $max_imgs_sub ) || 0 > $max_imgs_sub )
			return $file;
	}

	$attachments = get_posts(array(
		'post_type'=> 'attachment',
		'posts_per_page' => -1,
		'author' => $user_id,
		'fields' => 'ids'
	));

	$count = count( $attachments);

	if ( 'on' == $sub_mode ) {
		if ( $count >= $max_imgs_sub )
			$file['error'] = esc_html__( 'Upload limit has been reached for your account.', 'wyzi-business-finder' );
		return $file;
	}

	if ( is_nan( $max_imgs_opt ) || 0 > $max_imgs_opt )
		return $file;


	if ( $count >= $max_imgs_opt )
		$file['error'] = esc_html__( 'Upload limit has been reached for your account.', 'wyzi-business-finder' );

	return $file;
}


/**
 * Add the points to user on purchase of a product baring points
 */
function wyz_add_points_on_payment_complete( $order_id ){
	$order = wc_get_order( $order_id );
	$user_id = $order->user_id;

	if ( '' != $user_id && $user_id > 0 && 'refund' !== $order->order_type ) {
		$items = $order->get_items();
		foreach ( $items as $item ) {
			$qnt = isset( $item['item_meta']['_qty'][0] ) ? intval( $item['item_meta']['_qty'][0] ) : $item['quantity'];
			$product = $order->get_product_from_item( $item );
			$pts = $product->get_attribute( 'pa_points_value' );
			if ( '' != $pts ) {
				$pts = intval( $pts );
				$pts *= $qnt;
				$user_pts = get_user_meta( $user_id, 'points_available', true );
				if ( ! $user_pts || '' == $user_pts ) {
					$user_pts = 0;
				}
				$user_pts = intval( $user_pts ) + $pts;
				update_user_meta( $user_id, 'points_available', $user_pts );
			}
		}
	}
}
add_action( 'woocommerce_order_status_completed', 'wyz_add_points_on_payment_complete' );




/**
 * Add the points to user on purchase of a product baring points
 */
/*function wyz_add_points_on_payment_complete( $order_id ){
	$order = wc_get_order( $order_id );
	$user_id = $order->user_id;

	if ( '' != $user_id && $user_id > 0 && 'refund' !== $order->order_type ) {
		$items = $order->get_items();
		foreach ( $items as $item ) {
			$qnt = intval( $item['item_meta']['_qty'][0] );
			$product = $order->get_product_from_item( $item );
			$pts = $product->get_attribute( 'pa_points_value' );
			if ( '' != $pts ) {
				$pts = intval( $pts );
				$pts *= $qnt;
				$user_pts = get_user_meta( $user_id, 'points_available', true );
				if ( ! $user_pts || '' == $user_pts ) {
					$user_pts = 0;
				}
				$user_pts = intval( $user_pts ) + $pts;
				update_user_meta( $user_id, 'points_available', $user_pts );
			}
		}
    }
}
add_action( 'woocommerce_order_status_completed', 'wyz_add_points_on_payment_complete' );*/




//Fill cart with user session products
function wyz_add_cart_to_user_after_login($user_login, $user) {
 
	if( function_exists('wc_empty_cart') ){
		global $woocommerce;
		$user_id = $user->ID;
		$meta_key = 'cart-'.$user_id;
		$cart_content = get_option( $meta_key );
		if ( is_array( $cart_content ) ) {
			foreach ( $cart_content as $cart_item_key => $values ) {
				$id =$values['product_id'];
				$quant=$values['quantity'];
				$woocommerce->cart->add_to_cart( $id, $quant);
			}
		}
		delete_option( $meta_key );
	}
}

add_action( 'wp_login', 'wyz_add_cart_to_user_after_login',10,2 );

//save user session cart
function wyz_logout_save_wc_cart(){

	if( function_exists('wc_empty_cart') ){
	
		global $woocommerce;
		$user_id = get_current_user_id();
		$cart_contents = $woocommerce->cart->get_cart();
		if ( empty( $cart_contents ) ) return;
		$meta_key = 'cart-'.$user_id;
		$meta_value = $cart_contents;
		update_option( $meta_key, $meta_value );
		wc_empty_cart();
	}
}
add_action('wp_logout','wyz_logout_save_wc_cart',1);





add_filter( 'woocommerce_billing_fields', 'woo_filter_state_billing', 10, 1 );
add_filter( 'woocommerce_shipping_fields', 'woo_filter_state_shipping', 10, 1 );
function woo_filter_state_billing( $address_fields ) { 
  $address_fields['billing_state']['required'] = false;
	return $address_fields;
}
function woo_filter_state_shipping( $address_fields ) { 
	$address_fields['shipping_state']['required'] = false;
	return $address_fields;
}



function wyz_remove_related_products( $args ) {
	if ( 'on' == get_option( 'wyz_woocommerce_hide_related' ) ) {
		return array();
	}
	return $args;
}
add_filter('woocommerce_related_products_args','wyz_remove_related_products', 10); 


function wyz_woo_remove_reviews_tab($tabs) {
	if ( 'on' == get_option( 'wyz_woocommerce_hide_reviews' ) ) {
		unset($tabs['reviews']);
	}
	return $tabs;
}
add_filter( 'woocommerce_product_tabs', 'wyz_woo_remove_reviews_tab', 98 );


function wyz_add_points_to_product_display() {
	if ( is_singular( 'product' ) ) {
		global $product;
		$pts = $product->get_attribute( 'pa_points_value' );
		if ( $pts && '' != $pts ) {
			echo esc_html__( 'Points', 'wyzi-business-finder' ) . ': ' . $pts;
		}
	}
}
add_action('woocommerce_after_add_to_cart_form','wyz_add_points_to_product_display');

//Disable woocommerce limit to the backend
add_filter( 'woocommerce_prevent_admin_access', '__return_false' );

add_filter('woocommerce_show_page_title', '__return_false');


//add bookable type to wcmp product type options
add_filter( 'wcmp_product_type_options', function( $options ){
	$options['booked_appointment'] = array(
		'id' => '_booked_appointment',
		'wrapper_class' => 'show_if_simple show_if_variable',
		'label' => __('Booked Appointment Service', 'wyzi-business-finder'),
		'description' => __('Booked Appointment products are used for Booked Payments.', 'wyzi-business-finder'),
		'default' => 'no'
	);
	return $options;
});

// add "product bookable" to new WCMP product form
add_action('wcmp_after_product_excerpt_metabox_panel', 'wyz_add_wyzi_fields_to_wcmp_product_form' );
function wyz_add_wyzi_fields_to_wcmp_product_form($product_id) {
	?>
	<div id="woocommerce-wyz-product-data" class="add-product-info-holder">
		<div class="product-info-tab-wrapper">
	<?php
	$args = array(
		'post_type' => 'wyz_business',
		'post_status' => 'publish',
		'author' => get_current_user_id(),
		'fields' => 'ids',
	);
	$query = new WP_Query( $args );
	if ( $query->have_posts() ) {
		$business_id = get_post_meta( $product_id, 'business_id', true );
		echo '<div class="select-group" style="margin-right: 30px;">' . 
				'<label for="product-type">' . esc_html__( 'Owner Business', 'wyzi-business-finder' ) . '</label>'.
	            	'<select class="form-control inline-select" name="product-owner-business">'.
	                '<option></option>';

		for ($i=0;$i<count($query->posts);$i++) {
			echo '<option value="'.$query->posts[$i].'"'.((empty($business_id)&&$i==0)?' selected':($query->posts[$i]==$business_id?' selected':'')).'>'.get_the_title($query->posts[$i]).'</option>';
		}

		echo '</select></div></div></div>';
	}
}

add_action( 'wcmp_process_product_object', function( $product, $POST ){

	$props = array(
		'business_id' => $POST['product-owner-business']
	);

	if ( 'yes' == $POST['product-bookable'] )
		$props['virtual'] = true;
	$product->set_props(array('booked_appointment' => true));
	$product->set_props(array('_booked_appointment' => true));
}, 10, 2 );

add_action( 'wcmp_process_product_meta_simple', function($post_id, $POST){
	if ( 'on' == $POST['_booked_appointment'] ) {
		update_post_meta($post_id, '_virtual', 'yes');
		update_post_meta($post_id, '_booked_appointment', 'yes');
	} else
		update_post_meta($post_id, '_booked_appointment', 'no');
	update_post_meta( $post_id, 'business_id', $POST['product-owner-business']);
},10,2 );


//do_action( 'wcmp_process_product_meta_' . $product_type, $post_id, $_POST );

/*---------------------------------
			ESSENCIAL GRID
----------------------------------*/

add_filter('essgrid_post_meta_handle', 'wyz_add_post_option');
add_filter('essgrid_post_meta_content', 'wyz_add_post_content', 10, 4);
 
function wyz_add_post_option($post_options){
 
	 // where 'author_id' is the attribute of the data to pull in
	 $post_options['wyzi_listing_ratings'] = array('name' => 'Rating Value');
	 $post_options['wyzi_listing_sticky'] = array('name' => 'Sticky Listing');
	 $post_options['wyzi_listing_favorite_btn'] = array('name' => 'Favorite Listing Button');
	 $post_options['wyzi_listing_category_icon'] = array('name' => 'Listing Category Icon');
	 $post_options['wyzi_listing_logo_src'] = array('name' => 'Listing Logo src');
	 $post_options['wyzi_listing_featured_banner'] = array('name' => 'Listing Featured Banner');
	 //$post_options['wyzi_listing_open_close'] = array('name' => 'Listing Open/Close');
	 
	 return $post_options;
 
}

/*
 * Add gallery first image media source
 */
add_filter('essgrid_set_media_source_order', 'wyz_add_first_image_media_source_order');
 
function wyz_add_first_image_media_source_order( $media ){
	$media['wyzi_first_gallery_image'] = array('name' => __('WYZI Listing First Gallery Image'), 'type' => 'picture');
	return $media;
}


add_filter('essgrid_modify_media_sources', 'wyz_set_first_image_media_source', 10, 2);
 
function wyz_set_first_image_media_source( $media, $postid ){

	$attachments = get_post_meta( $postid, 'business_gallery_image', true );
	$temp = '';

	if ( $attachments && ! empty( $attachments ) ) {
		$attachments = array_keys( $attachments );
		if ( ! is_array( $attachments ) ) {
			$temp = wp_get_attachment_image_src( $attachments, 'full' );
		} else {
			foreach ( $attachments as $attachment ) {
				$temp = wp_get_attachment_image_src( $attachment, 'full' );
				if ( '' != $temp ) {
					break;
				}
			}
		}
	}
	if(empty($temp))$temp=array('');
	$feat_img_alt_text = get_post_meta($temp, '_wp_attachment_image_alt', true);

	if ( ! isset( $temp[0] ) || empty( $temp[0] ) || ! $temp[0] )
	$temp[0] = WyzHelpers::get_default_gallery_cover( $postid ); 
	$media['wyzi_first_gallery_image'] = $temp[0];
	$media['wyzi_first_gallery_image-full'] = $temp[0];
	$media['wyzi_first_gallery_image-alt'] = $feat_img_alt_text;

	return $media;
}

add_filter('essgrid_set_media_source', 'wyz_set_interior_value', 10, 3);
 
function wyz_set_interior_value($media, $handle, $media_sources){
    
	if($handle == 'wyzi_first_gallery_image'){
		if(isset($media_sources['wyzi_first_gallery_image'] ) ) {
			$media = '<img src="'.$media_sources['wyzi_first_gallery_image'].'" alt="'.@$media_sources['wyzi_first_gallery_image-alt'].'">';
		}
	}
	return $media;
}


$featured_posts = array();
$user_id = '';
$user_favorites = array();
$loaded_favorites = false;

function wyz_add_post_content($meta_value, $meta, $post_id, $post){

 
	 // where 'author_id' is the attribute of the data to pull in
	 if($meta === 'wyzi_listing_ratings'){

	 	if ( $post['post_type'] != 'wyz_business' ) return '';

		$rate_nb = get_post_meta( $post_id, 'wyz_business_rates_count', true );
		$rate_sum = get_post_meta( $post_id, 'wyz_business_rates_sum', true );
		if ( 0 == $rate_nb ) {
			$rate = 0;
		} else {
			$rate = number_format( ( $rate_sum ) / $rate_nb, 1 );
		}
		 
		 $data = '';

		if ( 0 != $rate ) {
			for ( $i = 0; $i < 5; $i++ ) {

				if ( $rate > 0 ) {
					$data .= '<i class="fa fa-star star-checked" aria-hidden="true"></i>';
					$rate--;
				} else {
					$data .= '<i class="fa fa-star star-unchecked" aria-hidden="true"></i>';
				}
			}
		}
		return $data;
	}
	if ( $meta == 'wyzi_listing_sticky' ) {
		global $featured_posts;
		if ( empty( $featured_posts ) )
			$featured_posts = get_option( 'sticky_posts' );
		if ( in_array( $post_id, $featured_posts ) )
			return '<div class="sticky-notice featured-banner"><span  class="wyz-primary-color wyz-prim-color">' . esc_html__( 'FEATURED', 'wyzi-business-finder' ) .'</span></div>';
		return '';
	}

	if ( $meta == 'wyzi_listing_favorite_btn' ) {

		if ( $post['post_type'] != 'wyz_business' ) return '';
		global $user_id;
		global $user_favorites;
		global $loaded_favorites;
		if ( ! $loaded_favorites ) {
			$loaded_favorites = true;
			if ( is_user_logged_in() ) {
				$user_id = get_current_user_id();
				$user_favorites = WyzHelpers::get_user_favorites( $user_id );
			}
		}
		$is_favorite = in_array( $post_id, $user_favorites );
		if ( is_user_logged_in() ) {
			return '<button class="' . ( $is_favorite ? '' : 'ajax-click ' ) . 'fav-bus" data-action="favorite" data-fav="'.($is_favorite?1:0).'" data-busid="' . $post_id . '"><i class="fa fa-heart' . ( $is_favorite ? '' : '-o' ) . '" aria-hidden="true"></i></button>';
		}
		return '<button class="' . ( $is_favorite ? '' : 'ajax-click ' ) . 'fav-bus-no-login"><i class="fa fa-heart' . ( $is_favorite ? '' : '-o' ) . '" aria-hidden="true"></i></button>';
	}

	if ( 'wyzi_listing_category_icon' == $meta ) {
		$cat_id = WyzHelpers::wyz_get_representative_business_category_id( $post_id );
		$cat = get_term( $cat_id );

		if ( ! is_wp_error( $cat ) && ! empty( $cat ) ) {
			//$parent_cat = $parent_cat[0];
			$cat_name = $cat->name;
			$cat_link = get_term_link( $cat, 'wyz_business_category' );
			$cat_icn = WyzHelpers::get_category_icon( $cat_id );
			$color = get_term_meta( $cat_id, 'wyz_business_cat_bg_color', true );
		} else {
			return '';
		}

		return '<a class="busi-post-label" style="background-color:' . esc_attr( $color ) . '" href="' . esc_url( $cat_link ) . '">
					<img src="' . esc_url( $cat_icn ) . '" alt="' . esc_attr( $cat_name ) . '" />
				</a>';
	}

	if ( 'wyzi_listing_logo_src' == $meta ) {
		return WyzHelpers::get_post_thumbnail_url( $post_id, 'business' );
	}
	if ( 'wyzi_listing_featured_banner' == $meta ) {
		return in_array( $post_id, WyzHelpers::get_sticky_businesses() ) ? '<div class="sticky-notice featured-banner"><span class="wyz-primary-color">'.esc_html__('FEATURED','wyzi-business-finder').'</span></div>' : '';
	}

 	return $meta_value;
}




function wyz_remove_metabox_from_all_post_types() {

	if ( function_exists( 'wyz_get_option' ) && 'off' == wyz_get_option( 'disable_pg_metabox' ) ) return;
 
	if( is_admin() && current_user_can('manage_options') ) {

		$args = array(

			'public'   => true,

		);

		$output = 'names'; // names or objects, note names is the default

		$operator = 'and'; // 'and' or 'or'

		$post_types = get_post_types( $args, $output, $operator ); 

		foreach ( $post_types  as $post_type ) {

			remove_meta_box('eg-meta-box', $post_type, 'normal');

			remove_meta_box('mymetabox_revslider_0', $post_type, 'normal');

		}

	}
 
}
 
add_action('add_meta_boxes', 'wyz_remove_metabox_from_all_post_types', 999);


/**
 * remove visual composer update nag
 */
add_action('admin_init', function()
{
    if( is_admin() ) {
        setcookie( 'vchideactivationmsg', '1', strtotime('+3 years'), '/');
        setcookie('vchideactivationmsg_vc11', (defined('WPB_VC_VERSION') ? WPB_VC_VERSION : '1'), strtotime('+3 years'), '/');
    }
});


/*
 * Override wcmp user roles removal
 */
add_action( 'wcmp_set_user_role', function( $user_id, $new_role, $old_role ) {
	
	if ( ! empty( $old_role ) && ! empty( $new_role ) ) {
		$user = new WP_User( $user_id );
		if ( is_array( $old_role ) ) {
			foreach ( $old_role as $role ) {
				if ( 'dc_rejected_vendor' != $role && 'dc_pending_vendor' != $role )
					$user->add_role( $role );
			}
		} else {
			if ( 'dc_rejected_vendor' != $old_role && 'dc_pending_vendor' != $old_role )
				$user->add_role( $old_role );
		}
	}
}, 10, 3 );

/*
 * add "return to listings" button in wcmp dashboard
 */
add_filter('wcmp_vendor_dashboard_nav', function( $header_nav ) {
	$header_nav['back-to-listings'] = array(
		'label' => esc_html__('Back to Listings', 'wyzi-business-finder')
        , 'url' => esc_url( home_url( '/user-account' ) )
        , 'class' => ''
        , 'submenu' => array()
        , 'position' => 0
        , 'link_target' => '_self'
		, 'capability' => true
        , 'nav_icon' => 'wcmp-font ico-left-arrow-icon'
	);
	return $header_nav;
});




//hook on rate delete
function wyz_delete_rates_action( $post_id ) {
	if ( 'wyz_business_rating' !== get_post_type( $post_id ) ) {
		return;
	}
	return;

	$rate = get_post_meta( $post_id, 'wyz_business_rate', true );


	$query = new WP_Query(array(
		'post_status'=>array('publish','pending'),
		'post_type'=>'wyz_business',
		'posts_per_page' => 1,
		'fields'=>'ids',
		'meta_query'=>array(
			array(
				'key'=>'wyz_business_ratings',
				'value'=>$post_id,
				'compare'=>'IN'
			)
		)
	));

	if($query->have_posts()){
		foreach($query->posts as $bus_id){
			$rate_count = intval( get_post_meta( $bus_id, 'wyz_business_rates_count', true ) );
			$rate_sum = intval( get_post_meta( $bus_id, 'wyz_business_rates_sum', true ) );
			$all_business_ratings = get_post_meta( $bus_id, 'wyz_business_ratings', true );
			if (($key = array_search($post_id, $all_business_ratings)) !== false) {
				unset($all_business_ratings[$key]);
				if($rate_count>0)$rate_count--;
				$rate_sum-=$rate;
				if($rate_sum<0)$rate_sum=0;
				update_post_meta($bus_id,'wyz_business_rates_count',$rate_count);
				update_post_meta($bus_id,'wyz_business_rates_sum',$rate_sum);
				update_post_meta($bus_id,'wyz_business_ratings',$all_business_ratings);
			}
		}
	}
	
}
add_action( 'trashed_post', 'wyz_delete_rates_action' );

// wcmp "sold by" override and replace it by Link to Business instead of Vendor Page
add_filter('wcmp_sold_by_text_after_products_shop_page', function( $condition ){
	if('vendor'==get_option('wyz_woocommerce_soldby_direct_to'))
		return true;
	return false;
});

add_action('woocommerce_product_meta_start',function(){
	if('vendor'==get_option('wyz_woocommerce_soldby_direct_to'))
		return;
	if ( function_exists ('get_wcmp_vendor_settings'))
	if ('Enable' === get_wcmp_vendor_settings('sold_by_catalog', 'general')) {
		global $product;
		if ( ! empty(get_post_meta( $product->get_id() ,'business_id', true ))) {
			$bus_ID = get_post_meta( $product->get_id() ,'business_id', true );
			echo  ' <a class="by-vendor-name-link" style="display: block;" href="'.get_permalink($bus_ID).'" rel="tag">Sold By: '.get_the_title($bus_ID).'</a>';
		}
		
	}

});

add_action('woocommerce_after_shop_loop_item_title', function(){
	if('vendor'==get_option('wyz_woocommerce_soldby_direct_to'))
		return;
	global $post;
	global $wyz_product_tab_url;
    if ( function_exists( 'get_wcmp_vendor_settings' ) && 'Enable' === get_wcmp_vendor_settings('sold_by_catalog', 'general') ) {
        $vendor = get_wcmp_product_vendors($post->ID);
        if ($vendor) {
            $sold_by_text = apply_filters('wcmp_sold_by_text', __('Sold By', 'dc-woocommerce-multi-vendor'), $post->ID);
            $business_id = get_post_meta( $post->ID, 'business_id', true );
            echo '<a class="by-vendor-name-link" style="display: block;" href="' . get_the_permalink( $business_id ) . '">' . $sold_by_text . ' ' . get_the_title( $business_id ) . '</a>';
            do_action('after_sold_by_text_shop_page', $vendor);
        }
    }
}, 30);



global $wyz_product_tab_url;

add_action( 'init', function(){
	global $wyz_product_tab_url;
	$wyz_product_tab_url = '';
	$tabs_url = get_option( 'wyz_business_tabs_order_data' );
	if(is_array($tabs_url))
		foreach ( $tabs_url as $tab ) {
			if ( 'products' == $tab['type'] ) {
				if ( ! isset( $tab['urlid'] ) || empty( $tab['urlid'] ) )
					$wyz_product_tab_url = '#products';
				else
					$wyz_product_tab_url = $tab['urlid'];
				break;
			}
		}
}, 100);


////////////////////////////////////////////////
/*

Personal Data deletion

*/
///////////////////////////////////////////////


function wyz_register_cpt_personal_data_exporter( $exporters ) {
	$cpts = array(
		'wyz_business' => WYZ_BUSINESS_CPT,
		'wyz_offers' => WYZ_OFFERS_CPT,
		'wyz_business_post' => esc_html__( 'Business Post', 'wyzi-business-finder'),
		'wyz_business_rating' => esc_html__( 'Rating', 'wyzi-business-finder' ),
		'booked_appointments' => esc_html__( 'Appointments', 'wyzi-business-finder' ),
		'shop_order' => esc_html__( 'Orders', 'wyzi-business-finder' ),

	);
	foreach ($cpts as $key => $value) {
		$exporters[ $key ] = array(
			'exporter_friendly_name' => $value,
			'callback'               => "wyz_{$key}_personal_data_exporter",
		);
	}
	return $exporters;
}
add_filter( 'wp_privacy_personal_data_exporters', 'wyz_register_cpt_personal_data_exporter' );


function wyz_get_export_query( $cpt, $email_address, $page = 1 ) {
	// Limit us to 50 businesses at a time to avoid timing out.
	$number = 50;
	$page   = (int) $page;

	$data_to_export = array();

	$user = get_user_by( 'email' , $email_address );
	if ( false === $user ) {
		return array(
			'data' => $data_to_export,
			'done' => true,
		);
	}

	return new WP_Query(
		array(
			'author'         => $user->ID,
			'posts_per_page' => $number,
			'paged'          => $page,
			'post_type'      => $cpt,
			'post_status'    => 'any',
			'orderby'        => 'ID',
			'order'          => 'ASC',
		)
	);

}

function wyz_done_personal_data_export( $post_query, $data_to_export, $page) {
	$done = $post_query->max_num_pages <= $page;

	return array(
		'data' => $data_to_export,
		'done' => $done,
	);
}
function wyz_wyz_business_personal_data_exporter ($email_address, $page = 1) {
	$query = wyz_get_export_query( 'wyz_business', $email_address, $page );
	if ( !is_object( $query )) return $query;
	$data_to_export = array();
	foreach ( (array) $query->posts as $post ) {
		$permalink = get_the_permalink( $post->ID );
		$title = get_the_title( $post->ID );

		if ( $permalink ) {
			$post_data_to_export = array(
				array( 'name'  => __( 'Title' ), 'value' => $title ),
				array( 'name'  => __( 'Permalink' ), 'value' => $permalink ),
			);

			$data_to_export[] = array(
				'group_id'    => 'businesses',
				'group_label' => WYZ_BUSINESS_CPT,
				'item_id'     => "post-{$post->ID}",
				'data'        => $post_data_to_export,
			);
		}
	}
	return wyz_done_personal_data_export( $query, $data_to_export, $page );
}

function wyz_wyz_offers_personal_data_exporter ($email_address, $page = 1) {
	$query = wyz_get_export_query( 'wyz_offers', $email_address, $page );
	if ( !is_object( $query )) return $query;
	$data_to_export = array();
	foreach ( (array) $query->posts as $post ) {
		$permalink = get_the_permalink( $post->ID );
		$title = get_the_title( $post->ID );

		if ( $permalink ) {
			$post_data_to_export = array(
				array( 'name'  => __( 'Title' ), 'value' => $title ),
				array( 'name'  => __( 'Permalink' ), 'value' => $permalink ),
			);

			$data_to_export[] = array(
				'group_id'    => 'offers',
				'group_label' => WYZ_OFFERS_CPT,
				'item_id'     => "post-{$post->ID}",
				'data'        => $post_data_to_export,
			);
		}
	}
	return wyz_done_personal_data_export( $query, $data_to_export, $page );
}

function wyz_wyz_business_post_personal_data_exporter ($email_address, $page = 1) {
	$query = wyz_get_export_query( 'wyz_business_post', $email_address, $page );
	$data_to_export = array();
	if ( !is_object( $query )) return $query;
	foreach ( (array) $query->posts as $post ) {
		$permalink = get_the_permalink( $post->ID );
		$title = get_the_title( $post->ID );

		if ( $permalink ) {
			$post_data_to_export = array(
				array( 'name'  => __( 'Title' ), 'value' => $title ),
				array( 'name'  => __( 'Owner Business' ), 'value' => get_the_title( get_post_meta( $post->ID,'business_id', true) ) ),
			);

			$data_to_export[] = array(
				'group_id'    => 'business_posts',
				'group_label' => __( 'Business Posts' ),
				'item_id'     => "post-{$post->ID}",
				'data'        => $post_data_to_export,
			);
		}
	}
	return wyz_done_personal_data_export( $query, $data_to_export, $page );
}

function wyz_wyz_business_rating_personal_data_exporter ($email_address, $page = 1) {
	$query = wyz_get_export_query( 'wyz_business_rating', $email_address, $page );
	$data_to_export = array();
	if ( !is_object( $query )) return $query;
	foreach ( (array) $query->posts as $post ) {
		$title = get_the_title( $post->ID );

		if ( $title ) {
			$post_data_to_export = array(
				array( 'name'  => __( 'Title' ), 'value' => $title ),
				array( 'name'  => __( 'Value' ), 'value' => get_post_meta( $post->ID,'wyz_business_rate', true) ),
			);

			$data_to_export[] = array(
				'group_id'    => 'business_ratings',
				'group_label' => __( 'Business Ratings'),
				'item_id'     => "post-{$post->ID}",
				'data'        => $post_data_to_export,
			);
		}
	}
	return wyz_done_personal_data_export( $query, $data_to_export, $page );
}


function wyz_booked_appointments_personal_data_exporter ($email_address, $page = 1) {
	$query = wyz_get_export_query( 'booked_appointments', $email_address, $page );
	$data_to_export = array();
	if ( !is_object( $query )) return $query;
	foreach ( (array) $query->posts as $post ) {
		$title = get_the_title( $post->ID );

		if ( $title ) {
			$post_data_to_export = array(
				array( 'name'  => __( 'Title' ), 'value' => $title ),
			);

			$data_to_export[] = array(
				'group_id'    => 'booked_appointments',
				'group_label' => __( 'Appointments' ),
				'item_id'     => "post-{$post->ID}",
				'data'        => $post_data_to_export,
			);
		}
	}
	return wyz_done_personal_data_export( $query, $data_to_export, $page );
}


function wyz_shop_order_personal_data_exporter ($email_address, $page = 1) {
	$query = wyz_get_export_query( 'shop_order', $email_address, $page );
	$data_to_export = array();
	if ( !is_object( $query )) return $query;
	foreach ( (array) $query->posts as $post ) {
		$title = get_the_title( $post->ID );

		if ( $title ) {
			$post_data_to_export = array(
				array( 'name'  => __( 'Title' ), 'value' => $title ),
			);

			$data_to_export[] = array(
				'group_id'    => 'shop_order',
				'group_label' => __( 'Shop Orders' ),
				'item_id'     => "post-{$post->ID}",
				'data'        => $post_data_to_export,
			);
		}
	}
	return wyz_done_personal_data_export( $query, $data_to_export, $page );
}

/**
 * Lets remove Vendor Tab Added by WCMP and then Add our own Vendor Tab
 */

add_action( 'init', function(){
global $WCMp_Product;
	WyzHelpers::wyz_remove_class_action('woocommerce_product_tabs', 'WCMp_Product', 'product_vendor_tab');
});

/**
 * Add a Vendor product data tab in WooCommerce Product
 */
add_filter( 'woocommerce_product_tabs', 'wyz_woo_new_product_tab' );
function wyz_woo_new_product_tab( $tabs ) {
	
	// Adds the new tab
	global $product;
	if ( ! empty(get_post_meta( $product->get_id() ,'business_id', true )))
	$tabs['test_tab'] = array(
		'title' 	=> __( 'Vendor', 'wyzi-business-finder' ),
		'priority' 	=> 50,
		'callback' 	=> 'wyz_woo_new_product_tab_content'
	);

	return $tabs;

}
function wyz_woo_new_product_tab_content() {

global $product;
$bus_ID = get_post_meta( $product->get_id() ,'business_id', true );

	echo '<h2><a href="'.get_permalink($bus_ID).'">'.get_the_title($bus_ID).'</a></h2>';
	echo '<p><a href="'.  get_the_permalink($bus_ID) . "#" . WyzHelpers::wyz_get_bus_tab_id('products') .'">'. esc_html__('Click Here to View All Products of this Vendor', 'wyzi-business-finder') . '</a></p>';
	
}

/**
 * Add a lable to bookable products
 */
add_action( 'woocommerce_before_shop_loop_item', 'wyz_show_bookable_notice', 9);
add_action( 'woocommerce_before_single_product_summary', 'wyz_show_bookable_notice', 9 );
function wyz_show_bookable_notice(){
	if ( 'yes' == get_post_meta( get_the_ID(), '_booked_appointment', true) ) {
		echo '<span class="onsale">' . apply_filters( 'wyz_product_bookable_lable', esc_html__( 'Bookable', 'wyzi-business-finder' ) ) . '</span>';
	}
}


/**
 * Remove Bookable & Internal Messaging System Products from Search Results
 */
add_action('pre_get_posts','wyz_remove_products');
function wyz_remove_products($query) {
   if(!is_admin() && $query->is_main_query()){

	$meta_query = array(
			array(
				'key' => 'service_comment',
     		    'compare' => 'EXISTS'
			));

	if ( 'on' !== get_option( 'wyz_display_booking_products_in_listing' )) {
		$meta_query["relation"] =  "OR";
		$meta_query[] = array(

			'key' => '_booked_appointment',
			'compare' => '=',
			'value' => 'yes'

			);
		}

		
	$query_products = new WP_Query( array(
		'post_type' => 'product',
		'posts_per_page' => -1,
		'post_status' => array( 'publish', 'pending' ),
		'meta_query' => $meta_query,
		'fields' => 'ids'
	));

	$post__in = $query_products->posts;
      
 	$current_not_in_array = $query->get('post__not_in');
 	$post__in = array_merge($current_not_in_array, $post__in);
	

      //Exclude the product
      if(is_array($post__in) && $post__in != null) { 
         $query->set('post__not_in', $post__in);
      }


   }

}
