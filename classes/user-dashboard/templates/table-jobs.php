<?php
require_once( plugin_dir_path( __FILE__ ) . 'table-listings.php' );

class WyzDashboardJobsTable extends WyzDashboardListingsTable {

	private $query;
	private $business_id;

	public function __construct( $u_id, $b_id = -1 ) {
		parent::__construct( esc_html__( 'Jobs', 'wyzi-business-finder' ), $u_id, 'job_listing' );
		$this->business_id = $b_id;
	}

	public function set_edit_condition() {
		return true;
	}

	public function set_delete_condition() {
		return true;
	}

	function init_query_args() {
		if ( ! emptY( $this->query ) )
			return;
		$query_args = array(
			'post_type' => 'job_listing',
			'posts_per_page' => -1,
			'post_status' => array( 'publish','pending' ),
			'author' => $this->user_id,
			'fields' => 'ids'
		);
		if ( $this->business_id > 0 )
			$query_args['meta_query'] = array(
				array (
					'key' => '_wyz_job_listing',
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

	protected function display_actions( $data, $extra_btns = '' ) {
		echo '<div class="actions">';
		if ( $this->edit_condition ) {
			echo '<a class="btn btn-info" title="' . esc_html__( 'Edit', 'wyzi-business-finder' ) . '" href="'.$data['edit'].'"><i class="fa fa-edit white" aria-hidden="true"></i></a>';
		}
		if ( $this->delete_condition ) {
			echo '<a class="btn btn-danger" title="' . esc_html__( 'Delete', 'wyzi-business-finder' ) . '" href="?delete_job=' . get_the_ID() . '&nonce=' . wp_create_nonce( 'wyz_delete_job_' . get_the_ID() ) . '" onclick="return confirm( \'' .esc_html__( 'Are you sure you want to delete this? This step is irreversible.', 'wyzi-business-finder' ) .'\' );"><i class="fa fa-trash white" aria-hidden="true"></i></a>';
		}
		echo '</div>';
	}

	public function _add_column( $page_permalink, $return = false ) {
		$id = get_the_ID();
		ob_start();?>
		<div class="listing">
			<div class="logo"><?php the_post_thumbnail( 'thumbnail' );?></div>
			<div class="listing-content">
				<h3><a href="<?php echo get_the_permalink();?>"><?php wpjm_the_job_title(); ?></a></h3>
				<?php $this->display_status( get_post_status() );?>
				<p><?php the_job_location( false ); ?> &mdash; <?php the_job_publish_date(); ?></p>
				<div class="btns">
					<?php
					if ( get_option( 'job_manager_enable_types' ) && function_exists( 'wpjm_get_the_job_types' ) ) {
						$types = wpjm_get_the_job_types();
						if ( ! empty( $types ) ) {
							foreach ( $types as $type ) { ?>
								<div class="job-type <?php echo esc_attr( sanitize_title( $type->slug ) ); ?>"><?php echo esc_html( $type->name ); ?></div>
							<?php }
						}?>
					<?php }
					$this->display_actions(array(
						'edit'=> add_query_arg( array( 'page' => 'add-edit-job', 'action' => 'edit', 'job_id' => $id ), $page_permalink ),
					));
					?>
				</div>
			</div>
		</div>
		<?php
		if ( $return )
			return $this->add_column( ob_get_clean(), true );
		$this->add_column( ob_get_clean() );
	}
}
