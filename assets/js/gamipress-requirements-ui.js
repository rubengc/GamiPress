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
                $(this).find('input[name="order"]').val( index );

            });

        }
    });

    // Listen for our change to our trigger type selectors
    $('#requirements-list').on( 'change', '.select-trigger-type', function() {

        // Initialize select 2 on select trigger type
        if( ! $(this).hasClass('select2-hidden-accessible') ) {
            $(this).select2({ theme: 'default gamipress-select2' });
        }

        // Grab our selected trigger type and achievement selector
        var trigger_type = $(this).val();

        // Common selectors for points and rank trigger types
        var count = $(this).siblings('.required-count');
        var count_text = $(this).siblings('.required-count-text');
        var limit_text = $(this).siblings('.limit-text');
        var limit = $(this).siblings('.limit');
        var limit_type = $(this).siblings('.limit-type');

        if ( trigger_type === 'earn-points' || trigger_type === 'earn-rank' ) {
            // Hide limit fields
            count.hide();
            count_text.hide();
            limit_text.hide();
            limit.hide();
            limit_type.hide();
        } else {
            // Show limit fields
            count.show();
            count_text.show();
            limit_text.show();
            if( limit_type.val() !== 'unlimited' ) { limit.show(); }
            limit_type.show();
        }

        // Required points
        var points_selector_required = $(this).siblings('.points-required');
        var points_type_selector_required = $(this).siblings('.select-points-type-required');

        if ( trigger_type === 'earn-points' ) {
            // Show required points fields
            points_selector_required.show();
            points_type_selector_required.show();
        } else {
            // Hide required points fields
            points_selector_required.hide();
            points_type_selector_required.hide();
        }

        // Required rank
        var rank_type_selector_required = $(this).siblings('.select-rank-type-required');
        var rank_selector_required = $(this).siblings('.select-rank-required');

        if ( trigger_type === 'earn-rank' ) {
            // Show required rank fields
            rank_type_selector_required.show();
            //rank_selector_required.show();

            rank_type_selector_required.change();
        } else {
            // Hide required rank fields
            rank_type_selector_required.hide();
            rank_selector_required.hide();
        }

        // Achievement type
        var achievement_type_selector = $(this).siblings('.select-achievement-type');
        var achievement_post_selector = $(this).siblings('.select-achievement-post');

        // If we're working with achievements, show the achievement type selector (otherwise, hide it)
        if ( trigger_type === 'any-achievement' || trigger_type === 'all-achievements' || trigger_type === 'specific-achievement') {
            achievement_type_selector.show();

            // Trigger a change for our achievement type post selector to determine if it should show
            achievement_type_selector.change();
        } else {
            // Hide achievement type and post selector
            achievement_type_selector.hide();
            achievement_post_selector.hide();
        }

        var post_selector = $(this).siblings('.select-post');

        // Lets to check if there is a specific activity trigger
        if ( gamipress_requirements_ui.specific_activity_triggers[trigger_type] !== undefined ) {
            // Show select post
            post_selector
                .show()
                .data( 'trigger-type', trigger_type )
                .data( 'post-type', gamipress_requirements_ui.specific_activity_triggers[trigger_type].join(',') )
            ;

            // Check if post selector Select2 has been initialized
            if( post_selector.hasClass('select2-hidden-accessible') ) {
                post_selector
                    .val('').change()   // Reset value
                    .next().show();     // Show Select2 container
            } else {
                post_selector.select2({
                    ajax: {
                        url: ajaxurl,
                        dataType: 'json',
                        delay: 250,
                        type: 'POST',
                        data: function( params ) {
                            return {
                                q: params.term,
                                action: 'gamipress_get_posts',
                                post_type: $(this).data('post-type').split(','),
                                trigger_type: $(this).data('trigger-type'),
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
        } else {
            // Hide select post
            post_selector.hide();

            if( post_selector.hasClass('select2-hidden-accessible') ) {
                post_selector.next().hide(); // Hide select2 container
            }
        }

    });

    // Trigger a change for our trigger type post selector to determine if it should show
    $( '.select-trigger-type' ).change();

    // Listen for a change to our achievement type selectors
    $('#requirements-list').on( 'change', '.select-achievement-type', function() {

        // Setup our necessary variables
        var $this                = $(this);
        var achievement_post     = $this.siblings('.select-achievement-post');
        var achievement_type     = $this.val();
        var requirement_id       = $this.parent('li').attr('data-requirement-id');
        var requirement_type     = $this.siblings('input[name="requirement_type"]').val();
        var excluded_posts       = [$this.siblings('input[name="post_id"]').val()];
        var trigger_type         = $this.siblings('.select-trigger-type').val();

        // If we've selected a *specific* achievement type, show our post selector and populate it w/ the corresponding achievement posts
        if ( '' !== achievement_type && 'specific-achievement' === trigger_type ) {
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
                    achievement_post.html( response );
                    achievement_post.show();
                }
            );
        } else {
            achievement_post.hide();
        }
    });

    // Listen for a change to our rank type selectors
    $('#requirements-list').on( 'change', '.select-rank-type-required', function() {

        // Setup our necessary variables
        var $this                = $(this);
        var rank_selector        = $this.siblings('.select-rank-required');
        var rank_type            = $this.val();
        var requirement_id       = $this.parent('li').attr('data-requirement-id');
        var trigger_type         = $this.siblings('.select-trigger-type').val();

        // If we've selected a *specific* achievement type, show our post selector and populate it w/ the corresponding achievement posts
        if ( '' !== rank_type && 'earn-rank' === trigger_type ) {

            rank_selector.hide();
            $('<span class="spinner is-active" style="float: none;"></span>').insertAfter( $this );

            $.post(
                ajaxurl,
                {
                    action: 'gamipress_get_ranks_options_html',
                    requirement_id: requirement_id,
                    post_type: rank_type
                },
                function( response ) {

                    $this.next('.spinner').remove();

                    rank_selector.html( response );
                    rank_selector.show();
                }
            );
        } else {
            rank_selector.hide();
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

// Add a requirement
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
            var new_requirement_menu_order = Number( jQuery( '#requirements-list li.requirement-row' ).eq( -2 ).find( 'input[name="order"]' ).val() ) + 1;
            jQuery( '#requirements-list li.requirement-row:last' ).find( 'input[name="order"]' ).val( new_requirement_menu_order );

            // Trigger a change for the new trigger type <select> element
            jQuery( '#requirements-list li.requirement-row:last' ).find( '.select-trigger-type' ).change();
            jQuery( '#requirements-list li.requirement-row:last' ).find( '.limit-type' ).change();

            // Hide the spinner
            jQuery( '.requirements-spinner' ).removeClass('is-active');
        }
    );
}

// Delete a requirement
function gamipress_delete_requirement( requirement_id ) {

    jQuery( '.requirement-' + requirement_id ).hide();

    jQuery.post(
        ajaxurl,
        {
            action: 'gamipress_delete_requirement',
            requirement_id: requirement_id
        },
        function( response ) {
            jQuery( '.requirement-' + requirement_id ).remove();
        }
    );
}

// Update all requirements
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
            requirement_id          : requirement.find( 'input[name="requirement_id"]').val(),
            requirement_type        : requirement.find( 'input[name="requirement_type"]').val(),
            order                   : requirement.find( 'input[name="order"]' ).val(),
            points_required         : requirement.find( '.points-required' ).val(),
            points_type_required    : requirement.find( '.select-points-type-required' ).val(),
            rank_type_required      : requirement.find( '.select-rank-type-required' ).val(),
            rank_required           : requirement.find( '.select-rank-required' ).val(),
            required_count          : requirement.find( '.required-count' ).val(),
            limit                   : requirement.find( '.limit' ).val(),
            limit_type              : requirement.find( '.limit-type' ).val(),
            trigger_type            : trigger_type,
            achievement_type        : requirement.find( '.select-achievement-type' ).val(),
            achievement_post        : ( gamipress_requirements_ui.specific_activity_triggers[trigger_type] !== undefined ? requirement.find( '.select-post' ).val() : requirement.find( 'select.select-achievement-post' ).val() ),
            title                   : requirement.find( '.requirement-title .title' ).val()
        };

        if( requirement_details.requirement_type === 'points-award' ) {
            requirement_details.points = requirement.find( '.points' ).val();
            requirement_details.points_type = requirement.find( 'input[name="points_type"]' ).val();
            requirement_details.maximum_earnings = requirement.find( '.maximum-earnings' ).val();
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
