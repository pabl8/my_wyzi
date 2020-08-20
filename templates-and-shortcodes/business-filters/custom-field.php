<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

require_once( plugin_dir_path( __FILE__ ) . 'business-filters.php' );

class WyzCustomFieldFilter extends WyzBusinessFilter {

	private $data;

	public function __construct( $name ) {
		$data = get_option( 'wyz_business_filters' )['custom_fields'];

		if ( ! isset( $data[ $name ] ) ) {
			parent::__construct( '', '', '' );
			return;
		}
		$this->data = $data[ $name ];
		parent::__construct( $name, $this->data['label'], '' );
	}


	/*
	 *
	 * @Override
	 */
	public function content( $attr, $count_attr ) {

		$this->parse_params( $attr );
		$this->count_attr = $count_attr;

		switch ( $this->data['type'] ) {
			case 'textbox':
			case 'email':
			case 'url':
			case 'textarea':
			case 'wysiwyg':
				return $this->content_text();
			case 'number':
				return $this->content_number();
			case 'selectbox':
				if( $this->data['selecttype'] == 'dropdown' || $this->data['selecttype'] == 'radio' ) {
					return $this->content_dropdown();
				}
				if( $this->data['selecttype'] == 'checkboxes' ) {
					return $this->content_checkbox();
				}
		}
		return '';
	}

	private function content_text() {
		$value = $this->get_value();
		ob_start();
		?>
		<div class="<?php $this->css_classes( $this->data['type'] );?>"><input name="<?php echo $this->name;?>" type="text" placeholder="<?php echo sprintf( esc_html__( 'Search %s', 'wyzi-business-finder' ), $this->label );?>" value="<?php echo $value;?>"></div>
		<?php
		return ob_get_clean();
	}

	private function content_number() {
		ob_start();
		if( isset( $this->params['attributes']['number-type'] ) && 'slider' == $this->params['attributes']['number-type'] ) {
			$this->content_number_slider();
		} else {
			$this->content_number_text();
		}
		return ob_get_clean();
	}


	private function content_number_slider() {
		$value = $this->get_value();
		$min = isset( $this->params['attributes']['min-range'] ) ? $this->params['attributes']['min-range'] : 0;
		$max = isset( $this->params['attributes']['max-range'] ) ? $this->params['attributes']['max-range'] : 100;
		$step = isset( $this->params['attributes']['step-range'] ) ? $this->params['attributes']['step-range'] : 1;
		$def = isset( $this->params['attributes']['def-val'] ) ? $this->params['attributes']['def-val'] : 0;

		if(empty($value))$value=$def;
		?>
		<div class="<?php $this->css_classes( $this->data['type'] );?>">
			<p><?php echo $this->label;?>:  <span class="slider-val"></span><!-- <a href="#" class="cancel-slider-filter">X</a> --></p>
			<input name="<?php echo $this->name;?>" class="number-filter-range" type="range" value="<?php echo $value;?>" min="<?php echo $min;?>" max="<?php echo $max;?>" step="<?php echo $step;?>" />
		</div>	
		<?php
	}

	private function content_number_text() {
		$value = $this->get_value();
		?>
		<div class="<?php $this->css_classes( $this->data['type'] );?>"><input name="<?php echo $this->name;?>" type="number" placeholder="<?php echo sprintf( esc_html__( 'Search %s', 'wyzi-business-finder' ), $this->label );?>" value="<?php echo $value;?>"></div>
		<?php
	}

	private function content_dropdown() {
		$value = $this->get_value();
		ob_start();
		?>
		<div class="<?php $this->css_classes( $this->data['type'] );?>">
			<select class="wyz-input wyz-select" name="<?php echo $this->name;?>">
				<option value=''><?php echo sprintf( esc_html__( 'Select %s', 'wyzi-business-finder' ), $this->label );?></option>
				<?php foreach ($this->data['options'] as $option) {
					echo '<option value="' . $option['value'] . '"' . ( $option['value'] == $value ? 'selected' : '' ) . '>' . $option['label'] . '</option>';
				}?>
			</select>
		</div>
		<?php
		return ob_get_clean();
	}


	private function content_checkbox() {
		$value = $this->get_value();
		ob_start();
		add_action( 'wp_footer', function(){
			wp_enqueue_script( 'jQuery_tags_select', WYZI_PLUGIN_URL . 'classes/js/selectize.min.js', array( 'jquery' ), false, true );
			wp_enqueue_style( 'jQuery_tags_select_style', WYZI_PLUGIN_URL . 'classes/css/selectize.default.css' );
		}, 10 );
		?>
		<div class="<?php $this->css_classes();?>">
			<?php $multiple = apply_filters('wyz_custom_search_field_checkbox_multiple', true, $this->data );?>
			<select <?php echo $multiple ? 'multiple' : '' ;?> name="<?php echo $this->name;?><?php echo $multiple ? '[]' : '' ;?>" class="wyz-selectize-filter" data-selectator-keep-open="<?php echo $multiple ? 'true' : 'false' ;?>" placeholder="<?php echo sprintf( esc_html__( 'Select %s', 'wyzi-business-finder' ), $this->label );?>">
				<?php foreach ($this->data['options'] as $option) {
					echo '<option value="' . $option['value'] . '"' . ( ( is_array( $value ) ? ( in_array( $option['value'], $value ) ) : $option['value'] == $value  )  ? 'selected' : '' ) . '>' . $option['label'] . '</option>';
				}?>
			</select>
		</div>
		<?php
		return ob_get_clean();
	}

	/*
	 *
	 * @Override
	 */
	public function options() {
		if ( $this->data['type'] == 'number' ) {
			ob_start();?>
			<script type="text/javascript">
			jQuery('#wyz-number-filter-type,#wyz-number-min-range,#wyz-number-max-range,#wyz-number-step-range,#wyz-number-def-range').on('change',function(){
				var val = jQuery(this).val();
				var key = jQuery(this).data('key');
				jQuery(document).trigger( "wyz-filter-option-change", [ '<?php echo $this->name;?>', key, val ] );
				if(this.id == 'wyz-number-filter-type' ){
					if('slider'==val){
						jQuery('#wyz-number-min-range').parent().show();
						jQuery('#wyz-number-max-range').parent().show();
						jQuery('#wyz-number-step-range').parent().show();
						jQuery('#wyz-number-def-range').parent().show();
					} else{
						jQuery('#wyz-number-min-range').parent().hide();
						jQuery('#wyz-number-max-range').parent().hide();
						jQuery('#wyz-number-step-range').parent().hide();
						jQuery('#wyz-number-def-range').parent().hide();
					}
				}
			});

			</script>
			<div class="filter-options">
				<label>Filter type:</label>
				<select id="wyz-number-filter-type" data-key="number-type">
					<option value="text">Text Input</option>
					<option value="slider">Range Slider</option>
				</select>
			</div>
			<div class="filter-options">
				<label>Min Range:</label>
				<input type="number" id="wyz-number-min-range" data-key="min-range"/>
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
		return '';
	}

	public function options_values() {
		//data-key => default_value
		if ( $this->data['type'] == 'number' ) {
			return array(
				'number-type' => 'text',
				'min-range' => '0',
				'max-range' => '100',
				'step-range' => '1',
				'def-val' => '0',
			);
		}

		return '';
	}
}