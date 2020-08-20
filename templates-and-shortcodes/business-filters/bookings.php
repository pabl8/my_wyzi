<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

require_once( plugin_dir_path( __FILE__ ) . 'business-filters.php' );

class WyzBookingsFilter extends WyzBusinessFilter {

	public function __construct() {
		parent::__construct( 'bookings', esc_html__('Bookings', 'wyzi-business-finder'), '' );
	}


	/*
	 *
	 * @Override
	 */
	public function content( $attr, $count_attr ) {

		$this->parse_params( $attr );
		$this->count_attr = $count_attr;

		if ( isset( $_GET[ $this->name  ] ) ) {
			$value = $_GET[ $this->name ];
		}

		$date = isset( $_GET['booking-date'] ) ? $_GET['booking-date'] : '';
		ob_start();
		?>

		<div class="<?php $this->css_classes();?>">
			<input class="booking-filter-date" name="booking-date" type="text" placeholder="<?php esc_html_e( 'Bookings Date', 'wyzi-business-finder' );?>" value="<?php echo $date;?>"/>
		</div>
		<?php
		return ob_get_clean();
	}

	/*
	 *
	 * @Override
	 */
	public function options() {return;
		ob_start();?>
		<div class="filter-options">
			<label>Booking Date</label>
			<input type="text" id="booking-filter-date"  data-key="booking-date"/>
		</div>
		
		<?php return ob_get_clean();
	}

	public function options_values() {
		return '';
	}
}