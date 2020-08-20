<?php
	
$appt_id = esc_html( $_POST['appt_id'] );
$appt = get_post( $appt_id );
$appt_author = $appt->post_author;

$timeslot = get_post_meta( $appt_id,'_appointment_timeslot',true);
$timestamp = get_post_meta( $appt_id,'_appointment_timestamp',true);
$timeslots = explode('-',$timeslot);
$timestamp_start = strtotime(date_i18n('Y-m-d',$timestamp).' '.$timeslots[0]);
$current_timestamp = current_time('timestamp');


$calendar_ids = wp_get_post_terms( $appt_id, 'booked_custom_calendars', array('fields'=>'ids') );

if( ! is_wp_error( $calendar_ids ) )
	$calendar_id = $calendar_ids[0];

if (get_current_user_id() == $appt_author):

	// Send an email to the Admin?
	if ( $timestamp_start >= $current_timestamp ):

		$email_content = get_option('booked_admin_cancellation_email_content');
		$email_subject = get_option('booked_admin_cancellation_email_subject');
		if ($email_content && $email_subject && isset($calendar_id) ):

			$admin_email = booked_which_admin_to_send_email($calendar_id);
			$token_replacements = booked_get_appointment_tokens( $appt_id );

			$email_content = booked_token_replacement( $email_content,$token_replacements );
			$email_subject = booked_token_replacement( $email_subject,$token_replacements );

			do_action( 'booked_admin_cancellation_email', $admin_email, $email_subject, $email_content, $token_replacements['email'], $token_replacements['name'] );
			
		endif;

		if ( function_exists( 'wyz_get_option' ) ) :
			
			if( isset( $calendar_id ) ) {
				$user_email = get_option("taxonomy_$calendar_id")['notifications_user_id'];
				$business_owner = get_user_by( 'email', $user_email );
				$business_name = get_the_title( get_term_meta( $calendar_id, 'business_id', true ) );


				if ( $business_owner ) {
					$business_owner = get_userdata( $business_owner->ID );
					$appt_author = get_userdata( $appt_author );
					$subject = wyz_get_option( 'business-owner-appointment-cancel-email-subject' );
					if ( empty( $subject ) )
						$subject = esc_html__( 'You got a new Email from', 'wyzi-business-finder' ) . ' {' . home_url() . '}';

					$conf_email = wyz_get_option( 'business-owner-appointment-cancel-email' );

					if ( empty( $conf_email ) )
						$conf_email = 'Dear %FIRST_NAME% %LAST_NAME%, client %CLIENT_FIRST_NAME% %CLIENT_LAST_NAME% has canceled his appointment from business: %BUSINESS_NAME% at %APPOINTMENT_DATE%';


					$conf_email = str_replace( '%FIRST_NAME%', $business_owner->first_name, $conf_email );
					$conf_email = str_replace( '%LAST_NAME%', $business_owner->last_name, $conf_email );
					$conf_email = str_replace( '%CLIENT_FIRST_NAME%', $appt_author->last_name, $conf_email );
					$conf_email = str_replace( '%CLIENT_LAST_NAME%', $appt_author->last_name, $conf_email );
					$conf_email = str_replace( '%BUSINESS_NAME%', $business_name, $conf_email );
					$conf_email = str_replace( '%APPOINTMENT_DATE%', date( apply_filters( 'wyz_business_owner_appointment_cancel_email_date_format', get_option('date_format') . ' : ' . get_option('time_format'), $appt, $business_owner ), $timestamp_start ), $conf_email );

					$to = $user_email;

					WyzHelpers::wyz_mail( $to, $subject, $conf_email, 'appointment_client_cancel' );
				}
			}

		endif;
	
	endif;
	
	do_action('booked_appointment_cancelled',$appt_id);
	wp_delete_post($appt_id,true);

endif;