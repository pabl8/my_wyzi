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
		<form method="post" class="login-reg-form">
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
		</div>
		</form>
		<?php
	}

	protected function after_fields() {
		?>
		<input type="hidden" name="wyz_register_nonce" value="<?php echo esc_attr( wp_create_nonce( 'wyz-register-nonce' ) ); ?>"/>
		<div class="col-xs-12">
			<button id="submitr" type="submit" class="action-btn btn-bg-blue wyz-prim-color btn-rounded"><?php esc_html_e( 'SIGN UP', 'wyzi-business-finder' ); ?></button>
			<?php wyz_this_get_login_social_options();?>
		</div>
		<?php if ( function_exists( 'wyz_get_option' ) && 'on' == wyz_get_option( 'terms-and-cond-on-off' ) ) {?>
			<div class="col-xs-12 terms-and-cond">
				<?php wyz_extract_termsandconditions();?>
			</div>
		<?php }
	}

	protected function after_profile_fields() {
		?>
		<?php if ( current_user_can( 'publish_businesses' ) && 'on' != get_option( 'wyz_hide_points' ) ) {
				$points_credit = get_the_author_meta( 'points_available', $this->user_id );
				$points_credit = ( isset( $points_credit ) ? $points_credit : 0 );?>
			<div class="col-xs-6 mb-25">
				<div class="input-box gray-bg">
					<label for="available-points"><?php esc_html_e( 'Available Points', 'wyzi-business-finder' ); ?></label>
					<input name="available-points" type="text" disabled value="<?php echo esc_html( $points_credit );?>" />
				</div>
			</div>
			<div class="col-xs-6 mb-25">
				<label class="opacity"><?php esc_html_e( 'buy points', 'wyzi-business-finder' );?></label>
				<a id="buy-points" href="<?php echo WyzHelpers::add_clear_query_arg( array( WyzQueryVars::GetPoints =>true ) ); ?>"><?php esc_html_e( 'Buy Points', 'wyzi-business-finder' ); ?></a>
			</div>
			<?php }
			// Action hook for plugin and extra fields.
			do_action( 'wyz_edit_user_profile', $this->user_id ); ?>
			<div class="col-xs-12">
				<center><button id="wyz-update-user" type="submit" class="action-btn btn-bg-blue wyz-prim-color btn-rounded"><?php echo esc_html__( 'update', 'wyzi-business-finder' ); ?></button></center>
				<a id="logout-btn" href="<?php echo wp_logout_url( home_url() ); ;?>"  class="action-btn logout-btn"><?php echo esc_html__( 'logout', 'wyzi-business-finder' ); ?></a>
			</div>
			<?php wp_nonce_field( 'wyz-update-user' ); ?>
			<input name="action" type="hidden" id="action" value="wyz-update-user" />
			<input name="wyz-updateuser" type="hidden" value="1" />

			<?php if ( 'on' == get_option( 'wyz_user_export_erase' ) && function_exists( 'wp_create_user_request' ) ) { ?>
			<div id="user-comp-cont">
				<button id="delete-user" class="float-left" data-nonce="<?php echo wp_create_nonce( 'wyz_delete_' . $this->user_id .  '_user' );?>"><?php esc_html_e( 'Delete me', 'wyzi-business-finder' );?></button>
				<button id="export-user" data-nonce="<?php echo wp_create_nonce( 'wyz_export_' . $this->user_id .  '_user' );?>" class="wyz-prim-color-txt wyz-primary-color-txt float-right"><?php esc_html_e( 'Export my data', 'wyzi-business-finder' );?></button>
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
		?><div class="input-two mb-50"><?php
	}


	protected function pass_field( $key, $value,$is_profile ) {
		$rep_show = !isset( $value['hideRepPass'] );
		$class = ( $rep_show ? 'col-md-6 ' : '' ) . 'col-xs-12 mb-50';?>
		<div class="<?php echo $class;?>">
			<label for="wyz_user_pass "><?php echo $value['label']; ?><span class="req"> *</span></label>
			<input id="wyz_user_pass " name="wyz_user_pass" type="password" <?php echo !$is_profile ? 'required' : '';?>/>
		</div>
		<?php if ( $rep_show ) {?>
		<div class="col-md-6 col-xs-12 mb-50">
			<label for="password_again">
			    <?php echo $value['passaglabel']; ?>
			<span class="req"> *</span></label>
			<input id="password_again" name="wyz_user_pass_confirm" type="password" <?php echo !$is_profile ? 'required' : '';?>/>
		</div>
		<?php }?>
		<div class="col-xs-12">
			<span id="password-strength"></span>
		</div>
		<?php
	}
}