<div class="row-fluid">		
	<div class="span12">
		<?php 
 		if ( current_user_can( 'manage_options' ) ) :
			WyzHelpers::wyz_info( sprintf( esc_html__( 'Admins can manage calendars from the %sbackend%s.', 'wyzi-business-finder' ), '<a href="' . admin_url('admin.php?page=booked-settings') . '">', '</a>' ) );
		else :
			$this->check_for_businesses_no_calendars();
			$bk = new booked_plugin();
			$bk->plugin_settings_page();
		endif;?>
	</div>
</div>