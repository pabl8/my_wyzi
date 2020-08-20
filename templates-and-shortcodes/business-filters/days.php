<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

require_once( plugin_dir_path( __FILE__ ) . 'business-filters.php' );

class WyzDaysFilter extends WyzBusinessFilter {

	public function __construct() {
		parent::__construct( 'open_days', esc_html__( 'Open Days', 'wyzi-business-finder'), '' );
	}


	/*
	 *
	 * @Override
	 */
	public function content( $attr, $count_attr ) {

		$this->parse_params( $attr );
		$this->count_attr = $count_attr;

		$days_get = $this->get_value();

		add_action( 'wp_footer', function(){
			wp_enqueue_script( 'jQuery_tags_select', WYZI_PLUGIN_URL . 'classes/js/selectize.min.js', array( 'jquery' ), false, true );
			wp_enqueue_style( 'jQuery_tags_select_style', WYZI_PLUGIN_URL . 'classes/css/selectize.default.css' );
		}, 10 );

		ob_start();
		?>
		<div class="<?php $this->css_classes();?>">
			<?php $days = array( 
				'mon' => esc_html__( 'Monday', 'wyzi-business-finder' ),
				'tue' => esc_html__( 'Tuesday', 'wyzi-business-finder' ),
				'wed' => esc_html__( 'Wednesday', 'wyzi-business-finder' ),
				'thur' => esc_html__( 'Thursday', 'wyzi-business-finder' ),
				'fri' => esc_html__( 'Friday', 'wyzi-business-finder' ),
				'sat' => esc_html__( 'Saturday', 'wyzi-business-finder' ),
				'sun' => esc_html__( 'Sunday', 'wyzi-business-finder' ),
			); ?>
			<select multiple name="open_days[]" id="wyz-day-filter" class="wyz-selectize-filter" data-selectator-keep-open="true" placeholder="<?php esc_html_e( 'Open Days', 'wyzi-business-finder' );?>">
				<?php
				foreach ( $days as $key => $value ) {
					echo '<option value="' . $key . '"';
					if ( ! empty( $days_get ) && in_array( $key, $days_get ) ) {
						echo ' selected="selected"';
					}
					echo  '>'. $value . '</option>';
				}
				?>
			</select>
			<div class="tagchecklist hide-if-no-js"></div>
		</div>
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