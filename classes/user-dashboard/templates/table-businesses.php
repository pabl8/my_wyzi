<?php
require_once( plugin_dir_path( __FILE__ ) . 'table-listings.php' );

class WyzDashboardBusinessesTable extends WyzDashboardListingsTable {

	private $extra_args;
	public function __construct( $u_id, $extra_args = array() ) {
		parent::__construct( esc_html__( 'Businesses', 'wyzi-business-finder' ), $u_id, 'wyz_business' );
		$statusses = array('published', 'pending');
		if ( isset( $_GET['status'] ) && in_array( $_GET['status'], $statusses ) )
			$extra_args['post_status'] = ( 'published' == $_GET['status'] ? 'publish' : $_GET['status'] );
		$this->extra_args = $extra_args;
	}

	public function set_edit_condition() {
		return current_user_can( 'administrator' ) || ( 'off' != get_option( 'wyz_allow_business_edit' ) && WyzHelpers::wyz_sub_can_bus_owner_do( $this->user_id , 'wyzi_sub_can_edit_business' ) );
	}

	public function set_delete_condition() {
		return true;
	}

	public function the_table() {
		$this->table_start();
		$args = array(
			'post_type' => 'wyz_business',
			'post_status' => array( 'pending', 'publish' ),
			'posts_per_page' => -1,
			'author' => $this->user_id,
			'fields' => 'ids'
		);
		
		if ( ! empty( $this->extra_args ) ) {
			foreach ($this->extra_args as $key => $value) {
				if ( 'meta_query' == $key ) {
					foreach ($value as $v) {
						$args['meta_query'][]=$v;
					}
				} else {
					$args[ $key ] = $value;
				}
			}
		}

		$query = new WP_Query( $args );

		$names = array(
			'offers' => get_option( 'wyz_offer_plural_name', WYZ_OFFERS_CPT ),
			'products' => esc_html__( 'products', 'wyzi-business-finder' ),
			'jobs' => esc_html__( 'Jobs', 'wyzi-business-finder' ),
		);

		$page_permalink = get_the_permalink();
		while ( $query->have_posts() ) {
			$query->the_post();
			$id = get_the_ID();
			$has = array(
				'offers' => WyzHelpers::business_has_offers( $id, $this->user_id ),
				//'products' => WyzHelpers::business_has_products( $id, $this->user_id ),
				'jobs' => WyzHelpers::business_has_jobs( $id, $this->user_id )
			);
			ob_start();?>
			<div class="listing">
				<div class="logo"><?php the_post_thumbnail( 'medium' );?></div>
				<div class="listing-content">
					<h3><a href="<?php echo get_the_permalink();?>"><?php the_title();?></a></h3>
					<?php $this->display_status( get_post_status() );?>
					<p><?php echo get_post_meta( $id, 'wyz_business_excerpt', true );?></p>
					<?php WyzBusinessRating::rate_with_count( $id );?>
				</div>
				<div class="btns">
					<?php
					$is_fav = 'favorite' == $_GET['page'];
					$btn = '';
					/*if ( ! $is_fav && ( $has['offers'] || $has['jobs'] ) ){?>
		  				<?php $btn = '<a class="btn btn-success get-bus-listings-popup" title="' . esc_html__( 'View', 'wyzi-business-finder' ) . '" data-container="body" data-toggle="popover" data-placement="bottom" data-content="';
		  					foreach ($has as $key => $does) {
		  						if ( $does ) {
		  							$btn .= '<button class=\'btn get-business-listings\' action=\'ud_get_business_' . $key . '\'>' . $names[ $key ] . '</button>';
		  						}
		  					}
		  				$btn .= '" href="#" data-del="' . WyzHelpers::encrypt( $id ) .'"><i class="fa fa-list white" aria-hidden="true"></i></a>';
					}*/
					if ( $is_fav ) {
						$btn .= WyzPostShare::the_favorite_button( $id, false, true );
					}
					$actions = array();
					if( !$is_fav || current_user_can( 'edit_post', $id ) )
						$actions = array(
							'edit'=> add_query_arg( array( 'page' => 'add-edit-business', WyzQueryVars::EditBusiness => $id ), $page_permalink ),
							'delete' => esc_url( get_delete_post_link( $id ) )
						);
					$this->display_actions( $actions, $btn);
					?>
				</div>
			</div>
			<?php $this->add_column( ob_get_clean() );
		}
		wp_reset_postdata();
		$this->table_close();
	}
}
