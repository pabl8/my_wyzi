<?php

global $WYZ_USER_ACCOUNT;
$WYZ_USER_ACCOUNT_TYPE;

//redirect to my account page after business deletion
add_action( 'init', function () {
	if ( is_admin() ) return;
	if ( isset( $_GET['trashed'] ) && isset( $_GET['ids'] ) && isset( $_GET['manage-business'] ) ){
		wp_redirect( home_url( '/user-account/' ) );
	}
});

//redirect un-loged in users to signup page after entering un-authorized pages
add_action( 'pre_get_posts', function () {
	if ( is_admin() ) return;
	if ( ! is_user_logged_in() && is_page( 'user-account' ) ){
		wp_redirect( home_url( '/signup/' ) );
	}
});

global $WYZ_USER_ACCOUNT;
global $WYZ_USER_ACCOUNT_TYPE;
require_once( plugin_dir_path( __FILE__ ) . 'class-user-account.php' );
function wyz_init_user_account() {

	global $WYZ_USER_ACCOUNT;
	global $WYZ_USER_ACCOUNT_TYPE;

	if ( isset( $_GET[ WyzQueryVars::AddNewBusiness ] ) ) $WYZ_USER_ACCOUNT_TYPE = WyzQueryVars::AddNewBusiness;
	elseif ( isset( $_GET[ WyzQueryVars::EditBusiness ] ) ) $WYZ_USER_ACCOUNT_TYPE = WyzQueryVars::EditBusiness;
	elseif ( isset( $_GET[ WyzQueryVars::ManageBusiness ] ) ) $WYZ_USER_ACCOUNT_TYPE = WyzQueryVars::ManageBusiness;
	elseif ( isset( $_GET[ WyzQueryVars::AddNewOffer ] ) ) $WYZ_USER_ACCOUNT_TYPE = WyzQueryVars::AddNewOffer;
	elseif ( isset( $_GET[ WyzQueryVars::EditOffer ] ) ) $WYZ_USER_ACCOUNT_TYPE = WyzQueryVars::EditOffer;
	elseif ( isset( $_GET[ WyzQueryVars::GetPoints ] ) ) $WYZ_USER_ACCOUNT_TYPE = WyzQueryVars::GetPoints;
	elseif ( isset( $_GET[ WyzQueryVars::TransferPoints ] ) ) $WYZ_USER_ACCOUNT_TYPE = WyzQueryVars::TransferPoints;
	elseif ( isset( $_GET[ WyzQueryVars::BusinessCalendar ] ) ) $WYZ_USER_ACCOUNT_TYPE = WyzQueryVars::BusinessCalendar;
	elseif ( isset( $_GET[ WyzQueryVars::AddProduct ] ) ) $WYZ_USER_ACCOUNT_TYPE = WyzQueryVars::AddProduct;
	elseif ( is_page( 'user-account' ) ) $WYZ_USER_ACCOUNT_TYPE = WyzQueryVars::Dashboard;
	else $WYZ_USER_ACCOUNT_TYPE = false;
	$WYZ_USER_ACCOUNT_TYPE = apply_filters( 'wyz_user_account_type', $WYZ_USER_ACCOUNT_TYPE );
}
add_action( 'wp', 'wyz_init_user_account',1 );

add_action( 'init', function() {

	global $WYZ_USER_ACCOUNT;
	global $WYZ_USER_ACCOUNT_TYPE;

	require_once( plugin_dir_path( __FILE__ ) . 'user-dashboard/cmb2-handler.php' );
	if ( 'design_2' == get_option( 'wyz_user_dashboard_template' ) ) {
		add_filter( 'template_include', function( $template_path ){
			global $post;
			if ( is_object( $post ) && has_shortcode( $post->post_content, 'wyz_my_account' ) )
				return plugin_dir_path( __FILE__ ) . 'user-dashboard/class-user-dashboard.php';

			return $template_path;
		}, 1 );
	}
		
	add_action( 'wp', function(){
		global $WYZ_USER_ACCOUNT;
		global $WYZ_USER_ACCOUNT_TYPE;
		$WYZ_USER_ACCOUNT = new WyzUserAccount( $WYZ_USER_ACCOUNT_TYPE );
	},2 );
}, 1);

function wyz_get_bus_offers_ajax() {
	if ( ! isset( $_POST['del'] ) )wp_die('0');
	$b_id = WyzHelpers::encrypt( $_POST['del'], 'd');
	require_once( plugin_dir_path( __FILE__ ) . 'user-dashboard/templates/table-offers.php' );
	$table = new WyzDashboardOffersTable( get_current_user_id(), $b_id );
	$columns = $table->get_the_columns();
	if ( '' == $columns )
		wp_die('0');

	wp_die( $columns );
}
add_action( 'wp_ajax_ud_get_business_offers', 'wyz_get_bus_offers_ajax' );
add_action( 'wp_ajax_nopriv_ud_get_business_offers', 'wyz_get_bus_offers_ajax' );

function wyz_get_bus_products_ajax() {
	if ( ! isset( $_POST['del'] ) )wp_die('0');
	$b_id = WyzHelpers::encrypt( $_POST['del'], 'd');
	require_once( plugin_dir_path( __FILE__ ) . 'user-dashboard/templates/table-offers.php' );
	$table = new WyzDashboardOffersTable( get_current_user_id(), $b_id );
	$columns = $table->get_the_columns(true);
	if ( '' == $columns )
		wp_die('0');

	wp_die( $columns );
}
add_action( 'wp_ajax_ud_get_business_products', 'wyz_get_bus_products_ajax' );
add_action( 'wp_ajax_nopriv_ud_get_business_products', 'wyz_get_bus_products_ajax' );


function wyz_get_bus_jobs_ajax() {
	if ( ! isset( $_POST['del'] ) )wp_die('0');
	$b_id = WyzHelpers::encrypt( $_POST['del'], 'd');
	require_once( plugin_dir_path( __FILE__ ) . 'user-dashboard/templates/table-jobs.php' );
	$table = new WyzDashboardJobsTable( get_current_user_id(), $b_id );
	$columns = $table->get_the_columns();
	if ( '' == $columns )
		wp_die('0');

	wp_die( $columns );
}
add_action( 'wp_ajax_ud_get_business_jobs', 'wyz_get_bus_jobs_ajax' );
add_action( 'wp_ajax_nopriv_ud_get_business_jobs', 'wyz_get_bus_jobs_ajax' );





