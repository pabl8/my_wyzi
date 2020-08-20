<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

	
$business_filters = array(
	'keyword' => array(
		'label' => esc_html__( 'Keyword', 'wyzi-business-finder' ),
		'metadata' => '',
	),
	'category' => array(
		'label' => esc_html__( 'Categories', 'wyzi-business-finder' ),
		'metadata' => '',
	),
	'locations' => array(
		'label' => esc_html__( 'Locations', 'wyzi-business-finder' ),
		'filter-type' => 'dropdown',
		'metadata' => '',
	),
	'radius' => array(
		'label' => esc_html__( 'Radius', 'wyzi-business-finder' ),
		'minrange' => '1',
		'max-range' => '500',
		'step-range' => '1',
		'metadata' => '',
	),
	'submit' => array(
		'label' => esc_html__( 'Submit', 'wyzi-business-finder' ),
		'metadata' => '',
	),
	'verified' => array(
		'label' => esc_html__( 'Verified', 'wyzi-business-finder' ),
		'metadata' => 'wyz_business_verified',
	),
	'open_days' => array(
		'label' => esc_html__( 'Open Days', 'wyzi-business-finder' ),
		'metadata' => array( 
			'wyz_open_close_monday',
			'wyz_open_close_tuesday',
			'wyz_open_close_wednesday',
			'wyz_open_close_thursday',
			'wyz_open_close_friday',
			'wyz_open_close_saturday',
			'wyz_open_close_sunday'
		),
	),
	'bookings' => array(
		'label' => esc_html__( 'Bookings', 'wyzi-business-finder' ),
		'metadata' => '',
	)
);


$custom_form_data = get_option( 'wyz_business_custom_form_data', array() );

$custom_fields = array();

if ( ! empty( $custom_form_data ) )
	foreach ( $custom_form_data as $key => $value ) {

		$inner_data = array();

		$inner_data['label'] = $value['label'];
		

		$inner_data['type'] = $value['type'];
		if ( $value['type'] == 'selectbox' ) {
			 $inner_data['selecttype'] = $value['selecttype'];
			 $inner_data['options'] = $value['options'];
		}
		$custom_fields["wyzi_claim_fields_$key"] = $inner_data;
	}

$business_filters['custom_fields'] = $custom_fields;

update_option( 'wyz_business_filters', $business_filters );
