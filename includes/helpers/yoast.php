<?php

namespace YoastExtended;

/**
 * Return hardcoded prefix if we don't have access to the core Yoast Meta class
 *
 * @return string
 */
function get_post_type_meta_prefix() {
	return class_exists( '\WPSEO_Meta' ) ? \WPSEO_Meta::$meta_prefix : '_yoast_wpseo_';
}

/**
 * Return prefix for taxonomy meta fields
 *
 * @return string
 */
function get_taxonomy_meta_prefix() {
	return 'wpseo_'; // This is hardcoded in the yoast core
}

/**
 * Combine meta prefix and meta key
 *
 * @param  string $key
 * @return string
 */
function meta_key( string $key, string $type ) {

	$prefixes = [
		'post_type' => get_post_type_meta_prefix(),
		'taxonomy' => get_taxonomy_meta_prefix()
	];

	$prefix = $prefixes[ $type ];
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
	return \get_post_meta( $post_id, meta_key( $yoast_key, 'post_type' ), $single );
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
	return \update_post_meta( $post_id, meta_key( $yoast_key, 'post_type' ), $value, $previous );
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

/**
 * Get meta values for a specific term by term_id
 *
 * @param  string      $yoast_key   [description]
 * @param  int         $term_id     [description]
 * @return mixed
 */
function get_term_meta( string $yoast_key, int $term_id ) {
	if ( $term = get_term( $term_id ) ) {
		$meta = get_taxonomy_yoast_meta( $term->taxonomy );

		// Check meta exists for term
		if ( isset( $meta[ $term->term_id ] ) ) {
			$found = $meta[ $term->term_id ];

			// Prefix field
			$yoast_key = meta_key( $yoast_key, 'taxonomy' );

			// Return value if found
			return isset( $found[ $yoast_key ] ) ? $found[ $yoast_key ] : null;
		}
	}

	return null;
}


/**
 * Update yoast term meta value
 *
 * @param  string $yoast_key
 * @param  int    $term_id
 * @param  mixed  $value
 * @return boolean
 */
function update_term_meta( string $yoast_key, int $term_id, $value )  {
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
		$yoast_key = meta_key( $yoast_key, 'taxonomy' );

		// Add field value
		$meta[ $term->taxonomy ][ $term->term_id ][ $yoast_key ] = $value;

		// Update option
		return \update_option( 'wpseo_taxonomy_meta', $meta, true );
	}

	return false;
}



