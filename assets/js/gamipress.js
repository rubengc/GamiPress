( function( $ ) {

	var $body = $( 'body' );

    /**
     * Achievement list ajax call
     *
     * @since 	1.0.0
	 * @updated 1.7.9 Added nonce usage
     */
	function gamipress_ajax_achievement_list( achievement_list ) {

        // Show the spinner
		achievement_list.find( '#gamipress-achievements-spinner' ).show();

		var data = {
			action: 'gamipress_get_achievements',
			nonce: gamipress.nonce,

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

        /**
         * Allow external functions to add their own data to the array
         *
         * @since 1.5.9
         *
         * @selector    .gamipress-achievements-list
         * @event       gamipress_achievements_list_data
		 *
		 * @param Object data
         */
        achievement_list.trigger( 'gamipress_achievements_list_request_data', [ data ] );

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

                /**
                 * Allow external functions to add their own functionality
                 *
                 * @since 1.5.9
                 *
                 * @selector    .gamipress-achievements-list
                 * @event       gamipress_achievements_list_data
                 *
                 * @param Object response
                 */
                achievement_list.trigger( 'gamipress_achievements_list_request_success', [ response ] );
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
	 * Unlock achievement with points
	 *
	 * @since 	1.7.8.1
	 * @updated 1.7.9 Added nonce usage
	 */
    function gamipress_unlock_achievement_with_points( submit_wrap ) {

		var button = submit_wrap.find('.gamipress-achievement-unlock-with-points-button');
		var spinner = submit_wrap.find('.gamipress-spinner');
		var achievement_id = submit_wrap.data('id');
		var confirmation = submit_wrap.find('.gamipress-achievement-unlock-with-points-confirmation');

		// Show the spinner
		spinner.show();

		/**
		 * Allow external functions to process anything before unlock achievement with points
		 *
		 * @since 1.7.7
		 *
		 * @selector    .gamipress-achievement-unlock-with-points-button
		 * @event       gamipress_before_unlock_achievement_with_points
		 */
		submit_wrap.trigger( 'gamipress_before_unlock_achievement_with_points', [] );

		$.ajax( {
			url: gamipress.ajaxurl,
			method: 'POST',
			dataType: 'json',
			data: {
				action: 'gamipress_unlock_achievement_with_points',
				nonce: gamipress.nonce,
				achievement_id: achievement_id
			},
			success: function( response ) {

				// Ensure response wrap
				if( submit_wrap.find('.gamipress-achievement-unlock-with-points-response').length === 0 )
					submit_wrap.append('<div class="gamipress-achievement-unlock-with-points-response gamipress-notice" style="display: none;"></div>');

				var response_wrap = submit_wrap.find('.gamipress-achievement-unlock-with-points-response');

				// Add class gamipress-notice-success on successful unlock, if not will add the class gamipress-notice-error
				response_wrap.addClass( 'gamipress-notice-' + ( response.success === true ? 'success' : 'error' ) );

				// Update and show response messages
				response_wrap.html( response.data );
				response_wrap.slideDown();

				// Hide the spinner
				spinner.hide();

				if( response.success === true ) {

					// Hide confirmation
					if( confirmation.length )
						confirmation.slideUp();

					// Hide the button
					button.slideUp();

					// Add the class earned to the achievement
					// Single template
					button.closest('.single-achievement').addClass('user-has-earned');
					button.closest('.user-has-not-earned[class*="post"]').removeClass('user-has-not-earned').addClass('user-has-earned');

					// Shortcode/Widget template
					button.closest('.gamipress-achievement.user-has-not-earned').removeClass('user-has-not-earned').addClass('user-has-earned');
				} else {

					if( confirmation.length ) {
						// Hide confirmation and enable confirm/cancel buttons
						confirmation.slideUp();
						submit_wrap.find('.gamipress-achievement-unlock-with-points-confirm-button').prop( 'disabled', false );
						submit_wrap.find('.gamipress-achievement-unlock-with-points-cancel-button').prop( 'disabled', false );
					}

					// Enable the button
					button.prop( 'disabled', false );
				}

				/**
				 * Allow external functions to process anything after unlock achievement with points
				 *
				 * @since 1.7.7
				 *
				 * @selector    .gamipress-achievement-unlock-with-points-button
				 * @event       gamipress_after_unlock_achievement_with_points
				 *
				 * @param Object response	Response retrieved from server
				 */
				submit_wrap.trigger( 'gamipress_after_unlock_achievement_with_points', [ response ] );
			}
		});

	}

	/**
	 * Unlock rank with points
	 *
	 * @since 	1.7.8.1
	 * @updated 1.7.9 Added nonce usage
	 */
	function gamipress_unlock_rank_with_points( submit_wrap ) {

		var button = submit_wrap.find('.gamipress-rank-unlock-with-points-button');
		var spinner = submit_wrap.find('.gamipress-spinner');
		var rank_id = submit_wrap.data('id');
		var confirmation = submit_wrap.find('.gamipress-rank-unlock-with-points-confirmation');

		// Show the spinner
		spinner.show();

		/**
		 * Allow external functions to process anything before unlock rank with points
		 *
		 * @since 1.7.7
		 *
		 * @selector    .gamipress-rank-unlock-with-points-button
		 * @event       gamipress_before_unlock_rank_with_points
		 */
		submit_wrap.trigger( 'gamipress_before_unlock_rank_with_points', [] );

		$.ajax( {
			url: gamipress.ajaxurl,
			method: 'POST',
			dataType: 'json',
			data: {
				action: 'gamipress_unlock_rank_with_points',
				nonce: gamipress.nonce,
				rank_id: rank_id
			},
			success: function( response ) {

				// Ensure response wrap
				if( submit_wrap.find('.gamipress-rank-unlock-with-points-response').length === 0 )
					submit_wrap.append('<div class="gamipress-rank-unlock-with-points-response gamipress-notice" style="display: none;"></div>');

				var response_wrap = submit_wrap.find('.gamipress-rank-unlock-with-points-response');

				// Add class gamipress-notice-success on successful unlock, if not will add the class gamipress-notice-error
				response_wrap.addClass( 'gamipress-notice-' + ( response.success === true ? 'success' : 'error' ) );

				// Update and show response messages
				response_wrap.html( response.data );
				response_wrap.slideDown();

				// Hide the spinner
				spinner.hide();

				if( response.success === true ) {

					// Hide confirmation
					if( confirmation.length )
						confirmation.slideUp();

					// Hide the button
					button.slideUp();

					// Add the class earned to the rank
					// Single template
					button.closest('.single-rank').addClass('user-has-earned');
					button.closest('.user-has-not-earned[class*="post"]').removeClass('user-has-not-earned').addClass('user-has-earned');

					// Shortcode/Widget template
					button.closest('.gamipress-rank.user-has-not-earned').removeClass('user-has-not-earned').addClass('user-has-earned');
				} else {

					if( confirmation.length ) {
						// Hide confirmation and enable confirm/cancel buttons
						confirmation.slideUp();
						submit_wrap.find('.gamipress-rank-unlock-with-points-confirm-button').prop( 'disabled', false );
						submit_wrap.find('.gamipress-rank-unlock-with-points-cancel-button').prop( 'disabled', false );
					}

					// Enable the button
					button.prop( 'disabled', false );
				}

				/**
				 * Allow external functions to process anything after unlock rank with points
				 *
				 * @since 1.7.7
				 *
				 * @selector    .gamipress-rank-unlock-with-points-button
				 * @event       gamipress_after_unlock_rank_with_points
				 *
				 * @param Object response	Response retrieved from server
				 */
				submit_wrap.trigger( 'gamipress_after_unlock_rank_with_points', [ response ] );
			}
		});

	}

	/**
	 * Unlock achievement/rank with points button
	 *
	 * @since 1.3.7
	 */
	$body.on( 'click', '.gamipress-achievement-unlock-with-points-button, .gamipress-rank-unlock-with-points-button', function(e) {

		var button = $(this);

		var selector = ( button.hasClass('gamipress-achievement-unlock-with-points-button') ? 'achievement' : 'rank' );

		var submit_wrap = button.closest('.gamipress-' + selector + '-unlock-with-points');
		var confirmation = submit_wrap.find('.gamipress-' + selector + '-unlock-with-points-confirmation');

		// Disable the button
		button.prop( 'disabled', true );

		// Hide previous notices
		if( submit_wrap.find('.gamipress-' + selector + '-unlock-with-points-response').length )
			submit_wrap.find('.gamipress-' + selector + '-unlock-with-points-response').slideUp();

		// If has confirmation, show it and return
		if( confirmation.length ) {
			confirmation.slideDown();
		} else {
			// Perform the ajax request to unlock with points
			if( selector === 'achievement' )
				gamipress_unlock_achievement_with_points( submit_wrap );
			else
				gamipress_unlock_rank_with_points( submit_wrap );
		}

	});

	/**
	 * Unlock achievement/rank with points confirmation confirm button
	 *
	 * @since 1.7.8.1
	 */
	$body.on( 'click', '.gamipress-achievement-unlock-with-points-confirm-button, .gamipress-rank-unlock-with-points-confirm-button', function(e) {

		var $this = $(this);
		var selector = ( $this.hasClass('gamipress-achievement-unlock-with-points-confirm-button') ? 'achievement' : 'rank' );

		var submit_wrap = $this.closest('.gamipress-' + selector + '-unlock-with-points');

		// Disable the confirm button
		$this.prop( 'disabled', true );
		// Disable the cancel button
		submit_wrap.find('.gamipress-' + selector + '-unlock-with-points-cancel-button').prop( 'disabled', true );

		// Perform the ajax request to unlock with points
		if( selector === 'achievement' )
			gamipress_unlock_achievement_with_points( submit_wrap );
		else
			gamipress_unlock_rank_with_points( submit_wrap );

	});

	/**
	 * Unlock achievement/rank with points confirmation cancel button
	 *
	 * @since 1.7.8.1
	 */
	$body.on( 'click', '.gamipress-achievement-unlock-with-points-cancel-button, .gamipress-rank-unlock-with-points-cancel-button', function(e) {

		var $this = $(this);
		var selector = ( $this.hasClass('gamipress-achievement-unlock-with-points-cancel-button') ? 'achievement' : 'rank' );

		var submit_wrap = $this.closest('.gamipress-' + selector + '-unlock-with-points');
		var button = submit_wrap.find('.gamipress-' + selector + '-unlock-with-points-button');
		var confirmation = submit_wrap.find('.gamipress-' + selector + '-unlock-with-points-confirmation');

		// Hide confirmation
		confirmation.slideUp();

		// Enable the button
		button.prop( 'disabled', false );

	});

	/**
	 * Share achievement/rank button
	 *
	 * @since 1.8.6
	 */
	$body.on( 'click', '.gamipress-share-button', function (e) {
		e.preventDefault();

		var wrapper = $(this).closest('.gamipress-share-buttons');
		var network = $(this).data('network');

		// URL templates
		var templates = {
			facebook: 'https://www.facebook.com/sharer.php?u={url}',
			twitter: 'https://twitter.com/intent/tweet?text={text}%0A{url}',
			linkedin: 'https://www.linkedin.com/shareArticle?mini=true&url={url}&title={title}&summary={text}&source={url}',
			pinterest: 'https://www.pinterest.com/pin/create/button/?url={url}&media={image}',
		};

		// Default dimensions for the new window
		var width = 640;
		var height = 480;

		if( templates[network] !== undefined ) {

			var url = wrapper.data('url');
			var title = wrapper.data('title');
			var text = wrapper.data('twitter-text');
			var image = wrapper.data('image');

			// Replace the template (ensuring each element is correctly encoded)
			var parsed_url = templates[network]
				.replace( '{url}', encodeURIComponent( url ) )
				.replace( '{title}', encodeURIComponent( title ) )
				.replace( '{image}', encodeURIComponent( image ) )
				.replace( '{text}', encodeURIComponent( text ) );

			// Center the new window at center of the screen
			var window_top = ( screen.height / 2 ) - ( height / 2 );
			var window_left = ( screen.width / 2 ) - ( width / 2 );

			var window_parameters = 'toolbar=0,status=0,width=' + width + ',height=' + height + ',top=' + window_top + ',left=' + window_left;

			var share_window = window.open( parsed_url, network, window_parameters );

			// Force focus to the new window
			if( window.focus ) {
				share_window.focus();
			}

		}

	});

    /**
     * Logs ajax pagination
     *
     * @since 	1.4.9
	 * @updated 1.7.9 Added nonce usage
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
			nonce: gamipress.nonce,
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
     * @since 	1.4.9
	 * @updated 1.7.9 Added nonce usage
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
			nonce: gamipress.nonce,
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

	/**
	 * Email settings
	 *
	 * @since 	2.2.1
	 */
	$body.on( 'change', '.gamipress-email-settings input', function (e) {

		var $this = $(this);
		var form = $this.closest('.gamipress-email-settings');
		var row = $this.closest('tr');
		var loader = row.find('.gamipress-email-settings-loader');
		var value = $this.val();
		var name = $this.attr('name');
		var setting = name.replace('gamipress_email_settings[', '').replace(']', '');

		switch( setting ) {
			case 'all':
				form.find('input[value="' + value + '"]:not([name="' + name + '"])').prop('checked', true);
				break;
			case 'points_types':
			case 'achievement_types':
			case 'rank_types':
				form.find('input[value="' + value + '"][name^="gamipress_email_settings[' + setting + '_"]').prop('checked', true);
				break;
		}

		// Show the "Saving..." text
		loader.find('.gamipress-email-settings-saving').show();
		loader.find('.gamipress-email-settings-saved').hide();
		loader.show();

		$.ajax( {
			url: gamipress.ajaxurl,
			data: {
				action: 'gamipress_save_email_settings',
				nonce: gamipress.nonce,
				setting: setting,
				value: value,
			},
			dataType: 'json',
			success: function( response ) {

				// Show the "Saved!" text
				loader.find('.gamipress-email-settings-saving').hide();
				loader.find('.gamipress-email-settings-saved').show();
				loader.show();

				setTimeout( function() {
					loader.hide();
				}, 2000 );

			}
		} );

	});

} )( jQuery );