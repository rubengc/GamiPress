(function($) {
    // Hide requirements meta box if unnecessary
    $("#_gamipress_earned_by").change( function() {
        if ( 'triggers' == $(this).val() )
            $('#gamipress-requirements-ui').show();
        else
            $('#gamipress-requirements-ui').hide();
    }).change();

    // Make requirements list sortable
    $(".requirements-list").sortable({

        // When the list order is updated
        update : function ( e, ui ) {

            // Loop through each element
            $(e.target).find('li').each(function( index, value ) {

                // Write it's current position to our hidden input value
                $(this).find('input[name="order"]').val( index );

            });

        }
    });

    // On change sequential requirements, add order display on all requirements
    $("#_gamipress_sequential").change( function() {

        $('.requirements-list .requirement-header-title .requirement-order').remove();

        if( $(this).prop('checked') ) {
            var index = 1;

            $('.requirements-list .requirement-published .requirement-header-title').each(function() {
                $(this).prepend('<span class="requirement-order">' + index + ' -</span>');

                index++;
            });
        }

    }).change();

    // Change status action
    $('.requirements-list').on( 'change', '.requirement-action.requirement-action-change-status input', function() {

        var $this = $(this);
        var requirement = $this.closest('.requirement-row');

        // Enable/Disable all inputs
        requirement.find('input:not([id="' + $this.attr('id') + '"]), select, textarea').prop( 'disabled', ! $this.prop('checked') );

        if( $this.prop('checked') ) {
            // Remove a custom class to the requirement
            requirement.removeClass('requirement-disabled');
            requirement.addClass('requirement-published');

            // Change action title
            $this.attr('title', $this.data('enabled-title'));
        } else {
            // Add a custom class to the requirement
            requirement.addClass('requirement-disabled');
            requirement.removeClass('requirement-published');

            // Change action title
            $this.attr('title', $this.data('disabled-title'));
        }

        // Trigger change event on sequential input to update the order again
        $("#_gamipress_sequential").change();
    });

    // Duplicate action
    $('.requirements-list').on( 'click', '.requirement-action.requirement-action-duplicate', function(e) {
        e.preventDefault();

        // Bail if already clicked
        if( $(this).hasClass('requirement-action-active') ) {
            return;
        }

        // Add a custom class to meet that has been clicked
        $(this).addClass('requirement-action-active');

        gamipress_duplicate_requirement( this, $(this).closest('.requirement-row').attr('data-requirement-id') );
    });

    // Delete action
    $('.requirements-list').on( 'click', '.requirement-action.requirement-action-delete', function(e) {
        e.preventDefault();

        gamipress_delete_requirement( this, $(this).closest('.requirement-row').attr('data-requirement-id') );
    });

    // On change requirement title, also update requirement header title
    $('.requirements-list').on( 'change keyup', '.requirement-title input', function() {
        $(this).closest('.requirement-row').find('.requirement-header-title strong').html($(this).val());
    });

    // Listen for our change to our trigger type selectors
    $('.requirements-list').on( 'change', '.select-trigger-type', function() {

        // Initialize select 2 on select trigger type
        if( ! $(this).hasClass('select2-hidden-accessible') ) {
            $(this).select2({ theme: 'default gamipress-select2' });
        }

        // Grab our selected trigger type and achievement selector
        var trigger_type = $(this).val();

        // Common selectors for points and rank trigger types
        var count = $(this).siblings('.count');
        var count_text = $(this).siblings('.count-text');
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

        if ( trigger_type === 'earn-points' || trigger_type === 'gamipress_expend_points' ) {
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
                                page: params.page || 1,
                                action: 'gamipress_get_posts',
                                post_type: $(this).data('post-type').split(','),
                                trigger_type: $(this).data('trigger-type'),
                            };
                        },
                        processResults: gamipress_select2_posts_process_results
                    },
                    escapeMarkup: function ( markup ) { return markup; }, // Let our custom formatter work
                    templateResult: gamipress_select2_posts_template_result,
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

    // Listen for a change to our achievement type selectors
    $('.requirements-list').on( 'change', '.select-achievement-type', function() {

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
            // Add the loader
            $('<span class="achievement-type-spinner spinner is-active" style="float: none; margin: 0 2px 0 4px;"></span>').insertAfter($this);

            achievement_post.hide();

            $.post(
                ajaxurl,
                {
                    action: 'gamipress_get_achievements_options_html',
                    requirement_id: requirement_id,
                    requirement_type: requirement_type,
                    achievement_type: achievement_type,
                    excluded_posts: excluded_posts
                },
                function( response ) {

                    // Remove the loader
                    $this.next('.achievement-type-spinner').remove();

                    // Add the new options and show the input again
                    achievement_post.html( response );
                    achievement_post.show();
                }
            );
        } else {
            achievement_post.hide();
        }
    });

    // Listen for a change to our rank type selectors
    $('.requirements-list').on( 'change', '.select-rank-type-required', function() {

        // Setup our necessary variables
        var $this                = $(this);
        var rank_selector        = $this.siblings('.select-rank-required');
        var rank_type            = $this.val();
        var requirement_id       = $this.parent('li').attr('data-requirement-id');
        var trigger_type         = $this.siblings('.select-trigger-type').val();

        // If we've selected a *specific* achievement type, show our post selector and populate it w/ the corresponding achievement posts
        if ( '' !== rank_type && 'earn-rank' === trigger_type ) {

            // Add the loader
            $('<span class="rank-type-spinner spinner is-active" style="float: none; margin: 0 2px 0 4px;"></span>').insertAfter( $this );

            rank_selector.hide();

            $.post(
                ajaxurl,
                {
                    action: 'gamipress_get_ranks_options_html',
                    requirement_id: requirement_id,
                    post_type: rank_type
                },
                function( response ) {

                    // Remove the loader
                    $this.next('.rank-type-spinner').remove();

                    // Add the new options and show the input again
                    rank_selector.html( response );
                    rank_selector.show();
                }
            );
        } else {
            rank_selector.hide();
        }
    });

    // Limit inputs
    $('.requirements-list').on( 'change', '.limit-type', function() {
        var limit_type_selector = $(this);

        if( limit_type_selector.val() === 'unlimited' ) {
            limit_type_selector.siblings('.limit').hide();
        } else {
            limit_type_selector.siblings('.limit').show();
        }
    });

    // Trigger a change for our trigger type post selector to determine if it should show
    $( '.select-trigger-type' ).change();

    // Trigger a change for our limit type to determine if limit should show
    $( '.limit-type' ).change();

    // Trigger a change for our change status input
    $( '.requirement-action-change-status input' ).change();

    // Add a custom data with current fields values to check their changes
    $('.requirements-list input, .requirements-list select, .requirements-list textarea').each( function() {
        $(this).data('unsaved-value', $(this).val());
    });

    // Check if any field has change their values
    $('.requirements-list').on( 'change', 'input, select, textarea', function() {

        var row = $(this).closest('.requirement-row');
        var has_unsaved_changes = false;

        // Check if any field has change his value
        row.find('input, select, textarea').each( function() {
            if( $(this).val() !== $(this).data('unsaved-value') ) {
                has_unsaved_changes = true;
            }
        });

        // If not has unsaved changes, remove warning and return
        if( ! has_unsaved_changes ) {
            row.find('.requirement-header-title .requirement-unsaved-changes').remove();
            return;
        }

        // If has unsaved changes and not the waring, add it
        if( has_unsaved_changes && ! row.find('.requirement-header-title .requirement-unsaved-changes').length ) {
            row.find('.requirement-header-title').append('<span class="requirement-unsaved-changes dashicons dashicons-warning" title="Unsaved Changes"></span>');
        }

    });
})(jQuery);

/**
 * Add a requirement
 *
 * @since 1.0.0
 *
 * @param element
 * @param post_id
 * @param requirement_type
 */
function gamipress_add_requirement( element, post_id, requirement_type ) {

    var requirements_list = jQuery(element).siblings('.requirements-list');
    jQuery(element).siblings( '.requirements-spinner' ).addClass('is-active');

    jQuery.post(
        ajaxurl,
        {
            action: 'gamipress_add_requirement',
            post_id: post_id,
            requirement_type: requirement_type
        },
        function( response ) {
            jQuery( response ).appendTo( requirements_list );

            // Hide the new requirement
            requirements_list.find( 'li.requirement-row:last').attr('style', 'display: none;');

            // Dynamically add the menu order for the new points award to be one higher than the last in line
            var new_requirement_menu_order = Number( requirements_list.find( 'li.requirement-row' ).eq( -2 ).find( 'input[name="order"]' ).val() ) + 1;
            requirements_list.find( 'li.requirement-row:last' ).find( 'input[name="order"]' ).val( new_requirement_menu_order );

            // Trigger a change for the new trigger type and limit type elements
            requirements_list.find( 'li.requirement-row:last' ).find( '.select-trigger-type' ).change();
            requirements_list.find( 'li.requirement-row:last' ).find( '.limit-type' ).change();

            // Hide the spinner
            requirements_list.siblings( '.requirements-spinner' ).removeClass('is-active');

            // Slide Down the new requirement
            requirements_list.find( 'li.requirement-row:last').slideDown('fast');

            // Add a custom data with current fields values to check their changes
            requirements_list.find( 'li.requirement-row:last').find('input, select, textarea').each( function() {
                $(this).data('unsaved-value', $(this).val());
            });

            // Trigger change event on sequential input to update the order again
            $("#_gamipress_sequential").change();
        }
    );
}

/**
 * Duplicate a requirement
 *
 * @since 1.4.6
 *
 * @param element
 * @param requirement_id
 */
function gamipress_duplicate_requirement( element, requirement_id ) {

    var requirements_list = jQuery(element).closest('.requirements-list');
    requirements_list.siblings( '.requirements-spinner' ).addClass('is-active');

    jQuery.post(
        ajaxurl,
        {
            action: 'gamipress_duplicate_requirement',
            post_id: $('input#post_ID').val(),
            requirement_id: requirement_id
        },
        function( response ) {
            jQuery( response ).appendTo( requirements_list );

            // Hide the new requirement
            requirements_list.find( 'li.requirement-row:last').attr('style', 'display: none;');

            // Dynamically add the menu order for the new points award to be one higher than the last in line
            var new_requirement_menu_order = Number( requirements_list.find( 'li.requirement-row' ).eq( -2 ).find( 'input[name="order"]' ).val() ) + 1;
            requirements_list.find( 'li.requirement-row:last' ).find( 'input[name="order"]' ).val( new_requirement_menu_order );

            // Trigger a change for the new trigger type and limit type elements
            requirements_list.find( 'li.requirement-row:last' ).find( '.select-trigger-type' ).change();
            requirements_list.find( 'li.requirement-row:last' ).find( '.limit-type' ).change();

            // Hide the spinner
            requirements_list.siblings( '.requirements-spinner' ).removeClass('is-active');

            // Slide Down the new requirement
            requirements_list.find( 'li.requirement-row:last').slideDown('fast');

            // Add a custom data with current fields values to check their changes
            requirements_list.find( 'li.requirement-row:last').find('input, select, textarea').each( function() {
                $(this).data('unsaved-value', $(this).val());
            });

            // Trigger change event on sequential input to update the order again
            $("#_gamipress_sequential").change();

            // If current element has a custom class for requirement actions, remove it
            if( jQuery(element).hasClass('requirement-action-active') ) {
                jQuery(element).removeClass('requirement-action-active');
            }
        }
    );

}

/**
 * Delete a requirement
 *
 * @since 1.0.0
 *
 * @param element
 * @param requirement_id
 */
function gamipress_delete_requirement( element, requirement_id ) {

    var requirements_list = jQuery(element).closest('.requirements-list');
    requirements_list.find( '.requirement-' + requirement_id ).slideUp( 'fast' );

    // Remove requirement published class to update requirements order
    requirements_list.find( '.requirement-' + requirement_id).removeClass('requirement-published')

    // Trigger change event on sequential input to update the order again
    $("#_gamipress_sequential").change();

    jQuery.post(
        ajaxurl,
        {
            action: 'gamipress_delete_requirement',
            requirement_id: requirement_id
        },
        function( response ) {
            requirements_list.find( '.requirement-' + requirement_id ).remove();
        }
    );
}

/**
 * Update all requirements
 *
 * @since 1.0.0
 *
 * @param element
 */
function gamipress_update_requirements( element ) {

    var requirements_list = jQuery(element).siblings('.requirements-list');
    requirements_list.siblings( '.requirements-spinner' ).addClass('is-active');

    var requirement_data = {
        action: 'gamipress_update_requirements',
        post_id: $('input#post_ID').val(),
        _gamipress_sequential: ( $('input#_gamipress_sequential').prop('checked') ? 'on' : '' ),
        requirements: []
    };

    // Loop through each points award and collect its data
    requirements_list.find( '.requirement-row' ).each( function() {

        // Cache our points award object
        var requirement = jQuery(this);
        var trigger_type = requirement.find( '.select-trigger-type' ).val();

        // Setup our points award object
        var requirement_details = {
            requirement_id          : requirement.find( 'input[name="requirement_id"]').val(),
            requirement_type        : requirement.find( 'input[name="requirement_type"]').val(),
            order                   : requirement.find( 'input[name="order"]' ).val(),
            status                  : ( requirement.find( '.requirement-action-change-status input' ).prop('checked') ? 'publish' : 'pending' ),
            points_required         : requirement.find( '.points-required' ).val(),
            points_type_required    : requirement.find( '.select-points-type-required' ).val(),
            rank_type_required      : requirement.find( '.select-rank-type-required' ).val(),
            rank_required           : requirement.find( '.select-rank-required' ).val(),
            count                   : requirement.find( '.count' ).val(),
            limit                   : requirement.find( '.limit' ).val(),
            limit_type              : requirement.find( '.limit-type' ).val(),
            trigger_type            : trigger_type,
            achievement_type        : requirement.find( '.select-achievement-type' ).val(),
            achievement_post        : ( gamipress_requirements_ui.specific_activity_triggers[trigger_type] !== undefined ? requirement.find( '.select-post' ).val() : requirement.find( 'select.select-achievement-post' ).val() ),
            title                   : requirement.find( '.requirement-title .title' ).val()
        };

        if( requirement_details.requirement_type === 'points-award' || requirement_details.requirement_type === 'points-deduct' ) {
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
                requirements_list.find('.requirement-' + id + ' .requirement-header-title strong').html(value);
                requirements_list.find('#requirement-' + id + '-title').val(value);
            });

            // Update all unsaved values to meet that current ones has been saved
            requirements_list.find('input, select, textarea').each( function() {
                $(this).data('unsaved-value', $(this).val());
            });

            // Remove all unsaved data warnings
            requirements_list.find('.requirement-unsaved-changes').remove();

            // Hide the spinner
            jQuery(element).siblings( '.requirements-spinner' ).removeClass('is-active');
        }
    );
}
