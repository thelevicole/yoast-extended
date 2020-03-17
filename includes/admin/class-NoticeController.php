<?php

namespace YoastExtended\Admin;

use \WP_Error;
use \YoastExtended\Admin\Notice as Notice;

/**
 * Notice collection controller
 */
class NoticeController {

	/**
	 * Store notices to display to the user
	 *
	 * @var array
	 */
	var $notices = [];

	/**
	 * Class constructor
	 *
	 * @return  void
	 */
	function __construct() {
		add_action( 'admin_notices', [ $this, 'admin_notices' ] );
	}

	/**
	 * Render any admin notices
	 *
	 * @link	https://codex.wordpress.org/Plugin_API/Action_Reference/admin_notices
	 * @return	void
	 */
	public function admin_notices() {

		if ( $notices = $this->get_notices() ) {
			foreach ( $notices as $notice ) {
				$notice->render();
			}
		}

	}

	/**
	 * Add notice data
	 *
	 * @param	string	$text
	 * @param	array	$args
	 * @return	void
	 */
	public function add_notice( string $text, array $args = [] ) {

		$notice = new Notice( $text, $args );
		$this->notices[] = $notice;

		return $notice;
	}

	/**
	 * Add success notice
	 *
	 * @param	string	$text
	 * @param	array	$args
	 * @return	Notice
	 */
	public function add_success( string $text, array $args = [] ) {
		$args[ 'type' ] = 'success';
		return $this->add_notice( $text, $args );
	}

	/**
	 * Add warning notice
	 *
	 * @param	string	$text
	 * @param	array	$args
	 * @return	Notice
	 */
	public function add_warning( string $text, array $args = [] ) {
		$args[ 'type' ] = 'warning';
		return $this->add_notice( $text, $args );
	}

	/**
	 * Add error notice
	 *
	 * @param	string	$text
	 * @param	array	$args
	 * @return	Notice
	 */
	public function add_error( string $text, array $args = [] ) {
		$args[ 'type' ] = 'error';
		return $this->add_notice( $text, $args );
	}

	/**
	 * Return an array of admin notices
	 *
	 * @return	array|boolean
	 */
	function get_notices() {
		return !empty( $this->notices ) ? $this->notices : false;
	}

}

yoast_extended()->notices = new \YoastExtended\Admin\NoticeController;






