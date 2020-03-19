<?php

namespace YoastExtended;

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
 * Get the human readable label of a post type by key
 *
 * @param  string $post_type
 * @param  string $attribute
 * @param  mixed  $default
 * @return mixed
 */
function get_post_type_attr( string $post_type, string $attribute, $default = null ) {
	$post_type_object = get_post_type_object( $post_type );

	return !empty( $post_type_object->$attribute ) ? $post_type_object->$attribute : $default;
}


/**
 * Get the human readable label of a taxonomy by key
 *
 * @param  string $taxonomy
 * @param  string $attribute
 * @param  mixed  $default
 * @return mixed
 */
function get_taxonomy_attr( string $taxonomy, string $attribute, $default = null ) {
	$taxonomy_object = get_taxonomy( $taxonomy );

	return !empty( $taxonomy_object->$attribute ) ? $taxonomy_object->$attribute : $default;
}



