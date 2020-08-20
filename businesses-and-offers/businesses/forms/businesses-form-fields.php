<?php
/**
 * Business creation frontend form fields.
 *
 * @package wyz
 */


global $initial;
global $first_page;
global $fields_count;
global $open_close_msgs;
$open_close_msgs = WyzHelpers::get_open_closed_all_day_msg();

$initial = true;
$first_page = true;
$fields_count = 0;

/**
 * Gets current business location if it exists.
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


function wyz_add_business_form_field( $field, $type_after, $wyz_cmb_businesses ) {

	global $initial;
	global $first_page;
	global $fields_count;
	$fields_count++;
	$prefix = 'wyz_';
	$data = array();
	switch ( $field['type'] ) {
		case 'separator':
			$initial = true;
			return;
		break;
		case 'name' :
			$data = array(
				'desc' => '',
				'id' => $prefix . 'business_name',
				'type' => 'text',
				'default_cb' => 'wyz_set_default_business_name',
				'attributes' => array(
					'class' => 'wyz-input',
				),
			);
		break;
		case 'logo':
			$data = array(
				'id' => $prefix . 'business_logo',
				'type' => 'file',
				'options' => array( 'url' => false, ),
				'attributes' => array(),
				'text'    => array(
					'add_upload_file_text' => esc_html__( 'ADD OR UPLOAD FILE', 'wyzi-business-finder' ),
				),
			);
		break;
		case 'banner_image':
			if ( 'image' == get_option( 'wyz_business_header_content' ) ) {
				$data = array(
					'id' => $prefix . 'business_header_image',
					'type' => 'file',
					'options' => array( 'url' => false, ),
					'attributes' => array(),
					'text'    => array(
						'add_upload_file_text' => esc_html__( 'ADD OR UPLOAD FILE', 'wyzi-business-finder' ),
					),
				);
			}
		break;
		case 'logoBg':
			$data = array(
				'id' => $prefix . 'business_logo_bg',
				'type' => 'colorpicker',
				'options' => array( 'url' => false ),
			);
		break;
		case 'desc':
			$data = array(
				'id' => $prefix . 'business_excerpt',
				'type' => 'text_medium',
				'attributes' => array(
					'class' => 'wyz-input',
				),
			);
		break;
		case 'about':
			$data = array(
				'id' => $prefix . 'business_description',
				'type' => 'wysiwyg',
				'attributes' => array(
					'class' => 'wyz-input',
				),
				'options' => array(
					'media_buttons' => true
				),
			);
		break;
		case 'gallery':
			$data = array(
				'id' => 'business_gallery_image',
				'type' => 'file_list',
				'preview_size' => array( 100, 100 ),
			);
		break;
		case 'slogan':
			$data = array(
				'id' => $prefix . 'business_slogan',
				'type' => 'text_small',
				'attributes' => array(
					'class' => 'wyz-input',
				),
			);
		break;
		case 'category':
			$data = array(
				'id'        => 'wyz_business_categories[]',
				'type'      => 'cats',
			);
		break;
		case 'categoryIcon':
			$data = array(
				'id'        => 'wyz_business_category_icon',
				'type'      => 'cats_icons',
				'desc'      => sprintf( esc_html__( 'Set the category icon that you want to represent your %s.', 'wyzi-business-finder' ), esc_html( ucwords( get_option( 'wyz_business_old_single_permalink' ) ) ) ),
			);
		break;
		case 'time':
			// Time open.
			$before_time_row = $initial ? '<fieldset>' : '';
			$wyz_cmb_businesses->add_field(
				array(
					'name' => esc_html__( 'opening', 'wyzi-business-finder' ),
					'type' => 'title',
					'id' => $prefix . 'business_open',
					'before_row'   => $before_time_row . '<div class="open-close-days"><div class="open-days">',
					'attributes' => array( 'class' => 'open-close-title title-sub' ),
					'after_row' => '</div>',
				)
			);
			$wyz_cmb_businesses->add_field(
				array(
					'name' => esc_html__( 'closing', 'wyzi-business-finder' ),
					'type' => 'title',
					'id' => $prefix . 'business_close',
					'before_row' => '<div class="close-days">',
					'attributes' => array( 'class' => 'open-close-title title-sub' ),
					'after_row' => '</div>',
				)
			);

			$_24_format = '24' == get_option( 'wyz_openclose_time_format_24' ) ? 'G:i' : 'h:i A';
			$fields_array = array(
				array(
					'name' => '',
					'id' => 'open',
					'type' => 'text_time',
					'time_format' => $_24_format,
				),
				array(
					'name' => '',
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
					'after_group_row' => wyz_after_group_row('monday'),
				)
			);
			$wyz_cmb_businesses->add_field(
				array(
					'id'          => $prefix . 'open_close_tuesday',
					'type'        => 'group',
					'description' => esc_html__( 'Tuesday', 'wyzi-business-finder' ),
					'options'     => $field_options,
					'fields'      => $fields_array,
					'after_group_row' => wyz_after_group_row('tuesday'),
				)
			);
			$wyz_cmb_businesses->add_field(
				array(
					'id'          => $prefix . 'open_close_wednesday',
					'type'        => 'group',
					'description' => esc_html__( 'Wednesday', 'wyzi-business-finder' ),
					'options'     => $field_options,
					'fields'      => $fields_array,
					'after_group_row' => wyz_after_group_row('wednesday'),
				)
			);
			$wyz_cmb_businesses->add_field(
				array(
					'id'          => $prefix . 'open_close_thursday',
					'type'        => 'group',
					'description' => esc_html__( 'Thursday', 'wyzi-business-finder' ),
					'options'     => $field_options,
					'fields'      => $fields_array,
					'after_group_row' => wyz_after_group_row('thursday'),
				)
			);
			$wyz_cmb_businesses->add_field(
				array(
					'id'          => $prefix . 'open_close_friday',
					'type'        => 'group',
					'description' => esc_html__( 'Friday', 'wyzi-business-finder' ),
					'options'     => $field_options,
					'fields'      => $fields_array,
					'after_group_row' => wyz_after_group_row('friday'),
				)
			);
			$wyz_cmb_businesses->add_field(
				array(
					'id'          => $prefix . 'open_close_saturday',
					'type'        => 'group',
					'description' => esc_html__( 'Saturday', 'wyzi-business-finder' ),
					'options'     => $field_options,
					'fields'      => $fields_array,
					'after_group_row' => wyz_after_group_row('saturday'),
				)
			);
			$wyz_cmb_businesses->add_field(
				array(
					'id'          => $prefix . 'open_close_sunday',
					'type'        => 'group',
					'description' => esc_html__( 'Sunday', 'wyzi-business-finder' ),
					'options'     => $field_options,
					'fields'      => $fields_array,
					'after_group_row' => wyz_after_group_row('sunday'),
				)
			);
			$after_time_row = '</div>';
			if ( 'separator' == $type_after ) {
				if ( $first_page ) {
					$first_page = false;
					$after_time_row  = '<button type="button" class="btn btn-next btn-square wyz-primary-color wyz-prim-color">' . esc_html__( 'Next', 'wyzi-business-finder' ) . '</button></fieldset>';
				} else
					$after_time_row  = '<button type="button" class="btn btn-previous wyz-primary-color wyz-prim-color btn-square">' . esc_html__( 'Previous', 'wyzi-business-finder' ) . '</button><button type="button" class="btn btn-next wyz-primary-color btn-square  wyz-prim-color">' . esc_html__( 'Next', 'wyzi-business-finder' ) . '</button></fieldset>';
			}
			elseif ( '' == $type_after ) {
				if ( 1 == $fields_count )
					$after_time_row = '</fieldset>';
				else {
					if ( $first_page )
						$after_time_row = '</fieldset>';
					else
						$after_time_row = '<button type="button" class="btn btn-previous wyz-primary-color wyz-prim-color btn-square">' . esc_html__( 'Previous', 'wyzi-business-finder' ) . '</button></fieldset>';
				}
			}
			
			if ($initial) {
			
			$initial = false;
			
			
			} 
			
			if('' == $type_after || 'separator' == $type_after) { 
			$after_time_row = '</div>' . $after_time_row ;
			}
			// Close time group.
			$wyz_cmb_businesses->add_field(
				array(
					'name' => '',
					'type' => 'title',
					'id' => $prefix . 'time_closer',
					'after_row' => $after_time_row,
				)
			);
		break;
		case 'bldg':
			$data = array(
				'id' => $prefix . 'business_bldg',
				'type' => 'text_small',
				'attributes' => array(
					'class' => 'wyz-input',
				),
			);
		break;
		case 'street':
			$data = array(
				'name' => esc_html__( 'Street', 'wyzi-business-finder' ),
				'id' => $prefix . 'business_street',
				'type' => 'text_small',
				'attributes' => array(
					'class' => 'wyz-input',
				),
			);
		break;
		case 'city':
			$data = array(
				'id' => $prefix . 'business_city',
				'type' => 'text_small',
				'attributes' => array(
					'class' => 'wyz-input',
				),
			);
		break;
		case 'location':
			$data = array(
				'id' => $prefix . 'business_country',
				'type' => 'select',
				'default_cb' => wyz_get_default_business_locations(),
				'options' => WyzHelpers::get_businesses_locations_options(),
				'attributes' => array(
					'class' => 'wyz-select',
				),
			);
		break;
		case 'range_radius':
			$range_desc = 'Range of coverage ';
			$range_desc .= 'mile' == get_option( 'wyz_business_map_radius_unit' ) ? '(in yards)' : '(in meters)';
			$range_desc = esc_html__( $range_desc, 'wyzi-business-finder' );
			$data = apply_filters( 'wyz_range_of_coverage_params', array(
				'id' => $prefix . 'range_radius',
				'type' => 'text_small',
				'desc' => $range_desc,
				'attributes' => array(
					'class' => 'wyz-input',
				),
			));
		break;
		case 'addAddress':
			$data = array(
				'id' => $prefix . 'business_addition_address_line',
				'type' => 'text_small',
				'attributes' => array( 'class' => 'wyz-input' ),
			);
		break;
		case 'zipcode':
			$data = array(
				'id' => $prefix . 'business_zipcode',
				'type' => 'text_small',
				'attributes' => array( 'class' => 'wyz-input' ),
			);
		break;
		case 'map':
			$data = array(
				'desc' => esc_html__( 'Choose Your Country then fine tune your location by moving the pointer', 'wyzi-business-finder' ),
				'latitude'=>12,
				'longitude'=>13,
				'id' => $prefix . 'business_location',
				'type' => 'pw_map',
				'split_values' => true,
			);
		break;
		case 'phone1':
			$data = array(
				'id' => $prefix . 'business_phone1',
				'type' => 'text_small',
				'attributes' => array(
					'class' => 'wyz-input',
				),
			);
		break;
		case 'phone2':
			$data = array(
				'id' => $prefix . 'business_phone2',
				'type' => 'text_small',
				'attributes' => array( 'class' => 'wyz-input' ),
			);
		break;
		case 'email1':
			$data = array(
				'id' => $prefix . 'business_email1',
				'type' => 'text_email',
				'attributes' => array(
					'class' => 'wyz-input',
				),
			);
		break;
		case 'email2':
			$data = array(
				'id' => $prefix . 'business_email2',
				'type' => 'text_email',
				'attributes' => array( 'class' => 'wyz-input' ),
			);
		break;
		case 'website':
			$data = array(
				'id' => $prefix . 'business_website',
				'type' => 'text_url',
				'attributes' => array( 'class' => 'wyz-input' ),
			);
		break;
		case 'fb':
			$data = array(
				'id' => $prefix . 'business_facebook',
				'type' => 'text_medium',
				'attributes' => array( 'class' => 'wyz-input' ),
			);
		break;
		case 'twitter':
			$data = array(
				'id' => $prefix . 'business_twitter',
				'type' => 'text_medium',
				'attributes' => array( 'class' => 'wyz-input' ),
			);
		break;
		case 'gplus':
			$data = array(
				'id' => $prefix . 'business_google_plus',
				'type' => 'text_medium',
				'attributes' => array( 'class' => 'wyz-input' ),
			);
		break;
		case 'linkedin':
			$data = array(
				'id' => $prefix . 'business_linkedin',
				'type' => 'text_medium',
				'attributes' => array( 'class' => 'wyz-input' ),
			);
		break;
		case 'youtube':
			$data = array(
				'id' => $prefix . 'business_youtube',
				'type' => 'text_medium',
				'attributes' => array( 'class' => 'wyz-input' ),
			);
		break;
		case 'insta':
			$data = array(
				'id' => $prefix . 'business_instagram',
				'type' => 'text_medium',
				'attributes' => array( 'class' => 'wyz-input' ),
			);
		break;
		case 'flicker':
			$data = array(
				'id' => $prefix . 'business_flicker',
				'type' => 'text_medium',
				'attributes' => array( 'class' => 'wyz-input' ),
			);
		break;
		case 'pinterest':
			$data = array(
				'id' => $prefix . 'business_pinterest',
				'type' => 'text_medium',
				'attributes' => array( 'class' => 'wyz-input' ),
			);
		break;
		case 'comments':
			$data = array(
				'id' => $prefix . 'business_comments',
				'type' => 'select',
				'default_cb' => 'on',
				'options' => array( 'on' => __( 'ON', 'wyzi-business-finder' ), 'off' => __( 'OFF', 'wyzi-business-finder' ) ),
				'attributes' => array(
					'class' => 'wyz-select',
				),
			);
		break;
		case 'ratings':
			$data = array(
				'id' => $prefix . 'business_ratings_on_off',
				'type' => 'select',
				'default_cb' => 'on',
				'options' => array( 'on' => __( 'ON', 'wyzi-business-finder' ), 'off' => __( 'OFF', 'wyzi-business-finder' ) ),
				'attributes' => array(
					'class' => 'wyz-select',
				),
			);
		break;
		case 'tags':
			$data = array(
				'desc'      => '',
				'id'        => 'wyz_business_tags[]',
				'type'      => 'tags',
			);
		break;
		case 'custom':
			$after_custom_row = '';
			if ( 'separator' == $type_after ) {
				if ( $first_page ) {
					$first_page = false;
					$after_custom_row  = '<button type="button" class="btn btn-next wyz-primary-color wyz-prim-color btn-square">' . esc_html__( 'Next', 'wyzi-business-finder' ) . '</button></fieldset>';
				} else
					$after_custom_row = '<button type="button" class="btn btn-previous wyz-primary-color wyz-prim-color btn-square">' . esc_html__( 'Previous', 'wyzi-business-finder' ) . '</button><button type="button" class="btn btn-next wyz-primary-color btn-square  wyz-prim-color">' . esc_html__( 'Next', 'wyzi-business-finder' ) . '</button></fieldset>';
			}
			elseif ( '' == $type_after ) {
				if ( 1 == $fields_count )
					$after_custom_row = '</fieldset>';
				else
					$after_custom_row = '<button type="button" class="btn btn-previous wyz-primary-color wyz-prim-color btn-square">' . esc_html__( 'Previous', 'wyzi-business-finder' ) . '</button></fieldset>';
			}

			$wyz_business_custom_form_data = get_option( 'wyz_business_custom_form_data', array() );

			$custom_size = is_array( $wyz_business_custom_form_data ) ? count( $wyz_business_custom_form_data ) : 0;
			$counter = 1;
			//Custom form fields
			if ( ! empty( $wyz_business_custom_form_data ) ) {
				foreach ( $wyz_business_custom_form_data as $key => $value ) {
					$type = '';
					$attr = array( 'class' => 'wyz-input' );
					$san_func = '';
					$after_row = '';
					$before_row = '';
					$options = array();
					$args = array();
					
					if ( ! empty( $value['required'] ) ) $attr['required'] = 'required';
					if ( ! empty( $value['cssClass'] ) ) $attr['class'] = $value['cssClass'];
					if ( ! empty( $value['placeholder'] ) ) $attr['placeholder'] = $value['placeholder'];

					if ( $custom_size == $counter++ )
						$after_row = $after_custom_row;
					if ( $initial ) {
						$initial = false;
						$before_row = '<fieldset>';
					}
					$select_defaults = '';
					switch ( $value['type'] ) {
						case 'label':
							$type = 'title';
							$attr['class'] .= ' section-label';
						break;
						case 'textbox':
							$type = 'text';
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
						case 'email':
							$type = 'text_email';
						break;
						case 'textarea':
							$type = 'textarea';
						break;
						case 'url':
							$type = 'text_url';
						break;
						case 'selectbox':
							$default = '';
							switch ($value['selecttype']) {
								case 'dropdown':
									$type = 'select';
									$attr['class'] .= ' wyz-select';
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
								if ( isset( $option['selected'] ) && $option['selected'] ){
									$select_defaults = $option['value'];
								}
							}
						break;
						case 'file':
							$type = 'file';
				    		$options['url'] = false;
				    		$attr = array( 'classes' => $attr['class'] );
						break;
						case 'date':
							$type = 'text_date';
							$attr = array( 'classes' => $attr['class'] );
						break;
						case 'wysiwyg':
							$type = 'wysiwyg';
							$before_row = '<div class="'.$value['cssClass'].'">';
							$after_row = '</div>';
				    		$options['media_buttons'] = $value['mediaupload'];
						break;
					}


					if ( '' != $type ) {
						$args = array(
							'name' => $value['label'],
							'id' => "wyzi_claim_fields_$key",
							'type' => $type,
							'attributes' => $attr,
							'after_row' => $after_row,
							'before_row' => $before_row,
						);
						if ( ! empty( $select_defaults ) )
							$args['default'] = $select_defaults;

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
			} else {
				$args = array(
					'id' => "wyzi_claim_fields_dummy",
					'type' => 'text',
					'attributes' => array
						(
							'class' => 'wyzi-hidden',
						),
					'after_row' => $after_custom_row,
				);
				$wyz_cmb_businesses->add_field( $args );
			}

		break;
		default:
			$data = apply_filters( 'wyz_business_registration_form_field', array(), $field, $type_after, $wyz_cmb_businesses, $initial, $first_page, $fields_count );
			if ( isset( $data['skip_addition'] ) )
				$initial = $first_page = false;

	}
	if ( 'time' != $field['type'] && 'custom' != $field['type'] && ! isset( $data['skip_addition'] ) ) {
		$data['name'] = $field['label'];
		if ( $initial ) {
			$initial = false;
			$data['before_row'] = '<fieldset>';
		}
		if ( 'separator' == $type_after ) {
			if ( $first_page ) {
				$first_page = false;
				$data['after_row'] = '<button type="button" class="btn btn-next wyz-primary-color wyz-prim-color btn-square">' . esc_html__( 'Next', 'wyzi-business-finder' ) . '</button></fieldset>';
			} else{
				$data['after_row'] = '<button type="button" class="btn btn-previous wyz-primary-color  wyz-prim-color btn-square">' . esc_html__( 'Previous', 'wyzi-business-finder' ) . '</button><button type="button" class="btn btn-next wyz-primary-color wyz-prim-color btn-square">' . esc_html__( 'Next', 'wyzi-business-finder' ) . '</button></fieldset>';
			}
		}
		elseif ( '' == $type_after ) {
			if ( 1 == $fields_count ){
				$data['after_row'] = '</fieldset>';
			}
			elseif(!$first_page){
				$data['after_row'] = '<button type="button" class="btn btn-previous wyz-primary-color wyz-prim-color btn-square">' . esc_html__( 'Previous', 'wyzi-business-finder' ) . '</button></fieldset>';
			}
		}
		if ( ! empty( $field['required'] ) ) $data['attributes']['required'] = 'required';
		if ( ! empty( $field['cssClass'] ) ) $data['attributes']['class'] = $field['cssClass'];
		if ( ! empty( $field['placeholder'] ) ) $data['attributes']['placeholder'] = $field['placeholder'];
		$wyz_cmb_businesses->add_field( $data );
	}
}


$wyz_business_form_data = apply_filters( 'wyz_business_registration_form_data', get_option( 'wyz_business_form_builder_data', array() ) );
//Custom form fields
if ( ! empty( $wyz_business_form_data ) && is_array( $wyz_business_form_data ) ) {
	if ( ! WyzHelpers::wyz_sub_can_bus_owner_do( get_current_user_id(),'wyzi_sub_business_can_custom_fields') ) {
		$just_before = false;
		$last_key = '';
		foreach ( $wyz_business_form_data as $key => $value ) {
			if ( 'custom' == $value['type'] ){
				unset( $wyz_business_form_data[ $key ] );
				if ( $just_before ) {
					unset( $wyz_business_form_data[ $last_key ] );
					$just_before=false;
				}
			}
			elseif ( 'separator' == $value['type']){
				if ( $just_before ) {
					unset( $wyz_business_form_data[ $key ] );
					$just_before = false;
				}
				else {
					$just_before = true;
					$last_key = $key;
				}
			}
			else {
				$just_before = false;	
			}
		}
	}
	$form_size = count( $wyz_business_form_data );
	$counter = 1;
	$keys = array_keys( $wyz_business_form_data );
	foreach ( $wyz_business_form_data as $key => $value ) {
		$after_type = '';
		if ( $counter < $form_size ){
			$after_type = $wyz_business_form_data[ $keys[ $counter++ ] ]['type'];
		}
		wyz_add_business_form_field( $value, $after_type, $wyz_cmb_businesses );
	}
}


function wyz_after_group_row( $name ) {
	$data = '';
	global $open_close_msgs;
	$data .= '<div class="open_all_day-info wyz-info"><p>' . $open_close_msgs['open'] . '</p></div>';
	$data .= '<div class="closed_all_day-info wyz-info"><p>' . $open_close_msgs['closed'] . '</p></div>';
	$data .= '<div class="open_close_status">';
	$prefix = 'wyz_';
	$values = array(
		array(
			'key' => 'open_all_day',
			'value' => $open_close_msgs['open']
		),
		array(
			'key' => 'closed_all_day',
			'value' => $open_close_msgs['closed']
		),
		array(
			'key' => 'custom',
			'value' => esc_html__('Custom time', 'wyzi-business-finder')
		),
	);

	$values = apply_filters( 'wyz_business_registration_form_open_close_labels', $values );
	$id = isset( $_GET[ WyzQueryVars::EditBusiness ] ) ? $_GET[ WyzQueryVars::EditBusiness ] : '';
	$id = isset( $_GET[ WyzQueryVars::AddNewBusiness ] ) ? $_GET[ WyzQueryVars::AddNewBusiness ] : $id;

	if ( empty( $id ) ) return '';
	$i=0;
	foreach( $values as $key => $value ) {
		$val = get_post_meta( $id, $prefix . 'open_close_' . $name . '_status', true );

		$data .= '<div><input '.( ( $val == $value['key'] || ( empty( $val ) && $i == 2 ) )?'checked ':'').'id="' . $prefix . "open_close_" . $name . "_status$i" . '" type="radio" name="' . $prefix . 'open_close_' . $name . '_status' . '" value="' . $value['key'] . '"/><label for="' . $prefix . 'open_close_' . $name . "_status$i" . '" >' . $value['value'] . '</label></div>';
		$i++;
	}
	$data .= '</div>';
	return $data;
}
/**
 * Override the default business form 'Submit' button.
 *
 * @param string  $form_format the default form format.
 * @param integer $object_id the form's id.
 * @param object  $cmb the current cmb form object.
 */
function wyz_options_modify_cmb2_metabox_form_format( $form_format, $object_id, $cmb ) {
	global $WYZ_USER_ACCOUNT_TYPE;
	if ( 'wyz_frontend_businesses' === $cmb->cmb_id ) {
		if ( isset( $_GET[ WyzQueryVars::EditBusiness ] ) && get_the_ID() == $_GET[ WyzQueryVars::EditBusiness ] ) {
			$lbl = esc_html__( 'Update Business' , 'wyzi-business-finder' );
		} else {
			$lbl = esc_html__( 'Create Business', 'wyzi-business-finder' );
		}
		return '<form class="cmb-form" method="post" id="%1$s" enctype="multipart/form-data" encoding="multipart/form-data"><input type="hidden" name="object_id" value="%2$s">%3$s<div class="submit-wrap"><input type="submit" name="submit-cmb" value="' . $lbl . '" class="wyz-primary-color wyz-prim-color btn-square button-primary"></div></form>';
	}

	return $form_format;
}
add_filter( 'cmb2_get_metabox_form_format', 'wyz_options_modify_cmb2_metabox_form_format', 10, 3 );

function wyz_intval( $value, $field_args, $field ) {
	if ( ! is_numeric( $value ) ) {
		$sanitized_value = '';
	} else {
		$sanitized_value = intval( $value );
	}
	return $sanitized_value;
}

function wyz_absint( $value, $field_args, $field ) {
	if ( ! is_numeric( $value ) ) {
		$sanitized_value = '';
	} else {
		$sanitized_value = absint( $value );
	}
	return $sanitized_value;
}

// In case business submission error.
$form_ids = array(
	$prefix . 'business_name',
	$prefix . 'business_logo',
	$prefix . 'business_excerpt',
	$prefix . 'business_description',
	$prefix . 'business_slogan',
	$prefix . 'business_adress',
	$prefix . 'business_bldg',
	$prefix . 'business_street',
	$prefix . 'business_city',
	$prefix . 'business_addition_address_line',
	$prefix . 'business_zipcode',
	$prefix . 'business_phone1',
	$prefix . 'business_phone2',
	$prefix . 'business_email1',
	$prefix . 'business_email2',
	$prefix . 'business_website',
	$prefix . 'business_facebook_app_id',
	$prefix . 'business_facebook',
	$prefix . 'business_twitter',
	$prefix . 'business_linkedin',
	$prefix . 'business_google_plus',
	$prefix . 'business_youtube',
	$prefix . 'business_instagram',
	$prefix . 'business_flicker',
	$prefix . 'business_pinterest',
	$prefix . 'business_ratings_on_off',
	$prefix . 'business_comments',
);

$time_form_ids = array(
	$prefix . 'open_close_monday',
	$prefix . 'open_close_tuesday',
	$prefix . 'open_close_wednesday',
	$prefix . 'open_close_thursday',
	$prefix . 'open_close_friday',
	$prefix . 'open_close_saturday',
	$prefix . 'open_close_sunday',
);

foreach ( $form_ids as $form_id ) {
	if ( filter_input( INPUT_POST, $form_id ) ) {
		$wyz_cmb_businesses->get_field( $form_id )->args['attributes']['value'] = filter_input( INPUT_POST, $form_id );
	}
}
foreach ( $time_form_ids as $form_id ) {
	if ( filter_input( INPUT_POST, $form_id ) ) {
		foreach ( filter_input( INPUT_POST, $form_id ) as $key => $value ) {
			$wyz_cmb_businesses->get_field( $form_id )[ $key ]->args['attributes']['value'] = $value;
		}
	}
}
if ( filter_input( INPUT_POST, $prefix . 'business_category_check' ) ) {
	$wyz_cmb_businesses->get_field( $prefix . 'business_category_check' )->args['default_cb'] = filter_input( INPUT_POST, $prefix . 'business_category_check' );
}
if ( filter_input( INPUT_POST, $prefix . 'business_country' ) ) {
	$wyz_cmb_businesses -> get_field( $prefix . 'business_country' )->args['default_cb'] = filter_input( INPUT_POST, $prefix . 'business_country' );
}
?>
