<?php
		
/*
Plugin Name: Booked Add-On: Calendar Feeds
Plugin URI: https://getbooked.io
Description: Adds a read-only calendar feed to the Booked plugin.
Version: 1.1.3
Author: Boxy Studio
Author URI: https://www.boxystudio.com
*/

define('BOOKEDICAL_PLUGIN_DIR', dirname(__FILE__));

$secure_hash = md5( 'booked_ical_feed_' . get_site_url() );
define('BOOKEDICAL_SECURE_HASH',$secure_hash);

// Include the required class for plugin updates.
require_once('updates/plugin-update-checker.php');
$BookedFEA_BoxyUpdateChecker = PucFactory::buildUpdateChecker('http://boxyupdates.com/get/?action=get_metadata&slug=booked-calendar-feeds', __FILE__, 'booked-calendar-feeds');

// Is Booked installed and active?
if( in_array('booked/booked.php',apply_filters('active_plugins',get_option('active_plugins')))) {
	
	if(!class_exists('bookedical_plugin')) {
		class bookedical_plugin {
			
			public function __construct() {
				
				$this->booked_screens = array('booked-feeds');
		
				add_action('init', array(&$this, 'booked_ical_feed') );
				add_action('admin_enqueue_scripts', array(&$this, 'admin_styles'));
				add_action('admin_menu', array(&$this, 'add_feeds_menu'));
			
			}
				
			public function booked_ical_feed(){
				
				if (isset($_GET['booked_ical'])):
					include(BOOKEDICAL_PLUGIN_DIR . DIRECTORY_SEPARATOR . 'calendar-feed.php');
					exit;
				endif;
				
			}
			
			// Add a New Menu Item
			public function add_feeds_menu() {
				add_submenu_page('booked-appointments', __('Calendar Feeds','booked-calendar-feeds'), __('Calendar Feeds','booked-calendar-feeds'), 'manage_options', 'booked-feeds', array(&$this, 'plugin_feeds_page'));
			}

			// Booked Feeds Page
			public function plugin_feeds_page() {
				if(!current_user_can('manage_options')) {
					wp_die(__('You do not have sufficient permissions to access this page.', 'booked-calendar-feeds'));
				}
				include(sprintf("%s/admin/feeds.php", BOOKEDICAL_PLUGIN_DIR));
			}
			
			public function admin_styles() {
				$current_page = (isset($_GET['page']) ? $_GET['page'] : false);
				$screen = get_current_screen();
				if (in_array($current_page,$this->booked_screens)):
					wp_enqueue_style('booked-icons', BOOKED_PLUGIN_URL . '/assets/css/icons.css', array(), BOOKED_VERSION);
					wp_enqueue_style('booked-admin', BOOKED_PLUGIN_URL . '/assets/css/admin-styles.css', array(), BOOKED_VERSION);
				endif;
			}

		}
	}
	
} else {
	
	// Show notice when Booked and/or WooCommerce is not active
	add_action('admin_notices', 'bookedical_required_plugins_notice');

}

add_action('plugins_loaded','init_bookedical');

function init_bookedical(){

	if(class_exists('bookedical_plugin')) {
	
		// instantiate the plugin class
		$bookedical_plugin = new bookedical_plugin();
	
	}

}

function bookedical_required_plugins_notice() {
			
	echo '<div class="update-nag">';
		echo sprintf( __('In order to use the %s plugin, you need to have %s installed and active.'), '<strong>Booked Calendar Feed</strong>', '<strong><a href="http://codecanyon.net/item/booked-appointment-booking-for-wordpress/9466968/?ref=boxystudio" target="_blank">Booked</a></strong>' );
	echo '</div>';
	
}