<?php

/**
 * Add post type/taxonomy routes
 */
\add_action( 'init', function() {

	$taxonomies = get_taxonomies( [
		'public' => true
	], 'objects' );

	add_rewrite_tag( '%post_type%','([^/]+)' );

	foreach ( $taxonomies as $taxonomy ) {

		/**
		 * /{post types}/{taxononmy}/{term}/page/{number}/
		 * /{post types}/{taxononmy}/{term}/
		 */
		add_rewrite_rule( '^(' . implode( '|', $taxonomy->object_type ) . ')/' . $taxonomy->rewrite[ 'slug' ] . '/([^/]+)/page/?([0-9]{1,})/?$', 'index.php?post_type=$matches[1]&' . $taxonomy->query_var . '=$matches[2]&paged=$matches[3]', 'top' );
		add_rewrite_rule( '^(' . implode( '|', $taxonomy->object_type ) . ')/' . $taxonomy->rewrite[ 'slug' ] . '/([^/]+)/?$', 'index.php?post_type=$matches[1]&' . $taxonomy->query_var . '=$matches[2]', 'top' );
	}

}, 10 );
