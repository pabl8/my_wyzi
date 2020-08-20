<?php
add_action( 'cmb2_after_init', 'wyz_handle_cmb2_submitted_forms' );

function wyz_handle_cmb2_submitted_forms() {
	if ( !isset( $_GET['page'] ) ) return;

	switch ($_GET['page']) {
		case 'add-edit-business':
			if( isset( $_GET[ WyzQueryVars::AddNewBusiness ] ) ) {
				require_once( apply_filters( 'front_end_business_submission_path', WYZI_PLUGIN_DIR . 'businesses-and-offers/businesses/front-end-business-submission.php' ) );
				wyz_handle_frontend_new_business_submission_form();
			} elseif(isset( $_GET[ WyzQueryVars::EditBusiness ])) {
				require_once( apply_filters( 'front_end_business_edit_path', WYZI_PLUGIN_DIR . 'businesses-and-offers/businesses/edit-business.php' ) );
				wyz_handle_frontend_business_update_form();
			}
			break;
		case 'add-edit-offer':
			if( isset( $_GET[ WyzQueryVars::AddNewOffer ] ) )
				wyz_handle_frontend_new_offer_submission_form();
			elseif(isset( $_GET[ WyzQueryVars::EditOffer ]))
				wyz_handle_frontend_offer_update_form();
			break;
		default:
			# code...
			break;
	}
}
