<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://wp.timersys.com
 * @since      1.0.0
 *
 * @package    Facebook_Login
 * @subpackage Facebook_Login/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Facebook_Login
 * @subpackage Facebook_Login/public
 * @author     Damian Logghe <info@timersys.com>
 */
class WYZFacebook_Login_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->opts         = get_option('fbl_settings');
	}


	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/facebook-login.js', array( 'jquery' ), $this->version, false );
		wp_localize_script( $this->plugin_name, 'fbl', apply_filters( 'fbl/js_vars', array(
			'ajaxurl'      => admin_url('admin-ajax.php'),
			'site_url'     => home_url(),
			'scopes'       => 'email,public_profile',
			'appId'        => get_option( 'wyz_fb_app_id' ),
			'l18n'         => array(
				'chrome_ios_alert'      => __( 'Please login into facebook and then click connect button again', 'fbl' ),
			)
		)));
	}

	/**
	 * Print the button on login page
	 * @since   1.0.0
	 */
	public function print_button() {
		$redirect = ! empty( $_GET['redirect_to'] ) ? esc_url($_GET['redirect_to']) : home_url( '/user-account/' );

		// if we are in login page we don't want to redirect back to it
		if ( isset( $GLOBALS['pagenow'] ) && in_array( $GLOBALS['pagenow'], array( 'wp-login.php', 'wp-register.php' ) ) && empty($_GET['redirect_to']) )
			$redirect = home_url( '/user-account/' );

		$template_type = 1;
		if ( function_exists( 'wyz_get_theme_template' ) ) $template_type = wyz_get_theme_template();

		switch ( $template_type) {
			case 1:
				echo apply_filters('fbl/login_button', '<a href="#" class="wyz-button icon social-login facebook  js-fbl" data-redirect="'.apply_filters( 'flp/redirect_url', $redirect).'" data-fb_nonce="' . wp_create_nonce( 'facebook-nonce' ).'"><i class="fa fa-facebook"></i>' . esc_html__( 'Sign In with facebook', 'wyzi-business-finder' ) . '</a>');
				break;
			case 2:
				echo apply_filters('fbl/login_button', '<a href="#" class="social-login facebook  js-fbl" data-redirect="'.apply_filters( 'flp/redirect_url', $redirect).'" data-fb_nonce="' . wp_create_nonce( 'facebook-nonce' ).'"><i class="fa fa-facebook"></i></a>');
				break;
		}
	}

	/**
	 * Prints fb script in login head
	 * @since   1.0.0
	 */
	public function add_fb_scripts(){
		?>
		<script>

			window.fbAsyncInit = function() {
				FB.init({
					appId      : '<?php echo trim( get_option( 'wyz_fb_app_id' ) );?>',
					cookie     : true,  // enable cookies to allow the server to access
					xfbml      : true,  // parse social plugins on this page
					version    : 'v2.2' // use version 2.2
				});

			};

			// Load the SDK asynchronously
			(function(d, s, id) {
				var js, fjs = d.getElementsByTagName(s)[0];
				if (d.getElementById(id)) return;
				js = d.createElement(s); js.id = id;
				js.src = "//connect.facebook.net/en_US/sdk.js";
				fjs.parentNode.insertBefore(js, fjs);
			}(document, 'script', 'facebook-jssdk'));

		</script><?php

	}

	/**
	 * Main function that handles user login/ registration
	 */
	public function login_or_register_user() {
		check_ajax_referer( 'facebook-nonce', 'security' );

		$access_token = isset( $_POST['fb_response']['authResponse']['accessToken'] ) ? $_POST['fb_response']['authResponse']['accessToken'] : '';

		// Get user from Facebook with given access token
		$fb_url = add_query_arg(
			apply_filters( 'fbl/js_auth_data',
				array(
					'fields'            =>  'id,name,email,first_name,last_name',
					'access_token'      =>  $access_token,
				)
			),
			'https://graph.facebook.com/v2.4/'.$_POST['fb_response']['authResponse']['userID']
		);
		//
		if( '' != get_option( 'wyz_fb_app_secret' ) ) {
			$appsecret_proof = hash_hmac('sha256', $access_token, trim( get_option( 'wyz_fb_app_secret' ) ) );
			$fb_url = add_query_arg(
				array(
					'appsecret_proof' => $appsecret_proof
				),
				$fb_url
			);
		}

		$fb_response = wp_remote_get( esc_url_raw( $fb_url ), array( 'timeout' => 30 ) );

		if( is_wp_error( $fb_response ) )
			$this->ajax_response( array( 'error' => $fb_response->get_error_message() ) );

		$fb_user = apply_filters( 'fbl/auth_data',json_decode( wp_remote_retrieve_body( $fb_response ), true ) );

		if( isset( $fb_user['error'] ) )
			$this->ajax_response( array( 'error' => 'Error code: '. $fb_user['error']['code'] . ' - ' . $fb_user['error']['message'] ) );

		//check if user at least provided email
		if( empty( $fb_user['email'] ) )
			$this->ajax_response( array( 'error' => __('We need your email in order to continue. Please try loging again. ', 'fbl' ) ) );

		// Map our FB response fields to the correct user fields as found in wp_update_user
		$user = apply_filters( 'fbl/user_data_login', array(
			'fb_user_id' => $fb_user['id'],
			'first_name' => $fb_user['first_name'],
			'last_name'  => $fb_user['last_name'],
			'user_email' => $fb_user['email'],
			//'user_url'   => $fb_user['link'],
			'user_pass'  => wp_generate_password(),
		));

		do_action( 'fbl/before_login', $user);

		$status = array( 'error' => __( 'Invalid User', 'fbl' ) );

		if ( empty( $user['fb_user_id'] ) )
			$this->ajax_response( $status );

		$user_obj = $this->getUserBy( $user );

		$meta_updated = false;

		if ( $user_obj ){
			$user_id = $user_obj->ID;
			$status = array( 'success' => $user_id, 'method' => 'login');
			// check if user email exist or update accordingly
			if( empty( $user_obj->user_email ) )
				wp_update_user( array( 'ID' => $user_id, 'user_email' => $user['user_email'] ) );

		} else {
			if( ! get_option('users_can_register') && apply_filters( 'fbl/registration_disabled', true ) )
				$this->ajax_response( array( 'error' => __( 'User registration is disabled', 'fbl' ) ) );
			// generate a new username
			$user['user_login'] = apply_filters( 'fbl/generateUsername', $this->generateUsername( $fb_user ) );

			$user_id = $this->register_user( apply_filters( 'fbl/user_data_register',$user ) );
			if( !is_wp_error( $user_id ) ) {
				$this->notify_new_registration( $user_id );
				update_user_meta( $user_id, '_fb_user_id', $user['fb_user_id'] );
				update_user_meta( $user_id, 'points_available', 0 );
				update_user_meta( $user_id, 'has_business', false );
				update_user_meta( $user_id, 'wyz_user_businesses_count', 0 );
				update_user_meta( $user_id, 'wyz_user_businesses', array('pending'=>array(),'published'=>array()) );
				$meta_updated = true;
				$user_role = 'client';
            	if ( 'business_owner' == get_option( 'wyz_reg_def_user_role' ) ) $user_role = 'business_owner';
				wp_update_user( array( 'ID' => $user_id,'role' => $user_role, ) );
				$status = array( 'success' => $user_id, 'method' => 'registration' );
				do_action( 'wyz_after_user_register', $user_id );
			}
		}
		if( is_numeric( $user_id ) ) {
			wp_set_auth_cookie( $user_id, true );
			if( !$meta_updated )
				update_user_meta( $user_id, '_fb_user_id', $user['fb_user_id'] );
			do_action( 'fbl/after_login', $user, $user_id);
		}
		$this->ajax_response( apply_filters( 'fbl/success_status', $status ) );
	}

	/**
	 * Register new user
	 * @param $user Array of user values captured in fb
	 *
	 * @return int user id
	 */
	private function register_user( $user ) {
		do_action( 'fbl/register_user', $user );
		return wp_insert_user( $user );
	}

	/**
	 * Filters an avatar URL wrapped in an <img> element.
	 *
	 * @since BuddyPress (1.1.0)
	 *
	 * @param $img constructed img
	 * @param array $params Array of parameters for the request.
	 * @param $item_id
	 * @param $avatar_dir
	 * @param string $html_css_id ID attribute for avatar.
	 * @param string $html_width Width attribute for avatar.
	 * @param string $html_height Height attribtue for avatar.
	 * @param string $avatar_folder_url Avatar URL path.
	 * @param string $avatar_folder_dir Avatar dir path.
	 *
	 * @return string
	 */
	public function bp_core_fetch_avatar( $img, $params, $item_id, $avatar_dir, $html_css_id, $html_width, $html_height, $avatar_folder_url, $avatar_folder_dir ) {

		// if not a facebook user return default img otherwise calculate it
		$fb_id = get_user_meta( $params['item_id'], '_fb_user_id', true );
		if ( empty( $fb_id ) )
			return $img;

		preg_match( '@src="([^"]+)"@' , $img, $match );
		$src = array_pop( $match );

		$avatar_url = $this->bp_core_fetch_avatar_url( $src , $params, $img );

		if( empty( $avatar_url ) )
			return $img;

		// Get a fallback for the 'alt' parameter, create html output.
		if ( empty( $params['alt'] ) ) {
			$params['alt'] = __( 'Profile Photo', 'buddypress' );
		}
		$html_alt = ' alt="' . esc_attr( $params['alt'] ) . '"';

		// Filter image title and create html string.
		$html_title = '';

		/**
		 * Filters the title attribute value to be applied to avatar.
		 *
		 * @since BuddyPress (1.5.0)
		 *
		 * @param string $value  Title to be applied to avatar.
		 * @param string $value  ID of avatar item being requested.
		 * @param string $value  Avatar type being requested.
		 * @param array  $params Array of parameters for the request.
		 */
		$params['title'] = apply_filters( 'bp_core_avatar_title', $params['title'], $params['item_id'], $params['object'], $params );

		if ( ! empty( $params['title'] ) ) {
			$html_title = ' title="' . esc_attr( $params['title'] ) . '"';
		}

		// Use an alias to leave the param unchanged
		$avatar_classes = $params['class'];
		if ( ! is_array( $avatar_classes ) ) {
			$avatar_classes = explode( ' ', $avatar_classes );
		}

		// merge classes
		$avatar_classes = array_merge( $avatar_classes, array(
			$params['object'] . '-' . $params['item_id'] . '-avatar',
			'avatar-' . $params['width'],
		) );

		// Sanitize each class
		$avatar_classes = array_map( 'sanitize_html_class', $avatar_classes );

		// populate the class attribute
		$html_class = ' class="' . join( ' ', $avatar_classes ) . ' photo"';

		return '<img src="' . $avatar_url . '"' . $html_class . $html_css_id  . $html_width . $html_height . $html_alt . $html_title . ' />';
	}

	/**
	 * Filters a locally uploaded avatar URL.
	 *
	 * @since BuddyPress (1.2.5)
	 *
	 * @param string $avatar_url URL for a locally uploaded avatar.
	 * @param array $params Array of parameters for the request.
	 *
	 * @return string|void
	 */
	public function bp_core_fetch_avatar_url( $avatar_url, $params ) {

		$bp = buddypress();

		// If avatars are disabled for the root site, obey that request and bail
		if ( ! $bp->avatar->show_avatars ) {
			return;
		}

		// only for users
		if( $params['object'] != 'user' )
			return $avatar_url;

		$fb_id = get_user_meta( $params['item_id'], '_fb_user_id', true );

		if ( empty($fb_id) )
			return $avatar_url;

		// If is not gravatar it's local. And if it's local but the not the default one it means it's one uploaded by user
		// so we show that one.
		if( ! empty( $avatar_url ) ) {

			$gravatar = apply_filters( 'bp_gravatar_url', '//www.gravatar.com/avatar/' );

			if ( strpos( $avatar_url, $gravatar) === false && $avatar_url != bp_core_avatar_default( 'local' ) ) {
				return $avatar_url;
			}
		}

		return 'https://graph.facebook.com/' . $fb_id . '/picture?width=' . $params['width'] . '&height=' . $params['height'];
	}
	/**
	 * Function to send ajax response in script
	 * @param $status
	 */
	private function ajax_response( $status ) {
		wp_send_json( $status );
		die();
	}

	/**
	 * Try to retrieve an user by email or username
	 *
	 * @param $user array of username and pass
	 *
	 * @return false|WP_User
	 */
	private function getUserBy( $user ) {

		// if the user is logged in, pass curent user
		if( is_user_logged_in() )
			return wp_get_current_user();

		$user_data = get_user_by('email', $user['user_email']);

		if( ! $user_data ) {
			$users     = get_users(
				array(
					'meta_key'    => '_fb_user_id',
					'meta_value'  => $user['fb_user_id'],
					'number'      => 1,
					'count_total' => false
				)
			);
			if( is_array( $users ) )
				$user_data = reset( $users );
		}
		return $user_data;
	}

	/**
	 * Generated a friendly username for facebook users
	 * @param $user
	 *
	 * @return string
	 */
	private function generateUsername( $user ) {
		global $wpdb;

		do_action( 'fbl/generateUsername', $user );

		if( !empty( $user['first_name'] ) && !empty( $user['last_name'] ) )
			$username = $this->cleanUsername( trim( $user['first_name'] ) .'-'. trim( $user['last_name'] ) );

		if( ! validate_username( $username ) ) {
			$username = '';
			// use email
			$email    = explode( '@', $user['email'] );
			if( validate_username( $email[0] ) )
				$username = $this->cleanUsername( $email[0] );
		}

		// User name can't be on the blacklist or empty
		$illegal_names = get_site_option( 'illegal_names' );
		if ( empty( $username ) || in_array( $username, (array) $illegal_names ) ) {
			// we used all our options to generate a nice username. Use id instead
			$username = 'fbl_' . $user['id'];
		}

		// "generate" unique suffix
		$suffix = $wpdb->get_var( $wpdb->prepare(
			"SELECT 1 + SUBSTR(user_login, %d) FROM $wpdb->users WHERE user_login REGEXP %s ORDER BY 1 DESC LIMIT 1",
			strlen( $username ) + 2, '^' . $username . '(-[0-9]+)?$' ) );

		if( !empty( $suffix ) ) {
			$username .= "-{$suffix}";
		}
		return apply_filters( 'fbl/generateUsername', $username );
	}

	/**
	 * Simple pass sanitazing functions to a given string
	 * @param $username
	 *
	 * @return string
	 */
	private function cleanUsername( $username ) {
		return sanitize_title( str_replace('_','-', sanitize_user(  $username  ) ) );
	}

	/**
	 * Send notifications to admin and bp if active
	 * @param $user_id
	 */
	private function notify_new_registration( $user_id ) {
		// Notify the site admin of a new user registration.
		wp_new_user_notification( $user_id,'','admin' );
		// notify the user
		wp_new_user_notification( $user_id,'','user' );
		do_action( 'fbl/notify_new_registration', $user_id );
		// bp notifications
		// fires xprofile_sync_wp_profile, bp_core_new_user_activity, bp_core_clear_member_count_caches
		do_action( 'bp_core_activated_user', $user_id );
	}
}
