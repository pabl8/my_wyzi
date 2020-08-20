<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $job_manager;
global $user_is_author;

if ( $user_is_author ) {

	echo '<a class="float-left wyz-edit-btn clear btn-blue" href="' . esc_url( home_url( '/user-account' ) ) . '?action=edit&job_id=' . get_the_ID() . '">' . esc_html__( 'Edit', 'wyzi-business-finder' ) . '</a>';
	echo '<a href="?delete_job=' . get_the_ID() . '&nonce=' . wp_create_nonce( 'wyz_delete_job_' . get_the_ID() ) . '" class="float-left clear wyz-edit-btn btn-red"  onclick="return confirm( \'' . esc_html__( 'Are you sure you want to delete this job? This step is irreversible.', 'wyzi-business-finder' ) . '\');">' . esc_html__( 'Delete', 'wyzi-business-finder' ) . '</a>';
}
	?>
<a href="<?php the_permalink(); ?>">

	<?php 

	if ( get_option( 'job_manager_enable_types' ) ) { ?>
		<?php $types = wpjm_get_the_job_types(); ?>
		<?php if ( ! empty( $types ) ) : foreach ( $types as $type ) : ?>

			<div class="job-type <?php echo esc_attr( sanitize_title( $type->slug ) ); ?>"><?php echo esc_html( $type->name ); ?></div>

		<?php endforeach; endif; ?>
	<?php } ?>

	<?php if ( $logo = get_the_company_logo(get_the_ID(),'medium') ) : ?>
		<img src="<?php echo esc_attr( $logo ); ?>" alt="<?php the_company_name(); ?>" title="<?php the_company_name(); ?> - <?php the_company_tagline(); ?>" />
	<?php endif; ?>

	<div class="job_summary_content">

		<h1><?php wpjm_the_job_title(); ?></h1>

		<p class="meta"><?php the_job_location( false ); ?> &mdash; <?php the_job_publish_date(); ?></p>

	</div>
	<div class="clear"></div>
</a>
