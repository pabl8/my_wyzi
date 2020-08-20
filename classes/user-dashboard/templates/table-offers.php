<?php
require_once( plugin_dir_path( __FILE__ ) . 'table-listings.php' );

class WyzDashboardOffersTable extends WyzDashboardListingsTable {

	private $query;
	private $business_id;

	public function __construct( $u_id, $b_id = -1 ) {
		parent::__construct( esc_html__( 'Offers', 'wyzi-business-finder' ), $u_id, 'wyz_offer' );
		$this->business_id = $b_id;
	}

	public function set_edit_condition() {
		return current_user_can( 'administrator' ) || 'off' != get_option( 'wyz_offer_editable' );
	}

	public function set_delete_condition() {
		return true;
	}

	function init_query_args() {
		if ( ! empty( $this->query ) )
			return;
		$status = array( 'publish','pending' );
		$statusses = array('published', 'pending');
		if ( isset( $_GET['status'] ) && in_array( $_GET['status'], $statusses ) )
			$status = ( 'published' == $_GET['status'] ? 'publish' : $_GET['status'] );
		$query_args = array(
			'post_type' => 'wyz_offers',
			'posts_per_page' => -1,
			'post_status' => $status,
			'author' => $this->user_id,
			'fields' => 'ids'
		);
		if ( $this->business_id > 0 )
			$query_args['meta_query'] = array(
				array (
					'key' => 'business_id',
					'value' => $this->business_id,
				)
			);

		$this->query = new WP_Query( $query_args );
	}

	public function the_table() {
		$this->table_start();
		$this->init_query_args();
		echo $this->get_the_columns();
		$this->table_close();
	}

	public function get_the_columns( $array = false) {
		$this->init_query_args();
		$page_permalink = get_the_permalink();
		$columns = $array?array():'';
		while ( $this->query->have_posts() ) {
			$this->query->the_post();
			if($array)
				$columns[] = $this->_add_column( $page_permalink, true );
			else
				$columns .= $this->_add_column( $page_permalink, true );
		}
		wp_reset_postdata();
		return !empty($columns)?$columns:'';
	}

	public function _add_column( $page_permalink, $return = false ) {
		$id = get_the_ID();
		ob_start();?>
		<div class="listing">
			<div class="logo"><?php echo wp_get_attachment_image( get_post_meta( $id, 'wyz_offers_image_id', true ), 'thumbnail' );?></div>
			<div class="listing-content">
				<h3><a href="<?php echo get_the_permalink();?>"><?php the_title();?></a></h3>
				<?php $this->display_status( get_post_status() );?>
				<p><?php echo get_post_meta( $id, 'wyz_offers_excerpt', true );?></p>
				<div class="btns">
					<?php $this->display_actions(array(
						'edit'=> add_query_arg( array( 'page' => 'add-edit-offer', WyzQueryVars::EditOffer => $id ), $page_permalink ),
						'delete' => esc_url( get_delete_post_link( $id ) )
					));?>
				</div>
			</div>
		</div>
		<?php
		if ( $return )
			return $this->add_column( ob_get_clean(), true );
		$this->add_column( ob_get_clean() );
	}
}
