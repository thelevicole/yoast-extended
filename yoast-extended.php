<?php

/**
 * Plugin Name: Yoast SEO Extended
 * Plugin URI: https://github.com/thelevicole/yoast-extended
 * Description: Advanced features and tools to extended the all-in-one SEO solution for WordPress, Yoast.
 * Author: Levi Cole
 * Author URI: https://thelevicole.com
 * Version: 1.0.0
 * Text Domain: yoast_extended
 * Network: true
 * Requires at least: 5.3.2
 * Requires PHP: 7.2.0
 */

/**
 * Plugin controller class
 */
class YoastExtended {

	protected $settings = [];

	function __construct() {}

	/**
	 * Setup plugin on init
	 *
	 * @return void
	 */
	public function initialise() {

		/**
		 * Register settings
		 *
		 * @var array
		 */
		$this->settings = [

			// Generic
			'version'		=> '1.0.0',
			'path'			=> plugin_dir_path( __FILE__ ),
			'url'			=> plugin_dir_url( __FILE__ ),
			'basename'		=> plugin_basename( __FILE__ ),
			'capability'	=> 'manage_options'
		];

		/**
		 * Define global constants
		 */
		$this->define( 'PATH', $this->settings[ 'path' ] );
		$this->define( 'URL', $this->settings[ 'url' ] );
		$this->define( 'BASENAME', $this->settings[ 'url' ] );

		/**
		 * Include required files
		 */
		$this->require( 'includes/helpers/wordpress.php' );
		$this->require( 'includes/helpers/general.php' );
		$this->require( 'includes/helpers/yoast.php' );

		/**
		 * Load files in admin only
		 */
		if ( is_admin() ) {
			$this->require( 'includes/admin/class-Notice.php' );
			$this->require( 'includes/admin/class-NoticeController.php' );
			$this->require( 'includes/admin/class-AjaxRequests.php' );
			$this->require( 'includes/admin/class-Views.php' );

			// Load WP_List_Table's
			$this->require( 'includes/admin/tables/class-BulkEdit_PostTypes.php' );
			$this->require( 'includes/admin/tables/class-BulkEdit_Taxonomies.php' );

			/**
			 * Inform user that they Yoast SEO plugin has not been detected
			 */
			if ( !function_exists( 'wpseo_activate' ) ) {
				$this->notices->add_warning( __( 'Yoast SEO plugin has not been detected. Please install and activate this pluging to use Yoast Extended.', 'yoast_extended' ) );
			}
		}

	}

	/**
	 * Define a constant safetly, includes predefined check and adds a prefix
	 *
	 * @param	string	$key	Name of the globally created constant
	 * @param	mixed	$value	The value that will be returned when calling the constant
	 * @return	void
	 */
	public function define( string $key, $value ) {

		$key = 'YoastExtended_' . strtoupper( $key );

		if ( !defined( $key ) ) {
			define( $key, $value );
		}
	}

	/**
	 * Include file from relative path
	 *
	 * @param	string	$path
	 * @return	void
	 */
	public function include( string $path ) {
		$path = $this->get_setting( 'path' ) . ltrim( $path, '/\\' );

		if ( file_exists( $path ) ) {
			include $path;
		}
	}

	/**
	 * Require file from relative path
	 *
	 * @param	string	$path
	 * @return	void
	 */
	public function require( string $path ) {
		$path = $this->get_setting( 'path' ) . ltrim( $path, '/\\' );

		if ( file_exists( $path ) ) {
			require $path;
		}
	}

	/**
	 * Check if the setting with `$name` exists in this instance
	 *
	 * @param	string	$name	Name of setting
	 * @return	boolean
	 */
	public function has_setting( string $name ): bool {
		return isset( $this->settings[ $name ] );
	}

	/**
	 * Get a specific setting value from the instance
	 *
	 * @param	string	$name	Name of setting
	 * @return	mixed
	 */
	public function get_setting( string $name ) {
		return $this->has_setting( $name ) ? $this->settings[ $name ] : null;
	}

	/**
	 * Update an instance setting
	 *
	 * @param	string	$name	Name of setting
	 * @param	mixed	$value
	 * @return	boolean
	 */
	public function update_setting( string $name, $value ): bool {
		$this->settings[ $name ] = $value;
		return true;
	}
}

/**
 * Get global instance of plugin controller
 *
 * @return YoastExtended
 */
function yoast_extended() {
	global $yoast_extended;

	if ( empty( $yoast_extended ) ) {
		$yoast_extended = new YoastExtended;
		$yoast_extended->initialise();
	}

	return $yoast_extended;
}

/**
 * Initialise on load
 */
yoast_extended();
