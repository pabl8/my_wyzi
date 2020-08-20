<?php defined('ABSPATH') OR die('restricted access'); return echo "Display blocked list"; ?>

<div class="oz-msg-system">
	
	<div class="oz-msg-menubar">
		<?php require_once $base_directory . '/views/menu.php'; ?>
	</div>

	<strong><?php echo esc_html( $message->post_title ); ?></strong>
	<p><?php echo esc_html( $message->post_content ); ?></p>
	<span><?php echo esc_html( $sender_name ); ?></span>

	<span>
		<?php
		echo esc_html(
			get_post_meta( $message->ID, 'message_sender_id', true )
		); ?>
	</span>

	<?php if ( $replies->have_posts() ) : ?>

		<?php while( $replies->have_posts() ) : $replies->the_post(); ?>
			<p><?php the_content(); ?></p>
		<?php endwhile; ?>

		<span><?php the_author_meta( 'display_name' ); ?></span>
	<?php endif; ?>

	<div class="reply">
		<form method="<?php echo $_SERVER['REQUEST_URI']; ?>" id="pm-reply">
			<strong><?php esc_html_e('Reply', 'wyzi-business-finder'); ?></strong>
			<textarea name="message"></textarea>

			<input type="hidden" name="message_id" value="<?php echo esc_attr( $message_id ); ?>" />
			<input type="hidden" name="secret" value="<?php echo wp_create_nonce( "private-message-{$message_id}" ); ?>" />

			<input type="hidden" name="action" value="oz_reply_message" />

			<button type="submit"><?php esc_html_e('Send', 'wyzi-business-finder'); ?></button>
		</form>
	</div>

</div>