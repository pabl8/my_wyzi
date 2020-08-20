<?php
/**
 * Business controls display
 *
 * @package wyz
 */

/**
 * Get the user's business info from the database, if any.
 */
if ( ! function_exists( 'wyz_business' ) ) {
	function wyz_business() {
		if ( ! current_user_can( 'publish_businesses' ) ) {
			return;
		}

		$user_id = get_current_user_id();

		if ( ! WyzHelpers::wyz_sub_can_bus_owner_do( $user_id , 'wyzi_sub_can_create_business' ) ){
			WyzHelpers::wyz_info( esc_html__( 'You need to subscribe to a plan to be able to create or edit a business.', 'wyzi-business-finder' ), true );
		}


		$user_businesses = WyzHelpers::get_user_businesses( $user_id );
		$return = wyz_display_business( $user_businesses );

		if ( ! WyzHelpers::wyz_has_business( $user_id ) ) {
			$return .= WyzHelpers::wyz_info( esc_html( get_option( 'wyz_businesses_no_business' ) ), true );
		}

		if ( WyzHelpers::user_can_create_business( $user_id ) ) {
			$return .= '<center class="clear"><a class="wyz-button wyz-primary-color icon wyz-prim-color"  href="?add-new-business=true">' . esc_html__( 'Add New business', 'wyzi-business-finder' ) . ' <i class="fa fa-angle-right" aria-hidden="true"></i></a></center>';
		}
		
		return $return;
	}
}

/**
 * Display the user's business.
 *
 * @param object $current_post the current business being displayed.
 */

if ( ! function_exists( 'wyz_display_business' ) ) {
	function wyz_display_business( $user_businesses ) {
		ob_start();

		if ( filter_input( INPUT_GET, 'business_created' ) && ( isset( $user_businesses['pending'][ $_GET[ 'business_created' ] ] ) || isset( $user_businesses['published'][ $_GET[ 'business_created' ] ] ) ) ) {

			//$current_post = get_post( $_GET[ 'business_created' ] );
			// If user doesn't have enough points to publish an offer.
			if ( isset( $_GET['not_enough_points'] ) ) {
				WyzHelpers::wyz_info( esc_html__( 'You don\'t have enough points to create a business, so your business is now pending', 'wyzi-business-finder' ) );
			} elseif ( 'off' === get_option( 'wyz_businesses_immediate_publish' ) ) { // If option for imediate publish is turned off by the admin.
				WyzHelpers::wyz_success( esc_html__( 'Thank you, your business is now pending for review.', 'wyzi-business-finder' ) );
			} elseif ( 'on' === get_option( 'wyz_businesses_immediate_publish' ) ) { // If option for imediate publish is turned on by the admin.
				WyzHelpers::wyz_success( esc_html__( 'Thank you, your new business has been created successfully.', 'wyzi-business-finder' ) );
			}
		} elseif ( filter_input( INPUT_GET, 'business_updated' ) && isset( $user_businesses[ $_GET[ 'business_updated' ] ] ) ) { // If user just updated his business.

			// Add notice of submission to our output.
			WyzHelpers::wyz_success( esc_html__( 'Thank you, your new business info was updated successfully.', 'wyzi-business-finder' ) );
		}
		
		wyz_display_user_businesses_table( $user_businesses );

		return ob_get_clean();
	}
}


if ( ! function_exists( 'wyz_display_user_businesses_table' ) ) {

	function wyz_display_user_businesses_table( &$user_businesses ) {
		global $template_type;
		$btn_class = ( 2 == $template_type ? 'action-btn btn-bg-blue' : 'btn') . ' wyz-secondary-color-hover';
		if ( ! apply_filters( 'wyz_hide_vendor_settings_page', false ) && WyzHelpers::is_user_vendor( get_current_user_id() ) ) {
			$wyz_shop_settings_link = get_home_url( null,'/wcmp/' );
			if ( function_exists( 'wcmp_vendor_dashboard_page_id' ) ) 
				$wyz_shop_settings_link = get_page_link(wcmp_vendor_dashboard_page_id());
			echo '<div id="shop-settings" class="float-right"><a class="btn-square wyz-primary-color wyz-prim-color" href="'.$wyz_shop_settings_link.'">'.esc_html__('Shop Settings','wyzi-business-finder').'</a></div>';
		}
		
		if ( isset( $user_businesses['pending'] ) && ! empty( $user_businesses['pending'] ) ) : ?>
			
		<div class="section-title col-xs-12 margin-bottom-50"><div class="row">
			<h1><?php echo esc_html__( 'PENDING BUSINESSES', 'wyzi-business-finder' );?></h1>
		</div></div>
		<!-- pending businesses -->
		<div class="publish-offers col-xs-12">
			
			<?php do_action( 'wy_user_account_before_pending_businesses' );?>
			
			<div class="row">

				<?php foreach( $user_businesses['pending'] as $curr_id ) :

					$curr_post = get_post( $curr_id );?>
				<div class="sin-pub-offer">
					<div class="logo"><?php echo WyzHelpers::get_post_thumbnail( $curr_id, 'business', 'thumbnail' );?></div>
					<div class="title"><h4><a href="<?php echo get_the_permalink( $curr_id );?>"><?php echo get_the_title( $curr_id ); ?></a></h4></div>
					<div class="buttons">
						<?php $business_tbl_btns =  array(
							'manage' => array(
								'link' => WyzHelpers::add_clear_query_arg( array( WyzQueryVars::ManageBusiness => $curr_id ) ),
								'label' => esc_html__( 'manage', 'wyzi-business-finder' )
							)
						);
						if ( 'off' != get_option( 'wyz_users_can_booking' ) && WyzHelpers::wyz_sub_can_bus_owner_do( get_current_user_id() , 'wyzi_sub_business_can_create_bookings' ) ){
							$business_tbl_btns['calendar'] = array(
								'link' => WyzHelpers::add_clear_query_arg( array( WyzQueryVars::BusinessCalendar => $curr_id ) ),
								'label' => esc_html__( 'calendar', 'wyzi-business-finder' )
							);
						}

						$business_tbl_btns = apply_filters( 'wyz_pending_business_table_buttons', $business_tbl_btns, $curr_id );
						foreach ($business_tbl_btns as $key => $value) {
							echo '<a href="' . $value['link'] . '"' . ( isset( $value['extra'] ) ? ( ' ' . $value['extra'] ) : '' ) . 'class="' . ( isset( $value['class'] ) ? $value['class'] : $btn_class ) . '">' . $value['label'] . '</a>';
						}
						?>
					</div>
				</div>
			<?php 
			endforeach;?>
			</div>
		</div>
		<?php endif;

		if ( isset( $user_businesses['published'] ) && ! empty( $user_businesses['published'] ) ) : ?>
			
		<div class="section-title col-xs-12 margin-bottom-50"><div class="row">
			<h1 class="mb-35"><?php echo esc_html__( 'Published Businesses', 'wyzi-business-finder' );?></h1>
		</div></div>
		<!-- published businesses -->
		<div class="publish-offers col-xs-12">

			<?php do_action( 'wy_user_account_before_published_businesses' );?>

			<div class="row">
		
			<?php foreach( $user_businesses['published'] as $curr_id ) :

				$curr_post = get_post( $curr_id );?>
			<div class="sin-pub-offer">
				<div class="logo"><?php echo WyzHelpers::get_post_thumbnail( $curr_id, 'business', 'thumbnail' );?></div>
				<div class="title"><h4><a href="<?php echo get_the_permalink( $curr_id );?>"><?php echo get_the_title( $curr_id ); ?></a></h4></div>
				<div class="buttons">
					<?php $business_tbl_btns =  array(
						'manage' => array(
							'link' => WyzHelpers::add_clear_query_arg( array( WyzQueryVars::ManageBusiness => $curr_id ) ),
							'label' => esc_html__( 'manage', 'wyzi-business-finder' )
						)
					);
					if ( 'off' != get_option( 'wyz_users_can_booking' ) && WyzHelpers::wyz_sub_can_bus_owner_do( get_current_user_id() , 'wyzi_sub_business_can_create_bookings' ) ){
						$business_tbl_btns['calendar'] = array(
							'link' => WyzHelpers::add_clear_query_arg( array( WyzQueryVars::BusinessCalendar => $curr_id ) ),
							'label' => esc_html__( 'calendar', 'wyzi-business-finder' )
						);
					}
					$business_tbl_btns = apply_filters( 'wyz_published_business_table_buttons', $business_tbl_btns, $curr_id );
					foreach ($business_tbl_btns as $key => $value) {
						echo '<a href="' . $value['link'] . '"' . ( isset( $value['extra'] ) ? ( ' ' . $value['extra'] ) : '' ) . 'class="' . ( isset( $value['class'] ) ? $value['class'] : $btn_class ) . '">' . $value['label'] . '</a>';
					}?>
				</div>
			</div>
			<?php 
			endforeach;?>
			</div>
		</div>
		<?php endif;
	}

}

if ( ! function_exists( 'wyz_display_user_business' ) ) {
	function wyz_display_user_business() {
		$user_businesses = WyzHelpers::get_user_businesses();

		$user_id = get_current_user_id();

		if ( ! isset( $user_businesses['pending'][ $_GET[ WyzQueryVars::ManageBusiness ] ] ) && ! isset( $user_businesses['published'][ $_GET[ WyzQueryVars::ManageBusiness ] ] ) ) {
			return;
		}
		if ( ! isset( $user_businesses['published'][ $_GET[ WyzQueryVars::ManageBusiness ] ] ) ) {
			WyzHelpers::wyz_info( esc_html__( 'This business is pending', 'wyzi-business-finder' ) );
			if ( 'on' == get_option( 'wyz_businesses_immediate_publish' ) && WyzHelpers::wyz_current_user_affords_business_registry() ) {
				WyzHelpers::wyz_success( esc_html( 'You have enough points to', 'wyzi-business-finder' ) . ' <a href="' . WyzHelpers::add_clear_query_arg( array( 'publish-business' => $_GET[  WyzQueryVars::ManageBusiness  ] ) ) . '">' . esc_html__( 'Publish', 'wyzi-business-finder' ) . '</a> ' . esc_html__( 'your business', 'wyzi-business-finder' ) );
			}
		}
		?>
		<!--buttons for view, edit and delete business-->
		<div class="business-buttons col-xs-12 row">
			<a href="<?php echo esc_url( get_the_permalink( $_GET[ WyzQueryVars::ManageBusiness ] ) ); ?>" class="busi-btn wyz-primary-color wyz-prim-color"><?php esc_html_e( 'View Business', 'wyzi-business-finder' );?></a>
			<?php if( current_user_can( 'administrator' ) || ( 'off' != get_option( 'wyz_allow_business_edit' ) && WyzHelpers::wyz_sub_can_bus_owner_do( $user_id , 'wyzi_sub_can_edit_business' ) ) ) { ?>
			<a href="<?php echo WyzHelpers::add_clear_query_arg( array( 'edit-business' => $_GET[  WyzQueryVars::ManageBusiness  ] ) )?>" class="busi-btn wyz-primary-color wyz-prim-color"><?php esc_html_e( 'Edit Business', 'wyzi-business-finder' );?></a>
			<?php }?>
			<a href="<?php echo esc_url( get_delete_post_link( $_GET[ WyzQueryVars::ManageBusiness ] ) ); ?>" class="busi-btn wyz-primary-color wyz-prim-color" onclick="return confirm( '<?php esc_html_e( 'Are you sure you want to delete your business? This step is irreversible.', 'wyzi-business-finder' );?>' );"><?php esc_html_e( 'Delete Business', 'wyzi-business-finder' );?></a>
			<?php if ( class_exists( 'WooCommerce' ) && class_exists( 'WCMp' ) && function_exists( 'is_user_wcmp_vendor' ) && is_user_wcmp_vendor( get_current_user_id() ) ) {
			}
			do_action( 'wyz_manage_business_buttons', $_GET[ WyzQueryVars::ManageBusiness ] );
			?>
		</div>
		<?php
		echo wyz_display_offers();
	}
}

?>
