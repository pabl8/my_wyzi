<?php
class AccountJob extends AccountContent {

	public function the_condition() {
		$this->condition =  $this->is_business_owner && 'on' == get_option( 'wyz_users_can_job' ) && WyzHelpers::wyz_sub_can_bus_owner_do($this->user_id,'wyzi_sub_can_create_job') && class_exists( 'WP_Job_Manager' ) && ( 'on' != get_option( 'wyz_job_requires_business' ) || WyzHelpers::wyz_has_business( $this->user_id, 'published' ) );
	}

	public function _active () {
		return false;
	}

	public function tab_title () {
		$this->tab_title = esc_html__( 'Jobs', 'wyzi-business-finder' );
	}

	public function link () {
		$this->link = 'jobs';
	}

	public function icon () {
		$this->icon = 'suitcase';
	}

	public function notifications() { }

	public function content() { 
		$step='';
		$steps  = array('submit' => '','preview' => '','done' => '');
		if ( isset( $_POST['step'] ) ) {
			$step = is_numeric( $_POST['step'] ) ? max( absint( $_POST['step'] ), 0 ) : array_search( $_POST['step'], array_keys( $steps ) );
		} elseif ( ! empty( $_GET['step'] ) ) {
			$step = is_numeric( $_GET['step'] ) ? max( absint( $_GET['step'] ), 0 ) : array_search( $_GET['step'], array_keys( $steps ) );
		}
		$can = WyzHelpers::user_can_create_job( $this->user_id );
		if ( ( isset( $_GET['add-job'] ) && $can )|| ( isset( $_GET['action'] ) && 'edit' == $_GET['action'] ) || $step == 1 ) {
			echo '<a href="' . WyzHelpers::add_clear_query_arg(array(),$this->link) .  '" class="back-to-job">' . esc_html__( '< back to Job Listings', 'wyzi-business-finder' ) . '</a><br>';
			if(!( isset( $_GET['action'] ) && 'edit' == $_GET['action'] ))
			echo do_shortcode( '[submit_job_form]' );
		}
		if ( ! isset( $_GET['add-job'] ) || $step == 1) {
			echo do_shortcode( '[job_dashboard]' );

			if ( $can && ( ! isset( $_GET['action'] ) || 'edit' != $_GET['action'] ) )
				echo '<a href="' . WyzHelpers::add_clear_query_arg( array( 'add-job' => true ) ) . '" class="action-btn btn-bg-blue btn-rounded wyz-button wyz-primary-color wyz-prim-color">' . esc_html__( 'Add New Job', 'wyzi-business-finder' ) . '</a>';
			elseif( ! WyzHelpers::current_user_affords_job_registry( $this->user_id ) )
				WyzHelpers::wyz_info( esc_html__( 'You don\'t have enough points to publish a new job.', 'wyzi-business-finder' ) );
		}
	}
}
?>