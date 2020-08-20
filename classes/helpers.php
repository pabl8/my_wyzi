<?php
/**
 * Assistant functions
 *
 * @package wyz
 */

/**
 * Class WyzHelpers.
 */
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

if (class_exists('WyzHelpersOverride')) {
	class WyzHelpersOverridden extends WyzHelpersOverride { }
} else {
	class WyzHelpersOverridden { }
}


class WyzHelpers extends WyzHelpersOverridden
{

	private static $open_all_day_msg;
	private static $closed_all_day_msg;
	// 30 minutes
	private static $VISITS_TIMEOUT_DURATION = 1800;
	public static $STICKY_POSTS = false;
	private static $Businesses_Ratings;

	public static $allowed_mimes = array( 'image/jpg', 'image/jpeg', 'image/png', 'video/mp4', 'application/pdf', 'application/zip', 'application/x-zip-compressed', 'application/x-gzip', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' );

	/**
	 * Create the business sidebar.
	 *
	 * @param integer $id the business id.
	 */	


	public static function the_business_sidebar( $id ) {
		if ( method_exists( 'WyzHelpersOverride', 'the_business_sidebar') ) {
			return WyzHelpersOverride::the_business_sidebar( $id );
		}
		global $current_user;
		global $template_type;

		wp_get_current_user();

		$about = strip_shortcodes( self::get_about( $id ) );

		/* Opening/Closing times. */
		$days = self::get_days( $id );
		$days_names = $days[0];
		$days_arr = $days[1];

		$author_id = self::wyz_the_business_author_id();

		$address = self::get_address( $id );
		$phone = self::get_phone( $id, $author_id );
		$email = self::get_email( $id, $author_id );
		$website = get_post_meta( $id, 'wyz_business_website', true );

		$no_days_data = true;

		for ( $i=0; $i<7; $i++)
			if ( ! empty( $days_arr[ $i ] ) ){
				$no_days_data = false;
				break;
			}


		if ( 'off' == get_option( 'wyz_switch_sidebars_single_bus','off' ) && 'on' !== get_option('wyz_hide_extra_sidebar_single_bus') ) 
			$coulmns_class_name =  'on' === wyz_get_option( 'resp' ) ? 'col-md-3 col-xs-12' : 'col-xs-3';

		elseif( 'off' != get_option( 'wyz_switch_sidebars_single_bus','off' ) && 'on' !== get_option('wyz_hide_extra_sidebar_single_bus'))
			$coulmns_class_name =  'on' === wyz_get_option( 'resp' ) ? 'col-md-3 col-xs-12' : 'col-xs-3';

		elseif ('off' == get_option( 'wyz_switch_sidebars_single_bus','off' ) && 'on' == get_option('wyz_hide_extra_sidebar_single_bus'))
			$coulmns_class_name =  'on' === wyz_get_option( 'resp' ) ? 'col-md-4 col-xs-12' : 'col-xs-4';

		else
			$coulmns_class_name =  'on' === wyz_get_option( 'resp' ) ? 'col-md-4 col-xs-12' : 'col-xs-4';

		if ( $template_type == 2 ) {
			self::the_business_sidebar_2( $id, $days_names, $days_arr, $author_id, $about, $address, $phone, $email, $website, $no_days_data );
		} else {
			self::the_business_sidebar_1( $id, $days, $days_names, $days_arr, $author_id, $about, $address, $phone, $email, $website, $no_days_data,$coulmns_class_name );
		}
	}


	public static function the_business_sidebar_1 ( $id, $days, $days_names, $days_arr, $author_id, $about, $address, $phone, $email, $website, $no_days_data, $coulmns_class_name ) {
		if ( method_exists( 'WyzHelpersOverride', 'the_business_sidebar_1') ) {
			return WyzHelpersOverride::the_business_sidebar_1( $id, $days, $days_names, $days_arr, $author_id, $about, $address, $phone, $email, $website, $no_days_data, $coulmns_class_name );
		}
		ob_start();
		?>
		<!-- Business Sidebar -->
				
		<div class="business-sidebar <?php echo $coulmns_class_name;?>">
		<?php
		if ( is_sticky() ) {
			echo '<div class="sticky-notice"><span class="wyz-primary-color">' . esc_html__( 'featured', 'wyzi-business-finder' ) . '</span></div>';
		}
		$sidebar_order_data = get_option( 'wyz_business_sidebar_order_data' ); 
		
		
		foreach ( $sidebar_order_data as $key => $tab ) { 
			
			switch ( $tab['type']  ) {

				case 'About':
					// About
					if ( self::wyz_sub_can_bus_owner_do( $author_id,'wyzi_sub_business_show_description') ) {
					?>
						<!-- About Business Sidebar -->
						<div class="sin-busi-sidebar<?php echo (!empty($tab['cssClass']) ) ? " " . $tab['cssClass'] : '';?>">
							<?php if (!empty($tab['title'])) { ?>
							<h4 class="sidebar-title"><?php echo esc_html( $tab['title']  );?></h4>
							<?php } ?>
							<div class="about-business-sidebar fix">
								<div class="desc-see-more"><p><?php echo $about;?> </p></div>
							</div>
						</div>
						<?php }
				break;

				case 'Opening_Hours':
					//Opening Hours
					if ( self::wyz_sub_can_bus_owner_do( $author_id,'wyzi_sub_business_show_opening_hours') ) {
						if ( ! $no_days_data ) { ?>
						<!-- Opening Hours Business Sidebar -->
						<div class="sin-busi-sidebar<?php echo (!empty($tab['cssClass']) ) ? " " . $tab['cssClass'] : '';?>">
							<?php if (!empty($tab['title'])) { ?>
							<h4 class="sidebar-title"><?php echo esc_html( $tab['title']  );?></h4>
							<?php } ?>
							<div class="opening-hours-sidebar fix">
							<?php
							for( $i=0; $i<7; $i++)
								self::wyz_display_time( $days_arr[ $i ], $days_names[ $i ] );
							?>
							</div>
						</div>
						<?php } } 
						do_action( 'wyz_sidebar_after_days', $id, $author_id );
				break;

				case 'Contact_Info':

					self::get_contact_info_widget_template1( $author_id, $phone, $address, $website, $email, $tab['title'],$tab['cssClass'] );
					
				break;

				case 'Map':
					// Map
					 if ( 'image' == get_option( 'wyz_business_header_content' ) &&  WyzHelpers::wyz_sub_can_bus_owner_do( $author_id,'wyzi_sub_business_show_map') ) { ?>
					 	<?php if (!empty($tab['title'])) { ?>
							<h4 class="sidebar-title"><?php echo esc_html( $tab['title']  );?></h4>
							<?php } ?>
						<div class="sin-busi-sidebar<?php echo (!empty($tab['cssClass']) ) ? " " . $tab['cssClass'] : '';?>">
							<?php WyzMap::wyz_the_business_map( $id, true ); ?>
						</div>
					<?php }
				break;

				case 'Social_Media':
					// Social Media
					if ( self::wyz_sub_can_bus_owner_do( $author_id,'wyzi_sub_business_show_social_media') ) {
						self::social_links( $id, $tab['title'], $tab['cssClass'] );
					}
				break;
				
				case 'Tags':
				// Tags
				?>
					<div id="sticky-sidebar">
					<?php 
					if ( self::wyz_sub_can_bus_owner_do( $author_id,'wyzi_sub_business_show_business_tags') ) {
						if ( $tags = get_the_term_list( $id, 'wyz_business_tag', '', ', ' ) ) {?>
							<div class="sin-busi-sidebar<?php echo (!empty($tab['cssClass']) ) ? " " . $tab['cssClass'] : '';?>">
								<?php if (!empty($tab['title'])) { ?>
								<h4 class="sidebar-title"><?php echo esc_html( $tab['title']  );?></h4>
								<?php } ?>
								<div class="tags-sidebar">
									<?php echo $tags;?>
								</div>
							</div>
					<?php }
					}
					?>
					</div>
					<?php 
				break;

				case 'Claim':
					//claim
					if ( 'off' != get_option( 'wyz_business_claiming' ) && 'yes' != get_post_meta( $id, 'wyz_business_claimed', true ) ) { ?>
						<?php if (!empty($tab['title'])) { ?>
						<h4 class="sidebar-title"><?php echo esc_html( $tab['title']  );?></h4>
						<?php } ?>
						<div class="single-claim-container<?php echo (!empty($tab['cssClass']) ) ? " " . $tab['cssClass'] : '';?>"> <?php
						echo '<a href="' . home_url( '/claim/?id=' ) . $id .'" class="light-blue-link wyz-primary-color-text">' . esc_html__( 'Claim this Business', 'wyzi-business-finder' ) . '</a>'; ?>
						</div> <?php
					}
				break;

				case 'Recent_Ratings':
					$all_business_rates = get_post_meta( $id, 'wyz_business_ratings', true );
					if ( empty($all_business_rates))$all_business_rates = array(-1);
					$args = array(
						'post_type' => 'wyz_business_rating',
						'post__in' => $all_business_rates,
						'posts_per_page' => 3,
						//'paged' => $page,
					);
					$rate_query = new WP_Query( $args );

					$first_id = - 1;
					if ( $rate_query->have_posts() ) {?>
					<!-- Sidebar Widget -->
					<div class="sin-busi-sidebar<?php echo (!empty($tab['cssClass']) ) ? " " . $tab['cssClass'] : '';?>">
						<!--Widget Title-->
						<?php if (!empty($tab['title'])) { ?>
						<h4 class="sidebar-title"><?php echo esc_html( $tab['title']  );?></h4>
						<?php } ?>
						<!-- Rating Widget -->
						<div class="recent-rating-widget">

						<?php while ( $rate_query->have_posts() ) {
							$rate_query->the_post();
							$rate_id = get_the_ID();
							$first_id = $rate_id;
							echo WyzBusinessRating::wyz_create_rating( $rate_id, 2 );
						}
						wp_reset_postdata(); ?>
						</div>
					</div>


						<?php
					}
				break;

				case 'All_Ratings':

					$all_business_rates = get_post_meta( $id, 'wyz_business_ratings', true );
					if ( empty($all_business_rates))$all_business_rates = array(-1);
					$args = array(
						'post_type' => 'wyz_business_rating',
						'post__in' => $all_business_rates,
						'posts_per_page' => 3,
						//'paged' => $page,
					);
					$rate_query = new WP_Query( $args );

					$first_id = - 1;
					if ( $rate_query->have_posts() ) {?>
						<div class="sin-busi-sidebar<?php echo (!empty($tab['cssClass']) ) ? " " . $tab['cssClass'] : '';?>">
						<!--Widget Title-->
						<?php $rate_stats = WyzBusinessRating::get_business_rates_stats( $id );?>
						<?php if (!empty($tab['title'])) { ?>
						<h4 class="sidebar-title" style="float: left;"><?php echo esc_html( $tab['title']  );?></h4>
						<?php } ?>
						<!-- Rating Widget -->
						<div class="rating-widget all-ratings-widget">
							<div class="single-rating fix">
								<div class="head fix">
									<?php echo WyzBusinessRating::get_business_rates_stars( $id, $display_count = true, $rate_stats );?>
								</div>
							</div>

							<?php echo WyzBusinessRating::get_business_rates_cats_perc( $id, $all_business_rates, $rate_stats['rate_nb'] );?>

						</div>
					</div>
				<?php }

				break;

				default:
					 // nothing as defalult
				break;
			}
		}
			//End of Business Sidebar
			do_action( 'wyz_after_the_business_sidebar', $id, $author_id );

			?>
		</div>

		<?php 
		echo ob_get_clean();
	}

	public static function the_business_sidebar_2 ( $id, $days_names, $days_arr, $author_id, $about, $address, $phone, $email, $website, $no_days_data ) {

		if ( method_exists( 'WyzHelpersOverride', 'the_business_sidebar_2') ) {
			return WyzHelpersOverride::the_business_sidebar_2( $id, $days_names, $days_arr, $author_id, $address, $phone, $email, $website, $no_days_data );
		}

		$can_address = self::wyz_sub_can_bus_owner_do( $author_id,'wyzi_sub_business_show_address') && ! empty( $address );
		$can_email = self::wyz_sub_can_bus_owner_do( $author_id,'wyzi_sub_business_show_email_1') && ! empty( $email ) && '<a href="mailto:" target="_blank"></a>' != $email;
		$can_phone = self::wyz_sub_can_bus_owner_do( $author_id,'wyzi_sub_business_show_phone_1') && ! empty( $phone );
		$can_website = self::wyz_sub_can_bus_owner_do( $author_id,'wyzi_sub_business_show_website_url') && ! empty( $website );
		$can_info_tab = $can_address || $can_email || $can_phone || $can_website ;
		ob_start();?>
		<!-- Business Sidebar -->
		<div class="sidebar-wrapper<?php if ( 'off' === wyz_get_option( 'resp' ) ) { ?> col-xs-4 <?php } else { ?> col-md-4 col-xs-12<?php } ?>">
		<?php 
		if ( 'on' == get_option( 'wyz_switch_sidebars_single_bus','off' ) && 'on' !== get_option('wyz_hide_extra_sidebar_single_bus') ) {
			if ( is_active_sidebar( 'wyz-single-business-sb' ) ) :
				dynamic_sidebar( 'wyz-single-business-sb' );
			endif; 
			}
		if ( is_sticky() ) {
			echo '<div class="sticky-notice"><span class=" wyz-prim-color">' . esc_html__( 'featured', 'wyzi-business-finder' ) . '</span></div>';
		}
		$sidebar_order_data = get_option( 'wyz_business_sidebar_order_data' ); 

		foreach ( $sidebar_order_data as $key => $tab ) { 

			switch ( $tab['type']  ) {
				case 'About':
					// About
					if ( self::wyz_sub_can_bus_owner_do( $author_id,'wyzi_sub_business_show_description') ) {
					?>
						<!-- About Business Sidebar -->
						<div class="widget widget_text<?php echo (!empty($tab['cssClass']) ) ? " " . $tab['cssClass'] : '';?>">
							<?php if (!empty($tab['title'])) { ?>
							<h4 class="widget-title"><?php echo esc_html( $tab['title']  );?></h4>
							<?php } ?>
							<div class="about-business-sidebar fix">
								<div class="desc-see-more"><p><?php echo $about;?> </p></div>
							</div>
						</div>
						<?php }
				break;
				case 'Contact_Info':
					// Contact Information
					if ( self::wyz_sub_can_bus_owner_do( $author_id,'wyzi_sub_business_show_contact_information_tab') && $can_info_tab ) { ?>
							<!-- Contact Business Sidebar -->
						<div class="widget widget_text<?php echo (!empty($tab['cssClass']) ) ? " " . $tab['cssClass'] : '';?>">
							<?php if (!empty($tab['title'])) { ?>
							<h4 class="widget-title"><?php echo esc_html( $tab['title']  );?></h4>
							<?php } ?>

							<div class="contact-info-widget">
								<?php if ( $can_address ) { ?>
								<div class="single-info fix">
									<h5><?php esc_html_e( 'Address', 'wyzi-business-finder' );?></h5>
									<p><?php echo esc_html( $address );?></p>
								</div>
								<?php } 
								if ( $can_email ) { ?>
								<div class="single-info fix">
									<h5><?php esc_html_e( 'E-mail', 'wyzi-business-finder' );?></h5>
									<p><?php echo $email; ?></p>
								</div>
								<?php }
								if ( $can_phone ) { ?>
								<div class="single-info fix">
									<h5><?php esc_html_e( 'Phone', 'wyzi-business-finder' );?></h5>
									<p><?php echo $phone; ?></p>
								</div>
								<?php }
								if ( $can_website ) {?>
								<div class="single-info fix">
									<h5><?php esc_html_e( 'Website', 'wyzi-business-finder' );?></h5>
									<p class="website"><a target="_blank" href="<?php  echo esc_url( $website ); ?>"><?php echo esc_html( $website ); ?></a></p>
								</div>
								<?php } ?>
							</div>
						</div>

					<?php }
				break;

				case 'Map':
					// Map
					if ( 'image' == get_option( 'wyz_business_header_content' ) &&  WyzHelpers::wyz_sub_can_bus_owner_do( $author_id,'wyzi_sub_business_show_map') ) { ?>
						<div class="widget<?php echo (!empty($tab['cssClass']) ) ? " " . $tab['cssClass'] : '';?>">
							<?php if (!empty($tab['title'])) { ?>
							<h4 class="widget-title"><?php echo esc_html( $tab['title']  );?></h4>
							<?php } ?>
							<?php WyzMap::wyz_the_business_map( $id, true ); ?>
						</div>
					<?php }
				break;

				case 'Recent_Ratings':
					// Recent Ratings
					$all_business_rates = get_post_meta( $id, 'wyz_business_ratings', true );
					if ( empty($all_business_rates))$all_business_rates = array(-1);
					$args = array(
						'post_type' => 'wyz_business_rating',
						'post__in' => $all_business_rates,
						'posts_per_page' => 3,
						//'paged' => $page,
					);
					$query = new WP_Query( $args );

					$first_id = - 1;
					if ( $query->have_posts() ) {?>
					<!-- Sidebar Widget -->
					<div class="widget<?php echo (!empty($tab['cssClass']) ) ? " " . $tab['cssClass'] : '';?>">
						<!--Widget Title-->
						<?php if (!empty($tab['title'])) { ?>
						<h4 class="widget-title"><?php echo esc_html( $tab['title']  );?></h4>
						<?php } ?>
						<!-- Rating Widget -->
						<div class="rating-widget">

						<?php while ( $query->have_posts() ) {
							$query->the_post();
							$rate_id = get_the_ID();
							$first_id = $rate_id;
							echo WyzBusinessRating::wyz_create_rating( $rate_id, 2 );
						}
						wp_reset_postdata(); ?>
						</div>
					</div>
					<?php }
				break;

				case 'All_Ratings':
					// All Ratings
					$all_business_rates = get_post_meta( $id, 'wyz_business_ratings', true );
					if ( empty($all_business_rates))$all_business_rates = array(-1);
					$args = array(
						'post_type' => 'wyz_business_rating',
						'post__in' => $all_business_rates,
						'posts_per_page' => 3,
						//'paged' => $page,
					);
					$query = new WP_Query( $args );

					$first_id = - 1;
					if ( $query->have_posts() ) {?>

					<div class="widget<?php echo (!empty($tab['cssClass']) ) ? " " . $tab['cssClass'] : '';?>">
						<!--Widget Title-->
						<?php $rate_stats = WyzBusinessRating::get_business_rates_stats( $id );?>
						<?php if (!empty($tab['title'])) { ?>
						<h4 class="widget-title" style="float:left;"><?php echo esc_html( $tab['title']  );?></h4>
						<?php } ?>
						<!-- Rating Widget -->
						<div class="rating-widget all-ratings-widget">
							<div class="single-rating fix">
								<div class="head fix">
									<?php echo WyzBusinessRating::get_business_rates_stars( $id, $display_count = true, $rate_stats );?>
								</div>
							</div>

							<?php echo WyzBusinessRating::get_business_rates_cats_perc( $id, $all_business_rates, $rate_stats['rate_nb'] );?>

						</div>
					</div>


						<?php }
					
					global $business_data;
					if ( '' != $business_data && property_exists( $business_data, 'rate_form' ) )
						$business_data->rate_form();
				break;

				case 'Opening_Hours':

					// Opening Houra
					if ( self::wyz_sub_can_bus_owner_do( $author_id,'wyzi_sub_business_show_opening_hours') && ! $no_days_data ) {?>
					<div class="widget<?php echo (!empty($tab['cssClass']) ) ? " " . $tab['cssClass'] : '';?>">
						<!--Widget Title-->
						<?php if (!empty($tab['title'])) { ?>
						<h4 class="widget-title"><?php echo esc_html( $tab['title']  );?></h4>
						<?php } ?>
						<!-- Opening Time Widget -->
						<div class="open-time-widget">
							<ul>
								<?php

							for( $i=0; $i<7; $i++)
								self::wyz_display_time( $days_arr[ $i ], $days_names[ $i ] );

								
								?>
							</ul>
						</div>
					</div>
					<?php
					}
				break;

				case 'Tags':
					// Tags
					if ( self::wyz_sub_can_bus_owner_do( $author_id,'wyzi_sub_business_show_business_tags') ) {
						if ( $tags = get_the_term_list( $id, 'wyz_business_tag', '', '' ) ) {?>

						<div class="widget<?php echo (!empty($tab['cssClass']) ) ? " " . $tab['cssClass'] : '';?>">
							<!--Widget Title-->
							<?php if (!empty($tab['title'])) { ?>
							<h4 class="widget-title"><?php echo esc_html( $tab['title']  );?></h4>
							<?php } ?>
								<!-- Tags Widget -->
							<div class="tag-widget fix">
								<?php echo $tags;?>
							</div>
						</div>
					<?php }
					}
				break;

				case 'Social_Media':

					// Social Media
					if ( self::wyz_sub_can_bus_owner_do( $author_id,'wyzi_sub_business_show_social_media') ) {
						self::social_links( $id, $tab['title'], $tab['cssClass'] ); }
				break;

				case 'Claim':
				// Claim 
				if ( 'off' != get_option( 'wyz_business_claiming' ) && 'yes' != get_post_meta( $id, 'wyz_business_claimed', true ) ) { ?>
						<div class="widget<?php echo (!empty($tab['cssClass']) ) ? " " . $tab['cssClass'] : '';?>"> 
						<?php if (!empty($tab['title'])) { ?>
						<h4 class="widget-title"><?php echo esc_html( $tab['title']  );?></h4>
						<?php } ?>
						<?php
						echo '<a href="' . home_url( '/claim/?id=' ) . $id .'" class="light-blue-link wyz-primary-color-text">' . esc_html__( 'Claim this Business', 'wyzi-business-finder' ) . '</a>'; ?>
						</div> <?php
					}
				break;
				default:
					 // nothing as defalult
				break;
			}
		}
		if ( 'off' == get_option( 'wyz_switch_sidebars_single_bus','off' ) && 'on' !== get_option('wyz_hide_extra_sidebar_single_bus') ) {
			if ( is_active_sidebar( 'wyz-single-business-sb' ) ) :
				dynamic_sidebar( 'wyz-single-business-sb' );
			endif;
		} ?>
		</div>
		<?php echo ob_get_clean();
	}

	public static function get_contact_info_widget_template1( $author_id, $phone, $address, $website, $email,$title, $tabCustomClass ) {
	if ( method_exists( 'WyzHelpersOverride', 'get_contact_info_widget_template1') ) {
		return WyzHelpersOverride::get_contact_info_widget_template1( $author_id, $phone, $address, $website, $email, $title, $tabCustomClass );
	} 
			$result = '';
			if ( self::wyz_sub_can_bus_owner_do( $author_id,'wyzi_sub_business_show_contact_information_tab') &&
						( '' != $phone || '' != $address || '<a href="mailto:" target="_blank"></a>' != $email || '' != $website) ) {
					$result .= '<!-- Contact Business Sidebar -->'	;
					$result .= '<div class="sin-busi-sidebar';
					if (!empty($tabCustomClass) )
					$result .=  " " . $tabCustomClass;
					$result .= '">';
					if (!empty($title)){
						$result .= '<h4 class="sidebar-title">';
						$result .= 	esc_html( $title ). '</h4>';
					}
					$result .= '<div class="contact-info-sidebar fix">';		
							
					
					if ( self::wyz_sub_can_bus_owner_do( $author_id,'wyzi_sub_business_show_phone_1') ) { 
								if ( '' != $phone ) {
									$result .= '<p class="phone">' . $phone . '</p>';
								} 
					}
					if ( self::wyz_sub_can_bus_owner_do( $author_id,'wyzi_sub_business_show_address') ) {
								if ( '' != $address ) {
									$result .= '<p class="address">' . esc_html( $address );
								}
					
						$result .= '</p>';
					} 
					if ( self::wyz_sub_can_bus_owner_do( $author_id,'wyzi_sub_business_show_email_1') ) {
								if ( '' != $email ) {
					
								$result .= '<p class="email">' . $email . '</p>';
					 }} 
					
					if ( self::wyz_sub_can_bus_owner_do( $author_id,'wyzi_sub_business_show_website_url') ) {
								if ( '' !== $website ) {
								$result .= '<p class="website"><a target="_blank" href="' . esc_url( $website ) . '">' . esc_html( $website ) . '</a></p>';
								
								}
					} 
								
							$result .=  '</div></div>';
						

					}


		echo $result;


	}


	private static function get_sidebar_business_map() {

	}

	private static function get_about( $id ) {
		$logged_in_user = is_user_logged_in();
		$about = get_post_meta( $id, 'wyz_business_description', true );
		$about = preg_replace("/<img[^>]+\>/i", " ", $about);
		$about = preg_replace("/<div[^>]+>/", "", $about);
		$about = preg_replace("/<\/div[^>]+>/", "", $about);
		$about = self::wyz_strip_tags( $about, '<table><td><tr><th>' );
		if ( is_singular( 'wyz_offers' ) ) 
			$about_link = get_permalink( $id ) . '#about';
		else
			$about_link = '#about';
		$about = self::substring_excerpt($about, 150 ) . '<a href="' . $about_link . '" class="read-more wyz-secondary-color-text wyz-prim-color-txt">' . esc_html__( 'show more', 'wyzi-business-finder' ) . '</a>';
		$about = wyz_split_glue_link( array( $about, false ) );
		return $about[0][0];
	}


	public static function get_days( $id ) {
		if ( method_exists( 'WyzHelpersOverride', 'get_days') ) {
			return WyzHelpersOverride::get_days( $id );
		}
		self::get_open_closed_all_day_msg();
		$days_arr = array();
		$days_names = array(
			esc_html__( 'Mon', 'wyzi-business-finder' ),
			esc_html__( 'Tue', 'wyzi-business-finder' ),
			esc_html__( 'Wed', 'wyzi-business-finder' ),
			esc_html__( 'Thu', 'wyzi-business-finder' ),
			esc_html__( 'Fri', 'wyzi-business-finder' ),
			esc_html__( 'Sat', 'wyzi-business-finder' ),
			esc_html__( 'Sun', 'wyzi-business-finder' ),
		);
		$days_ids = array( 'open_close_monday', 'open_close_tuesday', 'open_close_wednesday',
					'open_close_thursday', 'open_close_friday', 'open_close_saturday', 'open_close_sunday' );
		for( $i=0; $i<7; $i++) {
			$open_status = get_post_meta( $id, 'wyz_' . $days_ids[ $i ] . '_status', true );
			switch ( $open_status ) {
				case 'open_all_day':
					$days_arr[] = array( 'class' => 'oad-time', 'status' => self::$open_all_day_msg );
					break;
				case 'closed_all_day':
					$days_arr[] = array( 'class' => 'cad-time', 'status' => self::$closed_all_day_msg );
					break;
				default:
					$days_arr[] = self::wyz_set_time( get_post_meta( $id, 'wyz_' . $days_ids[ $i ], true ) );
			}
		}

		return array( $days_names, $days_arr );
	}


	public static function get_address( $id ) {
		if ( method_exists( 'WyzHelpersOverride', 'get_address') ) {
			return WyzHelpersOverride::get_address( $id );
		}
		$prefix = 'wyz_';

		$bldg = get_post_meta( $id, $prefix . 'business_bldg', true );
		$street = get_post_meta( $id, $prefix . 'business_street', true );
		$zipcode = get_post_meta( $id, $prefix . 'business_zipcode', true );
		$city = get_post_meta( $id, $prefix . 'business_city', true );
		$country = get_post_meta( $id, $prefix . 'business_country', true );
		if ( '' != $country )
			$country = get_the_title( $country );
		else $country = '';
		$additional_address = get_post_meta( $id, $prefix . 'business_addition_address_line', true );
		$address = '';
		if ( '' !== $bldg ) {
			$address .= $bldg . ', ';
		}
		if ( '' !== $street ) {
			$address .=  $street . ', ';
		}
		if ( '' !== $zipcode ) {
			$address .=  $zipcode . ', ';
		}
		if ( '' !== $city ) {
			$address .= $city . ', ';
		}
		if ( '' !== $country ) {
			$address .= $country . ', ';
		}
		if ( '' !== $additional_address ) {
			$address .= $additional_address . ', ';
		}
		if ( '' != $address ) {
			$address = substr( $address, 0, strlen( $address ) - 2 );
		}
		return $address;
	}

	public static function get_phone( $id, $author_id ) {
		if ( method_exists( 'WyzHelpersOverride', 'get_phone') ) {
			return WyzHelpersOverride::get_phone( $id, $author_id );
		}
		$phone1 = esc_html( get_post_meta( $id, 'wyz_business_phone1', true ) );
		$phone2 = esc_html( get_post_meta( $id, 'wyz_business_phone2', true ) );
		
		if ( ! empty( $phone1 ) ) {
			$phone1 = '<a href="tel:' . $phone1 . '">' . $phone1 . '</a> ';
		}
		if ( ! empty( $phone2 ) ) {
			$phone2 = '<a href="tel:' . $phone2 . '">' . $phone2 . '</a> ';
		}

		if ( ! self::wyz_sub_can_bus_owner_do( $author_id,'wyzi_sub_business_show_phone_2') ) { 
			$phone2 = '';
		}

		$final_phone = '';
		if ( '' === $phone2 ) {
		    $final_phone = $phone1;
		} elseif ( '' === $phone1 ) {
		    $final_phone = $phone2;
		} else {
		    $final_phone = $phone1 . ' / ' . $phone2;
		}

		return $final_phone;
	}

	public static function get_email( $id, $author_id ) {
		$email1 = get_post_meta( $id, 'wyz_business_email1', true );
		$email2 = get_post_meta( $id, 'wyz_business_email2', true );

		if ( ! self::wyz_sub_can_bus_owner_do( $author_id,'wyzi_sub_business_show_email_2') ) { 
			$email2 = '';
		}

		$final_email = '';
		if ( '' === $email2 ) {
		    $final_email = '<a href="mailto:' . esc_attr( $email1 ) . '" target="_blank">' . esc_html( $email1 ) . '</a>';
		} elseif ( '' === $email1 ) {
		    $final_email = '<a href="mailto:' . esc_attr( $email2 ) . '" target="_blank">' . esc_html( $email2 ) . '</a>';
		} else {
		    $final_email = '<a href="mailto:' . esc_attr( $email1 ) . '" target="_blank">' . esc_html( $email1 ) . '</a> / <a href="mailto:' . esc_attr( $email2 ) . '" target="_blank">' . esc_html( $email2 ) . '</a>';
		}

		return $final_email;
	}

	public static function social_links( $id , $title = '', $tabCustomClass = '') {
		if ( method_exists( 'WyzHelpersOverride', 'social_links') ) {
			return WyzHelpersOverride::social_links( $id ,$title, $tabCustomClass );
		}
		$social = array();
		$ids = array( 'wyz_business_facebook','wyz_business_twitter','wyz_business_linkedin','wyz_business_google_plus','wyz_business_youtube',
			'wyz_business_flicker','wyz_business_pinterest','wyz_business_instagram' );

		foreach ($ids as $d) {
			$social[] = get_post_meta( $id, $d, true );
		}

		$has_social_links = false;
		foreach ( $social as $s ) {
			if ( '' != $s ) {
				$has_social_links = true;
				break;
			}
		}

		if ( $has_social_links ) {?>
			<!-- Social Business Sidebar -->
			<div class="sin-busi-sidebar widget social-widget <?php echo (!empty($tabCustomClass) ) ? " " . $tabCustomClass : '';?>">
				<?php if(!empty($title)) { ?>
				<h4 class="sidebar-title widget-title"><?php echo esc_html( $title);?></h4>
				<?php } ?>
				<div class="sidebar-social fix">
			<?php if ( isset( $social[0] ) && ! empty( $social[0] ) ) { ?>

					<a href="<?php echo self::wyz_link_auth( $social[0] ); ?>" class="facebook wyz-prim-color-hover" target="_blank"><i class="fa fa-facebook"></i></a>
			<?php }

			if ( isset( $social[1] ) && ! empty( $social[1] ) ) { ?>

					<a href="<?php echo self::wyz_link_auth( $social[1] ); ?>" class="twitter wyz-prim-color-hover" target="_blank"><i class="fa fa-twitter"></i></a>
			<?php }

			if ( isset( $social[2] ) && ! empty( $social[2] ) ) { ?>

					<a href="<?php echo self::wyz_link_auth( $social[2] ); ?>" class="linkedin wyz-prim-color-hover" target="_blank"><i class="fa fa-linkedin"></i></a>
			<?php }

			if ( isset( $social[3] ) && ! empty( $social[3] ) ) { ?>

					<a href="<?php echo self::wyz_link_auth( $social[3] ); ?>" class="google-plus wyz-prim-color-hover" target="_blank"><i class="fa fa-google-plus"></i></a>
			<?php }

			if ( isset( $social[4] ) && ! empty( $social[4] ) ) { ?>

					<a href="<?php echo self::wyz_link_auth( $social[4] ); ?>" class="youtube-play wyz-prim-color-hover" target="_blank"><i class="fa fa-youtube-play"></i></a>
			<?php }

			if ( isset( $social[5] ) && ! empty( $social[5] ) ) { ?>

					<a href="<?php echo self::wyz_link_auth( $social[5] ); ?>" class="flickr wyz-prim-color-hover" target="_blank"><i class="fa fa-flickr"></i></a>
			<?php }

			if ( isset( $social[6] ) && ! empty( $social[6] ) ) { ?>

					<a href="<?php echo self::wyz_link_auth( $social[6] ); ?>" class="pinterest-p wyz-prim-color-hover" target="_blank"><i class="fa fa-pinterest-p"></i></a>
			<?php }

			if ( isset( $social[7] ) && ! empty( $social[7] ) ) { ?>

					<a href="<?php echo self::wyz_link_auth( $social[7] ); ?>" class="instagram wyz-prim-color-hover" target="_blank"><i class="fa fa-instagram"></i></a>
			<?php } ?>
				</div>
			</div>
		<?php } 
	}


	public static function wyz_set_time( $data ) {
		if ( method_exists( 'WyzHelpersOverride', 'wyz_set_time') ) {
			return WyzHelpersOverride::wyz_set_time( $data );
		}
		$open_close = array();
		if ( '' != $data && ! empty( $data ) ) {
			foreach ( $data as $key => $value ) {
				if ( ( isset( $value['open'] ) && '' != $value['open'] ) ||
					( isset( $value['close'] ) && '' != $value['close'] ) ) {
					$open_close[] = $value;
				}

			}
		}
		return $open_close;
	}

	public static function wyz_display_time( $arr, $day ) {
		if ( method_exists( 'WyzHelpersOverride', 'wyz_display_time') ) {
			return WyzHelpersOverride::wyz_display_time( $arr, $day );
		}

		global $template_type;

		if ( ! empty( $arr ) ) {
			if ( 1 == $template_type ) { ?>
			<div class="clearfix">
				<p class="day wyz-secondary-color-text"><?php esc_html_e( $day, 'wyzi-business-finder');?></p>
				<div class="time-container">
				<?php if ( isset( $arr['status'] ) ) {?>
					<div><p class="time <?php echo $arr['class'];?>"><?php  echo $arr['status']; ?></p></div>
				<?php } else {
					foreach ( $arr as $key => $value ) {?>
						<div><p class="time"><?php  echo ( isset( $value['open'] ) ?  esc_html( $value['open'] ) : '' ) . ' - ' . ( isset( $value['close'] ) ?  esc_html( $value['close'] ) : '' ); ?></p></div>
					<?php }
				}?>
				</div>
			</div>
		<?php } else {?>
			<div class="clearfix">
				<li><h5 class="day"><?php esc_html_e( $day, 'wyzi-business-finder');?></h5>
				<span class="dates">
				<?php if ( isset( $arr['status'] ) ) {?>
				<div><p class="time <?php echo $arr['class']?>"><?php  echo $arr['status']; ?></p></div>
				<?php } else
				foreach ( $arr as $key => $value ) {?>
					<?php  echo '<span class="date wyz-prim-color-txt-hover">' . ( isset( $value['open'] ) ? '<span class="open">' . esc_html( $value['open'] ) . '</span>': '' ) . ' - ' . ( isset( $value['close'] ) ? '<span class="closed">' . esc_html( $value['close']   ). '</span>' : '' ) . '</span>'; ?>
				<?php }?>
				</span>
				</li>
			</div>
					
			<?php }
		}
	}

	/**
	 * Get the business subheader below the map.
	 *
	 * @param integer $id business id.
	 */
	public static function wyz_the_business_subheader( $id ) {
		if ( method_exists( 'WyzHelpersOverride', 'wyz_the_business_subheader') ) {
			return WyzHelpersOverride::wyz_the_business_subheader( $id );
		}
		ob_start();
		$prefix = 'wyz_';
		$name = get_the_title( $id );
		/*if ( has_post_thumbnail( $id ) ) {
			$logo = wp_get_attachment_url( get_post_thumbnail_id( $id ) );
		} else {
			$logo = self::get_default_image( 'business' );
		}*/
		self::get_post_thumbnail_url( $id, 'business' );
		$description = get_post_meta( $id, $prefix . 'business_excerpt', true );
		$slogan = get_post_meta( $id, $prefix . 'business_slogan', true );
		?>
		<div class="business-data-area">
			<div class="container">
				<div class="row">
					<?php WyzPostShare::the_favorite_button( $id );?>
					<div class="business-data-wrapper col-xs-12">
						<?php
						if ( self::wyz_sub_can_bus_owner_do(self::wyz_the_business_author_id(),'wyzi_sub_show_business_logo') ) {
							if ( is_singular( 'wyz_offers' ) ) {
								echo self::get_post_thumbnail( $id, 'business', 'medium', array( 'class' => 'logo float-left' ) );
							} else {
								echo self::get_post_thumbnail( get_the_ID(), 'business', 'medium', array( 'class' => 'logo float-left' ) );
							}
						} 
						?>
						<div class="content fix">
							<h1><?php echo esc_html( $name );
								if ( '' != $slogan ) {
									echo ' - ' . $slogan;
								}?></h1>
							<?php echo self::verified_icon( $id );?>
							<h2><?php echo esc_html( $description );?></h2>
							<div class="bus-term-tax clear"><?php echo get_the_term_list( $id, 'wyz_business_category', '', ', ', '' );?></div>
						</div>
						<?php 
						if ( self::wyz_sub_can_bus_owner_do(self::wyz_the_business_author_id(),'wyzi_sub_business_show_social_shares') ) {
								echo self::wyz_get_social_links( $id ); 
						} 
						if ( function_exists( 'wyz_breadcrumbs' ) ) {
							echo '<div>' . wyz_breadcrumbs() . '</div>';
						}?>
					</div>
				</div>
			</div>
		</div>

		<?php echo ob_get_clean();
	}


	public static function get_post_thumbnail_url( $post_id, $post_type ) {
		$logo_url = '';
		if ( has_post_thumbnail( $post_id ) ) {
			$logo_url = wp_get_attachment_url( get_post_thumbnail_id( $post_id ) );
		}
		if ( empty( $logo_url ) || ! $logo_url ) {
			$logo_url = self::get_default_image( $post_type );
		}
		return $logo_url;
	}

	public static function get_post_thumbnail( $post_id, $post_type, $size = 'thumbnail', $attr = array() ) {
		//$img = wp_get_attachment_image_src( $post_id, $size );

		$ret = '<img class="';
		if( isset( $attr['class'] ) ){
			$ret .= $attr['class'] . ' ';
			unset($attr['class']);
		}
		$ret .= 'lazyload" ';

		$img = get_the_post_thumbnail_url( $post_id, $size );
		if ( $img ){
			$ret .= 'data-src="'.$img.'"/>';
			return $ret;
		}

		return $ret . 'data-src="' . self::get_default_image( $post_type ) . '" ' . join(' ', array_map(function($key) use ($attr){
				if ( is_bool( $attr[ $key ] ) ) {
					return $attr[ $key ] ? $key : '';
				}
				return $key . '="' . $attr[ $key ] . '"';
				}, array_keys( $attr ) ) ) . '/>';
	}


	public static function get_default_image( $image ) {
		if ( method_exists( 'WyzHelpersOverride', 'get_default_image') ) {
			return WyzHelpersOverride::get_default_image( $image );
		}
		$def = '';
		$img = '';
		if ( 'business' == $image ) {
			$def = WYZI_PLUGIN_URL . 'businesses-and-offers/businesses/images/default-business.png';
			if ( function_exists( 'wyz_get_option') ) {
				$img = wyz_get_option( 'default-business-logo' );
			}
		} elseif ( 'offer' == $image ) {
			$def = WYZI_PLUGIN_URL . 'businesses-and-offers/offers/images/offers-placeholder.jpg';
			if ( function_exists( 'wyz_get_option') ) {
				$img = wyz_get_option( 'default-offer-logo' );
			}
		} elseif ( 'location' == $image ) {
			$def = WYZI_PLUGIN_URL . 'locations/images/location-placeholder.jpg';
			if ( function_exists( 'wyz_get_option') ) {
				$img = wyz_get_option( 'default-location-logo' );
			}
		}
		return ! empty( $img ) ? $img : $def;
	}

	public static function fisherYatesShuffle(&$items, $seed) {
		@mt_srand($seed);
		for ($i = count($items) - 1; $i > 0; $i--) {
			$j = @mt_rand(0, $i);
			$tmp = $items[$i];
			$items[$i] = $items[$j];
			$items[$j] = $tmp;
		}
	}


	/**
	 * Check if business verification has expired
	 *
	 * @param integer $id business id.
	 */
	public static function verification_expired( $id ) {
		$verified_date = get_post_meta( $id, 'wyz_business_verify_expiry', true );
		if ( empty( $verified_date) ) return false;
		return ( time() > strtotime( $verified_date ) );
	}

	public static function get_open_closed_all_day_msg() {
		if ( method_exists( 'WyzHelpersOverride', 'get_open_closed_all_day_msg') ) {
			return WyzHelpersOverride::get_open_closed_all_day_msg();
		}
		if ( empty( self::$open_all_day_msg ) || empty( self::$closed_all_day_msg ) ) {
			$wyz_business_form_data = get_option( 'wyz_business_form_builder_data', array() );
			//Custom form fields
			if ( ! empty( $wyz_business_form_data ) ) {
				foreach ( $wyz_business_form_data as $key => $value ) {
					if( 'time' == $value['type'] ) {
						if( isset( $value['openMsg'] ) )
							self::$open_all_day_msg = $value['openMsg'];
						if( isset( $value['closedMsg'] ) )
							self::$closed_all_day_msg = $value['closedMsg'];
						break;
					}
				}
			}

			if ( empty( self::$open_all_day_msg ) )
				self::$open_all_day_msg = esc_html__( 'Open all day', 'wyzi-business-finder' );

			if ( empty( self::$closed_all_day_msg ) )
				self::$closed_all_day_msg = esc_html__( 'Closed all day', 'wyzi-business-finder' );
		}
		return array( 'open' => self::$open_all_day_msg, 'closed' => self::$closed_all_day_msg );
	}


	public static function get_sticky_businesses() {
		if ( false === self::$STICKY_POSTS )
			self::$STICKY_POSTS = get_option( 'sticky_posts' );
		return self::$STICKY_POSTS;
	}


	/**
	 * Get the business verified icon.
	 *
	 * @param integer $id business id.
	 */
	private static $verified_icon;
	public static function verified_icon( $id=-1 ) {
		if ( method_exists( 'WyzHelpersOverride', 'verified_icon') ) {
			return WyzHelpersOverride::verified_icon( $id );
		}
		if ( $id<1)$id=get_the_ID();
		if('yes'!==get_post_meta($id,'wyz_business_verified',true))return '';
		if ( self::verification_expired( $id ) )return '';
		if ( '' == self::$verified_icon )
			self::$verified_icon = '<img alt="'.esc_html__( 'verified', 'wyzi-business-finder' ).'" src="'. apply_filters( 'wyz_verified_icon', plugin_dir_url( __FILE__ ) . 'img/verified.png' ) .'" class="verified-icon"/>';
		return self::$verified_icon;
	}


	public static function wyz_info( $msg, $return = false, $attr='' ) {
		 $info = '<div ' . $attr . ' class="wyz-info"><p>' . $msg . '</p></div>';
		 if ( $return )
		 	return $info;
		 echo $info;
	}

	public static function wyz_success( $msg, $return = false ) {
		 $success = '<div class="wyz-success"><p>' . $msg . '</p></div>';
		 if ( $return )
		 	return $success;
		 echo $success;
	}

	public static function wyz_warning( $msg, $return = false ) {
		 $warning = '<div class="wyz-warning"><p>' . $msg . '</p></div>';
		 if ( $return )
		 	return $warning;
		 echo $warning;
	}

	public static function wyz_error( $msg, $return = false ) {
		 $error = '<div class="wyz-error"><p>' . $msg . '</p></div>';
		 if ( $return )
		 	return $error;
		 echo $error;
	}

	public static function add_new_offer_post( $offer_id, $author_id ) {
		$ttl = get_the_title( $offer_id );
		$exrpt = get_post_meta( $offer_id, 'wyz_offers_excerpt', true );
		$desc_arr = array_slice( explode( ' ', WyzHelpers::wyz_strip_tags( apply_filters('the_content', get_post_field( 'post_content', $offer_id ) ), '<table><td><tbody><tr><th>' ) ), 0, 60 );
		$desc = WyzHelpers::close_tags( implode( ' ', $desc_arr ) );
		if ( count( $desc_arr ) > 59 ) {
			$desc .= '...';
		}
		$bus_id = get_post_meta( $offer_id, 'business_id', true );
		$logo_bg = get_post_meta( $bus_id, 'wyz_business_logo_bg', true );
		$image = get_post_meta( $offer_id, 'wyz_offers_image_id', true );
		if ( '' != $image ) {
		    if (filter_var($image, FILTER_VALIDATE_URL)){
		        $image = '<img style="width:100%;height:auto;" src="'.$image.'"/>';
		    }
            else{
    			$image = wp_get_attachment_url( $image );
    			$image = '<img style="width:100%;height:auto;" src="'.$image.'"/>';
            }
		}
		if ( ! $image || empty( $image ) )
			$image = WyzHelpers::get_post_thumbnail( $offer_id, 'offer', 'large', array( 'class' => 'attachment-large size-large' ) );
		$dscnt = get_post_meta( $offer_id, 'wyz_offers_discount', true );
		ob_start();?>

		<div id="post-<?php echo $offer_id; ?>" class="offer-item">
			<div class="image col-xs-12"><?php echo $image;
				if ( 0 < $dscnt ) { ?>
				<span class="offer-label"><?php esc_html_e( 'DISCOUNT', 'wyzi-business-finder' );?> <?php echo esc_html( $dscnt );?>%</span>
				<?php }?>
			</div>
			<div class="content col-xs-12">
				<div class="head fix">
					<div class="text">
						<h3><?php echo esc_html( $ttl ); ?></h3>
						<h4 class="wyz-secondary-color-text"><?php echo esc_html( $exrpt ); ?></h4>
					</div>
				</div>
				<p><?php echo $desc; ?></p>
				<a href="<?php echo esc_url( get_post_permalink( $offer_id ) ); ?>" class="view-offer wyz-button wyz-secondary-color icon"><?php echo sprintf( esc_html__( 'View %s', 'wyzi-business-finder' ), WYZ_OFFERS_CPT );?> <i class="fa fa-angle-right"></i></a>
			</div>
		</div>
		<?php

		$offer_content = ob_get_clean();

		$post_status = get_post_status( $bus_id );
		$post_data = array();
		if ( 'publish' != $post_status )
			$post_status = 'pending';

		$post_meta_data = array(
			'wyz_business_post_likes'=> array(),
			'wyz_business_post_likes_count' => 0,
			'business_id' => $bus_id,
			'post_offer_id' => $offer_id,
		);

		$post_data['post_content'] = $offer_content;
		$post_img = '';
		$post_data['post_title'] = 'Post of ' . get_the_title( $bus_id ) . ' new Offer on ' . date( 'Y M jS' ) . ' at ' . date( 'h:i:s' );
		$post_data['post_status'] = $post_status;
		$post_data['post_type'] = 'wyz_business_post';
		$bus_comm_stat = get_post_meta( $bus_id, 'wyz_business_comments', true );
		$post_data['comment_status'] = ( 'off' != $bus_comm_stat ? 'open' : 'closed' );

		$new_post_id = wp_insert_post( $post_data, true );

		foreach ( $post_meta_data as $key => $value ) {
			update_post_meta( $new_post_id, $key, $value );
		}

		$business_posts = get_post_meta( $bus_id, 'wyz_business_posts', true );
		if ( '' === $business_posts || ! $business_posts ) {
			$business_posts = array();
		}
		array_push( $business_posts, $new_post_id );
		update_post_meta( $bus_id, 'wyz_business_posts', $business_posts );
	}

	/**
	 * Get user account button dropdown
	 *
	 */
	public static function get_myaccount_btn_dropdown( $args = array(), $shortcode = false ) {
		if ( method_exists( 'WyzHelpersOverride', 'get_myaccount_btn_dropdown') ) {
			return WyzHelpersOverride::get_myaccount_btn_dropdown( $args, $shortcode );
		}

		$user_id = get_current_user_id();
		$is_business_owner = current_user_can( 'publish_businesses' );
		if ( $is_business_owner ) {
			$user_points = get_user_meta( $user_id, 'points_available', true );
			if ( '' != $user_points ) {
				$user_points = intval( $user_points );
			} else {
				$user_points = 0;
			}
		}

		$tabs = array();
		

		$tabs[] = new AccountBusiness($is_business_owner, $user_id, false );
		$tabs[] = new AccountProfile($is_business_owner, $user_id, false);
		$tabs[] = new AccountFavorite($is_business_owner, $user_id, false);
		$tabs[] = new AccountWoo($is_business_owner, $user_id, false);
		$tabs[] = new AccountVendor($is_business_owner, $user_id, false);
		$tabs[] = new AccountProducts($is_business_owner, $user_id, false);
		$tabs[] = new AccountSubscription($is_business_owner, $user_id, false);
		$tabs[] = new AccountJob($is_business_owner, $user_id, false);
		$tabs[] = new AccountBooking($is_business_owner, $user_id, false);
		$tabs[] = new InternalMessaging( $is_business_owner, $user_id, false );

		$tabs = apply_filters( 'wyz_account_button_dropdown_tabs', $tabs);

		ob_start();

		?>
		<div id="acount-btn-content" class="login-dropdown list-group" style="display:none;">
			<?php if ( $is_business_owner && 'on' != get_option( 'wyz_hide_points' ) ) {?>
			<div class="element point-status"><span><?php echo sprintf( esc_html__( 'You Have %d Points', 'wyzi-business-finder' ), $user_points );?></span></div>
			<?php }
			foreach ( $tabs as $tab ) {
			if($tab->condition){?>
			<div class="element">
			<i class="fa fa-<?php echo $tab->icon;?>" aria-hidden="true"></i>
			<span>
				<?php $tab->the_tab(true); ?>
			</span></div>
			<?php } }
			?>

		</div>
		<?php
		return ob_get_clean();
	}


	public static function get_businesses_ratings ( $bus_id ) {
		if ( empty( self::$Businesses_Ratings ) )
			self::$Businesses_Ratings = array();
		if ( ! isset( self::$Businesses_Ratings[ $bus_id ] ) ) {
			$business_ratings = array();
			$business_rating_sum = 0;
			$all_business_ratings = get_post_meta( $bus_id, 'wyz_business_ratings', true );
			if ( ! $all_business_ratings || '' == $all_business_ratings ) {
				$all_business_ratings = array();
			}
			if ( ! empty( $all_business_ratings ) ) {
				$args = array(
					'post_type' => 'wyz_business_rating',
					'post_status' => 'publish',
					'post__in' => $all_business_ratings,
					'posts_per_page' => -1,
					'fields' => 'ids'
				);
				$query = new WP_Query( $args );

				foreach ($query->posts as $id) {
					$rt = intval( get_post_meta( $id, 'wyz_business_rate', true ) );
					$business_ratings[] = array(
						'id' => $id,
						'rate' => $rt,
					);
					$business_rating_sum += $rt;
				}

			}
			self::$Businesses_Ratings[ $bus_id ] = array(
				'business_id' => $bus_id,
				'ratings' => $business_ratings,
				'count' => count( $business_ratings ),
				'sum' => $business_rating_sum,
				'avg' => ( count( $business_ratings ) ? ( intval( 100 * ( 1.0 * $business_rating_sum ) / count( $business_ratings ) )/100.0 ) : 0 )
			);
		}
		return self::$Businesses_Ratings[ $bus_id ];
	}


	/**
	 * Query businesses with featured in mind
	 *
	 * @param integer $business_id business id.
	 */
	public static function query_businesses( $args = array(), $shortcode = false ) {

		if ( method_exists( 'WyzHelpersOverride', 'query_businesses') ) {
			return WyzHelpersOverride::query_businesses( $args, $shortcode );
		}

		$featured_posts_per_page = get_option( 'wyz_featured_posts_perpage', 2 );

		if ( 0 == $featured_posts_per_page )
			return new WP_Query( $args );

		$sticky_posts = get_option( 'sticky_posts' );

		if ( is_tax( 'wyz_business_category' ) || $shortcode ) {

			$cat_feat = array(
				'post_type' => 'wyz_business',
				'posts_per_page' => -1,
				'post__in' => $sticky_posts,
				'fields' => 'ids',
			);
			if ( isset( $args['meta_query'] ) )
				$cat_feat['meta_query'] = $args['meta_query'];
			if ( ! $shortcode ) {
				$cat_feat['tax_query'] = array(
					array(
						'taxonomy' => 'wyz_business_category',
						'field'    => 'term_id',
						'terms'    => get_queried_object()->term_id,
					),
				);
			}
			$sticky_posts = ( new WP_Query( $cat_feat ) )->posts;
		}
		if ( empty( $sticky_posts ) || ( isset( $args['paged'] ) && 1 < $args['paged'] ) ) {
			$args['post_type'] = 'wyz_business';
			return new WP_Query( $args );
		}

 
		$featured_businesses_args = array(
			'post_type' => 'wyz_business',
			//'posts_per_page' => $featured_posts_per_page,
			'post__in' => $sticky_posts,
			'fields' => 'ids',
		);

		if ( isset( $args['tax_query'] ) ) {
			$featured_businesses_args['tax_query'] = $args['tax_query'];
		}

		if ( isset( $args['paged'] ) && 1 < $args['paged'] ) {
			$featured_businesses_args['paged'] = $args['paged'];
		}

		$featured_businesses_args = apply_filters( 'wyz_query_featured_businesses_args_search', $featured_businesses_args, $args );


		$query1 = new WP_Query( $featured_businesses_args );

		$sticky_posts = $query1->posts;

		if ( count( $sticky_posts ) > $featured_posts_per_page ) {

			self::fisherYatesShuffle( $sticky_posts, rand(10,100) );
			$sticky_posts = array_slice( $sticky_posts, 0, $featured_posts_per_page );
		}


		$args['fields'] = 'ids';
		$args['post__not_in'] = $sticky_posts;
		$args['post_type'] = 'wyz_business';
		
		$pos_p_p = '';
		if(isset($args['posts_per_page']))
            $pos_p_p = $args['posts_per_page'];
        $args['posts_per_page']=-1;
		$query2 = new WP_Query( $args );

		$all_the_ids = array_merge( $sticky_posts, $query2->posts );

		if ( empty( $all_the_ids ) ) $all_the_ids = array( 0 );

		$final_query_args = array(
			'post_type' => 'wyz_business',
			'post__in' => $all_the_ids,
			'orderby' => 'post__in',
		);
		
		if ( !empty( $pos_p_p ) ) 
			$final_query_args['posts_per_page'] = $pos_p_p;

		if ( isset( $args['paged'] ) ) {
			$final_query_args['paged'] = $args['paged'];
		}

		return new WP_Query( $final_query_args );
	}


	public static function substring_excerpt ( $string, $length ) {
		if ( method_exists( 'WyzHelpersOverride', 'substring_excerpt') ) {
			return WyzHelpersOverride::substring_excerpt( $string, $length );
		}
		$substring = substr( $string , 0, $length );
		$sub_len = strlen( $substring );
		if ( $sub_len < $length )
			return $substring;
		$temp_substr = $substring;

		for ( $i = $sub_len - 1; $i >= 0; $i-- ) {
			if ( substr( $temp_substr, $i, 1)  == " " )
				break;
			$temp_substr = substr( $temp_substr, 0, $i );
		}

		if ( strlen( $temp_substr ) == 0 )
			return $substring;
		return $temp_substr;
	}


	/**
	 * Get the business social links.
	 *
	 * @param integer $business_id business id.
	 */
	public static function wyz_get_social_links( $business_id ) {
		if ( method_exists( 'WyzHelpersOverride', 'wyz_get_social_links') ) {
			return WyzHelpersOverride::wyz_get_social_links( $business_id );
		}

		if ('on' == get_option( 'wyz_hide_header_social_share', 'off' ))
			return;
		
		$fbid = function_exists( 'wyz_get_option' ) ? wyz_get_option( 'businesses_fb_app_ID' ) : '';

		//WyzPostShare::the_js_scripts();

		ob_start();?>	
		<div class="business-social">
			<?php if ( true ) {?>




			<div class="social social-facebook">
				<div class="front wyz-primary-color wyz-prim-color">
					<i class="fa fa-facebook"></i>
				</div>
				<div class="back wyz-primary-color wyz-prim-color">
					<div class="fb-share-button" data-mobile-iframe="true" data-href="<?php echo get_permalink(); ?>"  data-layout="button_count" data-size="small" data-show-faces="false" data-share="false"><a target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=https%3A%2F%2Fdevelopers.facebook.com%2Fdocs%2Fplugins%2F&amp;src=sdkpreparse" class="fb-xfbml-parse-ignore"><?php esc_html__( 'Share', 'wyzi-business-finder');?></a></div>

					
					<div id="fb-root"></div>
					<script>
					//<![CDATA[
					(function(d, s, id) {
					  var js, fjs = d.getElementsByTagName(s)[0];
					  if (d.getElementById(id)) return;
					  js = d.createElement(s); js.id = id;
					  js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.6&appId=<?php echo esc_js( $fbid );?>";
					  fjs.parentNode.insertBefore(js, fjs);
					}(document, 'script', 'facebook-jssdk'));
					//]]>
					</script>
				</div>
			</div>
			<?php }?>

			<div class="social social-twitter">
				<div class="front wyz-primary-color wyz-prim-color">
					<i class="fa fa-twitter"></i>
				</div>
				<div class="back wyz-primary-color wyz-prim-color">
					<iframe allowtransparency="true" scrolling="no" src="//platform.twitter.com/widgets/tweet_button.html" style="width:60px; height:20px;"></iframe>
				</div>
			</div>
			<div class="social social-googleplus">
				<div class="front wyz-primary-color wyz-prim-color">
					<i class="fa fa-google-plus"></i>
				</div>
				<div class="back wyz-primary-color wyz-prim-color">
					<div class="g-plusone" data-size="medium"></div>
				</div>
			</div>
			<script type="text/javascript">
			//<![CDATA[
			(function() {
				var po = document.createElement("script"); po.type = "text/javascript"; po.async = true;
				po.src = "https://apis.google.com/js/plusone.js";
				var s = document.getElementsByTagName("script")[0]; s.parentNode.insertBefore(po, s);
			})();
			//]]>
			</script>

			<div class="social social-linkedin">
				<div class="front wyz-primary-color wyz-prim-color">
					<i class="fa fa-linkedin"></i>
				</div>
				<div class="back wyz-primary-color wyz-prim-color">
					<script src="//platform.linkedin.com/in.js" type="text/javascript"></script>
					<script type="IN/Share" data-url="<?php the_permalink(); ?>" data-counter="right"></script>
				</div>
			</div>

		</div>
		<?php return ob_get_clean();
	}


	private static $custom_form_data;
	public static function get_custom_field_type( $name ) {
		if ( ! isset( self::$custom_form_data) )
			self::$custom_form_data = get_option( 'wyz_business_custom_form_data', array() );

		if ( empty( self::$custom_form_data ) ) return '';
		foreach ( self::$custom_form_data as $key => $value ) {
			if ( "wyzi_claim_fields_$key" == $name ) {
				if ( 'selectbox' == $value['type'] ) {
					return array(
						'type' => 'selectbox',
						'selecttype' => $value['selecttype']
					);	
				}
				return $value['type'];
			}
		}
	}

	public static function FCM_is_on() {
		return false;
		return 'on' == get_option('wyz_fcm_on_off') && 
				! empty( get_option('wyz_fcm_api_key') ) &&
				! empty( get_option('wyz_fcm_auth_domain') ) &&
				! empty( get_option('wyz_fcm_db_url') ) &&
				! empty( get_option('wyz_fcm_project_id') ) &&
				! empty( get_option('wyz_fcm_storage_bucket') ) &&
				! empty( get_option('wyz_fcm_msg_sndr_id') );
	}


	/*
	 * Check if user has a Wyzi-defined role
	 *
	 */
	public static function is_user_wyzi_user( $user_role, $user_id = false ) {
		if ( empty( $user_role ) ) {
			if ( ! $user_id ) return false;
			$user_meta = get_userdata( $user_id );
			if ( ! $user_meta ) return false;
			$user_role = $user_meta->roles;
		}
		$wyzi_roles = array( 'client', 'business_owner', 'pending_user', 'dc_pending_vendor' );
		if ( is_array( $user_role ) ) {
			foreach ( $user_role as $role )
				if ( in_array( $role, $wyzi_roles ) )
					return true;
		} else {
			return in_array( $user_role, $wyzi_roles );
		}
		return false;
	}


	/*
	 * Check if user is pending
	 *
	 */
	public static function is_user_pending( $user_id = false ) {
		if ( ! $user_id )
			$user_id = get_current_user_id();
		$user_meta = get_userdata( $user_id );
		if ( ! $user_meta )
			return false;
		$user_role = $user_meta->roles;

		if ( is_array( $user_role ) )
			return in_array( 'pending_user', $user_role );
		return 'pending_user' == $user_role;
	}


	/**
	 * Check if current user is the post author.
	 *
	 * @param integer $post_id the post id.
	 */
	public static function wyz_is_current_user_author( $post_id ) {

		if ( method_exists( 'WyzHelpersOverride', 'wyz_is_current_user_author') ) {
			return WyzHelpersOverride::wyz_is_current_user_author( $post_id );
		}

		$post = get_post( $post_id );
		if ( null === $post ) {
			return false;
		}
		return get_current_user_id() == $post->post_author;
	}
	
	/**
	 * Check if current user is the a client.
	 *
	 * @param integer $post_id the post id.
	 */
	
	public static function wyz_is_current_user_client(){
	    return current_user_can('client');
	}


	public static function add_business_to_user( $user_id, $business_id, $status ) {
		$user_businesses = self::get_user_businesses( $user_id );

		if ( ! isset( $user_businesses['pending'][ $business_id ] ) && ! isset( $user_businesses['published'][ $business_id ] ) ) {
			$count = get_user_meta( $user_id, 'wyz_user_businesses_count', true );
			if ( empty( $count ) ) $count = 0;
			else $count = intval( $count );
			$count++;
			update_user_meta( $user_id, 'wyz_user_businesses_count', $count );
		}
		if ( 'publish' == $status ) {
			$status = 'published';
			if ( isset( $user_businesses[ 'pending' ][ $business_id ] ) )
				unset( $user_businesses[ 'pending' ][ $business_id ] );
		}
		$user_businesses[ $status ][ $business_id ] = $business_id;
		update_user_meta( $user_id, 'wyz_user_businesses', $user_businesses );
	}


	private static $is_calendar_page;

	public static function is_calendar_page($easy=false) {
		if ( ! isset( self::$is_calendar_page ) || empty( self::$is_calendar_page ) ) {
			self::$is_calendar_page = ( ( isset( $_GET['page'] ) && 'calendars' == $_GET['page'] ) || ( isset( $_GET[ WyzQueryVars::BusinessCalendar ] ) &&  current_user_can( 'edit_businesses', $_GET[ WyzQueryVars::BusinessCalendar ] ) ) ) && ($easy||is_page( 'user-account' ));
		}
		return self::$is_calendar_page;
	}



	public static function remove_business_from_user( $user_id, $business_id ) {

		$user_businesses = self::get_user_businesses( $user_id );
		$deleted = false;
		if ( isset( $user_businesses['pending'][ $business_id ] ) ){
			unset( $user_businesses['pending'][ $business_id ] );
			$deleted = true;
		}
		if ( isset( $user_businesses['published'][ $business_id ] ) ){
			unset( $user_businesses['published'][ $business_id ] );
			$deleted = true;
		}
		if ( $deleted ) {
			$count = intval( get_user_meta( $user_id, 'wyz_user_businesses_count', true ) );
			$count--;
			update_user_meta( $user_id, 'wyz_user_businesses', $user_businesses );
			if ( $count < 0 ) $count = 0;
			update_user_meta( $user_id, 'wyz_user_businesses_count', $count );
		}
	}

	public static function get_user_businesses( $user_id = false ) {
		if ( ! $user_id ) $user_id = get_current_user_id();

		$user_businesses = get_user_meta( $user_id, 'wyz_user_businesses', true );
		if ( empty( $user_businesses ) ) {
			$user_businesses = array();
			$user_businesses['pending'] = array();
			$user_businesses['published'] = array();
		}

		return $user_businesses;
	}


	/**
	 * Get the user's calendar for the provided business id, or the business id set in $_GET
	 *
	 * @param int  $user_id current user id.
	 * @param int $business_id user's business id.
	 *
	 * @return int the calendar corresponding to the user's business
	 */
	public static function get_user_calendar( $user_id = false, $business_id = false ) {
		$user_id = $user_id ? $user_id : get_current_user_id();
		$business_id = $business_id ? ''.$business_id : ( isset( $_GET[ WyzQueryVars::BusinessCalendar ] ) ? ''.$_GET[ WyzQueryVars::BusinessCalendar ] : '' );

		if ( empty( $user_id ) || empty( $business_id ) )
			return false;
		$calendars = get_user_meta( $user_id, 'wyz_business_calendars', true );
		if ( empty( $calendars ) )$calendars = array();
		if ( ! isset( $calendars[ $business_id ] ) || empty( $calendars[ $business_id ] ) )
			return false;
		if ( term_exists( $calendars[ $business_id ], 'booked_custom_calendars' ) )
			return $calendars[ $business_id ];
		
		return false;
	}

	/**
	 * Get the business tab hash permalink
	 *
	 * @param string $tab the tab name
	 *
	 * @return string the tab hash href
	 */
	public static function get_business_tab_hash( $tab ) {
		$tab_data = get_option( 'wyz_business_tabs_order_data' );
		$count = count( $tab_data );
		for ( $i = 0; $i < $count; $i++ ) {
			if ( $tab_data[ $i ]['type'] == $tab ) {
				if ( ! isset( $tab_data[ $i ]['urlid'] ) || '' == $tab_data[ $i ]['urlid'] )
					$tab_data[ $i ]['urlid'] = urlencode( $tab_data[ $i ]['type'] );
				return ! empty( $tab_data[ $i ]['urlid'] ) ? ( '#' . $tab_data[ $i ]['urlid'] ) : '';
			}
		}
		return '';
	}

	

	/**
	 * Get the user's favorite businesses.
	 *
	 * @param int  $user_id current user id.
	 *
	 * @return array the ids of the favorite businesses
	 */
	public static function get_user_favorites( $user_id = false ) {
		$user_id = $user_id ? $user_id : get_current_user_id();

		$favorites = get_user_meta( $user_id, 'wyz_user_favorites', true );
		if ( empty( $favorites ) || '' == $favorites ) return array();
		return $favorites;
	}


	public static function get_business_favorite_count( $b_id ) {
		$fav_count = intval( get_post_meta( $b_id, 'wyz_business_fav_count', true ) );
		if ( empty( $fav_count ) || is_nan($b_id) ) $fav_count = 0;
		return $fav_count;
	}

	public static function wyz_get_ip_addr(){
		return ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ?  $_SERVER['HTTP_CLIENT_IP'] :
				( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : 
					$_SERVER['REMOTE_ADDR']
				);
	}


	public static function maybe_increment_business_visits( $b_id ) {
		if ( self::wyz_is_current_user_author( $b_id ) ){
			return;
		}
		$time = $_SERVER['REQUEST_TIME'];
		/**
		* Here we look for the user's LAST_ACTIVITY timestamp. If
		* it's set and indicates our $timeout_duration has passed,
		* blow away any previous $_SESSION data and start a new one.
		*/
		if ( isset($_SESSION['LAST_ACTIVITY'] ) ){
			if ( ( $time - $_SESSION['LAST_ACTIVITY'] ) > self::$VISITS_TIMEOUT_DURATION ) {
				session_unset();
				session_destroy();
				session_start();
			} else
				return;
		}

		/**
		* Finally, update LAST_ACTIVITY so that our timeout
		* is based on it and not the user's login time.
		*/
		$_SESSION['LAST_ACTIVITY'] = $time;
		self::increment_business_visits( $b_id );
	}

	private static function increment_business_visits( $b_id ) {
		$visits = get_post_meta( $b_id, 'wyz_business_visits', true );
		if ( empty( $visits )  )
			$visits = array();
		$date = date('m-d-Y', time());
		if ( ! isset( $visits[ $date ] ) ){
			$visits[ $date ] = 0;
		}
		$visits[ $date ]++;
		$total_visits = get_post_meta( $b_id, 'wyz_business_visits_count', true );
		$total_visits++;
		update_post_meta( $b_id, 'wyz_business_visits_count', $total_visits );
		update_post_meta( $b_id, 'wyz_business_visits', $visits );
	}

	public static function get_author_week_visits( $user_id, $week=0 ) {
		$businesses = self::get_user_businesses( $user_id );
		$data = array(
			'stats' => array(),
			'color' => array(),
			'title' => array()
		);
		foreach ( $businesses['published'] as $business_id ) {
			$data['stats'][] = self::get_business_week_visits( $business_id );
			$col = get_post_meta( $business_id, 'wyz_business_logo_bg', true );
			if ( empty( $col ) ) {
				$cat_id = self::wyz_get_representative_business_category_id( $business_id );
				$col = get_term_meta( $cat_id, 'wyz_business_cat_bg_color', true );
				if ( empty( $col ) )
					$col = sprintf( '#%06X', mt_rand( 0, 0xFFFFFF ) );	
			}
			$data['color'][] = $col;
			$data['title'][] = get_the_title( $business_id );
		}

		return $data;
	}

	public static function get_business_week_visits( $b_id, $week = 0 ) {
		$indexes = self::get_week_dates();
		$visits = get_post_meta( $b_id, 'wyz_business_visits', true );
		$data = array();
		foreach ( $indexes as $key => $value ) {
			$data[] = isset( $visits[ $value ] ) ? $visits[ $value ] : 0;
		}
		return $data;
	}

	private static function get_week_dates() {
		$first_day_of_the_week = 'Monday';
		$start_of_the_week     = strtotime("Last $first_day_of_the_week");
		if ( strtolower(date('l')) === strtolower($first_day_of_the_week) )
		{
		    $start_of_the_week = strtotime('today');
		}
		$days = array(0=>$start_of_the_week);
		for($i=1;$i<7;$i++)
			$days[ $i ] = $start_of_the_week + ( $i )*86400;
		
		$date_format =  'm-d-Y';
		$indexes = array(
			'mon' => '',
			'tues' => '',
			'wed' => '',
			'thurs' => '',
			'fri' => '',
			'sat' => '',
			'sun' => '',
		);

		$i = 0;
		foreach ($indexes as $key => $value) {
			$indexes[ $key ] = date( $date_format, $days[ $i++ ] );
		}
		return $indexes;
	}


	/*stats*/
	public static function get_business_visits_count( $b_id ) {
		$vis_count = intval( get_post_meta( $b_id, 'wyz_business_visits_count', true ) );
		if ( empty( $vis_count ) || is_nan($b_id) ) $vis_count = 0;
		return $vis_count;
	}
	public static function get_business_visits_total_time( $b_id ) {
		$vis_time = intval( get_post_meta( $b_id, 'wyz_business_visits_total_time', true ) );
		if ( empty( $vis_time ) || is_nan($b_id) ) $vis_time = 0;
		return $vis_time;
	}
	public static function get_business_visits_average_time( $b_id ) {
		$avg_time = intval( get_post_meta( $b_id, 'wyz_business_visits_average_time', true ) );
		if ( empty( $avg_time ) || is_nan($b_id) ) $avg_time = 0;
		return $avg_time;
	}


	/**
	 * Set the user's calendar for the provided business id
	 *
	 * @param int $business_id user's business id.
	 * @param int $calendar_id calendar's id.
	 * @param int  $user_id current user id.
	 */
	public static function set_user_calendar( $business_id, $calendar_id, $user_id = false ) {
		$user_id = $user_id ? $user_id : get_current_user_id();

		$user_info = get_userdata( $user_id );
		$auth_email = $user_info->user_email;
		
		if ( ( $calendar_id = intval( $calendar_id ) ) < 1 ) return false;
		$calendars = get_user_meta( $user_id, 'wyz_business_calendars', true );
		if ( empty( $calendars ) ) $calendars = array();
		$calendars[ ''.$business_id ] = $calendar_id;
		update_user_meta( $user_id, 'wyz_business_calendars', $calendars );
		update_post_meta( $business_id, 'calendar_id', $calendar_id );
		update_term_meta( $calendar_id, 'business_id', $business_id );

		update_user_meta( $user_id, 'wyz_business_calendars', $calendars );
		$term_meta = get_option( "taxonomy_$calendar_id" );
		$term_meta['notifications_user_id'] = $auth_email;
		update_option( "taxonomy_$calendar_id", $term_meta );

		return true;
	}

	/**
	 * Authenticate a link to make wp compatible.
	 *
	 * @param string  $link the link to authenticate.
	 * @param boolean $isfb is a facebook link.
	 */
	public static function wyz_link_auth( $link, $isfb = false ) {

		if ( ! isset( $link ) || '' == $link ) {
			return '';
		}
		$hd = substr( $link, 0, 4 );
		if ( 'http' == $hd ) {
			return esc_url( $link );
		}
		if ( $isfb ) {
			return esc_url( 'http://' . $link );
		}
		return  esc_url( '//' . $link );
	}


	/**
	 * Clear all query arguments from current url,
	 * and add query argument to it.
	 *
	 * @param string  $query_arg the query argument to add.
	 */
	public static function add_clear_query_arg( $query_arg, $hash = '' ) {

		if ( method_exists( 'WyzHelpersOverride', 'add_clear_query_arg') ) {
			return WyzHelpersOverride::add_clear_query_arg( $query_arg, $hash );
		}

		$url = explode( '?', esc_url_raw( add_query_arg( array() ) ) );
		
		if ( '' != $hash )
			$url[0] .= "#$hash";
		return add_query_arg( $query_arg, $url[0] );
	}

	/**
	 * Authenticate date.
	 *
	 * @param string $time the time to authenticate.
	 */
	public static function wyz_date_auth( $time ) {
		$pattern = "/^(?:0[1-9]|1[0-2]):[0-5][0-9] (am|pm|AM|PM)$/";
		if ( preg_match( $pattern, $time ) ) {
			return true;
		}
		return false;
	}
	

	public static function the_publish_date( $publish_time, $full = false ) {

		if ( method_exists( 'WyzHelpersOverride', 'the_publish_date') ) {
			return WyzHelpersOverride::the_publish_date( $publish_time, $full );
		}

		$now = new DateTime;
		$ago = new DateTime( $publish_time );
		$diff = $now->diff( $ago );

		$diff->w = floor( $diff->d / 7 );
		$diff->d -= $diff->w * 7;

		$string = array(
			'y' => esc_html__( '%d year', 'wyzi-business-finder' ),
			'm' => esc_html__( '%d month', 'wyzi-business-finder' ),
			'w' => esc_html__( '%d week', 'wyzi-business-finder' ),
			'd' => esc_html__( '%d day', 'wyzi-business-finder' ),
			'h' => esc_html__( '%d hour', 'wyzi-business-finder' ),
			'i' => esc_html__( '%d minute', 'wyzi-business-finder' ),
			's' => esc_html__( '%d second', 'wyzi-business-finder' ),
		);
		$strings = array(
			'y' => esc_html__( '%d years', 'wyzi-business-finder' ),
			'm' => esc_html__( '%d months', 'wyzi-business-finder' ),
			'w' => esc_html__( '%d weeks', 'wyzi-business-finder' ),
			'd' => esc_html__( '%d days', 'wyzi-business-finder' ),
			'h' => esc_html__( '%d hours', 'wyzi-business-finder' ),
			'i' => esc_html__( '%d minutes', 'wyzi-business-finder' ),
			's' => esc_html__( '%d seconds', 'wyzi-business-finder' ),
		);
		foreach ( $string as $k => &$v ) {
			if ( $diff->$k ) {
				$v = sprintf( _n( $v, $strings[ $k ], $diff->$k, 'wyzi-business-finder' ), $diff->$k );
			} else {
				unset( $string[$k] );
			}
		}

		if ( ! $full ) {
			$string = array_slice( $string, 0, 1 );
		}

		echo $string ? sprintf( esc_html__( '%s ago', 'wyzi-business-finder' ), implode( ', ', $string ) ) : esc_html__( 'just now', 'wyzi-business-finder' );
	}


	/**
	 * Fix image alpha mask blending.
	 */
	public static function wyz_imagealphamask( &$picture, $mask ) {
		// Get sizes and set up new picture.
		$xSize = imagesx( $picture );
		$ySize = imagesy( $picture );
		$newPicture = imagecreatetruecolor( $xSize, $ySize );
		imagesavealpha( $newPicture, true );
		imagefill( $newPicture, 0, 0, imagecolorallocatealpha( $newPicture, 0, 0, 0, 127 ) );

		// Resize mask if necessary.
		if ( $xSize != imagesx( $mask ) || $ySize != imagesy( $mask ) ) {
			$tempPic = imagecreatetruecolor( $xSize, $ySize );
			imagecopyresampled( $tempPic, $mask, 0, 0, 0, 0, $xSize, $ySize, imagesx( $mask ), imagesy( $mask ) );
			imagedestroy( $mask );
			$mask = $tempPic;
		}

		// Perform pixel-based alpha map application.
		for ( $x = 0; $x < $xSize; $x++ ) {
			for ( $y = 0; $y < $ySize; $y++ ) {
				$alpha = imagecolorsforindex( $mask, imagecolorat( $mask, $x, $y ) );
				$alpha = $alpha['alpha'];
				$color = imagecolorsforindex( $picture, imagecolorat( $picture, $x, $y ) );
				// Preserve alpha by comparing the two values.
				if ( $color['alpha'] > $alpha ) {
					$alpha = $color['alpha'];
				}
				// Kill data for fully transparent pixels.
				if ( 127 == $alpha ) {
					$color['red'] = 0;
					$color['blue'] = 0;
					$color['green'] = 0;
				}
				imagesetpixel( $newPicture, $x, $y, imagecolorallocatealpha( $newPicture, $color['red'], $color['green'], $color['blue'], $alpha ) );
			}
		}

		// Copy back to original picture.
		imagedestroy( $picture );
		$picture = $newPicture;
	}



	/**
	 * Get all business categories in terms of id => title.
	 */
	public static function get_business_categories_dropdown_format( $empty_init = false, $order=1 ) {
		return self::get_taxonomy_dropdown_format( 'wyz_business_category', $empty_init, $order );
	}


	/**
	 * Get all business categories in terms of id => title.
	 */
	public static function get_offers_categories_dropdown_format( $empty_init = false, $order=1 ) {
		return self::get_taxonomy_dropdown_format( 'offer-categories', $empty_init, $order );
	}


	private static function get_taxonomy_dropdown_format( $taxonomy, $empty_init = false, $order=1 ){

		$taxonomies = array();

		if ( $empty_init )
			$taxonomies[''] = '';

		$tax_terms = get_terms( $taxonomy, array( 'hide_empty' => false ) );
		$length = count( $tax_terms );

		if($order==1)
			foreach ($tax_terms as $tax) {
				$taxonomies[ $tax->name ] = ''.$tax->term_id;
			}
		elseif($order==2){
			foreach ($tax_terms as $tax) {
				$taxonomies[ ''.$tax->term_id ] = $tax->name;
			}
		}

		return $taxonomies;
	}



	/**
	 * Get all locations in terms of id => title.
	 */
	public static function get_business_locations_dropdown_format( $empty_init = false ) {

		$locations = array();
		if ( $empty_init )
			$locations[''] = '';

		$qry_args = array(
			'post_status' => 'publish',
			'post_type' => 'wyz_location',
			'orderby' => 'title',
			'order' => 'ASC',
			'posts_per_page' => - 1,
		);
		$posts_array = get_posts( $qry_args );
		foreach ($posts_array as $post) {
			$locations[ $post->post_title ] = ''.$post->ID;
		}

		return $locations;
	}




	/**
	 * Get all business categories.
	 */
	public static function get_business_categories() {
		if ( method_exists( 'WyzHelpersOverride', 'get_business_categories') ) {
			return WyzHelpersOverride::get_business_categories();
		}

		$taxonomies = array();
		$taxonomy = 'wyz_business_category';
		$tax_terms = get_terms( $taxonomy, array( 'hide_empty' => false ) );
		$length = count( $tax_terms );
		for ( $i = 0; $i < $length; $i++ ) {
			if ( ! isset( $tax_terms[ $i ] ) ) {
				continue;
			}
			$temp_tax = array();
			$obj = $tax_terms[ $i ];
			if ( 0 == $obj->parent ) {
				$temp_tax['id'] = $obj->term_id;
				$temp_tax['name'] = $obj->name;
				$temp_tax['children'] = array();
				$temp_child = array();
				for ( $j = 0; $j < $length; $j++ ) {
					if ( ! isset( $tax_terms[ $j ] ) ) {
						continue;
					}
					$tmp = $tax_terms[ $j ];
					if ( $tmp->parent == $obj->term_id ) {
						$temp_child['id'] = $tmp->term_id;
						$temp_child['name'] = $tmp->name;
						$temp_tax['children'][] = $temp_child;
						unset( $tax_terms[ $j ] );
					}
				}
				$taxonomies[] = $temp_tax;
				unset( $tax_terms[ $i ] );
			}
		}
		return $taxonomies;
	}

	public static function new_wcmp_installed() {
		if( ! defined( 'WCMp_PLUGIN_VERSION' ) ) return false;
		$version = explode( '.', WCMp_PLUGIN_VERSION );
		return intval( $version[0] ) >= 3;
	}

	public static function product_need_display_wyzi_edit_link() {
		if ( method_exists( 'WyzHelpersOverride', 'product_need_display_wyzi_edit_link') ) {
			return WyzHelpersOverride::product_need_display_wyzi_edit_link();
		}
		if ( self::new_wcmp_installed() && ! apply_filters( 'wyzi_display_wyzi_product_edit_link', false ) )return false;
		return current_user_can('edit_published_products') && ( ! function_exists( 'get_wcmp_vendor_settings' ) || get_wcmp_vendor_settings( 'is_edit_delete_published_product', 'capabilities', 'product') == 'Enable' );
	}


	public static function add_product_link( $page ) {
		if ( method_exists( 'WyzHelpersOverride', 'add_product_link') ) {
			return WyzHelpersOverride::add_product_link($page);
		}
		switch ( $page ) {
			case 'wall':
				echo '<div class="float-right" style="margin-bottom: 10px;"><a class="wyz-edit-btn btn-blue" href="';
				if ( self::new_wcmp_installed() )
					echo esc_url( wcmp_get_vendor_dashboard_endpoint_url( get_wcmp_vendor_settings( 'wcmp_add_product_endpoint', 'vendor', 'general', 'add-product' ) ) );
				else
					echo esc_url( home_url( '/user-account' ) ) . '?product_id=1';
				echo '">'.esc_html__( 'Add New', 'wyzi-business-finder' ) . '</a></div>';
				break;
		}
	}

	public static function make_user_vendor( $user ) {
		$user->remove_role( 'subscriber' );
		$user->remove_role( 'customer' );
		$user->remove_cap( 'dc_pending_vendor' );
		$user->remove_role( 'dc_pending_vendor' );
		$user->remove_role( 'client' );
		$user->add_role( 'business_owner' );
		$user->add_role( 'dc_vendor' );
		$user->add_cap( 'dc_vendor' );
		if ( function_exists( 'get_wcmp_vendor' ) ) {
			$vendor = get_wcmp_vendor( $user->ID );
			if ($vendor) {
				$vendor->generate_shipping_class();
				$vendor->generate_term();
			}
		}
	}

	/**
	 * Display business category dropdown filter.
	 */
	public static function wyz_business_category_filter() {
		if ( method_exists( 'WyzHelpersOverride', 'wyz_business_category_filter') ) {
			return WyzHelpersOverride::wyz_business_category_filter();
		}

		ob_start();

		$taxonomies = self::get_business_categories();
		$sector = get_queried_object()->name;
		$len = count( $taxonomies );?>
		<div id="cat-filter-mobile-trigger" class="filter-mobile-trigger wyz-primary-color wyz-prim-color">
			<i class="fa fa-search"></i>
		</div>
		<select id="wyz-cat-filter" class="wyz-input wyz-select">
			<option value=""><?php echo apply_filters( 'wyz_categories_filter_placeholder', esc_html__( 'categories ...', 'wyzi-business-finder' ) );?></option>
			<?php for ( $i = 0; $i < $len; $i++ ) {
				$img = self::get_category_icon( $taxonomies[ $i ]['id'] );
				$url = get_term_link( $taxonomies[ $i ]['id'], 'wyz_business_category' );
				$bgc = get_term_meta( $taxonomies[ $i ]['id'], 'wyz_business_cat_bg_color', true );
				echo '<option ' . ( $taxonomies[ $i ]['name'] == $sector ? 'selected ' : '' ) . 'value="' . esc_url( $url ) . '" ' . ( false != $img ? 'data-left="<div class=\'cat-prnt-icn\' ' . ( '' != $bgc ? 'style=\'background-color:' . esc_attr( $bgc ) . ';\' ' : '' ) .'><img class=\'lazyload\' data-src=\'' . $img . '\'/></div>"' : '') .' data-right=\'' . esc_url( $url ) . '\' >&nbsp;' . $taxonomies[ $i ]['name'] . '</option>';
				if ( isset( $taxonomies[ $i ]['children'] ) && ! empty( $taxonomies[ $i ]['children'] ) ) {
					foreach ( $taxonomies[ $i ]['children'] as $chld ) {
						$url = get_term_link( $chld['id'], 'wyz_business_category' );
						echo '<option ' . ( $chld['name'] == $sector ? 'selected ' : '' ) . 'value="' . esc_url( $url ) . '" data-right=\'' . esc_url( $url ) . '\'>' . $chld['name'] . '</option>';
					}
				}
			}?>

		</select>
		<?php  echo ob_get_clean();
	}
	
	public static function upload_limit_exceeded() {
		$user_id = get_current_user_id();
		$sub_mode = get_option( 'wyz_sub_mode_on_off', 'off');
		$max_imgs_opt = intval( get_option( 'wyz_max_attchmtn_count', -1 ) );
		$max_imgs_sub = self::wyz_sub_can_bus_owner_do( $user_id, 'wyzi_max_attchmtn_count' );
		if ( false === $max_imgs_sub ) $max_imgs_sub = -1;
		$max_imgs_sub = intval( $max_imgs_sub );

		if ( 'off' == $sub_mode && 0 > $max_imgs_opt )
			return false;

		if ( 'on' == $sub_mode ) {
			if ( is_nan( $max_imgs_sub ) || 0 > $max_imgs_sub )
				return false;
		}

		$attachments = get_posts(array(
			'post_type'=> 'attachment',
			'posts_per_page' => -1,
			'author' => $user_id,
			'fields' => 'ids'
		));

		$count = count( $attachments);

		if ( 'on' == $sub_mode ) {
			if ( $count >= $max_imgs_sub )
				return true;
		}

		if ( is_nan( $max_imgs_opt ) || 0 > $max_imgs_opt )
			return false;


		if ( $count >= $max_imgs_opt )
			return true;
		return false;
	}

	/**
	 * Display locations dropdown filter.
	 */
	public static function wyz_locations_filter( $is_map, $last = false, $filt_type = '', $def='' ) {

		if ( method_exists( 'WyzHelpersOverride', 'wyz_locations_filter') ) {
			return WyzHelpersOverride::wyz_locations_filter( $is_map );
		}

		$filter_type = '';
		if ( $is_map )
			$filter_type = ! empty($filt_type) ? $filt_type : get_post_meta( get_the_ID(), 'wyz_map_location_filter_type', true );


		if ( '' == $filter_type )
			$filter_type = 'dropdown';

		if( 'text' == $filter_type ) {
			?>
			<div class="bus-filter input-box input-location<?php echo ( $last ? ' last' : '' );?>">
				<input type="text" name="wyz-loc-filter-txt" id="wyz-loc-filter-txt" placeholder="<?php 
				echo apply_filters( 'wyz_locations_filter_placeholder', esc_html__( 'locations...', 'wyzi-business-finder' ) );
				?>"/>
				<input type="hidden" id="loc-filter-txt" name="loc-filter-txt" />
				<input type="hidden" id="loc-filter-lat" name="loc-filter-lat" />
				<input type="hidden" id="loc-filter-lon" name="loc-filter-lng" />
			</div>
			<?php
			return;
		}


		$qry_args = array(
			'post_status' => 'publish',
			'post_type' => 'wyz_location',
			'post_parent' => 0,
			'orderby' => 'title',
			'order' => 'ASC',
			'posts_per_page' => - 1,
		);

		$location = isset( $_GET['location'] ) ? intval( $_GET['location'] ) : 0;

		$def_image = plugins_url( 'img/default-location.png', __FILE__ );

		$imgt = '';
		if ( function_exists( 'wyz_get_option') ) {
			$imgt = wyz_get_option( 'default-location-logo' );
		}
		if ( ! empty( $imgt ) ) {
			$def_image = $imgt;
		}

		$all_posts = new WP_Query( $qry_args );?>

		<div class="bus-filter input-box input-location map-locations<?php echo ( $last ? ' last' : '' );?>">

		<select id="wyz-loc-filter" name="location" class="wyz-input wyz-select">
			<option value=""><?php 
			echo apply_filters( 'wyz_locations_filter_placeholder', esc_html__( 'location...', 'wyzi-business-finder' ) );?>
			</option>

		<?php 
		$def_loc_id = get_post_meta( get_the_ID(), 'wyz_def_image_location', true );
		if ( $def_loc_id == '' || $def_loc_id < 1 )
			$def_loc_id = -1;

		$selected =  !empty( $def ) ? $def : get_post_meta( get_the_ID(), 'wyz_def_map_location', true );
		$def_loc_id = ( -1 == $selected ) ? $def_loc_id : $selected;

		while ( $all_posts->have_posts() ) {
			$all_posts->the_post();
			$l_id = get_the_ID();
			if ( has_post_thumbnail() ) {
				$img = '<img data-src="' . get_the_post_thumbnail_url( $l_id, array(50,50) ) . '" class="lazyload"/>';
			} else {
				$img = '<img class="lazyload" data-src="' . $def_image . '"/>';
			}
			if ( $is_map ) {

				$coor = get_post_meta( $l_id, 'wyz_location_coordinates', true );
				if(!is_array($coor))$coor=array('latitude'=>'','longitude'=>'');
				echo '<option value=\'{"id":"'.$l_id.'","lat":"'. $coor['latitude'] .'","lon":"'. $coor['longitude'] .'"}\' ' . ( ( $location == $l_id || $def_loc_id == $l_id ) ? 'selected' : '' ) . ' data-left=\'' . $img . '\'>' . get_the_title() . '</option>';
			} else {
				echo '<option value="'.$l_id.'" ' . ( ( $location == $l_id || $def_loc_id == $l_id ) ? 'selected' : '' ) . ' data-left=\'' . $img . '\'>' . get_the_title() . '</option>';
			}
			$children = self::get_location_children_grand_children ( $l_id, true ) ;
			/*get_children( array(
				'post_parent' => $l_id,
				'post_status' => 'publish',
				'post_type' => 'wyz_location',
				'numberposts' => -1,
			));*/
			if ( ! empty( $children ) ) {
				foreach ($children as $child) {
					if ( $is_map ) {
						$coor = get_post_meta( $child, 'wyz_location_coordinates', true );
						if(!is_array($coor))$coor=array('latitude'=>'','longitude'=>'');
						echo '<option value=\'{"id":"'.$child.'","lat":"'. $coor['latitude'] .'","lon":"'. $coor['longitude'] .'"}\' ' . ( ( $location == $child || $def_loc_id == $child ) ? 'selected' : '' ) . '>&nbsp;&nbsp;&nbsp;&nbsp;' . get_the_title( $child ) . '</option>';
					} else {
						echo '<option value="'.$child.'" ' . ( ( $location == $child || $def_loc_id == $child ) ? 'selected' : '' ) . '>&nbsp;&nbsp;&nbsp;&nbsp;' . get_the_title( $child ) . '</option>';
					}
				}
			}

		}
		wp_reset_postdata();
		?>
		</select>

		</div>

		<?php

		wp_reset_postdata();
	}
	


	/**
	 * Display Business Categories dropdown filter.
	 */
	public static function wyz_categories_filter( $taxonomies, $last = false, $def='' ) {

		if ( method_exists( 'WyzHelpersOverride', 'wyz_categories_filter') ) {
			return WyzHelpersOverride::wyz_categories_filter( $taxonomies );
		}

		$len = count( $taxonomies );
		$category = isset( $_GET['category'] ) ? intval( $_GET['category'] ) : 0;
		$selected =  !empty($def)?$def:get_post_meta( get_the_ID(), 'wyz_default_map_category', true );
		if ( 0 == $category ) $category = $selected;
		?>
		<div class="bus-filter input-location input-box<?php echo ( $last ? ' last' : '' );?>">
		<select id="wyz-cat-filter" name="category" class="wyz-input wyz-select bus-filter-locations-dropdown">
			<option value=""><?php 
			echo apply_filters( 'wyz_categories_filter_placeholder', esc_html__( 'categories...', 'wyzi-business-finder' ) );?></option>
			<?php for ( $i = 0; $i < $len; $i++ ) {
				$url = self::get_category_icon( $taxonomies[ $i ]['id'] );
				$bgc = get_term_meta( $taxonomies[ $i ]['id'], 'wyz_business_cat_bg_color', true );
				echo '<option value="'.$taxonomies[ $i ]['id'].'" ' . ( $category == $taxonomies[ $i ]['id'] ? 'selected ' : '' ) . ( false != $url ? ' data-left="<div class=\'cat-prnt-icn\'' . ( '' != $bgc ? 'style=\'background-color:'.$bgc.';\'' : '' ) .'><img class=\'lazyload\' data-src=\''.$url.'\'></div>"' : '') . '>&nbsp;'.$taxonomies[$i]['name'].'</option>';
				if ( isset( $taxonomies[ $i ]['children'] ) && ! empty( $taxonomies[ $i ]['children'] ) ) {
					foreach ( $taxonomies[ $i ]['children'] as $chld ) {
						echo '<option ' . ( $category == $chld['id'] ? 'selected ' : '' ) . 'value="' . $chld['id'] . '">' . $chld['name'] . '</option>';
					}
				}
			}?>
		</select>
		</div>
		<?php
	}
	


	public static function wyz_get_business_filters( $inputs = array(), $echo = true ) {

		if ( method_exists( 'WyzHelpersOverride', 'wyz_get_business_filters') ) {
			return WyzHelpersOverride::wyz_get_business_filters( $inputs );
		}
		
		if ( empty( $inputs) ) $inputs = array('1','2','3','4');
		$keyword = '';
		$days_get = array();
		if ( isset( $_GET['keyword'] ) ) {
			$keyword = $_GET['keyword'];
		}
		if ( isset( $_GET['open_days'] ) ) {
			$days_get = $_GET['open_days'];
		}

		add_action( 'wp_footer', function(){
			$url = plugin_dir_url( __FILE__ );
			wp_enqueue_script( 'jQuery_tags_select', $url . 'js/selectize.min.js', array( 'jquery' ), false, true );
			wp_enqueue_script( 'business_archives_js', $url . 'js/archives.js', array( 'jQuery_tags_select' ), false, true );
			wp_localize_script( 'business_archives_js', 'WyzLocFilter', array( 'filterType' => ( 'text' == get_post_meta( get_the_ID(), 'wyz_image_location_filter_type', true ) ? 'text' : 'dropdown' ) ) );
			wp_enqueue_style( 'jQuery_tags_select_style', $url . 'css/selectize.default.css' );
		}, 10 );

		$filter_count = count( $inputs );

		if ( ! $echo )
			ob_start();
		?>

		<div class="location-search<?php echo " filter-count-$filter_count";?> filter-location-search">
			<form method="GET" action="<?php echo get_post_type_archive_link( 'wyz_business' );?>">
				
				<?php 
				$i =0;
				foreach( $inputs as $input ) {
					$i++;
					switch ($input) {
						case 1:
							self::keyword_filter( $keyword, $i == $filter_count );
							break;
						case 2:
							self::wyz_locations_filter( false, $i == $filter_count );
							break;
						case 3:
							self::wyz_categories_filter( self::get_business_categories(), $i == $filter_count );
							break;
						case 4:
							self::days_filter( $days_get, $i == $filter_count );
							break;
					}
				} ?>
				
				<div class="input-submit">
					<input type="submit" class="wyz-primary-color wyz-secon-color wyz-prim-color-hover wyz-secon-color" id="map-search-submit" value="<?php esc_html_e( 'Search', 'wyzi-business-finder' );?>"/>
				</div>
			</form>
		</div>
		<?php
		if ( ! $echo )
			return ob_get_clean();
	}

	public static function get_default_archive_map_coordinates() {
		$lat = floatval(get_option( 'wyz_businesses_default_lat', 0 ));
		$lon = floatval(get_option( 'wyz_businesses_default_lon', 0 ));
		if ( empty($lat || is_nan( $lat ) ) ) $lat = 0;
		if ( empty($lon || is_nan( $lon ) ) ) $lon = 0;
		return array( $lat, $lon );
	}
	


	private static function keyword_filter( $keyword, $last = false ) {
		?>
		<div class="bus-filter input-box input-keyword input-location<?php echo ( $last ? ' last' : '' );?>"><input name="keyword" type="text" id="search-keyword" placeholder="<?php echo apply_filters( 'wyz_keyword_filter_placeholder', esc_html__( 'Keyword', 'wyzi-business-finder') );?>" value="<?php echo $keyword;?>"></div>
		<?php
	}


	private static function days_filter( $days_get, $last = false ) {
		?>
		<div class="bus-filter input-keyword input-days input-box input-location<?php echo ( $last ? ' last' : '' );?>">
			<?php $days = array( 
				'mon' => esc_html__( 'Monday', 'wyzi-business-finder' ),
				'tue' => esc_html__( 'Tuesday', 'wyzi-business-finder' ),
				'wed' => esc_html__( 'Wednesday', 'wyzi-business-finder' ),
				'thur' => esc_html__( 'Thursday', 'wyzi-business-finder' ),
				'fri' => esc_html__( 'Friday', 'wyzi-business-finder' ),
				'sat' => esc_html__( 'Saturday', 'wyzi-business-finder' ),
				'sun' => esc_html__( 'Sunday', 'wyzi-business-finder' ),
			); ?>
			<select multiple name="open_days[]" id="wyz-day-filter" class="wyz-selectize-days-filter" data-selectator-keep-open="true" placeholder="<?php esc_html_e( 'Open Days', 'wyzi-business-finder' );?>">
				<?php
				foreach ( $days as $key => $value ) {
					echo '<option value="' . $key . '"';
					if ( ! empty( $days_get ) && in_array( $key, $days_get ) ) {
						echo ' selected="selected"';
					}
					echo  '>'. $value . '</option>';
				}
				?>
			</select>
			<div class="tagchecklist hide-if-no-js"></div>
		</div>
		<?php
	}


	public static function wyz_get_representative_business_category_id( $business_id ) {
		$cat_icon_id = get_post_meta( $business_id, 'wyz_business_category_icon', true );
		
		if ( '' != $cat_icon_id && wp_get_attachment_url( get_term_meta( $cat_icon_id, 'wyz_business_icon_upload', true ) ) ) {
			return $cat_icon_id;
		}

		$tmp_cats = get_the_terms( $business_id, 'wyz_business_category' );
		if ( ! $tmp_cats || is_wp_error( $tmp_cats ) ) {
			return false;
		}

		foreach ($tmp_cats as $tmp_cat) {
			if ( 0 == $tmp_cat->parent ) {
				return $tmp_cat->term_id;
			}
			$parent_cat = get_term( $tmp_cat->parent, 'wyz_business_category' );
			if ( ! is_wp_error( $parent_cat ) ) {
				$icon = get_term_meta( $parent_cat->term_id, 'wyz_business_icon_upload', true );
				if ( '' != $icon ) {
					return $parent_cat->term_id;
				}
			}
		}
	}

	public static function get_category_icon( $term_id ) {
		$cat_icn = wp_get_attachment_image_src( get_term_meta( $term_id, 'wyz_business_icon_upload', true ), 'thumbnail' );
		if ( empty( $cat_icn ) || empty( $cat_icn[0] ) )
			$cat_icn = WYZI_PLUGIN_URL . 'businesses-and-offers/businesses/images/default-category-icon.png';
		else
			$cat_icn = $cat_icn[0];

		return $cat_icn;
	}

	public static function get_default_gallery_cover( $id ) {
		return apply_filters( 'wyz_default_business_logo_path', plugin_dir_url( __FILE__ ) . 'img/gallery-placeholder.jpg', $id );
	}


	private static function radius( $x ) {
		return $x * pi() / 180;
	}

	public static function open_close ($b_id) {
	    
	    $retured_value = '';
	    $days_ids = array( 'open_close_monday', 'open_close_tuesday', 'open_close_wednesday',
						'open_close_thursday', 'open_close_friday', 'open_close_saturday', 'open_close_sunday' );
	    for( $i=0; $i<7; $i++)
				$days_arr[] =  get_post_meta( $b_id, 'wyz_' . $days_ids[ $i ], true ) ;
		$are_all_days_empty = true;
		// lets check if all fields are empty first so we return nothing
		for( $i=0; $i<7; $i++) {
			if ( empty( $days_arr[$i] ) ) continue;
			foreach ( $days_arr[$i] as $key => $value ) { 
			    if ( !empty($value['open']) || !empty($value['close']) ) {
			        $are_all_days_empty = false; break;
			    }
			}
		}

		if ($are_all_days_empty) 
		    return array(
				'txt' 	=> '',
				'bool' 	=> '',
		    ); 
		
		$current_day_of_the_week = date( "w");
		$current_day_converstion = $current_day_of_the_week -1;
		
		if ($current_day_of_the_week == 0 ) 
			$current_day_converstion = 6;
		$current_business_open = false;
		$current_business_status_txt = "Closed Now";
		
		if ( is_array( $days_arr[$current_day_converstion] ) ) {
			foreach ( $days_arr[$current_day_converstion] as $key => $value ) {
			    if ( !empty($value['open']) || !empty($value['close']) ) {
			      
			        if ( !empty($value['open']) && !empty($value['close']) ) {  
			            if (strtotime(date("H:i")) >  strtotime($value['open']) &&  strtotime(date("H:i")) < strtotime($value['close'])) {
			                $current_business_status_txt = "Open Now";
			                $current_business_open = true;
			                break;
			            }else {
			                $current_business_status_txt = "Closed Now";
			            }
			            
			        }  
			       if (empty($value['open'] ) && !empty($value['close'])) {
			           if (strtotime(date("H:i")) < strtotime($value['close']) ){
			               $current_business_status_txt = "Open Now";
			               $current_business_open = true;
			               break;
			           }else {
			              $current_business_status_txt = "Closed Now";
			           }
			           
			       }
			       if (empty($value['close'] ) && !empty($value['open'])) {
		    	           if (strtotime(date("H:i")) > strtotime($value['open']) ){
		    	               $current_business_status_txt = "Open Now";
		    	               $current_business_open = true;
		    	               break;
		    	           }else {
		    	               $current_business_status_txt = "Closed Now";
		    	           }
		    	         
		    	       }
			        if (empty($value['close'] ) && empty($value['open'])) {
			            $current_business_status_txt = "Closed Now";
			        }
			        
			    }
			    
			}
		}
		
	    return array(
			'txt' 	=> $current_business_status_txt,
			'bool' 	=> $current_business_open
		);
	}


	/**
	 * Calculates the distance between user and business (by ip as well).
	 *
	 * @param array $p1 first location.
	 * @param array $p2 second location.
	 */
	public static function get_user_business_distance( $id ){
     
		 $user_lat = '';
		 $user_long = '';
		 $exact = false;
		 $what_to_return = array(
		 	'distance' => 0,
		 	'text' => '',
		 	'link' => '',
		 	'pure_link'=>''
		 );

		 if(isset($_COOKIE['user_lat']) && isset($_COOKIE['user_long']))  {
		     
		     if ( $_COOKIE['user_lat'] != "NA"  && $_COOKIE['user_long'] != "NA" ){
				$user_lat = $_COOKIE['user_lat'];
				$user_long = $_COOKIE['user_long'];
				$exact = true;
		     }else {
		    $new_arr[]= unserialize(file_get_contents('http://www.geoplugin.net/php.gp?ip='.$_SERVER['REMOTE_ADDR']));
				$user_lat = $new_arr[0]['geoplugin_latitude'];
				$user_long = $new_arr[0]['geoplugin_longitude'];
		     }
		 } else {
		      $new_arr[]= unserialize(file_get_contents('http://www.geoplugin.net/php.gp?ip='.$_SERVER['REMOTE_ADDR']));
		      $user_lat = $new_arr[0]['geoplugin_latitude'];
		      $user_long = $new_arr[0]['geoplugin_longitude']; 
		 }

		if ( ! $exact )
			$what_to_return['text'] .= "~ ";

		$mapGPS = get_post_meta( $id, 'wyz_business_location', true );
		if ( ! isset( $mapGPS['latitude'] ) || '' == $mapGPS['latitude'] || ! isset( $mapGPS['longitude'] ) || '' == $mapGPS['longitude'] ) {
			$what_to_return['text'] = "Not Available";
		} else {
			$lat = $mapGPS['latitude'];
			$lon = $mapGPS['longitude'];
			$distance = self::get_driving_distance($user_lat, $lat, $user_long,$lon);
			$what_to_return['distance'] = $distance;
			$what_to_return['text'] = sprintf( esc_html__( "%s away", 'wyzi-business-finder' ), $distance['distance'] );
			$what_to_return['link'] .= '<a target="_blank" href="http://maps.google.com/?saddr='.$user_lat.','.$user_long.'&daddr='.$lat.','.$lon.'">' . esc_html__( 'Get Directions', 'wyzi-business-finder' ) . '</a>';
			$what_to_return['pure_link'] .= 'http://maps.google.com/?saddr='.$user_lat.','.$user_long.'&daddr='.$lat.','.$lon;
		}
		return $what_to_return;
	}


	/**
	 * Calculates the distance between 2 locations.
	 *
	 * @param array $p1 first location.
	 * @param array $p2 second location.
	 */
	public static function get_driving_distance($lat1, $lat2, $long1, $long2) {
		$url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=".$lat1.",".$long1."&destinations=".$lat2.",".$long2."&mode=driving&language=pl-PL&key=" . get_option( 'wyz_map_api_key' );
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		$response = curl_exec($ch);
		curl_close($ch);
		$response_a = json_decode($response, true);
		if ( !isset( $response_a['rows'] ) || !isset( $response_a['rows'][0]['elements'][0]['status'] ) || 'OK' != $response_a['rows'][0]['elements'][0]['status'] ){
			$dist = $time = $value = '';
		} else {
			$dist = $response_a['rows'][0]['elements'][0]['distance']['text'];
			$time = $response_a['rows'][0]['elements'][0]['duration']['text'];
			$value = $response_a['rows'][0]['elements'][0]['distance']['value'];
		}
		
		return array('distance' => $dist, 'value' => $value, 'time' => $time);
	}


	public static function get_business_range_radius_in_meters( $id ) {
		$range = get_post_meta( $id, 'wyz_range_radius', true );
		$return = 'mile' == get_option( 'wyz_business_map_radius_unit' ) ? ( $range * 0.9144 ) : $range;
		$altered_range = apply_filters( 'wyz_range_of_coverage_value', $range );
		if ( $altered_range != $range ) return $altered_range;
		return $return;
	}


	/**
	 * Calculates the distance between 2 locations.
	 *
	 * @param array $p1 first location.
	 * @param array $p2 second location.
	 */
	public static function get_distance_between( $p1, $p2 ) {
		$R = 6378137; // Earth’s mean radius in meter.
		$result = array();
		if ( is_array( $p2['lat'] ) ) {
			for ( $i=0; $i < count( $p2['lat'] ); $i++ ) {
				$result[] = self::the_distance( $p1, array( 'lat' => $p2['lat'][ $i ], 'lon' => $p2['lon'][ $i ] ) );
			}
			return $result;
		}
		return self::the_distance($p1, $p2);
	}

	private static function the_distance( $p1, $p2 ) {
		$R = 6378137;
		$dLat = self::radius( $p2['lat'] - $p1['lat'] );
		$dLong = self::radius( $p2['lon'] - $p1['lon'] );
		$a = sin( $dLat / 2 ) * sin( $dLat / 2 ) + cos( self::radius( $p1['lat'] ) ) * cos( self::radius( $p2['lat'] ) ) * sin( $dLong / 2 ) * sin( $dLong / 2 );
		$c = 2 * atan2( sqrt( $a ), sqrt( 1 - $a ) );
		$d = $R * $c;
		$radius_unit = get_option( 'wyz_business_map_radius_unit' );
		if ( 'mile' == $radius_unit ) {
			 return ( $d / 1000.0 )*0.621371; // Returns the distance in Miles.
		} else {
			return $d / 1000.0; // Returns the distance in kilometer.
		}
	}

	public static function wyz_strip_tags( $string, $allowable_tags = '' ) {
		$string = preg_replace( '@<(script|style)[^>]*?>.*?</\\1>@si', '', $string );
		$string = strip_tags( $string, $allowable_tags );

		return trim( $string );
	}


	public static function get_image( $id, $display_default = true ) {
		$img = get_post_meta( $id, 'wyz_business_header_image', true );
		if ( ! empty( $img ) )
			return $img;
		$attachments = get_post_meta( $id, 'business_gallery_image', true );
		$temp = '';
		if ( $attachments && ! empty( $attachments ) ) {
			$attachments = array_keys( $attachments );
			if ( ! is_array( $attachments ) ) {
				$temp = wp_get_attachment_image_src( $this->attachments, 'full' );
				$temp = $temp[0];
			} else {
				foreach ( $attachments as $attachment ) {
					$temp = wp_get_attachment_image_src( $attachment, 'full' );
					if ( '' != $temp ) {
						$temp = $temp[0];
						break;
					}
				}
			}
		}
		if( empty( $temp ) && $display_default ){
				$temp = self::get_default_gallery_cover( $id );
		}
		return $temp;
	}


	/**
	 * Check if a user is vendor
	 */
	public static function is_user_vendor( $user ) {
		return function_exists('is_user_wcmp_vendor') && is_user_wcmp_vendor( $user );
	}

	/**
	 * Check if a user has a draft business
	 *
	 * @return boolean If the user has a business, draft
	 * @param integer $user_id user id.
	 */
	public static function wyz_user_has_draft_business( $user_id ) {
		$query = new WP_Query( array(
			'post_type' => 'wyz_business',
			'posts_per_page' => '1',
			'author' => $user_id,
			'post_status' => array( 'draft' ),
		) );
		$id = false;
		if ( $query->have_posts() ) {
			$query->the_post();
			$id = get_the_ID();
		}
		wp_reset_postdata();
		return $id;
	}


	/**
	 * Check if a user has a draft offer
	 *
	 * @return boolean If the user has an offer, draft
	 * @param integer $user_id user id.
	 */
	public static function wyz_user_has_draft_offer( $user_id ) {
		$query = new WP_Query( array(
			'post_type' => 'wyz_offers',
			'posts_per_page' => '1',
			'author' => $user_id,
			'post_status' => array( 'draft' ),
		) );
		$id = false;
		if ( $query->have_posts() ) {
			$query->the_post();
			$id = get_the_ID();
		}
		wp_reset_postdata();
		return $id;
	}

	/**
	 * Check if a user has a draft event
	 *
	 * @return boolean If the user has an offer, draft
	 * @param integer $user_id user id.
	 */
	public static function wyz_user_has_draft_event( $user_id ) {
		$query = new WP_Query( array(
			'post_type' => 'tribe_events',
			'posts_per_page' => '1',
			'author' => $user_id,
			'post_status' => array( 'draft' ),
		) );
		$id = false;
		if ( $query->have_posts() ) {
			$query->the_post();
			$id = get_the_ID();
		}
		wp_reset_postdata();
		return $id;
	}


	/**
	 * Gets all available locations as an array ( ID => Name).
	 */
	public static function get_businesses_locations_options() {

		$qry_args = array(
			'post_status' => 'publish',
			'post_type' => 'wyz_location',
			'post_parent' => 0,
			'posts_per_page' => - 1,
			'orderby' => 'title',
			'order' => 'ASC',
			'fields' => 'ids'
		);

		$all_posts = new WP_Query( $qry_args );
		$locs = array();
		$locs[''] = '';
		foreach ( $all_posts->posts as $id ) {
			$locs[ $id ] = get_the_title( $id );
			$children = self::get_location_children_grand_children( $id );
			if ( ! empty( $children ) ) {
				foreach ($children as $child) {
					$locs[ $child ] = '&nbsp;&nbsp;&nbsp;'.get_the_title( $child );
				}
			}
		}
		wp_reset_postdata();
		return $locs;
	}

	public static function get_appointment_note_form() {
		return '<div class="app-note-cont input-box"><input type="text" class="app-note" placeholder="'.esc_html__( 'Aproval/rejection note', 'wyzi-business-finder' ).'"/></div>';
	}


	/**
	 * Map/Image search handler
	 */
	public static function wyz_handle_business_search( $keywords, $cat_id, $loc_id, $rad, $lat, $lon, $page ) {

		if ( method_exists( 'WyzHelpersOverride', 'wyz_handle_business_search') ) {
			return WyzHelpersOverride::wyz_handle_business_search( $keywords, $cat_id, $loc_id, $rad, $lat, $lon, $page );
		}

		$loc_radius_search = false;
		$bus_names = $keywords;

		if ( ! $rad || '' == $rad || ! is_numeric( $rad ) ) {
			$rad = 0;
			$lat = $lon = 0;
		}
		elseif ( ! $lat || ! $lon || empty( $lat ) || empty( $lon ) ) {
			$lat = $lon = 0;
		}

		//if we have radius search,and country search, search by the radius with respect to location
		if ( 0 != $rad && '' != $loc_id && '0' < $loc_id ) {

			$loc_coor = get_post_meta( $loc_id, 'wyz_location_coordinates', true );
			if ( ! empty( $loc_coor ) && ! empty( $loc_coor['latitude'] )  && ! empty( $loc_coor['longitude'] ) ) {
				$lat = $loc_coor['latitude'];
				$lon = $loc_coor['longitude'];
				$loc_radius_search = true;
			}
			$children = self::get_location_children_grand_children( $loc_id );
			/*get_children( array(
				'post_parent' => $loc_id,
				'post_status' => 'publish',
				'post_type' => 'wyz_location',
				'numberposts' => -1,
			));*/
			if ( ! empty( $children ) ) {
				if ( $lat && ! empty( $lat ) ) {
					$lat = array($lat);
					$lon = array($lon);
				} else {
					$lat = array();
					$lon = array();
				}
				foreach ($children as $child) {
					$loc_coor = get_post_meta( $child, 'wyz_location_coordinates', true );
				}
				if ( ! empty( $loc_coor ) && ! empty( $loc_coor['latitude'] )  && ! empty( $loc_coor['longitude'] ) ) {
					$lat[] = $loc_coor['latitude'];
					$lon[] = $loc_coor['longitude'];
					$loc_radius_search = true;
				}
			}
		}

		if ( '' != $bus_names )
			$bus_names_arr = explode( ' ', $bus_names );
		else
			$bus_names_arr = array();

		$meta_query = '';
		$loc_query = '';

		if ( '' != $loc_id && '0' < $loc_id && ! $loc_radius_search ) {
			$meta_query = array( 'relation' => 'AND' );
			$children = get_children( array(
				'post_parent' => $loc_id,
				'post_status' => 'publish',
				'post_type' => 'wyz_location',
				'numberposts' => -1,
			));
			$locations_chld = array( $loc_id );
			if ( ! empty( $children ) ) {
				foreach ($children as $child) {
					$locations_chld[] = $child;
				}
			}

			if ( empty( $bus_names_arr ) ) {
				$meta_query[] = array( 'key' => 'wyz_business_country', 'value' => $locations_chld, 'compare' => 'IN' );
			} else {
				$loc_query = $locations_chld;
			}
			
			if(!empty($bus_names)){
				$meta_query[] = array( // Include excerpt and slogan in global map search.
					'relation' => 'OR',
					array( 'key' => 'wyz_business_excerpt', 'value' => $bus_names, 'compare' => 'LIKE' ),
					array( 'key' => 'wyz_business_slogan', 'value' => $bus_names, 'compare' => 'LIKE' ),
				);
			}
		} elseif( ! empty( $bus_names ) ) {
			$meta_query = array( // Include excerpt and slogan in global map search.
				'relation' => 'OR',
				array( 'key' => 'wyz_business_excerpt', 'value' => $bus_names, 'compare' => 'LIKE' ),
				array( 'key' => 'wyz_business_slogan', 'value' => $bus_names, 'compare' => 'LIKE' ),
			);
		}

		$args = array(
			'post_type' => 'wyz_business',
			'posts_per_page' => get_option( 'wyz_map_max_ajax_load',400 ),
			'offset' => $page,
			'post_status' => array( 'publish' ),
		);

		if (! empty($loc_query))
			$args['loc_query'] = $loc_query;


		$tax_query = array();

		if ( ! empty( $bus_names_arr ) ) {
			$tag_ids = self::get_tax_like_ids( 'wyz_business_tag', $bus_names_arr );
			if ( ! empty( $tag_ids ) ) {
				$tax_query = array(
					array(
						'taxonomy' => 'wyz_business_tag',
						'field'    => 'term_id',
						'terms' => $tag_ids,
					),
				);
			}
			if ( '' !== $cat_id && 0 < $cat_id ){
				$args['cat_query'] = $cat_id;
			}
		} elseif ( '' !== $cat_id && 0 < $cat_id ) {
			$tax_query = array(
				array(
					'taxonomy' => 'wyz_business_category',
					'field'    => 'term_id',
					'terms' => $cat_id,
				),
			);
		}
		


		if ( '' != $meta_query ) {
			$args['meta_query'] = $meta_query;
		}

		if ( ! empty( $bus_names_arr ) ) {

			$args['_meta_or_title'] = $bus_names_arr;
			$args['my_tax_query'] = $tax_query;
			$args['_meta_or_tax'] = true;
		} elseif ( isset( $tax_query ) && ! empty( $tax_query) ) {
			$args['tax_query'] = $tax_query;
		}

		return array(
			'query' => $args,
			'lat' => $lat,
			'lon' => $lon,
		);
	}

	public static function get_wp_timezone() {

        $timezone_string = get_option( 'timezone_string' );

        if ( ! empty( $timezone_string ) ) {
            return sprintf( esc_html__( 'UTC %s', 'wyzi-business-finder') , $timezone_string );
        }

        $offset  = get_option( 'gmt_offset' );
        $hours   = (int) $offset;
        $minutes = ( $offset - floor( $offset ) ) * 60;
        $offset  = sprintf( '%+03d:%02d', $hours, $minutes );
        
        return sprintf( esc_html__( 'UTC %s', 'wyzi-business-finder') , $offset );
    }


	public static function get_location_children_grand_children ( $loc_id, $exclude_parent = false ) {
		$children_grend_children = self::get_children_grand_children( array( $loc_id ), 'wyz_location', 'publish' );
		if ( ! empty( $children_grend_children ) ){
			if ( ! $exclude_parent )
				$children_grend_children[] = $loc_id;
			if ( ! is_array( $children_grend_children ) )
				$children_grend_children = array( $children_grend_children );
			return $children_grend_children;
		} else {
			$loc_id = array( $loc_id );
			return $loc_id;
		}
	}

	public static function get_children_grand_children( $ids, $post_type, $post_status ){
		global $wpdb;
		if ( empty($ids))return array();
		$new_ids = array();
		foreach ($ids as $id) {
			$t = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type='%s' AND post_status='%s' AND post_parent=%d;", $post_type, $post_status, $id ) );
			if ( ! empty( $t ) )
				$new_ids = array_merge( $new_ids, $t );
		}
		$next = self::get_children_grand_children( $new_ids, $post_type, $post_status );
		if ( ! empty( $next ) )
			$new_ids = array_merge( $new_ids, $next );
		return $new_ids;
	}


	/**
	 * Check if a user has a business
	 *
	 * @return boolean If the user has a business or not.
	 * @param integer $user_id user id.
	 */
	public static function wyz_has_business( $user_id, $status = '' ) {

		$user_businesses = get_user_meta( $user_id, 'wyz_user_businesses', true );

		if ( empty( $user_businesses ) || ( ! isset( $user_businesses['pending'] ) && ! isset( $user_businesses['published'] ) ) || ( empty( $user_businesses['pending'] ) && empty( $user_businesses['published'] ) ) )
			return false;
		if ( ! empty( $status ) && ( ! isset( $user_businesses[ $status ] ) || empty( $user_businesses[ $status ] ) ) )
			return false;
		return true;
	}



	public static function user_owns_business( $business_id, $user_id ) {
		$business = get_post( $business_id );
		return $user_id == $business->post_author;
	}

	public static function add_extra_points( $user_id ) {
		if ( user_can( $user_id, 'publish_businesses' ) ) {
			if ( true == get_user_meta( $user_id, 'extra_points_added', true ) )return;
			$points = intval( get_option( 'wyz_add_points_registration', 0 ) );
			if ( $points ) {
				$available = get_user_meta( $user_id, 'points_available', true );
				if ( '' == $available ) $available = 0;
				$available = intval( $available );
				$available += $points;
				update_user_meta( $user_id, 'points_available', $available );
				update_user_meta( $user_id, 'extra_points_added', true );
			}
		}
	}


	/**
	 * Get count of all user's businesses
	 *
	 * @return int total number of user businesses
	 * @param integer $user_id user id.
	 */
	public static function get_user_businesses_count( $user_id  ) {
		$user_businesses = self::get_user_businesses( $user_id );
		$count = 0;
		foreach ( $user_businesses['pending'] as $business ) {
			$count++;
		}
		foreach ( $user_businesses['published'] as $business ) {
			$count++;
		}
		return $count;
	}

	public static function get_tax_like_ids( $tax, $keywords ) {
		$ids = array();
		$args             = array(
			'taxonomy'   => $tax,
			'orderby'    => 'id',
			'order'      => 'ASC',
			'hide_empty' => true,
			'fields'     => 'ids'
		);
		foreach ( $keywords as $k ) {
			$args['name__like'] = $k;
			$tmp = get_terms( $args );
			if ( ! is_wp_error( $tmp ) ) {
				$ids = array_merge( $ids, $tmp );
			}
		}
		$ids = array_unique( $ids );
		return $ids;
	}

	
	/**
	 * Close open tags in html string
	 *
	 * @return string the html string with closed tags
	 * @param string html string.
	 */
	public static function close_tags($html) {
		preg_match_all('#<(?!meta|img|br|hr|input\b)\b([a-z]+)(?: .*)?(?<![/|/ ])>#iU', $html, $result);
		$openedtags = $result[1];
		preg_match_all('#</([a-z]+)>#iU', $html, $result);
		$closedtags = $result[1];
		$len_opened = count($openedtags);
		if (count($closedtags) == $len_opened) {
			return $html;
		}
		$openedtags = array_reverse($openedtags);
		for ($i=0; $i < $len_opened; $i++) {
			if (!in_array($openedtags[$i], $closedtags)) {
				$html .= '</'.$openedtags[$i].'>';
			} else {
				unset($closedtags[array_search($openedtags[$i], $closedtags)]);
			}
		}
		return $html;
	} 


	/**
	 * Check if a user has enough points to register a business.
	 *
	 * @return boolean If the user has enough points to register a business.
	 * @param integer $user_id user id.
	 */
	public static function wyz_current_user_affords_business_registry( $user_id = -1) {

		if ( $user_id < 1 )
			$user_id = get_current_user_id();
		$points_available = get_user_meta( $user_id, 'points_available', true );
		if ( '' == $points_available ) {
			$points_available = 0;
		} else {
			$points_available = intval( $points_available );
		}
		$registery_price = get_option( 'wyz_businesses_registery_price' );
		if ( '' == $registery_price ) {
			$registery_price = 0;
		} else {
			$registery_price = intval( $registery_price );
			if ( $registery_price < 0 ){
				$registery_price = 0;
			}
		}
		return $points_available >= $registery_price;
	}


	/**
	 * Check if a user has enough points to register a job.
	 *
	 * @return boolean If the user has enough points to register a job.
	 * @param integer $user_id user id.
	 */
	public static function current_user_affords_job_registry( $user_id = 0 ) {

		if ( ! $user_id ) $user_id = get_current_user_id();
		$points_available = get_user_meta( $user_id, 'points_available', true );
		if ( '' == $points_available ) {
			$points_available = 0;
		} else {
			$points_available = intval( $points_available );
		}
		$registery_price = intval( get_option( 'wyz_job_submit_cost', 0 ) );

		return $points_available >= $registery_price;
	}


	/**
	 * Check if current user can rate.
	 * A user can rate if he is logged in, hasn't rated current business yet and is not the business owner.
	 *
	 * @return boolean If the user can rate or not.
	 */
	 public static function wyz_can_user_rate() {
		$can_rate = true;

		global $current_user;
		wp_get_current_user();
		$logged_in_user = is_user_logged_in();
		$id = get_the_ID();

		if ( $logged_in_user ) {
			$rates = get_post_meta( $id, 'wyz_business_rates', true );
			if ( self::wyz_is_current_user_author( $id ) ) {
				return false;
			}
			if ( is_array( $rates )  ) {
				foreach ( $rates as $key => $value ) {
					if ( $key == $current_user->ID ) {
						return false;
					}
				}
			} else {
				return false;
			}
		} else {
			return false;
		}
		return true;
	}

	/**
	 * Get image id from image url
	 *
	 * @param string $image_url the image url.
	 */
	public static function wyz_get_image_id( $image_url ) {
		global $wpdb;
		$attachment = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE guid='%s';", $image_url ) );
		return $attachment[0];
	}


	/**
	 * Send email.
	 *
	 * @param string $to email addresses to send message to.
	 * @param string $subject email subject.
	 * @param string $message message content.
	  */
	public static function wyz_mail( $to, $subject, $message, $type = '' ) {
		$allowed_html = wp_kses_allowed_html( 'post' );
		$message = wp_kses( $message, $allowed_html );
		$from = get_option( 'wyz_businesses_from_email' );
		$subject  = esc_html( get_bloginfo( 'name' ) ) . ' ' . $subject;
		$semi_rand = md5( time() );
		$mime_boundary = "==Multipart_Boundary_x{$semi_rand}x";
		$headers = 'From: ' . get_bloginfo( 'name' ) . ' <' . $from . '>';
		$headers .= "\nMIME-Version: 1.0\n" . "Content-type: text/html; charset=UTF-8;\n" . " boundary=\"{$mime_boundary}\"";

		if ( '' != $type ) {
			$subject = apply_filters( "wyzi-email-$type-subject", $subject, $to );
		}
		return wp_mail( $to, $subject, $message, $headers );
	}


	public static function send_user_verify_email( $user_email, $user_id ) {

		$token = self::encrypt( uniqid() );
		$redirect_link = home_url( "/email_verify?token=$token" );
		update_option( $token, $user_id );
		update_user_meta( $user_id, 'pending_user_token', $token );
		$user = get_userdata( $user_id );

		$subject = wyz_get_option( 'verify-mail-subject' );
		if ( empty($subject))
			$subject = (esc_html__( 'Registration Verification from', 'wyzi-business-finder' ) . ' {' . home_url() . '}');
		$message = wyz_get_option( 'verify-mail' );
		if ( empty( $message ) ) {
			$message = "%FIRSTNAME%, Follow the following link to complete your registration: $redirect_link";
		} else{
			$message = str_replace( '%LINK%', $redirect_link, $message );
			$message = str_replace( '%FIRSTNAME%', $user->first_name, $message );
			$message = str_replace( '%LASTNAME%', $user->last_name, $message );
		}

		$mail_sent = self::wyz_mail( $user_email, $subject, $message, 'verify' );
	}

	public static function encrypt( $string, $action = 'e' ) {
		// you may change these values to your own
		$secret_key = '49A84167D37CC';
		$secret_iv = '797CA97A8C98D';

		$output = false;
		$encrypt_method = "AES-256-CBC";
		$key = hash( 'sha256', $secret_key );
		$iv = substr( hash( 'sha256', $secret_iv ), 0, 16 );

		if( $action == 'e' ) {
			$output = base64_encode( openssl_encrypt( $string, $encrypt_method, $key, 0, $iv ) );
		}
		else if( $action == 'd' ){
			$output = openssl_decrypt( base64_decode( $string ), $encrypt_method, $key, 0, $iv );
		}

		return $output;
	}


	public static function business_has_offers( $b_id, $auth_id ) {
		$query = new WP_Query( array(
			'post_type' => 'wyz_offers',
			'posts_per_page' => 1,
			'post_status' => array( 'publish','pending' ),
			'fields' => 'ids',
			'author' => $auth_id,
			'meta_query' =>array(
				array (
					'key' => 'business_id',
					'value' => $b_id,
				)
			),
		) );
		return $query->have_posts();
	}

	public static function business_has_jobs( $b_id, $auth_id ) {
		$query = new WP_Query( array(
			'post_type' => 'job_listing',
			'posts_per_page' => 1,
			'post_status' => array( 'publish','pending' ),
			'fields' => 'ids',
			'author' => $auth_id,
			'meta_query' =>array(
				array (
					'key' => '_wyz_job_listing',
					'value' => $b_id,
				)
			),
		) );

		return $query->have_posts();
	}

	public static function business_has_products( $b_id, $auth_id ) {
		$query = new WP_Query( array(
			'post_type' => 'product',
			'posts_per_page' => 1,
			'post_status' => array( 'publish','pending' ),
			'fields' => 'ids',
			'author' => $auth_id,
			'meta_query' =>array(
				array (
					'key' => 'business_id',
					'value' => $b_id,
				)
			),
		) );
		return $query->have_posts();
	}

	/**
	 * Get current business author id.
	 */
	public static function wyz_the_business_author_id( $post_id = false ) {
		if ( ! $post_id ) 
			global $post;
		else
			$post = get_post( $post_id );

		if ( null == $post || ! isset( $post ) ) {
			return 0;
		}
		return $post->post_author;
	}

	/**
	 * Check Subscription Capability.
	 *
	 * @param string $user_id to check his capbabilities.
	 * @param string $extra_option is the option to check.
	  */
	public static function wyz_sub_can_bus_owner_do( $user_id, $extra_option ) {

		if ( 'off' == get_option( 'wyz_sub_mode_on_off', 'off') ||
				user_can( $user_id, 'manage_options' ) ||
				! function_exists( 'pmpro_getMembershipLevelForUser' ) ) {
			return true;
		}
		
		$wyzi_subscription_options = get_option ('wyzi_pmpro_subscription_options','not_found');

		if ( 'not_found' == $wyzi_subscription_options ) {

			return true;
		}

		$membership_level = pmpro_getMembershipLevelForUser( $user_id );

		if ( ! is_object( $membership_level ) || ! $membership_level->id || ! isset ( $wyzi_subscription_options[ $membership_level->id ] )
			|| ! isset( $wyzi_subscription_options[ $membership_level->id ][ $extra_option ] )
			|| empty( $wyzi_subscription_options[ $membership_level->id ][ $extra_option ] ) ) {

			return false;
		}

		return $wyzi_subscription_options[$membership_level->id][$extra_option];
		
	}


	/**
	 * Check if subscriber can create a business
	 *
	 * @param string $user_id to check his capbabilities.
	 */
	public static function user_can_create_business( $user_id ) {

		if ( user_can( $user_id, 'manage_options' ) )
			return true;

		if ( ! self::wyz_current_user_affords_business_registry() )
			return false;

		$count = self::get_user_businesses_count( $user_id );

		if ( 'on' == get_option( 'wyz_sub_mode_on_off', 'off' ) ) {


			$wyzi_subscription_options = get_option ('wyzi_pmpro_subscription_options','not_found');
			$membership_level = function_exists('pmpro_getMembershipLevelForUser') ? pmpro_getMembershipLevelForUser( $user_id ) : '';
			

			if ( ! is_object( $membership_level ) || ! $membership_level->id || ! isset ( $wyzi_subscription_options[ $membership_level->id ] )
				|| ! isset( $wyzi_subscription_options[ $membership_level->id ][ 'wyzi_sub_can_create_business' ] )
				|| empty( $wyzi_subscription_options[ $membership_level->id ][ 'wyzi_sub_can_create_business' ] ) ) {

				return false;
			}

			if ( $wyzi_subscription_options[ $membership_level->id ][ 'wyzi_sub_can_create_business' ] )

			$max = isset( $wyzi_subscription_options[ $membership_level->id ][ 'wyzi_sub_max_businesses' ] ) ? $wyzi_subscription_options[ $membership_level->id ][ 'wyzi_sub_max_businesses' ] : 1;

			return $count < $max;
		}

		$max = intval( get_option( 'wyz_max_allowed_businesses', 1) );

		
		return $count < $max;
	}


	/*
	 * check if user can edit produts in frontend
	 */
	public static function user_can_edit_products( $user_id = 0 ) {
		if ( !$user_id )
			$user_id = get_current_user_id();
		$wcmp_settings = get_option('wcmp_capabilities_product_settings_name', array());
		$can_edit = is_array( $wcmp_settings ) && array_key_exists('is_edit_delete_published_product', $wcmp_settings );
		return self::is_user_vendor( $user_id ) && $can_edit;
	}

	/*
	 * check if user can edit produts in frontend
	 */
	public static function user_can_publish_products( $user_id = 0 ) {
		if ( !$user_id )
			$user_id = get_current_user_id();
		return self::is_user_vendor( $user_id ) && 'on' == get_option( 'wyz_allow_front_end_submit','on' );
	}


	/**
	 * Check if subscriber can create a Job
	 *
	 * @param string $user_id to check his capbabilities.
	 */
	public static function user_can_create_job( $user_id ) {

		if ( 'on' != get_option( 'wyz_users_can_job' ) ) return false;

		if ( user_can( $user_id, 'manage_options' ) )
			return true;

		if ( 'on' == get_option( 'wyz_sub_mode_on_off', 'off' ) ) {

			$wyzi_subscription_options = get_option ('wyzi_pmpro_subscription_options','not_found');
			$membership_level = function_exists('pmpro_getMembershipLevelForUser') ? pmpro_getMembershipLevelForUser( $user_id ) : '';

			if ( ! is_object( $membership_level ) || ! $membership_level->id || ! isset ( $wyzi_subscription_options[ $membership_level->id ] )
				|| ! isset( $wyzi_subscription_options[ $membership_level->id ][ 'wyzi_sub_can_create_job' ] )
				|| empty( $wyzi_subscription_options[ $membership_level->id ][ 'wyzi_sub_can_create_job' ] ) ) {

				return false;
			}

			$cost = intval( get_option( 'wyz_job_submit_cost', 0 ) );

			if ( $wyzi_subscription_options[ $membership_level->id ][ 'wyzi_sub_can_create_job' ] ) {
				if ( self::user_exceeded_max_jobs( $user_id, $wyzi_subscription_options[ $membership_level->id ][ 'wyzi_sub_max_jobs' ] ) )
					return false;
			}
			return self::current_user_affords_job_registry( $user_id );
		}

		if ( self::user_exceeded_max_jobs( $user_id ) ) return false;

		return self::current_user_affords_job_registry( $user_id );
	}


	public static function user_exceeded_max_jobs( $user_id, $max_per_business = -1 ) {
		if ( -1 == $max_per_business )
			$max_per_business = intval( get_option( 'wyz_jobs_max', 0 ) );

		if ( $max_per_business < 1 ) return false;

		$args = array(
			'post_type' => 'job_listing',
			'posts_per_page' => -1,
			'post_status' => array( 'publish', 'pending' ),
			'fields' => 'ids',
			'author' => $user_id
		);
		$query = new WP_Query( $args );

		return $max_per_business <= intval( $query->post_count );
	}

	public static function delete_user_data( $user ){
		global $wpdb;
		$wpdb->query( 
			$wpdb->prepare( 
				"
				DELETE p, pm
				FROM $wpdb->posts p
				INNER 
				JOIN $wpdb->postmeta pm
				ON pm.post_id = p.ID
				WHERE p.post_author = %d;",
				$user->ID
			)
		);

		$wpdb->query( 
			$wpdb->prepare( 
				"
				DELETE FROM $wpdb->usermeta
				WHERE user_id = %d;",
				$user->ID
			)
		);
	}

	/**
	 * WordPress Remove Filter (remove_filter converted to remove_class_filter) to remove Filter/Action without Class Object access. 
	 *
	 * @param string $action        Filter to remove
	 * @param string $class         Class name for the filter's callback
	 * @param string $method        Method name for the filter's callback
	 */
	public static function wyz_remove_class_action ($action,$class,$method) {
	global $wp_filter ;
	if (isset($wp_filter[$action])) {
		$len = strlen($method) ;
		foreach ($wp_filter[$action] as $pri => $actions) {
			foreach ($actions as $name => $def) {
				if (substr($name,-$len) == $method) {
					if (is_array($def['function'])) {
						if (get_class($def['function'][0]) == $class) {
							if (is_object($wp_filter[$action]) && isset($wp_filter[$action]->callbacks)) {
								unset($wp_filter[$action]->callbacks[$pri][$name]) ;
							} else {
								unset($wp_filter[$action][$pri][$name]) ;
							}
						}
					}
				}
			}
		}
	}
	}

	/**
	 * Display locations dropdown filter.
	 */
	public static function get_date_components( $date, $format = '' ) {

		if ( method_exists( 'WyzHelpersOverride', 'get_date_components') ) {
			return WyzHelpersOverride::get_date_components( $date, $format );
		}
		if ( '' == $format )
			$format = get_option('date_format');
		return date_parse_from_format( $format, $date );
	}


	/**
	 * Get Single Business Tab Name directly
	 *
	 * @param string $tabname        The Name of the tab
	 */
	public static function wyz_get_bus_tab_id ($tabname) {
		$tab_id = '';
		$tab_data = get_option( 'wyz_business_tabs_order_data' );
		foreach($tab_data as $tab){
			if($tab['type'] == $tabname){
				$tab_id = $tab['urlid'];
				break;
			}
		}
		return $tab_id;
	}

}
