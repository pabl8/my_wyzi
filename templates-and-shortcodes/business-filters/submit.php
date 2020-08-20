<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

require_once( plugin_dir_path( __FILE__ ) . 'business-filters.php' );

class WyzFilterSubmit extends WyzBusinessFilter {

	public function __construct() {
		parent::__construct( 'submit', 'Search', '' );
	}


	/*
	 *
	 * @Override
	 */
	public function content( $attr, $count_attr ) {


		$this->parse_params( $attr );
		$this->count_attr = $count_attr;
		$lbl = isset($this->params['attributes']['label']) ? $this->params['attributes']['label'] : esc_html__( 'Search', 'wyzi-busines-finder' );
		ob_start();
		?>
		<div class="input-submit <?php $this->css_classes();?>">
			<button type="submit" class="wyz-primary-color wyz-secon-color wyz-prim-color-hover wyz-secon-color bus-filter-submit"><i class="fa fa-search"></i> <?php echo $lbl;?></button>
		</div>
		<?php
		return ob_get_clean();
	}

	/*
	 *
	 * @Override
	 */
	public function options() {
		ob_start();?>
		<script type="text/javascript">
		jQuery('#wyz-filter-submit-label').on('change',function(){
			var val = jQuery(this).val();
			var key = jQuery(this).data('key');
			jQuery(document).trigger( "wyz-filter-option-change", [ 'submit', key, val ] );
		});
		</script>
		<div class="filter-options">
			<label>Label:</label>
			<input id="wyz-filter-submit-label" data-key="label"/>
		</div>

		<?php
		return ob_get_clean();
	}

	public function options_values() {
		return array(
			'label' => 'Search',
		);
	}
}