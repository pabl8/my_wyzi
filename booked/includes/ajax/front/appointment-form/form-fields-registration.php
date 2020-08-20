<div class="field">
	<label class="field-label"><?php esc_html_e("Registration:",'wyzi-business-finder'); ?><i class="required-asterisk booked-icon booked-icon-required"></i></label>
	<p class="field-small-p"><?php esc_html_e('Please enter your name, your email address and choose a password to get started.','wyzi-business-finder'); ?></p>
</div>

<?php
	$name_requirements = get_option('booked_registration_name_requirements',array('require_name'));
	$name_requirements = ( isset($name_requirements[0]) ? $name_requirements[0] : false );
?>

<?php if ( $name_requirements == 'require_surname' ): ?>
	<div class="field">
		<input value="" placeholder="<?php esc_html_e('First Name','wyzi-business-finder'); ?>..." type="text" class="textfield" name="booked_appt_name" />
		<input value="" placeholder="<?php esc_html_e('Last Name','wyzi-business-finder'); ?>..." type="text" class="textfield" name="booked_appt_surname" />
	</div>
<?php else: ?>
	<div class="field">
		<input value="" placeholder="<?php esc_html_e('Name','wyzi-business-finder'); ?>..." type="text" class="large textfield" name="booked_appt_name" />
	</div>
<?php endif; ?>

<div class="field">
	<input value="" placeholder="<?php esc_html_e('Email Address','wyzi-business-finder'); ?>..." type="email" class="textfield" name="booked_appt_email" />
	<input value="" placeholder="<?php esc_html_e('Choose a password','wyzi-business-finder'); ?>..." type="password" class="textfield" name="booked_appt_password" />
</div>
<?php if ( function_exists( 'wyz_get_option' ) && 'on' == wyz_get_option( 'terms-and-cond-on-off' ) ) {?>
	<div class="col-xs-12 terms-and-cond field">
		<?php wyz_extract_termsandconditions();?>
	</div>
<?php }