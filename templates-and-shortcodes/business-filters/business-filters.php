<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

abstract class WyzBusinessFilter {
	public $label;
	protected $name;
	protected $metadata;
	protected $params;
	protected $count_attr;

	public function __construct( $name, $label, $metadata ) {
		$this->name = $name;
		$this->label = apply_filters( 'wyz_custom_filter_field_label', $label, $name );
		$this->metadata = $metadata;
	}

	public abstract function content( $attr, $count_attr );
	public abstract function options();
	public abstract function options_values();


	protected function get_value( $def = '' ) {
		$value = '';
		if ( isset( $_GET[ $this->name  ] ) ) {
			$value = $_GET[ $this->name ];
		}
		return $value;
	}

	protected function parse_params( $attr ) {

		$data = array();
		$coor = explode( ',', substr( $attr, 1, -1), 5 );

		$data['width'] = intval( $coor[2] );
		if ( count( $coor ) == 5 && ! empty( $coor[4] ) ) {
			$attributes = explode( ',', substr( $coor[4], 1, -1) );
			$data['attributes'] = array();
			foreach ( $attributes as $attribute ) {
				$tmp = explode( ":", $attribute );
				if( count( $tmp ) == 2 ){
					$data['attributes'][ $tmp[0] ] = $tmp[1];
				}
			}
		} else {
			$data['attributes'] = '';
		}
		$this->params = $data;	
	}

	protected function css_classes( $type = '' ) {
		echo 'bus-filter nth-' . $this->count_attr[0] . ' width-' . $this->params['width'] . ' bus-filter-' . $this->name . (!empty($type)?' bus-filter-' . $type:'');
	}
}