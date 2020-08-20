<?php
$curr_path = plugin_dir_path( __FILE__ );
$paths = array(
	'parent' 				=> 'class-parent.php',
	'booking' 				=> 'class-account-booking.php',
	'business' 				=> 'class-account-businesses.php',
	'calendar' 				=> 'class-account-business-calendar.php',
	'favorite' 				=> 'class-account-favorite.php',
	'internal-messaging' 	=> 'class-account-internal-messaging.php',
	'jobs' 					=> 'class-account-jobs.php',
	'products' 				=> 'class-account-products.php',
	'profile' 				=> 'class-account-profile.php',
	'subscription' 			=> 'class-account-subscription.php',
	'vendor' 				=> 'class-account-vendor.php',
	'woocommerce' 			=> 'class-account-woo.php',
);

foreach ($paths as $key => $value) {
	require_once( apply_filters( "wyz_user_account_require_$key", $curr_path . $value ) );
}

do_action( 'wyz_classes_after_user_account_tabs_require' );