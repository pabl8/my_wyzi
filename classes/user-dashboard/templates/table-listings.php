<?php
abstract class WyzDashboardListingsTable{

	protected $user_id;
	protected $post_type;
	protected $table_title;
	protected $edit_condition;
	protected $delete_condition;
	protected $table_statuses;

	public function __construct( $t_title, $u_id, $p_type ) {
		$this->table_title = $t_title;
		$this->user_id = $u_id;
		$this->post_type = $p_type;
		$this->edit_condition = $this->set_edit_condition();
		$this->delete_condition = $this->set_delete_condition();
		$this->init_table_statuses();
	}

	public abstract function set_edit_condition();
	public abstract function set_delete_condition();

	private function init_table_statuses() {
		$this->table_statuses = array(
			'pending' => '<span class="label label-pending">'.__( 'Pending','wyzi-business-finder').'</span>',
			'publish' => '<span class="label label-success">'.__( 'Published','wyzi-business-finder').'</span>',
		);
	}

	protected function display_actions( $data, $extra_btns = '' ) {
		echo '<div class="actions">';

		if ( '' != $extra_btns )
			echo $extra_btns;

		if ( isset( $data['edit'] ) && $this->edit_condition ) {
			echo '<a class="btn btn-info" title="' . esc_html__( 'Edit', 'wyzi-business-finder' ) . '" href="'.$data['edit'].'"><i class="fa fa-edit white" aria-hidden="true"></i></a>';
		}
		if ( isset($data['delete']) && $this->delete_condition ) {
			echo '<a class="btn btn-danger" title="' . esc_html__( 'Delete', 'wyzi-business-finder' ) . '" href="'.$data['delete'].'" onclick="return confirm( \'' .esc_html__( 'Are you sure you want to delete this? This step is irreversible.', 'wyzi-business-finder' ) .'\' );"><i class="fa fa-trash white" aria-hidden="true"></i></a>';
		}
		echo '</div>';
	}

	protected function display_status( $stat ) {
		echo '<div class="status">' . ( isset( $this->table_statuses[ $stat ] ) ? $this->table_statuses[ $stat ] : '' ) . '</div>';
	}

	public function table_start(){
		?>
		<div class="span12">
			<div class="box-header" data-original-title>
				<h2></span><?php echo $this->table_title;?></h2>
				<div class="box-icon">
					<a href="#" class="btn-minimize"><i class="fa fa-chevron-up"></i></a>
				</div>
			</div>
			<div class="box-content">
				<table class="table bootstrap-datatable wyz-datatable">
					<thead>
						<tr><th><?php esc_html_e( 'Title', 'wyzi-business-finder' );?></th></tr>
					</thead> 
				<tbody>
		<?php
	}

	public function add_column( $c, $return = false ) {
		if ( $return )
			return '<tr class="center"><td>'.$c.'</td></tr>';;
		echo '<tr class="center"><td>'.$c.'</td></tr>';
	}

	public function table_close() {
		?>
					</tbody>
				</table>
			</div>
		</div>
	<?php
	}

}