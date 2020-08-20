<?php
class WyzUserAccount {

	private $tabs = array();
	private $UserAccountType;
	public $is_business_owner;
	public $user_id;
	private $template_type;
	
	public function __construct( $acc_type ) {
		$this->template_type = 1;
		if ( function_exists( 'wyz_get_theme_template' ) )
			$this->template_type = wyz_get_theme_template();
		$this->UserAccountType = $acc_type;
		$this->is_business_owner = current_user_can( 'publish_businesses' );
		if ( ! $this->UserAccountType ) { return; }
		$this->user_id = get_current_user_id();

		$this->tabs[] = new AccountBusiness($this->is_business_owner, $this->user_id );
		$this->tabs[] = new AccountProfile($this->is_business_owner, $this->user_id);
		$this->tabs[] = new AccountFavorite($this->is_business_owner, $this->user_id);
		$this->tabs[] = new AccountWoo($this->is_business_owner, $this->user_id);
		$this->tabs[] = new AccountVendor($this->is_business_owner, $this->user_id);
		$this->tabs[] = new AccountProducts($this->is_business_owner, $this->user_id);
		$this->tabs[] = new AccountSubscription($this->is_business_owner, $this->user_id);
		$this->tabs[] = new AccountJob($this->is_business_owner, $this->user_id);
		$this->tabs[] = new AccountBooking($this->is_business_owner, $this->user_id);
		$this->tabs[] = new InternalMessaging($this->is_business_owner, $this->user_id);

		do_action('wyz_after_account_tab_declaration');

		$this->tabs = apply_filters( 'wyz_additional_user_account_tabs', $this->tabs );


		if ( class_exists('WP_Job_Manager_Shortcodes') ) {
			add_action( 'wp', function(){
				$shortcodes_handler = WP_Job_Manager_Shortcodes::instance();
				$shortcodes_handler->job_dashboard_handler();
			} );
		}

		add_action( 'wp_enqueue_scripts', array($this, 'enqueue_frontend_scripts' ) );

		if( 'verified'==get_user_meta( $this->user_id, 'pending_email_verify', true )){
			delete_user_meta( $this->user_id, 'pending_email_verify' );
		}
		
	}


	public function the_account_tabs() {

		if ( ! $this->UserAccountType ) { return; }

		if ( apply_filters( 'wyz_hide_account_page_content', false, $this->UserAccountType ) )
			return;

		if ( $this->UserAccountType == WyzQueryVars::Dashboard ) {?>

		<div class="business-profile-tab-list">
			<?php if ( 1 == $this->template_type ) echo '<div class="container">';?>
					<!-- Tab List -->
					<div class="profile-tab-list col-xs-12">
						<ul>
							<?php
							foreach ( $this->tabs as $tab )
								$tab->the_tab();
							?>
						</ul>
						<div class="scrollbar wyz-primary-color wyz-prim-color"><div class="handle"><div class="mousearea"></div></div></div>
					</div>
					<div class="profile-tab-list profile-tab-list-dropdown col-xs-12">
						<select id="profile-tab-list-dropdown" class="wyz-input wyz-select">
							<?php
							foreach ( $this->tabs as $tab )
								$tab->the_tab_drop();
							?>
						</select>
						<div class="scrollbar wyz-primary-color wyz-prim-color"><div class="handle"><div class="mousearea"></div></div></div>
					</div>
			<?php if ( 1 == $this->template_type ) echo '</div>';?>	
		</div>
		<div class="clear"></div>
	<?php
		} elseif ( $this->UserAccountType ) {
			//$this->the_points_status( false );
		}
	}

	public function the_page_title() {
		global $WYZ_USER_ACCOUNT_TYPE;

		if ( ! $WYZ_USER_ACCOUNT_TYPE || $WYZ_USER_ACCOUNT_TYPE == WyzQueryVars::Dashboard )
			return  the_title( '', '' );

		if ( $WYZ_USER_ACCOUNT_TYPE == WyzQueryVars::ManageBusiness )
			return sprintf( esc_html__( 'Manage %s', 'wyzi-business-finder' ), get_the_title( $_GET[ $WYZ_USER_ACCOUNT_TYPE ] ) );

		if ( $WYZ_USER_ACCOUNT_TYPE == WyzQueryVars::AddNewBusiness )
			return esc_html__( 'Add New Business', 'wyzi-business-finder');

		if ( $WYZ_USER_ACCOUNT_TYPE == WyzQueryVars::EditBusiness )
			return sprintf( esc_html__( 'Edit Business: %s', 'wyzi-business-finder' ), get_the_title( $_GET[ $WYZ_USER_ACCOUNT_TYPE ] ) );

		if ( $WYZ_USER_ACCOUNT_TYPE == WyzQueryVars::EditOffer )
			return sprintf( esc_html__( 'Edit %s: %s', 'wyzi-business-finder' ), WYZ_OFFERS_CPT, get_the_title( $_GET[ $WYZ_USER_ACCOUNT_TYPE ] ) );

		if ( $WYZ_USER_ACCOUNT_TYPE == WyzQueryVars::GetPoints )
			return esc_html__( 'Buy Points', 'wyzi-business-finder' );

		if ( $WYZ_USER_ACCOUNT_TYPE == WyzQueryVars::TransferPoints )
			return esc_html__( 'Transfer Points', 'wyzi-business-finder' );

		if ( $WYZ_USER_ACCOUNT_TYPE == WyzQueryVars::BusinessCalendar )
			return esc_html__( 'Business Calendar', 'wyzi-business-finder' );
		
		if ( $WYZ_USER_ACCOUNT_TYPE == WyzQueryVars::AddProduct ) {
			return $_GET[ WyzQueryVars::AddProduct ] > 1 ? sprintf( esc_html__( 'Edit Product', 'wyzi-business-finder' ) ) : sprintf( esc_html__( 'Add a New Product', 'wyzi-business-finder' ) );
		}
	}

	public function the_points_status( $tabs_visible = false ) {

		if ( ! $this->UserAccountType || 'on' == get_option( 'wyz_hide_points' ) ) { return; }

		$user_can_business = current_user_can( 'publish_businesses' );
		$user_points = get_user_meta( $this->user_id, 'points_available', true );
		if ( '' != $user_points ) {
			$user_points = intval( $user_points );
		} else {
			$user_points = 0;
		}
		if ( ! $tabs_visible ) { ?>

		<div class="business-profile-tab-list">
			<div class="container">
				<div class="row">

		<?php }?>

		<div id="pts-info-cont" class="float-right">
			<h2><span id="youhave"><?php echo sprintf( esc_html__( 'You Have %d Points', 'wyzi-business-finder' ), $user_points );?></span></h2>
			<div class="buy-transfer-points">
				<a id="buy-points" href="<?php echo WyzHelpers::add_clear_query_arg( array( WyzQueryVars::GetPoints => true ) ); ?>"><?php esc_html_e( 'Buy Points', 'wyzi-business-finder' );?></a>
				<?php if( 'on' == get_option( 'wyz_businesses_points_transfer' ) ) {?>
					<br/><a id="transfer-points" href="<?php echo WyzHelpers::add_clear_query_arg( array( WyzQueryVars::TransferPoints => true ) ); ?>"><?php esc_html_e( 'Transfer Points', 'wyzi-business-finder' );?></a>
				<?php }?>
			</div>
		</div>

		<?php if ( ! $tabs_visible ) { ?>

				</div>
			</div>
		</div>

		<?php
		}
	}

	public function enqueue_frontend_scripts() {
		//wcmp script
		wp_enqueue_script('frontend_js');
	}

	public function the_account_content() {
		if ( $this->UserAccountType != WyzQueryVars::Dashboard ) { return; } 
		do_action( 'wyz_before_user_account_content' );
		?>
		<div class="business-profile-page">
			<div class="tab-content">
				<?php foreach ( $this->tabs as $tab ) {
					$tab->the_content();
				}?>
			</div>
		</div>
		<?php
	}

	public static function subscribtion_tab_on() {
		return current_user_can( 'publish_businesses' ) && 'on' == get_option( 'wyz_sub_mode_on_off' ) && (function_exists('pmpro_hasMembershipLevel') && !pmpro_hasMembershipLevel());
	}
}
require_once( plugin_dir_path( __FILE__ ) . 'user-account-tabs/user-account-tabs.php' );
?>