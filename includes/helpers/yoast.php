<?php

namespace YoastExtended;

/**
 * Return hardcoded prefix if we don't have access to the core Yoast Meta class
 *
 * @return string
 */
function get_meta_prefix() {
	return class_exists( '\WPSEO_Meta' ) ? \WPSEO_Meta::$meta_prefix : '_yoast_wpseo_';
}

/**
 * Combine meta prefix and meta key
 *
 * @param  string $key
 * @return string
 */
function combine_meta_key( string $key ) {
	$prefix = get_meta_prefix();
	$key = preg_replace( '/^' . preg_quote( $prefix, '/' ) . '/', '', $key );
	return $prefix . $key;
}

/**
 * Get a yoast meta value from database
 *
 * @param  string       $yoast_key Key without prefix
 * @param  int          $post_id
 * @param  boolean      $single
 * @return mixed
 */
function get_post_meta( string $yoast_key, int $post_id, bool $single = true ) {
	return \get_post_meta( $post_id, combine_meta_key( $yoast_key ), $single );
}

/**
 * Update yoast meta value
 *
 * @param  string $yoast_key
 * @param  int    $post_id
 * @param  mixed  $value
 * @param  mixed  $previous
 * @return int|boolean
 */
function update_post_meta( string $yoast_key, int $post_id, $value, $previous = '' ) {
	return \update_post_meta( $post_id, combine_meta_key( $yoast_key ), $value, $previous );
}

/**
 * Get a yoast meta value from database for taxonomy term
 *
 * @param  string       $yoast_key Key without prefix
 * @param  int          $term_id
 * @param  boolean      $single
 * @return mixed
 */
function get_term_meta( string $yoast_key, int $term_id, bool $single = true ) {
	return \get_term_meta( $term_id, combine_meta_key( $yoast_key ), $single );
}

/**
 * Update yoast meta value for taxonomy term
 *
 * @param  string $yoast_key
 * @param  int    $term_id
 * @param  mixed  $value
 * @param  mixed  $previous
 * @return int|boolean
 */
function update_term_meta( string $yoast_key, int $term_id, $value, $previous = '' ) {
	return \update_term_meta( $term_id, combine_meta_key( $yoast_key ), $value, $previous );
}




