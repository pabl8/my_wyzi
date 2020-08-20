<?php
/**
 * Contains code to manage schedualed event of deleting offers.
 *
 * @package wyz
 */


if ( ! wp_next_scheduled( 'wyz_daily_event' ) ) {
	wp_schedule_event( time(), 'daily', 'wyz_daily_event' );
}

if ( ! wp_next_scheduled( 'wyz_hourly_event' ) ) {
	wp_schedule_event( time(), 'hourly', 'wyz_hourly_event' );
}


if ( ! wp_next_scheduled( 'wyz_rating_reminder' ) ) {
	wp_schedule_event( time(), 'daily', 'wyz_rating_reminder' );
}