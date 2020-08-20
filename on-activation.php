<?php
// Flag for renaming not working on plugin update.
update_option( 'just_activated', true );

if ( '' == get_option( 'ver_1.3.7' ) ) {
	update_option( 'ver_1.3.7', 1 );
	$args = array(
		'post_type'  => array( 'page' ),
		'meta_query' => array(
			array(
				'key'     => 'wyz_map_checkbox',
				'value'   => 'on',
			),
		),
		'posts_per_page' => -1,
	);
	$query = new WP_Query( $args );
	while ( $query->have_posts() ) {
		$query->the_post();
		update_post_meta( get_the_ID(), 'wyz_page_header_content', 'map' );
	}
	wp_reset_postdata();
}

if ( '' == get_option( 'ver_1.4.1' ) ) {
	update_option( 'ver_1.4.1', 1 );
	if ( '' == get_option( 'wyz_business_custom_form_data' ) && '' != get_option( 'wyzi_business_custom_form_data' ) ) {
		update_option( 'wyz_business_custom_form_data', get_option( 'wyzi_business_custom_form_data' ) );
	}
	if ( '' == get_option( 'wyz_claim_registration_form_data' ) && '' != get_option( 'wyzi_claim_registration_form_data' ) ) {
		update_option( 'wyz_claim_registration_form_data', get_option( 'wyzi_claim_registration_form_data' ) );
	}
}

if ( '' == get_option( 'ver_1.5.0' ) ) {
	update_option( 'ver_1.5.0', 1 );
	$users = get_users(array('fields'=>'ID'));
	foreach ($users as $user) {
		$old_id = get_user_meta( $user, 'business_id', true);
		$business_data = array(
			'pending' => array(),
			'published' => array(),
		);
		$count = 0;
		if ( ! empty( $old_id ) && '' != $old_id ) {
			$old_bus = get_post( $old_id );
			if ( $old_bus ) {

				$products_quer = new WP_Query(
					array(
						'post_type' => 'product',
						'posts_per_page' => -1,
						'author' => $user,
					)
				);

				while ( $products_quer->have_posts() ) {
					$products_quer->the_post();
					update_post_meta(get_the_ID(), 'business_id', $old_bus->ID );
				}
				wp_reset_postdata();

				if ( $old_bus->post_status == 'pending' ) {
					$business_data['pending'][$old_bus->ID] = $old_bus->ID;
					$count = 1;
				} elseif ( $old_bus->post_status == 'publish' ) {
					$business_data['published'][$old_bus->ID] = $old_bus->ID;
					$count = 1;
				}
			}
		}
		
		$role = get_role( 'business_owner' );
		$role->add_cap( 'edit_booked_appointments' );
		
		update_user_meta( $user, 'wyz_user_businesses_count', $count );
		update_user_meta( $user, 'wyz_user_businesses', $business_data );
	}
}

if ( '' == get_option( 'ver_2.2.2' ) ) {
	update_option( 'ver_2.2.2', 1 );
	$query = new WP_Query( array(
		'post_type' => 'private-message',
		'post_status' => array( 'any' ),
		'posts_per_page' => -1,
		'fields' => 'ids',
		'post_parent' => 0
	) );

	if(!$query->have_posts())return;
	$ids = $query->posts;
	foreach ($ids as $id) {
		$query = new WP_Query( array(
			'post_type' => 'private-message',
			'post_status' => array( 'any' ),
			'posts_per_page' => -1,
			'post_parent' => $id
		) );
		while ( $query->have_posts() ) {
			$query->the_post();
		    $data = array(
		        'comment_post_ID' => $id,
		        'comment_content' => get_the_content(),
		        'user_id' => get_the_author_meta('ID'),
		        'comment_date' => get_the_date('YYYY-m-d')
		    );

		    $message_id = wp_insert_comment($data);
		    $post_thumbnail_id = get_post_thumbnail_id( get_the_ID() );
		    //update_comment_meta( $message_id, 'subject', $subject );
		    update_comment_meta( $message_id, 'message_receiver_id', get_post_meta( get_the_ID(), 'message_receiver_id', true ) );
		    update_comment_meta( $message_id, 'message_sender_id', get_post_meta( get_the_ID(), 'message_sender_id', true ) );
			update_comment_meta( $message_id, 'attachment_id', $post_thumbnail_id );
		}
		wp_reset_postdata();
	}
}

//update method of linking businesses, users and calendars
if ( '' == get_option( 'ver_2.1.7.3' ) ) {
	update_option( 'ver_2.1.7.3', 1 );
	$all_users = get_users( array( 'fields' => 'ID' ) );
	foreach ( $all_users as $user_id ) {
		$calendars = get_user_meta( $user_id, 'wyz_business_calendars', true );
		if ( ! empty( $calendars ) ) {
			foreach ( $calendars as $bus_id => $cal_id ) {
				update_term_meta( $cal_id, 'business_id', $bus_id );
				update_post_meta( $bus_id, 'calendar_id', $cal_id );
			}
		}
	}
}

if( '' == get_option( 'wyz_business_gallery_updated','' ) ) {

	update_option( 'wyz_business_gallery_updated', 1 );

	$query = new WP_Query( array(
		'post_type' => 'wyz_business',
		'posts_per_page' => -1,
		'post_status' => array('publish','draft','pending','trash'),
		'fields' => 'ids',
	));

	foreach ( $query->posts as $id ) {
		$gal = get_post_meta( $id, 'business_gallery_image', true );
		$gallery = array();
		if ( is_array( $gal ) )
			foreach ( $gal as $img ) {
				$url = wp_get_attachment_url( $img );
				if ( $url )
					$gallery[ $img ] = $url;
			}
		elseif( ! empty( $gal ) ) {
			$url = wp_get_attachment_url( $gal );
			if ( $url )
				$gallery[ $gal ] = $url;
		}
		update_post_meta( $id, 'business_gallery_image', $gallery );
		update_post_meta( $id, 'business_old_gallery_image', $gal );
	}
}
