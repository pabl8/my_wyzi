<div class="row-fluid">		
	<div class="span12">
		<?php
		global $current_user;
		wp_get_current_user();
		$user_login = $current_user->user_login;

		$args = array(
			'post_type' => 'product',
			'post_status' => array('publish','pending'),
			'author' => $this->user_id,
			'posts_per_page' => -1,
			'fields' => 'ids',
		);

		$products_query = new WP_Query( $args );
		$posts = implode( ',', $products_query->posts );

		echo do_shortcode( "[wcmp_products per_page=-1 ids='$posts']" );
		?>
	</div>
</div>