<?php

class AccountVendor extends AccountContent {

	public function the_condition() {
		$this->condition = ! current_user_can( 'manage_options' ) && $this->is_business_owner && WyzHelpers::wyz_sub_can_bus_owner_do( $this->user_id,'wyzi_sub_business_can_apply_vendor')
							&& WyzHelpers::wyz_has_business( $this->user_id ) && class_exists( 'WooCommerce' ) && function_exists( 'is_user_wcmp_vendor' ) &&
							! is_user_wcmp_vendor( $this->user_id ) && 'off' != get_option( 'wyz_can_become_vendor' ) && get_option( 'woocommerce_enable_myaccount_registration' ) === 'yes' ;
	}

	public function _active () {
		return false;
	}

	public function tab_title () {
		$this->tab_title = esc_html__( 'Become a vendor', 'wyzi-business-finder' );
	}

	public function link () {
		$this->link = 'vendor-form';
	}

	public function icon () {
		$this->icon = 'usd';
	}

	public function notifications() {
		
	}

	public function content() {
		if ( function_exists( 'is_user_wcmp_rejected_vendor' ) ) {
			$vend_id = get_current_vendor_id();
			if ( is_user_wcmp_pending_vendor( $vend_id ) ) {
				if ( ! is_user_wcmp_rejected_vendor( $vend_id ) )
					echo WyzHelpers::wyz_info( esc_html__( 'You already have a pending vendor request.', 'wyzi-business-finder' ) );
				else
					echo WyzHelpers::wyz_error( esc_html__( 'Your vendor application was rejected.', 'wyzi-business-finder' ) );
				return;
			} elseif ( is_user_wcmp_rejected_vendor( $vend_id ) ) {
				echo WyzHelpers::wyz_error( esc_html__( 'Your vendor application was rejected.', 'wyzi-business-finder' ) );
				return;
			}
		} else {
			$peding_form_id = get_user_meta($this->user_id, 'wcmp_vendor_registration_form_id', true);
			if ( ! empty($peding_form_id) && $peding_form_id ) {
				$form_ower = get_post_meta($peding_form_id, 'user_id', true);
				if ( $form_ower == $this->user_id && 'publish' == get_post_status($peding_form_id) ) {
					$user = $user = wp_get_current_user();
					if ( in_array( 'dc_rejected_vendor', (array) $user->roles ) ) {
						echo WyzHelpers::wyz_error( esc_html__( 'Your vendor application was rejected.', 'wyzi-business-finder' ) );
					}else
						echo WyzHelpers::wyz_info( esc_html__( 'You already have a pending vendor request.', 'wyzi-business-finder' ) );
					return;
				}
			}
		}
		
		echo do_shortcode( '[vendor_registration]' );
	}
}

?>