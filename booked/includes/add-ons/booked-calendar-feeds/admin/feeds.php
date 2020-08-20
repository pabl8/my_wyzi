<div class="booked-settings-wrap wrap">
	
	<div class="booked-settings-title"><?php _e('Appointment Calendar Feeds','booked-ical'); ?></div>
	
	<div id="booked-admin-panel-container">
	
		<div style="padding:20px 30px; line-height:1.5; background:#fff;">

			<?php $secure_hash = md5( home_url() . '' ); ?>
			
			<p style="font-size:16px;"><strong><?php _e('Instructions','booked-ical'); ?></strong></p>
			<p style="width:50%; margin:0 0 25px; padding:0;"><?php _e('Use the following URLs to either download a static feed (not auto-updating) or paste the URL into your favorite calendar app (Google Calendar, Apple Calendar, etc.) as a subscription to load a read-only auto-updating appointment feed.','booked-ical'); ?></p>
			
			<p style="font-size:14px; margin:0; padding:0 0 5px;"><strong><?php _e('All Appointments','booked-ical'); ?></strong></p>
			<p style="margin:0; padding:0 0 20px;"><input type="text" style="width:50%;" value="<?php echo get_site_url(); ?>/?booked_ical&sh=<?php echo esc_attr( BOOKEDICAL_SECURE_HASH ); ?>"></p>
			
			<?php $calendars = get_terms('booked_custom_calendars','orderby=slug&hide_empty=0');
				
			if (!empty($calendars)):
				
				foreach($calendars as $calendar):
					
					?><p style="font-size:14px; margin:0; padding:0 0 5px;"><strong><?php echo $calendar->name; ?></strong></p>
					<p style="margin:0; padding:0 0 20px;"><input type="text" style="width:50%;" value="<?php echo get_site_url(); ?>/?booked_ical&calendar=<?php echo $calendar->term_id; ?>&sh=<?php echo esc_attr( BOOKEDICAL_SECURE_HASH ); ?>"></p><?php
				
				endforeach;
							
			endif;
			
			?>
			
		</div>
		
	</div>
</div>