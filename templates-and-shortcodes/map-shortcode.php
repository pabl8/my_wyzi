<?php



add_shortcode( 'wyz_map', 'wyz_handle_map_shortcode' );

global $wyz_map_id;
$wyz_map_id = 1;
global $script_enqueued;
$script_enqueued = false;
global $taxonomies;


function wyz_handle_map_shortcode( $attr ) {

	$attr = shortcode_atts( array( 'height'=>600,'auto-fit'=>true,'def-lat'=>'','def-lon'=>'','def-zoom'=>'','load-local'=>true,'location-filter-type'=>'dropdown','def-location'=>'','def-category'=>'','def-radius'=>'','radius-max-value'=>500,'radius-step'=>1, 'marker-type' => '1', 'sidebar' => false, 'skin'=>1, 'hide-poi' => false ), $attr );

	return wyz_get_global_map_shortcode( $attr );
}

function wyz_initialize_map_shortcode_scripts() {
	WyzMap::wyz_initialize_map_scripts(3);
	wp_register_script( 'wyz_map_shortcode', plugin_dir_url( __FILE__ ) . 'js/map-shortcode.js', WyzMap::$map_js, '', true );
}

$map_java_data = array();

function wyz_get_map_shortcode_js_data( $attr,$taxonomies ) {

	$template_type = 1;
	if ( function_exists( 'wyz_get_theme_template' ) )
		$template_type = wyz_get_theme_template();


	$radius_unit = get_option( 'wyz_business_map_radius_unit' );
	if ( 'mile' != $radius_unit ) {
		$radius_unit = 'km';
	}

	$def_loc_id =  $attr['def-location'];
	if ( '' != $def_loc_id ) {
		$coor = get_post_meta( $def_loc_id, 'wyz_location_coordinates', true );
		if(!is_array($coor))$coor=array('latitude'=>'','longitude'=>'');
		$def_loc_id = '{"id":"'.$def_loc_id.'","lat":"'. $coor['latitude'] .'","lon":"'. $coor['longitude'] .'"}';
	} else {
		$def_loc_id = -1;
	}

	$def_cat_id =  $attr['def-category'];
	if ( '' == $def_cat_id ) {
		$def_cat_id = -1;
	}

	$def_rad =  $attr['def-radius'];
	if ( '' == $def_rad ) {
		$def_rad = 0;
	}

	$map_skin = WyzMap::get_map_skin( $attr['skin'], '', $attr['hide-poi'] );
	
	$filter_type = $attr['location-filter-type'];

	if ( 'text'!=$filter_type)
		$filter_type = 'dropdown';
	$on_load_loc_req = ( 'true' == $attr['load-local'] ? 1:0);
	

	$tabs = WyzMap::get_map_tabs();

	$wyz_primary_color = function_exists('wyz_get_option')? wyz_get_option( 'primary-color' ):'';

	if ( '' == $wyz_primary_color ) {
		$wyz_primary_color = '#00aeff';
	}

	$map_java_data = array(
		'defCoor'=>array (
			'latitude'=> $attr['def-lat'],
			'longitude'=> $attr['def-lon'],
			'zoom'=> $attr['def-zoom'],
		),
		'radius' => array(
			'radiusUnit' => $radius_unit,
			'radiusDef' => $attr['def-radius'],
			'radiusMax' => $attr['radius-max-value'],
			'radiusStep' => $attr['radius-step'],
		),
		'markerType' => $attr['marker-type'],
		'sidebar' => 'true' == $attr['sidebar'] ? 1 : 0,
		'GPSLocations' => array(),
		'radFillColor'=>$wyz_primary_color,
		'radStrokeColor'=>$wyz_primary_color,
		'markersWithIcons' => array(),
		'businessNames' => array(),
		'businessLogoes' => array(),
		'businessPermalinks' => array(),
		'businessCategories' => array(),
		'businessCategoriesColors' => array(),
		'myLocationMarker' => plugin_dir_url( __FILE__ ) . 'images/iamhere.png',
		'spiderfyMarker' => plugin_dir_url( __FILE__ ) . 'images/marker-spiderfy.png',
		'locLocationMarker' => plugin_dir_url( __FILE__ ) . 'images/locationhere.png',
		'geolocation' => 'on' == wyz_get_option( 'geolocation' ),
		'taxonomies' => $taxonomies,
		'businessList' => '',
		'favEnabled' => 'on' == get_option( 'wyz_enable_favorite_business' ),
		'businessIds' => array(),
		'defLoc' => $def_loc_id,
		'defCat' => $def_cat_id,
		'autoFit' => true==$attr['auto-fit'],
		'defLogo' => WyzHelpers::get_default_image( 'business' ),
		'templateType' => $template_type,
		'favorites' => array(),
		'filterType' => $filter_type,
		'onLoadLocReq' => $on_load_loc_req,
		'translations' => array(
			'searchText' => esc_html__( 'Search here...', 'wyzi-business-finder' ),
			'viewDetails' => esc_html__( 'View Details', 'wyzi-business-finder' ),
			'viewAll' => esc_html__( 'View All', 'wyzi-business-finder' ),
			'noBusinessesFound' => esc_html__( 'No Businesses match your search', 'wyzi-business-finder' ),
			'notValidRad' => esc_html__( 'Not a valid radius', 'wyzi-business-finder' ),
			'geoFail' => esc_html__( 'The Geolocation service failed. You can still search by location radius, but not by your location.', 'wyzi-business-finder' ),
			'geoBrowserFail' => esc_html__( 'Error: Your browser doesn\'t support geolocation.', 'wyzi-business-finder' ),
		),
		'tabs' => $tabs,
		'mapSkin' => $map_skin,
	);

	return apply_filters( 'wyz_shortcode_map_js_data', $map_java_data );
}

function wyz_enqueue_map_shortcode_scripts() {
	wp_enqueue_script( 'wyz_map_shortcode' );
}

function wyz_get_global_map_shortcode( $attr ) {
	global $map_java_data;
	global $script_enqueued;
	global $wyz_map_id;
	global $taxonomies;

	if ( ! $script_enqueued ) {
		wyz_initialize_map_shortcode_scripts();
		$taxonomies = WyzHelpers::get_business_categories();
	}

	wyz_load_map_shortcode_inline_style();

	$content = wyz_get_the_shortcode_map( $attr, $taxonomies );

	$map_java_data[ $wyz_map_id++ ] = wyz_get_map_shortcode_js_data( $attr, $taxonomies );

	if ( ! $script_enqueued && $script_enqueued = true){
		add_action('wp_footer', 'wyz_enqueue_map_shortcode_scripts', 10);
		add_action('wp_footer', function(){
			global $map_java_data;
			?><script>var globalOptions = <?php echo json_encode( $map_java_data );?>;</script><?php
		}, 11);
	}
	return $content;
}
   

function wyz_get_the_shortcode_map( $attr, $taxonomies ) {

	global $template_type;
	global $wyz_map_id;


	$map_height = intval( $attr['height'] );
	$auto_fit = boolval( $attr['auto-fit'] );

	ob_start(); ?>

	<div class="map-container home-map-container map-shortcode margin-bottom-100" id="map-id__-_<?php echo $wyz_map_id;?>">

		<div id="map-loading" class="map-cssload-container">
			<div class="cssload-whirlpool"></div>
		</div>

		<!-- <div class="home-map-area section"> -->
		<div id="wyz-map-<?php echo $wyz_map_id;?>" class="home-map" style="height:<?php echo $map_height;?>px;"></div>
		<div class="map-mask" style="position:absolute;top:0; width:100%; height: <?php echo $map_height;?>px;background-color:#e4e9f5;opacity:0.6;display:none;z-index:0;"></div>
		<?php if ('true'== $attr['sidebar'] ) {?>
		<div class="hide-overflow" style="overflow: hidden;">
		<div class="slidable-map-sidebar" style="height:<?php echo intval($attr['height']);?>px;"><?php wyz_shortcode_map_sidebar($attr['height']);?></div>
		</div>
		<?php }?>

		<!-- Location Search -->
		<div class="location-search-float pt-60 pb-60">
			<div class="container">
				<div class="row">
					<div class="text-center col-xs-12">
						<div class="location-search filter-count-4">
							<?php if ( 1 == $template_type ) {?><h2><?php esc_html_e( 'search your location', 'wyzi-business-finder' );?></h2><?php }?>
							<form action="#">
								<div class="input-keyword input-location input-box"><input class="map-names" type="text" placeholder="<?php esc_html_e( 'search keywords', 'wyzi-business-finder' );?>"/>
								</div>
								<?php WyzHelpers::wyz_locations_filter( true, false, $attr['location-filter-type'], $attr['def-location'] );?>
								
								<?php WyzHelpers::wyz_categories_filter( $taxonomies, false, $attr['def-category'] );?>
								<?php $def_rad =  intval($attr['def-radius']);
								$rad_max =  intval($attr['radius-max-value']);
								$rad_step = intval($attr['radius-step']);

								if ( '' == $def_rad )
									$def_rad = 0;
								if ( '' == $rad_max )
									$rad_max = 500;
								if ( '' == $rad_step )
									$rad_step = 1;
								?>
								<div class="loc-radius-cont input-range input-location input-box last">
									<p><?php esc_html_e( 'Radius', 'wyzi-business-finder' );?>:  <span></span></p>
									<input class="loc-radius" type="range" value="<?php echo $def_rad;?>" min="0" max="<?php echo $rad_max;?>" step="<?php echo $rad_step;?>" />
								</div>
								<div class="input-submit">
									<button class="map-search-submit wyz-primary-color wyz-secon-color" type="button"><i class="fa fa-search"></i> <?php esc_html_e( 'search', 'wyzi-business-finder' );?></button>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php return ob_get_clean();
}


function wyz_shortcode_map_sidebar( $height ){

		
	echo '<div class="col-md-8"></div>';
	$can_booking = false;
	$tab_data = get_option( 'wyz_business_tabs_order_data' );
	foreach ($tab_data as $tab) {
		if( $tab['type'] == 'bookings') {
			$can_booking = true;
			break;
		}
	}
	
	$tabs = array(
		'photo' => '',
		'rating' => '',
		'booking' => '',
	);

	if ( '' == $height )
		$height = 500;
	?>

	<div class="col-md-4">
                
		<!-- Page Map Right Content -->
		<div class="page-map-right-content"  style="height:<?php echo $height;?>px;">
		
			<div class="search-wrapper">
				<a href="#" class="close-button"></a>
				<div class="map-sidebar-loading loading-spinner">
					<div class="dot1 wyz-primary-color wyz-prim-color"></div>
					<div class="dot2 wyz-primary-color wyz-prim-color"></div>
				</div>
			</div>

			<div class="map-company-info fix">
				<a href="#" class="company-logo float-left"><img src="" alt=""></a>
				<div class="content fix">
					<h4 class="map-company-info-name"><a href="#"></a></h4>
					<p class="map-company-info-slogan"></p>
					<div class="map-company-info-rating rating wyz-prim-color-txt"></div>
				</div>
			</div>

			<ul class="map-info-links fix">
				<?php if('on' == get_option( 'wyz_enable_favorite_business' )){
				$fav_log_cls = is_user_logged_in() ? '' : ' fav-no-log';?>
				<li <?php if(!$can_booking)echo'class="three-way-width"';?>><a href="#" class="ajax-click fav-bus<?php echo $fav_log_cls;?>" data-action="1" data-busid="">
					<i class="fa fa-heart-o"></i><span><?php esc_html_e( 'Favorite', 'wyzi-business-finder' );?></span></a>
				</li>
				<?php }
				$permalink = '';
				$rate_url = $permalink . '#' . $tabs['rating'];
				$booking_url = $permalink . '#' . $tabs['booking'];
				?>
				<li <?php if(!$can_booking)echo'class="three-way-width"';?>><a href="<?php echo $rate_url;?>" class="rate-bus"><i class="fa fa-star-o"></i><span><?php esc_html_e( 'Rate List', 'wyzi-business-finder' );?></span></a></li>
				<li <?php if(!$can_booking)echo'class="three-way-width"';?>><a class="map-share-btn" href="#"><i class="fa fa-share-alt"></i><span><?php esc_html_e( 'Share List', 'wyzi-business-finder' );?></span></a></li>
				<?php if ( $can_booking ) {?>
				<li><a href="<?php echo $booking_url;?>" class="book-bus"><i class="fa fa-calendar-o"></i><span><?php esc_html_e( 'Book List', 'wyzi-business-finder' );?></span></a></li>
				<?php }?>
			</ul>
			<ul class="map-info-gallery fix">
			</ul>

		</div>

	</div>
		<?php
}

global $map_shortcode_script_loaded;
$map_shortcode_inline_style_loaded = false;
function wyz_load_map_shortcode_inline_style() {
	global $map_shortcode_inline_style_loaded;
	if ( $map_shortcode_inline_style_loaded )return;
	$map_shortcode_inline_style_loaded = true;

	$fields_count = 5;

	echo '<style>';
	wyz_map_shortcode_css_dyn( array(12,10), $fields_count, 5 );
	wyz_map_shortcode_css_dyn( array(9,8), $fields_count, 3 );
	wyz_map_shortcode_css_dyn( array(6), $fields_count, 2 );
	wyz_map_shortcode_css_dyn_sml( array(4,3,2), $fields_count, 1 );?>
	@media only screen and (max-width: 991px) {
		<?php wyz_map_shortcode_css_dyn_sml( array(6), $fields_count, 1 );?>
	}

	@media only screen and (max-width: 767px) {
		<?php wyz_map_shortcode_css_small( array(12,10,9,8,6,4,3,2), $fields_count, 1 );?>
	}
	<?php echo '</style>';
}


function wyz_map_shortcode_css_dyn( $columns, $fields_count, $count ) {
	global $template_type;
	$perc = 100.0/$count;

	if ( $count >1)$perc--;
	for($i=0;$i<count($columns);$i++) {
		echo '.vc_col-sm-' . $columns[ $i ].' .input-location,';
		echo '.vc_col-sm-' . $columns[ $i ].' .input-submit';
		if ( $i != count($columns)-1)
			echo ',';
	}
	echo "{width:" . ($perc) . "% !important;}";

	if ( 2 == $template_type && $perc > 33.33  ) {?>
		.vc_column_container .location-search .input-submit{
			display: inline-block;
			float: none;
		}
		
		<?php
		$entered = false;
		for($i=0;$i<count($columns);$i++) {
			if ( $columns[ $i ] <=6 ) {
				echo '.vc_col-sm-' . $columns[ $i ].' .location-search >form';
				if ( $i != count($columns)-1)
					echo ',';
				$entered = true;
			}
		}
		if ( $entered ) {
			echo '{ border-radius:0; }';
		}
	}

	for($i=0;$i<count($columns);$i++) {
		echo '.vc_col-sm-' . $columns[ $i ].' .input-location,';
		echo '.vc_col-sm-' . $columns[ $i ].' .input-submit';
		if ( $i != count($columns)-1)
			echo ',';
	}
	echo "{padding-right: 0 !important;margin-right: 0 !important;}";

	if ( $perc<21)
		$sbw=40;
	elseif($perc<40)
		$sbw=50;
	elseif($perc<60)
		$sbw=70;
	else
		$sbw=100;

	for($i=0;$i<count($columns);$i++) {
		echo '.vc_col-sm-' . $columns[ $i ].' .slidable-map-sidebar .col-md-4';
		if ( $i != count($columns)-1)
			echo ',';
	}
	echo "{width:".$sbw."% !important;}";
}


function wyz_map_shortcode_css_dyn_sml( $columns, $fields_count, $count ) {
	for($i=0;$i<count($columns);$i++) {
		echo '.vc_col-sm-' . $columns[ $i ].' .input-location,';
		echo '.vc_col-sm-' . $columns[ $i ].' .input-submit';
		if ( $i != count($columns)-1)
			echo ',';
	}
	echo "{width:100% !important;margin-bottom:10px !important;}";

	for($i=0;$i<count($columns);$i++) {
		echo '.vc_col-sm-' . $columns[ $i ].' .slidable-map-sidebar .col-md-4';
		if ( $i != count($columns)-1)
			echo ',';
	}
	echo "{width:100% !important;}";
}


function wyz_map_shortcode_css_small( $columns ) {
	for($i=0;$i<count($columns);$i++) {
		echo '.vc_col-sm-' . $columns[ $i ].' .input-location,';
		echo '.vc_col-sm-' . $columns[ $i ].' .input-submit';
		if ( $i != count($columns)-1)
			echo ',';
	}
	echo "{width:100% !important;}";

	 echo '.vc_column_container .input-submit{width:100%}';

	for($i=0;$i<count($columns);$i++) {
		echo '.vc_col-sm-' . $columns[ $i ].' .input-location,';
		echo '.vc_col-sm-' . $columns[ $i ].' .input-submit';
		if ( $i != count($columns)-1)
			echo ',';
	}
	echo "{padding-righ: 0 !important;margin-bottom:10px !important;}";

	for($i=0;$i<count($columns);$i++) {
		echo '.vc_col-sm-' . $columns[ $i ].' .slidable-map-sidebar .col-md-4';
		if ( $i != count($columns)-1)
			echo ',';
	}
	echo "{width:100% !important;}";
}
