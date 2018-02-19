( function( $ ) {

	var $body = $( 'body' );

	// Our main achievement list AJAX call
	function gamipress_ajax_achievement_list( achievement_list ) {
		achievement_list.find( '#gamipress-achievements-spinner' ).show();

		var data = {
			action: 'gamipress_get_achievements',

			// Achievements atts
			type: achievement_list.find('input[type="hidden"][name="type"]').val(),
			limit: achievement_list.find('input[type="hidden"][name="limit"]').val(),
			current_user: achievement_list.find('input[type="hidden"][name="current_user"]').val(),
			user_id: achievement_list.find('input[type="hidden"][name="user_id"]').val(),
			wpms: achievement_list.find('input[type="hidden"][name="wpms"]').val(),
			offset: achievement_list.find( '#gamipress-achievements-offset' ).val(),
			count: achievement_list.find( '#gamipress-achievements-count' ).val(),
			filter: ( ( achievement_list.find( '#achievements_list_filter').length ) ? achievement_list.find('#achievements_list_filter').val() : '' ),
			search: achievement_list.find('#gamipress-achievements-search-input').val(),
			orderby: achievement_list.find('input[type="hidden"][name="orderby"]').val(),
			order: achievement_list.find('input[type="hidden"][name="order"]').val(),
			include: achievement_list.find('input[type="hidden"][name="include"]').val(),
			exclude: achievement_list.find('input[type="hidden"][name="exclude"]').val(),
		};

		// Single achievement atts
		for( var i = 0; i < gamipress.achievement_fields.length; i++ ) {
			var achievement_field = gamipress.achievement_fields[i];

			data[achievement_field] = achievement_list.find('input[type="hidden"][name="' + achievement_field + '"]').val()
		}

		$.ajax( {
			url: gamipress.ajaxurl,
			data: data,
			dataType: 'json',
			success: function( response ) {
				achievement_list.find( '#gamipress-achievements-spinner' ).hide();

				if ( response.data.message === null ) {
					//alert("That's all folks!");
				} else {

					achievement_list.find( '#gamipress-achievements-container' ).append( response.data.message );
					achievement_list.find( '#gamipress-achievements-offset' ).val( response.data.offset );
					achievement_list.find( '#gamipress-achievements-count' ).val( response.data.achievement_count );

					// Just continue if load more has been enables
					if( achievement_list.find( '#gamipress-achievements-load-more').length ) {

						if ( response.data.query_count <= response.data.offset ) {

							// Hide load more button
							achievement_list.find( '#gamipress-achievements-load-more' ).hide();

						} else {

							// Show load more button
							achievement_list.find( '#gamipress-achievements-load-more' ).show();

						}

					}

				}
			}
		} );
	}

	// Reset all our base query vars and run an AJAX call
	function gamipress_ajax_achievement_list_reset( achievement_list ) {
		achievement_list.find( '#gamipress-achievements-offset' ).val( 0 );
		achievement_list.find( '#gamipress-achievements-count' ).val( 0 );

		achievement_list.find( '#gamipress-achievements-container' ).html( '' );
		achievement_list.find( '#gamipress-achievements-load-more' ).hide();

		gamipress_ajax_achievement_list( achievement_list );
	}

	// Listen for changes to the achievement filter
    $body.on( 'change', '#achievements_list_filter', function() {
		gamipress_ajax_achievement_list_reset( $(this).closest('.gamipress-achievements-list') );
	} );

	// Listen for search queries
    $body.on( 'submit', '#gamipress-achievements-search-form', function( event ) {
		event.preventDefault();

		gamipress_ajax_achievement_list_reset( $(this).closest('.gamipress-achievements-list') );

		// Disabled submit button
		$(this).find('#gamipress-achievements-search-submit').prop('disabled', true);
	});

	// Enabled submit button
    $body.on( 'focus', '#gamipress-achievements-search-input', function (e) {
		$(this).closest('.gamipress-achievements-list').find('#gamipress-achievements-search-submit').prop('disabled', false);
	} );

	// Listen for users clicking the "Load More" button
    $body.on( 'click', '#gamipress-achievements-load-more', function() {
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

	// Listen for unlock achievement with points button click
	$body.on( 'click', '.gamipress-achievement-unlock-with-points-button', function(e) {

		var button = $(this);
		var submit_wrap = button.parent();
		var spinner = submit_wrap.find('.gamipress-spinner');
		var achievement_id = button.data('id');

		// Disable the button
		button.prop( 'disabled', true );

		// Hide previous notices
		if( submit_wrap.find('.gamipress-achievement-unlock-with-points-response').length ) {
			submit_wrap.find('.gamipress-achievement-unlock-with-points-response').slideUp()
		}

		// Show the spinner
		spinner.show();

		$.ajax( {
			url: gamipress.ajaxurl,
			method: 'POST',
			dataType: 'json',
			data: {
				action: 'gamipress_unlock_achievement_with_points',
				achievement_id: achievement_id
			},
			success: function( response ) {

				// Ensure response wrap
				if( submit_wrap.find('.gamipress-achievement-unlock-with-points-response').length === 0 ) {
					submit_wrap.prepend('<div class="gamipress-achievement-unlock-with-points-response gamipress-notice" style="display: none;"></div>')
				}

				var response_wrap = submit_wrap.find('.gamipress-achievement-unlock-with-points-response');

				// Add class gamipress-notice-success on successful unlock, if not will add the class gamipress-notice-error
				response_wrap.addClass( 'gamipress-notice-' + ( response.success === true ? 'success' : 'error' ) );

				// Update and show response messages
				response_wrap.html( response.data );
				response_wrap.slideDown();

				// Hide the spinner
				spinner.hide();

				if( response.success === true ) {
					// Hide the button
					button.slideUp();

					// Add the class earned to the achievement
					// Single template
					button.closest('.single-achievement').addClass('user-has-earned');
					button.closest('.user-has-not-earned[class*="post"]').removeClass('user-has-not-earned').addClass('user-has-earned');

					// Shortcode/Widget template
					button.closest('.gamipress-achievement.user-has-not-earned').removeClass('user-has-not-earned').addClass('user-has-earned');
				} else {
					// Enable the button
					button.prop( 'disabled', false );
				}
			}
		});
	});

	// Listen for unlock rank with points button click
	$body.on( 'click', '.gamipress-rank-unlock-with-points-button', function(e) {

		var button = $(this);
		var submit_wrap = button.parent();
		var spinner = submit_wrap.find('.gamipress-spinner');
		var rank_id = button.data('id');

		// Disable the button
		button.prop( 'disabled', true );

		// Hide previous notices
		if( submit_wrap.find('.gamipress-rank-unlock-with-points-response').length ) {
			submit_wrap.find('.gamipress-rank-unlock-with-points-response').slideUp()
		}

		// Show the spinner
		spinner.show();

		$.ajax( {
			url: gamipress.ajaxurl,
			method: 'POST',
			dataType: 'json',
			data: {
				action: 'gamipress_unlock_rank_with_points',
				rank_id: rank_id
			},
			success: function( response ) {

				// Ensure response wrap
				if( submit_wrap.find('.gamipress-rank-unlock-with-points-response').length === 0 ) {
					submit_wrap.prepend('<div class="gamipress-rank-unlock-with-points-response gamipress-notice" style="display: none;"></div>')
				}

				var response_wrap = submit_wrap.find('.gamipress-rank-unlock-with-points-response');

				// Add class gamipress-notice-success on successful unlock, if not will add the class gamipress-notice-error
				response_wrap.addClass( 'gamipress-notice-' + ( response.success === true ? 'success' : 'error' ) );

				// Update and show response messages
				response_wrap.html( response.data );
				response_wrap.slideDown();

				// Hide the spinner
				spinner.hide();

				if( response.success === true ) {
					// Hide the button
					button.slideUp();

					// Add the class earned to the rank
					// Single template
					button.closest('.single-rank').addClass('user-has-earned');
					button.closest('.user-has-not-earned[class*="post"]').removeClass('user-has-not-earned').addClass('user-has-earned');

					// Shortcode/Widget template
					button.closest('.gamipress-rank.user-has-not-earned').removeClass('user-has-not-earned').addClass('user-has-earned');
				} else {
					// Enable the button
					button.prop( 'disabled', false );
				}
			}
		});
	});

} )( jQuery );