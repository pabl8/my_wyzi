<?php require_once( plugin_dir_path( __FILE__ ) . 'table-businesses.php' );?>
<div class="row-fluid">		
	<div class="span12">
		<div class="row-fluid">	
			<?php $table = new WyzDashboardBusinessesTable( $this->user_id );
			$table->the_table();?>
		</div>
	</div>
</div>