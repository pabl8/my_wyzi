<?php
class InternalMessaging extends AccountContent {

	public function the_condition() {
		$this->condition = 'on' == get_option( 'wyz_private_msg_status_on_off' ) && ( ( $this->is_business_owner && WyzHelpers::wyz_sub_can_bus_owner_do($this->user_id,'wyzi_sub_business_have_inbox') ) || current_user_can( 'manage_options' ) || ( WyzHelpers::wyz_is_current_user_client() && 'on' != get_option( 'wyz_private_msg_hide_client' ) ) );
	}

	public function _active () {
	    return false;
	}

	public function tab_title () {
		$this->tab_title = esc_html__( 'Inbox', 'wyzi-business-finder' );
	}

	public function link () {
		$this->link = 'inbox';
	}

	public function icon () {
		$this->icon = 'envelope';
	}


	public function notifications() {
		return;
	}

	public function content() {

		if ( is_user_logged_in() ) {
			echo do_shortcode('[private_message_user_inbox]');
		}
	}
}
?>