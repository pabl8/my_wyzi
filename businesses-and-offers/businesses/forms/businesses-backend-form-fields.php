<?php
/**
 * Business creation backend form fields.
 *
 * @package wyz
 */
if ( ! function_exists( 'wyz_get_default_business_locations' ) ) {
	function wyz_get_default_business_locations() {
		global $WYZ_USER_ACCOUNT_TYPE;
		if ( $WYZ_USER_ACCOUNT_TYPE != WyzQueryVars::EditBusiness ) {
			return '';
		}
		return '' === get_the_title( $_GET[ WyzQueryVars::EditBusiness ] ) ? '' : $_GET[ WyzQueryVars::EditBusiness ];
	}
}
// Logo bg color.
$wyz_cmb_businesses->add_field(
	array(
		'name' => esc_html__( 'Logo background color', 'wyzi-business-finder' ),
		'id' => $prefix . 'business_logo_bg',
		'type' => 'colorpicker',
		'options' => array( 'url' => false ),
	)
);

// Description.
$wyz_cmb_businesses->add_field(
	array(
		'name' => esc_html__( 'Description', 'wyzi-business-finder' ),
		'desc' => esc_html__( 'A small description about your business', 'wyzi-business-finder' ),
		'id' => $prefix . 'business_excerpt',
		'type' => 'text_medium',
	)
);

// About.
$wyz_cmb_businesses->add_field(
	array(
		'name' => esc_html__( 'About Your Business', 'wyzi-business-finder' ),
		'desc' => esc_html__( 'A full description about your business', 'wyzi-business-finder' ),
		'id' => $prefix . 'business_description',
		'type' => 'wysiwyg',
	)
);

//Gallery
$wyz_cmb_businesses->add_field(
	array(
		'name' => esc_html__( 'Business Gallery', 'wyzi-business-finder' ),
		'id' => 'business_gallery_image',
		'type' => 'file_list',
		'preview_size' => array( 100, 100 ),
	)
);


// Slogan.
$wyz_cmb_businesses->add_field(
	array(
		'name' => esc_html__( 'Slogan', 'wyzi-business-finder' ),
		'desc' => esc_html__( 'Your business slogan', 'wyzi-business-finder' ),
		'id' => $prefix . 'business_slogan',
		'type' => 'text_small',
	)
);


$_24_format = '24' == get_option( 'wyz_openclose_time_format_24' ) ? 'H:i' : 'h:i A';
$fields_array = array(
	array(
		'name' => 'Open',
		'id' => 'open',
		'type' => 'text_time',
		'time_format' => $_24_format,
	),
	array(
		'name' => 'Close',
		'id' => 'close',
		'type' => 'text_time',
		'time_format' => $_24_format,
	),
);
$field_options = array(
	'group_title'   => '',
	'add_button'    => '+',
	'remove_button' => '-',
);
$wyz_cmb_businesses->add_field(
	array(
		'id'          => $prefix . 'open_close_monday',
		'type'        => 'group',
		'description' => esc_html__( 'Monday', 'wyzi-business-finder' ),
		'options'     => $field_options,
		'fields'      => $fields_array,
		'after_group_row' => wyz_after_group_row_backend('monday'),
	)
);
$wyz_cmb_businesses->add_field(
	array(
		'id'          => $prefix . 'open_close_tuesday',
		'type'        => 'group',
		'description' => esc_html__( 'Tuesday', 'wyzi-business-finder' ),
		'options'     => $field_options,
		'fields'      => $fields_array,
		'after_group_row' => wyz_after_group_row_backend('tuesday'),
	)
);
$wyz_cmb_businesses->add_field(
	array(
		'id'          => $prefix . 'open_close_wednesday',
		'type'        => 'group',
		'description' => esc_html__( 'Wednesday', 'wyzi-business-finder' ),
		'options'     => $field_options,
		'fields'      => $fields_array,
		'after_group_row' => wyz_after_group_row_backend('wednesday'),
	)
);
$wyz_cmb_businesses->add_field(
	array(
		'id'          => $prefix . 'open_close_thursday',
		'type'        => 'group',
		'description' => esc_html__( 'Thursday', 'wyzi-business-finder' ),
		'options'     => $field_options,
		'fields'      => $fields_array,
		'after_group_row' => wyz_after_group_row_backend('thursday'),
	)
);
$wyz_cmb_businesses->add_field(
	array(
		'id'          => $prefix . 'open_close_friday',
		'type'        => 'group',
		'description' => esc_html__( 'Friday', 'wyzi-business-finder' ),
		'options'     => $field_options,
		'fields'      => $fields_array,
		'after_group_row' => wyz_after_group_row_backend('friday'),
	)
);
$wyz_cmb_businesses->add_field(
	array(
		'id'          => $prefix . 'open_close_saturday',
		'type'        => 'group',
		'description' => esc_html__( 'Saturday', 'wyzi-business-finder' ),
		'options'     => $field_options,
		'fields'      => $fields_array,
		'after_group_row' => wyz_after_group_row_backend('saturday'),
	)
);
$wyz_cmb_businesses->add_field(
	array(
		'id'          => $prefix . 'open_close_sunday',
		'type'        => 'group',
		'description' => esc_html__( 'Sunday', 'wyzi-business-finder' ),
		'options'     => $field_options,
		'fields'      => $fields_array,
		'after_group_row' => wyz_after_group_row_backend('sunday'),
	)
);


// Address.
$wyz_cmb_businesses->add_field(
	array(
		'name' => esc_html__( 'Address', 'wyzi-business-finder' ),
		'type' => 'title',
		'id' => $prefix . 'business_adress',
	)
);

// zipcode.
$wyz_cmb_businesses->add_field(
	array(
		'name' => esc_html__( 'Zipcode', 'wyzi-business-finder' ),
		'type' => 'text_small',
		'id' => $prefix . 'business_zipcode',
	)
);

// Bldg.
$wyz_cmb_businesses->add_field(
	array(
		'name' => esc_html__( 'Bldg', 'wyzi-business-finder' ),
		'id' => $prefix . 'business_bldg',
		'type' => 'text_small',
	)
);

// Street.
$wyz_cmb_businesses->add_field(
	array(
		'name' => esc_html__( 'Street', 'wyzi-business-finder' ),
		'id' => $prefix . 'business_street',
		'type' => 'text_small',
	)
);

// City.
$wyz_cmb_businesses->add_field(
	array(
		'name' => esc_html__( 'City', 'wyzi-business-finder' ),
		'id' => $prefix . 'business_city',
		'type' => 'text_small',
	)
);

// Country.
$wyz_cmb_businesses->add_field(
	array(
		'name' => LOCATION_CPT,
		'id' => $prefix . 'business_country',
		'type' => 'select',
		'default_cb' => wyz_get_default_business_locations(),
		'options' => WyzHelpers::get_businesses_locations_options(),
	)
);

// Additional address line.
$wyz_cmb_businesses->add_field(
	array(
		'name' => esc_html__( 'Addition Address Line', 'wyzi-business-finder' ),
		'id' => $prefix . 'business_addition_address_line',
		'type' => 'text_small',
	)
);

// Phone number1.
$wyz_cmb_businesses->add_field(
	array(
		'name' => esc_html__( 'Phone Number 1', 'wyzi-business-finder' ),
		'id' => $prefix . 'business_phone1',
		'type' => 'text_small',
	)
);

// Phone number2.
$wyz_cmb_businesses->add_field(
	array(
		'name' => esc_html__( 'Phone Number 2', 'wyzi-business-finder' ),
		'id' => $prefix . 'business_phone2',
		'type' => 'text_small',
	)
);

// Email 1.
$wyz_cmb_businesses->add_field(
	array(
		'name' => esc_html__( 'Email 1', 'wyzi-business-finder' ),
		'id' => $prefix . 'business_email1',
		'type' => 'text_email',
	)
);

// Email 2.
$wyz_cmb_businesses->add_field(
	array(
		'name' => esc_html__( 'Email 2', 'wyzi-business-finder' ),
		'id' => $prefix . 'business_email2',
		'type' => 'text_email',
	)
);

// Business website.
$wyz_cmb_businesses->add_field(
	array(
		'name' => esc_html__( 'Business website', 'wyzi-business-finder' ),
		'id' => $prefix . 'business_website',
		'type' => 'text_url',
	)
);

// Facebook.
$wyz_cmb_businesses->add_field(
	array(
		'name' => esc_html__( 'Facebook Page Link', 'wyzi-business-finder' ),
		'id' => $prefix . 'business_facebook',
		'type' => 'text_medium',
	)
);

// Twitter.
$wyz_cmb_businesses->add_field(
	array(
		'name' => esc_html__( 'Twitter Page Link', 'wyzi-business-finder' ),
		'id' => $prefix . 'business_twitter',
		'type' => 'text_medium',
	)
);

// Linkedin.
$wyz_cmb_businesses->add_field(
	array(
		'name' => esc_html__( 'Linkedin Page Link', 'wyzi-business-finder' ),
		'id' => $prefix . 'business_linkedin',
		'type' => 'text_medium',
	)
);

// Google plus.
$wyz_cmb_businesses->add_field(
	array(
		'name' => esc_html__( 'Google Plus Page Link', 'wyzi-business-finder' ),
		'id' => $prefix . 'business_google_plus',
		'type' => 'text_medium',
	)
);

// Youtube.
$wyz_cmb_businesses->add_field(
	array(
		'name' => esc_html__( 'Youtube Channel Link', 'wyzi-business-finder' ),
		'id' => $prefix . 'business_youtube',
		'type' => 'text_medium',
	)
);

// Instagram.
$wyz_cmb_businesses->add_field(
	array(
		'name' => esc_html__( 'Instagram Link', 'wyzi-business-finder' ),
		'id' => $prefix . 'business_instagram',
		'type' => 'text_medium',
	)
);

// Flicker.
$wyz_cmb_businesses->add_field(
	array(
		'name' => esc_html__( 'Flicker Link', 'wyzi-business-finder' ),
		'id' => $prefix . 'business_flicker',
		'type' => 'text_medium',
	)
);

// Pinterest.
$wyz_cmb_businesses->add_field(
	array(
		'name' => esc_html__( 'Pinterest Link', 'wyzi-business-finder' ),
		'id' => $prefix . 'business_pinterest',
		'type' => 'text_medium',
	)
);



$wyz_cmb_businesses->add_field(
	array(
		'name' => esc_html__( 'Header Image', 'wyzi-business-finder' ),
		'id' => $prefix . 'business_header_image',
		'type' => 'file',
		'options' => array( 'url' => false, ),
		'text'    => array(
			'add_upload_file_text' => esc_html__( 'ADD OR UPLOAD FILE', 'wyzi-business-finder' ),
		),
	)
);

// Longitude and latitude.
$wyz_cmb_businesses->add_field(
	array(
		'name' => esc_html__( 'Location', 'wyzi-business-finder' ),
		'desc' => esc_html__( 'Choose Your Country then fine tune your location by moving the pointer', 'wyzi-business-finder' ),
		'default_cb' => '0',
		'id' => $prefix . 'business_location',
		'type' => 'pw_map',
		'split_values' => true,
	)
);


// Range Radius.
$range_desc = 'Range of coverage ';
$range_desc .= 'mile' == get_option( 'wyz_business_map_radius_unit' ) ? '(in yards)' : '(in meters)';
$range_desc = esc_html__( $range_desc, 'wyzi-business-finder' );
$wyz_cmb_businesses->add_field(
	apply_filters( 'wyz_range_of_coverage_params', array(
		'name' => esc_html__( 'Range Radius', 'wyzi-business-finder' ),
		'id' => $prefix . 'range_radius',
		'type' => 'text_small',
		'desc' => $range_desc,
	))
);

$wyz_cmb_businesses->add_field(
	array(
		'name' => esc_html__( 'Ratings', 'wyzi-business-finder' ),
		'id' => $prefix . 'business_ratings_on_off',
		'type' => 'select',
		'default_cb' => 'on',
		'options' => array( 'on' => esc_html__( 'ON', 'wyzi-business-finder' ), 'off' => esc_html__( 'OFF', 'wyzi-business-finder' ) ),
		'attributes' => array(
			'class' => 'wyz-select',
		),
	)
);

$wyz_cmb_businesses->add_field(
	array(
		'name' => esc_html__( 'Posts Comments', 'wyzi-business-finder' ),
		'id' => $prefix . 'business_comments',
		'type' => 'select',
		'default_cb' => 'on',
		'options' => array( 'on' => esc_html__( 'ON', 'wyzi-business-finder' ), 'off' => esc_html__( 'OFF', 'wyzi-business-finder' ) ),
		'attributes' => array(
			'class' => 'wyz-select',
		),
	)
);


$wyz_business_custom_form_data = get_option( 'wyz_business_custom_form_data', array() );
//Custom form fields
if ( ! empty( $wyz_business_custom_form_data ) ) {
	foreach ( $wyz_business_custom_form_data as $key => $value ) {
		$type = '';
		$san_func = '';
		$attr = array( 'class' => 'wyz-input' );
		$options = array();
		$args = array();

		if ( ! empty( $value['cssClass'] ) ) $attr['class'] = $value['cssClass'];
		if ( ! empty( $value['placeholder'] ) ) $attr['placeholder'] = $value['placeholder'];

		switch ( $value['type'] ) {
			case 'textbox':
				$type = 'text';
			break;
			case 'email':
				$type = 'text_email';
			break;
			case 'textarea':
				$type = 'textarea';
			break;
			case 'url':
				$type = 'text_url';
			break;
			case 'number':
				$attr['type'] = 'number';
				$attr['pattern'] = '\d*';
				$type = 'text';
				$san_func = 'wyz_intval';
				if ( ! empty( $value['positiveOnly'] ) ) {
					$san_func = 'wyz_absint';
				}
			break;
			case 'selectbox':
				switch ($value['selecttype']) {
					case 'dropdown':
						$type = 'select';
						$attr['class'] = 'wyz-select';
						break;
					case 'radio':
						$type = 'radio';
						break;
					case 'checkboxes':
						$type = 'multicheck';
						break;
				}
				foreach( $value['options'] as $option ) {
					$options[ $option['value'] ] = $option['label'];
				}
			break;
			case 'file':
				$type = 'file';
	    		$options['url'] = false;
	    		$attr = array();
			break;
			case 'date':
				$type = 'text_date_timestamp';
				$attr = array();
			break;
			case 'wysiwyg':
				$type = 'wysiwyg';
	    		$options['media_buttons'] = $value['mediaupload'];
			break;
		}


		if ( '' != $type ) {
			$args = array(
				'name' => $value['label'],
				'id' => "wyzi_claim_fields_$key",
				'type' => $type,
				'attributes' => $attr,
			);
			/*if ( isset( $time_format ) )
				$args['time_format'] = $_24_format;*/
			if ( 'file' == $type ) {
				$args['query_args'] = array(
	        		'type' => array(),
	    		);
	    		foreach ( $value['fileType'] as $file_type ) {
	    			if ( $file_type['selected'] == 1 ) {
	    				if('DOC' == $file_type['label'] ){
	    					$args['query_args']['type'][] = $file_type['value'][0];
	    					$args['query_args']['type'][] = $file_type['value'][1];
	    				}
	    				else
	    					$args['query_args']['type'][] = $file_type['value'];
	    			}
	    		}
			}

			if ( ! empty( $options ) ){
				$args['options'] = $options;
			}

			if( ! empty( $san_func ) ) {
				$args['sanitization_cb'] = $san_func;
				$args['escape_cb'] = $san_func;
			}

			$wyz_cmb_businesses->add_field( $args );
		}
	}
}

global $open_close_msgs_b;

function wyz_after_group_row_backend( $name ) {
	$data = '';
	$data .= '<div class="open_close_status">';
	$prefix = 'wyz_';
	global $open_close_msgs_b;

	if(isset($_GET['post'])){$id=$_GET['post'];}else return'';

	if ( empty( $open_close_msgs_b ) )
		$open_close_msgs_b = WyzHelpers::get_open_closed_all_day_msg();

	$values = array(
		array(
			'key' => 'open_all_day',
			'value' => $open_close_msgs_b['open']
		),
		array(
			'key' => 'closed_all_day',
			'value' => $open_close_msgs_b['closed']
		),
		array(
			'key' => 'custom',
			'value' => esc_html__('Custom', 'wyzi-business-finder')
		),
	);

	if ( empty( $id ) ) return '';

	$values = apply_filters( 'wyz_business_registration_form_open_close_labels', $values );

	$i=0;
	foreach( $values as $key => $value ) {
		$val = get_post_meta( $id, $prefix . 'open_close_' . $name . '_status', true );

		$data .= '<div><input '.( ( $val == $value['key'] || ( empty( $val ) && $i == 2 ) )?'checked ':'').'id="' . $prefix . "open_close_" . $name . "_status$i" . '" type="radio" name="' . $prefix . 'open_close_' . $name . '_status' . '" value="' . $value['key'] . '"/><label for="' . $prefix . 'open_close_' . $name . "_status$i" . '" >' . $value['value'] . '</label></div>';
		$i++;
	}
	$data .= '</div>';
	return $data;
}
?>
