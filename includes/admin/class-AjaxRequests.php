<?php

namespace YoastExtended\Admin;

use \WP_Error;

/**
 *
 */
class AjaxRequests {

	/**
	 * Action string used for verifying request
	 *
	 * @var string
	 */
	public $nonce_action = 'yoast-extended-csrf';

	/**
	 * Array of translatable strings
	 *
	 * @var array
	 */
	public $strings = [];

	/**
	 * Register all AJAX actions
	 */
	function __construct() {
		$this->strings = [
			'invalid_request' => __( 'Invalid request', 'yoast_extended' ),
			'unknown_error' => __( 'An unknown error occured', 'yoast_extended' ),
			'generic_success' =>  __( 'Value successfully updated', 'yoast_extended' ),
		];

		/**
		 * Register accepted ajax requests
		 */
		$this->register_action( 'bulk_edit_post_types' );
		$this->register_action( 'bulk_edit_taxonomies' );
	}

	/**
	 * Action prefixer
	 *
	 * @param	string	$name
	 * @return	void
	 */
	public function register_action( string $name ) {
		add_action( 'wp_ajax_YoastExtended-' . $name, [ $this, 'request_' . $name ] );
	}

	/**
	 * Get sanitized value from request
	 *
	 * @param	string	$name		Name of field in request
	 * @param	string	$filter		Name of function to clean field
	 * @return	mixed
	 */
	public function input( string $name, ?string $filter = 'sanitize_text_field' ) {
		$value = !empty( $_REQUEST[ $name ] ) ? $_REQUEST[ $name ] : null;

		if ( $filter ) {
			$value = call_user_func_array( $filter, [ $value ] );
		}

		return $value;
	}

	/**
	 * Check if request is valid
	 *
	 * @return boolean
	 */
	public function is_valid_nonce() {
		return wp_verify_nonce( $this->input( 'csrf' ), $this->nonce_action );
	}

	/**
	 * Update a post type meta values
	 */
	public function request_bulk_edit_post_types() {
		$post_id = $this->input( 'post_id', 'intval' );
		$value = $this->input( 'value' );
		$field = $this->input( 'field' );

		$valid = $this->is_valid_nonce();

		if ( !$valid || !$post_id || !in_array( $field, [ 'title', 'metadesc' ] ) ) {
			return wp_send_json_error( $this->strings[ 'invalid_request' ] );
		}

		$success = \YoastExtended\update_post_meta( $field, $post_id, $value );

		if ( $success ) {
			return wp_send_json_success( [
				'message' => $this->strings[ 'generic_success' ],
				'replacement' => sprintf( '<small><strong>%s</strong> %s</small>', __( 'New value:', 'yoast_extended' ), esc_html( wp_unslash( $value ) ) )
			] );
		}


		return wp_send_json_error( $this->strings[ 'unknown_error' ] );
	}

	/**
	 * Update a post type meta values
	 */
	public function request_bulk_edit_taxonomies() {
		$term_id = $this->input( 'term_id', 'intval' );
		$value = $this->input( 'value' );
		$field = $this->input( 'field' );

		$valid = $this->is_valid_nonce();

		if ( !$valid || !$term_id || !in_array( $field, [ 'title', 'desc' ] ) ) {
			return wp_send_json_error( $this->strings[ 'invalid_request' ] );
		}

		$success = \YoastExtended\update_term_yoast_meta( $term_id, $field, $value );

		if ( $success ) {
			return wp_send_json_success( [
				'message' => $this->strings[ 'generic_success' ],
				'replacement' => sprintf( '<small><strong>%s</strong> %s</small>', __( 'New value:', 'yoast_extended' ), esc_html( wp_unslash( $value ) ) )
			] );
		}


		return wp_send_json_error( $this->strings[ 'unknown_error' ] );
	}


}

yoast_extended()->ajax = new \YoastExtended\Admin\AjaxRequests;
