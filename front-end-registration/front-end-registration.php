<?php
/**
 * Front end registration nitializer.
 *
 * @package wyz
 */

$mail_success = '';

/**
 * Display signup form.
 */

require_once (dirname(__FILE__) . '/social-login/facebook/facebook-login.php');
require_once (dirname(__FILE__) . '/social-login/google/google-connect.php');
require_once (dirname(__FILE__) . '/forms/class-registration-form-frontend-factory.php');

function wyz_signup_display() {
	global $wpdb;
	global $template_type;

	if ( 1 != get_option( 'users_can_register' ) && (! isset( $_GET['action'] ) || 'login' != $_GET['action'] ) ) {
		return WyzHelpers::wyz_error( esc_html__( 'User Registration is not enabled', 'wyzi-business-finder' ),true);
	}

	if ( is_user_logged_in() ) 
		return WyzHelpers::wyz_warning( esc_html__( 'You are already logged in.', 'wyzi-business-finder' ),true );

	if ( isset( $_GET['email-verify'] ) )
		return wyz_email_verification_message();

	if ( isset( $_GET['reset-pass'] ) && true == $_GET['reset-pass'] ) {
		return wyz_reset_pass_form();
	} elseif ( isset( $_GET['key'] ) && 'reset_pwd' === $_GET['action'] ) {
		$reset_key = $_GET['key'];
		$user_login = $_GET['login'];
		$user_data = $wpdb->get_row( $wpdb->prepare( "SELECT ID, user_login, user_email FROM $wpdb->users WHERE user_activation_key = %s AND user_login = %s", $reset_key, $user_login ) );
		$user_login = $user_data->user_login;
		$user_email = $user_data->user_email;
		if ( ! empty( $reset_key ) && ! empty( $user_data ) ) {

			echo wyz_new_password_form($user_data, $reset_key);
		} else {
			exit( WyzHelpers::wyz_error( esc_html__( 'Not a Valid Key', 'wyzi-business-finder' ),true) );
		}
	} else {
		if ( 1 == $template_type ) {
			if ( isset( $_GET['action'] ) && 'login' == $_GET['action'] ) {
				$out = '';
				if(isset($_GET['pwd-reset'])&&'complete'==$_GET['pwd-reset'])
					$out .= WyzHelpers::wyz_success( esc_html__( 'Password reset complete', 'wyzi-business-finder' ),true);
				$out .= wyz_login_form();
				return $out;
			} else {
				return wyz_registration_form();
			}
		} elseif ( 2 == $template_type ) {
			return wyz_login_registration_form();
		}
	}
}
add_shortcode( 'wyz_signup_form', 'wyz_signup_display' );



/**
 * New password form fields.
 */
function wyz_new_password_form($user_data,$token) {
	ob_start();
	$reset_pass_query = WyzHelpers::add_clear_query_arg( array( 'reset-pass' => true ) );
	// Show any error messages after form submission.
	wyz_show_error_messages(); ?>
	
	<div class="section-title col-xs-12 margin-bottom-50">
		<h1><?php  esc_html_e( 'New Password', 'wyzi-business-finder' ); ?></h1>
	</div>
	<!-- col-lg-6 col-md-7-->
	<div class="login-form col-xs-12 fix">
		<form id="wyz_registration_form" enctype="multipart/form-data" class="wyz-form" method="POST">
			<div class="input-two">
				<div class="input-box">
					<label for="wyz_user_new_pass"><?php esc_html_e( 'New Password', 'wyzi-business-finder' ); ?></label>
					<input name="wyz_user_new_pass" id="wyz_user_new_pass" class="text-input" type="password"/>
				</div>
				<div class="input-box">
					<label for="wyz_user_new_pass_rep"><?php esc_html_e( 'Password Repeat'
					, 'wyzi-business-finder' ); ?></label>
					<input name="wyz_user_new_pass_rep" id="wyz_user_new_pass_rep" class="text-input" type="password"/>
				</div>
			</div>
			<input type="hidden" name="wyz_pass_reset_nonce" value="<?php echo esc_attr( wp_create_nonce( 'wyz-password-reset-nonce' ) ); ?>"/>
			<input type="hidden" name="wyz_user_id" value="<?php echo $user_data->ID; ?>"/>
			<input type="hidden" name="wyz_pass_reset_token" value="<?php echo $token; ?>"/>
			<button id="wyz_login_submit" type="submit" class="wyz-button wyz-primary-color wyz-prim-color icon"><?php esc_html_e( 'Submit', 'wyzi-business-finder' ); ?> <i class="fa fa-angle-right"></i></button>
		</form>
	</div>
	<?php return ob_get_clean();
}



function wyz_do_reset_user_pass(){
	if(!isset($_POST['wyz_pass_reset_nonce'])||!wp_verify_nonce($_POST['wyz_pass_reset_nonce'],'wyz-password-reset-nonce')||
		!isset($_POST['wyz_user_new_pass'])||!isset($_POST['wyz_user_new_pass_rep'])||$_POST['wyz_user_new_pass']!=$_POST['wyz_user_new_pass_rep']
		|| !isset($_POST['wyz_pass_reset_token']))
			return;
		$token = $_POST['wyz_pass_reset_token'];
		$user = get_userdata( $_POST['wyz_user_id'] );
		if(!$user)return;
		global $wpdb;
		$user_data = $wpdb->get_row( $wpdb->prepare( "SELECT ID, user_login, user_email FROM $wpdb->users WHERE user_activation_key = %s AND user_login = %s", $token, $user->user_login ) );
		if ( ! empty( $token ) && ! empty( $user_data ) ) {
			wp_set_password( $_POST['wyz_user_new_pass'], $user->ID );
			wp_redirect(home_url('/signup/?action=login&pwd-reset=complete'));
			exit;
		}
}
add_action( 'wp', 'wyz_do_reset_user_pass' );

/**
 * User registration form.
 */
function wyz_registration_form() {
	// Only show the registration form to non-logged-in members.
	if ( ! is_user_logged_in() ) {
		add_action( 'wp_footer', 'wyz_add_pass_strength_script' );
		$output = '<div class="section-title text-center col-xs-12 margin-bottom-50"><h1>' . esc_html__( 'Sign Up To Your Account', 'wyzi-business-finder' ) . '</h1></div>'.WyzRegistrationForm_Frontend_Factory::registration_form( false );//wyz_registration_form_fields();
		return $output;
	} else {
		return esc_html__( 'You are already logged in', 'wyzi-business-finder' );
	}
}

function wyz_add_pass_strength_script() {
	wp_enqueue_script( 'wyz_pass_strength_js' );
	?>
	<script>
		var TandCText = "<?php esc_html_e( 'Please accept the terms and conditions before you proceed', 'wyzi-business-finder' );?>",
		TandCEnf = "<?php echo ( 'on' == get_option('wyz_terms_and_cond_checkbox') ) ? 'yes' : 'no';?>";
	</script>
	<?php
	wp_enqueue_script( 'wyz_signup_js', plugin_dir_url( __FILE__ ) . '/js/registration.js', array('jquery'), true );

	if ( isset( $_GET['justver'] ) )  {
		wp_localize_script( 'wyz_signup_js', 'regVar', array(
			'justVer' => 1,
			'emlCnf' => esc_html__( 'Your email was confirmed successfully.', 'wyzi-business-finder' )
		));
	}
}



/**
 * User login form.
 */
function wyz_login_form() {
	if ( ! is_user_logged_in() ) {
		$output = wyz_login_form_fields();
	} else {
		$output = WyzHelpers::wyz_warning( esc_html__( 'You are already logged in.', 'wyzi-business-finder' ),true);
	}
	return $output;
}

function wyz_reset_pass_form() {
	if ( ! is_user_logged_in() ) {
		$output = wyz_reset_pass_form_fields();
	} else {
		return WyzHelpers::wyz_warning( esc_html__( 'You are already logged in.', 'wyzi-business-finder' ),true);
	}
	return $output;
}

function wyz_reset_pass_form_fields() {
	ob_start();
	global $mail_success;
	global $template_type;
	wyz_show_error_messages();
	if ( '' != $mail_success ) {
		WyzHelpers::wyz_success( $mail_success );
	}
	if ( 2 == $template_type )
		$submit_button = '<button id="submitr" type="submit" class="action-btn btn-bg-blue btn-rounded">' . esc_html__( 'GET NEW PASSWORD', 'wyzi-business-finder' ) . '</button>';
	else
		$submit_button = '<button id="submitr" type="submit" class="wyz-button wyz-primary-color wyz-prim-color icon">' . esc_html__( 'GET NEW PASSWORD', 'wyzi-business-finder' ) . '<i class="fa fa-angle-right"></i></button>';?>
	<!-- <div class="section-title col-xs-12 margin-bottom-50">
		<h1><?php  esc_html_e( 'Reset Password', 'wyzi-business-finder' ); ?></h1>
	</div> -->
	<div class="login-form col-lg-6 col-md-7 col-xs-12 fix">
		<form id="wyz_registration_form" enctype="multipart/form-data" method="POST">
			<div class="input-two  mb-25">
				<div class="input-box">
					<label for="wyz_reset_Identifier"><?php esc_html_e( 'Username/E-mail:', 'wyzi-business-finder' ); ?></label>
					<input name="wyz_user_Identifier" id="wyz_user_Identifier" class="text-input" type="text"/>
				</div>
			</div>
			<input type="hidden" name="wyz_reset_pass_nonce" value="<?php echo esc_attr( wp_create_nonce( 'wyz-reset_pass-nonce' ) ); ?>"/>
			<?php echo $submit_button;?>
		</form>
	</div>
	<?php return ob_get_clean();
}

function wyz_extract_termsandconditions() {
	if ( ! function_exists( 'wyz_get_option' ) )
		return;

	$str = wyz_get_option( 'terms-and-conditions' );

	preg_match( '/\%.*\%/', $str, $matches);

	if ( ! empty( $matches ) )
		foreach ( $matches as $match ) {
			$str = str_replace( $match, '<a class="wy-link" target="_blank" href="' . esc_url( home_url( '/terms-and-conditions/' ) ) . '">' . substr( $match, 1, strlen( $match ) -2 ) . '</a>' , $str );
		}
	if ( 'on' == get_option('wyz_terms_and_cond_checkbox') )
		$str = '<input type="checkbox" name="terms-and-conditions-check" required="required"/>' . $str;
	echo $str;
}

/**
 * Login form fields.
 */
function wyz_login_form_fields() {
	ob_start();
	$reset_pass_query = WyzHelpers::add_clear_query_arg( array( 'reset-pass' => true ) );
	// Show any error messages after form submission.
	wyz_show_error_messages(); ?>
	
	<div class="section-title col-xs-12 margin-bottom-50">
		<h1><?php  esc_html_e( 'Sign In To Your Account', 'wyzi-business-finder' ); ?></h1>
	</div>
	<!-- col-lg-6 col-md-7-->
	<div class="login-form col-xs-12 fix">
		<?php do_action('wyz_before_login_form');?>
		<form id="wyz_registration_form" enctype="multipart/form-data" class="wyz-form" method="POST">
			<div class="input-two">
				<div class="input-box">
					<label for="wyz_user_login"><?php esc_html_e( 'Username/Email', 'wyzi-business-finder' ); ?></label>
					<input name="wyz_user_login" id="wyz_user_login" class="text-input" type="text"/>
				</div>
				<div class="input-box">
					<label for="wyz_user_pass_login"><?php esc_html_e( 'Password', 'wyzi-business-finder' ); ?></label>
					<input name="wyz_user_pass_login" id="wyz_user_pass_login" class="text-input" type="password"/>
				</div>
			</div>
			<input type="hidden" name="wyz_login_nonce" value="<?php echo esc_attr( wp_create_nonce( 'wyz-login-nonce' ) ); ?>"/>
			<div class="remember-forget-pass fix">
				<input type="checkbox" id="remember" name="remember-me" />
				<label for="remember"><?php esc_html_e( 'Remember me', 'wyzi-business-finder' ); ?></label>
				<a id="forgot-pass" class="wyz-primary-color-text wyz-prim-color-txt" href="<?php echo esc_url( $reset_pass_query );?>"> <?php esc_html_e( 'Forgot Password?', 'wyzi-business-finder' );?></a>
			</div>
			<?php do_action( 'wyz_after_login_form' );?>
			<button id="wyz_login_submit" type="submit" class="wyz-button wyz-primary-color wyz-prim-color icon"><?php esc_html_e( 'SIGN IN', 'wyzi-business-finder' ); ?> <i class="fa fa-angle-right"></i></button>

			<div class="social-login-container">
				<?php if ( '' != get_option( 'wyz_fb_app_id' ) && '' != get_option( 'wyz_fb_app_secret' ) ) {
					echo do_shortcode( '[fbl_login_button]' );
				}
				if ( '' != get_option( 'wyz_google_client_id' ) && '' != get_option( 'wyz_google_client_secret' ) && '' != get_option( 'wyz_google_developer_key' ) ){ ?>
				<a href="<?php echo wp_login_url() . '?loginGoogle=1&redirect=' . home_url( '/user-account/' );?>" class="wyz-button icon social-login google"><i class="fa fa-google"></i><?php esc_html_e( 'Sign In with google', 'wyzi-business-finder' );?></a>
				<?php }?>
			</div>

		</form>
	</div>
	<?php return ob_get_clean();
}


function wyz_login_registration_form() {
	add_action( 'wp_footer', 'wyz_add_pass_strength_script' );
	ob_start();

	$user_login = isset( $_POST['wyz_user_register'] ) ? $_POST['wyz_user_register'] : '';
	$user_email = isset( $_POST['wyz_user_email'] ) ? $_POST['wyz_user_email'] : '';
	$user_first = isset( $_POST['wyz_user_first'] ) ? $_POST['wyz_user_first'] : '';
	$user_last = isset( $_POST['wyz_user_last'] ) ? $_POST['wyz_user_last'] : '';
	$subscription = isset( $_POST['subscription'] ) ? $_POST['subscription'] : '';
	$reset_pass_query = WyzHelpers::add_clear_query_arg( array( 'reset-pass' => true ) );

	$login_active = ( isset( $_GET['action'] ) && 'login' == $_GET['action'] ? 'active' : '' );
	$register_active = ( '' == $login_active ? 'active' : '' );?>
	
	<?php wyz_show_error_messages(); ?>

	<!-- Sidebar Wrapper -->
	<!-- <div class="col-md-7 col-sm-8 col-md-offset-0 col-sm-offset-2 col-xs-12"> -->
		<div class="login-reg-forms">
			<!-- Login Register Tab List -->
			<ul class="login-reg-tab-list mb-50">
				<li class="<?php echo $login_active;?>"><a class="wyz-prim-color-txt-hover" href="#login-form" data-toggle="tab"><?php esc_html_e( 'login', 'wyzi-business-finder' );?></a></li>
				<li class="<?php echo $register_active;?>"><a class="wyz-prim-color-txt-hover" href="#reg-form" data-toggle="tab"><?php esc_html_e( 'register', 'wyzi-business-finder' );?></a></li>
			</ul>

			<!-- Login Register Tab Content -->
			<?php do_action('wyz_before_registration_form');?>
			<div class="login-reg-tab-content tab-content">
				<div class="tab-pane <?php echo $login_active;?>" id="login-form">
					<form action="#" class="login-reg-form" method="post">
						<div class="row">
							<div class="col-xs-12 mb-50">
								<label for="wyz_user_login"><?php esc_html_e( 'Username/Email', 'wyzi-business-finder' ); ?></label>
								<input id="wyz_user_login" name="wyz_user_login" type="text" required/>
							</div>
							<div class="col-xs-12 mb-25">
								<label for="wyz_user_pass_login"><?php esc_html_e( 'Password', 'wyzi-business-finder' ); ?></label>
								<input id="wyz_user_pass_login" name="wyz_user_pass_login" type="password" required/>
							</div>
							<input type="hidden" name="wyz_login_nonce" value="<?php echo esc_attr( wp_create_nonce( 'wyz-login-nonce' ) ); ?>"/>
							<div class="col-xs-12 mb-25">
								<input id="login-remember-pass" name="remember-me" type="checkbox">
								<label for="login-remember-pass"><?php esc_html_e( 'Remember me', 'wyzi-business-finder' ); ?></label>
								<a id="forgot-pass" href="<?php echo esc_url( $reset_pass_query );?>"><?php esc_html_e( 'Forgot Password?', 'wyzi-business-finder' );?></a>
							</div>
							<div class="col-xs-12">
								<button id="submit" type="submitr" class="action-btn btn-bg-blue wyz-prim-color btn-rounded"><?php esc_html_e( 'SIGN IN', 'wyzi-business-finder' ); ?></button>
								<?php wyz_this_get_login_social_options();?>
							</div>
						</div>
					</form>
				</div>
				<div class="tab-pane <?php echo $register_active;?>" id="reg-form">
					<?php WyzRegistrationForm_Frontend_Factory::registration_form();?>
				</div>
			</div>
		</div>
	<!-- </div> -->
<?php
}

function wyz_this_get_login_social_options() {
	$have_fb_login = ( '' != get_option( 'wyz_fb_app_id' ) && '' != get_option( 'wyz_fb_app_secret' ) );
	$have_google_login = ( '' != get_option( 'wyz_google_client_id' ) && '' != get_option( 'wyz_google_client_secret' ) && '' != get_option( 'wyz_google_developer_key' ) );
	
	if ( $have_fb_login || $have_google_login ) {?>
	<div class="social-login-cont">
		<h5 class="wyz-prim-color-txt"><?php esc_html_e( 'Other Login Options', 'wyzi-business-finder' );?></h5>
		<?php if ( $have_fb_login ) {
			echo do_shortcode( '[fbl_login_button]' );
		}if ( $have_google_login ){ ?>
		<a href="<?php echo wp_login_url() . '?loginGoogle=1&redirect=' . home_url( '/user-account/' );?>" class="social-login google"><i class="fa fa-google"></i></a>
		<?php }?>
	</div>
	<?php }
}

/**
 * Logs a member in after submitting a form.
 */
function wyz_login_member() {
	if ( isset( $_POST['wyz_user_login'] ) && wp_verify_nonce( $_POST['wyz_login_nonce'], 'wyz-login-nonce' ) ) {
		$user_input = wp_filter_nohtml_kses( $_POST['wyz_user_login'] );
		// This returns the user ID and other info from the user name.
		$user = get_user_by( 'login', $user_input );

		if ( ! $user || ! is_object( $user ) ) {
			 if ( filter_var( $user_input, FILTER_VALIDATE_EMAIL ) ) {
				$user = get_user_by( 'email', $user_input );
			}
			// If the user name doesn't exist.
			if ( ! $user || ! is_object( $user ) ) {
				wyz_errors()->add( 'no_user', esc_html__( 'User not found', 'wyzi-business-finder' ) );
				return;
			}
		}

		if ( ! isset( $_POST['wyz_user_pass_login'] ) || '' == $_POST['wyz_user_pass_login'] ) {
			// If no password was entered.
			wyz_errors()->add( 'empty_password', esc_html__( 'Please enter a password', 'wyzi-business-finder' ) );
		}

		// Check the user's login with their password.
		if ( ! wp_check_password( $_POST['wyz_user_pass_login'], $user->user_pass, $user->ID ) ) {
			// If the password is incorrect for the specified user.
			wyz_errors()->add( 'empty_password', esc_html__( 'Incorrect password', 'wyzi-business-finder' ) );
		}

		apply_filters( 'wyz_login_errors', wyz_errors() );

		// Retrieve all error messages.
		$errors = wyz_errors()->get_error_messages();

		// Only log the user in if there are no errors.
		if ( empty( $errors ) ) {
			if ( 'pending_verify' === get_user_meta( $user->ID, 'pending_email_verify', true ) ){
				wp_redirect( home_url() );
				exit;
			}
			$creds = array();
			$creds['user_login'] = wp_filter_nohtml_kses( $_POST['wyz_user_login'] );
			$creds['user_password'] = $_POST['wyz_user_pass_login'];
			$creds['remember'] = isset( $_POST['remember-me'] ) ? true : false;
			$user = wp_signon( $creds, is_ssl() );
			wp_redirect( apply_filters( 'wyz_after_login_redirect', home_url( '/user-account/' ), $user, false ) );
			exit;
		}
	}
}
add_action( 'init', 'wyz_login_member' );



/**
 * Logs a member in after submitting a form.
 */
function wyz_reset_user_pass() {
	if ( isset( $_POST['wyz_user_Identifier'] ) && wp_verify_nonce( $_POST['wyz_reset_pass_nonce'], 'wyz-reset_pass-nonce' ) ) {
		if ( is_user_logged_in() ) {
			wp_redirect( home_url() );
			exit;
		}
		$user_email = email_exists( $_POST['wyz_user_Identifier'] );
		$user_name = username_exists( $_POST['wyz_user_Identifier'] );
		$user_data = '';
		$user_login = '';
		$verified = false;
		if ( $user_email ) {
			$user_data = get_userdata( $user_email );
			$verified = true;
		} elseif ( $user_name ) {
			$user_data = get_userdata( $user_name );
			$verified = true;
		}
		if ( $verified ) {
			$user_email = $user_data->user_email;
			$user_name = $user_data->display_name;
			$user_login = $user_data->user_login;
			wyz_send_pass_email( $user_email, $user_name, $user_data->first_name, $user_data->last_name, $user_login );
		} else {
			wyz_errors()->add( 'username_email_invalid', esc_html__( 'Username/Email not found', 'wyzi-business-finder' ) );
		}

		wyz_override_errors( apply_filters( 'wyz_reset_user_pass_errors', wyz_errors() ) );

		// Retrieve all error messages.
		$errors = wyz_errors()->get_error_messages();
	}
}
add_action( 'wp', 'wyz_reset_user_pass' );


function wyz_send_pass_email( $email, $name, $first_name, $last_name, $login ) {
	global $wpdb;
	$key = $wpdb->get_var( $wpdb->prepare( "SELECT user_activation_key FROM $wpdb->users WHERE user_login = %s", $login ) );
	if ( empty( $key ) ) {
		// Generate reset key.
		$key = wp_generate_password( 20, false );
	}

	$to = $email;

	$subject = wyz_get_option( 'password-reset-subject' );
	if ( empty($subject))
		$subject = (esc_html__( 'You got a new Email from', 'wyzi-business-finder' ) . ' {' . home_url() . '}');
	$message = wyz_get_option( 'password-reset-mail' );
	if ( empty( $message ) ) {
		$message = "Someone requested that the password be reset for the following account:\r\n\r\n";
		$message .= "%SITE_NAME%\r\n\r\n";
		$message .= "Username: %USERNAME%\r\n\r\n";
		$message .= "If this was a mistake, just ignore this email and nothing will happen.\r\n\r\n";
		$message .= "To reset your password, visit the following address\r\n\r\n";
		$message .= "%LOGIN_LINK%\r\n";
	}

	$message = str_replace( '%USERNAME%', $name , $message );
	$message = str_replace( '%SITE_NAME%', get_bloginfo( 'name' ) , $message );
	$message = str_replace( '%FIRSTNAME%', $first_name , $message );
	$message = str_replace( '%LASTNAME%', $last_name , $message );

	$page_url = site_url( "signup/?action=reset_pwd&key=$key&login=" . rawurlencode( $login ) );
	$message = str_replace( '%LOGIN_LINK%', $page_url , $message );

	if( ! WyzHelpers::wyz_mail( $to, $subject, $message, 'forgot_password' ) ) {
		wyz_errors()->add( 'reset_pass_email_fail', esc_html__( 'Sending email failed', 'wyzi-business-finder' ) );
	} else {
		global $mail_success;
		$mail_success = esc_html__( 'We have just sent you an email with Password reset instructions', 'wyzi-business-finder' );
		$wpdb->update( $wpdb->users, array( 'user_activation_key' => $key ), array( 'user_login' => $login ) );
	}

	apply_filters( 'wyz_password_email_errors', wyz_errors() );

	wyz_errors()->get_error_messages();
}


/**
 * Register a new user.
 */
function wyz_add_new_member() {
	if ( isset( $_POST["wyz_user_register"] ) && wp_verify_nonce( $_POST['wyz_register_nonce'], 'wyz-register-nonce' ) ) {

		$data = get_option( 'wyz_registration_form_data', array() );
		$i=0;
		foreach ($data as $key => $value) {
			if ( $value['type'] == 'username' || $value['type'] == 'subscription' )
				unset( $data[ $i ] );
			$i++;
		}

		$defs = array( 'username', 'pemail', 'fname', 'lname', 'password', 'subscription' );

		$user_login = wp_filter_nohtml_kses( $_POST['wyz_user_register'] );
		$user_email = wp_filter_nohtml_kses( $_POST['wyz_user_email'] );
		$user_first = isset( $_POST['wyz_user_first'] ) ? wp_filter_nohtml_kses( $_POST['wyz_user_first'] ) : '';
		$user_last = isset( $_POST['wyz_user_last'] ) ? wp_filter_nohtml_kses( $_POST['wyz_user_last'] ) : '';
		$user_pass = $_POST['wyz_user_pass'];
		$pass_confirm = isset( $_POST['wyz_user_pass_confirm'] ) ? $_POST['wyz_user_pass_confirm'] : $_POST['wyz_user_pass'];
		$def_role = get_option( 'wyz_reg_def_user_role' );
		$subscription = ( 'client' != $def_role && 'business_owner' != $def_role ) ? ( isset( $_POST['subscription'] ) ? $_POST['subscription'] : 'client' ) : $def_role;

		if ( username_exists( $user_login ) ) {
			// Username already registered.
			wyz_errors()->add( 'username_unavailable', esc_html__( 'Username already taken', 'wyzi-business-finder' ) );
		}
		if ( ! validate_username( $user_login ) ) {
			// Invalid username.
			wyz_errors()->add( 'username_invalid', esc_html__( 'Invalid username', 'wyzi-business-finder' ) );
		}
		if ( '' === $user_login ) {
			// Empty username.
			wyz_errors()->add( 'username_empty', esc_html__( 'Please enter a username', 'wyzi-business-finder' ) );
		}
		if ( ! is_email( $user_email ) ) {
			// Invalid email.
			wyz_errors()->add( 'email_invalid', esc_html__( 'Invalid email', 'wyzi-business-finder' ) );
		}
		if ( email_exists( $user_email ) ) {
			// Email address already registered.
			wyz_errors()->add( 'email_used', esc_html__( 'Email already registered', 'wyzi-business-finder' ) );
		}
		if ( '' === $user_pass ) {
			// Passwords do not match.
			wyz_errors()->add( 'password_empty', esc_html__( 'Please enter a password', 'wyzi-business-finder' ) );
		}
		if ( $user_pass !== $pass_confirm ) {
			// Passwords do not match.
			wyz_errors()->add( 'password_mismatch', esc_html__( 'Passwords do not match', 'wyzi-business-finder' ) );
		}
		if ( empty( $subscription ) ) {
			// Empty subscription.
			wyz_errors()->add( 'no_subscription', esc_html__( 'Please choose your subscription', 'wyzi-business-finder' ) );
		}
		if ( 'on' == get_option('wyz_terms_and_cond_checkbox') && (!isset($_POST['terms-and-conditions-check'])||empty($_POST['terms-and-conditions-check'])) )
			wyz_errors()->add( 'no_terms_acceptance', esc_html__( 'Please Accept the terms and conditions to be able to register.', 'wyzi-business-finder' ) );

        if ( isset($_POST['g-recaptcha-response'] ) ) {

        	$captch_set = false;
        	$secret_key = '';
        	foreach ( $data as $key => $value ) {
				if ( $value['type'] == 'recaptcha' && isset( $value['recaptchaSiteKey'] ) && ! empty( $value['recaptchaSiteKey'] ) && isset( $value['recaptchaSecretKey'] ) && ! empty( $value['recaptchaSecretKey'] ) ) {
					$secret_key = $value['recaptchaSecretKey'];
					$captch_set = true;
					break;
				}
			}
			if ( $captch_set ) {
				$captcha = $_POST['g-recaptcha-response'];
				if ( ! $captcha ) {
					wyz_errors()->add( 'no_recaptcha', esc_html__( 'Recaptcha authentification failed', 'wyzi-business-finder' ) );
				}
				$response = json_decode( file_get_contents( "https://www.google.com/recaptcha/api/siteverify?secret=" . $secret_key . "&response=".$captcha."&remoteip=".$_SERVER['REMOTE_ADDR'] ), true);
				if ( $response['success'] == false ) {
					wyz_errors()->add( 'no_recaptcha', esc_html__( 'Recaptcha authentification failed', 'wyzi-business-finder' ) );
				}
			}

		}
		foreach ( $data as $key => $value ) {

			if ( ! in_array( $value['type'], $defs ) && ! empty( $value['required'] ) ) {
				if ( $value['type'] == 'file' ) {
					if ( ! isset( $_FILES['wyz_register_fields_' . $value['id'] ] ) ||
							empty( $_FILES['wyz_register_fields_' . $value['id'] ] ) ) 
						wyz_errors()->add( 'required_field', sprintf( esc_html__( '%s is required.', 'wyzi-business-finder' ), $value['label'] ) );
					elseif(wp_max_upload_size() < $_FILES['wyz_register_fields_' . $value['id'] ]['size'] ){
						wyz_errors()->add( 'max_upload_size_exceeded', sprintf( esc_html__( '%s exceeds the maximum upload size of %d KB.', 'wyzi-business-finder' ), $value['label'] ), wp_max_upload_size()/1000.0 );
					}
				} else{
					if ( ( ! isset( $_POST['wyz_register_fields_' . $value['id'] ] ) ||
							empty( $_POST['wyz_register_fields_' . $value['id'] ] ) 
						 ) && (
							! isset( $_POST[ $value['type'] ] ) ||
							empty( $_POST[ $value['type'] ] ) 
						) ){
						wyz_errors()->add( 'required_field', sprintf( esc_html__( '%s is required.', 'wyzi-business-finder' ), $value['label'] ) );
					}
				}
			}
		}

		apply_filters( 'wyz_register_user_errors', wyz_errors() );

		$errors = wyz_errors()->get_error_messages();

		$registration_data = apply_filters( 'wyz_user_registration_info', array(
			'user_login' => $user_login,
			'user_pass' => $user_pass,
			'user_email' => $user_email,
			'first_name' => $user_first,
			'last_name' => $user_last,
			'user_registered' => date( 'Y-m-d H:i:s' ),
			'role' => $subscription,
		));

		// Only create the user in if there are no errors.
		if ( empty( $errors ) ) {

			if ( 'on' == get_option( 'wyz_user_email_verification' ) ) {
				$role_to_be = $registration_data['role'];
				$registration_data['role'] = 'pending_user';
				$new_user_id = wp_insert_user( $registration_data );

				if ( $new_user_id ) {
					// Send an email to the admin alerting them of the registration.
					wp_new_user_notification( $new_user_id );

					// Give the user initial points value of zero.
					update_user_meta( $new_user_id, 'points_available', 0 );
					update_user_meta( $new_user_id, 'has_business', false );
					$user_businesses = array(
						'pending' => array(),
						'published' => array(),
					);
					update_user_meta( $new_user_id, 'wyz_user_businesses', $user_businesses );
					update_user_meta( $new_user_id, 'wyz_user_businesses_count', 0 );

					$woo_attrs = array('billing_company','billing_country','billing_address_1','billing_address_2',
										'billing_city','billing_state','billing_phone','billing_postcode');

					foreach ( $data as $key => $value ) {
						if ( in_array($value['type'], $woo_attrs) && isset( $_POST[ $value['type'] ] ) ) {
							update_user_meta( $new_user_id, $value['type'], $_POST[ $value['type'] ] );
						}
						elseif ( ! in_array( $value['type'], $defs ) && isset( $_POST['wyz_register_fields_' . $value['id'] ] ) )
							update_user_meta( $new_user_id, 'wyz_register_fields_' . $value['id'], $_POST['wyz_register_fields_' . $value['id'] ] );
					}

					do_action( 'wyz_after_user_register', $new_user_id );
					update_user_meta( $new_user_id, 'wyz_user_role', $role_to_be );
					
					update_user_meta( $new_user_id, 'pending_email_verify', 'pending_verify' );
					WyzHelpers::send_user_verify_email( $user_email, $new_user_id );
					wp_redirect( get_the_permalink() . '?email-verify' );
					exit;
				}

			} else {

				$new_user_id = wp_insert_user( $registration_data );

				if ( $new_user_id ) {
					// Send an email to the admin alerting them of the registration.
					wp_new_user_notification( $new_user_id );

					// Give the user initial points value of zero.
					update_user_meta( $new_user_id, 'points_available', 0 );
					update_user_meta( $new_user_id, 'has_business', false );
					$user_businesses = array(
						'pending' => array(),
						'published' => array(),
					);
					update_user_meta( $new_user_id, 'wyz_user_businesses', $user_businesses );
					update_user_meta( $new_user_id, 'wyz_user_businesses_count', 0 );

					$woo_attrs = array('billing_company','billing_country','billing_address_1','billing_address_2',
										'billing_city','billing_state','billing_phone','billing_postcode');

					foreach ( $data as $key => $value ) {
						if ( in_array($value['type'], $woo_attrs) && isset( $_POST[ $value['type'] ] ) ) {
							update_user_meta( $new_user_id, $value['type'], $_POST[ $value['type'] ] );
						} elseif( $value['type'] == 'file' ) {
							if ( ! empty( $_FILES['wyz_register_fields_' . $value['id']]['type'] ) ) {
								$attachment_file = $_FILES['wyz_register_fields_' . $value['id']];
								$attachment_type = current( (array) explode( '/', $attachment_file['type'] ) );
								if ( false === in_array( $attachment_file['type'], WyzHelpers::$allowed_mimes ) )
								{
									//wp_send_json_error( __( 'Unsupported file format.', 'wyzi-business-finder' ) );
									continue;
								}
								if ( ! empty( $attachment_file ) ) {
									$upload_overrides = array( 'test_form' => false );

									require_once(ABSPATH . "wp-admin" . '/includes/image.php');
									require_once(ABSPATH . "wp-admin" . '/includes/file.php');
									require_once(ABSPATH . "wp-admin" . '/includes/media.php');

									$movefile = wp_handle_upload( $attachment_file, $upload_overrides );

									if ( ! $movefile || isset( $movefile['error'] ) ) {
										continue;
										//wp_send_json_error( $movefile['error'] );
									}

									$filename = $movefile['file'];

									$attachment_data = array(
										'post_mime_type' => $movefile['type'],
										'guid'           => $movefile['url'],
										'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),
										'post_content'   => ''
									);

									$attachment_id = wp_insert_attachment( $attachment_data, $filename );
									if ( is_wp_error( $attachment_id ) )
									{
										@unlink( $movefile['file'] );
										continue;
									}

									$metadata = wp_generate_attachment_metadata( $attachment_id, $filename );

									wp_update_attachment_metadata( $attachment_id, $metadata );
									update_user_meta( $new_user_id, 'wyz_register_fields_' . $value['id'], $attachment_id );
								}

							}
						}
						elseif ( ! in_array( $value['type'], $defs ) && isset( $_POST['wyz_register_fields_' . $value['id'] ] ) )
							update_user_meta( $new_user_id, 'wyz_register_fields_' . $value['id'], $_POST['wyz_register_fields_' . $value['id'] ] );
					}

					do_action( 'wyz_after_user_register', $new_user_id );

					
					$creds = array();
					$creds['user_login'] = $registration_data['user_login'];
					$creds['user_password'] = $registration_data['user_pass'];
					$creds['remember'] = true;
					$user = wp_signon( $creds, is_ssl() );

					$url = apply_filters( 'wyz_after_login_redirect', home_url( '/user-account/' ), $user, true );
					$url = apply_filters( 'wyz_after_register_redirect', $url, $user );
					// Send the newly created user to the user account page after logging him in.
					wp_redirect( $url );
					exit;
				}
			}
		}
	}
}
add_action( 'init', 'wyz_add_new_member' );


function wyz_email_verification_check(){
    
    if (strpos($_SERVER['REQUEST_URI'], '/email_verify') === false || ! isset( $_GET['token'] ) ) return;

	$token_data = get_option( $_GET['token'] );

	if ( ! $token_data ) wp_die('Invalid or expired token');

	$user = get_user_by( 'id', $token_data );
	if ( ! $user ) wp_die('Invalid or expired token');
	delete_option( $_GET['token'] );
	$user->set_role( '' );
	$user->set_role( get_user_meta( $token_data, 'wyz_user_role', true ) );
	
	update_user_meta( $token_data, 'pending_email_verify', 'verified' );
	delete_user_meta( $token_data, 'wyz_user_role' );
	wp_set_current_user( $token_data );
	wyz_user_greeting_mail($token_data);
	do_action( 'wyz_after_user_register', $token_data );
	wp_redirect(home_url('/signup/?action=login&justver=1'));
	exit();

}
add_action('init', 'wyz_email_verification_check');

function wyz_email_verification_message() {
	return WyzHelpers::wyz_info( esc_html__( 'A message has been sent to your email address, please follow the link provided to complete your registration.', 'wyzi-business-finder' ),true);
}
/**
 * Used for tracking error messages.
 */
function wyz_errors() {
	static $wp_error;
	// Will hold global variable safely.
	return isset( $wp_error ) ? $wp_error :( $wp_error = new WP_Error( null, null, null ) );
}

function wyz_override_errors($errors) {
	static $wp_error;
	$wp_error = $errors;
}

/**
 * Displays error messages from form submissions.
 */
function wyz_show_error_messages() {
	if ( $codes = wyz_errors()->get_error_codes() ) {
		$msgs = '';
		// Loop error codes and display errors.
		foreach ( $codes as $code ) {
			$message = wyz_errors()->get_error_message( $code );
			$msgs .=  '<p>' . esc_html__( 'Error', 'wyzi-business-finder' ) . '</strong>: ' . esc_html( $message ) . '</p>';
		}
		WyzHelpers::wyz_error( $msgs );
	}
}



function wyz_map_meta_cap( $caps, $cap, $user_id, $args ) {
	if ( empty( $args ) ) return $caps;
	/* If editing, deleting, or reading an offer, get the post and post type object. */
	if ( 'edit_offer' == $cap || 'delete_offer' == $cap || 'read_offer' == $cap ) {
		$post = get_post( $args[0] );
		$post_type = get_post_type_object( $post->post_type );

		/* Set an empty array for the capabilities. */
		$caps = array();
	}

	/* If editing an offer, assign the required capability. */
	if ( 'edit_offer' == $cap ) {

		if ( $user_id == $post->post_author ) {
			$caps[] = $post_type->cap->edit_posts;
		} else {
			$caps[] = $post_type->cap->edit_others_posts;
		}
	} elseif ( 'delete_offer' == $cap ) { /* If deleting an offer, assign the required capability. */
		if ( $user_id == $post->post_author ) {
			$caps[] = $post_type->cap->delete_posts;
		} else {
			$caps[] = $post_type->cap->delete_others_posts;
		}
	} elseif ( 'read_offer' == $cap ) { /* If reading a private offer, assign the required capability. */
		if ( 'private' != $post->post_status ) {
			$caps[] = 'read';
		} elseif ( $user_id == $post->post_author ) {
			$caps[] = 'read';
		} else {
			$caps[] = $post_type->cap->read_private_posts;
		}
	}

	/* Return the capabilities required by the user. */
	return $caps;
}
add_filter( 'map_meta_cap', 'wyz_map_meta_cap', 10, 4 );
