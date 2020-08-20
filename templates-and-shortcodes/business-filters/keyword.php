<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

require_once( plugin_dir_path( __FILE__ ) . 'business-filters.php' );

class WyzKeywordFilter extends WyzBusinessFilter {

	public function __construct() {
		parent::__construct( 'keyword', esc_html__('Keyword', 'wyzi-business-finder'), '' );
	}


	/*
	 *
	 * @Override
	 */
	public function content( $attr, $count_attr ) {

		$this->parse_params( $attr );
		$this->count_attr = $count_attr;

		$value = $this->get_value();
		ob_start();
		?>
		<div class="<?php $this->css_classes();?>"><input name="<?php echo $this->name;?>" type="text" id="search-keyword" placeholder="<?php 
		echo apply_filters( 'wyz_keyword_filter_placeholder', esc_html__('Keyword', 'wyzi-business-finder') );?>" value="<?php echo $value;?>"></div>
		<?php
		return ob_get_clean();
	}

	/*
	 *
	 * @Override
	 */
	public function options() {
		return '';
	}

	public function options_values() {
		return '';
	}
}