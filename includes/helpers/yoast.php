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

/**
 * Get yoast taxonomy meta
 *
 * @param  ?string $taxonomy [description]
 * @return mixed
 */
function get_taxonomy_yoast_meta( ?string $taxonomy = null ) {
	$meta = \get_option( 'wpseo_taxonomy_meta', [] );

	if ( $taxonomy ) {
		return isset( $meta[ $taxonomy ] ) ? $meta[ $taxonomy ] : null;
	}

	return $meta;
}

function update_term_yoast_meta( int $term_id, string $field, $value )  {
	$meta = get_taxonomy_yoast_meta();

	if ( $term = get_term( $term_id ) ) {

		// Create taxonomy array if not exists
		if ( !isset( $meta[ $term->taxonomy ] ) ) {
			$meta[ $term->taxonomy ] = [];
		}

		// Create term array if not exists
		if ( !isset( $meta[ $term->taxonomy ][ $term->term_id ] ) ) {
			$meta[ $term->taxonomy ][ $term->term_id ] = [];
		}

		// Prefix field
		$field = preg_replace( '/^wpseo_/', '', $field );
		$field = 'wpseo_' . $field;

		// Add field value
		$meta[ $term->taxonomy ][ $term->term_id ][ $field ] = $value;

		// Update option
		return \update_option( 'wpseo_taxonomy_meta', $meta, true );
	}

	return false;
}

/**
 * Get meta values for a specific term by term_id
 *
 * @param  int         $term_id [description]
 * @param  ?string     $field   [description]
 * @return mixed
 */
function get_term_yoast_meta( int $term_id, ?string $field = null ) {
	if ( $term = get_term( $term_id ) ) {
		$meta = get_taxonomy_yoast_meta( $term->taxonomy );

		if ( isset( $meta[ $term->term_id ] ) ) {
			$found = $meta[ $term->term_id ];

			if ( $field ) {

				$field = preg_replace( '/^wpseo_/', '', $field );
				$field = 'wpseo_' . $field;

				return isset( $found[ $field ] ) ? $found[ $field ] : null;
			}

			return $found;
		}
	}

	return null;
}




