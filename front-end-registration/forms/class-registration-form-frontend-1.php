<?php

class WyzRegistrationForm_Frontend extends WyzRegistrationForm_Frontend_Parent {

	public function the_form() {
		parent::the_form();
	}

	public function the_profile_form() {
		parent::the_profile_form();
	}

	protected function open_form() {
		?>
		<div class="register-form text-center <?php echo $this->offset;?>col-md-offset-2 col-md-8 col-xs-12">
			<form id="wyz_registration_form" enctype="multipart/form-data" class="wyz-form" method="POST">
		<?php
	}

	protected function open_profile_form() {
		?>
		<form id="wyz_registration_form" enctype="multipart/form-data" class="login-reg-form" method="POST">
			<div class="row">
		<?php
	}

	protected function close_form() {
		?>
		</form>
		</div>
		<?php
	}

	protected function after_fields() {
		?>
		<input type="hidden" name="wyz_register_nonce" value="<?php echo esc_attr( wp_create_nonce( 'wyz-register-nonce' ) ); ?>"/>
		<?php if ( function_exists( 'wyz_get_option' ) && 'on' == wyz_get_option( 'terms-and-cond-on-off' ) ) {?>
		<div class="terms-and-cond fix">
			<?php wyz_extract_termsandconditions();?>
		</div>
		<?php }?>
		<button id="submitr" type="submit" class="wyz-button wyz-secondary-color icon" value=""><?php esc_html_e( 'SIGN UP', 'wyzi-business-finder' ); ?> <i class="fa fa-angle-right"></i></button>
		<div class="social-login-container">
		<?php if ( '' != get_option( 'wyz_fb_app_id' ) && '' != get_option( 'wyz_fb_app_secret' ) ) {
			echo do_shortcode( '[fbl_login_button]' );
		}
		if ( '' != get_option( 'wyz_google_client_id' ) && '' != get_option( 'wyz_google_client_secret' ) && '' != get_option( 'wyz_google_developer_key' ) ){ ?>
		<a href="<?php echo wp_login_url() . '?loginGoogle=1&redirect=' . home_url( '/user-account/' );?>" class="wyz-button icon social-login google"><i class="fa fa-google"></i><?php esc_html_e( 'Sign Up with google', 'wyzi-business-finder' );?></a>
		<?php }?>
		</div>
		<p id="have-acc" class="margin-top-15  wyz-prim-color-txt"><?php esc_html_e( 'Already have an account?', 'wyzi-business-finder' );?> <a href="<?php echo esc_url( home_url( '/signup/?action=login' ) );?>" class="link"><?php esc_html_e( 'Login', 'wyzi-business-finder' );?></a></p>
		<?php
	}

	protected function after_profile_fields() {
		if ( current_user_can( 'publish_businesses' ) && 'on' != get_option( 'wyz_hide_points' ) ) {
			$points_credit = get_the_author_meta( 'points_available', $this->user_id );
			$points_credit = ( isset( $points_credit ) ? $points_credit : 0 );?>
		<div class="input-two space-80">
			<div class="input-box gray-bg">
				<label for="available-points"><?php esc_html_e( 'Available Points', 'wyzi-business-finder' ); ?></label>
				<input name="available-points" type="text" disabled value="<?php echo esc_html( $points_credit );?>" />
			</div>
			<div class="input-box">
				<label class="opacity"><?php esc_html_e( 'buy points', 'wyzi-business-finder' );?></label>
				<?php $buy_per=array( WyzQueryVars::GetPoints =>true );if( isset($_GET['page']) )$buy_per['page']=$_GET['page'];?>
				<a id="buy-points" href="<?php echo WyzHelpers::add_clear_query_arg( $buy_per ); ?>"><?php esc_html_e( 'Buy Points', 'wyzi-business-finder' ); ?></a>
			</div>
		</div>
		<?php }
		// Action hook for plugin and extra fields.
		do_action( 'wyz_edit_user_profile', $this->user_id ); ?>
		
		<button id="wyz-update-user" type="submit" class="wyz-button wyz-secondary-color wyz-prim-color icon"><?php echo esc_html__( 'update', 'wyzi-business-finder' ); ?> <i class="fa fa-angle-right"></i></button>
		<a id="logout-btn" href="<?php echo wp_logout_url( home_url() ); ;?>"  class="action-btn logout-btn"><?php echo esc_html__( 'logout', 'wyzi-business-finder' ); ?></a>
		<?php wp_nonce_field( 'wyz-update-user' ); ?>
		<input name="action" type="hidden" id="action" value="wyz-update-user" />
		<input name="wyz-updateuser" type="hidden" value="1" />

		<?php if ( 'on' == get_option( 'wyz_user_export_erase' ) && function_exists( 'wp_create_user_request' ) ) { ?>
		<div id="user-comp-cont">
			<button id="delete-user" data-nonce="<?php echo wp_create_nonce( 'wyz_delete_' . $this->user_id .  '_user' );?>"><?php esc_html_e( 'Delete me', 'wyzi-business-finder' );?></button>
			<button id="export-user" data-nonce="<?php echo wp_create_nonce( 'wyz_export_' . $this->user_id .  '_user' );?>" class="wyz-prim-color-txt wyz-primary-color-txt"><?php esc_html_e( 'Export my data', 'wyzi-business-finder' );?></button>
		</div>
		<?php }
	}

	protected function open_field( $type ) {
		if ( 'password' != $type ) {
			?>
			<div class="input-box">
			<?php
		}
	}

	protected function close_field( $type ) {
		if ( 'password' != $type ) {
			?>
			</div>
			<?php
		}
	}

	protected function open_separate() {
		?><div class="input-two space-80"><?php
	}
	protected function pass_field(  $key, $value,$is_profile ) {
		$rep_show = !isset( $value['hideRepPass'] ) || ! $value['hideRepPass'];
		$class = ( $rep_show ? 'input-two ' : 'pass-field ' ) . 'space-80';?>
		<div class="<?php echo $class;?>">
			<div class="input-box">
				<label for="password"><?php echo $value['label']; ?><span class="req"> *</span></label>
				<input name="wyz_user_pass" id="password" type="password" <?php echo !$is_profile ? 'required' : '';?>/>
			</div>
		<?php if ( $rep_show ) {?>
			<div class="input-box">
				<label for="password_again"><?php echo $value['passaglabel']; ?><span class="req"> *</span></label>
				<input name="wyz_user_pass_confirm" id="password_again" type="password" <?php echo !$is_profile ? 'required' : '';?>/>
			</div>
		<?php }?>
		</div>
		<div class="clear"><span id="password-strength"></span></div>
		<?php
	}


}