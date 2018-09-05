( function( $ ) {

	var $body = $( 'body' );

    /**
     * Achievement list ajax call
     *
     * @since 1.0.0
     */
	function gamipress_ajax_achievement_list( achievement_list ) {

        // Show the spinner
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
			showed_ids: []
		};

		// Single achievement atts
		for( var i = 0; i < gamipress.achievement_fields.length; i++ ) {
			var achievement_field = gamipress.achievement_fields[i];

			data[achievement_field] = achievement_list.find('input[type="hidden"][name="' + achievement_field + '"]').val()
		}

		achievement_list.find('.gamipress-achievement').each(function() {
			var achievement_id = $(this).attr('id').replace('gamipress-achievement-', '');

			data.showed_ids.push( achievement_id );
		});

		$.ajax( {
			url: gamipress.ajaxurl,
			data: data,
			dataType: 'json',
			success: function( response ) {

                // Hide the spinner
				achievement_list.find( '#gamipress-achievements-spinner' ).hide();

				if ( response.data.achievements !== null ) {

					achievement_list.find( '#gamipress-achievements-container' ).append( response.data.achievements );
					achievement_list.find( '#gamipress-achievements-offset' ).val( response.data.offset );
					achievement_list.find( '#gamipress-achievements-count' ).val( response.data.achievement_count );

					// Just continue if load more has been enabled
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

    /**
     * Reset achievements query vars and run an ajax call
     *
     * @since 1.0.0
     */
	function gamipress_ajax_achievement_list_reset( achievement_list ) {
		achievement_list.find( '#gamipress-achievements-offset' ).val( 0 );
		achievement_list.find( '#gamipress-achievements-count' ).val( 0 );

		achievement_list.find( '#gamipress-achievements-container' ).html( '' );
		achievement_list.find( '#gamipress-achievements-load-more' ).hide();

		gamipress_ajax_achievement_list( achievement_list );
	}

    /**
     * Perform an achievements ajax call on change the select filter
     *
     * @since 1.0.0
     */
    $body.on( 'change', '#achievements_list_filter', function() {
		gamipress_ajax_achievement_list_reset( $(this).closest('.gamipress-achievements-list') );
	} );

    /**
     * Perform an achievements ajax call on submit the search form
     *
     * @since 1.0.0
     */
    $body.on( 'submit', '#gamipress-achievements-search-form', function( event ) {
		event.preventDefault();

		gamipress_ajax_achievement_list_reset( $(this).closest('.gamipress-achievements-list') );

		// Disable submit button on submit a search
		$(this).find('#gamipress-achievements-search-submit').prop('disabled', true);
	});

    /**
     * Enable the achievements search button when search input gets the focus
     *
     * @since 1.0.0
     */
    $body.on( 'focus', '#gamipress-achievements-search-input', function (e) {
		$(this).closest('.gamipress-achievements-list').find('#gamipress-achievements-search-submit').prop('disabled', false);
	} );

    /**
     * Achievements load more button
     *
     * @since 1.0.0
     */
    $body.on( 'click', '#gamipress-achievements-load-more', function() {
		gamipress_ajax_achievement_list( $(this).closest('.gamipress-achievements-list') );
	} );

    /**
     * Show/hide details link
     *
     * @since 1.0.0
     */
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

	/**
	 * Unlock achievement with points button
	 *
	 * @since 1.3.7
	 */
	$body.on( 'click', '.gamipress-achievement-unlock-with-points-button', function(e) {

		var button = $(this);
		var submit_wrap = button.closest('.gamipress-achievement-unlock-with-points');
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

	/**
	 * Unlock rank with points button
	 *
	 * @since 1.3.7
	 */
	$body.on( 'click', '.gamipress-rank-unlock-with-points-button', function(e) {

		var button = $(this);
		var submit_wrap = button.closest('.gamipress-rank-unlock-with-points');
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

    /**
     * Logs ajax pagination
     *
     * @since 1.4.9
     */
    $body.on( 'click', '#gamipress-logs-pagination a', function (e) {
        e.preventDefault();

        var $this = $(this);

        if( $this.hasClass('current') ) {
            return false;
        }

        var logs = $this.closest('.gamipress-logs');

        // Regex to match wordpress permalink config (paged={d}|/page/{d})
        var matches = $this.attr('href').match(/paged=(\d+)|\/page\/(\d+)/);
        var page = ( matches[1] !== undefined ) ? matches[1] : matches[2];

        var data = {
            action: 'gamipress_get_logs',
            page: page,
        };

        logs.find('.gamipress-logs-atts input').each(function() {
            data[$(this).attr('name')] = $(this).val();
        });

        // Show the spinner
        logs.find( '#gamipress-logs-spinner' ).show();

        $.ajax( {
            url: gamipress.ajaxurl,
            data: data,
            dataType: 'json',
            success: function( response ) {

                // Hide the spinner
                logs.find( '#gamipress-logs-spinner' ).hide();

                var parsed_content = $(response.data);

                logs.find('.gamipress-logs-list').html( parsed_content.find('.gamipress-logs-list').html() );
                logs.find('.gamipress-logs-pagination').html( parsed_content.find('.gamipress-logs-pagination').html() );

            }
        } );
    });

    /**
     * Earnings ajax pagination
     *
     * @since 1.4.9
     */
    $body.on( 'click', '#gamipress-earnings-pagination a', function (e) {
        e.preventDefault();

        var $this = $(this);

        if( $this.hasClass('current') ) {
            return false;
        }

        var earnings = $this.closest('.gamipress-earnings');

        // Regex to match wordpress permalink config (paged={d}|/page/{d})
        var matches = $this.attr('href').match(/paged=(\d+)|\/page\/(\d+)/);
        var page = ( matches[1] !== undefined ) ? matches[1] : matches[2];

        var data = {
            action: 'gamipress_get_user_earnings',
            page: page,
        };

        earnings.find('.gamipress-earnings-atts input').each(function() {
            data[$(this).attr('name')] = $(this).val();
        });

        // Show the spinner
        earnings.find( '#gamipress-earnings-spinner' ).show();

        $.ajax( {
            url: gamipress.ajaxurl,
            data: data,
            dataType: 'json',
            success: function( response ) {

                // Hide the spinner
                earnings.find( '#gamipress-earnings-spinner' ).hide();

                var parsed_content = $(response.data);

                earnings.find('.gamipress-earnings-table').html( parsed_content.find('.gamipress-earnings-table').html() );
                earnings.find('.gamipress-earnings-pagination').html( parsed_content.find('.gamipress-earnings-pagination').html() );

            }
        } );
    });

} )( jQuery );