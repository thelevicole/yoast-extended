<?php

namespace YoastExtended\Admin\Tables;

use \WP_Error, \WP_List_Table, \WP_Query, \WP_Post;
use \WPSEO_Meta;

class BulkEdit_PostTypes extends WP_List_Table {

	/**
	 * Return a list of user allowed mutable post types
	 *
	 * @return array
	 */
	public function get_mutable_types() {
		/**
		 * Get all registered post types
		 *
		 * @var array
		 */
		$post_types = get_post_types();

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
		foreach ( $post_types as $post_type ) {
			$display_metabox = 'display-metabox-pt-' . $post_type;
			$disable_type = 'disable-' . $post_type;

			$has_metabox = !empty( $yoast_settings[ $display_metabox ] );
			$is_disabled = !empty( $yoast_settings[ $disable_type ] );

			if ( $has_metabox && !$is_disabled ) {
				$mutable_types[] = $post_type;
			}
		}

		return $mutable_types;
	}

	public function get_views() {
		$post_types = $this->get_mutable_types();

		$is_filtered = false;

		$view_links = [
			'all' => '<a href="' . remove_query_arg( 'type' ) . '">' . __( 'All', 'yoast_extended' ) . '</a>'
		];

		foreach ( $post_types as $post_type ) {
			$type_object = get_post_type_object( $post_type );
			$current = null;

			if ( !empty( $_GET[ 'type' ] ) && $_GET[ 'type' ] === $post_type ) {
				$current = ' class="current"';
				$is_filtered = true;
			}

			$view_links[ $post_type ] = '<a href="' . add_query_arg( 'type', $post_type ) . '"' . $current . '>' . ( !empty( $type_object->label ) ? $type_object->label : $post_type ) . '</a>';
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
			'ID'                => __( 'Post ID', 'yoast_extended' ),
			'post_title'		=> __( 'Post Title', 'yoast_extended' ),
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
			'ID' => [ 'ID', true ],
			'post_title' => [ 'post_title', true ],
		];
	}

	/**
	 * Prepare the table with different parameters, pagination, columns and table elements
	 */
	public function prepare_items() {

		$this->_column_headers = [ $this->get_columns(), [], $this->get_sortable_columns() ];

		$post_types = $this->get_mutable_types();

		$search = !empty( $_REQUEST[ 's' ] ) ? \sanitize_text_field( $_REQUEST[ 's' ] ) : null;
		$paged = !empty( $_REQUEST[ 'paged' ] ) ? (int)$_REQUEST[ 'paged' ] : 1;

		$orderby = !empty( $_REQUEST[ 'orderby' ] ) ? \sanitize_text_field( $_REQUEST[ 'orderby' ] ) : null;
		$order = !empty( $_REQUEST[ 'order' ] ) ? \sanitize_text_field( $_REQUEST[ 'order' ] ) : null;

		$filter_type = !empty( $_GET[ 'type' ] ) ? \sanitize_text_field( $_GET[ 'type' ] ) : null;

		$_query = new WP_Query( [
			'post_type' => $filter_type && in_array( $filter_type, $post_types ) ? $filter_type : $post_types,
			's' => $search,
			'paged' => $paged,
			'orderby' => $orderby,
			'order' => $order
		] );

		// Update the pagination links
		$this->set_pagination_args( [
			'total_items'	=> $_query->found_posts,
			'total_pages'	=> $_query->max_num_pages,
			'per_page'		=> $_query->get( 'posts_per_page' )
		] );

		// Get posts from query
		$this->items = $_query->posts;

		\wp_reset_postdata();

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
	public function column_post_title( $item ) {
		printf( '<div><strong>%s</strong></div>', $item->post_title );
		printf( '<div><strong>%s</strong> %s</div>', __( 'Post type:', 'yoast_extended' ), $item->post_type );
		printf( '<a href="%1$s" target="_blank" title="%2$s" tabindex="-1">%1$s</strong>', get_permalink( $item->ID ), __( 'Opens in new tab', 'yoast_extended' ) );
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
		$current_value = \YoastExtended\get_meta_value( 'title', $item->ID );

		printf( '<input type="text" name="seo_title" class="yoast_extended-title" data-id="%d" value="" placeholder="%s" style="width: 100%%;">', $item->ID, esc_attr( __( 'Enter a new SEO title', 'yoast_extended' ) ) );

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
		$current_value = \YoastExtended\get_meta_value( 'metadesc', $item->ID );

		printf( '<textarea name="seo_description" class="yoast_extended-description" data-id="%d" placeholder="%s" style="width: 100%%;"></textarea>', $item->ID, esc_attr( __( 'Enter a new SEO description', 'yoast_extended' ) ) );

		echo '<div class="yoast_extended-current">';
			if ( $current_value ) {
				printf( '<small><strong>%s</strong> %s</small>', __( 'Current value:', 'yoast_extended' ), esc_html( wp_unslash( $current_value ) ) );
			}
		echo '</div>';
	}


}






