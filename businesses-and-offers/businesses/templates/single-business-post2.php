<?php
/**
 * Template Name: Business Post Template.
 *
 * Used to display single business Post page.
 *
 * @package wyz
 */
 
get_header(); ?>

<div class="page-area section pt-90 pb-90">
	<div class="container">
		<div class="row">

			<?php get_template_part( 'sidebar/left-sidebar' ); ?>

			<div class="<?php if ( 'full-width' === wyz_get_option( 'sidebar-layout' ) ) { ?>col-md-12<?php } elseif ( 'off' === wyz_get_option( 'resp' ) ) { ?>col-xs-8<?php } else { ?>col-lg-9 col-md-8 col-xs-12<?php } ?>">

				<?php if ( have_posts() ) :
					the_post();?>

				<div class="blog-item-wide">
				<?php if ( has_post_thumbnail() ) {?>
					<div class="image"><?php the_post_thumbnail( 'large' );?>
					<?php if ( is_sticky() ) {?>
						<span class="badge"><i class="fa fa-thumb-tack pin"></i></span>
					<?php } ?>
					</div>
				<?php }
				
				$vid = get_post_meta( get_the_ID(), 'vid', true );
				if ( ! empty( $vid ) ) {
					echo $vid;
				}
				
				?>
					
					<div class="content">

						<div class="head fix">
							<div class="meta float-left">
								<div class="date"><a href="<?php echo esc_url( get_permalink() ); ?>"><?php the_time( get_option( 'date_format' ) );?></a></div>
								<div class="author"><?php echo sprintf( esc_html__( 'By: %s', 'wyzi-business-finder' ), get_the_author_posts_link() );?></div>
							</div>
							<div>
								<h3 class="title"><a class="wyz-secondary-color-text-hover" href="<?php echo esc_url( get_permalink() );?>"><?php esc_html( the_title() );?></a></h3>
								<div class="blog-meta-data">
									<span class="blog-meta"><i class="fa fa-comment" aria-hidden="true"></i><a href="<?php echo ( esc_url( get_permalink() ) . ( 'on' == wyz_get_option( 'sticky-menu') ? ( 0 == get_comments_number() ? '#respond' : '#comments' ) : '' ) );?>"><?php comments_number();?></a></span>
									<?php if ( has_category() ) {?>
									<span class="blog-meta"><i class="fa fa-folder-open" aria-hidden="true"></i><?php echo get_the_category_list( ', ', '', get_the_ID() );?></span>
									<?php } ?>
								</div>
							</div>
						</div>
						<p><?php the_content();?></p>
						<?php wyz_link_pages();?>
					</div>
					<div class="blog-meta-data fix">
						<?php if ( has_category() ) {?>
							<span class="blog-meta"><i class="fa fa-folder-open" aria-hidden="true"></i><?php echo get_the_category_list( ', ', '', get_the_ID() );?></span>
						<?php }
						if ( has_tag() ) {?>
						<span class="blog-meta"><i class="fa fa-tag" aria-hidden="true"></i><?php the_tags( '' );?></span>
						<?php }?>
					</div>
				</div>
				
				<?php wyz_pagination();?>
				<?php comments_template();
				endif; ?>
			</div>

			<?php get_template_part( 'sidebar/right-sidebar' ); ?>
		</div>
	</div>
</div>




<?php get_footer(); ?>