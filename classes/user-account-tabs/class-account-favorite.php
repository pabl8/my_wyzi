<?php

class AccountFavorite extends AccountContent {

	public function the_condition() { $this->condition = ( 'on' == get_option( 'wyz_enable_favorite_business' ) ); }

	public function _active () {
		return false;
	}

	public function tab_title () {
		$this->tab_title = esc_html__( 'Favorite', 'wyzi-business-finder' );
	}

	public function link () {
		$this->link = 'favorite';
	}

	public function icon () {
		$this->icon = 'star';
	}

	public function notifications() {
		return;
	}

	public function content() {
		$favorites = WyzHelpers::get_user_favorites( $this->user_id );
		if ( empty( $favorites ) )
			WyzHelpers::wyz_info( esc_html__( 'You don\'t have any favorites yet', 'wyzi-business-finder' ) );
		else {
			if ( $this->template_type == 1 ) {
				$query = new WP_Query(array('post_type'=>'wyz_business','post_status'=>array('publish'),'post__in' => $favorites ) );
				while($query->have_posts()){
					$query->the_post();
					echo WyzBusinessPost::wyz_create_business(true);
				}
				wp_reset_postdata();
			}
			elseif ( function_exists( 'wyz_get_option' ) ) {
				$grid_alias = wyz_get_option( 'listing_archives_ess_grid' );
				if ( '' != $grid_alias )
					echo do_shortcode( '[ess_grid alias="' . $grid_alias . '" posts='.implode(',',$favorites).']' );
			}
		}
	}
}

?>