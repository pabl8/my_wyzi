<?php
/**
 * Template Name: Business Post Template.
 *
 * Used to display single business Post page.
 *
 * @package wyz
 */
 
get_header(); ?>

<div class="margin-bottom-100 margin-top-50">
	<div class="container">
		<div class="row">

			<?php get_template_part( 'sidebar/left-sidebar' ); ?>

			<div class="<?php if ( 'full-width' === wyz_get_option( 'sidebar-layout' ) ) { ?>col-md-12<?php } elseif ( 'off' === wyz_get_option( 'resp' ) ) { ?>col-xs-8<?php } else { ?>col-lg-9 col-md-8 col-xs-12<?php } ?>">

				<?php if ( have_posts() ) :
					the_post();?>

				<!-- Single Business Post -->
				<div class="sin-blog<?php if ( 0 < get_comments_number() || comments_open() ) { echo ' margin-bottom-50'; }?>">
				<?php if ( has_post_thumbnail() ) {?>
					<div class="blog-image"><?php the_post_thumbnail( 'large' );?></div>
				<?php }
				$vid = get_post_meta( get_the_ID(), 'vid', true );
				if ( ! empty( $vid ) ) {
					echo $vid;
				}
				?>
					
					<div class="content">
						<h2 class="title"><?php esc_html( the_title() );?></h2>
						<div class="blog-meta-data fix">
							<span class="blog-meta"><i class="fa fa-calendar" aria-hidden="true"></i><a href="<?php echo esc_url( get_day_link( get_the_time( 'Y' ), get_the_time( 'm' ), get_the_time( 'd' ) ) ); ?>"><?php the_time( get_option( 'date_format' ) );?></a></span>
							<span class="blog-meta"><i class="fa fa-user" aria-hidden="true"></i><?php the_author_posts_link();?></span>
							<span class="blog-meta"><i class="fa fa-comment" aria-hidden="true"></i><a href="<?php echo ( esc_url( get_permalink() ) . ( 0 == get_comments_number() ? '#respond' : '#comments' ) );?>"><?php comments_number();?></a></span>
							<?php if ( has_category() ) {?>
								<span class="blog-meta"><i class="fa fa-folder-open" aria-hidden="true"></i><?php echo get_the_category_list( ', ', '', get_the_ID() );?></span>
							<?php }
							if ( has_tag() ) {?>
							<span class="blog-meta"><i class="fa fa-tag" aria-hidden="true"></i><?php the_tags( '' );?></span>
							<?php }?>
						</div>
						<p><?php the_content();?></p>
						<?php wyz_link_pages();?>
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