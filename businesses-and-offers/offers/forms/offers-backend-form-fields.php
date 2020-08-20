<?php
/**
 * Offer creation backend form fields.
 *
 * @package wyz
 */

// Excerpt.
$wyz_cmb_offers->add_field( array(
	'name' => esc_html__( 'Excerpt', 'wyzi-business-finder' ),
	'desc' => esc_html__( 'A small description', 'wyzi-business-finder' ),
	'id'   => $prefix . 'offers_excerpt',
	'type' => 'text_medium',
) );


// Description.
$wyz_cmb_offers->add_field( array(
	'name' => esc_html__( 'Description', 'wyzi-business-finder' ),
	'desc' => sprintf( esc_html__( 'A description of your %s', 'wyzi-business-finder' ), WYZ_OFFERS_CPT ),
	'id'   => $prefix . 'offers_description',
	'type' => 'textarea',
) ); 



// Image.
$wyz_cmb_offers->add_field( array(
	'name' => esc_html__( 'Main Image', 'wyzi-business-finder' ),
	'desc' => sprintf( esc_html__( 'The image that describes the %s', 'wyzi-business-finder' ), WYZ_OFFERS_CPT ),
	'id'   => $prefix . 'offers_image',
	'type' => 'file',
	'options' => array(
		'url' => false,
	),
	'text'    => array(
		'add_upload_file_text' => esc_html__( 'Add Or Upload File', 'wyzi-business-finder' ),
	),
) );

$_24_format = '24' == get_option( 'wyz_openclose_time_format_24' ) ? 'H:i' : 'h:i A';
$wyz_cmb_offers->add_field( array(
	'name' => esc_html__( 'Expirarion date', 'wyzi-business-finder' ),
	'id'   => $prefix . 'offer_expire',
	'type' => 'text_datetime_timestamp',
	'time_format' => $_24_format,
	'desc' => sprintf( esc_html__( 'If set, this %s will expire at the specified time, then it will not be visible to users', 'wyzi-business-finder' ), WYZ_OFFERS_CPT ),
) );


// Discount.
$wyz_cmb_offers->add_field( array(
	'name'        => esc_html__( 'Discount percentage', 'wyzi-business-finder' ),
	'desc'        => esc_html__( 'Set your value.', 'wyzi-business-finder' ),
	'id'          => $prefix . 'offers_discount',
	/*'type'        => 'own_slider',
	'min'         => '0',
	'max'         => '100',
	'default_cb'     => '50', // Start value.
	'value_label' => '%',*/
	'type' => 'text_small',
	'desc' => esc_html__( 'in %', 'wyzi-business-finder' ),
) );
?>
