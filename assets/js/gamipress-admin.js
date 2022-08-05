(function ( $ ) {

	// Selector Control
	$('.gamipress-selector select').each(function() { gamipress_selector( $(this) ); });

	// Post Selector Control
	$('.gamipress-post-selector select').each(function() { gamipress_post_selector( $(this) ); });

	// User Selector Control
	$('.gamipress-user-selector select').each(function() { gamipress_user_selector( $(this) ); });

	// Initialize on widgets area
	$(document).on('widget-updated widget-added', function(e, widget) {

		// Selector Control
		widget.find( '.gamipress-selector select:not(.select2-hidden-accessible)' ).each(function() {
			gamipress_selector( $(this) );
		});

		// Post Selector Control
		widget.find( '.gamipress-post-selector select:not(.select2-hidden-accessible)' ).each(function() {
			gamipress_post_selector( $(this) );
		});

		// User Selector Control
		widget.find( '.gamipress-user-selector select:not(.select2-hidden-accessible)' ).each(function() {
			gamipress_user_selector( $(this) );
		});

	});

	// Dynamically show/hide achievement meta inputs based on "Earned By" selection
	$("#_gamipress_earned_by").on('change', function() {

		// Define our potentially unnecessary inputs
		var gamipress_points_required = $('#_gamipress_points_required').closest('.cmb-row');
		var gamipress_rank_type_required = $('#_gamipress_rank_type_required').parent().parent();
		var gamipress_rank_required = $('#_gamipress_rank_required').parent().parent();

		// Hide our potentially unnecessary inputs
		gamipress_points_required.hide();
		gamipress_rank_type_required.hide();
		gamipress_rank_required.hide();

		// Determine which inputs we should show
		 if ( $(this).val() === 'points' ) {
			gamipress_points_required.show();
		} else if ( $(this).val() === 'rank' ) {
			gamipress_rank_type_required.show();
			$('#_gamipress_rank_type_required').trigger('change');
			//gamipress_rank_required.show();
		}
	}).trigger('change');

	$('#_gamipress_rank_type_required').on('change', function() {

		var $this = $(this);

		var rank_type = $(this).val();

		if( rank_type === '' ) {
			return;
		}

		$('<span class="spinner is-active" style="float: none;"></span>').insertAfter( $this );

		var gamipress_rank_required = $('#_gamipress_rank_required').parent().parent();
		var gamipress_rank_required_select = $('#_gamipress_rank_required');

		gamipress_rank_required.hide();

		if( rank_type !== '' && $("#_gamipress_earned_by").val() === 'rank' ) {

			$.post(
				ajaxurl,
				{
					action: 'gamipress_get_ranks_options_html',
					nonce: gamipress_admin.nonce,
					post_type: rank_type,
					selected: gamipress_rank_required_select.val(),
				},
				function( response ) {

					$this.next('.spinner').remove();

					gamipress_rank_required_select.html( response );

					// During request user can change the earned by value, so prevent to show if it was changed
					if( $("#_gamipress_earned_by").val() === 'rank' ) {
						gamipress_rank_required.show();
					}
				}
			);

		} else {
			gamipress_rank_required.hide();
		}
	});

	// Dynamically show/hide achievement meta inputs based on "Unlock with Points" checkbox
	$('#_gamipress_unlock_with_points').on('change', function() {
		var target = $('.cmb2-id--gamipress-points-to-unlock');

		if( $(this).prop('checked') ) {
			target.slideDown(250);
		} else {
			target.slideUp(250);
		}
	});

	if( ! $('#_gamipress_unlock_with_points').prop('checked') ) {
		$('.cmb2-id--gamipress-points-to-unlock').hide();
	}

	// Toggle visibility of maximum earners based on show earners checked status
	$('#_gamipress_show_earners').on('change', function() {
		var target = $('.cmb2-id--gamipress-maximum-earners');

		if( $(this).prop('checked') ) {
			target.slideDown(250);
		} else {
			target.slideUp(250);
		}
	});

	if( ! $('#_gamipress_show_earners').prop('checked') ) {
		$('.cmb2-id--gamipress-maximum-earners').hide();
	}

	// Get the current slug for slugs checks
	var current_slug = $('input#post_name').val();

	$('.gamipress-form').on( 'keyup', 'input#post_name', function() {
		var field = $(this);
		var slug = $(this).val();
		var preview = $(this).next('.cmb2-metabox-description').find('.gamipress-post-name');
		var error = gamipress_get_slug_error( slug, current_slug );

		// Update preview element
		if( preview.length )
			preview.text(slug);

		// Delete any existing version of this warning
		$('#slug-warning').remove();

		if( error.length ) {
			// Set input to look like danger
			field.css({'background':'#faa', 'color':'#a00', 'border-color':'#a55' });

			// Output a custom warning
			field.parent().append('<span id="slug-warning" class="cmb2-metabox-description" style="color: #a00;">' + error + '</span>');

			// Disable the save button
			$('input#publish').prop( 'disabled', true );

			return false;
		} else {
			// Restore the input style if there is no error
			field.css({'background':'', 'color':'', 'border-color':''});

			// Re-enable the save button
			if( $('input#publish').prop( 'disabled' ) ) {
				$('input#publish').prop( 'disabled', false );
			}
		}
	});

	$('.gamipress-form input#post_name').trigger( 'keyup' );

	// Edit user rank
    $('body').on('click', '.profile-rank .profile-rank-toggle', function(e) {
        e.preventDefault();

        $(this).slideUp();
        $(this).next('.profile-rank-form-wrapper').slideDown();

        var select = $(this).next('.profile-rank-form-wrapper').find('select');

        // Save current value in a element's data
        select.data( 'current', select.val() );

    });

	// Cancel user rank edit
    $('body').on('click', '.profile-rank .profile-rank-cancel', function(e) {
        e.preventDefault();

        var parent = $(this).parent().parent();
        var select = parent.find('select');

        parent.slideUp();
        parent.prev('.profile-rank-toggle').slideDown();

        // Restore current value (if user changed the input but won't save it)
        select.val( select.data( 'current' ) );

    });

	// Save user rank edit
    $('body').on('click', '.profile-rank .profile-rank-save', function(e) {
        e.preventDefault();

        var $this = $(this);
        var parent = $this.closest('.profile-rank-form-wrapper');
        var select = parent.find('select');
        var rank_id = select.val();
        var current_rank_id = select.data('current');
        var user_id = $('#wpbody-content input[name="user_id"]').val();

        // If no changes made, then toggle form visibility
        if( current_rank_id === rank_id ) {
            parent.slideUp();
            parent.prev('.profile-rank-toggle').slideDown();

            return false;
        }

        // Show loader
        parent.find('.spinner').addClass('is-active');

        $.ajax({
            url: ajaxurl,
            method: 'POST',
            data: {
                action: 'gamipress_profile_update_user_rank',
				nonce: gamipress_admin.nonce,
                rank_id: rank_id,
                user_id: user_id
            },
            success: function( response ) {
                // Hide loader
                parent.find('.spinner').removeClass('is-active');

                // Toggle form visibility
                parent.slideUp();
                parent.prev('.profile-rank-toggle').slideDown();

                // Update the rank preview
                if( response.data !== undefined && response.data.rank !== undefined ) {
                    var rank_wrapper = $this.closest('.profile-rank');

                    rank_wrapper.find('.profile-rank-thumbnail').html( response.data.rank.thumbnail );
                    rank_wrapper.find('.profile-rank-title').html( response.data.rank.post_title );
                }

                // Save current value in a element's data
                select.data( 'current', rank_id );

                // Force user earnings table to refresh
                gamipress_refresh_user_earnings_table();
            }
        });
    });

    // Edit user points
    $('body').on('click', '.profile-points .profile-points-toggle', function(e) {
        e.preventDefault();

        var form = $(this).next('.profile-points-form-wrapper');
		var points_input = form.find('.profile-points-new-balance-input input');

        $(this).slideUp();
		form.slideDown();

        // Save current value in a element's data
		points_input.data( 'current', points_input.val() );
    });

    // On check register points balance movement
	$('body').on('change', '.profile-points .profile-points-register-movement-input input', function(e) {

		var target = $(this).parent().next();

		if( $(this).prop('checked') ) {
			target.slideDown('fast');
		} else {
			target.slideUp('fast');
		}
	});

    // Cancel user points edit
    $('body').on('click', '.profile-points .profile-points-cancel', function(e) {
        e.preventDefault();

        var form = $(this).parent().parent();
		var points_input = form.find('.profile-points-new-balance-input input');
		var register_movement_input = form.find('.profile-points-register-movement-input input');

		form.slideUp();
		form.prev('.profile-points-toggle').slideDown();

        // Restore current value (if user changed the input but won't save it)
		points_input.val( points_input.data('current') );
		register_movement_input.prop( 'checked', false ).trigger('change');

    });

    // Save user points edit
    $('body').on('click', '.profile-points .profile-points-save', function(e) {
        e.preventDefault();

        var $this = $(this);
        var parent = $this.closest('.profile-points-form-wrapper');
		var points_input = parent.find('.profile-points-new-balance-input input');
		var register_movement_input = parent.find('.profile-points-register-movement-input input');
		var earnings_text_input = parent.find('.profile-points-earning-text-input input');
        var points = points_input.val();
        var register_movement = register_movement_input.prop('checked');
        var earnings_text = earnings_text_input.val();
        var current_points = points_input.data('current');
        var points_type = points_input.data('points-type');
        var user_id = $('#wpbody-content input[name="user_id"]').val();

        // If no changes made, then toggle form visibility
        if( current_points === points ) {
            parent.slideUp();
            parent.prev('.profile-points-toggle').slideDown();
            return false;
        }

        // Show loader
        parent.find('.spinner').addClass('is-active');

        $.ajax({
            url: ajaxurl,
            method: 'POST',
            data: {
                action: 'gamipress_profile_update_user_points',
				nonce: gamipress_admin.nonce,
                points: points,
                points_type: points_type,
				register_movement: ( register_movement ? 1 : 0 ),
				earnings_text: earnings_text,
                user_id: user_id
            },
            success: function( response ) {
                // Hide loader
                parent.find('.spinner').removeClass('is-active');

                // Toggle form visibility
                parent.slideUp();
                parent.prev('.profile-points-toggle').slideDown();
				register_movement_input.prop( 'checked', false );

                // Update the points preview
                if( response.data !== undefined && response.data.points !== undefined ) {

                    $this.closest('.profile-points').find('.profile-points-amount').html( response.data.points );

                    // Update the ranks preview
                    if( response.data.ranks !== undefined && response.data.ranks.length ) {

                        // Loop each rank to update their respective preview
                        response.data.ranks.forEach(function(rank) {
                            var rank_wrapper = $('.profile-rank-' + rank.post_type );

                            if( rank_wrapper.length ) {

                                var select = rank_wrapper.find('select');

                                // Update the preview elements
                                rank_wrapper.find('.profile-rank-thumbnail').html( rank.thumbnail );
                                rank_wrapper.find('.profile-rank-title').html( rank.post_title );

                                // Update the input element value
                                select.val( rank.ID );
                                select.data( 'current', rank.ID );

                            }
                        });

                    }

					// Force user earnings table to refresh
					gamipress_refresh_user_earnings_table();
                }

                // Save current value in a element's data
				points_input.data( 'current', points );

                // Force user earnings table to refresh
                gamipress_refresh_user_earnings_table();
            }
        });
    });

    $('body').on('keypress', '.profile-points input', function(e) {
        var keycode = ( e.keyCode ? e.keyCode : e.which );

        // check if key pressed is enter/intro
        if ( keycode === 13 ) {
            e.preventDefault();

            // Trigger click on save button
			$(this).closest('.profile-points').find('.profile-points-save').trigger('click');
        }
    });

    // Award type select
    if( $("#gamipress-award-achievement-type-select, #gamipress-award-requirement-type-select").length ) {

        $("#gamipress-award-achievement-type-select, #gamipress-award-requirement-type-select").on('change', function() {
        	var $this = $(this);
        	var award_options_wrapper = $this.closest('.form-table').next();
        	var post_type = $this.val();

        	// Hide all tables
			award_options_wrapper.children().hide();

			if( ! post_type.length ) {
				return;
			}

			// Show the option selected
			$("#" + post_type).show();

			if( ! $("#" + post_type).data('loaded') ) {

				var action = ( $this.attr('id') === 'gamipress-award-achievement-type-select' ? 'achievement' : 'requirement' );

				$("#" + post_type).data('loaded', true);

				$.ajax({
					url: ajaxurl,
					method: 'POST',
					data: {
						action: 'gamipress_profile_load_award_' + action + '_table',
						nonce: gamipress_admin.nonce,
						user_id: $("input#user_id").val(),
						post_type: post_type,
					},
					success: function(response) {
						$("#" + post_type).html( $(response.data).html() );
					}
				});

			}
        }).trigger('change');

    }

	// User award/revoke ajax from awards table
	$('body').on('click', '#gamipress-awards-options .gamipress-award-achievement, #gamipress-awards-options .gamipress-revoke-achievement', function(e) {
		e.preventDefault();

		var $this = $(this);

		// Bail if already running an ajax request
		if( $this.hasClass('disabled') ) return;

		var sibling_selector = ( $this.hasClass('gamipress-award-achievement') ? '.gamipress-revoke-achievement' : '.gamipress-award-achievement' );

		// Add a custom class to avoid multiples clicks
		$this.addClass('disabled');

		// Also, disable the other link
		$this.parent().find(sibling_selector).addClass('disabled');

		// Add a loader
		$( '<span class="spinner is-active"></span>' ).insertAfter( $this );

        // Make the request
		$.get( $this.attr('href'), function( response ) {

			var $response = $(response);

            // Update user ranks and points
            $('.profile-ranks').html( $response.find('.profile-ranks').html() );
            $('.profile-points').html( $response.find('.profile-points').html() );

			// Reload the awards table
			var post_type = $this.closest('.gamipress-table').attr('id').replace('-table', '');
			var requirements = [ 'points-award', 'points-deduct', 'step', 'rank-requirement' ];

			var action = ( requirements.indexOf( post_type ) === -1 ? 'achievement' : 'requirement' );

			$.ajax({
				url: ajaxurl,
				method: 'POST',
				data: {
					action: 'gamipress_profile_load_award_' + action + '_table',
					nonce: gamipress_admin.nonce,
					user_id: $("input#user_id").val(),
					post_type: post_type,
				},
				success: function(response) {
					$("#" + post_type).html( $(response.data).html() );
				}
			});

            // Force user earnings table to refresh
            gamipress_refresh_user_earnings_table();
		} );
	});

    // User award/revoke ajax from user earnings table
    $('body').on('click', '.gamipress_user_earnings .gamipress-revoke-user-earning', function(e) {

        e.preventDefault();

        var $this = $(this);

        // Bail if already running an ajax request
        if( $this.hasClass('disabled') ) return;

        // Add a custom class to avoid multiples clicks
        $this.addClass('disabled');

        // Add a loader
        $( '<span class="spinner is-active"></span>' ).insertAfter( $this );

        // Make the request
        $.get( $this.attr('href'), function( response ) {

			// If currently on user earnings screen, refresh current page and return
			if( $('body').hasClass('gamipress_page_gamipress_user_earnings') ) {

				// Remove the loader
				$this.next('.spinner').remove();

				// Remove class disabled and add a confirmation text
				$this.removeClass('disabled').text('Revoked successfully!');

				// Refresh current screen
				location.reload();

				// Bail to not refresh component that aren't on user earnings screen
				return;
			}

			var $response = $(response);

			// Update user ranks and points
			$('.profile-ranks').html( $response.find('.profile-ranks').html() );
			$('.profile-points').html( $response.find('.profile-points').html() );

			// Force user earnings table to refresh
			gamipress_refresh_user_earnings_table();

        } );

    });

    // Pattern tags toggle
	$('body').on('click', '.gamipress-pattern-tags-list-toggle', function(e) {
		e.preventDefault();

		var $this = $(this);
		var list = $this.closest('.cmb-td').find('.gamipress-pattern-tags-list');

		if( ! list.hasClass('gamipress-pattern-tags-list-open') ) {
			list.addClass('gamipress-pattern-tags-list-open').slideDown('fast');
			$this.text( $this.data('show-text') );
		} else {
			list.removeClass('gamipress-pattern-tags-list-open').slideUp('fast');
			$this.text( $this.data('hide-text') );
		}
	});

	// Add-ons page
	if( $('.gamipress_page_gamipress_add_ons').length ) {

		// Add-ons tabs
		$('.gamipress_page_gamipress_add_ons .wp-filter a').on('click', function(e) {
			e.preventDefault();

			if( $(this).hasClass('current') ) {
				return;
			}

			var current = $(this).closest('.wp-filter').find('a.current');

			// Toggle plugin cards visibility
			$('.gamipress-plugin-card.' + current.data('target')).hide();

			$('.gamipress-plugin-card.' + $(this).data('target')).fadeIn(250);

			// Toggle current class
			current.removeClass('current');
			$(this).addClass('current');
		});

		// Hide all plugins cards
		$('.gamipress-add-ons .gamipress-plugin-card:not(.' + $('.gamipress_page_gamipress_add_ons .wp-filter a.current').data('target') + ')').hide();

		// Trigger click on first tab
		$('.gamipress_page_gamipress_add_ons .wp-filter a.current').trigger('click');

	}

	// Hide review notice
    $('body').on('click', '.gamipress-hide-review-notice', function(e) {

        e.preventDefault();

        $.ajax({
			url: ajaxurl,
			data: {
				action: 'gamipress_hide_review_notice',
				nonce: gamipress_admin.nonce,
			},
			success: function(response) {
				// Hide the notice on success
				$('.gamipress-review-notice').slideUp('fast');
			}
		});

    });

	// Target blank on featured menu links
	$('#adminmenu .gamipress-admin-menu-badge, '
		+ '#wp-admin-bar-gamipress .gamipress-admin-menu-badge').each( function() {
			var parent = $(this).closest('a');

			parent.attr('target', '_blank');
	} );

	// Auto initialize upgrade if user reloads the page during an upgrade
	if( $('#gamipress-upgrade-notice').find('.gamipress-upgrade-progress[data-running-upgrade]').length ) {
		gamipress_start_upgrade( $('#gamipress-upgrade-notice').find('.gamipress-upgrade-progress[data-running-upgrade]').data('running-upgrade') );
	}

})( jQuery );

var gamipress_current_upgrade_info;
var gamipress_current_upgrade_progress;
var gamipress_current_upgrade_cancelled = false;

// Start upgrade
function gamipress_start_upgrade( version ) {

	var $ = $ || jQuery;
	version = version.replace('.', '').replace('.', '').replace('.', '');

	$('#gamipress-upgrade-notice').html(
		'<p>Upgrading GamiPress database... <span></span></p>'
		+ '<div class="gamipress-upgrade-progress"><div class="gamipress-upgrade-progress-bar" style="width: 0%;"></div></div>'
		+ '<p style="display: none;">'
			+ '<a id="gamipress-cancel-upgrade" href="#" class="button">Cancel Upgrade</a>'
		+ '</p>'
	);

	$.ajax({
		url: ajaxurl,
		data: {
			action: 'gamipress_' + version + '_upgrade_info',
			nonce: gamipress_admin.nonce,
		},
		success: function( response ) {

			// Upgrade done!
			if( response.data.upgraded !== undefined && response.data.upgraded ) {
				$('#gamipress-upgrade-notice').html('<p>Upgrade has been already completed.</p><div class="gamipress-upgrade-progress"><div class="gamipress-upgrade-progress-bar" style="width: 100%;"></div></div>');
				return;
			}

			// Update progress vars
			gamipress_current_upgrade_info = response.data.total;
			gamipress_current_upgrade_progress = 0;

            // Show a visual text with remaining entries
            $('#gamipress-upgrade-notice p:first span').html( gamipress_current_upgrade_info - gamipress_current_upgrade_progress + ' remaining entries' );

            // Add a click event on the upgrade cancel button
            // Note: Function is placed here because clicking fast on start upgrade could provoke cancelling it at start
            $('body').on('click', '#gamipress-cancel-upgrade', function(e) {
                e.preventDefault();

                $('#gamipress-upgrade-notice').html('<p>Cancelling upgrade...</p>');

                gamipress_current_upgrade_cancelled = true;

                gamipress_stop_upgrade( version, true );
            });

            // Show the cancel upgrade button
            $('#gamipress-cancel-upgrade').parent().show();

			gamipress_run_upgrade( version );

		},
		error: function( response ) {
			gamipress_stop_upgrade( version );
			$('#gamipress-upgrade-notice').html('<p class="error">Upgrading process failed.</p>');
		}
	});

}

// Refresh the user earnings table (located in user profile screen)
function gamipress_refresh_user_earnings_table() {

	var $ = $ || jQuery;

    // Check if table exists
    var table = $('.ct-ajax-list-table[data-object="gamipress_user_earnings"]');

    if( table.length )
        ct_ajax_list_table_paginate_table( table, table.find('input#current-page-selector').val() );

}

// Run upgrade
function gamipress_run_upgrade( version ) {

	var $ = $ || jQuery;

    if( gamipress_current_upgrade_cancelled ) {
        return;
    }

	version = version.replace('.', '').replace('.', '').replace('.', '');

	$.ajax({
		url: ajaxurl,
		data: {
			action: 'gamipress_process_' + version + '_upgrade',
			nonce: gamipress_admin.nonce,
			current: gamipress_current_upgrade_progress,
		},
		success: function( response ) {

			// Upgrade done!
			if( response.data.upgraded !== undefined && response.data.upgraded ) {
				$('#gamipress-upgrade-notice').html('<p>Upgrading process finished successfully.</p><div class="gamipress-upgrade-progress"><div class="gamipress-upgrade-progress-bar" style="width: 100%;"></div></div>');
				return;
			}

			gamipress_current_upgrade_progress = response.data.current;

			// Upgraded successfully
			if( gamipress_current_upgrade_progress >= gamipress_current_upgrade_info ) {
				$('#gamipress-upgrade-notice').html('<p>Upgrading process finished successfully.</p><div class="gamipress-upgrade-progress"><div class="gamipress-upgrade-progress-bar" style="width: 100%;"></div></div>');
				return;
			}

            // Show a visual text with remaining entries
            $('#gamipress-upgrade-notice p:first span').html( gamipress_current_upgrade_info - gamipress_current_upgrade_progress + ' remaining entries' );

			// Update progress bar width
			$('#gamipress-upgrade-notice .gamipress-upgrade-progress .gamipress-upgrade-progress-bar').attr('style', 'width: ' + ( ( gamipress_current_upgrade_progress / gamipress_current_upgrade_info ) * 100 ) + '%')

			// Give to the server 3 seconds until the next upgrade run
			setTimeout( function() {
				gamipress_run_upgrade( version );
			}, 3000 );

		},
		error: function( response ) {
			gamipress_stop_upgrade( version );
			$('#gamipress-upgrade-notice').html('<p class="error">Upgrading process failed.</p>');
		}
	});

}

// Stop upgrade
function gamipress_stop_upgrade( version, show_result ) {

    if( show_result === undefined ) {
        show_result = false;
    }

	var $ = $ || jQuery;
	version = version.replace('.', '').replace('.', '').replace('.', '');

	$.ajax({
		url: ajaxurl,
		data: {
			action: 'gamipress_stop_process_' + version + '_upgrade',
			nonce: gamipress_admin.nonce,
		},
        success: function( response ) {
            if( show_result === true ) {
                $('#gamipress-upgrade-notice').html('<p class="error">Upgrade cancelled.</p>');
            }
        },
        error: function( response ) {
            if( show_result === true ) {
                $('#gamipress-upgrade-notice').html('<p class="error">Upgrade cancellation failed.</p>');
            }
        }
	});

}
