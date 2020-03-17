( function( $ ) {
	'use strict';

	window.YoastExtended = window.YoastExtended || {};

	/**
	 * AJAX helper
	 *
	 * @param  {String} action
	 * @param  {Object} options
	 * @return {Promise}
	 */
	YoastExtended.ajax = function( action, options = {} ) {

		// Overrideable options
		options = $.extend( true, {
			url: YoastExtended.ajaxUrl,
			method: 'GET',
			data: {
				csrf: YoastExtended.token || null
			}
		}, options );

		// Required unchangeable option
		options.data.action = 'YoastExtended-' + ( options.data.action || action ).replace( /^YoastExtended\-/i, '' );

		// Return promise
		return $.ajax( options );
	};

} )( jQuery );
