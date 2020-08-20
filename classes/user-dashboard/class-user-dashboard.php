<?php
class WyzUserDashboard{
	public $title;
	public $page_type;
	private $permalink;
	public $current_page;
	private $page_titles;
	private $is_mobile;
	//sidebar menu items
	private $nav_items;
	private $top_add_items;
	private $user_id;
	private $is_business_owner;
	//conditions
	private $can_offers;
	private $can_products;
	private $can_jobs;
	private $show_subsc;
	private $can_favorite;
	private $can_shop;
	private $show_become_vendor;
	private $can_booking;
	private $can_calendars;
	private $can_add_business;
	private $can_add_offer;
	private $can_add_product;
	private $can_add_job;
	private $can_inbox;
	private $current_page_condition;
	//stats
	private $vendor_stats;
	private $products_count;
	private $have_offers;
	private $css_addr;
	private $js_addr;
	private $tmpl_addr;
	
	public function __construct() {
		global $page_user_dashboard;
		$page_user_dashboard = 'is_set';
		$this->user_id = get_current_user_id();
		$this->permalink = get_the_permalink();
		$this->css_addr = plugin_dir_url( __FILE__ ) . 'css/';
		$this->js_addr = plugin_dir_url( __FILE__ ) . 'js/';
		$this->tmpl_addr = plugin_dir_path( __FILE__ ) . 'templates/';
		$this->is_mobile = function_exists('wyz_is_mobile') && wyz_is_mobile();
		$this->handle_job_delete();
		$this->hooks();
		$this->init_conditions();
		$this->get_current_page();
		$this->init_page_titles();
		$this->setup_nav_links();
		$this->the_content();
	}
	private function init_conditions() {
		$quer = new WP_Query ( array(
			'post_status' => array( 'pending', 'publish'),
			'post_type' => 'wyz_offers',
			'posts_per_page' => 1,
			'author' => $this->user_id
		));
		$this->have_offers = $quer->have_posts();
		$this->is_business_owner = current_user_can( 'publish_businesses' );
		$this->can_offers = $this->is_business_owner && ('on' != get_option( 'wyz_disable_offers' ) || $this->have_offers );
		$this->can_products = $this->is_business_owner && class_exists( 'WooCommerce' ) && WyzHelpers::is_user_vendor( $this->user_id ) && 'off' != get_option( 'wyz_display_vendor_products' );
		$this->can_jobs = $this->is_business_owner && 'on' == get_option( 'wyz_users_can_job' ) && WyzHelpers::wyz_sub_can_bus_owner_do( $this->user_id,'wyzi_sub_can_create_job') && class_exists( 'WP_Job_Manager' ) && ( 'on' != get_option( 'wyz_job_requires_business' ) || WyzHelpers::wyz_has_business( $this->user_id ) );
		$this->show_subsc = $this->is_business_owner && 'on' == get_option( 'wyz_sub_mode_on_off' );
		$this->can_favorite = 'on' == get_option( 'wyz_enable_favorite_business' );
	$this->can_shop = class_exists( 'WooCommerce' ) && 'on' != get_option( 'wyz_woocommerce_hide_orders_tab' );
		$this->show_become_vendor = ! current_user_can( 'manage_options' ) && $this->is_business_owner && WyzHelpers::wyz_sub_can_bus_owner_do( $this->user_id,'wyzi_sub_business_can_apply_vendor') && WyzHelpers::wyz_has_business( $this->user_id, 'published' ) && class_exists( 'WooCommerce' ) && function_exists( 'is_user_wcmp_vendor' ) && ! is_user_wcmp_vendor( $this->user_id ) && 'off' != get_option( 'wyz_can_become_vendor' ) &&
			get_option( 'woocommerce_enable_myaccount_registration' ) === 'yes';
		$this->can_booking = 'off' != get_option( 'wyz_users_can_booking' );
		$this->can_calendars = $this->is_business_owner && 'off' != get_option( 'wyz_users_can_booking' ) && WyzHelpers::wyz_sub_can_bus_owner_do($this->user_id,'wyzi_sub_business_can_create_bookings') && class_exists( 'WooCommerce' );
		$this->can_add_product = WyzHelpers::user_can_publish_products( $this->user_id );
		$this->can_add_offer = 'on' != get_option( 'wyz_disable_offers' ) && WyzHelpers::wyz_sub_can_bus_owner_do( $this->user_id,'wyzi_sub_business_can_create_offers');
		$this->can_add_job = WyzHelpers::user_can_create_job( $this->user_id );
		$this->can_add_business = WyzHelpers::user_can_create_business( $this->user_id );
		$this->can_inbox = 'on' == get_option( 'wyz_private_msg_status_on_off' ) && ( ( $this->is_business_owner && WyzHelpers::wyz_sub_can_bus_owner_do($this->user_id,'wyzi_sub_business_have_inbox') ) || current_user_can( 'manage_options' ) || ( WyzHelpers::wyz_is_current_user_client() && 'on' != get_option( 'wyz_private_msg_hide_client' ) ) );
	}
	private function get_current_page() {
		if ( isset( $_GET['page'] ) )
			switch ( $_GET['page'] ) {
				case WyzQueryVars::Offers:
					$this->current_page_condition = $this->can_offers;
					$this->current_page = $_GET['page'];
					break;
				case WyzQueryVars::Products:
					$this->current_page_condition = $this->can_products;
					$this->current_page = $_GET['page'];
					break;
				case WyzQueryVars::Businesses:
					$this->current_page_condition = $this->is_business_owner;
					$this->current_page = $_GET['page'];
					break;
				case 'profile':
					$this->current_page_condition = true;
					$this->current_page = $_GET['page'];
					break;
				case 'subscription':
					$this->current_page_condition = $this->show_subsc;
					$this->current_page = $_GET['page'];
					break;
				case 'favorite':
					$this->current_page_condition = $this->can_favorite;
					$this->current_page = $_GET['page'];
					break;
				case 'shop':
					$this->current_page_condition = $this->can_shop;
					$this->current_page = $_GET['page'];
					break;
				case 'vendor-form':
					$this->current_page_condition = $this->show_become_vendor;
					$this->current_page = $_GET['page'];
					break;
				case 'appointments':
					$this->current_page_condition = $this->can_booking;
					$this->current_page = $_GET['page'];
					break;
				case 'calendars':
					$this->current_page_condition = $this->can_calendars;
					$this->current_page = $_GET['page'];
					break;
				case 'add-edit-business':
					$this->current_page_condition = ( isset( $_GET[ WyzQueryVars::AddNewBusiness ] ) ? $this->can_add_business : ( isset( $_GET[ WyzQueryVars::EditBusiness ] ) ? 'off' != get_option( 'wyz_allow_business_edit' ) && WyzHelpers::wyz_sub_can_bus_owner_do( $this->user_id, 'wyzi_sub_can_edit_business' ) : false ) );
					$this->current_page = 'business-form';
					break;
				case 'add-edit-offer':
					$this->current_page_condition = ( isset( $_GET[ WyzQueryVars::AddNewOffer ] ) ? $this->can_add_offer : ( isset( $_GET[ WyzQueryVars::EditOffer ] ) ? 'off' != get_option( 'wyz_offer_editable' ) : false ) );
					$this->current_page = 'offer-form';
					break;
				case WyzQueryVars::Jobs:
					$this->current_page_condition = $this->can_jobs;
					if ( isset( $_GET['action'] ) && 'edit' == $_GET['action'] && isset( $_GET['job_id'] ) )
						$this->current_page = 'job-form';
					else
						$this->current_page = 'jobs';
					break;
				case 'add-edit-job':
					$this->current_page_condition = $this->can_jobs;
					$this->current_page = 'job-form';
					break;
				case 'add-edit-product':
					$this->current_page_condition = $this->can_add_product;
					$this->current_page = 'product-form';
					break;
				case 'inbox':
					$this->current_page_condition = $this->can_inbox;
					$this->current_page = 'inbox';
					break;
				default:
					$this->current_page_condition = true;
					$this->current_page = $this->is_business_owner ? 'landing' : 'favorite';
					break;
			} elseif( isset( $_GET['action'] ) && ( 'sent_items' == $_GET['action'] || 'trash' == $_GET['action'] || 'read' == $_GET['action'] ) ) {
				$this->current_page_condition = $this->can_inbox;
				$this->current_page = 'inbox';
			}elseif( function_exists('is_wc_endpoint_url')&&is_wc_endpoint_url()) {
				$this->current_page_condition = $this->can_products;
				$this->current_page = 'shop';
			} else {
				$this->current_page_condition = true;
				$this->current_page = $this->is_business_owner ? 'landing' : 'favorite';;
			}
	}
	private function init_page_titles() {
		$this->page_titles = array(
			WyzQueryVars::Offers => array(
				esc_html__( 'Offers', 'wyzi-business-finder' ),
				'star-o'
			), WyzQueryVars::Businesses => array(
				'Mi Tienda',
				'institution'
			),WyzQueryVars::Products => array(
				__( 'Products', 'wyzi-business-finder' ),
				'shopping-bag'
			),'profile' => array(
				__( 'My Profile', 'wyzi-business-finder' ),
				'user'
			),'subscription' => array(
				__( 'Subscription', 'wyzi-business-finder' ),
				'credit-card'
			),'favorite' => array(
				__( 'Favorites', 'wyzi-business-finder' ),
				'heart'
			),'shop' => array(
				__( 'Usuario', 'wyzi-business-finder' ),
				'user'
			),'vendor-form' => array(
				__( 'Become a Vendor', 'wyzi-business-finder' ),
				'money'
			),'appointments' => array(
				__( 'Appointments', 'wyzi-business-finder' ),
				'calendar-o'
			),'calendars' => array(
				__( 'Calendars', 'wyzi-business-finder' ),
				'calendar'
			),'business-form' => array(
				( isset( $_GET[ WyzQueryVars::EditBusiness ] ) ? __( 'Edit Business', 'wyzi-business-finder' ) : __( 'Add New Business', 'wyzi-business-finder' ) ),
				(isset( $_GET[ WyzQueryVars::EditBusiness ] ) ? 'edit' : 'plus-circle' )
			),'offer-form' => array(
				( isset( $_GET[ WyzQueryVars::EditOffer ] ) ? __( 'Edit Offer', 'wyzi-business-finder' ) : __( 'Add New Offer', 'wyzi-business-finder' ) ),
				(isset( $_GET[ WyzQueryVars::EditOffer ] ) ? 'edit' : 'plus-circle' )
			),'job-form' => array(
				( isset( $_GET['action'] ) && 'edit' == $_GET['action'] ? __( 'Edit Job', 'wyzi-business-finder' ) : __( 'Add New Job', 'wyzi-business-finder' ) ),
				(isset( $_GET['action'] ) && 'edit' == $_GET['action'] ? 'edit' : 'plus-circle' )
			),'jobs' => array(
				__( 'Jobs', 'wyzi-business-finder' ),
				'suitcase'
			),'landing' => array(
				__( 'Dashboard', 'wyzi-business-finder' ),
				'star'
			),'inbox' => array(
				__( 'Inbox', 'wyzi-business-finder' ),
				'envelope'
			),'inbox-sent-items' => array(
				__( 'Sent Items', 'wyzi-business-finder' ),
				'envelope'
			),'inbox-trash' => array(
				__( 'Trash', 'wyzi-business-finder' ),
				'envelope'
			),
		);
		if ( isset( $_GET['get-points'] ) ) {
			$this->page_titles['profile'] = array(
				__( 'Buy Points', 'wyzi-business-finder' ),
				'usd'
			);
		}
	}
	private function hooks() {
		
		add_action('wp_enqueue_scripts', array($this,'remove_unneeded_scripts' ), 1000 );
		add_action('wp_enqueue_scripts', array( $this,'scripts' ),1001 );
		//wp_enqueue_script( 'business_form_jQuery',1002);
		add_filter( 'submit_job_form_wp_editor_args', array( $this, 'override_job_mce' ) );
		add_action( 'wyz_user_dashboard_before_content', array( $this, 'handle_page_notifications' ) );
		add_filter( 'wyz_user_dashboard_notifications', array( $this, 'check_product_publish' ), 10, 2 );
	}
	public function scripts() {
		$req = array();
		if ( 'vendor-form' == $this->current_page )
			$req[] = 'wcmp_vandor_registration_css';
		wp_enqueue_style( 'user-dashboard-style', $this->css_addr . 'all.css', $req );
		if ( is_rtl() )
			wp_enqueue_style( 'user-dashboard-style-rtl', $this->css_addr . 'rtl.css', 'user-dashboard-style' );
		wp_enqueue_script( 'user-dashboard-basics', $this->js_addr . 'base.js',1 );
		if ( 'landing' == $this->current_page ) {
			wp_enqueue_script( 'user-dashboard-charts', $this->js_addr . 'charts.min.js',1 );
			$business_week_info = WyzHelpers::get_author_week_visits( $this->user_id );
			wp_localize_script( 'user-dashboard-charts', 'charts', array(
				'days' => array(
					esc_html__( 'Mon', 'wyzi-business-finder' ),
					esc_html__( 'Tues', 'wyzi-business-finder' ),
					esc_html__( 'Wed', 'wyzi-business-finder' ),
					esc_html__( 'Thurs', 'wyzi-business-finder' ),
					esc_html__( 'Fri', 'wyzi-business-finder' ),
					esc_html__( 'Sat', 'wyzi-business-finder' ),
					esc_html__( 'Sun', 'wyzi-business-finder' ),
				),
				'label' => esc_html__( 'Visits', 'wyzi-business-finder' ),
				'data' => $business_week_info['stats'],
				'color' => $business_week_info['color'],
				'title' => $business_week_info['title'],
			) );
		}
		$exporters       = apply_filters( 'wp_privacy_personal_data_exporters', array() );
		$exporters_count = count( $exporters );
		$cur_user = wp_get_current_user();
		$email = is_object( $cur_user ) ? $cur_user->user_email : '';
		wp_localize_script( 'user-dashboard-basics', 'myAccount', array( 
			'invalidText' => esc_html__( 'Invalid amount', 'wyzi-business-finder' ),
			'reduce' => esc_html__( 'points will be reduced from your balance', 'wyzi-business-finder' ),
			'pointsAvailable' => get_user_meta( get_current_user_id(), 'points_available', true ),
			'exceeds' => esc_html__( 'points exceed your balance', 'wyzi-business-finder' ),
			'logoutText' => esc_html__( 'Are you sure You want to logout?', 'wyzi-business-finder' ),
			'logout' => esc_html__( 'logout', 'wyzi-business-finder' ),
			'cancel' => esc_html__( 'cancel', 'wyzi-business-finder' ),
			'delete' => esc_html__( 'delete', 'wyzi-business-finder' ),
			'deleteText' => esc_html__( 'Warning, this will delete your account, along side all your associated info. This step cannot be undone. If you want to proceed, please input your password below, and click "delete"', 'wyzi-business-finder' ),
			'export' => esc_html__( 'Export', 'wyzi-business-finder' ),
			'exportText' => esc_html__( 'This will generate a file containing your information. If you want to proceed, please input your password below, and click "Export"', 'wyzi-business-finder' ),
			'deleteError' => esc_html__( 'Incorrect Password', 'wyzi-business-finder' ),
			'deleteReload' => esc_url( home_url() ),
			'verifiedText' => esc_html__( 'Your account has been verified!', 'wyzi-business-finder' ),
			'isWoocommerceEndpoint' => function_exists( 'is_wc_endpoint_url' ) && is_wc_endpoint_url(),
			'justVerified' => ('verified'==get_user_meta( get_current_user_id(), 'pending_email_verify', true ) ? 'yes':'no'),
			'exportersCount' => $exporters_count,
			'userEmail' => $email,
			'deleteError' => esc_html__( 'An error occurred during the process', 'wyzi-business-finder' )
		));
		wp_enqueue_script( 'wyz_my_account_js' );
		add_filter( 'cmb2_enqueue_css', '__return_false' );
	}
	public  function remove_unneeded_scripts () {
		global $wp_scripts;
		global $wp_styles;
		
		//wp_register_script( 'wyz_my_account_js', plugin_dir_url( __FILE__ ) . 'templates-and-shortcodes/js/my-account.js', array( 'jquery', 'password-strength-meter' )
		 if( 'business-form' == $this->current_page || 'offer-form' == $this->current_page || 'appointments' == $this->current_page || 'calendars' == $this->current_page ) {
		 	$scripts_to_remove = array('wyz-page-loader-js','wyz_placeholder','wp-job-manager-job-submission', 'wp-job-manager-ajax-file-upload');
		 	$styles_to_remove = array('cmb2_field_slider_css','wyz-template-style','wyz-default-style','wyz-responsive-style','wyz_booked_addon_style','wyz-page-loader-css');
		 	if ( 'appointments' != $this->current_page && 'calendars' != $this->current_page ) {
		 		$scripts_to_remove[] = 'booked-wc-fe-functions';
		 		$scripts_to_remove[] = 'booked-wc-fe-functions';
		 		$scripts_to_remove[] = 'booked-fea-js';
		 		$scripts_to_remove[] = 'booked-spin-js';
		 		$scripts_to_remove[] = 'booked-spin-jquery';
		 		$scripts_to_remove[] = 'booked-chosen';
		 		$scripts_to_remove[] = 'booked-fitvids';
		 		$scripts_to_remove[] = 'booked-calendar-popup';
		 		$scripts_to_remove[] = 'booked-tooltipster';
		 		$scripts_to_remove[] = 'booked-functions';
			 	$styles_to_remove [] = 'booked-icons';
			 	$styles_to_remove [] = 'booked-tooltipster';
			 	$styles_to_remove [] = 'booked-tooltipster-theme';
			 	$styles_to_remove [] = 'booked-animations';
			 	$styles_to_remove [] = 'booked-styles';
			 	$styles_to_remove [] = 'booked-responsive';
			 	$styles_to_remove [] = 'booked-fea-styles';
			 	$styles_to_remove [] = 'booked-wc-fe-styles';
		 	}
		 	foreach ($wp_scripts->registered as $handle => $data)
			{
				if ( in_array($handle, $scripts_to_remove) ) {
					wp_deregister_script($handle);
					wp_dequeue_script($handle);
				}
			}
			foreach ($wp_styles->registered as $handle => $data)
			{
				if ( in_array($handle, $styles_to_remove) ) {
					wp_deregister_style($handle);
					wp_dequeue_style($handle);
				}
			}
			return;
		}
		$scripts_to_keep = array( 'user-dashboard-basics', 'wp-job-manager-job-submission', 'jquery-iframe-transport','jquery-fileupload', 'wp-job-manager-ajax-file-upload','booked-wc-fe-functions','booked-fea-js', 'media-views','utils','booked-spin-js','booked-spin-jquery','booked-chosen','booked-fitvids','booked-calendar-popup','booked-tooltipster','booked-functions'
		);
		$styles_to_keep = array('user-dashboard-style','bsf-Defaults','wp-job-manager-frontend', 'booked-icons','booked-tooltipster','booked-tooltipster-theme','booked-animations','booked-styles','booked-responsive','booked-fea-styles','booked-wc-fe-styles', 'wyz-google-font-raleway', 'wyz-google-font-varelaround', 'tp-open-sans', 'tp-raleway', 'tp-droid-serif','pmpro-advanced-levels-styles','pmprowoo');
		if ( 'products' == $this->current_page || ( 'profile' == $this->current_page && isset( $_GET['get-points'] ) ) ) {
			$styles_to_keep[] = 'wyz-woocommerce-style-overrides';
			$styles_to_keep[] = 'woocommerce-layout';
			$styles_to_keep[] = 'woocommerce-smallscreen';
			$styles_to_keep[] = 'woocommerce-general';
			$scripts_to_keep[] = 'jquery';
			$scripts_to_keep[] = 'woocommerce';
		} elseif ( 'shop' == $this->current_page || ( function_exists('is_wc_endpoint_url') && is_wc_endpoint_url() ) ) {
			$styles_to_keep[] = 'wyz-woocommerce-style-overrides';
			$styles_to_keep[] = 'woocommerce-general';
		} elseif( 'inbox' == $this->current_page ) {
			$scripts_to_keep[] = 'jquery-ui-autocomplete';
			$scripts_to_keep[] = 'private-message';
			$styles_to_keep[] = 'private-message';
		}
		foreach ($wp_scripts->registered as $handle => $data)
		{
			if ( in_array($handle, $scripts_to_keep) )  continue;
			wp_deregister_script($handle);
			wp_dequeue_script($handle);
		}
		if ( isset( $wp_scripts->registered['private-message'] ) ) {
			$pmrs = $wp_scripts->registered;
			$pmrs['private-message']->deps = array();
			$wp_scripts->registered = $pmrs;
		}
		foreach ($wp_styles->registered as $handle => $data)
		{
			if ( in_array($handle, $styles_to_keep) ) continue;
			wp_deregister_style($handle);
			wp_dequeue_style($handle);
		}
	}
	public function check_product_publish( $notifications, $current_page ) {
		if ( 'profile' == $current_page && isset( $_GET['get-points'] ) && isset( $_GET['add-to-cart'] ) ) {
			$pf = new WC_Product_Factory();
    		$product = $pf->get_product($_GET['add-to-cart']);
    		if ( $product ) {
    			global  $woocommerce;
				$pts = $product->get_attribute( 'pa_points_value' );
				$price = $product->get_price();
				$content = sprintf( esc_html__( "%d points for %d%s added to your cart." ), $pts, $price, get_woocommerce_currency_symbol() ) . ' <a href="' . wc_get_cart_url() . '">' . esc_html__( 'View cart', 'wyzi-business-finder' ) . '</a>';
				$notifications[] = array(
					'type' => 'success',
					'content' => $content
				);
    		}
		}
		return $notifications;
	}
	public function handle_page_notifications() {
		$notifications = array();
		$notifications = apply_filters( 'wyz_user_dashboard_notifications', $notifications, $this->current_page );
		foreach ( $notifications as $notif ) {
			switch ( $notif['type'] ) {
				case 'success':
					WyzHelpers::wyz_success( $notif['content'] );
					break;
				case 'warning':
					WyzHelpers::wyz_warning( $notif['content'] );
					break;
				case 'error':
					WyzHelpers::wyz_error( $notif['content'] );
					break;
				default:
					WyzHelpers::wyz_info( $notif['content'] );
					break;
			}
		}
	}
	public function override_job_mce( $args ) {
		$args['tinymce']['plugins'] = 'lists,paste,tabfocus';
		$args['tinymce']['toolbar1'] = 'bold,italic,|,bullist,numlist,|,undo,redo';
		return $args;
	}
	
	public function the_content() {
		$this->header();
		do_action( 'wyz_user_dashboard_after_header' );
		?>
		<!-- page content -->
		<div class="container-fluid-full<?php echo ($this->is_mobile ? '' : ' sb-open');?>">
			<div class="row-fluid">
				<?php $this->sidebar();
				do_action( 'wyz_user_dashboard_after_sidedbar' );?>
				<noscript>
					<div class="alert alert-block span10">
						<h4 class="alert-heading">Warning!</h4>
						<p>You need to have <a href="http://en.wikipedia.org/wiki/JavaScript" target="_blank">JavaScript</a> enabled to use this page.</p>
					</div>
				</noscript>
				<div id="sidebar-overlay" class="sidebar-toggle<?php echo ($this->is_mobile ? '' : ' apd_get_active_symbols()');?>"></div>
				<div class="page-title<?php echo ($this->is_mobile ? '' : ' sb-open');?>"><?php $this->current_page_title();?></div>
				<div id="content" class="<?php echo ($this->is_mobile ? '' : 'sb-open');?>">
				<?php do_action( 'wyz_user_dashboard_before_content' );?>
				<?php $this->page_content();?>
				</div>
			</div>
		</div>
		<?php
		$this->footer();
	}
	
	private function header() {
		?>
		<!DOCTYPE html>
		<html style="margin-top: 0!important;" <?php echo ( is_rtl() ? 'dir="rtl"' : '' );?> <?php language_attributes();?>>
		<head><meta http-equiv="Content-Type" content="text/html; charset=euc-jp">
			
			<meta name="viewport" content="width=device-width, initial-scale=1">

			<!-- The HTML5 shim, for IE6-8 support of HTML5 elements -->
			<!--[if lt IE 9]>
			  	<script src="https://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.3/html5shiv.js"></script>
				<link id="ie-style" href="<?php echo $this->css_addr . 'ie.css';?>" rel="stylesheet">
			<![endif]-->
			
			<!--[if IE 9]>
				<link id="ie9style" href="<?php echo $this->css_addr . 'ie9.css';?>" rel="stylesheet">
			<![endif]-->

			<?php do_action( 'wp_head' );?>	
		</head>

		<body>
		<?php $this->top_nav();
	}
	private function top_nav() {
		?>
		<div class="navbar">
			<div class="navbar-inner">
				<div class="container-fluid main-header">
					
					<a class="brand" href="<?php echo home_url();?>"><span><?php echo bloginfo('title');?></span></a>
					<button class="btn sidebar-toggle">
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>

					<?php $this->top_nav_right();?>
				</div>
			</div>
		</div>
        <?php
	}
	private function top_nav_right() {
		$this->setup_add_links();
		$points_on = current_user_can( 'publish_businesses' ) && 'on' != get_option( 'wyz_hide_points' );
		if ( $points_on ) {
			$points_credit = get_the_author_meta( 'points_available', $this->user_id );
			$points_credit = ( ! empty( $points_credit ) ? $points_credit : 0 );
			$buy_per = array( WyzQueryVars::GetPoints =>true );
			$buy_per['page'] = 'profile';
		}
		?>
		<!-- start: Header Menu -->
		<div class="nav-no-collapse header-nav">
			<ul class="nav">
				<!-- start: User Dropdown -->

				<li class="dropdown">
					<a id="add-new-listing" class="btn dropdown-toggle" data-toggle="dropdown" href="#">
						<i class="halflings-icon plus"></i> <?php esc_html_e( 'Add New', 'wyzi-business-finder' );?>
						<span class="fa fa-plus-circle"></span>
					</a>
					<ul class="dropdown-menu">
						<?php if ( $points_on )
							echo '<li id="points-available"><a href="#" class="disabled">' . esc_html__( 'Available Points:', 'wyzi-business-finder' ) . " <span>$points_credit</span></a></li>";?>
						<?php $this->add_new_links();?>
						<?php if ( $points_on ) {
							echo '<li id="buy-points"><a href="' . WyzHelpers::add_clear_query_arg( $buy_per ) . '"><i class="fa fa-usd"></i>' . esc_html__( 'Buy Points', 'wyzi-business-finder' ) . '</a></li>';
						} ?>
					</ul>
				</li>
			</ul>
		</div>
		<!-- end: Header Menu -->
	  <?php
	}
/* El boton añadir cambia si tienes publicada una tienda o no */
	private function add_new_links() {
	    if ( $this->can_add_business) 
	     echo    '<a   href="https://sesionesonline.com/user-account?page=add-edit-business&add-new-business=1" <i class="fa fa-address-card fa-1x" <p>Clic aquí para crear tu perfil profesional y comenzar a publicar avisos </p></i></a>'; 
	     	      else
	foreach ($this->top_add_items as $key => $item) {
			echo '<li><a href="'.$item['link'].'">'.(isset($item['icon'])&&!empty($item['icon'])?('<i class="fa fa-'.$item['icon'].'"></i>'):'').$item['title'].'</a></li>';
		}
	}
	private function sidebar() {
		$page = isset( $_GET['page'] ) ? $_GET['page']: '';
		?>
		<div id="sidebar-left" class="<?php echo ($this->is_mobile ? '' : 'in');?>">
			<div class="nav-collapse sidebar-nav">
				<ul class="nav nav-tabs nav-stacked main-menu">
				<?php foreach ($this->nav_items as $key => $value) {
					$has_child = isset( $value['children'] ) && is_array( $value['children'] );
					echo '<li' . ( isset( $value['class'] ) && ! empty( $value['class'] ) ? ' class="' . $value['class'] . '"' : '' ) . '><a href="'.($has_child?'#':$value['link']).'"'.($has_child?' class="dropmenu"':'').'>';
					if ( isset( $value['icon'] ) && ! empty( $value['icon'] ) )
						echo '<i class="fa fa-' . $value['icon'] . '"></i>';
					else
						echo '<i class="padd"></i>';
					echo '<span class="hidden-tablet"> '.$value['title'].'</span>';
					if ( $has_child ) echo '  <i class="fa fa-angle-down" aria-hidden="true"></i></a>';
					else echo '</a></li>';
					if ( $has_child ) {
						echo '<ul>';
						foreach ($value['children'] as $k => $v) {
							echo '<li><a href="'.$v['link'].'"><i class="icon-bar-chart"></i><span class="hidden-tablet"> '.$v['title'].'</span></a></li>';
						}
						echo '</ul></li>';
					}
				} ?>
				</ul>

			</div>
		</div>
		<?php
	}
	private function page_content() {
		if ( $this->current_page_condition && file_exists( $this->tmpl_addr . $this->current_page . '.php' ) )
			require( $this->tmpl_addr . $this->current_page . '.php'  );
	}
	private function get_link( $arg ) {
		if ( ! is_array( $arg ) )
			$arg = array( 'page' => $arg );
		return add_query_arg( $arg, $this->permalink );
	}
	private function setup_nav_links() {
		//Listing links
		$links = array();
			if ( $this->is_business_owner ) {
		/*	$links['listings'] = array(
				'title' => __( 'Listings', 'wyzi-business-finder' ),
				'link' => '#',
				'class' => 'disabled',
				'icon' => '',
				'order' => 1
			);
			$links['landing'] = array(
				'title' => __('Dashboard','wyzi-business-finder'),
				'link' => home_url('/user-account'),
				'class' => '',
				'icon' => 'cogs',
				'order' => 0
			);*/

							$links['home'] = array(
				'title' => 'Ir a Sesiones Online.com',
				'link' => 'https://sesionesonline.com/tienda',
				'class' => '',
				'icon' => 'home',  /* suitcase- institution*/
				'order' => 0
			);
	
			$links['businesses'] = array(
				'title' => 'Mi Tienda',
				'link' => '',
				'class' => '',
				'icon' => 'address-card',  /* suitcase- institution*/
				'order' => 2
			);
		/*	$links['businesses_all'] = array(
				'title' => __('Mi tienda Online','wyzi-business-finder'),
				'link' => $this->get_link( WyzQueryVars::Businesses ),
				'class' => '',
				'icon' => '',
				'parent' => 'businesses',
				'order' => 1
			); */
		$links['businesses_published'] = array(
				'title' => __('Mi tienda Online','wyzi-business-finder'),
				'link' => $this->get_link( array( 'page' => WyzQueryVars::Businesses, 'status' => 'published' ) ),
				'class' => '',
				'icon' => '',
				'parent' => 'businesses',
				'order' => 2
			);
			/*		$links['businesses_pending'] = array(
				'title' => __('Pending','wyzi-business-finder'),
				'link' => $this->get_link( array( 'page' => WyzQueryVars::Businesses, 'status' => 'pending' ) ),
				'class' => '',
				'icon' => '',
				'parent' => 'businesses',
				'order' => 3
			);*/
			if ( $this->can_add_business )
				$links['businesses_add'] = array(
					'title' => __('Crear mi perfil Profesional','wyzi-business-finder'),
					'link' => $this->get_link( array( 'page' => 'add-edit-business', WyzQueryVars::AddNewBusiness => 1 ) ),
					'class' => '',
					'icon' => '',
					'parent' => 'businesses',
					'order' => 4
				);
		}
    	if ( $this->can_offers ) {
			$links['offers'] = array(
				'title' => esc_html__( 'Offers', 'wyzi-business-finder' ),
				'link' => '#',
				'class' => '',
				'icon' => 'star-o',
				'order' => 3,
			);
			$links['offers_all'] = array(
				'title' => __('All', 'wyzi-business-finder'),
				'link' => $this->get_link( WyzQueryVars::Offers ),
				'class' => '',
				'icon' => '',
				'parent' => 'offers',
				'order' => 1,
			);
			$links['offers_published'] = array(
				'title' => __( 'Published', 'wyzi-business-finder' ),
				'link' => $this->get_link( array( 'page' => WyzQueryVars::Offers, 'status' => 'published' ) ),
				'class' => '',
				'icon' => '',
				'parent' => 'offers',
				'order' => 2,
			);
			$links['offers_pending'] = array(
				'title' => __( 'Pending', 'wyzi-business-finder' ),
				'link' => $this->get_link( array( 'page' => WyzQueryVars::Offers, 'status' => 'pending' ) ),
				'class' => '',
				'icon' => '',
				'parent' => 'offers',
				'order' => 3,
			);
			if ( $this->can_add_offer )
				$links['offers_add'] = array(
					'title' => __( 'Add New', 'wyzi-business-finder' ),
					'link' => $this->get_link( array( 'page' => 'add-edit-offer', WyzQueryVars::AddNewOffer => 1 ) ),
					'class' => '',
					'icon' => '',
					'parent' => 'offers',
					'order' => 4,
				);
		}
		if ( $this->can_jobs ) {
			$links['jobs'] = array(
				'title' => esc_html__( 'Jobs', 'wyzi-business-finder' ),
				'link' => '#',
				'class' => '',
				'icon' => 'suitcase',
				'order' => 4
			);
			$links['jobs_all'] = array(
				'title' => __('All', 'wyzi-business-finder'),
				'link' => $this->get_link( WyzQueryVars::Jobs ),
				'class' => '',
				'icon' => '',
				'parent' => 'jobs',
				'order' => 1,
			);
			if ( $this->can_add_job )
				$links['jobs_add'] = array(
					'title' => __('Add New', 'wyzi-business-finder'),
					'link' => $this->get_link( array( 'page' => 'add-edit-job', WyzQueryVars::AddJob => 1 ) ),
					'class' => '',
					'icon' => '',
					'parent' => 'jobs',
					'order' => 2,
				);
		}
		
/* link muerto no se por que : aparece en el panel un "vendor" para los vendedores y un "pagos" para los users
if ( $this->can_shop ) {
			$ttl = $this->is_business_owner ? __( 'Vendor', 'wyzi-business-finder') : __('Payments', 'wyzi-business-finder');
			$links['vendor'] = array(
				'title' => $ttl,
				'link' => '#',
				'class' => 'disabled',
				'icon' => '',
				'order' => 5
			);
		} */
	if ( !$this->can_add_business) {
			$links['products'] = array(
				'title' => esc_html( get_option( 'products_tab_label', __( 'Productos', 'wyzi-business-finder' ) ) ),
				'link' => $this->get_link( WyzQueryVars::Products ),
				'class' => '',
				'icon' => 'shopping-bag',
				'order' => 6,
			);
			$links['products_all'] = array(
				'title' => esc_html( get_option( 'products_tab_label', __( 'Products', 'wyzi-business-finder' ) ) ),
				'link' => esc_url( wcmp_get_vendor_dashboard_endpoint_url( get_wcmp_vendor_settings( 'wcmp_products_endpoint', 'vendor', 'general', 'products' ) ) ),
				'class' => '',
				'icon' => 'shopping-bag',
				'parent' => 'products',
				'order' => 1,
			);
			if ( $this->can_add_product && WyzHelpers::new_wcmp_installed() )
				$links['products_add'] = array(
					'title' => __('Add New', 'wyzi-business-finder'),
					'link' => esc_url( wcmp_get_vendor_dashboard_endpoint_url( get_wcmp_vendor_settings( 'wcmp_add_product_endpoint', 'vendor', 'general', 'add-product' ) ) ),
					'class' => '',
					'icon' => '',
					'parent' => 'products',
					'order' => 2,
				);
		}
/* Added by me */
if ( !$this->is_business_owner)  { 	/* condicionaL: solo dan fav los clientes */
$links['sesiones1'] = array(
				'title' => 'Ver Sesiones',
				'link' => '',
				'class' => '',
				'icon' => 'shopping-cart',
				'order' => 2
			);
$links['sesiones2'] = array(
				'title' => 'Todas las Sesiones',
				'link' => 'https://sesionesonline.com/tienda',
				'class' => '',
				'icon' => '',
				'order' => 101,
				'parent' => 'sesiones1'
			);
$links['sesiones3'] = array(
				'title' => 'Sesiones con descuento',
				'link' => 'https://sesionesonline.com/sesiones/descuento',
				'class' => '',
				'icon' => '',
				'order' => 101,
				'parent' => 'sesiones1'
			);
$links['sesiones4'] = array(
				'title' => 'Sesiones Gratis',
				'link' => 'https://sesionesonline.com/sesiones/gratis',
				'class' => '',
				'icon' => '',
				'order' => 102,
				'parent' => 'sesiones1'
			);
}
			
	if ( $this->is_business_owner ) {
			$links['recursos'] = array(
				'title' => 'Material gratuito',
				'link' => '',
				'class' => '',
				'icon' => 'institution',
				'order' => 98
			);	
		
			$links['recursos3'] = array(
				'title' => 'Biblioteca',
				'link' => 'https://sesionesonline.com/?s=Libros_gratis',
				'class' => '',
				'icon' => '',
				'order' => 101,
				'parent' => 'recursos'
			);
			
						$links['sesiones_all'] = array(
				'title' => 'Ver todas las Sesiones',
				'link' => 'https://sesionesonline.com/tienda',
				'class' => '',
				'icon' => 'list',  /* suitcase- institution*/
				'order' => 102
			);
		/*
			$links['recursos2'] = array(
				'title' => 'Recursos de uso libre',
				'link' => 'https://sesionesonline.com/category/recursos',
				'class' => '',
				'icon' => '',
				'order' => 101,
				'parent' => 'recursos'
			);
			
			
			
				$links['recursos4'] = array(
				'title' => 'Videos',
				'link' => 'https://sesionesonline.com/tienda',
				'class' => '',
				'icon' => '',
				'order' => 101,
				'parent' => 'recursos'
			);
				*/	  
	
					}
					
	/*	if ( $this->is_business_owner ) {
			$links['descuentos'] = array(
				'title' => '30% DE DESCUENTO EN SEO Y SEM',
				'link' => 'https://sesionesonline.com/category/recursos',
				'class' => '',
				'icon' => 'free-code-camp',
				'order' => 98
			);	
					    
					}				
		 
		 Shop - lo saco porque no da info para los users
		 if ( $this->can_shop )
			$links['shop'] = array(
				'title' => esc_html__( 'Shop', 'wyzi-business-finder' ), 
				'link' => $this->get_link( 'shop' ),
				'class' => '',
				'icon' => 'user',
				'order' => 7
			); */
	/*  dashboard (que da paso al otro panel)*/
	if ( !$this->can_add_business ) {
			$shop_settings_link = get_home_url( null,'/wcmp/' );
			if ( function_exists('wcmp_vendor_dashboard_page_id') )
				$shop_settings_link = get_page_link( wcmp_vendor_dashboard_page_id() );
			$links['shop-settings'] = array(
				'title' => esc_html__( 'Shop Settings', 'wyzi-business-finder' ),
				'link' => $shop_settings_link,
				'class' => '',
				'icon' => 'star',
				'order' => 1
			);
		}  
		elseif ( $this->show_become_vendor ){
			$links['vendor-form'] = array(
				'title' => esc_html__( 'Become a Vendor', 'wyzi-business-finder' ),
				'link' => $this->get_link( 'vendor-form' ),
				'class' => '',
				'icon' => 'money',
				'order' => 8
			);
		}  
		/* bookings (?)
		$links['bookings'] = array(
				'title' => esc_html__( 'Bookings', 'wyzi-business-finder' ),
				'link' => '#',
				'class' => 'disabled',
				'icon' => '',
				'order' => 9
			);
				if ( $this->can_booking )
			$links['appointments'] = array(
				'title' => esc_html__( 'Appointments', 'wyzi-business-finder' ),
				'link' => $this->get_link( 'appointments' ),
				'class' => '',
				'icon' => 'calendar-o',
				'order' => 10
			);
	/if ( $this->can_calendars ) {
			$links['calendars'] = array(
				'title' => esc_html__( 'Calendars', 'wyzi-business-finder' ),
				'link' => '#',
				'class' => '',
				'icon' => 'calendar',
				'order' => 11
			);
			$links['calendars_time'] = array(
				'title' => esc_html__( 'Time Slots', 'wyzi-business-finder' ),
				'link' => $this->get_link( array('page' => 'calendars', 'wz' => 1 )  ).'#defaults',
				'class' => '',
				'icon' => '',
				'parent' => 'calendars',
				'order' => 1
			);	
		/*		$links['calendars_c_time'] = array(
				'title' => esc_html__( 'Custom Time Slots', 'wyzi-business-finder' ),
				'link' => $this->get_link( array('page' => 'calendars', 'wz' => 2 ) ).'#custom-timeslots',
				'class' => '',
				'icon' => '',
				'parent' => 'calendars',
				'order' => 2
			);
			$links['calendars_c_fields'] = array(
				'title' => esc_html__( 'Custom Fields', 'wyzi-business-finder' ),
				'link' => $this->get_link( array('page' => 'calendars', 'wz' => 3 ) ).'#custom-fields',
				'class' => '',
				'icon' => '',
				'parent' => 'calendars',
				'order' => 3
			);
		} /
		if ( $this->can_inbox ) {
			$links['inboxs'] = array(
				'title' => esc_html__( 'Inbox', 'wyzi-business-finder' ),
				'link' => '#',
				'class' => '',
				'icon' => 'envelope',
				'order' => 12
			);
			$links['inbox'] = array(
				'title' => esc_html__( 'Inbox', 'wyzi-business-finder' ),
				'link' => $this->get_link( array('page' => 'inbox' )  ),
				'class' => '',
				'icon' => '',
				'parent' => 'inboxs',
				'order' => 1
			);
			$links['inbox-sent-items'] = array(
				'title' => esc_html__( 'Sent', 'wyzi-business-finder' ),
				'link' => $this->get_link( array('page' => 'inbox', 'action' => 'sent_items' ) ),
				'class' => '',
				'icon' => '',
				'parent' => 'inboxs',
				'order' => 2
			);
			$links['inbox-trash'] = array(
				'title' => esc_html__( 'Trash', 'wyzi-business-finder' ),
				'link' => $this->get_link( array('page' => 'inbox', 'action' => 'trash' ) ),
				'class' => '',
				'icon' => '',
				'parent' => 'inboxs',
				'order' => 3
			);
		}
		/*
		$links['account'] = array(
			'title' => esc_html__( 'Account', 'wyzi-business-finder' ),
			'link' => '#',
			'class' => 'disabled',
			'icon' => '',
			'order' => 13,
		);
		$links['profile'] = array(
			'title' => esc_html__( 'Editar Contrase単a', 'wyzi-business-finder' ),
			'link' => $this->get_link( 'profile' ),
			'class' => '',
			'icon' => 'cogs',
			'order' => 14,
		);*/
		if ( $this->show_subsc )
			$links['subscription'] = array(
				'title' => esc_html__( 'Subscription', 'wyzi-business-finder' ),
				'link' => $this->get_link( 'subscription' ),
				'class' => '',
				'icon' => 'credit-card',
				'order' => 15,
			);
if ( !$this->is_business_owner)   	/* condicionaL: solo dan fav los clientes */
			$links['favorite'] = array(
				'title' => esc_html__( 'Favorite', 'wyzi-business-finder' ),
				'link' => $this->get_link( 'favorite' ),
				'class' => '',
				'icon' => 'heart',
				'order' => 16
			);
	/* 
	
	$links['logout'] = array(
			'title' => esc_html__( 'Logout', 'wyzi-business-finder' ),
			'link' => wp_logout_url( home_url() ),
			'class' => '',
			'icon' => 'power-off',
			'order' => 17,
		);
*/
		$links = apply_filters( 'wyz_additional_user_dashboard_tabs', $links );
		$final_order = array();
		for( $i=0;! empty( $links ); $i++ )
			foreach ( $links as $key => $value ) {
				if ( ! isset( $value['parent'] ) ) {
					$final_order[ $key ] = $value;
					unset( $links[ $key ] );
				} elseif ( isset( $final_order[ $value[ 'parent' ] ] ) ) {
					if ( ! isset( $final_order[ $value[ 'parent' ] ]['children'] ) )
						$final_order[ $value[ 'parent' ] ]['children'] = array();
					$final_order[ $value[ 'parent' ] ]['children'][ $key ] = $value;
					unset( $links[ $key ] );
				}
			}
		usort( $final_order, array( $this, 'sort_links' ) );
		/*for ($i=0; $i<count($final_order); $i++) {
			if(('#'==$final_order[ $i ]['link']||empty($final_order[ $i ]['link']))&&(!isset($final_order[ $i ]['children'])||empty($final_order[ $i ]['children'])))
				unset( $final_order[ $i ] );
		}*/
		$this->nav_items = $final_order;
	}
	private function current_page_title() {
		echo '<h2><i class="fa fa-'.$this->page_titles[ $this->current_page ][1].'"></i>'.$this->page_titles[ $this->current_page ][0].'</h2>';
	}
	public function sort_links( $a, $b ) {
		if ( ! isset( $a['order'] ) || ! isset( $b['order'] ) )
			return -1;
		if ( $a['order'] == $b['order'] ) return 0;
		return $a['order'] < $b['order'] ? -1 : 1;
	}
	private function setup_add_links() {
		$links = array();
		if ( $this->can_add_business )
			$links[ WyzQueryVars::AddNewBusiness ] = array(
				'title' => 'Perfil Profesional',
				'link' => $this->get_link( array( 'page' => 'add-edit-business', WyzQueryVars::AddNewBusiness => 1 ) ),
				'icon' => $this->page_titles[ WyzQueryVars::Businesses ][1]
			);
		if ( $this->can_add_offer )
			$links[ WyzQueryVars::AddNewOffer ] = array(
				'title' => esc_html__( 'Offer', 'wyzi-business-finder' ),
				'link' => $this->get_link( array( 'page' => 'add-edit-offer', WyzQueryVars::AddNewOffer => 1 ) ),
				'icon' => $this->page_titles[ WyzQueryVars::Offers ][1]
			);
 
		if ( $this->can_add_product && WyzHelpers::new_wcmp_installed() ) {
			$links[ WyzQueryVars::AddProduct ] = array(
				'title' => esc_html__( 'Product', 'wyzi-business-finder' ),
				'link' => esc_url( wcmp_get_vendor_dashboard_endpoint_url( get_wcmp_vendor_settings( 'wcmp_add_product_endpoint', 'vendor', 'general', 'add-product' ) ) ),
				'icon' => $this->page_titles[ WyzQueryVars::Products ][1]
			);
		}
		if ( $this->can_add_job )
			$links[ WyzQueryVars::AddJob ] = array(
				'title' => esc_html__( 'Job', 'wyzi-business-finder' ),
				'link' => $this->get_link( array( 'page' => 'add-edit-job', WyzQueryVars::AddJob => 1 ) ),
				'icon' => $this->page_titles['jobs'][1]
			);
		$links = apply_filters( 'wyz_user_dashboard_top_add_items', $links );
		$this->top_add_items = $links;
	}
	private function footer() {
		do_action( 'wp_footer' );?>	
			</body>
		</html>
		<?php
	}
	private function handle_job_delete(){
		if ( isset( $_GET['delete_job'] ) ) {
			$red_perm = $this->get_link('jobs');
			if ( ! isset( $_GET['nonce'] ) || ! wp_verify_nonce( $_GET['nonce'], 'wyz_delete_job_' . $_GET[ 'delete_job' ] ) ) {
				wp_redirect( $red_perm );
				exit;
			}
			$post = get_post( $_GET['delete_job'] );
			if ( $post->post_author == get_current_user_id() ) {
				wp_trash_post( $_GET['delete_job'] );
			}
			wp_redirect( $red_perm );
			exit;
		}
	}
	private function check_for_businesses_no_calendars() {
		$query = new WP_Query(array(
			'post_type' => 'wyz_business',
			'posts_per_page' => -1,
			'post_status' => 'publish',
			'fields' => 'ids',
			'author' => $this->user_id
		));
		foreach( $query->posts as $id){
			if ( !WyzHelpers::get_user_calendar( $this->user_id, $id ) ) {
				$tmp_term_name = sprintf( '%s Calendar', get_the_title( $id ) );
				$term_name = $tmp_term_name;
				$i = 2;
				while( term_exists( $term_name, 'booked_custom_calendars' ) )
					$term_name = $tmp_term_name . '_' . $i++;
				$term = wp_insert_term( $term_name, 'booked_custom_calendars' );
				WyzHelpers::set_user_calendar( $id, $term['term_id'], $this->user_id );
				
				if ( is_array( $term ) ) $term = $term['term_id'];
				$user_data = get_userdata( $this->user_id );
				$email = $user_data->user_email;
				$term_meta = get_option( "taxonomy_$term" );
				$term_meta['notifications_user_id'] = $email;
				update_option( "taxonomy_$term", $term_meta );
			}
		}
	}
	private $stat_bus_posts;
	/*stats*/
	private function setup_stats_posts(){
		if ( !empty( $this->stat_bus_posts ) )
			return;
		$query = new WP_Query(array(
			'post_type' => 'wyz_business',
			'post_status' => 'publish',
			'posts_per_page' => -1,
			'fields' => 'ids',
			'author' => $this->user_id
		));
		$this->stat_bus_posts = $query->posts;
	}
	private function get_visits_count() {
		$this->setup_stats_posts();
		$count = 0;
		foreach ($this->stat_bus_posts as $id)
			$count+=WyzHelpers::get_business_visits_count($id);
		return $count;
	}
	private function get_favorites_count() {
		$this->setup_stats_posts();
		$count = 0;
		foreach ($this->stat_bus_posts as $id)
			$count+=WyzHelpers::get_business_favorite_count($id);
		return $count;
	}
	private $business_posts_data;
	private function business_posts_data() {
		if ( ! empty( $this->business_posts_data ) )return;
		$this->setup_stats_posts();
		$count=0;
		$likes=0;
		$comments=0;
		foreach ($this->stat_bus_posts as $id) {
			$all_business_posts = get_post_meta( $id, 'wyz_business_posts', true );
			if ( ! empty( $all_business_posts ) ) {
				$query = new WP_Query(array(
					'post_type' => 'wyz_business_post',
					'post__in' => $all_business_posts,
					'posts_per_page' => -1,
					'post_status' => 'publish',
					'fields' => 'ids',
				));
				$count += $query->post_count;
				foreach ($query->posts as $p) {
					$likes+=intval(get_post_meta($p,'wyz_business_post_likes_count',true));
					$tmp_c = wp_count_comments($p);
					$comments += $tmp_c->approved;
				}
			}
		}
		$this->business_posts_data = array(
			'posts_count' => $count,
			'posts_likes' => $likes,
			'posts_comments' => $comments
		);
	}
	private function get_posts_count() {
		$this->business_posts_data();
		return $this->business_posts_data['posts_count'];
	}
	private function get_comments_count() {
		$this->business_posts_data();
		return $this->business_posts_data['posts_comments'];
	}
	private function get_likes_count() {
		$this->business_posts_data();
		return $this->business_posts_data['posts_likes'];
	}
	private function get_vendor_stats() {
		if ( ! empty( $this->vendor_stats ) )
			return $this->vendor_stats;
		$this->vendor_stats = array();
		if ( function_exists( 'get_current_vendor_id' ) ) {
			global $WCMp;
			if ( isset( $_POST['wcmp_stat_start_dt'] ) ) {
			    $start_date = $_POST['wcmp_stat_start_dt'];
			} else {
			    // hard-coded '01' for first day     
			    $start_date = date( 'Y-m-01' );
			}
			if ( isset( $_POST['wcmp_stat_end_dt'] ) ) {
			    $end_date = $_POST['wcmp_stat_end_dt'];
			} else {
			    // hard-coded '01' for first day
			    $end_date = date( 'Y-m-t' );
			}
			$vendor = get_wcmp_vendor( get_current_vendor_id() );
			$WCMp_Plugin_Post_Reports = new WCMp_Report();
			$array_report = $WCMp_Plugin_Post_Reports->vendor_sales_stat_overview( $vendor, $start_date, $end_date );
			$this->vendor_stats = $array_report;
		}
		return $this->vendor_stats;
	}
	private function get_vendor_earnings() {
		$stats = $this->get_vendor_stats();
		if ( ! isset( $stats['total_vendor_earning'] ) )
			return 0;
		return $stats['total_vendor_earning'];
	}
	private function get_vendor_sales() {
		$stats = $this->get_vendor_stats();
		if ( ! isset( $stats['total_vendor_sales'] ) )
			return 0;
		return $stats['total_vendor_sales'];
	}
	private function get_vendor_sold_products() {
		$stats = $this->get_vendor_stats();
		if ( ! isset( $stats['total_purchased_products'] ) )
			return 0;
		return $stats['total_purchased_products'];
	}
	private function get_products_count() {
		if ( ! empty( $this->products_count ) )
			return $this->products_count;
		$products_query = new WP_Query(array(
			'post_type' => 'product',
			'post_status' => array( 'pending','publish'),
			'posts_per_page' => -1,
			'fields' => 'ids',
			'author' => $this->user_id
		));
		$this->products_count = 0;
		if ( $products_query->have_posts() ){
			$this->products_count = $products_query->post_count;
		}
		return $this->products_count;
	}
	private function get_rates() {
		$businesses = WyzHelpers::get_user_businesses( $this->user_id )['published'];
		$all_sum = 0;
		$all_nb = 0;
		foreach ($businesses as $id => $value) {
			$rate_nb = intval( get_post_meta( $id, 'wyz_business_rates_count', true ) );
			$rate_sum = intval( get_post_meta( $id, 'wyz_business_rates_sum', true ) );
			if ( is_nan( $rate_nb ) ) $rate_nb = 0;
			if ( is_nan( $rate_sum ) ) $rate_sum = 0;
			$all_sum+= $rate_sum;
			$all_nb += $rate_nb;
		}
		if ( ! $all_nb ) return 0;
		return $all_sum / ($all_nb*1.0);
	}
}
add_filter('show_admin_bar', '__return_false');
new WyzUserDashboard();