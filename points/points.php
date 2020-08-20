<?php
/**
 * Main Points initializer
 *
 * @package wyz
 */


/**
 * Register forms and fields for backend points submission.
 *
 * @param object $user current user whos profile is being viewed.
 */
function wyz_add_user_points_fields( $user ) {

	if( is_admin()&& current_user_can( 'administrator' ) ) { 
		$is_vendor = WyzHelpers::is_user_vendor( $user );?>
		<h3><?php esc_html_e( 'Points', 'wyzi-business-finder' ); ?></h3>

		<table class="form-table">

			<tr>
				<th>
					<label for="points-available">
						<?php esc_html_e( 'Available Points', 'wyzi-business-finder' ); ?>
					</label>
				</th>
				<td>
					<input type="text" name="points-available" id="points-available" value="<?php echo esc_attr( get_the_author_meta( 'points_available', $user->ID ) ); ?>" class="regular-text" /><br />
				</td>
			</tr>

			<tr>
				<th>
					<label for="wyz_is_vendor">
						<?php esc_html_e( 'Is Vendor', 'wyzi-business-finder' ); ?>
					</label>
				</th>
				<td>
					<input type="checkbox" name="wyz_is_vendor" id="wyz-is-vendor" <?php echo $is_vendor ? 'checked="checked"' : '';?> /><br />
				</td>
			</tr>

			<?php if ( WyzHelpers::is_user_pending( $user->ID ) ) {?>
				<tr>
					<th>
						<label for="wyz_approve_user">
							<?php esc_html_e( 'Approve User', 'wyzi-business-finder' ); ?>
						</label>
					</th>
					<td>
						<input type="checkbox" name="wyz_approve_user" id="wyz_approve_user"/><br />
						<p class="description">This user is pending email verification. You can skip this step and have him verified by checking this check box.</p>
					</td>
				</tr>
			<?php }?>

		</table>
	<?php }
}
add_action( 'show_user_profile', 'wyz_add_user_points_fields' );
add_action( 'edit_user_profile', 'wyz_add_user_points_fields' );

add_action( 'personal_options_update', 'wyz_save_user_points_fields' );
add_action( 'edit_user_profile_update', 'wyz_save_user_points_fields' );


if ( ! post_type_exists( 'wyz_points_transfer' ) ) 
	add_action( 'init', 'wyz_create_points_transfer', 5 );

function wyz_create_points_transfer() {
	register_post_type( 'wyz_points_transfer',array(
		'public' => true,
		'map_meta_cap' => true,
		'labels' => array(
			'name' => 'Points Transfer',
			'singular_name' => 'Points Transfer',
			'add_new' => esc_html__( 'Add New', 'wyzi-business-finder' ),
			'add_new_item' => esc_html__( 'Add new Transfer item', 'wyzi-business-finder' ),
			'edit' => esc_html__( 'Edit', 'wyzi-business-finder' ),
			'edit_item' => esc_html__( 'Edit Points Transfer', 'wyzi-business-finder' ),
			'new_item' => esc_html__( 'New Transfer item', 'wyzi-business-finder' ),
			'view' => esc_html__( 'View', 'wyzi-business-finder' ),
			'view_item' => esc_html__( 'View Transfer', 'wyzi-business-finder' ),
			'search_items' => esc_html__( 'Search Points Transfers', 'wyzi-business-finder' ),
			'not_found' => esc_html__( 'No items found', 'wyzi-business-finder' ),
			'not_found_in_trash' => esc_html__( 'No items found in trash', 'wyzi-business-finder' ),
			'parent' => esc_html__( 'Parent item', 'wyzi-business-finder' ),
		),
		'menu_position' => 55.8,
		'supports' => array( 'title', 'editor' ),
		'taxonomies' => array( '' ),
		'menu_icon' => plugins_url( 'images/icon-transfer.png', __FILE__ ),
		'publicly_queryable' => false,
		'exclude_from_search' => true,
		'has_archive' => false,
	) );
}


add_filter( 'manage_wyz_points_transfer_posts_columns', 'wyz_set_points_transferred_column' );
function wyz_set_points_transferred_column($columns) {
    $columns['transfer_from'] = __( 'From', 'wyzi-business-finder' );
    $columns['transfer_to'] = __( 'To', 'wyzi-business-finder' );
    $columns['transfer_amount'] = __( 'Ammount Transferred', 'wyzi-business-finder' );
    $columns['transfer_fee'] = __( 'Transfer fee', 'wyzi-business-finder' );
    return $columns;
}


add_action( 'manage_wyz_points_transfer_posts_custom_column' , 'wyz_custom_points_transferred_column', 10, 2 );
function wyz_custom_points_transferred_column( $column, $post_id ) {

	switch ( $column ) {
		case 'transfer_from':
		case 'transfer_to':
			$user = get_userdata( get_post_meta( $post_id , $column , true ) );
			echo $user->user_login;
		break;
		case 'transfer_amount':
		case 'transfer_fee':
			echo get_post_meta( $post_id , $column , true );
	}
}



function wyz_save_user_points_fields( $user_id ) {

	if( !is_admin() || !current_user_can( 'edit_user', $user_id ) ) {
		return false;
	}

	update_user_meta( $user_id, 'points_available', $_POST['points-available'] );
}

add_action( 'profile_update', 'wyz_add_user_vendor_role', 10, 2 );

function wyz_add_user_vendor_role( $user_id, $old_user_data ) {

	if ( isset( $_POST['wyz_approve_user'] ) && 'on' == $_POST['wyz_approve_user'] ) {
		$token = get_user_meta( $user_id, 'pending_user_token', true );
		update_user_meta( $user_id, 'pending_email_verify', 'verified' );
		delete_option( $token );
		
		$user = get_user_by( 'id', $user_id );
		$user->set_role( '' );
		$user->set_role( get_user_meta( $user_id, 'wyz_user_role', true ) );

		delete_user_meta( $user_id, 'wyz_user_role' );
		wyz_user_greeting_mail( $user_id );
	}

	if ( !function_exists( 'get_wcmp_vendor' ) || ! is_admin() )return;

	$user = new WP_User( $user_id );
	if ( isset( $_POST['wyz_is_vendor'] ) && 'on' == $_POST['wyz_is_vendor'] ) {

		WyzHelpers::make_user_vendor( $user );
		
	} else {
		$user->remove_role( 'dc_vendor' );
		$user->remove_cap( 'dc_vendor' );
	}
}


/**
 * Adds a Points column to the user display dashboard.
 *
 * @param array $columns user column.
 */
function wyz_add_user_points_column( $columns ) {

	if( is_admin() ) {
		$columns['points_available'] = esc_attr( __( 'Points', 'wyzi-business-finder' ) );
		return $columns;
	}
}
add_filter( 'manage_users_columns', 'wyz_add_user_points_column' );

/**
 * add points column to users display table
 *
 * @param object $value not needed here.
 * @param string $column_name current column name.
 * @param integer $user_id id of user whos profile is in view.
 */
function wyz_show_user_points_data( $value, $column_name, $user_id ) {
	
	if( is_admin() && 'points_available' == $column_name ) {
		return esc_attr( get_user_meta( $user_id, 'points_available', true ) );
	}
}
add_action( 'manage_users_custom_column', 'wyz_show_user_points_data', 10, 3 );

