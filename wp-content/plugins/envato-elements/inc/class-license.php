<?php
/**
 * Envato Elements:
 *
 * This starts things up. Registers the SPL and starts up some classes.
 *
 * @package Envato/Envato_Elements
 * @since 0.0.2
 */

namespace Envato_Elements;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * License registration and management.
 *
 * @since 0.0.2
 */
class License extends Base {

	const PAGE_SLUG = 'envato-elements-activation';

	const ERROR_TRANSIENT = 'envato-elements-license-message-error';

	/**
	 * License constructor.
	 */
	public function __construct() {
		add_action( 'admin_action_envato_elements_registration', [ $this, 'envato_elements_registration' ] );
		add_action( 'admin_action_envato_elements_deactivate', [ $this, 'envato_elements_deactivate' ] );

		// Add the license key to all API requests.
		add_filter( 'envato_elements_api_body_args', [ $this, 'filter_api_body_args' ] );

	}

	/**
	 * Called when the user visits our menu item without registering.
	 * Displays the welcome screen.
	 */
	public function admin_menu_open() {
		$this->content = $this->render_template( 'license/welcome.php' );
		$this->header  = $this->render_template( 'header.php' );
		echo $this->render_template( 'wrapper.php' );  // WPCS: XSS ok.
	}

	/**
	 * Gets the current license code.
	 *
	 * @return string Code
	 */
	public function get_license_code() {
		$codes = get_option( 'envato_elements_license_code' );
		// Edit: we want a code per user.
		if ( ! is_array( $codes ) ) {
			$codes = [];
		}
		if ( ! empty( $codes[ get_current_user_id() ] ) ) {
			return $codes[ get_current_user_id() ];
		}

		return false;
	}

	/**
	 * Sets current license code.
	 *
	 * @param string $license_code Code to save.
	 */
	public function set_license_code( $license_code ) {
		$codes = get_option( 'envato_elements_license_code' );
		// Edit: we want a code per user.
		if ( ! is_array( $codes ) ) {
			$codes = [];
		}
		$codes[ get_current_user_id() ] = $license_code;

		return update_option( 'envato_elements_license_code', $codes );
	}

	/**
	 * Works out if user has registered.
	 *
	 * @return bool
	 */
	public function is_activated() {
		return ! ! $this->get_license_code();
	}

	/**
	 * Filter API body arguments on every outbound request to Envato server.
	 * Allows us to add the users API key to all API requests so we can verify clients.
	 *
	 * @param array $body_args API args.
	 *
	 * @return array
	 */
	public function filter_api_body_args( $body_args ) {
		$license_code = $this->get_license_code();

		if ( ! empty( $license_code ) ) {
			$body_args['license_code'] = $license_code;
		}

		return $body_args;
	}

	/**
	 * Handles the form registration from the Welcome screen.
	 */
	public function envato_elements_registration() {
		check_admin_referer( 'envato_elements_signup' );

		$email = ! empty( $_POST['email_address'] ) ? filter_var( wp_unslash( $_POST['email_address'] ), FILTER_SANITIZE_EMAIL ) : false; // WPCS: input var ok.
		if ( empty( $_POST['condition_terms'] ) ) { // WPCS: input var ok.
			wp_safe_redirect( add_query_arg( 'registration', 'terms', Plugin::get_instance()->get_url() ) );
		} elseif ( $email ) {
			// Activate email against this install.
			$activation_result = API::get_instance()->api_call(
				'v1/activate', [
					'email'            => $email,
					'condition_terms'  => ! empty( $_POST['condition_terms'] ) ? 1 : 0, // WPCS: input var ok.
					'condition_emails' => ! empty( $_POST['condition_emails'] ) ? 1 : 0, // WPCS: input var ok.
				]
			);
			if ( $activation_result && ! is_wp_error( $activation_result ) && ! empty( $activation_result['license_code'] ) ) {
				$this->set_license_code( $activation_result['license_code'] );
				wp_safe_redirect( add_query_arg( 'registration', 'success', Plugin::get_instance()->get_url() ) );
			} else {
				$license_message_error = esc_html__( 'There was an error with the request, please try again.', 'envato-elements' );
				if ( is_wp_error( $activation_result ) ) {
					/* Translators: The HTTP error message */
					$license_message_error = sprintf( esc_html__( 'There was an error with the request: %s. If this error continues please contact the hosting provider for assistance.', 'envato-elements' ), esc_html( $activation_result->get_error_message() ) );
				}
				set_transient( self::ERROR_TRANSIENT, $license_message_error, 300 );
				wp_safe_redirect( add_query_arg( 'registration', 'error', Plugin::get_instance()->get_url() ) );
			}
		} else {
			wp_safe_redirect( add_query_arg( 'registration', 'failure', Plugin::get_instance()->get_url() ) );
		}

	}

	/**
	 * Deactivate their local license.
	 */
	public function envato_elements_deactivate() {
		// check_admin_referer( 'deactivate' ); // todo: uncomment this when we go live, it's currently good for easy testing. /wp-admin/admin.php?action=envato_elements_deactivate.
		$this->set_license_code( '' );
		wp_safe_redirect( add_query_arg( 'registration', 'reset', Plugin::get_instance()->get_url() ) );

	}

	/**
	 * When the plugin upgrades we clear any transient notices.
	 *
	 * @since 0.1.2
	 */
	public function activation() {
		delete_transient( self::ERROR_TRANSIENT );
	}

}
