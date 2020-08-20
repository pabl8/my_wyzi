<?php require_once( plugin_dir_path( __FILE__ ) . 'table-businesses.php' );?>
<div class="row-fluid">		
	<div class="span12">
		<div class="row-fluid">	
			<?php 
			$favorites = WyzHelpers::get_user_favorites( $this->user_id );
			if ( empty( $favorites ) )
				echo '<h3 class="main-notice">' . esc_html__( 'You don\'t have any favorites yet', 'wyzi-business-finder' ) . '</h3>';
			else {
				$extra_args = array( 'author' => '', 'post__in' => $favorites );
				$table = new WyzDashboardBusinessesTable( $this->user_id, $extra_args );
				$table->the_table();
			}
			?>
		</div>
	</div>
</div>