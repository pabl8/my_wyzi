<?php

/**
 * Get business map.
 */
$business_data->the_business_map();

WyzHelpers::wyz_the_business_subheader( $id );
$bg_color = '';
if ( function_exists( 'wyz_get_option' ) ) {
	$bg_color = wyz_get_option( 'business-wall-bg-color' );
}

if ( ! empty( $bg_color ) ) {
	echo '<style>.business-tab-area,.business-tab-list ul li a {background-color:' . $bg_color . ' !important;}</style>';
}
?>

<!-- Business Tab Area
============================================ -->
<div class="business-tab-area padding-bottom-100">
	<div class="container">
		<div class="row">
			<!-- Business Tab List -->
			<div class="business-tab-list col-xs-12">
				<ul id="business-tabs">
				<?php $business_data->the_tabs();?>
				</ul>
			</div>
			<?php if ( 'on' == get_option( 'wyz_allow_business_post_edit' ) ) {
				require_once( $business_path . 'forms/post-edit-form.php');
			}
			do_action( 'wyz_under_single_bus_tabs', $id );
			?>
			<!-- Business Content Area -->
			<div class="business-sidebar-content-area margin-top-50">
				<!-- Business Sidebar -->
				<?php 
				// Lets make sure if switch sidebar option is on or off
				if ( 'off' == get_option( 'wyz_switch_sidebars_single_bus','off' ))
					WyzHelpers::the_business_sidebar( $id );
				else
					$business_data->right_sidebar('col-md-3 col-xs-12',true );
				?>
				<div class="<?php echo $business_data->get_class_resp_names_temp1();?>">
					<!-- Business Tab Content -->
					<div class="tab-content">

					<?php $business_data->the_tabs_content(); ?>

					</div>
				</div>
				<?php $business_data->right_sidebar(); ?>
			</div>
		</div>
	</div>
</div>
