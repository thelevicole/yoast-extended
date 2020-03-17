<?php

	/**
	 * Get the admin view controller instance
	 *
	 * @var \YoastExtnded\Admin\Views
	 */
	$controller = yoast_extended()->admin;

	/**
	 * Available tabs
	 *
	 * @var array
	 */
	$tabs = [
		'post_types' => __( 'Post types', 'yoast_extended' ),
		'taxonomies' => __( 'Taxonomies', 'yoast_extended' ),
	];

	/**
	 * Get the current selected tab from request
	 *
	 * @var string
	 */
	$current = !empty( $_GET[ 'tab' ] ) ? sanitize_text_field( $_GET[ 'tab' ] ) : null;

	/**
	 * Validate selected tab and set default value
	 */
	if ( !$current || !isset( $tabs[ $current ] ) ) {
		$current = key( $tabs );
	}
?>
<main class="wrap">
	<h1 class="wp-heading-inline"><?= __( 'Bulk Editor', 'yoast_extended' ); ?></h1>

	<h2 class="nav-tab-wrapper">
		<?php foreach ( $tabs as $key => $label ): ?>
			<a class="nav-tab <?= $current === $key ? 'nav-tab-active' : ''; ?>" href="<?= $controller->build_url( [ 'tab' => $key ], 'bulk-edit' ); ?>"><?= $label; ?></a>
		<?php endforeach ?>
	</h2>
	<br>

	<?php yoast_extended()->require( 'includes/admin/views/bulk-edit/' . $current . '.php' ); ?>

</main>
