<?php

class WyzRegistrationForm_Frontend_Factory {

	private static $template_type;

	public static function registration_form( $echo = true, $errors = '' ) {
		
		require_once( plugin_dir_path( __FILE__ ) . 'class-registration-form-frontend-parent.php' );
		self::$template_type = function_exists( 'wyz_get_theme_template' ) ? wyz_get_theme_template() : 1;
		require_once( plugin_dir_path( __FILE__ ) . 'class-registration-form-frontend-' . self::$template_type . '.php' );
		if ( ! $echo )ob_start();
		wyz_show_error_messages();
		$form = new WyzRegistrationForm_Frontend();
		$form->the_form();
		if ( ! $echo )return ob_get_clean();
	}

	public static function profile_form( $echo = true ) {
		require_once( plugin_dir_path( __FILE__ ) . 'class-registration-form-frontend-parent.php' );
		self::$template_type = function_exists( 'wyz_get_theme_template' ) ? wyz_get_theme_template() : 1;
		require_once( plugin_dir_path( __FILE__ ) . 'class-registration-form-frontend-' . self::$template_type . '.php' );
		if ( ! $echo )ob_start();
		$form = new WyzRegistrationForm_Frontend();
		$form->the_profile_form();
		if ( ! $echo )return ob_get_clean();
	}


}