<?php

class AccountWoo extends AccountContent {

	public function the_condition() { 
		$this->condition = class_exists( 'WooCommerce' ) && 'on' != get_option( 'wyz_woocommerce_hide_orders_tab' );
	}

	public function _active () {
		return function_exists( 'is_wc_endpoint_url' ) && is_wc_endpoint_url();
	}

	public function tab_title () {
		$this->tab_title = esc_html__( 'Shop', 'wyzi-business-finder' );
	}

	public function link () {
		$this->link = 'woo-profile';
	}

	public function icon () {
		$this->icon = 'shopping-cart';
	}


	public function notifications() { }

	public function content() {
		echo do_shortcode( '[woocommerce_my_account]' );
	}
}
?>