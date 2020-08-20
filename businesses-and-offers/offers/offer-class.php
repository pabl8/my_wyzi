<?php
/**
 * Offer creator.
 *
 * @package wyz
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

if (class_exists('WyzOffersOverride')) {
	class WyzOffersOverridden extends WyzOffersOverride { }
} else {
	class WyzOffersOverridden { }
}

/**
 * Class WyzOffer.
 */
class WyzOffer extends WyzOffersOverridden{


	/**
	 * Creates offer to display in offer archives page and offers slider.
	 *
	 * @param integer $id the offer id.
	 */
	public static function wyz_the_offer( $id, $is_arch, $template_type = '', $auto_expand = false ) {
		if ( method_exists( 'WyzOffersOverride', 'wyz_the_offer') ) {
			return WyzOffersOverride::wyz_the_offer( $id, $is_arch, $template_type = '', $auto_expand );
		}
		if ( '' == $template_type )
			$template_type = ( function_exists( 'wyz_get_theme_template' ) ? wyz_get_theme_template() : 1 );
		$template_type == 1 ? self::the_offer_1( $id, $is_arch, $auto_expand ) : self::the_offer_2( $id, $is_arch, $auto_expand );
	}

	public static function the_offer_1( $id, $is_arch, $auto_expand ) {
		if ( method_exists( 'WyzOffersOverride', 'the_offer_1') ) {
			return WyzOffersOverride::the_offer_1( $id, $is_arch, $auto_expand );
		}

		$ttl = get_the_title( $id );
		$exrpt = get_post_meta( $id, 'wyz_offers_excerpt', true );
		$is_business_page = is_singular( 'wyz_business' );
		$desc_arr = preg_split('/[\s]+/', WyzHelpers::wyz_strip_tags( apply_filters('the_content', get_post_field( 'post_content', $id ) ), '<table><td><tbody><tr><th>' ));
		$sliced = count( $desc_arr ) > 59;
		if ( ! $is_business_page && $sliced ) {
			$desc_arr = array_slice( $desc_arr, 0, 60 );
			$desc = WyzHelpers::close_tags( implode( ' ', $desc_arr ) );
			$desc .= '...';
		} else
			$desc = implode( ' ', $desc_arr );
		$bus_id = get_post_meta( $id, 'business_id', true );
		$icon = WyzHelpers::get_post_thumbnail( $bus_id, 'business', 'medium' );
		$logo_bg = get_post_meta( $bus_id, 'wyz_business_logo_bg', true );
		$image = get_post_meta( $id, 'wyz_offers_image_id', true );
		$cat = get_the_term_list( $id, 'offer-categories', '<span class="offer-tax">', ', ', '</span>' );
		if ( '' != $image ) {
			$image = wp_get_attachment_image( $image, 'large' );
		} 
		if ( ! $image || empty( $image ) )
			$image = WyzHelpers::get_post_thumbnail( $id, 'offer', 'large', array( 'class' => 'attachment-large size-large' ) );
		$dscnt = get_post_meta( $id, 'wyz_offers_discount', true );
		ob_start();?>

		<div id="post-<?php echo $id; ?>" class="wyz_offers type-wyz_offers sin-offer-item row<?php echo $is_arch ? ' the-offer' : '';?>">
			<div class="image <?php if ( 'on' == wyz_get_option( 'resp' ) ) { echo 'col-md-5 col-xs-12'; } else { echo 'col-xs-5'; }?> float-right"><?php echo $image;
				if ( 0 < $dscnt ) { ?>
				<span class="offer-label"><?php esc_html_e( 'DISCOUNT', 'wyzi-business-finder' );?> <?php echo esc_html( $dscnt );?>%</span>
				<?php }?>
			</div>
			<div class="content <?php if ( 'on' == wyz_get_option( 'resp' ) ) { echo (is_singular('wyz_business') ? '' : ' col-md-7' ) . 'col-xs-12'; } elseif(!is_singular('wyz_business')) { echo 'col-xs-7'; }?>">
				<div class="head fix">
					<?php if ( $icon && '' != $icon ) {?>
					<div class="logo float-right"><?php echo $icon; ?>
						<?php 
						if ( $is_business_page && WyzHelpers::user_owns_business( $id, get_current_user_id() ) ) {
							if ( 'off' != get_option( 'wyz_offer_editable' ) )
								echo '<a class="float-right wyz-edit-btn btn-blue" href="' . esc_url( home_url( '/user-account' ) ) . '?edit-offer=' . $id . '">' . esc_html__( 'Edit', 'wyzi-business-finder' ) . '</a>';
							echo '<a href="' . get_delete_post_link( $id ) . '" style="clear: right;" class="float-right wyz-edit-btn btn-red"  onclick="return confirm( \'' . esc_html__( 'Are you sure you want to delete this? This step is irreversible.', 'wyzi-business-finder' ) . '\');">' . esc_html__( 'Delete', 'wyzi-business-finder' ) . '</a>';
						}
						?>

					</div>
					<?php }?>
					<div class="text float-left"><h3><?php echo esc_html( $ttl ); ?></h3><?php if ( $cat ) echo $cat; ?><h4 class="wyz-secondary-color-text"><?php echo esc_html( $exrpt ); ?></h4></div>
				</div>
				<div class="offr-desc<?php echo $auto_expand ? ' auto-expand' : ''?>"><p><?php echo $desc; ?></p></div>
				<div class="offer-caps">
					<?php WyzPostShare::the_like_button( $id, 1 )?>
					<?php WyzPostShare::the_share_buttons( $id, 1, true );?>
				</div>
				<?php if ( ! $is_business_page ) { ?>
					<a href="<?php echo esc_url( get_post_permalink( $bus_id ) ) . "?view_offer=$id" ; ?>" class="view-offer wyz-button wyz-secondary-color icon"><?php esc_html_e( 'view offer', 'wyzi-business-finder' );?> <i class="fa fa-angle-right"></i></a>
					<?php
				} elseif ( $sliced && ! $auto_expand ) {
					?>
					<a href="#" class="view-offer wyz-button wyz-secondary-color icon expand-offer"><?php esc_html_e( 'expand offer', 'wyzi-business-finder' );?> <i class="fa fa-angle-down"></i></a>
					<?php
				}
				?>
			</div>
		</div>
		<?php echo ob_get_clean();
	}

	public static function the_offer_2( $id, $is_arch, $auto_expand ) {
		if ( method_exists( 'WyzOffersOverride', 'the_offer_2') ) {
			return WyzOffersOverride::the_offer_2( $id, $is_arch, $auto_expand );
		}
		
		$ttl = get_the_title( $id );
		$exrpt = get_post_meta( $id, 'wyz_offers_excerpt', true );

		$is_business_page = is_singular( 'wyz_business' );
		$desc_arr = preg_split('/[\s]+/', WyzHelpers::wyz_strip_tags( apply_filters('the_content', get_post_field( 'post_content', $id ) ), '<table><td><tbody><tr><th>' ));
		$sliced = count( $desc_arr ) > 59;
		if ( ! $is_business_page && $sliced ) {
			$desc_arr = array_slice( $desc_arr, 0, 60 );
			$desc = WyzHelpers::close_tags( implode( ' ', $desc_arr ) );
			$desc .= '...';
		} else
			$desc = implode( ' ', $desc_arr );

		$cat = get_the_term_list( $id, 'offer-categories', '<span class="offer-tax">', ', ', '</span>' );
		$image = get_post_meta( $id, 'wyz_offers_image_id', true );
		$image = wp_get_attachment_image( $image, 'large', false, array( 'class' => 'image' ) );

		if ( ! $image || '' == $image ) {
			$image = '<img src="'.WyzHelpers::get_default_image( 'offer' ).'"/>';
			$image_class = '';
		}
		$dscnt = get_post_meta( $id, 'wyz_offers_discount', true );
		$permalink = !$is_business_page ? ( esc_url( get_post_permalink( get_post_meta( $id, 'business_id', true ) ) ) . "?view_offer=$id" ) : '';
		?>
		<div class="offer-wrapper mb-20">
			<div id="post-<?php echo $id; ?>" class="wyz_offers type-wyz_offers">
				<h3>
					<?php if ( $is_arch ) echo '<a href="' . $permalink . '">';
					echo $ttl;
					if ( $is_arch ) echo '</a>';?>
				</h3>
				<!-- Offer Banner -->
				<a href="<?php echo $permalink;?>" class="offer-banner mb-20">
				<?php echo $image;?>
				<?php if ( 0 < $dscnt ) { ?>
				<span><?php echo esc_html( $dscnt );?>%</span>
				<?php }?>
				</a>
				<?php if ( $cat ) echo $cat; ?>
				<?php if ( $is_arch ) { ?>
				<div class="offer-caps">
					<?php WyzPostShare::the_like_button( $id, 2 )?>
					<?php WyzPostShare::the_share_buttons( $id, 2, true );?>
					<?php 
						if ( is_singular( 'wyz_business' ) && WyzHelpers::user_owns_business( $id, get_current_user_id() ) ) {
							if ( 'off' != get_option( 'wyz_offer_editable' ) )
								echo '<a class="float-right wyz-edit-btn btn-blue" style="clear: right;" href="' . esc_url( home_url( '/user-account' ) ) . '?edit-offer=' . $id . '">' . esc_html__( 'Edit', 'wyzi-business-finder' ) . '</a>';
							echo '<a href="' . get_delete_post_link( $id ) . '" style="clear: right;" class="float-right wyz-edit-btn btn-red"  onclick="return confirm( \'' . esc_html__( 'Are you sure you want to delete this? This step is irreversible.', 'wyzi-business-finder' ) . '\');">' . esc_html__( 'Delete', 'wyzi-business-finder' ) . '</a>';
						}
						?>
				</div>
				<?php }?>
				<h5><?php echo esc_html( $exrpt );?></h5>
			 	
				<div class="offr-desc<?php echo $auto_expand ? ' auto-expand' : ''?>"><p><?php echo $desc; ?></p></div>
				<?php if ( !$is_business_page ) { ?>
				<div class="offer-caps not-arch">
					<?php WyzPostShare::the_like_button( $id, 2 )?>
					<?php WyzPostShare::the_share_buttons( $id, 2, true );?>
				</div>
				<div class="clear"></div>
				<?php } elseif ( $sliced && ! $auto_expand ) {
					echo '<a class="com-name com-view-more wyz-primary-color-text wyz-prim-color-txt expand-offer" data-offset="1" href="#">' . esc_html__( 'expand offer', 'wyzi-business-finder' ) . '</a>';
				}?>
				<div></div>
			</div>
		</div>
		<?php
	}



	/**
	 * Display all Offers for a specific Business.
	 *
	 * @param integer $business_id the Business id to get the Offers for.
	 */
	public static function wyz_the_business_all_offers( $business_id ) {
		if ( method_exists( 'WyzOffersOverride', 'wyz_the_business_all_offers') ) {
			return WyzOffersOverride::wyz_the_business_all_offers( $business_id );
		}
		$offers_ids = self::wyz_get_business_all_offers_IDs( $business_id );
		if ( empty( $offers_ids ) ) {
			WyzHelpers::wyz_info( esc_html__( 'This business has no offers to display.', 'wyzi-business-finder' ) );
			return;
		}

		if ( isset( $_GET['view_offer'] ) ) {
			$view_offer = explode('#',$_GET['view_offer'])[0];
			if ( isset( $offers_ids[0] ) && $offers_ids[0] == $view_offer ){
				self::wyz_the_offer( $offers_ids[0], true, '', true );
				unset( $offers_ids[0] );
			}
		}

		foreach ( $offers_ids as $id ) {
			self::wyz_the_offer( $id, true );
		}
	}



	/**
	 * Gets all Offers related to Business with id: $business_id.
	 *
	 * @param integer $business_id the Business id to get the Offers for.
	 * @return array the offers ids.
	 */
	public static function wyz_get_business_all_offers_IDs( $business_id ) {
		if ( method_exists( 'WyzOffersOverride', 'wyz_get_business_all_offers_IDs') ) {
			return WyzOffersOverride::wyz_get_business_all_offers_IDs( $business_id );
		}

		if ( ! $business_id || 0 > $business_id ) {
			return array();
		}

		$query = new WP_Query( array(
			'post_type' => 'wyz_offers',
			'posts_per_page' => '-1',
			'post_status' => 'publish',
			'meta_key' => 'business_id',
			'meta_value' => $business_id,
			'fields' => 'ids'
		) );

		$ids = $query->posts;
		if ( is_singular('wyz_business') && isset($_GET['view_offer']) && in_array( $_GET['view_offer'], $ids) ) {
			$new_ids = array( $_GET['view_offer'] );
			foreach ($ids as $id) {
				if ( $id != $_GET['view_offer'] )
					$new_ids[] = $id;
			}
			$ids = $new_ids;
		}

		return $ids;
	}

	/**
	 * Get image id from url.
	 *
	 * @param string $image_url the imsge url.
	 */
	private static function wyz_get_img_id( $image_url ) {
		global $wpdb;
		$attachment = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE guid='%s';", $image_url ) );
		return isset( $attachment[0] ) ? $attachment[0] : '';
	}
}
?>
