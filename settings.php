<?php
/**
 * Add settings admin page.
 */
//delete_option('wyz_business_tabs_order_data');
function wyz_settings_menu() {
	// Settings menu page.
	$parent_slug = 'admin.php';
	$page_title = esc_html__( 'Wyzi Toolkit Options', 'wyzi-business-finder' );
	$menu_title = esc_html__( 'Toolkit Options', 'wyzi-business-finder' );
	$capability = 'manage_options';
	$menu_slug = 'wyzi-toolkit-options';
	$function = 'wyz_toolkit_settings_content';
	$position = 58;
	$menu = add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, plugins_url( 'templates-and-shortcodes/images/wyz-toolkit-options.png', __FILE__ ), $position );

	add_action( 'admin_print_styles-' . $menu, 'wyz_toolkit_enqueue_custom_css' );

	add_action( 'admin_init', 'wyz_register_business_settings' );
	add_action( 'admin_init', 'wyz_register_single_business_settings' );
	add_action( 'admin_init', 'wyz_register_business_custom_fields_settings' );
	add_action( 'admin_init', 'wyz_register_registration_fields_settings' );
	add_action( 'admin_init', 'wyz_register_user_registration_fields_settings' );
	add_action( 'admin_init', 'wyz_register_business_form_fields_builder_settings' );
	add_action( 'admin_init', 'wyz_register_single_business_tab_fields_settings' );
	add_action( 'admin_init', 'wyz_register_offer_settings' );
	add_action( 'admin_init', 'wyz_register_woocommerce_settings' );
	add_action( 'admin_init', 'wyz_register_privileges_settings' );
	add_action( 'admin_init', 'wyz_claim_settings' );
	add_action( 'admin_init', 'wyz_claim_form' );
	add_action( 'admin_init', 'wyz_social_login_settings' );
	add_action( 'admin_init', 'wyz_private_msg_settings' );
}
add_action( 'admin_menu', 'wyz_settings_menu' );


/**
 * Enqueue toolkit settings page custom css.
 */
function wyz_toolkit_enqueue_custom_css(){
	wp_enqueue_style( 'wyz_toolkit_custom_css', plugin_dir_url( __FILE__ ) . 'templates-and-shortcodes/css/wyz-toolkit-settings.css' );
}


add_action('admin_init',function(){
	if ( isset( $_POST['form-builder-reset'] ) )
		update_option('wyz_business_form_builder_data',array());
	if ( isset( $_POST['business-tabs-reset'] ) )
		update_option('wyz_business_tabs_order_data',array());
	if ( isset( $_POST['business-sidebar-reset'] ) )
		update_option('wyz_business_sidebar_order_data',array());
	if ( isset( $_POST['business-custom-fields-reset'] ) )
		update_option('wyz_business_custom_form_data',array());
	if ( isset( $_POST['reg-form-builder-reset'] ) ){
		update_option('wyz_registration_form_data',wyz_get_reg_form_builder_defaults());
	}
	if ( isset( $_POST['claim-form-builder-reset'] ) )
		update_option('wyz_claim_registration_form_data',array());
});

if ( ! function_exists( 'wyz_toolkit_settings_content' ) ) {
	/**
	 * Business settings page content handler.
	 */ 
	function wyz_toolkit_settings_content() {
		?>
		<div class="wrap">
		<h1><?php esc_html_e( 'Wyzi Toolkit Options', 'wyzi-business-finder' ); ?></h1>
		<div class="nav-tab-wrapper">

			<?php if ( isset( $_GET['updated'] ) && 'true' == esc_attr( $_GET['updated'] ) ) {
				echo '<div class="updated" ><p>' . esc_html__( 'Settings updated', 'wyzi-business-finder' ) . '</p></div>';
			}
			if ( isset( $_GET['tab'] ) ) {
				wyz_admin_tabs( $_GET['tab'] );
			} else {
				wyz_admin_tabs( 'businesses' );
			} ?>

			<div id="poststuff">
					<?php global $pagenow;
					if ( 'admin.php' == $pagenow && isset( $_GET['page'] ) && 'wyzi-toolkit-options' == $_GET['page'] ) {

						if ( isset( $_GET['tab'] ) ) {
							$tab = $_GET['tab'];
						} else {
							$tab = 'businesses';
						}

						switch ( $tab ) {

						// Business settings page content.
						case 'businesses': ?>
							<tr>
								<form class="wyz_settings_form" method="post" action="options.php">
									<?php 
									submit_button();
									settings_fields( 'wyz_businesses_settings' );
									do_settings_sections( 'wyz_businesses_settings' ); ?>
									<table class="form-table">
										<tr valign="top">
										<?php $opt = get_option( 'wyz_sub_mode_on_off', 'off');?>
											<th scope="row">
												<div class="help-tip">
													<p class="description"><?php esc_html_e( 'Important: Turning Subscription Mode On will overide Registration Cost, and you will be able to control what a Business Owner can and cannot do from Memberships Levels. If you already have Business Accounts, enabling this feature will affect previosuly created businesses.', 'wyzi-business-finder' )?></p>
												</div>
											<?php esc_html_e( 'Subscription Mode', 'wyzi-business-finder' );?></th> 
											<td>
												<fieldset>
												<select class="wyz-select" name="wyz_sub_mode_on_off" id="wyz_sub_mode_on_off">
												<option value="off" <?php
												echo( 'off' == $opt ? 'selected' : '' ); ?>><?php esc_html_e( 'OFF', 'wyzi-business-finder' );?></option>
												<option value="on" <?php
												echo( 'on' == $opt ? 'selected' : '' ); ?>><?php esc_html_e( 'ON', 'wyzi-business-finder' );?></option>
												</select>
												</fieldset>
											</td>
										</tr>
										<tr valign="top">
										<?php $opt = get_option( 'wyz_sub_pay_woo_on_off', 'off');?>
											<th scope="row">
											<div class="help-tip">
												<p class="description"><?php esc_html_e( 'Turn this Option On in case you plan to use WooCommerce as the checkout page for Subscription, otherwise if you keep it off you can use available payment gateways found in Membership > Payment Settings. Make sure Paid Memberships Pro - WooCommerce Add On plugin is installed for this option to work. You can install it from Appearance > Install Plugins', 'wyzi-business-finder' )?></p>
											</div>
											<?php esc_html_e( 'WooCommerce Payment', 'wyzi-business-finder' );?></th> 
											<td>
												<fieldset>
												<select class="wyz-select" name="wyz_sub_pay_woo_on_off" id="wyz_sub_pay_woo_on_off">
												<option value="off" <?php
												echo( 'off' == $opt ? 'selected' : '' ); ?>><?php esc_html_e( 'OFF', 'wyzi-business-finder' );?></option>
												<option value="on" <?php
												echo( 'on' == $opt ? 'selected' : '' ); ?>><?php esc_html_e( 'ON', 'wyzi-business-finder' );?></option>
												</select>
												</fieldset>
											</td>
										</tr>
										<tr valign="top">
											<?php $opt = get_option( 'wyz_users_can_booking' );?>
											<th scope="row">
											<div class="help-tip">
												<p class="description"><?php esc_html_e( 'Allows business owners to create calendars for his business', 'wyzi-business-finder' );?></p>
											</div>
											<?php esc_html_e( 'Enable Booking', 'wyzi-business-finder' );?></th> 
											<td>
												<fieldset>
												<select class="wyz-select" name="wyz_users_can_booking" id="wyz_users_can_booking">
												<option value="on" <?php
												echo( 'on' == $opt ? 'selected' : '' ); ?>><?php esc_html_e( 'ON', 'wyzi-business-finder' );?></option><option value="off" <?php
												echo( 'off' == $opt ? 'selected' : '' ); ?>><?php esc_html_e( 'OFF', 'wyzi-business-finder' );?></option>
												</select>
												</fieldset>
											</td>
										</tr>
										<tr valign="top">
											<?php $opt = get_option( 'wyz_users_can_job' );?>
											<th scope="row">
											<div class="help-tip">
												<p class="description"><?php esc_html_e( 'Allows business owners to submit jobs (requires wp job manager plugin)', 'wyzi-business-finder' );?></p>
											</div>
											<?php esc_html_e( 'Enable Job Submits', 'wyzi-business-finder' );?></th> 
											<td>
												<fieldset>
												<select class="wyz-select" name="wyz_users_can_job" id="wyz_users_can_job">
												<option value="off" <?php
												echo( 'off' == $opt ? 'selected' : '' ); ?>><?php esc_html_e( 'OFF', 'wyzi-business-finder' );?></option>
												<option value="on" <?php
												echo( 'on' == $opt ? 'selected' : '' ); ?>><?php esc_html_e( 'ON', 'wyzi-business-finder' );?></option>
												</select>
												</fieldset>
											</td>
										</tr>
										<tr valign="top">
											<?php $opt = get_option( 'wyz_job_requires_business' );?>
											<th scope="row">
											<div class="help-tip">
												<p class="description"><?php esc_html_e( 'Business owners can publish a job only if they have a published business', 'wyzi-business-finder' );?></p>
											</div>
											<?php esc_html_e( 'Job Rquires a Business', 'wyzi-business-finder' );?></th> 
											<td>
												<fieldset>
												<select class="wyz-select" name="wyz_job_requires_business" id="wyz_job_requires_business">
												<option value="off" <?php
												echo( 'off' == $opt ? 'selected' : '' ); ?>><?php esc_html_e( 'OFF', 'wyzi-business-finder' );?></option>
												<option value="on" <?php
												echo( 'on' == $opt ? 'selected' : '' ); ?>><?php esc_html_e( 'ON', 'wyzi-business-finder' );?></option>
												</select>
												</fieldset>
											</td>
										</tr>
										<tr valign="top">
											<?php $opt = get_option( 'wyz_jobs_max', 0 );?>
											<th scope="row">
											<div class="help-tip">
												<p class="description"><?php esc_html_e( 'set to zero for no limit', 'wyzi-business-finder' );?></p>
											</div>
											<?php esc_html_e( 'Maximum allowed jobs per user', 'wyzi-business-finder' );?></th> 
											<td>
												<fieldset>
												<input type="number" name="wyz_jobs_max" min=0 value="<?php echo esc_attr( $opt ); ?>"/>
												</fieldset>
											</td>
										</tr>
										<tr valign="top">
											<?php $opt = get_option( 'wyz_job_submit_cost', 0 );?>
											<th scope="row">
											<div class="help-tip">
												<p class="description"><?php esc_html_e( 'Cost in Points for Job Submissions', 'wyzi-business-finder' );?></p>
											</div>
											<?php esc_html_e( 'Job Submition Cost', 'wyzi-business-finder' );?></th> 
											<td>
												<fieldset>
												<input type="number" name="wyz_job_submit_cost" min=0 value="<?php echo esc_attr( $opt ); ?>"/>
												</fieldset>
											</td>
										</tr>
										<tr valign="top">
											<th scope="row">
											<div class="help-tip">
												<p class="description"><?php esc_html_e( 'Number of featured businesses to display at the top of the listing, set to 0 to disable', 'wyzi-business-finder' );?></p>
											</div>
											<?php esc_html_e( 'Featured Businesses per Page', 'wyzi-business-finder' ) ;?></th> 
											<td>
											<fieldset><input type="number" name="wyz_featured_posts_perpage" min=0 value="<?php
											echo esc_attr( get_option( 'wyz_featured_posts_perpage', 2 ) ); ?>"/>
											</fieldset>
											</td>
										</tr>
										<tr valign="top">
											<?php $opt = get_option( 'wyz_businesses_registery_price', 0 );?>
											<th scope="row">
											<div class="help-tip">
												<p class="description"><?php esc_html_e( 'Cost in Points for new Business Registration, set zero to keep it free', 'wyzi-business-finder' );?></p>
											</div>
											<?php esc_html_e( 'Business Registration Cost', 'wyzi-business-finder' );?></th> 
											<td>
											<fieldset><input type="number" name="wyz_businesses_registery_price" min=0 value="<?php
											echo esc_attr( $opt ); ?>"/>
											</fieldset>
											</td>
										</tr>
										<tr valign="top">
											<th scope="row">
											<div class="help-tip">
												<p class="description"><?php esc_html_e( 'Cost in Points for Post new creation in Business wall, set zero to keep it free', 'wyzi-business-finder' );?></p>
											</div>
											<?php esc_html_e( 'Post Submission Cost', 'wyzi-business-finder' );?></th> 
											<td>
											<fieldset><input type="number" name="wyz_business_post_cost" min=0 value="<?php
											echo esc_attr( get_option( 'wyz_business_post_cost', 0 ) ); ?>"/>
											</fieldset>
											</td>
										</tr>
										<tr valign="top">
											<?php $opt = get_option( 'wyz_businesses_points_transfer', 'on' );?>
											<th scope="row">
											<div class="help-tip">
												<p class="description"><?php esc_html_e( 'Enable users to tranfer points between one another', 'wyzi-business-finder' );?></p>
											</div>
											<?php esc_html_e( 'Points Transfer', 'wyzi-business-finder' );?></th> 
											<td><fieldset>
											<select class="wyz-select" name="wyz_businesses_points_transfer">
											<option value="off" <?php
											echo( 'off' == $opt ? 'selected' : '' ); ?>><?php esc_html_e( 'OFF', 'wyzi-business-finder' );?></option>
											<option value="on" <?php
											echo( 'on' == $opt ? 'selected' : '' ); ?>><?php esc_html_e( 'ON', 'wyzi-business-finder' );?></option>
											</select>
											</fieldset></td>
										</tr>
										<tr valign="top">
											<?php $opt = get_option( 'wyz_hide_points', 'on' );?>
											<th scope="row">
											<div class="help-tip">
												<p class="description"><?php esc_html_e( 'Hide points status in the user dashboard page', 'wyzi-business-finder' );?></p>
											</div>
											<?php esc_html_e( 'Hide Points', 'wyzi-business-finder' );?></th> 
											<td><fieldset>
											<select class="wyz-select" name="wyz_hide_points">
											<option value="off" <?php
											echo( 'off' == $opt ? 'selected' : '' ); ?>><?php esc_html_e( 'OFF', 'wyzi-business-finder' );?></option>
											<option value="on" <?php
											echo( 'on' == $opt ? 'selected' : '' ); ?>><?php esc_html_e( 'ON', 'wyzi-business-finder' );?></option>
											</select>
											</fieldset></td>
										</tr>
										<tr valign="top">
											<th scope="row">
											<div class="help-tip">
												<p class="description"><?php esc_html_e( 'Cost in Points for transferring pionts between users.', 'wyzi-business-finder' );?></p>
											</div>
											<?php esc_html_e( 'Points transfer Cost', 'wyzi-business-finder' );?></th> 
											<td>
											<fieldset><input type="number" name="wyz_points_transfer_fee" min=0 value="<?php
											echo esc_attr( get_option( 'wyz_points_transfer_fee', 0 ) ); ?>"/>
											</fieldset>
											</td>
										</tr>
										<tr valign="top">
											<th scope="row">
											<div class="help-tip">
												<p class="description"><?php esc_html_e( 'Maximum allowed number of businesses for a business owner to have.', 'wyzi-business-finder' );?></p>
											</div>
											<?php esc_html_e( 'Maximum allowed Businesses', 'wyzi-business-finder' );?></th> 
											<td>
											<fieldset><input type="number" name="wyz_max_allowed_businesses" min=0 value="<?php
											echo esc_attr( get_option( 'wyz_max_allowed_businesses', 1 ) ); ?>"/>
											</fieldset>
											</td>
										</tr>
										<tr valign="top">
											<th scope="row">
											<div class="help-tip">
												<p class="description"><?php esc_html_e( 'Maximum allowed number of attachments for one user to upload. (-1 for unlimited)', 'wyzi-business-finder' );?></p>
											</div>
											<?php esc_html_e( 'Maximum attachments', 'wyzi-business-finder' );?></th> 
											<td>
											<fieldset><input type="number" name="wyz_max_attchmtn_count" value="<?php
											echo esc_attr( get_option( 'wyz_max_attchmtn_count', -1 ) ); ?>"/>
											</fieldset>
											</td>
										</tr>
										<tr valign="top">
											<th scope="row">
											<div class="help-tip">
												<p class="description"><?php esc_html_e( 'Enable Favorite Business Feature so Users can favourite Businesses', 'wyzi-business-finder' )?></p>
											</div>
											<?php esc_html_e( 'Favorite business', 'wyzi-business-finder' );?></th> 
											<td>
												<?php $opt = get_option( 'wyz_enable_favorite_business', 'off' );?>
												<fieldset>
												<select class="wyz-select" name="wyz_enable_favorite_business" id="immediate-publish">
												<option value="off" <?php
												echo( 'off' == $opt ? 'selected' : '' ); ?>><?php esc_html_e( 'OFF', 'wyzi-business-finder' );?></option>
												<option value="on" <?php
												echo( 'on' == $opt ? 'selected' : '' ); ?>><?php esc_html_e( 'ON', 'wyzi-business-finder' );?></option>
												</select>
												</fieldset>
											</td>
										</tr>
										<tr valign="top">
											<th scope="row">
											<div class="help-tip">
												<p class="description"><?php esc_html_e( 'Client Subscription Name as it appears to new registered Users', 'wyzi-business-finder' );?></p>
											</div>
											<?php esc_html_e( 'Client Subscription', 'wyzi-business-finder' );?></th> 
											<td><fieldset>
												<input type="text" class="regular-text" name="wyz_businesses_user_client" value="<?php echo esc_attr( get_option( 'wyz_businesses_user_client' ) ); ?>"/>
											</fieldset></td>
										</tr>
										<tr valign="top">
											<th scope="row">
											<div class="help-tip">
												<p class="description"><?php esc_html_e( 'Business Subscription Name as it appears to new registered Users', 'wyzi-business-finder' );?></p>
											</div>
											<?php esc_html_e( 'Business Subscription', 'wyzi-business-finder' );?></th>
											<td><fieldset>
												<input type="text" class="regular-text" name="wyz_businesses_user_owner" value="<?php echo esc_attr( get_option( 'wyz_businesses_user_owner' ) ); ?>"/>
											</fieldset></td>
										</tr>
										<tr valign="top">
											<th scope="row">
											<div class="help-tip">
												<p class="description"><?php esc_html_e( 'Additional points to be added to new business owners', 'wyzi-business-finder' );?></p>
											</div>
											<?php esc_html_e( 'New Business Owner extra points', 'wyzi-business-finder' );?></th> 
											<td><fieldset><input type="number" name="wyz_add_points_registration" min=0 value="<?php
											echo esc_attr( get_option( 'wyz_add_points_registration', 0 ) ); ?>"/>
											</fieldset></td>
										</tr>
										<?php /*<tr valign="top">
											<th scope="row"><?php esc_html_e( 'Enable Map lock', 'wyzi-business-finder' );?></th>
											<td><fieldset>
												<select class="wyz-select" name="wyz_map_lockable" id="scroll-zoom">
													<option value="off" <?php echo( 'off' === get_option( 'wyz_map_lockable','off' ) ? 'selected' : '' ); ?>><?php esc_html_e( 'OFF', 'wyzi-business-finder' );?></option>
													<option value="on" <?php echo( 'on' === get_option( 'wyz_map_lockable' ) ? 'selected' : '' ); ?>><?php esc_html_e( 'ON', 'wyzi-business-finder' );?></option>
												</select><p class="description"><?php esc_html_e( 'Display/Hide the lock icon which locka/unlocks this map.', 'wyzi-business-finder' );?>.</p></fieldset>
											</td>
										</tr>*/
										?>

										<tr valign="top">
											<th scope="row">
											<div class="help-tip">
												<p class="description"><?php esc_html_e( 'Hide map in Business Archives Page.', 'wyzi-business-finder' );?>.</p>
											</div>
											<?php esc_html_e( 'Hide Archives page Header', 'wyzi-business-finder' );?></th>
											<td><fieldset>
												<select class="wyz-select" name="wyz_business_map_hide_in_archives" id="scroll-zoom">
													<option value="off" <?php echo( 'off' === get_option( 'wyz_business_map_hide_in_archives','off' ) ? 'selected' : '' ); ?>><?php esc_html_e( 'OFF', 'wyzi-business-finder' );?></option>
													<option value="on" <?php echo( 'on' === get_option( 'wyz_business_map_hide_in_archives' ) ? 'selected' : '' ); ?>><?php esc_html_e( 'ON', 'wyzi-business-finder' );?></option>
												</select>
												</fieldset>
											</td>
										</tr>
										<tr valign="top">
											<th scope="row">
											<div class="help-tip">
												<p class="description"><?php esc_html_e( 'Archives page map zoom level. 0: max zoomed out, 22: max zoomed in. Set to -1 for activate marker autofit.', 'wyzi-business-finder' );?></p>
											</div>
											<?php esc_html_e( 'Archives map zoom', 'wyzi-business-finder' );?></th> 
											<td><fieldset><input type="number" name="wyz_archives_map_zoom" max=22 value="<?php
											echo esc_attr( get_option( 'wyz_archives_map_zoom', 12 ) ); ?>"/>
											</fieldset></td>
										</tr>


										<tr valign="top">
											<th scope="row">
												<div class="help-tip">
												<p class="description"><?php esc_html_e( 'Shows Business Preview sidebar on the map upon clikcing Business Location Pin in Archives Maps', 'wyzi-business-finder' );?></p>
											</div>
											<?php esc_html_e( 'Archives map sidebar', 'wyzi-business-finder' );?></th>
											<td>
												<fieldset>
													<select class="wyz-select" name="wyz_archives_map_sidebar" id="wyz_archives_map_sidebar">
														<option value="off" <?php echo( 'off' === get_option( 'wyz_archives_map_sidebar','off' ) ? 'selected' : '' ); ?>><?php esc_html_e( 'OFF', 'wyzi-business-finder' );?></option>
														<option value="on" <?php echo( 'on' === get_option( 'wyz_archives_map_sidebar' ) ? 'selected' : '' ); ?>><?php esc_html_e( 'ON', 'wyzi-business-finder' );?></option>
													</select>
												</fieldset>
											</td>
										</tr>

										<tr valign="top">
											<th scope="row">
											<div class="help-tip">
												<p class="description"><?php esc_html_e( 'Activate User GeoLocation Service in Archives Maps', 'wyzi-business-finder' );?></p>
											</div>
											<?php esc_html_e( 'Archives map My Location', 'wyzi-business-finder' );?></th>
											<td>
												<fieldset>
													<select class="wyz-select" name="wyz_archives_map_my_loction" id="wyz_archives_map_my_loction">
														<option value="off" <?php echo( 'off' === get_option( 'wyz_archives_map_my_loction','off' ) ? 'selected' : '' ); ?>><?php esc_html_e( 'OFF', 'wyzi-business-finder' );?></option>
														<option value="on" <?php echo( 'on' === get_option( 'wyz_archives_map_my_loction' ) ? 'selected' : '' ); ?>><?php esc_html_e( 'ON', 'wyzi-business-finder' );?></option>
													</select>
												</fieldset>
											</td>
										</tr>

										<tr valign="top">
											<th scope="row">
											<div class="help-tip">
												<p class="description"><?php esc_html_e( 'Show Listings/Businesses in Grid View in Archives pages,Category Pages & Location Pages in Template 1. If you are using Template 2, then you will need to use Essential Grid, as template 2 lists the Businesses using Essential Grid Alais, review Wyzi Documentation for more details.', 'wyzi-business-finder' );?></p>
											</div>
											<?php esc_html_e( 'Archives Businesses as Grid', 'wyzi-business-finder' );?></th>
											<td>
												<fieldset>
													<select class="wyz-select" name="wyz_archives_grid_view" id="wyz_archives_grid_view">
														<option value="off" <?php echo( 'off' === get_option( 'wyz_archives_grid_view','off' ) ? 'selected' : '' ); ?>><?php esc_html_e( 'OFF', 'wyzi-business-finder' );?></option>
														<option value="on" <?php echo( 'on' === get_option( 'wyz_archives_grid_view' ) ? 'selected' : '' ); ?>><?php esc_html_e( 'ON', 'wyzi-business-finder' );?></option>
													</select>
												</fieldset>
											</td>
										</tr>

										<tr valign="top">
											<th scope="row">
											<div class="help-tip">
												<p class="description"><?php esc_html_e( 'Content to display before the listings in business archives page, accepts shortcodes and html.', 'wyzi-business-finder');?></p>
											</div>
											<?php esc_html_e( 'Business Archives content', 'wyzi-business-finder' );?></th>
											<td>
												<fieldset>
													<textarea rows="8" cols="80" name="wyz_business_archive_before_listings"><?php echo get_option('wyz_business_archive_before_listings');?></textarea>
												</fieldset>
											</td>
										</tr>


										<tr valign="top">
											<th scope="row">
											<div class="help-tip">
												<p class="description"><?php esc_html_e( 'Hide points of interest on single business page and archives maps', 'wyzi-business-finder' );?>.</p>
											</div>
											<?php esc_html_e( 'Hide map POI', 'wyzi-business-finder' );?></th>
											<td><fieldset>
												<select class="wyz-select" name="wyz_hide_map_poi" id="wyz_hide_map_poi">
													<option value="off" <?php echo( 'off' === get_option( 'wyz_hide_map_poi','off' ) ? 'selected' : '' ); ?>><?php esc_html_e( 'OFF', 'wyzi-business-finder' );?></option>
													<option value="on" <?php echo( 'on' === get_option( 'wyz_hide_map_poi' ) ? 'selected' : '' ); ?>><?php esc_html_e( 'ON', 'wyzi-business-finder' );?></option>
												</select>
												</fieldset>
											</td>
										</tr>
										<tr valign="top">
											<th scope="row">
											<div class="help-tip">
												<p class="description"><?php esc_html_e( 'Enable/Disable using the mouse scroll wheel to zoom in business map', 'wyzi-business-finder' );?>.</p>
											</div>
											<?php esc_html_e( 'Map zooming by scroll wheel', 'wyzi-business-finder' );?></th>
											<td><fieldset>
												<select class="wyz-select" name="wyz_business_map_scroll_zoom" id="scroll-zoom">
													<option value="off" <?php echo( 'off' === get_option( 'wyz_business_map_scroll_zoom' ) ? 'selected' : '' ); ?>><?php esc_html_e( 'OFF', 'wyzi-business-finder' );?></option>
													<option value="on" <?php echo( 'on' === get_option( 'wyz_business_map_scroll_zoom' ) ? 'selected' : '' ); ?>><?php esc_html_e( 'ON', 'wyzi-business-finder' );?></option>
												</select>
												</fieldset>
											</td>
										</tr>
										<tr valign="top">
											<th scope="row">
											<div class="help-tip">
												<p class="description"><?php esc_html_e( 'Unit of measurment in global map radius slider', 'wyzi-business-finder' );?>.</p>
											</div>
											<?php esc_html_e( 'Map radius unit', 'wyzi-business-finder' );?></th>
											<td><fieldset>
												<select class="wyz-select" name="wyz_business_map_radius_unit" id="radius-unit">
													<option value="km" <?php echo( 'km' === get_option( 'wyz_business_map_radius_unit' ) ? 'selected' : '' ); ?>><?php esc_html_e( 'Km', 'wyzi-business-finder' );?></option>
													<option value="mile" <?php echo( 'mile' === get_option( 'wyz_business_map_radius_unit' ) ? 'selected' : '' ); ?>><?php esc_html_e( 'Miles', 'wyzi-business-finder' );?></option>
												</select>
												</fieldset>
											</td>
										</tr>
										<tr valign="top">
											<th scope="row">
											<div class="help-tip">
												<p class="description"><?php esc_html_e( 'The Email that the contact messages will be sent from', 'wyzi-business-finder' );?></p>
											</div>
											<?php esc_html_e( 'Email From', 'wyzi-business-finder' );?></th> 
											<td><fieldset><input class="regular-text" type="email" name="wyz_businesses_from_email" value="<?php
											echo esc_attr( get_option( 'wyz_businesses_from_email' ) ); ?>"/></fieldset></td>
										</tr>
										<tr valign="top">
											<th scope="row">
											<div class="help-tip">
												<p class="description"><?php esc_html_e( 'Send users daily emails containing their favourited businesses\' posts.', 'wyzi-business-finder' );?>.</p>
											</div>
											<?php esc_html_e( 'Favorite Business Post Email', 'wyzi-business-finder' );?></th>
											<td><fieldset>
												<select class="wyz-select" name="wyz_business_post_email" id="radius-unit">
													<option value="off" <?php echo( 'off' === get_option( 'wyz_business_post_email' ) ? 'selected' : '' ); ?>><?php esc_html_e( 'OFF', 'wyzi-business-finder' );?></option>
													<option value="on" <?php echo( 'on' === get_option( 'wyz_business_post_email' ) ? 'selected' : '' ); ?>><?php esc_html_e( 'ON', 'wyzi-business-finder' );?></option>
												</select>
												</fieldset>
											</td>
										</tr>
										<tr valign="top">
											<th scope="row">
											<div class="help-tip">
												<p class="description"><?php esc_html_e( 'A rewrite for the business cpt name', 'wyzi-business-finder' );?></p>
											</div>
											<?php esc_html_e( 'Business CPT Name', 'wyzi-business-finder' );?></th> 
											<td><fieldset><input type="text" class="regular-text" name="wyz_business_new_single_permalink" value="<?php
											echo esc_attr( get_option( 'wyz_business_old_single_permalink' ) ); ?>"/></fieldset></td>
										</tr>
										<tr valign="top">
											<th scope="row">
											<div class="help-tip">
												<p class="description"><?php esc_html_e( 'A rewrite for the business_tags permalink name', 'wyzi-business-finder' );?></p>
											</div>
											<?php esc_html_e( 'Business tag permalink name', 'wyzi-business-finder' );?></th> 
											<td><fieldset><input type="text" class="regular-text" name="wyz_business_tags_new_single_permalink" value="<?php
											echo esc_attr( get_option( 'wyz_business_tags_old_single_permalink' ) ); ?>"/></fieldset></td>
										</tr>
											<tr valign="top">
											<th scope="row">
											<div class="help-tip">
												<p class="description"><?php esc_html_e( 'A rewrite for the wyz_business_category slug name. Make sure not to input "category" which is a reserved category slug name to Blog Posts of WordPress by default', 'wyzi-business-finder' );?></p>
											</div>
											<?php esc_html_e( 'Business Category slug Name', 'wyzi-business-finder' );?></th> 
											<td><fieldset><input type="text" class="regular-text" name="wyz_business_category_new_permalink" value="<?php
											echo esc_attr( get_option( 'wyz_business_category_old_permalink' ) ); ?>"/>
											</fieldset></td>
										</tr>
										<tr valign="top">
											<th scope="row">
											<div class="help-tip">
												<p class="description"><?php esc_html_e( 'Plural display name of Businesses', 'wyzi-business-finder' );?></p>
											</div>
											<?php esc_html_e( 'Business Plural Name', 'wyzi-business-finder' );?></th> 
											<td><fieldset><input type="text" class="regular-text" name="wyz_business_plural_name" value="<?php
											echo esc_attr( get_option( 'wyz_business_plural_name' ) ); ?>"/>
											</fieldset></td>
										</tr>

										<tr valign="top">
										<th scope="row">
											<div class="help-tip">
												<p class="description"><?php esc_html_e( 'Map Skin in Archives & Single Business Pages', 'wyzi-business-finder' );?></p>
											</div>
										<?php esc_html_e( 'Map Skin', 'wyzi-business-finder' );?></th>
										<?php $map_skin = get_option( 'wyz_business_archives_map_skin' );
										$path = plugin_dir_url( __FILE__ ) . 'templates-and-shortcodes/images/';?>
										<td><fieldset>
										<?php
										for ( $i=0; $i <= 5; $i++ ) {
											echo '<div class="map-skin"><input type="radio" id="wyz_business_archives_map_skin_'.$i.'" name="wyz_business_archives_map_skin" value="' . $i . '"' . ( $map_skin == $i ? 'checked' : '' ) . ' /><label for="wyz_business_archives_map_skin_'.$i.'"><img src="'. $path . 'map-skin-' . ( $i ? $i : '' ) . '.jpg"/></label><br /></div>';
										}?>
										</fieldset></td>
										</tr>
										<tr valign="top">
											<th scope="row">
											<div class="help-tip">
												<p class="description"><?php esc_html_e( 'A rewrite for the wyz_business_post cpt name', 'wyzi-business-finder' );?></p>
											</div>
											<?php esc_html_e( 'Business Post CPT Name', 'wyzi-business-finder' );?></th> 
											<td><fieldset><input type="text" class="regular-text" name="wyz_business_post_new_single_permalink" value="<?php
											echo esc_attr( get_option( 'wyz_business_post_old_single_permalink' ) ); ?>"/>
											</fieldset></td>
										</tr>
										<tr valign="top">
											<th scope="row">
											<div class="help-tip">
												<p class="description"><?php esc_html_e( 'A rewrite for the wyz_location cpt name', 'wyzi-business-finder' );?></p>
											</div>
											<?php esc_html_e( 'Location CPT Name', 'wyzi-business-finder' );?></th> 
											<td><fieldset><input type="text" class="regular-text" name="wyz_location_new_single_permalink" value="<?php
											echo esc_attr( get_option( 'wyz_location_old_single_permalink' ) ); ?>"/>
											</fieldset></td>
										</tr>
										<tr valign="top">
											<th scope="row">
											<div class="help-tip">
												<p class="description"><?php esc_html_e( 'Plural display name of Locations', 'wyzi-business-finder' );?></p>
											</div>
											<?php esc_html_e( 'Location Plural Name', 'wyzi-business-finder' );?></th> 
											<td><fieldset><input type="text" class="regular-text" name="wyz_location_plural_name" value="<?php
											echo esc_attr( get_option( 'wyz_location_plural_name' ) ); ?>"/>
											</fieldset></td>
										</tr>
										<tr valign="top">
											<th scope="row">
											<div class="help-tip">
												<p class="description"><?php esc_html_e( 'An API Key for  your Maps', 'wyzi-business-finder' );?></p>
											</div>
											<?php esc_html_e( 'Google Map API Key', 'wyzi-business-finder' );?></th> 
											<td><fieldset><input type="text" class="regular-text" name="wyz_map_api_key" value="<?php
											echo esc_attr( get_option( 'wyz_map_api_key' ) ); ?>"/>
											</fieldset></td>
										</tr>
										<tr valign="top">
											<th scope="row">
											<div class="help-tip">
												<p class="description">The number of businesses to pull on each request on maps. This number depends on your server PHP memory. This is a very useful option for websites with thousands of businesses.</p>
											</div>
											<?php esc_html_e( 'Number of loaded businesses on map', 'wyzi-business-finder' );?></th> 
											<td><fieldset><input type="text" class="regular-text" name="wyz_map_max_ajax_load" value="<?php
											echo esc_attr( get_option( 'wyz_map_max_ajax_load', 400 ) ); ?>"/>
											</fieldset></td>
										</tr>
									</table>
									<?php
									submit_button(); ?>
								</form>
							</tr>
						<?php 
							
							break;

						case 'single_business_settings': ?>
							<tr>
								<form class="wyz_settings_form" method="post" action="options.php">
									<?php 
									submit_button();
									settings_fields( 'wyz_single_businesses_settings' );
									do_settings_sections( 'wyz_single_businesses_settings' ); ?>
									<table class="form-table">
									<tr valign="top">
										<th scope="row">
										<div class="help-tip">
											<p class="description"><?php esc_html_e( 'If ON, new Businesses will be directly published by Business Owners to public, without Admin Approval', 'wyzi-business-finder' )?></p>
										</div>
										<?php esc_html_e( 'Immediate Approval', 'wyzi-business-finder' );?></th> 
										<td>
											<?php $opt = get_option( 'wyz_businesses_immediate_publish' );?>
											<fieldset>
											<select class="wyz-select" name="wyz_businesses_immediate_publish" id="immediate-publish">
											<option value="off" <?php
											echo( 'off' == $opt ? 'selected' : '' ); ?>><?php esc_html_e( 'OFF', 'wyzi-business-finder' );?></option>
											<option value="on" <?php
											echo( 'on' == $opt ? 'selected' : '' ); ?>><?php esc_html_e( 'ON', 'wyzi-business-finder' );?></option>
											</select>
											</fieldset>
										</td>
									</tr>
									<tr valign="top">
										<th scope="row">
										<div class="help-tip">
											<p class="description"><?php esc_html_e( 'If ON, business owners will be able to edit their businesses.', 'wyzi-business-finder' )?></p>
										</div>
										<?php esc_html_e( 'Business Edit', 'wyzi-business-finder' );?></th> 
										<td>
											<?php $opt = get_option( 'wyz_allow_business_edit' );?>
											<fieldset>
											<select class="wyz-select" name="wyz_allow_business_edit" id="wyz_allow_business_edit">
											<option value="on" <?php
											echo( 'on' == $opt ? 'selected' : '' ); ?>><?php esc_html_e( 'ON', 'wyzi-business-finder' );?></option>
											<option value="off" <?php
											echo( 'off' == $opt ? 'selected' : '' ); ?>><?php esc_html_e( 'OFF', 'wyzi-business-finder' );?></option>
											</select>
											</fieldset>
										</td>
									</tr>
									<tr valign="top">
										<th scope="row">
										<div class="help-tip">
											<p class="description"><?php esc_html_e( 'If ON, businesses will be drafted once their owner\'s subscription expires.' , 'wyzi-business-finder' )?></p>
										</div>
										<?php esc_html_e( 'Draft on Expiry', 'wyzi-business-finder' );?></th> 
										<td>
											<?php $opt = get_option( 'wyz_businesses_draft_on_expiry', 'off' );?>
											<fieldset>
											<select class="wyz-select" name="wyz_businesses_draft_on_expiry" id="wyz_businesses_draft_on_expiry">
											<option value="off" <?php
											echo( 'off' == $opt ? 'selected' : '' ); ?>><?php esc_html_e( 'OFF', 'wyzi-business-finder' );?></option>
											<option value="on" <?php
											echo( 'on' == $opt ? 'selected' : '' ); ?>><?php esc_html_e( 'ON', 'wyzi-business-finder' );?></option>
											</select>
											</fieldset>
										</td>
									</tr>
									<tr valign="top">
										<th scope="row">
										<div class="help-tip">
											<p class="description"><?php esc_html_e( 'Hide header (map/image) in Single Business Listing Page and Single offer Listing Page.', 'wyzi-business-finder' );?>.</p>
										</div>
										<?php esc_html_e( 'Hide Single Business Header', 'wyzi-business-finder' );?></th>
											<td><fieldset>
												<select class="wyz-select" name="wyz_business_map_hide_in_single_bus" id="scroll-zoom">
												<option value="off" <?php echo( 'off' === get_option( 'wyz_business_map_hide_in_single_bus','off' ) ? 'selected' : '' ); ?>><?php esc_html_e( 'OFF', 'wyzi-business-finder' );?></option>
												<option value="on" <?php echo( 'on' === get_option( 'wyz_business_map_hide_in_single_bus' ) ? 'selected' : '' ); ?>><?php esc_html_e( 'ON', 'wyzi-business-finder' );?></option>
											</select>
											</fieldset>
											</td>
									</tr>
									<tr valign="top">
										<th scope="row">
										<div class="help-tip">
											<p class="description"><?php esc_html_e( 'Business header (map/image) height in pixels', 'wyzi-business-finder' );?></p>
										</div>
										<?php esc_html_e( 'Single Business Header height', 'wyzi-business-finder' );?></th> 
										<td><fieldset><input type="number" name="wyz_businesses_map_height" min=0 value="<?php
										echo esc_attr( get_option( 'wyz_businesses_map_height', 600 ) ); ?>"/>
										</fieldset></td>
									</tr>
									<tr valign="top">
										<?php $h_cont = get_option( 'wyz_business_header_content', 'map' );?>
										<th scope="row">
										<div class="help-tip">
											<p class="description"><?php esc_html_e( 'Choose what to display in the single business header area. (If an image is chosen, the map will be displayed in the business sidebar)', 'wyzi-business-finder' );?>.</p>
										</div>
										<?php esc_html_e( 'Single Business Header Content', 'wyzi-business-finder' );?></th>
										<td><fieldset>
											<select class="wyz-select" name="wyz_business_header_content" id="scroll-zoom">
												<option value="map" <?php echo( 'map' === $h_cont ? 'selected' : '' ); ?>><?php esc_html_e( 'Map', 'wyzi-business-finder' );?></option>
												<option value="image" <?php echo( 'image' === $h_cont ? 'selected' : '' ); ?>><?php esc_html_e( 'Image', 'wyzi-business-finder' );?></option>
											</select>
											</fieldset>
										</td>
									</tr>
									<tr valign="top">
										<th scope="row">
										<div class="help-tip">
											<p class="description"><?php esc_html_e( 'In case you chose to show Image in Single Business Header Content Option above, then a Business Logo copy will appear on the banner. You can choose to hide it in this option', 'wyzi-business-finder' )?></p>
										</div>
										<?php esc_html_e( 'Hide Business Logo', 'wyzi-business-finder' );?></th> 
										<td>
											<?php $opt = get_option( 'wyz_hide_header_busienss_logo_case_of_image_header', 'off' );?>
											<fieldset>
											<select class="wyz-select" name="wyz_hide_header_busienss_logo_case_of_image_header" id="switch_sidebars_single_bus">
											<option value="off" <?php
											echo( 'off' == $opt ? 'selected' : '' ); ?>><?php esc_html_e( 'OFF', 'wyzi-business-finder' );?></option>
											<option value="on" <?php
											echo( 'on' == $opt ? 'selected' : '' ); ?>><?php esc_html_e( 'ON', 'wyzi-business-finder' );?></option>
											</select>
											</fieldset>
										</td>
									</tr>
									<tr valign="top">
										<th scope="row">
										<div class="help-tip">
											<p class="description"><?php esc_html_e( 'Hides Social Shares (Facebook, Twitter, Google+, Linkedin) Button found directly under the Header in the Single Business Page. This option only works if you are using template 1, where these buttons appear.', 'wyzi-business-finder' )?></p>
										</div>
										<?php esc_html_e( 'Hide Business Shares', 'wyzi-business-finder' );?></th> 
										<td>
											<?php $opt = get_option( 'wyz_hide_header_social_share', 'off' );?>
											<fieldset>
											<select class="wyz-select" name="wyz_hide_header_social_share" id="switch_sidebars_single_bus">
											<option value="off" <?php
											echo( 'off' == $opt ? 'selected' : '' ); ?>><?php esc_html_e( 'OFF', 'wyzi-business-finder' );?></option>
											<option value="on" <?php
											echo( 'on' == $opt ? 'selected' : '' ); ?>><?php esc_html_e( 'ON', 'wyzi-business-finder' );?></option>
											</select>
											</fieldset>
										</td>
									</tr>
									<tr valign="top">
										<th scope="row">
										<div class="help-tip">
											<p class="description"><?php esc_html_e( 'Switch Sidebars, the static and dynamic sidebar locations in single listing business page', 'wyzi-business-finder' )?></p>
										</div>
										<?php esc_html_e( 'Switch Sidebars', 'wyzi-business-finder' );?></th> 
										<td>
											<?php $opt = get_option( 'wyz_switch_sidebars_single_bus', 'off' );?>
											<fieldset>
											<select class="wyz-select" name="wyz_switch_sidebars_single_bus" id="switch_sidebars_single_bus">
											<option value="off" <?php
											echo( 'off' == $opt ? 'selected' : '' ); ?>><?php esc_html_e( 'OFF', 'wyzi-business-finder' );?></option>
											<option value="on" <?php
											echo( 'on' == $opt ? 'selected' : '' ); ?>><?php esc_html_e( 'ON', 'wyzi-business-finder' );?></option>
											</select>
											</fieldset>
										</td>
									</tr>
									<tr valign="top">
										<th scope="row">
										<div class="help-tip">
											<p class="description"><?php esc_html_e( 'Hides the Custom Sidebar "The Single Business" sidebar, that can be controlled from Appearance > Widgets.' )?></p>
										</div>
										<?php esc_html_e( 'Hide Extra Sidebar', 'wyzi-business-finder' );?></th> 
										<td>
											<?php $opt = get_option( 'wyz_hide_extra_sidebar_single_bus', 'off' );?>
											<fieldset>
											<select class="wyz-select" name="wyz_hide_extra_sidebar_single_bus" id="switch_sidebars_single_bus">
											<option value="off" <?php
											echo( 'off' == $opt ? 'selected' : '' ); ?>><?php esc_html_e( 'OFF', 'wyzi-business-finder' );?></option>
											<option value="on" <?php
											echo( 'on' == $opt ? 'selected' : '' ); ?>><?php esc_html_e( 'ON', 'wyzi-business-finder' );?></option>
											</select>
											</fieldset>
										</td>
									</tr>
									<tr valign="top">
										<th scope="row">
										<div class="help-tip">
											<p class="description"><?php esc_html_e( 'New Businesses Default Location in case not specified when a Business is created from the back end', 'wyzi-business-finder' );?></p>
										</div>
										<?php esc_html_e( 'Default Location', 'wyzi-business-finder' );?></th> 
											<td><fieldset>
												<input type="number" name="wyz_businesses_default_lat" value="<?php echo esc_attr( get_option( 'wyz_businesses_default_lat' ) ); ?>" step="any"/> <?php esc_html_e( 'Latitude', 'wyzi-business-finder' );?><br/>
												<input type="number" name="wyz_businesses_default_lon" value="<?php echo esc_attr( get_option( 'wyz_businesses_default_lon' ) ); ?>" step="any"/> <?php esc_html_e( 'Longitude', 'wyzi-business-finder' );?><br/>
											</fieldset></td>
									</tr>
									<tr valign="top">
										<th scope="row">
										<div class="help-tip">
											<p class="description"><?php esc_html_e( 'This will show or hide Footer in Single Business page', 'wyzi-business-finder' )?></p>
										</div>
										<?php esc_html_e( 'Footer in Single Business page', 'wyzi-business-finder' );?></th> 
										<td>
											<?php $opt = get_option( 'wyz_enable_business_footer', 'off' );?>
											<fieldset>
											<select class="wyz-select" name="wyz_enable_business_footer" id="immediate-publish">
											<option value="off" <?php
											echo( 'off' == $opt ? 'selected' : '' ); ?>><?php esc_html_e( 'OFF', 'wyzi-business-finder' );?></option>
											<option value="on" <?php
											echo( 'on' == $opt ? 'selected' : '' ); ?>><?php esc_html_e( 'ON', 'wyzi-business-finder' );?></option>
											</select>
											</fieldset>
										</td>
									</tr>
									<tr valign="top">
											<th scope="row">
											<div class="help-tip">
												<p class="description"><?php esc_html_e( 'If ON, business owners will be able to edit posts in their wall page.', 'wyzi-business-finder' )?></p>
											</div>
											<?php esc_html_e( 'Wall Post Edit', 'wyzi-business-finder' );?></th> 
											<td>
												<?php $opt = get_option( 'wyz_allow_business_post_edit' );?>
												<fieldset>
												<select class="wyz-select" name="wyz_allow_business_post_edit" id="immediate-publish">
												<option value="off" <?php
												echo( 'off' == $opt ? 'selected' : '' ); ?>><?php esc_html_e( 'OFF', 'wyzi-business-finder' );?></option>
												<option value="on" <?php
												echo( 'on' == $opt ? 'selected' : '' ); ?>><?php esc_html_e( 'ON', 'wyzi-business-finder' );?></option>
												</select>
												</fieldset>
											</td>
									</tr>
									<tr valign="top">
											<th scope="row">
											<div class="help-tip">
												<p class="description"><?php esc_html_e( 'You may wish to hide Business Name from Business Post Header and you will be left with Business Logo, which makes Business Post Header Smaller and less repetitive.', 'wyzi-business-finder' )?></p>
											</div>
											<?php esc_html_e( 'Hide Wall Post Business Name', 'wyzi-business-finder' );?></th> 
											<td>
												<?php $opt = get_option( 'wyz_hide_business_name_in_business_posts', 'off' );?>
												<fieldset>
												<select class="wyz-select" name="wyz_hide_business_name_in_business_posts" id="immediate-publish">
												<option value="off" <?php
												echo( 'off' == $opt ? 'selected' : '' ); ?>><?php esc_html_e( 'OFF', 'wyzi-business-finder' );?></option>
												<option value="on" <?php
												echo( 'on' == $opt ? 'selected' : '' ); ?>><?php esc_html_e( 'ON', 'wyzi-business-finder' );?></option>
												</select>
												</fieldset>
											</td>
									</tr>
									<tr valign="top">
										<th scope="row">
										<div class="help-tip">
											<p class="description"><?php esc_html_e( 'Which social media, if any, to allow for business post sharing, if you choose none, then the share button will not appear in the first place in Business Posts', 'wyzi-business-finder' );?></p>
										</div>
										<?php esc_html_e( 'Business Post Social Share', 'wyzi-business-finder' );?></th>
										<?php $socials = get_option( 'wyz_business_post_social_share' );
										$path = plugin_dir_url( __FILE__ ) . 'templates-and-shortcodes/images/';?>
										<td><fieldset>
										<div class="social-share-post">
										<input type="checkbox" name="wyz_business_post_social_share[facebook]" value="facebook" <?php echo ( isset( $socials['facebook'] ) ? 'checked' : '' );?> /> <img src="<?php echo $path . 'facebook.png';?>"/><br />
										</div>
										<div class="social-share-post">
										<input type="checkbox" name="wyz_business_post_social_share[google]" value="google" <?php echo ( isset( $socials['google'] ) ? 'checked' : '' );?> /> <img src="<?php echo $path . 'googleplus.png';?>"/><br />
										</div>
										<div class="social-share-post">
										<input type="checkbox" name="wyz_business_post_social_share[linkedin]" value="linkedin" <?php echo ( isset( $socials['linkedin'] ) ? 'checked' : '' );?> /> <img src="<?php echo $path . 'linkedin.png';?>"/><br />
										</div>
										<div class="social-share-post">
										<input type="checkbox" name="wyz_business_post_social_share[twitter]" value="twitter" <?php echo ( isset( $socials['twitter'] ) ? 'checked' : '' );?> /> <img src="<?php echo $path . 'twitter.png';?>"/><br />
										</div>
										<div class="social-share-post">
										<input type="checkbox" name="wyz_business_post_social_share[pinterest]" value="pinterest" <?php echo ( isset( $socials['pinterest'] ) ? 'checked' : '' );?> /> <img src="<?php echo $path . 'pinterest.png';?>"/><br />
										</div>
										</fieldset>
										</fieldset></td>
									</tr>
									<tr valign="top">
										<th scope="row">
										<div class="help-tip">
											<p class="description"><?php esc_html_e( 'open-close time format in business registration page', 'wyzi-business-finder' );?>.</p>
										</div>
										<?php esc_html_e( 'Open/Close Time format', 'wyzi-business-finder' );?></th>
										<td><fieldset>
											<select class="wyz-select" name="wyz_openclose_time_format_24" id="scroll-zoom">
												<option value="12" <?php echo( '12' === get_option( 'wyz_openclose_time_format_24' ) ? 'selected' : '' ); ?>>1-12</option>
												<option value="24" <?php echo( '24' === get_option( 'wyz_openclose_time_format_24' ) ? 'selected' : '' ); ?>>0-23</option>
											</select>
											</fieldset>
										</td>
									</tr>
									<tr valign="top">
										<th scope="row">
										<div class="help-tip">
											<p class="description"><?php esc_html_e( 'What will appear on Business Wall Page in case the Business has no Posts yet', 'wyzi-business-finder' );?></p>
										</div>
										<?php esc_html_e( 'No Posts Message', 'wyzi-business-finder' )?></th> 
										<td><fieldset>
											<input type="text" class="regular-text" name="wyz_businesses_no_posts" value="<?php echo esc_attr( get_option( 'wyz_businesses_no_posts' ) ); ?>"/>
										</fieldset></td>
									</tr>
									<tr valign="top">
										<th scope="row">
										<div class="help-tip">
											<p class="description"><?php esc_html_e( 'What will appear on Business Images Page in case the Business has no Images yet', 'wyzi-business-finder' );?></p>
										</div>
										<?php esc_html_e( 'No Images Message', 'wyzi-business-finder' );?></th> 
										<td><fieldset>
											<input type="text" class="regular-text" name="wyz_businesses_no_images" value="<?php echo esc_attr( get_option( 'wyz_businesses_no_images' ) ); ?>"/>
										</fieldset></td>
									</tr>
									<tr valign="top">
										<th scope="row">
										<div class="help-tip">
											<p class="description"><?php esc_html_e( 'What a Business owner sees when he has no Business, not shown to public', 'wyzi-business-finder' );?></p>
										</div>
										<?php esc_html_e( 'No Business Message', 'wyzi-business-finder' );?></th> 
										<td><fieldset>
											<input type="text" class="regular-text" name="wyz_businesses_no_business" value="<?php echo esc_attr( get_option( 'wyz_businesses_no_business' ) ); ?>"/>
										</fieldset></td>
									</tr>
									<tr valign="top">
										<th scope="row">
										<div class="help-tip">
											<p class="description"><?php esc_html_e( 'A message to display when a businesshas no ratings.', 'wyzi-business-finder' );?></p>
										</div>
										<?php esc_html_e( 'No Ratings Message', 'wyzi-business-finder' );?></th> 
										<td><fieldset>
											<input type="text" class="regular-text" name="wyz_businesses_no_ratings" value="<?php echo esc_attr( get_option( 'wyz_businesses_no_ratings' ) ); ?>"/>
										</fieldset></td>
									</tr>
									<tr valign="top">
										<th scope="row">
										<div class="help-tip">
											<p class="description"><?php esc_html_e( 'The Rate Text Business Owner sees', 'wyzi-business-finder' );?>.</p>
										</div>
										<?php esc_html_e( 'Owner Rating Message', 'wyzi-business-finder' );?></th> 
										<td><fieldset><input type="text" class="regular-text" name="wyz_businesses_rate_owner" value="<?php
										echo esc_attr( get_option( 'wyz_businesses_rate_owner' ) ); ?>"/>
										</fieldset></td>
									</tr>
									<tr valign="top">
										<th scope="row">
										<div class="help-tip">
											<p class="description"><?php esc_html_e( 'The Rate Text Logged in Clients or other Business Owners sees that have not rated yet', 'wyzi-business-finder' );?>.</p>
										</div>
										<?php esc_html_e( 'Client Rating Message', 'wyzi-business-finder' );?></th> 
										<td><fieldset><input type="text" class="regular-text" name="wyz_businesses_rate_sub" value="<?php
										echo esc_attr( get_option( 'wyz_businesses_rate_sub' ) ); ?>"/>
										</fieldset></td>
									</tr>
									<tr valign="top">
										<th scope="row">
										<div class="help-tip">
											<p class="description"><?php esc_html_e( 'The Rate Text Logged in Clients or other Business Owners sees that have already rated', 'wyzi-business-finder' );?>.</p>
										</div>
										<?php esc_html_e( 'Already Rated Text', 'wyzi-business-finder' );?></th> 
										<td><fieldset><input type="text" class="regular-text" name="wyz_businesses_rate_rated_sub" value="<?php
										echo esc_attr( get_option( 'wyz_businesses_rate_rated_sub' ) ); ?>"/>
										</fieldset></td>
									</tr>
									<tr valign="top">
										<th scope="row">
										<div class="help-tip">
											<p class="description"><?php esc_html_e( 'The Rate Text that non logged inusers see', 'wyzi-business-finder' );?>.</p>
										</div>
										<?php esc_html_e( 'Not Logged in Rating Message', 'wyzi-business-finder' );?></th> 
										<td><fieldset><input type="text" class="regular-text" name="wyz_businesses_rate_not_sub" value="<?php
										echo esc_attr( get_option( 'wyz_businesses_rate_not_sub' ) ); ?>"/></fieldset></td>
									</tr>

									</table>
									<?php
									submit_button(); ?>
								</form>
							</tr>

						<?php
						
						break;

						case 'single_business_tab_settings':

							wp_enqueue_script('qtip_js', plugin_dir_url( __FILE__ ) . 'claim/js/qtip.js', array('jquery'));
							wp_enqueue_media();
							wp_enqueue_script('upload_js', plugin_dir_url( __FILE__ ) . 'claim/js/media-upload.js', array('jquery'), true);
							wp_enqueue_script( 'wp-color-picker' );
							wp_enqueue_script( 'colorpicker_init', plugin_dir_url( __FILE__ ) . 'claim/js/colorpicker.js', array( 'jquery', 'wp-color-picker' ), true );
							wp_enqueue_script('jquery-ui-datepicker');
							wp_enqueue_style('jquery-style', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');
							wp_enqueue_script('wyzi_angular', plugin_dir_url( __FILE__ ) . 'claim/js/angular.min.js', array());
							wp_enqueue_script('wyzi_angular-ui', plugin_dir_url( __FILE__ ) . 'claim/js/sortable.js',array('wyzi_angular') );

							wp_enqueue_script( 'wyzi_business_tabs_order_js', plugin_dir_url( __FILE__ ) . 'businesses-and-offers/businesses/forms/tabs/js/business-tabs.js', array('wyzi_angular'));
							$wyzi_business_tabs_order_data = get_option( 'wyz_business_tabs_order_data' );
							wp_localize_script( 'wyzi_business_tabs_order_js', 'wyziTabsData', array('partials' => plugin_dir_url( __FILE__ ) . 'businesses-and-offers/businesses/forms/tabs/partials/','ajax_url' => admin_url( 'admin-ajax.php' ),'form_data' => $wyzi_business_tabs_order_data ) );
							
							require_once( plugin_dir_path( __FILE__ ) . 'businesses-and-offers/businesses/forms/class-business-tabs-settings-backend.php' );
							new WyzSettingsBusinessTabsForm_Builder();

						break;

						case 'single_business_sidebar_settings':

							wp_enqueue_script('qtip_js', plugin_dir_url( __FILE__ ) . 'claim/js/qtip.js', array('jquery'));
							wp_enqueue_media();
							wp_enqueue_script('upload_js', plugin_dir_url( __FILE__ ) . 'claim/js/media-upload.js', array('jquery'), true);
							wp_enqueue_script( 'wp-color-picker' );
							wp_enqueue_script( 'colorpicker_init', plugin_dir_url( __FILE__ ) . 'claim/js/colorpicker.js', array( 'jquery', 'wp-color-picker' ), true );
							wp_enqueue_script('jquery-ui-datepicker');
							wp_enqueue_style('jquery-style', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');
							wp_enqueue_script('wyzi_angular', plugin_dir_url( __FILE__ ) . 'claim/js/angular.min.js', array());
							wp_enqueue_script('wyzi_angular-ui', plugin_dir_url( __FILE__ ) . 'claim/js/sortable.js',array('wyzi_angular') );

							wp_enqueue_script( 'wyzi_business_sidebar_order_js', plugin_dir_url( __FILE__ ) . 'businesses-and-offers/businesses/forms/business-sidebar/js/business-sidebar.js', array('wyzi_angular'));
							$wyzi_business_sidebar_order_data = get_option( 'wyz_business_sidebar_order_data' );
							wp_localize_script( 'wyzi_business_sidebar_order_js', 'wyziSideBarData', array('partials' => plugin_dir_url( __FILE__ ) . 'businesses-and-offers/businesses/forms/business-sidebar/partials/','ajax_url' => admin_url( 'admin-ajax.php' ),'form_data' => $wyzi_business_sidebar_order_data ) );
							
							require_once( plugin_dir_path( __FILE__ ) . 'businesses-and-offers/businesses/forms/class-business-sidebar-settings-backend.php' );
							new WyzSettingsBusinessTabsForm_Builder();

						break;

						case 'business_form_fields_builder':	

							settings_fields( 'business_form_fields_builder' );
							do_settings_sections( 'business_form_fields_builder' );?>
							<?php
							wp_enqueue_script('qtip_js', plugin_dir_url( __FILE__ ) . 'claim/js/qtip.js', array('jquery'));
							  wp_enqueue_media();
							wp_enqueue_script('upload_js', plugin_dir_url( __FILE__ ) . 'claim/js/media-upload.js', array('jquery'), true);
							wp_enqueue_script( 'wp-color-picker' );
							wp_enqueue_script( 'colorpicker_init', plugin_dir_url( __FILE__ ) . 'claim/js/colorpicker.js', array( 'jquery', 'wp-color-picker' ), true );
							wp_enqueue_script('jquery-ui-datepicker');
							wp_enqueue_style('jquery-style', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');
							wp_enqueue_script('wyzi_angular', plugin_dir_url( __FILE__ ) . 'claim/js/angular.min.js', array());
							wp_enqueue_script('wyzi_angular-ui',plugin_dir_url( __FILE__ ) . 'claim/js/sortable.js',array('wyzi_angular') );

							wp_enqueue_script( 'wyz_business_fields_builder_js', plugin_dir_url( __FILE__ ) . 'businesses-and-offers/businesses/forms/registration-form-builder/js/builder.js', array('wyzi_angular'));

							$wyz_business_form_builder_data = get_option( 'wyz_business_form_builder_data', array() );

							if ( ! is_array( $wyz_business_form_builder_data ) )
								$wyz_business_form_builder_data = array(0=>'',1=>'',2=>'');

							$wyz_business_fields_builder_js_params = apply_filters( 'wyz_business_fields_builder_js_params', array('partials' => plugin_dir_url( __FILE__ ) . 'businesses-and-offers/businesses/forms/registration-form-builder/partials/','ajax_url' => admin_url( 'admin-ajax.php' ),'form_data' => $wyz_business_form_builder_data, 'translations' => array( 'openMsg' => esc_html__( 'Open all Day', 'wyzi-business-finder' ), 'closedMsg' => esc_html__( 'Closed all Day', 'wyzi-business-finder' ) ) ) );

							wp_localize_script( 'wyz_business_fields_builder_js', 'wyziBuilderData', $wyz_business_fields_builder_js_params );

							
							require_once( apply_filters( 'wyz_business_form_builder_settings_backend_file_path', plugin_dir_path( __FILE__ ) . 'businesses-and-offers/businesses/forms/class-business-form-builder-settings-backend.php' ) );
							new WyzSettingsBusinessForm_Builder();
						break;

						case 'business_custom_form_fields':	

							settings_fields( 'business_custom_form_fields' );
							do_settings_sections( 'business_custom_form_fields' );?>
							<?php
							wp_enqueue_script('qtip_js', plugin_dir_url( __FILE__ ) . 'claim/js/qtip.js', array('jquery'));
							  wp_enqueue_media();
							wp_enqueue_script('upload_js', plugin_dir_url( __FILE__ ) . 'claim/js/media-upload.js', array('jquery'), true);
							wp_enqueue_script( 'wp-color-picker' );
							wp_enqueue_script( 'colorpicker_init', plugin_dir_url( __FILE__ ) . 'claim/js/colorpicker.js', array( 'jquery', 'wp-color-picker' ), true );
							wp_enqueue_script('jquery-ui-datepicker');
							wp_enqueue_style('jquery-style', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');
							wp_enqueue_script('wyzi_angular', plugin_dir_url( __FILE__ ) . 'claim/js/angular.min.js', array());
							wp_enqueue_script('wyzi_angular-ui',plugin_dir_url( __FILE__ ) . 'claim/js/sortable.js',array('wyzi_angular') );

							wp_enqueue_script( 'wyz_business_custom_fields_js', plugin_dir_url( __FILE__ ) . 'businesses-and-offers/businesses/forms/extra-fields/js/business-custom-fields.js', array('wyzi_angular'));

							$wyz_business_custom_form_data = get_option( 'wyz_business_custom_form_data' );

							$wyz_business_custom_fields_builder_js_params = apply_filters( 'wyz_business_custom_fields_builder_js_params', array('partials' => plugin_dir_url( __FILE__ ) . 'businesses-and-offers/businesses/forms/extra-fields/partials/','ajax_url' => admin_url( 'admin-ajax.php' ),'form_data' => $wyz_business_custom_form_data ) );

							wp_localize_script( 'wyz_business_custom_fields_js', 'wyzi_registration_parameters', $wyz_business_custom_fields_builder_js_params );

							
							require_once( plugin_dir_path( __FILE__ ) . 'businesses-and-offers/businesses/forms/class-custom-business-fields-settings-backend.php' );
							new WyzSettingsBusinessCustomFieldsForm_Builder();
						break;

						case 'user_settings':?>

								<form class="wyz_settings_form" method="post" action="options.php">
									<?php
									submit_button();
									settings_fields( 'user_settings' );
									do_settings_sections( 'user_settings' );
									?>
									<table class="form-table">
										<tr valign="top">
											<th scope="row">
										<div class="help-tip">
											<p class="description"><?php esc_html_e( 'Choose between different styles of User Dashboard in the frontend.', 'wyzi-business-finder' );?>.</p>
										</div>
											<?php esc_html_e( 'User Dashboard Style', 'wyzi-business-finder' );?></th> 
											<td>
												<fieldset>
												<select class="wyz-select" name="wyz_user_dashboard_template" id="wyz_user_dashboard_template">
												<option value="design_1" <?php
												echo( 'design_1' == get_option( 'wyz_user_dashboard_template' ) ? 'selected' : '' ); ?>><?php esc_html_e( 'Style 1', 'wyzi-business-finder' );?></option>
												<option value="design_2" <?php
												echo( 'design_2' == get_option( 'wyz_user_dashboard_template' ) ? 'selected' : '' ); ?>><?php esc_html_e( 'Style 2', 'wyzi-business-finder' );?></option>
												</select>
												</fieldset>
											</td>
										</tr>
										<tr valign="top">
											<th scope="row">
											<div class="help-tip">
												<p class="description"><?php esc_html_e( 'This determines what will be the user\'s role after registering. Selecting "User Chooses" will display a dropdown on registration page for users to choose from.' , 'wyzi-business-finder' )?></p>
											</div>
											<?php esc_html_e( 'Default user role', 'wyzi-business-finder' );?></th> 
											<td>
												<fieldset>
												<select class="wyz-select" name="wyz_reg_def_user_role" id="wyz_reg_def_user_role">
												<option value=""><?php esc_html_e( 'User Chooses', 'wyzi-business-finder' );?></option>
												<option value="client" <?php
												echo( 'client' == get_option( 'wyz_reg_def_user_role' ) ? 'selected' : '' ); ?>><?php esc_html_e( 'Client', 'wyzi-business-finder' );?></option>
												<option value="business_owner" <?php
												echo( 'business_owner' == get_option( 'wyz_reg_def_user_role' ) ? 'selected' : '' ); ?>><?php esc_html_e( 'Business Owner', 'wyzi-business-finder' );?></option>
												</select>
												</fieldset>
											</td>
										</tr>
										<tr valign="top">
											<th scope="row">
											<div class="help-tip">
												<p class="description"><?php esc_html_e( 'Send verification emails for users upon registration.' , 'wyzi-business-finder' )?></p>
											</div>
											<?php esc_html_e( 'Verification Email', 'wyzi-business-finder' );?></th> 
											<td>
												<fieldset>
												<select class="wyz-select" name="wyz_user_email_verification" id="wyz_user_email_verification">
												<option value="off" <?php
												echo( 'off' == get_option( 'wyz_user_email_verification' ) ? 'selected' : '' ); ?>><?php esc_html_e( 'OFF', 'wyzi-business-finder' );?></option>
												<option value="on" <?php
												echo( 'on' == get_option( 'wyz_user_email_verification' ) ? 'selected' : '' ); ?>><?php esc_html_e( 'ON', 'wyzi-business-finder' );?></option>
												</select>
												</fieldset>
											</td>
										</tr>
										<tr valign="top">
											<th scope="row">
											<div class="help-tip">
												<p class="description"><?php esc_html_e( 'As it is a GDPR requirement to have users explicitly state their agreement for an online service\'s terms and conditions, this adds a checkbox next to the terms and conditions link.' , 'wyzi-business-finder' )?></p>
											</div>
											<?php esc_html_e( 'Terms and Conditions checkbox', 'wyzi-business-finder' );?></th> 
											<td>
												<fieldset>
												<select class="wyz-select" name="wyz_terms_and_cond_checkbox" id="wyz_terms_and_cond_checkbox">
												<option value="off" <?php
												echo( 'off' == get_option( 'wyz_terms_and_cond_checkbox' ) ? 'selected' : '' ); ?>><?php esc_html_e( 'OFF', 'wyzi-business-finder' );?></option>
												<option value="on" <?php
												echo( 'on' == get_option( 'wyz_terms_and_cond_checkbox' ) ? 'selected' : '' ); ?>><?php esc_html_e( 'ON', 'wyzi-business-finder' );?></option>
												</select>
												</fieldset>
											</td>
										</tr>
										<tr valign="top">
											<th scope="row">
											<div class="help-tip">
												<p class="description"><?php esc_html_e( 'As it is a GDPR requirement to have users able to delete/export their personal data, this adds 2 buttons to the user profile page allowing users to delete/export their personal data. This includes their businesses, offers, products, jobs, metadata and account.' , 'wyzi-business-finder' )?></p>
											</div>
											<?php esc_html_e( 'User data export/erase', 'wyzi-business-finder' );?></th> 
											<td>
												<fieldset>
												<select class="wyz-select" name="wyz_user_export_erase" id="wyz_user_export_erase">
												<option value="off" <?php
												echo( 'off' == get_option( 'wyz_user_export_erase' ) ? 'selected' : '' ); ?>><?php esc_html_e( 'OFF', 'wyzi-business-finder' );?></option>
												<option value="on" <?php
												echo( 'on' == get_option( 'wyz_user_export_erase' ) ? 'selected' : '' ); ?>><?php esc_html_e( 'ON', 'wyzi-business-finder' );?></option>
												</select>
												</fieldset>
											</td>
										</tr>
									</table>

									<?php submit_button(); ?>
								</form>
							<?php
							
						break;

						case 'user_regiteration_fields':
							wp_enqueue_script('qtip_js', plugin_dir_url( __FILE__ ) . 'claim/js/qtip.js', array('jquery'));
							  wp_enqueue_media();
							wp_enqueue_script('upload_js', plugin_dir_url( __FILE__ ) . 'claim/js/media-upload.js', array('jquery'), true);
							wp_enqueue_script( 'wp-color-picker' );
							wp_enqueue_script( 'colorpicker_init', plugin_dir_url( __FILE__ ) . 'claim/js/colorpicker.js', array( 'jquery', 'wp-color-picker' ), true );
							wp_enqueue_script('jquery-ui-datepicker');
							wp_enqueue_style('jquery-style', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');
							wp_enqueue_script('wyzi_angular', plugin_dir_url( __FILE__ ) . 'claim/js/angular.min.js', array());
							wp_enqueue_script('wyzi_angular-ui',plugin_dir_url( __FILE__ ) . 'claim/js/sortable.js',array('wyzi_angular') );

							wp_enqueue_script( 'wyz_registration_fields_js', plugin_dir_url( __FILE__ ) . 'front-end-registration/forms/registration-form-builder/js/builder.js', array('wyzi_angular'));

							$wyz_registration_form_data = get_option( 'wyz_registration_form_data' );

							wp_localize_script( 'wyz_registration_fields_js', 'wyziRegistrationFormData', array('partials' => plugin_dir_url( __FILE__ ) . 'front-end-registration/forms/registration-form-builder/partials/','ajax_url' => admin_url( 'admin-ajax.php' ),'form_data' => $wyz_registration_form_data ) );

							
							require_once( plugin_dir_path( __FILE__ ) . 'front-end-registration/forms/class-registration-form-builder-backend.php' );
							new WyzRegistrationForm_Builder();
						break;

						case 'offers': ?>
							<tr>
								<form class="wyz_settings_form" method="post" action="options.php">

									<?php 
										submit_button();
										settings_fields( 'wyz_offers_settings' ); ?>

									<?php do_settings_sections( 'wyz_offers_settings' ); ?>

									<table class="form-table">

										<tr valign="top">
											<th scope="row">
											<div class="help-tip">
												<p class="description"><?php echo esc_html__( 'Remove Offers management/submission ability from frontend.', 'wyzi-business-finder' );?>.</p>
											</div>
											<?php esc_html_e( 'Disable Offers', 'wyzi-business-finder' );?></th> 
											<td><fieldset>
												<select class="wyz-select" name="wyz_disable_offers" id="offer-immediate-publish">
													<option value="off" <?php echo( 'off' === get_option( 'wyz_disable_offers' ) ? 'selected' : '' ); ?>><?php esc_html_e( 'OFF', 'wyzi-business-finder' );?></option>
													<option value="on" <?php echo( 'on' === get_option( 'wyz_disable_offers' ) ? 'selected' : '' ); ?>><?php esc_html_e( 'ON', 'wyzi-business-finder' );?></option>
												</select>
												</fieldset>
											</td>
										</tr>

										<tr valign="top">
											<th scope="row">
											<div class="help-tip">
												<p class="description"><?php echo sprintf( esc_html__( 'points per %s', 'wyzi-business-finder' ), WYZ_OFFERS_CPT );?></p>
											</div>
											<?php esc_html_e( 'Points Price', 'wyzi-business-finder' );?></th> 
											<td><fieldset><input type="number" name="wyz_offer_point_price" value="<?php echo esc_attr( get_option( 'wyz_offer_point_price' ) ); ?>"/>
											</fieldset></td>
										</tr>
										<tr valign="top">
											<th scope="row">
											<div class="help-tip">
												<p class="description"><?php echo sprintf( esc_html__( 'After how many days of publication should the %s be deleted. Enter -1 to never delete', 'wyzi-business-finder' ), WYZ_OFFERS_CPT );?>.</p>
											</div>
											<?php esc_html_e( 'Expiry Date', 'wyzi-business-finder' );?></th> 
											<td><fieldset><input type="number" name="wyz_offer_expiry_date" value="<?php echo esc_attr( get_option( 'wyz_offer_expiry_date' ) ); ?>"/> <?php esc_html_e( 'Days', 'wyzi-business-finder' );?>
											</fieldset></td>
										</tr>
										<tr valign="top">
											<th scope="row">
											<div class="help-tip">
												<p class="description"><?php echo sprintf( esc_html__( 'If turned off, %s published by business owners will remain pending, until the admin chooses to publish them', 'wyzi-business-finder' ), WYZ_OFFERS_CPT );?>.</p>
											</div>
											<?php esc_html_e( 'Immediate Publish', 'wyzi-business-finder' );?></th> 
											<td><fieldset>
												<select class="wyz-select" name="wyz_offer_immediate_publish" id="offer-immediate-publish">
													<option value="off" <?php echo( 'off' === get_option( 'wyz_offer_immediate_publish' ) ? 'selected' : '' ); ?>><?php esc_html_e( 'OFF', 'wyzi-business-finder' );?></option>
													<option value="on" <?php echo( 'on' === get_option( 'wyz_offer_immediate_publish' ) ? 'selected' : '' ); ?>><?php esc_html_e( 'ON', 'wyzi-business-finder' );?></option>
												</select>
												</fieldset>
											</td>
										</tr>
										<th scope="row">
										<div class="help-tip">
											<p class="description"><?php echo sprintf( esc_html__( 'If turned on, business owners will have the ability to edit their published %s', 'wyzi-business-finder' ), WYZ_OFFERS_CPT );?>.</p>
										</div>
										<?php esc_html_e( 'Editing Capability', 'wyzi-business-finder' );?></th> 
											<td><fieldset>
												<select class="wyz-select" name="wyz_offer_editable" id="offer-editable">
													<option value="off" <?php echo( 'off' === get_option( 'wyz_offer_editable' ) ? 'selected' : '' ); ?>><?php esc_html_e( 'OFF', 'wyzi-business-finder' );?></option>
													<option value="on" <?php echo( 'on' === get_option( 'wyz_offer_editable' ) ? 'selected' : '' ); ?>><?php esc_html_e( 'ON', 'wyzi-business-finder' );?></option>
												</select>
											</fieldset></td>
										</tr>
										<tr valign="top">
											<th scope="row">
											<div class="help-tip">
												<p class="description"><?php esc_html_e( 'A rewrite for the wyz_offers cpt name', 'wyzi-business-finder' );?></p>
											</div>
											<?php esc_html_e( 'Offers CPT Name', 'wyzi-business-finder' );?></th> 
											<td><fieldset><input type="text" class="regular-text" name="wyz_offers_new_single_permalink" value="<?php echo esc_attr( get_option( 'wyz_offers_old_single_permalink' ) ); ?>"/>
											</fieldset></td>
										</tr>
										<tr valign="top">
											<th scope="row">
											<div class="help-tip">
												<p class="description"><?php echo sprintf( esc_html__( 'If turned on, %s schedualling by business owners will be allowed and Imediate Publish Option will have no effect on schedualed offers', 'wyzi-business-finder' ), WYZ_OFFERS_CPT );?>.</p>
											</div>
											<?php esc_html_e( 'Allow Schedule', 'wyzi-business-finder' );?></th> 
											<td><fieldset>
												<select class="wyz-select" name="wyz_offer_allow_schedule" id="wyz_offer_allow_schedule">
													<option value="off" <?php echo( 'off' === get_option( 'wyz_offer_allow_schedule', 'off' ) ? 'selected' : '' ); ?>><?php esc_html_e( 'OFF', 'wyzi-business-finder' );?></option>
													<option value="on" <?php echo( 'on' === get_option( 'wyz_offer_allow_schedule', 'off' ) ? 'selected' : '' ); ?>><?php esc_html_e( 'ON', 'wyzi-business-finder' );?></option>
												</select>
												</fieldset>
											</td>
										</tr>
										<tr valign="top">
										<th scope="row">
										<div class="help-tip">
											<p class="description"><?php esc_html_e( 'Plural display name of Offers', 'wyzi-business-finder' );?></p>
										</div>
										<?php esc_html_e( 'Offer Plural Name', 'wyzi-business-finder' );?></th> 
										<td><fieldset><input type="text" class="regular-text" name="wyz_offer_plural_name" value="<?php
										echo esc_attr( get_option( 'wyz_offer_plural_name' ) ); ?>"/>
										</fieldset></td>
										</tr>
									</table>

									<?php submit_button(); ?>
								</form>
							</tr>
						<?php break;

						case 'woocommerce': ?>
							<tr>
								<form class="wyz_settings_form" method="post" action="options.php">

									<?php 
											submit_button();
											settings_fields( 'wyz_woocommerce_settings' ); ?>

									<?php do_settings_sections( 'wyz_woocommerce_settings' ); ?>

									<table class="form-table">
										<tr valign="top">
											<th scope="row">
											<div class="help-tip">
												<p class="description"><?php esc_html_e( 'Products tab label in user account page.', 'wyzi-business-finder' );?>.</p>
											</div>
											<?php esc_html_e( 'Products tab label', 'wyzi-business-finder' );?></th> 
											<td><fieldset><input type="text" name="products_tab_label" value="<?php echo esc_attr( get_option( 'products_tab_label' ) ); ?>"/>
											</fieldset></td>
										</tr>
										<tr valign="top">
											<th scope="row">
												<div class="help-tip">
												<p class="description"><?php esc_html_e( 'Business Owner will be able to apply to become Vendors ( Sell Products ) ', 'wyzi-business-finder' );?>.</p>
											</div>
											<?php esc_html_e( 'Allow Vendor registration', 'wyzi-business-finder' );?></th>
											<td><fieldset>
												<select class="wyz-select" name="wyz_can_become_vendor" id="wyz_can_become_vendor">
													<option value="on" <?php echo( 'on' === get_option( 'wyz_can_become_vendor' ) ? 'selected' : '' ); ?>><?php esc_html_e( 'ON', 'wyzi-business-finder' );?></option>
													<option value="off" <?php echo( 'off' === get_option( 'wyz_can_become_vendor' ) ? 'selected' : '' ); ?>><?php esc_html_e( 'OFF', 'wyzi-business-finder' );?></option>
												</select>
											</fieldset>
											</td>
										</tr>
										<tr valign="top">
											<th scope="row">
											<div class="help-tip">
												<p class="description"><?php esc_html_e( 'Users automatically become vendors upon registering as business owners, bypassing vendor application process. (Doesn\'t have an effect if subscription is on)', 'wyzi-business-finder' );?>.</p>
											</div>
											<?php esc_html_e( 'Auto Vendorship', 'wyzi-business-finder' );?></th>
											<td><fieldset>
												<select class="wyz-select" name="wyz_auto_vendorship" id="wyz_auto_vendorship">
													<option value="on" <?php echo( 'on' === get_option( 'wyz_auto_vendorship', 'off' ) ? 'selected' : '' ); ?>><?php esc_html_e( 'ON', 'wyzi-business-finder' );?></option>
													<option value="off" <?php echo( 'off' === get_option( 'wyz_auto_vendorship', 'off' ) ? 'selected' : '' ); ?>><?php esc_html_e( 'OFF', 'wyzi-business-finder' );?></option>
												</select>
											</fieldset>
											</td>
										</tr>
										<tr valign="top">
											<th scope="row">
												<div class="help-tip">
												<p class="description"><?php esc_html_e( 'Allows Vendors to Submit Products from the frontend', 'wyzi-business-finder' );?>.</p>
											</div>
											<?php esc_html_e( 'Allow Front End Product Submission', 'wyzi-business-finder' );?></th>
											<td><fieldset>
												<select class="wyz-select" name="wyz_allow_front_end_submit" id="wyz_allow_front_end_submit">
													<option value="on" <?php echo( 'on' === get_option( 'wyz_allow_front_end_submit' ) ? 'selected' : '' ); ?>><?php esc_html_e( 'ON', 'wyzi-business-finder' );?></option>
													<option value="off" <?php echo( 'off' === get_option( 'wyz_allow_front_end_submit' ) ? 'selected' : '' ); ?>><?php esc_html_e( 'OFF', 'wyzi-business-finder' );?></option>
												</select>
											</fieldset>
											</td>
										</tr>
										<tr valign="top">
											<th scope="row">
											<div class="help-tip">
												<p class="description"><?php esc_html_e('Vendors can assign products to be bookable', 'wyzi-business-finder');?></p>
											</div>
											<?php esc_html_e( 'Bookable products', 'wyzi-business-finder' );?></th>
											<td><fieldset>
												<select class="wyz-select" name="wyz_vendors_can_booking_products" id="wyz_vendors_can_booking_products">
													<option value="on" <?php echo( 'on' === get_option( 'wyz_vendors_can_booking_products', 'off' ) ? 'selected' : '' ); ?>><?php esc_html_e( 'ON', 'wyzi-business-finder' );?></option>
													<option value="off" <?php echo( 'off' === get_option( 'wyz_vendors_can_booking_products' , 'off') ? 'selected' : '' ); ?>><?php esc_html_e( 'OFF', 'wyzi-business-finder' );?></option>
												</select>
											</fieldset>
											</td>
										</tr>
										<tr valign="top">
											<th scope="row">
											<div class="help-tip">
												<p class="description"><?php esc_html_e('In case "Booked appointment service" is checked in a product, it will continue to show up in the shop', 'wyzi-business-finder');?></p>
											</div>
											<?php esc_html_e( 'Bookable products display', 'wyzi-business-finder' );?></th>
											<td><fieldset>
												<select class="wyz-select" name="wyz_display_booking_products_in_listing" id="wyz_display_booking_products_in_listing">
													<option value="on" <?php echo( 'on' === get_option( 'wyz_display_booking_products_in_listing', 'off' ) ? 'selected' : '' ); ?>><?php esc_html_e( 'ON', 'wyzi-business-finder' );?></option>
													<option value="off" <?php echo( 'off' === get_option( 'wyz_display_booking_products_in_listing' , 'off') ? 'selected' : '' ); ?>><?php esc_html_e( 'OFF', 'wyzi-business-finder' );?></option>
												</select>
											</fieldset>
											</td>
										</tr>
										<tr valign="top">
											<th scope="row">
											<div class="help-tip">
												<p class="description"><?php esc_html_e( 'Hide orders tab in "user account" page.', 'wyzi-business-finder' );?>.</p>
											</div>
											<?php esc_html_e( 'Hide orders tab', 'wyzi-business-finder' );?></th>
											<td><fieldset>
												<select class="wyz-select" name="wyz_woocommerce_hide_orders_tab" id="wyz_woocommerce_hide_orders_tab">
													<option value="off" <?php echo( 'off' === get_option( 'wyz_woocommerce_hide_orders_tab' ) ? 'selected' : '' ); ?>><?php esc_html_e( 'OFF', 'wyzi-business-finder' );?></option>
													<option value="on" <?php echo( 'on' === get_option( 'wyz_woocommerce_hide_orders_tab' ) ? 'selected' : '' ); ?>><?php esc_html_e( 'ON', 'wyzi-business-finder' );?></option>
												</select>
												</fieldset>
											</td>
										</tr>
										<tr valign="top">
											<th scope="row">
											<div class="help-tip">	
												<p class="description"><?php esc_html_e( 'Wheather to display related products in single product page or not.', 'wyzi-business-finder' );?>.</p>
											</div>
											<?php esc_html_e( 'Hide related products', 'wyzi-business-finder' );?></th>
											<td><fieldset>
												<select class="wyz-select" name="wyz_woocommerce_hide_related" id="wyz_woocommerce_hide_related">
													<option value="off" <?php echo( 'off' === get_option( 'wyz_woocommerce_hide_related' ) ? 'selected' : '' ); ?>><?php esc_html_e( 'OFF', 'wyzi-business-finder' );?></option>
													<option value="on" <?php echo( 'on' === get_option( 'wyz_woocommerce_hide_related' ) ? 'selected' : '' ); ?>><?php esc_html_e( 'ON', 'wyzi-business-finder' );?></option>
												</select>
												</fieldset>
											</td>
										</tr>
										<tr valign="top">
											<th scope="row">
											<div class="help-tip">	
												<p class="description"><?php esc_html_e( 'Wheather to display reviews in single product page or not.', 'wyzi-business-finder' );?>.</p>
											</div>
											<?php esc_html_e( 'Hide reviews', 'wyzi-business-finder' );?></th>
											<td><fieldset>
												<select class="wyz-select" name="wyz_woocommerce_hide_reviews" id="wyz_woocommerce_hide_reviews">
													<option value="off" <?php echo( 'off' === get_option( 'wyz_woocommerce_hide_reviews' ) ? 'selected' : '' ); ?>><?php esc_html_e( 'OFF', 'wyzi-business-finder' );?></option>
													<option value="on" <?php echo( 'on' === get_option( 'wyz_woocommerce_hide_reviews' ) ? 'selected' : '' ); ?>><?php esc_html_e( 'ON', 'wyzi-business-finder' );?></option>
												</select>
												</fieldset>
											</td>
										</tr>
										<tr valign="top">
											<th scope="row">
											<div class="help-tip">	
												<p class="description"><?php esc_html_e( 'Wheather to display cart menu item or not.', 'wyzi-business-finder' );?>.</p>
											</div>
											<?php esc_html_e( 'Hide cart', 'wyzi-business-finder' );?></th>
											<td><fieldset>
												<select class="wyz-select" name="wyz_woocommerce_hide_menu_cart" id="wyz_woocommerce_hide_menu_cart">
													<option value="on" <?php echo( 'on' === get_option( 'wyz_woocommerce_hide_menu_cart' ) ? 'selected' : '' ); ?>><?php esc_html_e( 'ON', 'wyzi-business-finder' );?></option>
													<option value="off" <?php echo( 'off' === get_option( 'wyz_woocommerce_hide_menu_cart' ) ? 'selected' : '' ); ?>><?php esc_html_e( 'OFF', 'wyzi-business-finder' );?></option>
												</select>
												</fieldset>
											</td>
										</tr>
										<tr valign="top">
											<th scope="row">
											<div class="help-tip">	
												<p class="description"><?php esc_html_e( 'If enabled, menu cart will be hidden if it contains no items. Note: requires "Show cart" option to be enabled.', 'wyzi-business-finder' );?>.</p>
											</div>
											<?php esc_html_e( 'Hide empty cart', 'wyzi-business-finder' );?></th>
											<td><fieldset>
												<select class="wyz-select" name="wyz_woocommerce_hide_menu_cart_if_empty" id="wyz_woocommerce_hide_menu_cart_if_empty">
													<option value="off" <?php echo( 'off' === get_option( 'wyz_woocommerce_hide_menu_cart_if_empty' ) ? 'selected' : '' ); ?>><?php esc_html_e( 'OFF', 'wyzi-business-finder' );?></option>
													<option value="on" <?php echo( 'on' === get_option( 'wyz_woocommerce_hide_menu_cart_if_empty' ) ? 'selected' : '' ); ?>><?php esc_html_e( 'ON', 'wyzi-business-finder' );?></option>
												</select>
												</fieldset>
											</td>
										</tr>

										<tr valign="top">
											<th scope="row">
												<div class="help-tip">	
												<p class="description"><?php esc_html_e( 'Sold By Label on Products Links back to Business Page or Vendor Page, the initial WC Marketplace way', 'wyzi-business-finder' );?>.</p>
											</div>
											<?php esc_html_e( '"Sold by" directs to', 'wyzi-business-finder' );?></th>
											<td><fieldset>
												<select class="wyz-select" name="wyz_woocommerce_soldby_direct_to" id="wyz_woocommerce_soldby_direct_to">
													<option value="business" <?php echo( 'business' === get_option( 'wyz_woocommerce_soldby_direct_to' ) ? 'selected' : '' ); ?>><?php esc_html_e( 'Business Page', 'wyzi-business-finder' );?></option>
													<option value="vendor" <?php echo( 'vendor' === get_option( 'wyz_woocommerce_soldby_direct_to' ) ? 'selected' : '' ); ?>><?php esc_html_e( 'Vendor Page', 'wyzi-business-finder' );?></option>
												</select>
											</fieldset>
											</td>
										</tr>
									</table>

									<?php submit_button(); ?>
								</form>
							</tr>
						<?php break;
						case 'privileges': ?>
							<tr>
								<form class="wyz_settings_form" method="post" action="options.php">

									<?php 
										submit_button();
										settings_fields( 'wyz_privileges_settings' ); ?>

									<?php do_settings_sections( 'wyz_privileges_settings' ); ?>

									<table class="form-table">

										<tr valign="top">
										<th scope="row"><?php esc_html_e( 'Hide admin bar from business owners and clients', 'wyzi-business-finder' );?>.</th> 
										<td><fieldset>
											<select class="wyz-select" name="wyz_businesses_hide_admin_bar" id="hide-admin-bar">
											<option value="on" <?php
											echo( 'on' == get_option( 'wyz_businesses_hide_admin_bar', 'on' ) ? 'selected' : '' ); ?>><?php esc_html_e( 'ON', 'wyzi-business-finder' );?></option>
											<option value="off" <?php
											echo( 'off' == get_option( 'wyz_businesses_hide_admin_bar', 'on' ) ? 'selected' : '' ); ?>><?php esc_html_e( 'OFF', 'wyzi-business-finder' );?></option>
											</select>
										</fieldset></td>
										</tr>
										<tr valign="top">
										<th scope="row"><?php esc_html_e( 'Allow Clients to upgrade to Business Owners', 'wyzi-business-finder' );?>.</th> 
										<td><fieldset>
											<select class="wyz-select" name="wyz-user-can-upgrade-account" id="allow-upgrade-account">
											<option value="on" <?php
											echo( 'on' == get_option( 'wyz-user-can-upgrade-account', 'on' ) ? 'selected' : '' ); ?>><?php esc_html_e( 'ON', 'wyzi-business-finder' );?></option>
											<option value="off" <?php
											echo( 'off' == get_option( 'wyz-user-can-upgrade-account', 'on' ) ? 'selected' : '' ); ?>><?php esc_html_e( 'OFF', 'wyzi-business-finder' );?></option>
											</select>
										</fieldset></td>
										</tr>
										<tr valign="top">
										<th scope="row"><?php esc_html_e( 'Prevent business owners and clients from accessing the dashboard', 'wyzi-business-finder' );?>.</th> 
										<td><fieldset>
										<select class="wyz-select" name="wyz_businesses_restrict_backend_access" id="restrict-backend-access">
											<option value="on" <?php
											echo( 'on' == get_option( 'wyz_businesses_restrict_backend_access', 'on' ) ? 'selected' : '' ); ?>><?php esc_html_e( 'ON', 'wyzi-business-finder' );?></option>
											<option value="off" <?php
											echo( 'off' == get_option( 'wyz_businesses_restrict_backend_access', 'on' ) ? 'selected' : '' ); ?>><?php esc_html_e( 'OFF', 'wyzi-business-finder' );?></option>
										</select>
										</fieldset></td>
										</tr>
										<tr valign="top">
										<th scope="row"><?php esc_html_e( 'Prevent vendors from accessing the dashboard', 'wyzi-business-finder' );?>.</th> 
										<td><fieldset>
										<select class="wyz-select" name="wyz_vendor_restrict_backend_access" id="restrict-backend-access">
											<option value="on" <?php
											echo( 'on' == get_option( 'wyz_vendor_restrict_backend_access', 'off' ) ? 'selected' : '' ); ?>><?php esc_html_e( 'ON', 'wyzi-business-finder' );?></option>
											<option value="off" <?php
											echo( 'off' == get_option( 'wyz_vendor_restrict_backend_access', 'off' ) ? 'selected' : '' ); ?>><?php esc_html_e( 'OFF', 'wyzi-business-finder' );?></option>
										</select>
										</fieldset></td>
										</tr>
									</table>

									<?php submit_button(); ?>
								</form>
							</tr>
						<?php
						break;

						case 'claim_settings': ?>
						
							<tr>
								<form class="wyz_settings_form" method="post" action="options.php">
									<?php 
									submit_button();
									settings_fields( 'wyz_claim_settings' );
									do_settings_sections( 'wyz_claim_settings' ); ?>
									<table class="form-table">
										<tr valign="top">
											<th scope="row">
											<div class="help-tip">	
												<p class="description">
														<?php esc_html_e( 'Wheather or not to allow users to claim businesses' , 'wyzi-business-finder' );?>.
												</p>
											</div>
											<?php esc_html_e( 'Business Claiming', 'wyzi-business-finder' );?></th>
											<td>
												<fieldset>
													<select class="wyz-select" name="wyz_business_claiming" id="wyz_business_claiming">
														<option value="on" <?php echo( 'on' === get_option( 'wyz_business_claiming' ) ? 'selected' : '' ); ?>><?php esc_html_e( 'ON', 'wyzi-business-finder' );?></option>
													<option value="off" <?php echo( 'off' === get_option( 'wyz_business_claiming' ) ? 'selected' : '' ); ?>><?php esc_html_e( 'OFF', 'wyzi-business-finder' );?></option>
													</select>
												</fieldset>
											</td>
										</tr>
										<tr valign="top">
											<th scope="row">
											<div class="help-tip">
												<p class="description">
														<?php esc_html_e( 'If this option is on, only registered business owners with no Buisnesses can Claim a Business' , 'wyzi-business-finder' );?>.
												</p>
											</div>
											<?php esc_html_e( 'Only Business Owners can claim', 'wyzi-business-finder' );?></th>
											<td>
												<fieldset>
													<select class="wyz-select" name="wyz_claim_should_be_business_owner" id="wyz_claim_should_be_business_owner">
													<option value="off" <?php echo( 'off' === get_option( 'wyz_claim_should_be_business_owner' ) ? 'selected' : '' ); ?>><?php esc_html_e( 'OFF', 'wyzi-business-finder' );?></option>
														<option value="on" <?php echo( 'on' === get_option( 'wyz_claim_should_be_business_owner' ) ? 'selected' : '' ); ?>><?php esc_html_e( 'ON', 'wyzi-business-finder' );?></option>
														
													</select>
												</fieldset>
											</td>
										</tr>
										<tr valign="top">
											<th scope="row">
											<div class="help-tip">
												<p class="description"><?php esc_html_e( 'Cost in Points for new Claim Submission, set zero to keep it free, needs Only Business Owners can claim be ON', 'wyzi-business-finder' );?>
													</p>
											</div>
											<?php esc_html_e( 'Claim Cost', 'wyzi-business-finder' );?></th> 
											<td>
												<fieldset>
													<input type="number" name="wyz_claim_submission_price" min=0 value="<?php echo esc_attr( get_option( 'wyz_claim_submission_price', 0 ) == '' ? 0 : get_option( 'wyz_claim_submission_price', 0 ) ); ?>"/>
													
												</fieldset>
											</td>
										</tr>
									</table>
									<?php submit_button(); ?>
								</form>
							</tr>
							<?php						
						break;

						case 'claim_form':
							wp_enqueue_script('qtip_js', plugin_dir_url( __FILE__ ) . 'claim/js/qtip.js', array('jquery'));
						  	wp_enqueue_media();
							wp_enqueue_script('upload_js', plugin_dir_url( __FILE__ ) . 'claim/js/media-upload.js', array('jquery'), true);
							wp_enqueue_script( 'wp-color-picker' );
							wp_enqueue_script( 'colorpicker_init', plugin_dir_url( __FILE__ ) . 'claim/js/colorpicker.js', array( 'jquery', 'wp-color-picker' ), true );
							wp_enqueue_script('jquery-ui-datepicker');
							wp_enqueue_style('jquery-style', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');
			
							wp_enqueue_script('wyzi_angular', plugin_dir_url( __FILE__ ) . 'claim/js/angular.min.js', array());
							wp_enqueue_script('wyzi_angular-ui',plugin_dir_url( __FILE__ ) . 'claim/js/sortable.js',array('wyzi_angular') );
							wp_enqueue_script( 'wyzi_claim_registration', plugin_dir_url( __FILE__ ) . 'claim/js/claim_registration_app.js', array('wyzi_angular'));

							$wyz_claim_registration_form_data = get_option('wyz_claim_registration_form_data');
							wp_localize_script('wyzi_claim_registration', 'wyzi_registration_parameters', array('partials' => plugin_dir_url( __FILE__ ) . 'claim/partials/','ajax_url' => admin_url('admin-ajax.php'),'form_data' => $wyz_claim_registration_form_data));
							
							require_once( plugin_dir_path( __FILE__ ) . 'claim/class-claim-settings-backend.php' );
							new Wyzi_Settings_Claim_Registration_Form_Building();

						break;

						case 'social_login': ?>
							<tr>
								<form class="wyz_settings_form" method="post" action="options.php">
									<?php 
									submit_button();
									settings_fields( 'wyz_social_login_settings' );
									do_settings_sections( 'wyz_social_login_settings' ); ?>
									<table class="form-table">
										<tr valign="top">
											<th scope="row"><?php esc_html_e( 'Facebook App ID', 'wyzi-business-finder' );?></th> 
											<td><fieldset><input type="text" class="regular-text" name="wyz_fb_app_id" value="<?php
											echo esc_attr( get_option( 'wyz_fb_app_id' ) ); ?>"/></fieldset></td>
										</tr>
										<tr valign="top">
											<th scope="row"><?php esc_html_e( 'Facebook App Secret', 'wyzi-business-finder' );?></th> 
											<td><fieldset><input type="text" class="regular-text" name="wyz_fb_app_secret" value="<?php
											echo esc_attr( get_option( 'wyz_fb_app_secret' ) ); ?>"/></fieldset></td>
										</tr>

										<tr valign="top">
											<th scope="row"><?php esc_html_e( 'Google Client ID', 'wyzi-business-finder' );?></th> 
											<td><fieldset><input type="text" class="regular-text" name="wyz_google_client_id" value="<?php
											echo esc_attr( get_option( 'wyz_google_client_id' ) ); ?>"/></fieldset></td>
										</tr>
										<tr valign="top">
											<th scope="row"><?php esc_html_e( 'Google Client Secret', 'wyzi-business-finder' );?></th> 
											<td><fieldset><input type="text" class="regular-text" name="wyz_google_client_secret" value="<?php
											echo esc_attr( get_option( 'wyz_google_client_secret' ) ); ?>"/></fieldset></td>
										</tr>
										<tr valign="top">
											<th scope="row"><?php esc_html_e( 'Google Developer Key', 'wyzi-business-finder' );?></th> 
											<td><fieldset><input type="text" class="regular-text" name="wyz_google_developer_key" value="<?php
											echo esc_attr( get_option( 'wyz_google_developer_key' ) ); ?>"/></fieldset></td>
										</tr>
									</table>
									<?php submit_button(); ?>
								</form>
							</tr>
							<?php
						break;
						case 'private_msg': ?>
						<tr>
								<form class="wyz_settings_form" method="post" action="options.php">
									<?php 
									submit_button();
									settings_fields( 'wyz_private_msg_settings' );
									do_settings_sections( 'wyz_private_msg_settings' ); ?>
									<table class="form-table">
										<tr valign="top">
										<?php $opt = get_option( 'wyz_private_msg_status_on_off', 'off');?>
											<th scope="row">
											<div class="help-tip">
												<p class="description"><?php esc_html_e( 'Turns the Inbox Tab in User Dashbaord On or Off', 'wyzi-business-finder' )?></p>
											</div>
											<?php esc_html_e( 'Messaging Status', 'wyzi-business-finder' );?></th> 
											<td>
												<fieldset>
												<select class="wyz-select" name="wyz_private_msg_status_on_off" id="wyz_private_msg_status_on_off">
												<option value="off" <?php
												echo( 'off' == $opt ? 'selected' : '' ); ?>><?php esc_html_e( 'OFF', 'wyzi-business-finder' );?></option>
												<option value="on" <?php
												echo( 'on' == $opt ? 'selected' : '' ); ?>><?php esc_html_e( 'ON', 'wyzi-business-finder' );?></option>
												</select>
												</fieldset>
											</td>
										</tr>
										<tr valign="top">
										<?php $opt = get_option( 'wyz_private_msg_hide_client', 'off');?>
											<th scope="row">
											<div class="help-tip">
												<p class="description"><?php esc_html_e( 'Hide private messaging from client users', 'wyzi-business-finder' )?></p>
											</div>
											<?php esc_html_e( 'Hide for clients', 'wyzi-business-finder' );?></th> 
											<td>
												<fieldset>
												<select class="wyz-select" name="wyz_private_msg_hide_client" id="wyz_private_msg_hide_client">
												<option value="off" <?php
												echo( 'off' == $opt ? 'selected' : '' ); ?>><?php esc_html_e( 'OFF', 'wyzi-business-finder' );?></option>
												<option value="on" <?php
												echo( 'on' == $opt ? 'selected' : '' ); ?>><?php esc_html_e( 'ON', 'wyzi-business-finder' );?></option>
												</select>
												</fieldset>
											</td>
										</tr>
										<!-- <tr valign="top">
										<?php //$opt = get_option( 'wyz_fcm_on_off', 'off');?>
											<th scope="row"><?php //esc_html_e( 'Enable Live Chat', 'wyzi-business-finder' );?></th> 
											<td>
												<fieldset>
												<select class="wyz-select" name="wyz_fcm_on_off" id="wyz_fcm_on_off">
												<option value="off" <?php
												//echo( 'off' == $opt ? 'selected' : '' ); ?>><?php //esc_html_e( 'OFF', 'wyzi-business-finder' );?></option>
												<option value="on" <?php
												//echo( 'on' == $opt ? 'selected' : '' ); ?>><?php //esc_html_e( 'ON', 'wyzi-business-finder' );?></option>
												</select>
												</fieldset>
											</td>
										</tr>

										<tr valign="top">
											<th scope="row"><?php //esc_html_e( 'FCM API Key', 'wyzi-business-finder' );?></th> 
											<td><fieldset>
												<input type="text" class="regular-text" name="wyz_fcm_api_key" value="<?php //echo esc_attr( get_option( 'wyz_fcm_api_key' ) ); ?>"/>
											</fieldset></td>
										</tr>

										<tr valign="top">
											<th scope="row"><?php //esc_html_e( 'FCM Authentication Domain', 'wyzi-business-finder' );?></th> 
											<td><fieldset>
												<input type="text" class="regular-text" name="wyz_fcm_auth_domain" value="<?php //echo esc_attr( get_option( 'wyz_fcm_auth_domain' ) ); ?>"/>
											</fieldset></td>
										</tr>
										<tr valign="top">
											<th scope="row"><?php //esc_html_e( 'FCM Database URL', 'wyzi-business-finder' );?></th> 
											<td><fieldset>
												<input type="text" class="regular-text" name="wyz_fcm_db_url" value="<?php //echo esc_attr( get_option( 'wyz_fcm_db_url' ) ); ?>"/>
											</fieldset></td>
										</tr>
										<tr valign="top">
											<th scope="row"><?php //esc_html_e( 'FCM Project ID', 'wyzi-business-finder' );?></th> 
											<td><fieldset>
												<input type="text" class="regular-text" name="wyz_fcm_project_id" value="<?php //echo esc_attr( get_option( 'wyz_fcm_project_id' ) ); ?>"/>
											</fieldset></td>
										</tr>

										<tr valign="top">
											<th scope="row"><?php //esc_html_e( 'FCM Storage Bucket', 'wyzi-business-finder' );?></th> 
											<td><fieldset>
												<input type="text" class="regular-text" name="wyz_fcm_storage_bucket" value="<?php //echo esc_attr( get_option( 'wyz_fcm_storage_bucket' ) ); ?>"/>
											</fieldset></td>
										</tr>
										<tr valign="top">
											<th scope="row"><?php //esc_html_e( 'FCM Messaging Sender ID', 'wyzi-business-finder' );?></th> 
											<td><fieldset>
												<input type="text" class="regular-text" name="wyz_fcm_msg_sndr_id" value="<?php //echo esc_attr( get_option( 'wyz_fcm_msg_sndr_id' ) ); ?>"/>
											</fieldset></td>
										</tr> -->
										</table>
									
									<?php submit_button(); ?>
								</form>
							</tr>
						
						<?php
						default:
							do_action( 'wyz_toolkit_settings_tab_content', $tab );
					}
			}
		echo '</div></div>';
	}
}


function wyz_get_reg_form_builder_defaults() {
	$path = plugin_dir_url( __FILE__ ) . '/front-end-registration/forms/registration-form-builder/partials';
	return array (
		array (
			'id' => 0,
			'type' => 'username',
			'label' => 'Username',
			'required' => 1,
			'partial' => "$path/form-element-required.html",
			'cssClass' => '',
			'hidden' => 1,
		),
		array(
			'id' => 1,
			'type' => 'pemail',
			'label' => 'Email',
			'required' => 1,
			'partial' => "$path/form-element-required.html",
			'cssClass' => '',
			'hidden' => 1,
		),
		array(
			'id' => 2,
			'type' => 'password',
			'label' => 'Password',
			'passaglabel' => 'password again',
			'required' => 1,
			'partial' => "$path/form-element-password.html",
			'cssClass' => '',
			'hidden' => 1,
		)
	);
}

if ( ! function_exists( 'wyz_register_business_settings' ) ) {
	/**
	 * Register businesses settings.
	 */
	function wyz_register_business_settings() {
		register_setting( 'wyz_businesses_settings', 'wyz_sub_mode_on_off' );
		register_setting( 'wyz_businesses_settings', 'wyz_sub_pay_woo_on_off' );
		register_setting( 'wyz_businesses_settings', 'wyz_users_can_booking' );
		register_setting( 'wyz_businesses_settings', 'wyz_users_can_job' );
		register_setting( 'wyz_businesses_settings', 'wyz_job_requires_business' );
		register_setting( 'wyz_businesses_settings', 'wyz_jobs_max' );
		register_setting( 'wyz_businesses_settings', 'wyz_job_submit_cost' );
		register_setting( 'wyz_businesses_settings', 'wyz_businesses_registery_price' );
		register_setting( 'wyz_businesses_settings', 'wyz_featured_posts_perpage' );
		register_setting( 'wyz_businesses_settings', 'wyz_business_post_cost' );
		register_setting( 'wyz_businesses_settings', 'wyz_businesses_points_transfer' );
		register_setting( 'wyz_businesses_settings', 'wyz_hide_points' );
		register_setting( 'wyz_businesses_settings', 'wyz_points_transfer_fee' );
		register_setting( 'wyz_businesses_settings', 'wyz_max_allowed_businesses' );
		register_setting( 'wyz_businesses_settings', 'wyz_max_attchmtn_count' );
		register_setting( 'wyz_businesses_settings', 'wyz_enable_favorite_business' );
		register_setting( 'wyz_businesses_settings', 'wyz_businesses_user_client' );
		register_setting( 'wyz_businesses_settings', 'wyz_businesses_user_owner' );
		register_setting( 'wyz_businesses_settings', 'wyz_add_points_registration' );
		register_setting( 'wyz_businesses_settings', 'wyz_map_lockable' );
		register_setting( 'wyz_businesses_settings', 'wyz_archives_map_zoom' );
		register_setting( 'wyz_businesses_settings', 'wyz_archives_map_sidebar' );
		register_setting( 'wyz_businesses_settings', 'wyz_archives_map_my_loction' );
		register_setting( 'wyz_businesses_settings', 'wyz_archives_grid_view' );
		register_setting( 'wyz_businesses_settings', 'wyz_business_archive_before_listings' );
		register_setting( 'wyz_businesses_settings', 'wyz_business_map_hide_in_archives' );
		register_setting( 'wyz_businesses_settings', 'wyz_hide_map_poi' );
		register_setting( 'wyz_businesses_settings', 'wyz_business_map_scroll_zoom' );
		register_setting( 'wyz_businesses_settings', 'wyz_business_map_radius_unit' );
		register_setting( 'wyz_businesses_settings', 'wyz_businesses_from_email' );
		register_setting( 'wyz_businesses_settings', 'wyz_business_archives_map_skin' );
		register_setting( 'wyz_businesses_settings', 'wyz_business_new_single_permalink' );
		register_setting( 'wyz_businesses_settings', 'wyz_business_tags_new_single_permalink' );
		register_setting( 'wyz_businesses_settings', 'wyz_business_category_new_permalink' );
		register_setting( 'wyz_businesses_settings', 'wyz_business_post_new_single_permalink' );
		register_setting( 'wyz_businesses_settings', 'wyz_location_new_single_permalink' );
		register_setting( 'wyz_businesses_settings', 'wyz_location_plural_name' );
		register_setting( 'wyz_businesses_settings', 'wyz_business_plural_name' );
		register_setting( 'wyz_businesses_settings', 'wyz_map_api_key' );
		register_setting( 'wyz_businesses_settings', 'wyz_map_max_ajax_load' );
	}
}

if ( ! function_exists( 'wyz_register_single_business_settings' ) ) {
	/**
	 * Register businesses settings.
	 */
	function wyz_register_single_business_settings() {
		register_setting( 'wyz_single_businesses_settings', 'wyz_allow_business_post_edit' );
		register_setting( 'wyz_single_businesses_settings', 'wyz_hide_business_name_in_business_posts' );
		register_setting( 'wyz_single_businesses_settings', 'wyz_enable_business_footer' );
		register_setting( 'wyz_single_businesses_settings', 'wyz_businesses_default_lat' );
		register_setting( 'wyz_single_businesses_settings', 'wyz_businesses_default_lon' );
		register_setting( 'wyz_single_businesses_settings', 'wyz_business_map_hide_in_single_bus' );
		register_setting( 'wyz_single_businesses_settings', 'wyz_businesses_map_height' );
		register_setting( 'wyz_single_businesses_settings', 'wyz_business_header_content' );
		register_setting( 'wyz_single_businesses_settings', 'wyz_hide_header_busienss_logo_case_of_image_header' );
		register_setting( 'wyz_single_businesses_settings', 'wyz_switch_sidebars_single_bus' );
		register_setting( 'wyz_single_businesses_settings', 'wyz_hide_header_social_share' );
		register_setting( 'wyz_single_businesses_settings', 'wyz_hide_extra_sidebar_single_bus' );
		register_setting( 'wyz_single_businesses_settings', 'wyz_businesses_immediate_publish' );
		register_setting( 'wyz_single_businesses_settings', 'wyz_businesses_draft_on_expiry' );
		register_setting( 'wyz_single_businesses_settings', 'wyz_allow_business_edit' );
		register_setting( 'wyz_single_businesses_settings', 'wyz_businesses_no_posts' );
		register_setting( 'wyz_single_businesses_settings', 'wyz_businesses_no_images' );
		register_setting( 'wyz_single_businesses_settings', 'wyz_businesses_no_business' );
		register_setting( 'wyz_single_businesses_settings', 'wyz_businesses_no_ratings' );
		register_setting( 'wyz_single_businesses_settings', 'wyz_businesses_rate_owner' );
		register_setting( 'wyz_single_businesses_settings', 'wyz_businesses_rate_sub' );
		register_setting( 'wyz_single_businesses_settings', 'wyz_businesses_rate_rated_sub' );
		register_setting( 'wyz_single_businesses_settings', 'wyz_businesses_rate_not_sub' );
		register_setting( 'wyz_single_businesses_settings', 'wyz_business_post_social_share' );
		register_setting( 'wyz_single_businesses_settings', 'wyz_openclose_time_format_24' );
	}
}

if ( ! function_exists( 'wyz_register_business_custom_fields_settings' ) ) {
	/**
	 * Register businesses settings.
	 */
	function wyz_register_business_custom_fields_settings() {
		register_setting( 'business_custom_form_fields', 'wyz_business_custom_form_data' );
	}
}

if ( ! function_exists( 'wyz_register_registration_fields_settings' ) ) {
	/**
	 * Register registration form settings.
	 */
	function wyz_register_registration_fields_settings() {
		register_setting( 'user_settings', 'wyz_user_dashboard_template' );
		register_setting( 'user_settings', 'wyz_reg_def_user_role' );
		register_setting( 'user_settings', 'wyz_user_email_verification' );
		register_setting( 'user_settings', 'wyz_terms_and_cond_checkbox' );
		register_setting( 'user_settings', 'wyz_user_export_erase' );
	}
}

if ( ! function_exists( 'wyz_register_user_registration_fields_settings' ) ) {

	function wyz_register_user_registration_fields_settings() {

	}
}

if ( ! function_exists( 'wyz_register_business_form_fields_builder_settings' ) ) {

	function wyz_register_business_form_fields_builder_settings() {
		register_setting( 'business_form_fields_builder', 'business_form_fields_builder' );
	}
}
if ( ! function_exists( 'wyz_register_single_business_tab_fields_settings' ) ) {

	function wyz_register_single_business_tab_fields_settings() {
		
	}
}


if ( ! function_exists( 'wyz_register_offer_settings' ) ) {
	/**
	 * Register offers settings.
	 */
	function wyz_register_offer_settings() {
		register_setting( 'wyz_offers_settings', 'wyz_disable_offers' );
		register_setting( 'wyz_offers_settings', 'wyz_offer_point_price' );
		register_setting( 'wyz_offers_settings', 'wyz_offer_expiry_date' );
		register_setting( 'wyz_offers_settings', 'wyz_offer_immediate_publish' );
		register_setting( 'wyz_offers_settings', 'wyz_offer_editable' );
		register_setting( 'wyz_offers_settings', 'wyz_offers_new_single_permalink' );
		register_setting( 'wyz_offers_settings', 'wyz_offer_allow_schedule' );
		register_setting( 'wyz_offers_settings', 'wyz_offer_plural_name' );
	}
}

if ( ! function_exists( 'wyz_register_woocommerce_settings' ) ) {
	/**
	 * Register woocommerce settings.
	 */
	function wyz_register_woocommerce_settings() {
		register_setting( 'wyz_woocommerce_settings', 'wyz_can_become_vendor' );
		register_setting( 'wyz_woocommerce_settings', 'wyz_auto_vendorship' );
		register_setting( 'wyz_woocommerce_settings', 'wyz_allow_front_end_submit' );
		register_setting( 'wyz_woocommerce_settings', 'wyz_vendors_can_booking_products' );
		register_setting( 'wyz_woocommerce_settings', 'wyz_display_booking_products_in_listing' );
		register_setting( 'wyz_woocommerce_settings', 'products_tab_label' );
		register_setting( 'wyz_woocommerce_settings', 'wyz_woocommerce_hide_orders_tab' );
		register_setting( 'wyz_woocommerce_settings', 'wyz_woocommerce_hide_related' );
		register_setting( 'wyz_woocommerce_settings', 'wyz_woocommerce_hide_reviews' );
		register_setting( 'wyz_woocommerce_settings', 'wyz_woocommerce_hide_menu_cart' );
		register_setting( 'wyz_woocommerce_settings', 'wyz_woocommerce_hide_menu_cart_if_empty' );
		register_setting( 'wyz_woocommerce_settings', 'wyz_woocommerce_soldby_direct_to' );
	}
}

if ( ! function_exists( 'wyz_register_privileges_settings' ) ) {
	/**
	 * Register points settings.
	 */
	function wyz_register_privileges_settings() {
		register_setting( 'wyz_privileges_settings', 'wyz_businesses_hide_admin_bar' );
		register_setting( 'wyz_privileges_settings', 'wyz-user-can-upgrade-account' );
		register_setting( 'wyz_privileges_settings', 'wyz_businesses_restrict_backend_access' );
		register_setting( 'wyz_privileges_settings', 'wyz_vendor_restrict_backend_access' );
	}
}

if ( ! function_exists( 'wyz_claim_settings' ) ) {
	/**
	 * Register Claim settings.
	 */
	function wyz_claim_settings() {
		register_setting( 'wyz_claim_settings', 'wyz_claim_should_be_business_owner' );
		register_setting( 'wyz_claim_settings', 'wyz_claim_submission_price' );
		register_setting( 'wyz_claim_settings', 'wyz_business_claiming' );
	}
}
if ( ! function_exists( 'wyz_claim_form' ) ) {

	function wyz_claim_form() {

	}
}

if ( ! function_exists( 'wyz_social_login_settings' ) ) {
	/**
	 * Register Social login settings.
	 */
	function wyz_social_login_settings() {
		register_setting( 'wyz_social_login_settings', 'wyz_fb_app_id' );
		register_setting( 'wyz_social_login_settings', 'wyz_fb_app_secret' );
		register_setting( 'wyz_social_login_settings', 'wyz_google_client_id' );
		register_setting( 'wyz_social_login_settings', 'wyz_google_client_secret' );
		register_setting( 'wyz_social_login_settings', 'wyz_google_developer_key' );
	}
}

if ( ! function_exists( 'wyz_private_msg_settings' ) ) {
	/**
	 * Register private messaging settings.
	 */
	function wyz_private_msg_settings() {
		register_setting( 'wyz_private_msg_settings', 'wyz_private_msg_status_on_off' );
		register_setting( 'wyz_private_msg_settings', 'wyz_private_msg_hide_client' );
		/*register_setting( 'wyz_private_msg_settings', 'wyz_fcm_on_off' );
		register_setting( 'wyz_private_msg_settings', 'wyz_fcm_api_key' );
		register_setting( 'wyz_private_msg_settings', 'wyz_fcm_auth_domain' );
		register_setting( 'wyz_private_msg_settings', 'wyz_fcm_db_url' );
		register_setting( 'wyz_private_msg_settings', 'wyz_fcm_project_id' );
		register_setting( 'wyz_private_msg_settings', 'wyz_fcm_storage_bucket' );
		register_setting( 'wyz_private_msg_settings', 'wyz_fcm_msg_sndr_id' );*/
	}
}


function wyz_admin_tabs( $current = 'businesses' ) {
	$tabs = array(
		'businesses' =>  esc_html__( 'General Settings', 'wyzi-business-finder' ),
		'single_business_settings' => esc_html__( 'Single Business', 'wyzi-business-finder' ),
		'single_business_tab_settings' => esc_html__( 'Single Business Tabs', 'wyzi-business-finder' ),	
		'single_business_sidebar_settings' => esc_html__( 'Business Sidebar', 'wyzi-business-finder' ),		
		'business_form_fields_builder' => esc_html__( 'Business Form', 'wyzi-business-finder'),
		'business_custom_form_fields' => esc_html__( 'Custom Fields', 'wyzi-business-finder' ),
		'user_settings' => esc_html__( 'User Settings', 'wyzi-business-finder' ),
		'user_regiteration_fields' => esc_html__( 'Registration Form', 'wyzi-business-finder' ),
		'offers' =>  esc_html__( 'Offer Settings', 'wyzi-business-finder' ),
		'woocommerce' => esc_html__( 'Woocommerce Settings', 'wyzi-business-finder' ),
		'privileges' => esc_html__( 'Privileges', 'wyzi-business-finder' ),
		'claim_settings' => esc_html__( 'Claim Settings', 'wyzi-business-finder' ),
		'claim_form' => esc_html__( 'Claim Form', 'wyzi-business-finder' ),
		'social_login' => esc_html__( 'Social Login', 'wyzi-business-finder' ),
		'private_msg' => esc_html__( 'Private Messaging', 'wyzi-business-finder' ),
	);
	$tabs = apply_filters( 'wyz_toolkit_settings_tabs', $tabs );
	$links = array();
	echo '<div id="icon-themes" class="icon32"><br></div>';
	echo '<h2 class="nav-tab-wrapper">';

	foreach ( $tabs as $tab => $name ) {
		$class = ( $tab == $current ) ? ' nav-tab-active' : '';
		echo "<a class='nav-tab$class' href='?page=wyzi-toolkit-options&tab=$tab'>$name</a>";
	}
	echo '</h2>';
}
