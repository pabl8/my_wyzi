<?php
$id = get_the_ID();
$cat = get_the_term_list( $id, 'wyz_business_category', '', ' , ' );
$rate_nb = get_post_meta( $id, 'wyz_business_rates_count', true );
$rate_sum = get_post_meta( $id, 'wyz_business_rates_sum', true );
$logo_bg = get_post_meta( $id, 'wyz_business_logo_bg', true );
$content = WyzHelpers::wyz_strip_tags( get_post_meta( $id, 'wyz_business_description', true ), '<table><td><tr><th>' );
if ( strlen( $content ) > 160 ) {
	$content = WyzHelpers::substring_excerpt( $content, 160 ) . '...';
}

if ( 0 == $rate_nb ) {
	$rate = 0;
} else {
	$rate = number_format( ( $rate_sum ) / $rate_nb, 1 ); 
} 
$rep_img = WyzHelpers::get_image( $id, false );?>
<div class="sin-added-item sin-added-item-featured<?php echo $levit;?>">

<div class="sticky-notice featured-banner"><span class="wyz-primary-color"><?php esc_html_e( 'FEATURED', 'wyzi-business-finder' );?></span></div>
	<div class="inner">
		<a href="<?php echo esc_url( get_permalink() ); ?>" class="image">
			<div class="logo-cont" style="background-color:<?php echo esc_attr( $logo_bg );?>;">



				<div class="dummy"></div>

				<div class="img-container"<?php
				if ( ! empty( $rep_img ) ) echo ' style="background-image: url(' . $rep_img . ');"';
				?>>
					<div class="centerer"></div>
					<?php 
					echo WyzHelpers::get_post_thumbnail( $id, 'business', 'medium' );
					if ( ! empty( $logo_bg ) ) {
						echo '<div class="color-bg" style="background-color:' . $logo_bg . ';"></div>';
					}?>
				</div>
			</div>
		</a>
		<div class="text fix">
			<div class="ratting fix">
				<?php if ( 0 == $rate_nb ) {
					esc_html_e( 'no ratings yet', 'wyzi-business-finder' ) ;
				} else {
					for ( $i = 0; $i < 5; $i++ ) {
						if ( $rate > 0 ) {
							echo '<i class="fa fa-star"></i>';
							$rate--;
						} else {
							echo '<i class="fa fa-star-o"></i>';
						}
					}
				} ?>
			</div>
			<h2><a href="<?php echo esc_url( get_permalink() ); ?>"><?php the_title(); ?></a></h2>
			<div class="bus-term-tax"><?php echo get_the_term_list( $id, 'wyz_business_category', '', ', ', '' );?></div>
			<p><?php echo $content; ?></p>
			<a class="wyz-secondary-color-text" href="<?php echo esc_url( get_permalink() ); ?>"><?php esc_html_e( 'READ MORE', 'wyzi-business-finder' );?></a>
		</div>
	</div>
</div>

<?php 
