<?php

namespace Wyzi\core;

class MetaBox
{
	private $_meta_fields = array();

	public function __construct()
	{
		$this->_meta_fields = $this->_get_meta_fields();

		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ), 1 );
		add_action( 'save_post', array( $this, 'save_fields' ) );
	}

	public function add_meta_boxes()
	{
		add_meta_box(
			'sender-information',
			esc_html__( 'Sender Information', 'wyzi-business-finder' ),
			array( &$this, 'metabox_markup' ),
			'private-message',
			'side',
			'high'
		);
	}

	public function metabox_markup( $post ) {
		wp_nonce_field( 'additionalfields_data', 'additionalfields_nonce' );

		$output_html = '';

		foreach ( $this->_meta_fields as $meta_field ) {

			$user_id = get_post_meta( $post->ID, $meta_field['id'], true );

			$label = '<label for="' . esc_attr( $meta_field['id'] ) . '">' . $meta_field['label'] . '</label>';

			$input = wp_dropdown_users( array(
					'selected' => $user_id,
					'echo'	   => false,
					'name'	   => $meta_field['id']
				) );

			$output_html .= $this->format_rows( $label, $input );
		}

		echo '<table class="form-table">
				<tbody>'
					. $output_html . '
				</tbody>
			</table>';
	}

	public function format_rows( $label, $input ) {
		return '
			<tr>
				<th>' . $label . '</th>
				<td>' . $input . '</td>
			</tr>
		';
	}

	public function save_fields( $post_id )
	{

		if ( ! isset( $_POST['additionalfields_nonce'] ) || ! wp_verify_nonce( $_POST['additionalfields_nonce'], 'additionalfields_data' ) || ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) ) {

			return $post_id;
		}

		foreach ( $this->_meta_fields as $meta_field ) {

			if ( ! isset( $_POST[ $meta_field['id'] ] ) || ! intval( $_POST[ $meta_field['id'] ] ) ) {
				return $post_id;
			}

			update_post_meta( $post_id, $meta_field['id'], $_POST[ $meta_field['id'] ] );
		}

		$additional_meta_fields = $this->additional_meta_fields();

		foreach ( $additional_meta_fields as $meta_key => $meta_value ) {
			add_post_meta( $post_id, $meta_key, $meta_value, true );
		}

		return $post_id;
	}

	private function _get_meta_fields()
	{
		return array(

			array(
				'label' 	=> esc_html__('Sender', 'wyzi-business-finder'),
				'id'		=> 'message_sender_id',
				'type'		=> 'text',
				'disabled'	=> true
			),

			array(
				'label' 	=> esc_html__('Receiver', 'wyzi-business-finder'),
				'id'		=> 'message_receiver_id',
				'type'		=> 'text',
				'disabled'	=> true
			)
		);
	}

	private function additional_meta_fields()
	{
		return array(
			'receiver_status'		=> 'inbox', // trash, permanent_deleted
			'sender_status'			=> 'sent_item', // trash, permanent_deleted
			'reported_spam'			=> false,
			'reported_harassment'	=> false,
			'read_status'			=> false
		);
	}
}

new MetaBox;