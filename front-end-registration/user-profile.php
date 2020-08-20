<?php
/**
 * Template : User profile
 *
 * @package wyz
 */

$update_error = array();
function wyz_user_profile_form_display() {
	$user_id = get_current_user_id();
	global $WYZ_USER_ACCOUNT_TYPE;
	global $template_type;
	ob_start(); ?>

	<div class="col-xs-12" id="post-<?php echo esc_attr( get_the_ID() ); ?>">

	<?php if ( ! is_user_logged_in() ) { ?>

			<p class="warning">
			    <?php esc_html_e( 'You must be logged in to edit your profile.', 'wyzi-business-finder' ); ?>
			</p>

	<?php } elseif ( isset( $_POST['upgrade-role-yes'] ) && 1 == $_POST['upgrade-role-yes'] ) {

			$u = new WP_User( $user_id );
			$u->remove_role( 'client' );
			$u->add_role( 'business_owner' );
			WyzHelpers::add_extra_points( $user_id );
			if( 'on' == get_option( 'wyz_auto_vendorship' ) && 'on' == get_option( 'wyz_can_become_vendor' ) && user_can( $user_id,'publish_businesses') && 'on' != get_option( 'wyz_sub_mode_on_off' ) ) {
				$user = new WP_User( $user_id );
				WyzHelpers::make_user_vendor( $user );
			}	
			WyzHelpers::wyz_success( esc_html__( 'Profile Updated', 'wyzi-business-finder' ) );
			echo '<script type="text/javascript">//<![CDATA[ 
			setTimeout(function(){ window.location = ' . wp_json_encode( get_permalink() ) . '; }, 2500); //]]></script>';

	}elseif ( isset( $_POST['upgrade-role'] ) && 1 == $_POST['upgrade-role'] && 'off' != get_option('wyz-user-can-upgrade-account') ) { ?>

		<div class="section-title text-center margin-bottom-50">
			<h1><?php esc_html_e( 'Upgrade Account', 'wyzi-business-finder' );?></h1>
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
		<div class="section-title text-center margin-bottom-50">
			<h1><?php esc_html_e( 'Buy Points', 'wyzi-business-finder' );?></h1>
		</div>
		<?php 

		echo do_shortcode('[product_category category="points-category"]');
		
	} else {
		global $update_error;
		if ( isset( $update_error ) && count( $update_error ) > 0 ) {
			WyzHelpers::wyz_error( implode( '', $update_error ) );
		}?>
		<div class="section-title text-center margin-bottom-50">
			<h1><?php esc_html_e( 'profile', 'wyzi-business-finder' );?></h1>
		</div>
		
		<?php
		do_action('wyz_before_profile_form');
		switch( $template_type ) {
			case 1:
			profile_form_1( $user_id );
			break;
			case 2:
			profile_form_2( $user_id );
			break;
		}?>
		
		
		<?php if ( ! current_user_can( 'publish_businesses' ) && 'off' != get_option('wyz-user-can-upgrade-account') ) { ?> 

		<form name="upgrade-role" id="upgrade-role" method="POST">
			<input type="hidden" name="upgrade-role" value="1" />
			<button class="wyz-button wyz-primary-color wyz-prim-color icon" type="submit"><?php esc_html_e( 'Upgrade Account to Business Owner', 'wyzi-business-finder' );?> <i class="fa fa-angle-right"></i></button>
		</form>

		<?php }
		do_action('wyz_after_user_role_upgrade_form', $user_id);
	}?>
	</div>

	<?php return ob_get_clean();
}

function profile_form_1( $user_id ) {
	?>
	<div class="profile-form text-center col-lg-6 col-lg-offset-3 col-md-8 col-md-offset-2 col-xs-12">
		<?php WyzRegistrationForm_Frontend_Factory::profile_form();?>
	</div>
	<?php
}

function profile_form_2($user_id ) {
	?>
	<div class="profile-form text-center col-lg-6 col-lg-offset-3 col-md-8 col-md-offset-2 col-xs-12">
		<div class="row">
		<?php WyzRegistrationForm_Frontend_Factory::profile_form();?>
		</div>
	</div>
	<?php
}

function wyz_transfer_points_form() {
	global $transfer_errors;
	?>
	<div class="section-title text-center margin-bottom-50">
		<h1><?php esc_html_e( 'Transfer Points', 'wyzi-business-finder' );?></h1>
	</div>

	<div class="profile-form text-center col-lg-6 col-lg-offset-3 col-md-8 col-md-offset-2 col-xs-12">
		<?php if ( ! empty( $transfer_errors) ) {
			foreach ($transfer_errors as $error) {
				echo $error;
			}
		}?>
		<form method="post">
			
			<div class="input-box">
				<div class="input-box">
					<label for="username-email"><?php esc_html_e( 'email/username of the user you want to transfer points to', 'wyzi-business-finder' ); ?></label>
					<input name="username-email" required="required" type="text" id="username-email" value="<?php echo esc_attr( isset( $_POST['username-email'] ) ? $_POST['username-email'] : '' ); ?>" />
				</div>
				<div class="input-box">
					<label for="points"><?php esc_html_e( 'amount of points you want to transfer', 'wyzi-business-finder' ); ?></label>
					<input name="points" required="required" type="number" id="transfer-points" value="<?php echo esc_attr( isset( $_POST['points'] ) ? $_POST['points'] : '' ); ?>" />
				</div>
				<div class="input-box">
					<label for="trans-note"><?php esc_html_e( 'transaction note', 'wyzi-business-finder' ); ?></label>
					<textarea name="trans-note" id="trans-note"><?php echo esc_attr( isset( $_POST['trans-note'] ) ? $_POST['trans-note'] : '' ); ?></textarea>
					<?php $points_fee = intval( get_option( 'wyz_points_transfer_fee', 0 ) );
					if ( $points_fee > 0 ) {
					WyzHelpers::wyz_info( esc_html__( 'Points transfer costs' ) . '<b id="points-fee">' . $points_fee . '</b> ' . esc_html__( 'points', 'wyzi-business-finder' ) . '<br/><span id="amount-notif"></span>' );
					 }?>
				</div>
			</div>
			
			<?php wp_nonce_field( 'points_form_nonce', 'points_nonce' ); ?>
			<input name="action" type="hidden" id="action" value="points-transfer" />
			<button id="wyz-update-user" type="submit" class="wyz-button wyz-secondary-color wyz-prim-color icon"><?php echo esc_html__( 'transfer', 'wyzi-business-finder' ); ?> <i class="fa fa-angle-right"></i></button>
		</form>
	</div>
	<?php
}

/**
 * Check user input and update profile.
 */
function wyz_check_user_update() {
	global $update_error;
	$update_error = array();
	$user_id = get_current_user_id();
	if ( 'POST' == $_SERVER['REQUEST_METHOD'] && ! empty( $_POST['wyz-updateuser'] ) ) {
		//Update user password.

		// Update user information.
		if ( ! empty( $_POST['wyz_user_email'] ) ) {
			$ex = email_exists( $_POST['wyz_user_email'] );
			if ( ! is_email( esc_attr( $_POST['wyz_user_email'] ) ) ) {
				$update_error[] = '<p>' . esc_html__( 'The Email you entered is not valid.', 'wyzi-business-finder' ) . '</p>';
			} elseif ( $ex && $ex != $user_id ) {
				$update_error[] = '<p>' . esc_html__( 'This email is already in use.', 'wyzi-business-finder' ) . '</p>';
			} else {
				$data = get_option( 'wyz_registration_form_data', array() );
				$defs = array( 'username', 'pemail', 'fname', 'lname', 'password', 'subscription' );

				foreach ( $data as $key => $value ) {
					if ( ! in_array( $value['type'], $defs ) && ! empty( $value['required'] ) && ( ( ! isset( $_POST['wyz_register_fields_' . $value['id'] ] ) || empty( $_POST['wyz_register_fields_' . $value['id'] ] ) ) ) && ( ! isset( $_POST[ $value['type'] ] ) || empty( $_POST[ $value['type'] ] ) ) )
						$update_error[] = '<p>' . sprintf( esc_html__( '%s is required.', 'wyzi-business-finder' ), $value['label'] ) . '</p>';
				}

			}


			/*if ( ! isset( $_POST['first-name'] ) || empty( $_POST['first-name'] ) ) {
				$update_error[] = '<p>' . esc_html__( 'Please enter your first name.', 'wyzi-business-finder' ) . '</p>';
			} elseif ( !isset( $_POST['last-name'] ) || empty( $_POST['last-name'] ) ) {
				$update_error[] = '<p>' . esc_html__( 'Please enter your last name.', 'wyzi-business-finder' ) . '</p>';
			}*/
		} 
		else {
			$update_error[] = '<p>' . esc_html__( 'Email is required.', 'wyzi-business-finder' ) . '</p>';
		} 

		if ( '' != $_POST['wyz_user_pass'] ) {
			if ( isset( $_POST['wyz_user_pass_confirm'] ) && $_POST['wyz_user_pass'] != $_POST['wyz_user_pass_confirm'] ) {
				$update_error[] = '<p>' . esc_html__( 'The passwords you entered do not match.', 'wyzi-business-finder' ) . '</p>';
			} else {
				wp_update_user( array( 'ID' => $user_id, 'user_pass' => $_POST['wyz_user_pass'] ) );
			}
		}

		// Redirect so the page will show updated info.
		if ( count( $update_error ) == 0 ) {

			if ( ! isset( $_POST['wyz_user_first'] ) )
				$_POST['wyz_user_first'] = '';

			if ( ! isset( $_POST['wyz_user_last'] ) )
				$_POST['wyz_user_last'] = '';

			$user = wp_get_current_user();
			$roles = $user->roles;
			wp_update_user( array(
				'ID' => $user_id,
				'first_name' => wp_filter_nohtml_kses( $_POST['wyz_user_first'] ),
				'last_name' => wp_filter_nohtml_kses( $_POST['wyz_user_last'] ),
				'user_email' => esc_attr( $_POST['wyz_user_email'] ),
			) );



			foreach ($roles as $role) {
				$user->add_role($role);
			}

			$woo_attrs = array('billing_company','billing_country','billing_address_1','billing_address_2',
									'billing_city','billing_state','billing_phone','billing_postcode');

			foreach ( $data as $key => $value ) {
				if ( in_array($value['type'], $woo_attrs) && isset( $_POST[ $value['type'] ] ) ) {
						update_user_meta( $user_id, $value['type'], $_POST[ $value['type'] ] );
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
									/*wp_send_json_error(
										sprintf(
											esc_html__( 'There was an error while uploading the attachment.', 'wyzi-business-finder' ),
											$filename
										)
									);*/
								}

								$metadata = wp_generate_attachment_metadata( $attachment_id, $filename );

								wp_update_attachment_metadata( $attachment_id, $metadata );
								update_user_meta( $user_id, 'wyz_register_fields_' . $value['id'], $attachment_id );
							}

						}
					}
					elseif ( ! in_array( $value['type'], $defs ) && isset( $_POST['wyz_register_fields_' . $value['id'] ] ) )
					update_user_meta( $user_id, 'wyz_register_fields_' . $value['id'], $_POST['wyz_register_fields_' . $value['id'] ] );
			}

			// Action hook for plugins and extra fields saving.
			WyzHelpers::add_extra_points( $user_id );
			do_action( 'edit_user_profile_update', $user_id );
			//wp_redirect( get_the_permalink() );
		}
	}
}
add_action( 'init', 'wyz_check_user_update');



/**
 * Register forms and fields for backend user meta data.
 *
 * @param object $user current user whos profile is being viewed.
 */
function wyz_add_user_metadata_fields( $user ) {

	if( is_admin()&& current_user_can( 'administrator' ) ) { 
		$data = get_option( 'wyz_registration_form_data', array() );

		?>
		<h3><?php esc_html_e( 'User Extra Meta Data', 'wyzi-business-finder' ); ?></h3>

		<table class="form-table">

		<?php
		$accepted_field_types = apply_filters( 'wyz_backend_show_edit_user_profile_accepted_field_types', array(
			'text','number','email','url','date','textarea','wysiwyg','selectbox','file'
		) );
		foreach ( $data as $key => $value ) {
			$id = "wyz_register_fields_".$value['id'];
			$val = get_user_meta( $user->ID, $id, true );
			if ( in_array( $value['type'], $accepted_field_types ) )
				echo '<tr><th><label for="' . $id . '">' . $value['label'] . ( ! empty( $value['required'] ) ? '<span class="req"> *</span>' : '' ) . '</label></th><td>';
			switch( $value['type'] ) {
				case 'text':
				case 'number':
				case 'email':
				case 'url':
				case 'date':
					echo '<input name="' . $id . '" class="regular-text" id="' . $id . '" type="' . $value['type'] . '" value="' .  esc_attr( $val ) . '"/>';
				break;
				case 'textarea':
				case 'wysiwyg':
					echo '<textarea name="' . $id . '" class="regular-text" id="' . $id . '" type="' . $value['type'] . '">'. esc_attr( $val ) . '</textarea>';
				break;
				case 'selectbox':
					echo '<div class="select-container">';
					switch ( $value['selecttype'] ) {
						case 'dropdown':
							$type = 'select';
							echo '<select name="'.$id.'" class="wyz-select">';
							break;
						case 'radio':
							$type = 'radio';
							break;
						case 'checkboxes':
							$type = 'multicheck';
							echo '<select name="'.$id.'" class="wyz-select" multiple="multiple">';
							break;
					}

					foreach( $value['options'] as $option ) {
						if($type=='radio')
							echo '<label class="radio-label" for="'.$id.'">'.$option['label'].'</label><input type="radio" ' . ( $option['value'] == $val ? 'checked="checked"' : '' ) . ' value="'.$option['value'].'" name="'.$id.'" ' . ( ! empty( $value['required'] ) ? 'required' : '' ) . '>';
						else
							echo '<option value="'.$option['value'].'"' . ( $option['value'] == $val ? 'selected="selected"' : '' ) . '>'.$option['label'].'</option>';
					}

					if($type!='radio')
						echo '</select>';
					echo '</div>';
				break;
				case 'file':
					if ( ! empty( $val ) ){
						$type = get_post_mime_type($val);
						$attch_content = '';
						$attch_link = '';
						$attch_ttl = get_the_title( $val );
						if ( $type == 'image/jpeg' ||
							 $type == 'image/png' ||
							 $type == 'image/gif' ) {
								$attch_content = '<img src="'.wp_get_attachment_url($val).'"/>';
								$attch_link = wp_get_attachment_url( $val, 'full' );
						} else {
							$attch_link = wp_get_attachment_url( $val );
							$attch_content = ! empty( $type ) ? "<p>Attachment Type: $type</p>" : '<strong>No Attachment</strong>';
						}
						echo '<div class="profile-attachment"><a href="'.$attch_link.'" download>'.$attch_content.
							 "<p class=\"attch-title\">$attch_ttl</p>".'</a></div>';
					}
				break;
			}
			do_action( 'wyz_backend_show_edit_user_profile', $value, $val, $user, $id );
			if ( in_array( $value['type'], $accepted_field_types ) )
				echo '</td></tr>';
		}
		?>
		</table>
	<?php }
}
add_action( 'show_user_profile', 'wyz_add_user_metadata_fields', 2 );
add_action( 'edit_user_profile', 'wyz_add_user_metadata_fields', 2 );

add_action( 'personal_options_update', 'wyz_save_user_metadata_fields' );
add_action( 'edit_user_profile_update', 'wyz_save_user_metadata_fields' );

function wyz_save_user_metadata_fields( $user_id ) {

	if( !is_admin() || !current_user_can( 'edit_user', $user_id ) ) {
		return false;
	}

	$data = get_option( 'wyz_registration_form_data', array() );

	$defs = array( 'username', 'pemail', 'fname', 'lname', 'password', 'subscription' );

	foreach ( $data as $key => $value ) {
		if ( ! in_array( $value['type'], $defs ) && isset( $_POST['wyz_register_fields_' . $value['id'] ] ) )
			update_user_meta( $user_id, 'wyz_register_fields_' . $value['id'], $_POST['wyz_register_fields_' . $value['id'] ] );
	}

}