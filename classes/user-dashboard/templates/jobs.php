<?php require_once( plugin_dir_path( __FILE__ ) . 'table-jobs.php' );?>
<div class="row-fluid">		
	<div class="span12">
		<div class="row-fluid">	
			<?php $table = new WyzDashboardJobsTable( $this->user_id );
			$table->the_table();?>
		</div>
	</div>
</div>