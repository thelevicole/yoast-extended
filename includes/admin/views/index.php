<?php

	$controller = yoast_extended()->admin;

	$view = !empty( $_GET[ 'view' ] ) ? sanitize_text_field( $_GET[ 'view' ] ) : null;
	$view = in_array( $view, $controller->view_slugs ) ? $view : $controller->view_slugs[ key( $controller->view_slugs ) ];

?>
<main class="wrap">
	<h1 class="wp-heading-inline"><?= $controller->page_title; ?></h1>
	<?php yoast_extended()->require( 'includes/admin/views/' . $view . '.php' ); ?>
</main>
