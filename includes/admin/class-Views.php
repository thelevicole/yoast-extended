<?php

namespace YoastExtended\Admin;

use \WP_Error;

/**
 *
 */
class Views {

	/**
	 * The admin page slug
	 *
	 * @var string
	 */
	public $key = 'yoast-extended';

	/**
	 * Slug of parent page
	 *
	 * @var string
	 */
	public $parent_page = 'wpseo_dashboard';

	/**
	 * The admin menu title
	 *
	 * @var string
	 */
	public $menu_title = '';

	/**
	 * The admin page title
	 *
	 * @var string
	 */
	public $page_title = '';

	/**
	 * Admin minimum capability
	 *
	 * @var string
	 */
	public $capability = '';

	/**
	 * The full admin page url
	 *
	 * @var string
	 */
	public $url;

	/**
	 * Valid slugs for used in admin views
	 *
	 * @var array
	 */
	public $view_slugs = [ 'default', 'bulk-edit' ];

	/**
	 * Class constructor
	 */
	function __construct() {

		/**
		 * Get page title from main controller
		 *
		 * @var string
		 */
		$this->page_title = __( 'Yoast SEO Extended', 'yoast_extended' );

		/**
		 * Get menu title from main controller
		 *
		 * @var string
		 */
		$this->menu_title = __( 'Yoast Extended', 'yoast_extended' );

		/**
		 * Get minimum capabilities from main controller
		 *
		 * @var string
		 */
		$this->capability = yoast_extended()->get_setting( 'capability' );

		/**
		 * Build this page URL
		 *
		 * @var string
		 */
		$this->url = $this->build_url();

		/**
		 * Enqueue scripts
		 */
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );

		/**
		 * Register menu item
		 */
		add_action( 'admin_menu', [ $this, 'admin_menu' ] );

	}

	/**
	 * Build a url relative to the base view key
	 *
	 * @param  array       $args      [description]
	 * @param  ?string     $key_affix [description]
	 * @return string                 [description]
	 */
	public function build_url( array $args = [], ?string $key_affix = null ) {
		$args[ 'page' ] = $this->key . ( $key_affix ? preg_replace( '/^_?/' , '_', $key_affix ) : null );
		return add_query_arg( $args, admin_url( 'admin.php' ) );
	}

	/**
	 * Add js and css to admin
	 *
	 * @param	string	$hook
	 * @return	void
	 */
	public function enqueue_scripts( $hook ) {
		if ( $hook === 'seo_page_' . $this->key . '_bulk-edit' ) {
			$version = yoast_extended()->get_setting( 'version' );
			wp_enqueue_style( $this->key, yoast_extended()->get_setting( 'url' ) . 'assets/css/backend.css', '', $version );
			wp_enqueue_script( $this->key, yoast_extended()->get_setting( 'url' ) . 'assets/js/backend.js', [ 'jquery' ], $version, true );

			wp_localize_script( $this->key, 'YoastExtended', [
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'token' => wp_create_nonce( yoast_extended()->ajax->nonce_action )
			] );
		}
	}

	/**
	 * Register admin pages
	 *
	 * @link https://developer.wordpress.org/reference/functions/add_submenu_page/
	 * @return	void
	 */
	public function admin_menu() {
		add_submenu_page( $this->parent_page, __( 'Yoast Bulk Edit', 'yoast_extended' ), __( 'Bulk Editor', 'yoast_extended' ), $this->capability, $this->key . '_bulk-edit', [ $this, 'render_bulk_edit' ], 99 );
	}

	/**
	 * Render the admin dashboard
	 *
	 * @return	void
	 */
	public function render_bulk_edit() {
		yoast_extended()->require( 'includes/admin/views/bulk-edit.php' );
	}
}

/**
 * Init class when file loaded
 */
yoast_extended()->admin = new \YoastExtended\Admin\Views;
