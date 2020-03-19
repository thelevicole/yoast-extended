<?php

namespace YoastExtended\Admin\Tables;

use \WP_Error, \WP_List_Table, \WP_Term_Query, \WP_Term;
use \WPSEO_Meta;

class BulkEdit_Taxonomies extends WP_List_Table {

	/**
	 * Return a list of user allowed mutable post types
	 *
	 * @return array
	 */
	public function get_mutable_taxonomies() {
		/**
		 * Get all registered post types
		 *
		 * @var array
		 */
		$taxonomies = get_taxonomies();

		/**
		 * Get user Yoast SEO settings
		 *
		 * @var array
		 */
		$yoast_settings = get_option( 'wpseo_titles', [] );

		/**
		 * Empty array of post types we allow the user to modify
		 *
		 * @var array
		 */
		$mutable_types = [];

		/**
		 * Build array of mutable post types
		 */
		foreach ( $taxonomies as $taxonomy ) {
			$display_metabox = 'display-metabox-tax-' . $taxonomy;
			$disable_type = 'disable-' . $taxonomy;

			$has_metabox = !empty( $yoast_settings[ $display_metabox ] );
			$is_disabled = !empty( $yoast_settings[ $disable_type ] );

			if ( $has_metabox && !$is_disabled ) {
				$mutable_types[] = $taxonomy;
			}
		}

		return $mutable_types;
	}

	public function get_views() {
		$taxonomies = $this->get_mutable_taxonomies();

		$is_filtered = false;

		$view_links = [
			'all' => '<a href="' . remove_query_arg( 'type' ) . '">' . __( 'All', 'yoast_extended' ) . '</a>'
		];

		foreach ( $taxonomies as $taxonomy ) {

			$current = null;

			if ( !empty( $_GET[ 'type' ] ) && $_GET[ 'type' ] === $taxonomy ) {
				$current = ' class="current"';
				$is_filtered = true;
			}

			$view_links[ $taxonomy ] = '<a href="' . add_query_arg( 'type', $taxonomy ) . '"' . $current . '>' . ( \YoastExtended\get_taxonomy_attr( $taxonomy, 'label', $taxonomy ) ) . '</a>';
		}

		if ( !$is_filtered ) {
			$view_links[ 'all' ] = str_replace( '<a href="' , '<a class="current" href="', $view_links[ 'all' ] );
		}

		return $view_links;
	}

	/**
	 * Associative array of columns
	 *
	 * @return array
	 */
	public function get_columns() {
		return [
			'term_id'			=> __( 'Term ID', 'yoast_extended' ),
			'name'				=> __( 'Term Name', 'yoast_extended' ),
			'yoast_title'		=> __( 'Yoast SEO title', 'yoast_extended' ),
			'yoast_description'	=> __( 'Yoast SEO description', 'yoast_extended' )
		];
	}

	/**
	 * Columns to make sortable
	 *
	 * @return array
	 */
	public function get_sortable_columns() {
		return [
			'term_id'	=> [ 'term_id', true ],
			'name'		=> [ 'name', true ],
		];
	}

	/**
	 * Prepare the table with different parameters, pagination, columns and table elements
	 */
	public function prepare_items() {

		$this->_column_headers = [ $this->get_columns(), [], $this->get_sortable_columns() ];

		$per_page = (int)get_option( 'posts_per_page' );

		$taxonomies = $this->get_mutable_taxonomies();

		$search = !empty( $_REQUEST[ 's' ] ) ? \sanitize_text_field( $_REQUEST[ 's' ] ) : '';
		$paged = !empty( $_REQUEST[ 'paged' ] ) ? (int)$_REQUEST[ 'paged' ] : 1;

		$orderby = !empty( $_REQUEST[ 'orderby' ] ) ? \sanitize_text_field( $_REQUEST[ 'orderby' ] ) : 'name';
		$order = !empty( $_REQUEST[ 'order' ] ) ? \sanitize_text_field( $_REQUEST[ 'order' ] ) : 'ASC';

		$filter_type = !empty( $_GET[ 'type' ] ) ? \sanitize_text_field( $_GET[ 'type' ] ) : null;

		$terms = ( new WP_Term_Query )->query( [
			'taxonomy' => $filter_type && in_array( $filter_type, $taxonomies ) ? $filter_type : $taxonomies,
			'search' => $search,
			'offset' => floor( ( $paged - 1 ) * $per_page ),
			'orderby' => $orderby,
			'order' => $order,
			'hide_empty' => false,
			'number' => $per_page
		] );

		$count = ( new WP_Term_Query )->query( [
			'fields' => 'count',
			'taxonomy' => $filter_type && in_array( $filter_type, $taxonomies ) ? $filter_type : $taxonomies,
			'search' => $search,
			'hide_empty' => false
		] );

		// Update the pagination links
		$this->set_pagination_args( [
			'total_items'	=> $count,
			'total_pages'	=> ceil( $count / $per_page ),
			'per_page'		=> $per_page
		] );

		// Get posts from query
		$this->items = $terms;

	}

	/**
	 * Default action for rendering a column value
	 *
	 * @param	WP_Post	$item
	 * @param	string	$column_name
	 * @return	void
	 */
	public function column_default( $item, $column_name ) {
		if ( !empty( $item->$column_name ) ) {
			echo $item->$column_name;
		}
	}

	/**
	 * Print the basic post information
	 *
	 * @param  WP_Post $item
	 * @return void
	 */
	public function column_name( $item ) {
		$permalink = get_term_link( $item->term_id );
		$taxonomy = \YoastExtended\get_taxonomy_attr( $item->taxonomy, 'label', $item->taxonomy );
		printf( '<div><strong><a href="%s">%s</a> &mdash; %s</strong></div>', $permalink, $item->name, $taxonomy );
		printf( '<div><small>%1$s</small></div>', wp_make_link_relative( $permalink ) );
	}

	/**
	 * Print the title field
	 *
	 * @param  WP_Post $item
	 * @return void
	 */
	public function column_yoast_title( $item ) {

		/**
		 * Get current title value from database
		 *
		 * @var ?string
		 */
		$current_value = \YoastExtended\get_term_meta( 'title', $item->term_id );

		printf( '<input type="text" name="seo_title" class="yoast_extended-title" data-id="%d" value="" placeholder="%s" style="width: 100%%;">', $item->term_id, esc_attr( __( 'Enter a new SEO title', 'yoast_extended' ) ) );

		echo '<div class="yoast_extended-current">';
			if ( $current_value ) {
				printf( '<small><strong>%s</strong> %s</small>', __( 'Current value:', 'yoast_extended' ), esc_html( wp_unslash( $current_value ) ) );
			}
		echo '</div>';
	}

	/**
	 * Print the description field
	 *
	 * @param  WP_Post $item
	 * @return void
	 */
	public function column_yoast_description( $item ) {

		/**
		 * Get current title value from database
		 *
		 * @var ?string
		 */
		$current_value = \YoastExtended\get_term_meta( 'desc', $item->term_id );

		printf( '<textarea name="seo_description" class="yoast_extended-description" data-id="%d" placeholder="%s" style="width: 100%%;"></textarea>', $item->term_id, esc_attr( __( 'Enter a new SEO description', 'yoast_extended' ) ) );

		echo '<div class="yoast_extended-current">';
			if ( $current_value ) {
				printf( '<small><strong>%s</strong> %s</small>', __( 'Current value:', 'yoast_extended' ), esc_html( wp_unslash( $current_value ) ) );
			}
		echo '</div>';
	}


}






