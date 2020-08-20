<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

require_once( plugin_dir_path( __FILE__ ) . 'business-filters.php' );

class WyzVerifiedFilter extends WyzBusinessFilter {

	public function __construct() {
		parent::__construct( 'verified', esc_html__('Verified', 'wyzi-business-finder'), '' );
	}


	/*
	 *
	 * @Override
	 */
	public function content( $attr, $count_attr ) {

		$this->parse_params( $attr );
		$this->count_attr = $count_attr;

		$lbl = isset($this->params['attributes']['label']) ? $this->params['attributes']['label'] : esc_html__( 'Show only Verified', 'wyzi-busines-finder' );

		$value = isset( $_GET[ $this->name ] );
		ob_start();
		?>
		<div class="<?php $this->css_classes();?>"><label><?php echo $lbl;?></label><input name="<?php echo $this->name;?>" type="checkbox"<?php echo ( $value ? ' checked="checked"' : '');?>></div>
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
		jQuery('#wyz-filter-verified-label').on('change',function(){
			var val = jQuery(this).val();
			var key = jQuery(this).data('key');
			jQuery(document).trigger( "wyz-filter-option-change", [ 'verified', key, val ] );
		});
		</script>
		<div class="filter-options">
			<label>Label:</label>
			<input id="wyz-filter-verified-label" data-key="label"/>
		</div>

		<?php
		return ob_get_clean();
	}

	public function options_values() {
		return array(
			'label' => 'Verified',
		);
	}
}