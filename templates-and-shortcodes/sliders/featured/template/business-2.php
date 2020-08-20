<?php
$id = get_the_ID();
$rate_nb = get_post_meta( $id, 'wyz_business_rates_count', true );
$rate_sum = get_post_meta( $id, 'wyz_business_rates_sum', true );
$cntr = get_post_meta( $id, 'wyz_business_country', true );
$cntr_link = '';
if ( '' != $cntr && ! empty( $cntr ) ) {
	$cntr_link = get_post_type_archive_link( 'wyz_business' ) . '?location=' . $cntr;
}
$cntr = get_the_title( $cntr );
$image = WyzHelpers::get_image( $id );

if ( 0 == $rate_nb ) {
	$rate = 0;
} else {
	$rate = number_format( ( $rate_sum ) / $rate_nb, 1 ); 
} ?>

<div class="masonry-item <?php echo $class;?> mb-30">
	<div class="single-place">
		<div class="image"><img class="lazyload" data-src="<?php echo esc_url( $image );?>" alt=""></div>
		<div class="content fix">
			<p class="location">
				<i class="fa fa-map-marker"></i>
				<span><a href="<?php echo esc_url( $cntr_link );?>"><?php echo esc_html( $cntr );?></a></span>
			</p>
			<p class="rating wyz-prim-color-txt">
				<?php for( $i=0; $i < $rate; $i++ ) {?>
				<i class="fa fa-star"></i>
				<?php }
				for( $i= $rate; $i <= 5; $i++ ) {?>
				<i class="fa fa-star-o"></i>
				<?php }?>
			</p>
		</div>
		<a href="<?php echo esc_url( get_permalink() );?>" class="link"><i class="fa fa-link wyz-secon-color"></i></a>
	</div>
</div>

<?php 