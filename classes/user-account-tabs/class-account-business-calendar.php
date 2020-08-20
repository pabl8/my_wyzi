<?php
class BusinessCalendar extends AccountContent {

	public function the_condition() {
		$this->condition = $this->is_business_owner && 'off' != get_option( 'wyz_users_can_booking' ) && WyzHelpers::wyz_sub_can_bus_owner_do($this->user_id,'wyzi_sub_business_can_create_bookings') &&
						class_exists( 'WooCommerce' );
	}

	public function _active () {
		return false;
	}

	public function tab_title () {
		$this->tab_title = esc_html__( 'Booking Calendar', 'wyzi-business-finder' );
	}

	public function link () {
		$this->link = 'booking-calendar';
	}

	public function icon () {
		$this->icon = 'calendar-check-o';
	}

	public function notifications() { }

	public function content() {
		if ( WyzHelpers::get_user_calendar() ) {
			//$bk = new booked_plugin();$bk->plugin_settings_page();
		}
	}

}
?>