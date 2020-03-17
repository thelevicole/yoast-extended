<?php

namespace YoastExtended;

/**
 * Print human-readable preformatted information about a variable
 *
 * @param	mixed		$var	The expression to be printed.
 * @param	callable	$func
 * @return	void
 */
function print_a( $var, $func = 'print_r' ) {
	echo '<pre>';
		call_user_func_array( $func, [ $var ] );
	echo '</pre>';
}

/**
 * Alias of `print_a()` but dies after execution
 *
 * @param	mixed		$var	The expression to be printed.
 * @param	callable	$func
 * @return	void
 */
function dd( $var, $func = 'print_r' ) {
	print_a( $var, $func );
	die;
}

/**
 * Run action with YoastExtended/ prefixed
 *
 * @link https://developer.wordpress.org/reference/functions/do_action/
 *
 * @return void
 */
function do_action() {
	$args = func_get_args();
	$tag = array_shift( $args );

	call_user_func_array( '\do_action', array_merge( [ 'YoastExtended/' . $tag ], $args ) );
}

/**
 * Add action with YoastExtended/ prefixed
 *
 * @link	https://developer.wordpress.org/reference/functions/add_action/
 *
 * @param	string		$tag
 * @param	callable	$function_to_add
 * @param	integer		$priority
 * @param	integer		$accepted_args
 * @return	void
 */
function add_action( string $tag, callable $function_to_add, int $priority = 10, int $accepted_args = 1 ) {
	\add_action( 'YoastExtended/' . $tag, $function_to_add, $priority, $accepted_args );
}

/**
 * Add filter with YoastExtended/ prefixed
 *
 * @link https://developer.wordpress.org/reference/functions/apply_filters/
 *
 * @return mixed
 */
function apply_filters() {
	$args = func_get_args();
	$tag = array_shift( $args );
	$value = array_shift( $args );

	$value = call_user_func_array( '\apply_filters', array_merge( [ 'YoastExtended/' . $tag, $value ], $args ) );

	return $value;
}

/**
 * Add filter with YoastExtended/ prefixed
 *
 * @link	https://developer.wordpress.org/reference/functions/add_filter/
 *
 * @param	string		$tag
 * @param	callable	$function_to_add
 * @param	integer		$priority
 * @param	integer		$accepted_args
 * @return	void
 */
function add_filter( string $tag, callable $function_to_add, int $priority = 10, int $accepted_args = 1 ) {
	\add_filter( 'YoastExtended/' . $tag, $function_to_add, $priority, $accepted_args );
}


/**
 * Inline array of classes into a single line ready for printing
 *
 * @param	array|string	$classes
 * @return	string
 */
function class_inliner( $classes, $prefix = '', $echo = false ) {
	if ( !is_array( $classes ) ) {
		$classes = preg_split( '/\s+/', $classes );
	}

	// For each item in `$classes` array
	$classes = array_map( function( $item ) use ( $prefix ) {

		// Check if item has multiple classes in one
		$items = preg_split( '/\s+/' , $item );

		// Sanitize prefixed values for DOM
		$items = array_map( function( $value ) use ( $prefix ) {
			// @link https://developer.wordpress.org/reference/functions/sanitize_html_class/
			return \sanitize_html_class( $prefix . $value );
		}, $items );

		// Return inlined string
		return implode( $items, ' ' );

	}, $classes );

	// Implode values into a single string
	$classes = implode( $classes, ' ' );

	// Remove double white space
	$classes = preg_replace( '/\s{2,}/', ' ', $classes );

	// Trim any extra white space
	$classes = trim( $classes );

	if ( $echo ) {
		echo $classes;
	} else {
		return $classes;
	}
}
