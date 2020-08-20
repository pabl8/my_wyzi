<?php
class AccountBooking extends AccountContent {

	public function the_condition() {
		$this->condition =  'off' != get_option( 'wyz_users_can_booking' );
	}

	public function _active () {
		return false;
	}

	public function tab_title () {
		$this->tab_title = esc_html__( 'Appointments', 'wyzi-business-finder' );
	}

	public function link () {
		$this->link = 'booking';
	}

	public function icon () {
		$this->icon = 'calendar';
	}

	public function notifications() { }

	public function content() {
		echo do_shortcode( '[booked-profile]' );
	}
}
?>