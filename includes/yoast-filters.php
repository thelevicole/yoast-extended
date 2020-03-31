<?php

/**
 * Add custom taxonomy term meta fields otherwise yoast invalidates storage
 */
\add_filter( 'wpseo_add_extra_taxmeta_term_defaults', function( $term_defaults ) {
	if ( $post_types = get_post_types() ) {
		foreach ( $post_types as $post_type ) {
			$title_key = \YoastExtended\meta_key( "pt-{$post_type}-title", 'taxonomy' );
			$desc_key = \YoastExtended\meta_key( "pt-{$post_type}-desc", 'taxonomy' );

			$term_defaults[ $title_key ] = null;
			$term_defaults[ $desc_key ] = null;
		}
	}
	return $term_defaults;
} );

/**
 * Filter the Yoast title
 */
\add_filter( 'wpseo_title', function( $title ) {

	if ( is_tax() ) {
		$post_type = get_query_var( 'post_type' );
		$term = get_queried_object();

		if ( !empty( $term->term_id ) && $post_type ) {

			$ye_title = \YoastExtended\get_term_meta( "pt-{$post_type}-title", $term->term_id );

			if ( $ye_title ) {
				$title = \YoastExtended\apply_filters( 'wpseo_title', $ye_title );
			}
		}
	}

	return $title;
} );


/**
 * Filter the Yoast title
 */
\add_filter( 'wpseo_metadesc', function( $title ) {

	if ( is_tax() ) {
		$post_type = get_query_var( 'post_type' );
		$term = get_queried_object();

		if ( !empty( $term->term_id ) && $post_type ) {

			$ye_title = \YoastExtended\get_term_meta( "pt-{$post_type}-desc", $term->term_id );

			if ( $ye_title ) {
				$title = \YoastExtended\apply_filters( 'wpseo_metadesc', $ye_title );
			}
		}
	}

	return $title;
} );

