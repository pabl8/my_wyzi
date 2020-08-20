<?php
/**
 * Add tags field to business cmb2 fields
 *
 * @package wyz
 */

define( 'CMB_TIME_URL', plugin_dir_url( __FILE__ ) );

define( 'CMB_TIME_VERSION', '1.0.0' );

// Render boxes.
function cmb_open_close_render( $field, $value, $object_id, $object_type, $field_type ) { 
	cmb_open_close_enqueue();

	//$val_open = isset( $value['']) ? 

	$value = wp_parse_args( $value, array(
		'open' => '',
		'close' => '',
	) );

	echo $field_type->input( array(
		'name'  => $field_type->_name( '[open]' ),
		'id'    => $field_type->_id( '_open' ),
		'value' => isset( $value['open'] ) ? $value['open'] : '',
		'desc'  => '',
		'attributes' => array(
			'class' => 'time open_close_open',
		),
	) );

	echo $field_type->input( array(
		'name'  => $field_type->_name( '[close]' ),
		'id'    => $field_type->_id( '_close' ),
		'value' => isset( $value['close'] ) ? $value['close'] : '',
		'desc'  => '',
		'attributes' => array(
			'class' => 'time open_close_close',
		),
	) );
}
add_filter( 'cmb2_render_open_close', 'cmb_open_close_render', 10, 5 );
add_filter( 'cmb2_render_open_close_sortable', 'cmb_open_close_render', 10, 5 );



/**
 * Enqueue scripts.
 */
function cmb_open_close_enqueue() {
	wp_enqueue_script( 'jquery_timepicker_script', CMB_TIME_URL . 'js/jquery.timepicker.min.js', array( 'jquery' ), CMB_TIME_VERSION );
	wp_enqueue_script( 'cmb_open_close_script', CMB_TIME_URL . 'js/open_close.js', array( 'jquery_timepicker_script' ), CMB_TIME_VERSION );
	wp_enqueue_style( 'jquery_timepicker_style', CMB_TIME_URL . 'css/jquery.timepicker.min.css', array(), CMB_TIME_VERSION );
}
?>
