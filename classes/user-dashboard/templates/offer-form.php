<?php
if ( isset( $_GET[ WyzQueryVars::AddNewOffer ] ) ) {
	global $draft_id;
	$draft_id = wyz_create_draft_offer();
	add_action( 'cmb2_after_init', 'wyz_handle_frontend_new_offer_submission_form' );
	require_once( WYZI_PLUGIN_DIR . 'businesses-and-offers/offers/front-end-offer-submission.php' );?>
	<div class="wyz-form-wrapper">
		<?php echo wyz_display_add_new_offer_form( array() );?>
	</div>
	<?php
} elseif ( isset( $_GET[ WyzQueryVars::EditOffer ] ) ) {

	add_action( 'cmb2_after_init', 'wyz_handle_frontend_offer_update_form' );
	require_once( WYZI_PLUGIN_DIR . 'businesses-and-offers/offers/edit-offer.php' );
	?>
	<div class="wyz-form-wrapper">
		<?php echo wyz_do_frontend_offers_edit( array() );?>
	</div>
	<?php
}