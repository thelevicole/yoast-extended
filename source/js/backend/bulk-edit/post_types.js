( function( $, controller ) {
	'use strict';

	const $form = $( 'form#yoast_extended-bulk_edit-post_types' );

	let eventTracker = {};
	let valueTracker = {};

	const bulkInputTypes = [
		{
			selector: '.yoast_extended-title[data-id]',
			field: 'title',
			delay: 800 // 0.8 seconds
		},
		{
			selector: '.yoast_extended-description[data-id]',
			field: 'metadesc',
			delay: 1000 // 1 seconds
		}
	];

	/**
	 * Abort script if form not on page
	 */
	if ( !$form.length ) {
		return;
	}

	for ( let i = 0; i < bulkInputTypes.length; i++ ) {
		const bulkInput = bulkInputTypes[ i ];

		const selector = bulkInput.selector;
		const field = bulkInput.field;
		const delay = bulkInput.delay;

		// Create empty event tracker group
		if ( !eventTracker[ field ] ) {
			eventTracker[ field ] = {};
		}

		// Create empty value tracker group
		if ( !valueTracker[ field ] ) {
			valueTracker[ field ] = {};
		}

		$form.on( 'cut paste input change edit', selector, function() {
			const $input = $( this );
			const $prev = $input.next( '.yoast_extended-current' );
			const post_id = parseInt( $input.data( 'id' ) );
			const value = $input.val();

			if ( post_id ) {

				// Only run request if new value is different to old value
				if ( value !== valueTracker[ field ][ post_id ] ) {

					// Cancel previous edit
					if ( eventTracker[ field ][ post_id ] ) {
						clearTimeout( eventTracker[ field ][ post_id ] );
					}

					// Store old/new value in tracker
					valueTracker[ field ][ post_id ] = value;

					// Perform ajax request after timeout has completed
					eventTracker[ field ][ post_id ] = setTimeout( () => {

						$input.blur().prop( 'disabled', true );

						controller.ajax( 'bulk_edit_post_types', {
							method: 'POST',
							data: {
								post_id: post_id,
								field: field,
								value: value
							}
						} ).then( function( response ) {

							// Reset edit field
							if ( response.success ) {
								$input.val( '' );

								// Replace current value preview
								if ( response.data.replacement && $prev.length ) {
									$prev.html( response.data.replacement );
									$prev.css( 'color', 'darkgreen' );
								}
							} else {
								if ( typeof response.data === 'string' && $prev.length ) {
									$prev.empty();
									$prev.append( $( '<small>', { text: response.data } ) );
									$prev.css( 'color', 'red' );
								}
							}

						} ).always( function() {
							$input.prop( 'disabled', false );
						} );

					}, delay );
				}

			}

		} );

	}


} )( jQuery, window.YoastExtended );
