<?php

namespace YoastExtended\Admin;

use \WP_Error;

/**
 * A single notice
 */
class Notice {

	/**
	 * Determin if the notice is dismissible
	 *
	 * @var boolean
	 */
	public $dismissible = false;

	/**
	 * WordPress notice style class (success | warning | error)
	 *
	 * @var string
	 */
	public $type = 'success';

	/**
	 * The opening and closing tags to wrap the message
	 *
	 * @var ?string
	 */
	public $wrap = 'p';

	/**
	 * Array of classes render on the notice container
	 *
	 * @var array
	 */
	public $classes = [];

	/**
	 * Message printed in the notice
	 *
	 * @var string
	 */
	public $message = '';

	function __construct( string $message, array $args = [] ) {
		$args[ 'message' ] = $message;
		$this->setter( $args );
	}

	/**
	 * Bulk setter
	 *
	 * @param	array 	$args
	 * @return	void
	 */
	public function setter( array $args )  {
		if ( !empty( $args ) ) {
			foreach ( $args as $key => $value ) {
				if ( method_exists( $this, $key ) ) {
					call_user_func( [ $this, $key ], $value );
				}
			}
		}
	}

	/**
	 * Set messsage helper
	 *
	 * @param	string	$message
	 * @return	void
	 */
	public function message( string $message ) {
		$this->message = $message;
	}

	/**
	 * Set dismissible option helper
	 *
	 * @param	bool	$toggle
	 * @return	void
	 */
	public function dismissible( bool $toggle = true ) {
		$this->dismissible = $toggle;
	}

	/**
	 * Set the notice type helper
	 *
	 * @param	string	$type
	 * @return	void
	 */
	public function type( string $type = 'success' ) {
		$this->type = $type;
	}

	/**
	 * Set the notice wrapper tag helper
	 *
	 * @param	string	$wrap
	 * @return	void
	 */
	public function wrap( string $wrap = 'p' ) {
		$this->wrap = $wrap;
	}

	/**
	 * Override the classes array helper
	 *
	 * @param	array	$classes
	 * @return	void
	 */
	public function classes( array $classes = [] ) {
		$this->classes = $classes;
	}

	/**
	 * Add a single class to the classes array helper
	 *
	 * @param	string	$class
	 * @return	void
	 */
	public function add_class( string $class ) {
		if ( !in_array( $class, $this->classes ) ) {
			$this->classes[] = $class;
		}
	}

	/**
	 * Render the notice HTML
	 *
	 * @param	bool	$echo	False, to return the html
	 * @return	?string
	 */
	public function render( bool $echo = true ) {

		$classes = $this->classes;

		if ( $this->dismissible ) {
			$classes[] = 'is-dismissible';
		}

		if ( !empty( $this->type ) ) {
			$classes[] = 'notice-' . $this->type;
		}

		$open	= '';
		$close	= '';

		if ( $this->wrap ) {
			$open	= "<{$this->wrap}>";
			$close	= "</{$this->wrap}>";
		}

		$html = sprintf( '<div class="yoast-extended-admin-notice notice %s">%s%s%s</div>', \YoastExtended\class_inliner( $classes ), $open, $this->message, $close );

		if ( $echo ) {
			echo $html;
		} else {
			return $html;
		}
	}

}
