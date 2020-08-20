<?php
class AccountProfile extends AccountContent {

	public function the_condition() { $this->condition = true; }

	public function _active () {
		return isset( $_POST['updateuser'] ) || ! $this->is_business_owner;
	}

	public function tab_title () {
		$this->tab_title = esc_html__( 'profile', 'wyzi-business-finder' );
	}

	public function link () {
		$this->link = 'profile';
	}

	public function icon () {
		$this->icon = 'user';
	}

	public function notifications() {
		return;
	}

	public function content() {
		echo wyz_user_profile_form_display();
	}
}
?>