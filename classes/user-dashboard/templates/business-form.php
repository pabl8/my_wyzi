<?php
if ( isset( $_GET[ WyzQueryVars::AddNewBusiness ] ) ) {
	global $draft_id;
	$transfer_complete = -1;
	$draft_id = wyz_create_draft_business();
	add_action( 'cmb2_after_init', 'wyz_handle_frontend_new_business_submission_form' );
	require_once( apply_filters( 'front_end_business_submission_path', WYZI_PLUGIN_DIR . 'businesses-and-offers/businesses/front-end-business-submission.php' ) );?>
	<div class="wyz-form-wrapper">
		<?php echo wyz_display_add_new_business_form( array() );?>
	</div>
	<?php
} elseif ( isset( $_GET[ WyzQueryVars::EditBusiness ] ) ) {
	add_action( 'cmb2_after_init', 'wyz_handle_frontend_business_update_form' );
	require_once( apply_filters( 'front_end_business_edit_path', WYZI_PLUGIN_DIR . 'businesses-and-offers/businesses/edit-business.php' ) );
	?>
	<div class="wyz-form-wrapper">
		<?php echo wyz_do_frontend_business_edit( array() );?>
	</div>
	<?php
}