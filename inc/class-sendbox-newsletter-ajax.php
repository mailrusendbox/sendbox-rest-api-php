<?php

/**
 * Handle ajax actions.
 *
 * Class Sendbox_Newsletter_Ajax
 */
class Sendbox_Newsletter_Ajax {


	/**
	 * Register ajax actions for logged and un-logged user.
	 *
	 * Sendbox_Newsletter_Ajax constructor.
	 */
	public function __construct() {

		add_action( 'wp_ajax_sendbox_import', array( $this, 'import' ) );

	}

	/**
	 * Handle import ajax action.
	 */
	public function import() {

		check_ajax_referer( 'sendbox_import' );

		$book = isset( $_POST['book'] ) ? sanitize_text_field( $_POST['book'] ) : '';
		$role = isset( $_POST['role'] ) ? sanitize_text_field( $_POST['role'] ) : '';

		$msg = array(); // log emulation

		if ( empty( $book ) ) {
			$msg[] = ( __( 'Please, select Address Book', 'sendbox-email-marketing-newsletter' ) );
		}

		if ( empty( $role ) ) {
			$msg[] = ( __( 'Please, select Users Role', 'sendbox-email-marketing-newsletter' ) );
		}

		if ( empty( $msg ) ) {

			$msg[] = current_time( 'mysql' ) . ' ' . __( 'Import start', 'sendbox-email-marketing-newsletter' );


			$api = new Sendbox_Newsletter_API();

			$emails = array();

			$users = get_users( array(
					'role' => $role
				)
			);


			foreach ( $users as $user ) {
				$email = array(
					'email'     => $user->user_email,
					'variables' => array(
						'name' => $user->display_name
					)
				);

				$user_ip = Sendbox_Newsletter_Users::get_user_ip( $user->ID );

				if ( $user_ip ) {
					$email['variables']['subscribe_ip'] = $user_ip;
				}

				$emails[] = $email;

				$msg[] = sprintf( '%s: %s %s', __( 'Add user', 'sendbox-email-marketing-newsletter' ), $user->user_email, $user->display_name );
			}

			$result = $api->addEmails( $book, $emails );

			if ( isset( $result->is_error ) && $result->is_error ) {
				$msg[] = isset( $result->message ) ? $result->message : __( 'Something went wrong. Import unsuccessful', 'sendbox-email-marketing-newsletter' );
			}


			$msg[] = current_time( 'mysql' ) . ' ' . __( 'Import finished', 'sendbox-email-marketing-newsletter' );

		}

		wp_send_json_success( array( 'msg' => implode( "\n", $msg ) ) );


	}

}

new Sendbox_Newsletter_Ajax();