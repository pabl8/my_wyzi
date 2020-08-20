<div class="row-fluid">		
	<div class="span12">
	<div class="user-profile-form">
		<?php $user_id = get_current_user_id();
	global $WYZ_USER_ACCOUNT_TYPE;
	global $template_type;


	do_action( 'wyz_before_user_profile', $user_id );
	?>

	<?php if ( ! is_user_logged_in() ) { ?>

			<p class="warning">
			    <?php esc_html_e( 'You must be logged in to edit your profile.', 'wyzi-business-finder' ); ?>
			</p>

	<?php } elseif ( isset( $_POST['upgrade-role-yes'] ) && 1 == $_POST['upgrade-role-yes'] ) {

			$u = new WP_User( $this->user_id );
			$u->remove_role( 'client' );
			$u->add_role( 'business_owner' );
			WyzHelpers::add_extra_points( $this->user_id );
			if( 'on' == get_option( 'wyz_auto_vendorship' ) && 'on' == get_option( 'wyz_can_become_vendor' ) && user_can( $this->user_id,'publish_businesses') && 'on' != get_option( 'wyz_sub_mode_on_off' ) ) {
				$user = new WP_User( $this->user_id );
				WyzHelpers::make_user_vendor( $this->user );
			}	
			WyzHelpers::wyz_success( esc_html__( 'Profile Updated', 'wyzi-business-finder' ) );
			echo '<script type="text/javascript">//<![CDATA[ 
			setTimeout(function(){ window.location = ' . wp_json_encode( get_permalink() ) . '; }, 2500); //]]></script>';

	}elseif ( isset( $_POST['upgrade-role'] ) && 1 == $_POST['upgrade-role'] && 'off' != get_option('wyz-user-can-upgrade-account') ) { ?>

		<div class="section-title margin-bottom-50">
			<h2><?php esc_html_e( 'Upgrade Account', 'wyzi-business-finder' );?></h2>
		</div>
		<p>
		<?php echo sprintf( esc_html__( 'You are about to upgrade your account to Business Owner. This allows you to create a business and start publishing %s.', 'wyzi-business-finder' ), WYZ_OFFERS_CPT );?><br/>
		<?php esc_html_e( 'Be aware that this step is', 'wyzi-business-finder' );?> <font color="red"><?php esc_html_e( 'irreversible', 'wyzi-business-finder' );?></font>.
		<?php esc_html_e( 'Click the button below to proceed.', 'wyzi-business-finder' );?>
		</p>
		<form name="upgrade-role-yes" method="POST">
			<input name="upgrade-role-yes" type="hidden" value="1"/>
			<button type="submit" class="wyz-button wyz-secondary-color wyz-prim-color icon"><?php esc_html_e( 'Upgrade', 'wyzi-business-finder' );?> <i class="fa fa-angle-right"></i></button>
		</form>

	<?php
	} /*elseif ( isset( $_POST['action'] ) && 'points-transfer' == $_POST['action'] && 'on' == get_option( 'wyz_businesses_points_transfer' ) ) { ?>

		<div class="section-title text-center margin-bottom-50">
			<h1><?php esc_html_e( 'Upgrade Account', 'wyzi-business-finder' );?></h1>
		</div>
		<p>
		<?php echo sprintf( esc_html__( 'You are about to upgrade your account to Business Owner. This allows you to create a business and start publishing %s.', 'wyzi-business-finder' ), get_option( 'wyz_offer_plural_name', WYZ_OFFERS_CPT ) );?><br/>
		<?php esc_html_e( 'Be aware that this step is', 'wyzi-business-finder' );?> <font color="red"><?php esc_html_e( 'irreversible', 'wyzi-business-finder' );?></font>.
		<?php esc_html_e( 'Click the button below to proceed.', 'wyzi-business-finder' );?>
		</p>
		<form name="upgrade-role-yes" method="POST">
			<input name="upgrade-role-yes" type="hidden" value="1"/>
			<button type="submit" class="wyz-button wyz-secondary-color wyz-prim-color icon"><?php esc_html_e( 'Upgrade', 'wyzi-business-finder' );?> <i class="fa fa-angle-right"></i></button>
		</form>

	<?php
	}*/ elseif ( $WYZ_USER_ACCOUNT_TYPE == WyzQueryVars::GetPoints ) { ?>
		<div class="section-title margin-bottom-50">
			<h2><?php esc_html_e( 'Buy Points', 'wyzi-business-finder' );?></h2>
		</div>
		<?php 

		echo do_shortcode('[product_category category="points-category"]');
		
	} else {
		global $update_error;
		if ( isset( $update_error ) && count( $update_error ) > 0 ) {
			WyzHelpers::wyz_error( implode( '', $update_error ) );
		}?>
		<div class="section-title margin-bottom-50">
			<h2><?php esc_html_e( 'profile', 'wyzi-business-finder' );?></h2>
		</div>
		
		<?php WyzRegistrationForm_Frontend_Factory::profile_form();?>
		
		
		<?php if ( ! current_user_can( 'publish_businesses' )   && 'off' != get_option('wyz-user-can-upgrade-account')) { ?> 

		<form name="upgrade-role" id="upgrade-role" method="POST">
			<input type="hidden" name="upgrade-role" value="1" />
			<button class="wyz-button wyz-primary-color wyz-prim-color icon" type="submit"><?php esc_html_e( 'Upgrade Account to Business Owner', 'wyzi-business-finder' );?> <i class="fa fa-angle-right"></i></button>
		</form>

		<?php }
	} ?>
	</div>
	</div>
</div>