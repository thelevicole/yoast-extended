<?php

	/**
	 * Get the admin view controller instance
	 *
	 * @var \YoastExtnded\Admin\Views
	 */
	$controller = yoast_extended()->admin;

	/**
	 * Create new instance of the table
	 *
	 * @var \YoastExtended\Admin\Tables\BulkEdit_PostTypes
	 */
	$table =  new \YoastExtended\Admin\Tables\BulkEdit_PostTypes( [
		'singular'	=> 'yoast_extended-bulk_edit-post_type',
		'plural'	=> 'yoast_extended-bulk_edit-post_types',
		'ajax'		=> false
	] );

	/**
	 * Perform db query
	 */
	$table->prepare_items();

	/**
	 * Translated search label
	 *
	 * @var string
	 */
	$search_label = __( 'Search post types', 'yoast_extended' );

	/**
	 * Get the current search value from request
	 *
	 * @var ?string
	 */
	$search_value = !empty( $_REQUEST[ 's' ] ) ? sanitize_text_field( $_REQUEST[ 's' ] ) : null;

?>

<?php $table->views(); ?>

<form method="post" id="yoast_extended-bulk_edit-post_types">
	<input type="hidden" name="page" value="<?= !empty( $_GET[ 'page' ] ) ? esc_attr( $_GET[ 'page' ] ) : null; ?>">
	<input type="hidden" name="tab" value="<?= !empty( $_GET[ 'tab' ] ) ? esc_attr( $_GET[ 'tab' ] ) : null; ?>">
	<p class="search-box" style="margin-bottom: 10px;">
		<label class="screen-reader-text" for="post-search-input"><?= esc_html( $search_label ); ?>:</label>
		<input type="search" id="post-search-input" name="s" value="<?= esc_attr( $search_value ); ?>">
		<input type="submit" id="search-submit" class="button" value="<?= esc_attr( $search_label ); ?>">
	</p>
	<?php $table->display(); ?>
</form>
