( function( $ ) {

	var $body = $( 'body' );

	// Our main achievement list AJAX call
	function gamipress_ajax_achievement_list( achievement_list ) {
		achievement_list.find( '.gamipress-spinner' ).show();

		$.ajax( {
			url: gamipress.ajaxurl,
			data: {
				action: 'gamipress_get_achievements',
				type: achievement_list.find('input[type="hidden"][name="type"]').val(),
				limit: achievement_list.find('input[type="hidden"][name="limit"]').val(),
				user_id: achievement_list.find('input[type="hidden"][name="user_id"]').val(),
				wpms: achievement_list.find('input[type="hidden"][name="wpms"]').val(),
				offset: achievement_list.find( '#gamipress_achievements_offset' ).val(),
				count: achievement_list.find( '#gamipress_achievements_count' ).val(),
				filter: ( ( achievement_list.find( '#achievements_list_filter').length ) ? achievement_list.find('#achievements_list_filter').val() : '' ),
				search: achievement_list.find('#achievements_list_search').val(),
				orderby: achievement_list.find('input[type="hidden"][name="orderby"]').val(),
				order: achievement_list.find('input[type="hidden"][name="order"]').val(),
				include: achievement_list.find('input[type="hidden"][name="include"]').val(),
				exclude: achievement_list.find('input[type="hidden"][name="exclude"]').val(),
			},
			dataType: 'json',
			success: function( response ) {
				achievement_list.find( '.gamipress-spinner' ).hide();

				if ( response.data.message === null ) {
					//alert("That's all folks!");
				} else {

					achievement_list.find( '#gamipress-achievements-container' ).append( response.data.message );
					achievement_list.find( '#gamipress_achievements_offset' ).val( response.data.offset );
					achievement_list.find( '#gamipress_achievements_count' ).val( response.data.achievement_count );

					// Just continue if load more has been enables
					if( achievement_list.find( '#achievements_list_load_more').length ) {

						if ( response.data.query_count <= response.data.offset ) {

							// Hide load more button
							achievement_list.find( '#achievements_list_load_more' ).hide();

						} else {

							// Show load more button
							achievement_list.find( '#achievements_list_load_more' ).show();

						}

					}

				}
			}
		} );
	}

	// Reset all our base query vars and run an AJAX call
	function gamipress_ajax_achievement_list_reset( achievement_list ) {
		achievement_list.find( '#gamipress_achievements_offset' ).val( 0 );
		achievement_list.find( '#gamipress_achievements_count' ).val( 0 );

		achievement_list.find( '#gamipress-achievements-container' ).html( '' );
		achievement_list.find( '#achievements_list_load_more' ).hide();

		gamipress_ajax_achievement_list( achievement_list );
	}

	// Listen for changes to the achievement filter
    $body.on( 'change', '#achievements_list_filter', function() {
		gamipress_ajax_achievement_list_reset( $(this).closest('.gamipress-achievements-list') );
	} );

	// Listen for search queries
    $body.on( 'submit', '#achievements_list_search_go_form', function( event ) {
		event.preventDefault();

		gamipress_ajax_achievement_list_reset( $(this).closest('.gamipress-achievements-list') );

		// Disabled submit button
		$(this).prop('disabled', true);
	});

	// Enabled submit button
    $body.on( 'focus', '#achievements_list_search', function (e) {
		$(this).closest('.gamipress-achievements-list').find('#achievements_list_search_go').prop('disabled', false);
	} );

	// Listen for users clicking the "Load More" button
    $body.on( 'click', '#achievements_list_load_more', function() {
		$(this).closest('.gamipress-achievements-list').find( '.gamipress-spinner' ).show();

		gamipress_ajax_achievement_list( $(this).closest('.gamipress-achievements-list') );
	} );

	// Initial achievements lists load
	$('.gamipress-achievements-list').each(function() {
		gamipress_ajax_achievement_list( $(this) );
	});

	// Listen for users clicking the show/hide details link
    $body.on( 'click', '.gamipress-open-close-switch a', function( event ) {
		event.preventDefault();

		var link = $( this );

		if ( 'close' === link.data( 'action' ) ) {
			link.parent().siblings( '.gamipress-extras-window' ).slideUp( 300 );
			link.data( 'action', 'open' ).prop( 'class', 'show-hide-open' ).text( link.data('open-text') );
		} else {
			link.parent().siblings( '.gamipress-extras-window' ).slideDown( 300 );
			link.data( 'action', 'close' ).prop( 'class', 'show-hide-close' ).text( link.data('close-text') );
		}
	} );


} )( jQuery );