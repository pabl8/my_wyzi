<?php
class AccountSubscription extends AccountContent {

	public function the_condition() {
		$this->condition = $this->is_business_owner && 'on' == get_option( 'wyz_sub_mode_on_off' );
	}

	public function _active () {
		return WyzUserAccount::subscribtion_tab_on();
	}

	public function tab_title () {
		$this->tab_title = esc_html__( 'Subscription', 'wyzi-business-finder' );
	}

	public function link () {
		$this->link = 'subscription';
	}

	public function icon () {
		$this->icon = 'list-alt';
	}

	public function notifications() { }

	public function content() {
	
	$wyz_sub_pay_woo_on_off = get_option('wyz_sub_pay_woo_on_off','off');

		if ( 'off' == $wyz_sub_pay_woo_on_off ) {
			if ( function_exists('pmpro_hasMembershipLevel') && pmpro_hasMembershipLevel() && function_exists( 'pmpro_getOption' ) ) {
				echo '<div style="height: 50px;"><a style="float:right;" href="' . get_the_permalink( pmpro_getOption("account_page_id") ) . '" class="action-btn btn-bg-blue btn-rounded wyz-button wyz-primary-color wyz-prim-color">' . esc_html__( 'Manage Subscription', 'wyzi-business-finder' ) . '</a></div>';
			}
			$levels = apply_filters( 'wyz_membership_levels_ids', '' );
			echo do_shortcode( '[memberlite_levels'.(!empty($levels)?(' levels='.implode(',', $levels)):'').']'); 
		}
	
		
		else { 
			if( is_user_logged_in() && function_exists('pmpro_hasMembershipLevel') && pmpro_hasMembershipLevel() ) {
				global $current_user;
				echo '<div style="height: 50px;"><a style="float:right;" href="' . get_the_permalink( pmpro_getOption("account_page_id") ) . '" class="action-btn btn-bg-blue btn-rounded wyz-button wyz-primary-color wyz-prim-color">' . esc_html__( 'Manage Subscription', 'wyzi-business-finder' ) . '</a></div>';
				$current_user->membership_level = pmpro_getMembershipLevelForUser($current_user->ID);
				WyzHelpers::wyz_info( sprintf( esc_html__( 'Your Membership Level: %s', 'wyzi-business-finder' ),$current_user->membership_level->name ) );
			} else {
				echo WyzHelpers::wyz_info( sprintf( esc_html__( 'You don\'t have a subscription yet', 'wyzi-business-finder' ) ) );
			}
		// lets get products Ids with memberships assigned to it
		global $wpdb;
			
		$product_ids = '';
			
		$get_product_ids_with_membership = $wpdb->get_results( "SELECT post_id FROM  ".$wpdb->prefix . "postmeta where meta_key ='_membership_product_level' and meta_value != 0");
			
		foreach ($get_product_ids_with_membership as  $key  ) {
			$product_ids .= $key->post_id .',';
		}
		
		if (!empty($product_ids)) 
			echo do_shortcode('[products ids="'.$product_ids.'"]');
		 
		}
		
	}
}
?>