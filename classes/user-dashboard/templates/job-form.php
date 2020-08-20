<?php
$step='';
$steps  = array('submit' => '','preview' => '','done' => '');
if ( isset( $_POST['step'] ) ) {
	$step = is_numeric( $_POST['step'] ) ? max( absint( $_POST['step'] ), 0 ) : array_search( $_POST['step'], array_keys( $steps ) );
} elseif ( ! empty( $_GET['step'] ) ) {
	$step = is_numeric( $_GET['step'] ) ? max( absint( $_GET['step'] ), 0 ) : array_search( $_GET['step'], array_keys( $steps ) );
}
if ( ( isset( $_GET['add-job'] ) && $this->can_add_job )|| $step == 1 ) {
	echo do_shortcode( '[submit_job_form]' );
}
elseif ( isset( $_GET['action'] ) && 'edit' == $_GET['action'] ) {
	echo do_shortcode( '[job_dashboard]' );

}
