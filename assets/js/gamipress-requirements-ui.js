(function($) {
    // Hide our Triggers metabox if unnecessary
    $("#_gamipress_earned_by").change( function() {
        if ( 'triggers' == $(this).val() )
            $('#gamipress-requirements-ui').show();
        else
            $('#gamipress-requirements-ui').hide();
    }).change();

    // Make our Triggers list sortable
    $("#requirements-list").sortable({

        // When the list order is updated
        update : function () {

            // Loop through each element
            $('#requirements-list li').each(function( index, value ) {

                // Write it's current position to our hidden input value
                $(this).children('input[name="order"]').val( index );

            });

        }
    });

    // Listen for our change to our trigger type selectors
    $('#requirements-list').on( 'change', '.select-trigger-type', function() {

        if( ! $(this).hasClass('select2-hidden-accessible') ) {
            $(this).select2({ theme: 'default gamipress-select2' });
        }

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

    // Listen for a change to our achievement type selectors
    $('#requirements-list').on( 'change', '.select-achievement-type', function() {

        // Setup our necessary variables
        var achievement_selector = $(this);
        var achievement_type     = achievement_selector.val();
        var requirement_id       = achievement_selector.parent('li').attr('data-requirement-id');
        var requirement_type     = achievement_selector.siblings('input[name="requirement_type"]').val();
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
                    action: 'gamipress_requirement_achievement_post',
                    requirement_id: requirement_id,
                    requirement_type: requirement_type,
                    achievement_type: achievement_type,
                    excluded_posts: excluded_posts
                },
                function( response ) {
                    achievement_selector.siblings('select.select-achievement-post').html( response );
                    achievement_selector.siblings('select.select-achievement-post').show();
                }
            );

        } else {
            // Otherwise, keep our post selector hidden

            achievement_selector.siblings('.select-achievement-post').hide();
            achievement_selector.siblings('.select-post').hide();
            achievement_selector.siblings('.select-post.select2-hidden-accessible').next().hide();

            if ( gamipress_requirements_ui.specific_activity_triggers[trigger_type] !== undefined ) {
                achievement_selector.siblings('.select-post').show().data( 'post-type', gamipress_requirements_ui.specific_activity_triggers[trigger_type].join(',') );
                achievement_selector.siblings('.select-post.select2-hidden-accessible')
                    .val('').change()   // Reset value
                    .next().show();     // Show

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
                    placeholder: gamipress_requirements_ui.post_placeholder,
                    allowClear: true,
                    multiple: false
                });
            }
        }
    });

    // Limit inputs
    $('#requirements-list').on( 'change', '.limit-type', function() {
        var limit_type_selector = $(this);

        if( limit_type_selector.val() === 'unlimited' ) {
            limit_type_selector.siblings('.limit').hide();
        } else {
            limit_type_selector.siblings('.limit').show();
        }
    });

    // Trigger a change for our limit type to determine if limit should show
    $( '.limit-type' ).change();
})(jQuery);

// Add a points award
function gamipress_add_requirement( post_id ) {
    jQuery( '.requirements-spinner' ).addClass('is-active');

    jQuery.post(
        ajaxurl,
        {
            action: 'gamipress_add_requirement',
            post_id: post_id
        },
        function( response ) {
            jQuery( response ).appendTo( '#requirements-list' );

            // Dynamically add the menu order for the new points award to be one higher than the last in line
            var new_requirement_menu_order = Number( jQuery( '#requirements-list li.requirement-row' ).eq( -2 ).children( 'input[name="order"]' ).val() ) + 1;
            jQuery( '#requirements-list li.requirement-row:last' ).children( 'input[name="order"]' ).val( new_requirement_menu_order );

            // Trigger a change for the new trigger type <select> element
            jQuery( '#requirements-list li.requirement-row:last' ).children( '.select-trigger-type' ).change();
            jQuery( '#requirements-list li.requirement-row:last' ).children( '.limit-type' ).change();

            // Hide the spinner
            jQuery( '.requirements-spinner' ).removeClass('is-active');
        }
    );
}

// Delete a points award
function gamipress_delete_requirement( requirement_id ) {
    // Show the spinner
    //jQuery( '.requirements-spinner' ).addClass('is-active');
    jQuery( '.requirement-' + requirement_id ).hide();

    jQuery.post(
        ajaxurl,
        {
            action: 'gamipress_delete_requirement',
            requirement_id: requirement_id
        },
        function( response ) {
            jQuery( '.requirement-' + requirement_id ).remove();

            // Hide the spinner
            //jQuery( '.requirements-spinner' ).removeClass('is-active');
        }
    );
}

// Update all points awards
function gamipress_update_requirements() {

    jQuery( '.requirements-spinner' ).addClass('is-active');

    var requirement_data = {
        action: 'gamipress_update_requirements',
        requirements: []
    };

    // Loop through each points award and collect its data
    jQuery( '.requirement-row' ).each( function() {

        // Cache our points award object
        var requirement = jQuery(this);
        var trigger_type = requirement.find( '.select-trigger-type' ).val();

        // Setup our points award object
        var requirement_details = {
            requirement_id   : requirement.find( 'input[name="requirement_id"]').val(),
            requirement_type : requirement.find( 'input[name="requirement_type"]').val(),
            order            : requirement.find( 'input[name="order"]' ).val(),
            required_count   : requirement.find( '.required-count' ).val(),
            limit            : requirement.find( '.limit' ).val(),
            limit_type       : requirement.find( '.limit-type' ).val(),
            trigger_type     : trigger_type,
            achievement_type : requirement.find( '.select-achievement-type' ).val(),
            achievement_post : ( gamipress_requirements_ui.specific_activity_triggers[trigger_type] !== undefined ? requirement.find( '.select-post' ).val() : requirement.find( 'select.select-achievement-post' ).val() ),
            title            : requirement.find( '.requirement-title .title' ).val()
        };

        if( requirement_details.requirement_type === 'points-award' ) {
            requirement_details.points = requirement.find( '.points' ).val();
            requirement_details.points_type = requirement.find( 'input[name="points_type"]' ).val();
        }

        // Allow external functions to add their own data to the array
        requirement.trigger( 'update_requirement_data', [ requirement_details, requirement ] );

        // Add our relevant data to the array
        requirement_data.requirements.push( requirement_details );

    });

    jQuery.post(
        ajaxurl,
        requirement_data,
        function( response ) {
            // Parse response
            var titles = jQuery.parseJSON( response );

            // Update each points award titles
            jQuery.each( titles, function( id, value ) {
                jQuery('#requirement-' + id + '-title').val(value);
            });

            // Hide the spinner
            jQuery( '.requirements-spinner' ).removeClass('is-active');
        }
    );
}
