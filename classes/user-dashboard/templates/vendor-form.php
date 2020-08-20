<div class="row-fluid">		
	<div class="span12">
		<?php
		$echoed = false;
		$peding_form_id = get_user_meta($this->user_id, 'wcmp_vendor_registration_form_id', true);
        if ( ! empty($peding_form_id) && $peding_form_id) {
        	$form_ower = get_post_meta($peding_form_id, 'user_id', true);
        	if ( $form_ower == $this->user_id && 'publish' == get_post_status($peding_form_id) ) {
        		$user = $user = wp_get_current_user();
				if ( in_array( 'dc_rejected_vendor', (array) $user->roles ) ) {
					echo WyzHelpers::wyz_error( esc_html__( 'Your vendor application was rejected.', 'wyzi-business-finder' ) );	
				} else
	        		echo WyzHelpers::wyz_info( esc_html__( 'You already have a pending vendor request.', 'wyzi-business-finder' ) );
        		$echoed = true;
        	}
        }
		if ( ! $echoed )
			echo do_shortcode( '[vendor_registration]' );
		?>
	</div>
</div>