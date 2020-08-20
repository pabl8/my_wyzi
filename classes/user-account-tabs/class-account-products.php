<?php

class AccountProducts extends AccountContent {

	public function the_condition() {
		$this->condition = $this->is_business_owner && class_exists( 'WooCommerce' ) &&
							WyzHelpers::is_user_vendor( $this->user_id ) &&
							'off' != get_option( 'wyz_display_vendor_products' );
	}

	public function _active () {
		return false;
	}

	public function tab_title () {
		$this->tab_title = esc_html( get_option( 'products_tab_label' ) );
	}

	public function link () {
		$this->link = 'products';
	}

	public function icon () {
		$this->icon = 'shopping-bag';
	}

	public function notifications() { }

	public function content() {
		global $current_user;
		wp_get_current_user();
		$user_login = $current_user->user_login;
		if (  WyzHelpers::user_can_edit_products() && WyzHelpers::product_need_display_wyzi_edit_link() )
			add_action('woocommerce_after_shop_loop_item_title',function(){
				echo '<a href="?product_id='. get_the_ID() .'&action=edit" class="prod-edit">'.esc_html__('edit','wyzi-business-finder').'</a>';
			});

		if(WyzHelpers::user_can_publish_products($this->user_id)){

			echo '<div id="shop-settings" class="float-right" style="margin-bottom: 10px;"><a class="wyz-primary-color wyz-prim-color btn-square" href="'.get_home_url(null,'/user-account/?product_id=1').'">'.esc_html__('Add a Product','wyzi-business-finder').'</a></div>';

		}

		$args = array(
			'post_type' => 'product',
			'post_status' => array('publish','pending'),
			'author' => $this->user_id,
			'posts_per_page' => -1,
			'fields' => 'ids',
		);

		$products_query = new WP_Query( $args );
		$posts = implode( ',', $products_query->posts );

		echo do_shortcode( "[wcmp_products per_page=-1 ids='$posts']" );
	}
}
?>