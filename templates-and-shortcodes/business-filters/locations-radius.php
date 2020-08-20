<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

require_once( plugin_dir_path( __FILE__ ) . 'business-filters.php' );

class WyzLocationRadiusFilter extends WyzBusinessFilter {

	private $data;

	public function __construct() {
		parent::__construct( 'radius', esc_html__('Radius:', 'wyzi-business-finder'), '' );
	}

	public function content( $attr, $count_attr ) {

		$this->parse_params( $attr );
		$this->count_attr = $count_attr;
		$value = $this->get_value();

		ob_start();
		$min = isset( $this->params['attributes']['minrange'] ) ? $this->params['attributes']['minrange'] : 1;
		$max = isset( $this->params['attributes']['max-range'] ) ? $this->params['attributes']['max-range'] : 100;
		$step = isset( $this->params['attributes']['step-range'] ) ? $this->params['attributes']['step-range'] : 1;
		$def = isset( $this->params['attributes']['def-val'] ) ? $this->params['attributes']['def-val'] : 0;

		if(empty($value))$value=$def;
		?>
		<div class="<?php $this->css_classes();?>">
			<p><?php echo $this->label;?>  <span class="slider-val"></span><span> <?php echo ( 'mile' == get_option( 'wyz_business_map_radius_unit' ) ? 'mile' : 'km'  );?></span>
			<input name="<?php echo $this->name;?>" class="number-filter-range" type="range" value="<?php echo $value;?>" min="<?php echo $min;?>" max="<?php echo $max;?>" step="<?php echo $step;?>" />
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
		jQuery('#wyz-number-min-range,#wyz-number-max-range,#wyz-number-step-range,#wyz-number-def-range').on('change',function(){
			var val = jQuery(this).val();
			var key = jQuery(this).data('key');
			jQuery(document).trigger( "wyz-filter-option-change", [ '<?php echo $this->name;?>', key, val ] );
		});
		/*jQuery('#wyz-number-min-range').trigger('change');
		jQuery('#wyz-number-max-range').trigger('change');
		jQuery('#wyz-number-step-range').trigger('change');*/
		</script>
		<div class="filter-options">
			<label>Min Range:</label>
			<input type="number" id="wyz-number-min-range" data-key="minrange"/>
		</div>
		<div class="filter-options">
			<label>Max Range:</label>
			<input type="number" id="wyz-number-max-range" data-key="max-range"/>
		</div>
		<div class="filter-options">
			<label>Slider Step:</label>
			<input type="number" id="wyz-number-step-range" data-key="step-range"/>
		</div>
		<div class="filter-options">
				<label>Default value:</label>
				<input type="number" id="wyz-number-def-range" data-key="def-val"/>
			</div>
		<?php
		return ob_get_clean();
	}

	public function options_values() {
		return array(
			'minrange' => '1',
			'max-range' => '100',
			'step-range' => '1',
			'def-val' => '0',
		);
	}
}