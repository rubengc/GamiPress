(function($) {
    // Hide our Triggers metabox if unnecessary
    $("#_gamipress_earned_by").change( function() {
        if ( 'triggers' == $(this).val() )
            $('#gamipress_points_awards_ui').show();
        else
            $('#gamipress_points_awards_ui').hide();
    }).change();

    // Make our Triggers list sortable
    $("#points_awards_list").sortable({

        // When the list order is updated
        update : function () {

            // Loop through each element
            $('#points_awards_list li').each(function( index, value ) {

                // Write it's current position to our hidden input value
                $(this).children('input[name="order"]').val( index );

            });

        }
    });

    // Listen for our change to our trigger type selectors
    $('#points_awards_list').on( 'change', '.select-trigger-type', function() {

        // Grab our selected trigger type and achievement selector
        var trigger_type = $(this).val();
        var achievement_selector = $(this).siblings('.select-achievement-type');

        // If we're working with achievements, show the achievement selecter (otherwise, hide it)
        if ( 'any-achievement' == trigger_type || 'all-achievements' == trigger_type || 'specific-achievement' == trigger_type ) {
            achievement_selector.show();
        } else {
            achievement_selector.hide();
        }

        // Trigger a change for our achievement type post selector to determine if it should show
        achievement_selector.change();

    });
    // Trigger a change for our trigger type post selector to determine if it should show
    $( '.select-trigger-type' ).change();

    // Listen for a change to our achivement type selectors
    $('#points_awards_list').on( 'change', '.select-achievement-type', function() {

        // Setup our necessary variables
        var achievement_selector = $(this);
        var achievement_type     = achievement_selector.val();
        var points_award_id      = achievement_selector.parent('li').attr('data-points-award-id');
        var excluded_posts       = [achievement_selector.siblings('input[name="post_id"]').val()];
        var trigger_type         = achievement_selector.siblings('.select-trigger-type').val();

        // If we've selected a *specific* achievement type, show our post selector
        // and populate it w/ the corresponding achievement posts
        if ( '' !== achievement_type && 'specific-achievement' == trigger_type ) {
            achievement_selector.siblings('.select-post').hide();
            achievement_selector.siblings('.select-post.select2-hidden-accessible').next().hide();

            $.post(
                ajaxurl,
                {
                    action: 'post_select_ajax',
                    achievement_type: achievement_type,
                    points_award_id: points_award_id,
                    excluded_posts: excluded_posts
                },
                function( response ) {
                    achievement_selector.siblings('select.select-achievement-post').html( response );
                    achievement_selector.siblings('select.select-achievement-post').show();
                }
            );

            // Otherwise, keep our post selector hidden
        } else {
            achievement_selector.siblings('.select-achievement-post').hide();
            achievement_selector.siblings('.select-post').hide();
            achievement_selector.siblings('.select-post.select2-hidden-accessible').next().hide();

            if ( gamipress_points_awards_ui.specific_activity_triggers[trigger_type] !== undefined ) {
                achievement_selector.siblings('.select-post').show().data( 'post-type', gamipress_points_awards_ui.specific_activity_triggers[trigger_type].join(',') );
                achievement_selector.siblings('.select-post.select2-hidden-accessible').next().show();

                achievement_selector.siblings( '.select-post:not(.select2-hidden-accessible)' ).select2({
                    ajax: {
                        url: ajaxurl,
                        dataType: 'json',
                        delay: 250,
                        type: 'POST',
                        data: function( params ) {
                            return {
                                q: params.term,
                                action: 'gamipress_get_posts',
                                post_type: $(this).data('post-type').split(',')
                            };
                        },
                        processResults: function( results, page ) {
                            if( results === null ) {
                                return { results: [] };
                            }

                            var formatted_results = [];

                            results.data.forEach(function(item) {
                                formatted_results.push({
                                    id: item.ID,
                                    text: item.post_title,
                                });
                            });

                            return { results: formatted_results };
                        }
                    },
                    theme: 'default gamipress-select2',
                    placeholder: gamipress_points_awards_ui.post_placeholder,
                    allowClear: true,
                    multiple: false
                });
            }
        }
    });

    // Limit inputs
    $('#points_awards_list').on( 'change', '.limit-type', function() {
        var limit_type_selector = $(this);

        if( limit_type_selector.val() === 'unlimited' ) {
            limit_type_selector.siblings('.limit').hide();
        } else {
            limit_type_selector.siblings('.limit').show();
        }
    });

    // Trigger a change for our achievement type post selector to determine if it should show
    $( '.select-achievement-type' ).change();
    $( '.limit-type' ).change();
})(jQuery);

// Add a points award
function gamipress_add_new_points_award( post_id ) {
    jQuery.post(
        ajaxurl,
        {
            action: 'add_points_award',
            post_id: post_id
        },
        function( response ) {
            jQuery( response ).appendTo( '#points_awards_list' );

            // Dynamically add the menu order for the new points award to be one higher than the last in line
            var new_points_award_menu_order = Number( jQuery( '#points_awards_list li.points-award-row' ).eq( -2 ).children( 'input[name="order"]' ).val() ) + 1;
            jQuery( '#points_awards_list li.points-award-row:last' ).children( 'input[name="order"]' ).val( new_points_award_menu_order );

            // Trigger a change for the new trigger type <select> element
            jQuery( '#points_awards_list li.points-award-row:last' ).children( '.select-trigger-type' ).change();
            jQuery( '#points_awards_list li.points-award-row:last' ).children( '.limit-type' ).change();
        }
    );
}

// Delete a points award
function gamipress_delete_points_award( points_award_id ) {
    jQuery.post(
        ajaxurl,
        {
            action: 'delete_points_award',
            points_award_id: points_award_id
        },
        function( response ) {
            jQuery( '.points-award-' + points_award_id ).remove();
        }
    );
}

// Update all points awards
function gamipress_update_points_awards() {

    jQuery( '.save-points-awards-spinner' ).show();
    points_award_data = {
        action: 'update_points_awards',
        points_awards: []
    };

    // Loop through each points award and collect its data
    jQuery( '.points-award-row' ).each( function() {

        // Cache our points award object
        var points_award = jQuery(this);
        var trigger_type = points_award.find( '.select-trigger-type' ).val();

        // Setup our points award object
        var points_award_details = {
            points_award_id  : points_award.attr( 'data-points-award-id' ),
            order            : points_award.find( 'input[name="order"]' ).val(),
            required_count   : points_award.find( '.required-count' ).val(),
            points           : points_award.find( '.points' ).val(),
            points_type      : points_award.find( 'input[name="points_type"]' ).val(),
            limit            : points_award.find( '.limit' ).val(),
            limit_type       : points_award.find( '.limit-type' ).val(),
            trigger_type     : trigger_type,
            achievement_type : points_award.find( '.select-achievement-type' ).val(),
            achievement_post : ( gamipress_points_awards_ui.specific_activity_triggers[trigger_type] !== undefined ? points_award.find( '.select-post' ).val() : points_award.find( 'select.select-achievement-post' ).val() ),
            title            : points_award.find( '.points-award-title .title' ).val()
        };

        // Allow external functions to add their own data to the array
        points_award.trigger( 'update_points_award_data', [ points_award_details, points_award ] );

        // Add our relevant data to the array
        points_award_data.points_awards.push( points_award_details );

    });

    jQuery.post(
        ajaxurl,
        points_award_data,
        function( response ) {
            // Parse response
            var titles = jQuery.parseJSON( response );

            // Update each points award titles
            jQuery.each( titles, function( id, value ) {
                jQuery('#points-award-' + id + '-title').val(value);
            });

            // Hide our save spinner
            jQuery( '.save-points-awards-spinner' ).hide();
        }
    );
}
