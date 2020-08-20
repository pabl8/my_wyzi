<?php

class AccountBusiness extends AccountContent {

	public function the_condition() { $this->condition = $this->is_business_owner; }

	public function _active () {
		return $this->is_business_owner && (!WyzUserAccount::subscribtion_tab_on() ) && (! isset( $_POST['wyz-updateuser'] ) && ( ! function_exists( 'is_wc_endpoint_url' ) || ! is_wc_endpoint_url() ) ) ;
	}

	public function tab_title () {
		$this->tab_title = esc_html__( 'my business', 'wyzi-business-finder' );
	}

	public function link () {
		$this->link = 'my-business';
	}

	public function icon () {
		$this->icon = 'briefcase';
	}


	public function notifications() {
		$output = '';
		if ( isset( $_GET['post_submitted'] ) ) {
			if ( 'off' == get_option( 'wyz_offer_immediate_publish' ) ) {

				$output .= WyzHelpers::wyz_success( sprintf( esc_html__( 'Thank you, your new %s is now pending for submission.', 'wyzi-business-finder' ), esc_html( $name ), WYZ_OFFERS_CPT ),true);

			} else {
				$output .= WyzHelpers::wyz_success( sprintf( esc_html__( 'Thank you, your new %s has been published successfully.', 'wyzi-business-finder' ), WYZ_OFFERS_CPT ),true);
			}
		} elseif( isset( $_GET['offer_updated'] ) ) {

			// Add notice of submission to our output.
			$output .= WyzHelpers::wyz_success( sprintf( esc_html__( 'Thank you, your %s was updated successfully.', 'wyzi-business-finder' ), WYZ_OFFERS_CPT ),true);
		}
		echo $output;
	}

	public function content() {
		echo wyz_business();
	}
}
?>