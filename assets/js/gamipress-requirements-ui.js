(function($) {

    // Hide requirements meta box if unnecessary
    $('body').on('change', '#_gamipress_earned_by', function() {
        if ( 'triggers' == $(this).val() )
            $('#gamipress-requirements-ui').show();
        else
            $('#gamipress-requirements-ui').hide();
    }).trigger('change');

    // Make requirements list sortable
    $('.requirements-list').sortable({
        cancel: 'input, select, textarea, .gamipress-no-grab',
        // When the list order is updated
        update : function ( e, ui ) {

            // Loop through each element
            $(e.target).find('li').each(function( index, value ) {

                // Write it's current position to our hidden input value
                $(this).find('input[name="order"]').val( index );

            });

            // Trigger change on sequential input to update the requirements order display
            $("#_gamipress_sequential").trigger('change');

        }
    });

    // Click add requirement
    $('body').on('click', '.gamipress-add-requirement', function(e) {
        gamipress_add_requirement( e.target, $(e.target).data('post-id'), $(e.target).data('requirement-type') );
    });

    // Click save requirements
    $('body').on('click', '.gamipress-save-requirements', function(e) {
        gamipress_update_requirements( e.target );
    });

    // On change sequential requirements, add order display on all requirements
    $('body').on('change', '#_gamipress_sequential', function() {

        $('.requirements-list .requirement-header-title .requirement-order').remove();

        if( $(this).prop('checked') ) {
            var index = 1;

            $('.requirements-list .requirement-published .requirement-header-title').each(function() {
                $(this).prepend('<span class="requirement-order">' + index + ' -</span>');

                index++;
            });
        }

    });

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
        $("#_gamipress_sequential").trigger('change');
    });

    // Duplicate action
    $('.requirements-list').on( 'click', '.requirement-action.requirement-action-duplicate', function(e) {
        e.preventDefault();

        // Bail if already clicked
        if( $(this).hasClass('requirement-action-active') )
            return;

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
            $(this).gamipress_select2({
                theme: 'default gamipress-select2 gamipress-trigger-type-selector',
                matcher: gamipress_select2_optgroup_matcher,
            });
        }

        // Grab our selected trigger type and achievement selector
        var trigger_type = $(this).val();

        // Common selectors for points and rank trigger types
        var count = $(this).siblings('.count');
        var count_text = $(this).siblings('.count-text');
        var limit_text = $(this).siblings('.limit-text');
        var limit = $(this).siblings('.limit');
        var limit_type = $(this).siblings('.limit-type');

        if ( gamipress_requirements_ui.triggers_excluded_from_limit.indexOf( trigger_type ) !== -1 ) {
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

            if( limit_type.val() !== 'unlimited' ) {
                limit.show();
            } else {
                limit.hide();
            }

            limit_type.show();
        }

        // Points condition
        var points_condition_selector = $(this).siblings('.select-points-condition');

        if ( trigger_type === 'points-balance' ) {
            // Show points condition field
            points_condition_selector.show();
        } else {
            // Hide points condition field
            points_condition_selector.hide();
        }

        // Metas condition
        var meta_key_selector = $(this).siblings('.meta-key-required');

        if( trigger_type === 'gamipress_update_post_meta_any_value'
        || trigger_type === 'gamipress_update_user_meta_any_value'
        || trigger_type === 'gamipress_update_post_meta_specific_value'
        || trigger_type === 'gamipress_update_user_meta_specific_value' ) {
            // Show meta key field
            meta_key_selector.show();
        } else {
            // Hide meta key field
            meta_key_selector.hide();
        }

        var meta_value_selector = $(this).siblings('.meta-value-required');

        if( trigger_type === 'gamipress_update_post_meta_specific_value'
        || trigger_type === 'gamipress_update_user_meta_specific_value' ) {
            // Show meta value field
            meta_value_selector.show();
        } else {
            // Hide meta value field
            meta_value_selector.hide();
        }

        // Required points
        var points_selector_required = $(this).siblings('.points-required');
        var points_type_selector_required = $(this).siblings('.select-points-type-required');

        if ( gamipress_requirements_ui.points_triggers.indexOf( trigger_type ) !== -1 ) {
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

        if ( gamipress_requirements_ui.rank_type_triggers.indexOf( trigger_type ) !== -1 ) {
            // Show required rank fields
            rank_type_selector_required.show();

            // Trigger a change to the rank type selector to determine if it should show
            rank_type_selector_required.trigger('change');
        } else {
            // Hide required rank fields
            rank_type_selector_required.hide();
            rank_selector_required.hide();
        }

        // Achievement type
        var achievement_type_selector = $(this).siblings('.select-achievement-type');
        var achievement_post_selector = $(this).siblings('.select-achievement-post');

        // If we're working with achievements, show the achievement type selector (otherwise, hide it)
        if ( gamipress_requirements_ui.achievement_type_triggers.indexOf( trigger_type ) !== -1 ) {
            achievement_type_selector.show();

            // Trigger a change to the achievement type selector to determine if it should show
            achievement_type_selector.trigger('change');
        } else {
            // Hide achievement type and post selector
            achievement_type_selector.hide();
            achievement_post_selector.hide();
        }

        // Post type
        var post_type_selector_required = $(this).siblings('.select-post-type-required');

        if ( gamipress_requirements_ui.post_type_triggers.indexOf( trigger_type ) !== -1 ) {
            post_type_selector_required.show();
        } else {
            post_type_selector_required.hide();
        }

        // User role
        var user_role_selector_required = $(this).siblings('.select-user-role-required');

        if ( gamipress_requirements_ui.user_role_triggers.indexOf( trigger_type ) !== -1 ) {
            user_role_selector_required.show();
        } else {
            user_role_selector_required.hide();
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
                    .val('').trigger('change')   // Reset value
                    .next().show();     // Show Select2 container
            } else {
                post_selector.gamipress_select2({
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
                                nonce: gamipress_requirements_ui.nonce,
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

                post_selector.on('select2:select', function (e) {
                    var item = e.params.data;

                    // If site ID is defined, then update the hidden field
                    if( item.site_id !== undefined ) {
                        $(this).siblings('.select-post-site-id').val( item.site_id );
                    }
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
        var $this               = $(this);
        var achievement_post    = $this.siblings('.select-achievement-post');
        var achievement_type    = $this.val();
        var requirement_id      = $this.parent('li').attr('data-requirement-id');
        var requirement_type    = $this.siblings('input[name="requirement_type"]').val();
        var excluded_posts      = [$this.siblings('input[name="post_id"]').val()];
        var trigger_type        = $this.siblings('.select-trigger-type').val();
        var events              = [ 'specific-achievement', 'revoke-specific-achievement' ];

        // If we've selected a *specific* achievement type, show our post selector and populate it w/ the corresponding achievement posts
        if ( '' !== achievement_type && events.indexOf( trigger_type ) !== -1 ) {
            // Add the loader
            $('<span class="achievement-type-spinner spinner is-active" style="float: none; margin: 0 2px 0 4px;"></span>').insertAfter($this);

            achievement_post.hide();

            $.post(
                ajaxurl,
                {
                    action: 'gamipress_get_achievements_options_html',
                    nonce: gamipress_requirements_ui.nonce,
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
        var $this               = $(this);
        var rank_selector       = $this.siblings('.select-rank-required');
        var rank_type           = $this.val();
        var requirement_id      = $this.parent('li').attr('data-requirement-id');
        var trigger_type        = $this.siblings('.select-trigger-type').val();
        var events              = [ 'earn-rank', 'revoke-rank' ];

        // If we've selected a *specific* achievement type, show our post selector and populate it w/ the corresponding achievement posts
        if ( '' !== rank_type && events.indexOf( trigger_type ) !== -1 ) {

            // Add the loader
            $('<span class="rank-type-spinner spinner is-active" style="float: none; margin: 0 2px 0 4px;"></span>').insertAfter( $this );

            rank_selector.hide();

            $.post(
                ajaxurl,
                {
                    action: 'gamipress_get_ranks_options_html',
                    nonce: gamipress_requirements_ui.nonce,
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
        var $this = $(this);

        if( $this.val() === 'unlimited' ) {
            $this.siblings('.limit').hide();
        } else {
            $this.siblings('.limit').show();
        }
    });

    // Listen for a change to optional select
    $('.requirements-list').on( 'change', '.select-optional', function() {
        var $this = $(this);

        if( $this.val() === '1' ) {
            $this.addClass('optional-selected');
            $this.removeClass('required-selected');
        } else {
            $this.removeClass('optional-selected');
            $this.addClass('required-selected');
        }
    });

    // Trigger a change for our trigger type post selector to determine if it should show
    $( '.select-trigger-type' ).trigger('change');

    // Trigger a change for our sequential field
    $( '#_gamipress_sequential' ).trigger('change');

    // Trigger a change for our limit type to determine if limit should show
    $( '.limit-type' ).trigger('change');

    // Trigger a change for our optional select to update its class
    $( '.select-optional' ).trigger('change');

    // Trigger a change for our change status input
    $( '.requirement-action-change-status input' ).trigger('change');

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
            row.find('.requirement-header-title').append('<span class="requirement-unsaved-changes dashicons dashicons-warning" title="' + gamipress_requirements_ui.unsaved_changes_text + '"></span>');
        }

        // Trigger change on sequential input to update the requirements order display
        $("#_gamipress_sequential").trigger('change');

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

    var $ = jQuery;

    var requirements_list = $(element).siblings('.requirements-list');
    $(element).siblings( '.requirements-spinner' ).addClass('is-active');

    $.post(
        ajaxurl,
        {
            action: 'gamipress_add_requirement',
            nonce: gamipress_requirements_ui.nonce,
            post_id: post_id,
            requirement_type: requirement_type
        },
        function( response ) {
            $( response ).appendTo( requirements_list );

            // Hide the new requirement
            requirements_list.find( 'li.requirement-row:last').attr('style', 'display: none;');

            // Dynamically add the menu order for the new points award to be one higher than the last in line
            var new_requirement_menu_order = Number( requirements_list.find( 'li.requirement-row' ).eq( -2 ).find( 'input[name="order"]' ).val() ) + 1;
            requirements_list.find( 'li.requirement-row:last' ).find( 'input[name="order"]' ).val( new_requirement_menu_order );

            // Trigger a change for the new trigger type and limit type elements
            requirements_list.find( 'li.requirement-row:last' ).find( '.select-trigger-type' ).trigger('change');
            requirements_list.find( 'li.requirement-row:last' ).find( '.limit-type' ).trigger('change');

            // Hide the spinner
            requirements_list.siblings( '.requirements-spinner' ).removeClass('is-active');

            // Slide Down the new requirement
            requirements_list.find( 'li.requirement-row:last').slideDown('fast');

            // Add a custom data with current fields values to check their changes
            requirements_list.find( 'li.requirement-row:last').find('input, select, textarea').each( function() {
                $(this).data('unsaved-value', $(this).val());
            });

            // Trigger change event on sequential input to update the order again
            $("#_gamipress_sequential").trigger('change');
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

    var $ = jQuery;

    var requirements_list = $(element).closest('.requirements-list');
    requirements_list.siblings( '.requirements-spinner' ).addClass('is-active');

    $.post(
        ajaxurl,
        {
            action: 'gamipress_duplicate_requirement',
            nonce: gamipress_requirements_ui.nonce,
            post_id: $('input#post_ID').val(),
            requirement_id: requirement_id
        },
        function( response ) {
            $( response ).appendTo( requirements_list );

            // Hide the new requirement
            requirements_list.find( 'li.requirement-row:last').attr('style', 'display: none;');

            // Dynamically add the menu order for the new points award to be one higher than the last in line
            var new_requirement_menu_order = Number( requirements_list.find( 'li.requirement-row' ).eq( -2 ).find( 'input[name="order"]' ).val() ) + 1;
            requirements_list.find( 'li.requirement-row:last' ).find( 'input[name="order"]' ).val( new_requirement_menu_order );

            // Trigger a change for the new trigger type and limit type elements
            requirements_list.find( 'li.requirement-row:last' ).find( '.select-trigger-type' ).trigger('change');
            requirements_list.find( 'li.requirement-row:last' ).find( '.limit-type' ).trigger('change');

            // Hide the spinner
            requirements_list.siblings( '.requirements-spinner' ).removeClass('is-active');

            // Slide Down the new requirement
            requirements_list.find( 'li.requirement-row:last').slideDown('fast');

            // Add a custom data with current fields values to check their changes
            requirements_list.find( 'li.requirement-row:last').find('input, select, textarea').each( function() {
                $(this).data('unsaved-value', $(this).val());
            });

            // Trigger change event on sequential input to update the order again
            $("#_gamipress_sequential").trigger('change');

            // If current element has a custom class for requirement actions, remove it
            if( $(element).hasClass('requirement-action-active') )
                $(element).removeClass('requirement-action-active');
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

    var $ = jQuery;

    var requirements_list = $(element).closest('.requirements-list');
    requirements_list.find( '.requirement-' + requirement_id ).slideUp( 'fast' );

    // Remove requirement published class to update requirements order
    requirements_list.find( '.requirement-' + requirement_id).removeClass('requirement-published')

    // Trigger change event on sequential input to update the order again
    $("#_gamipress_sequential").trigger('change');

    $.post(
        ajaxurl,
        {
            action: 'gamipress_delete_requirement',
            nonce: gamipress_requirements_ui.nonce,
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
 * @since   1.0.0
 * @updated 1.4.7.2 Added loop parameter
 *
 * @param element
 * @param loop
 */
function gamipress_update_requirements( element, loop ) {

    if( loop === undefined ) {
        loop = 0;
    }

    var $ = jQuery;

    var requirements_list = $(element).siblings('.requirements-list');
    requirements_list.siblings( '.requirements-spinner' ).addClass('is-active');

    // On large requirements list, we need to store them in groups
    var requirements = requirements_list.find( '.requirement-row' );

    // So let's to define an offset and limit
    var requirements_limit = 20;
    var current_offset = ( loop * requirements_limit );
    var current_limit = ( ( loop + 1 ) * requirements_limit );

    // Setup the data to be send
    var requirement_data = {
        action: 'gamipress_update_requirements',
        nonce: gamipress_requirements_ui.nonce,
        post_id: $('input#post_ID').val(),
        loop: loop,
        _gamipress_sequential: ( $('input#_gamipress_sequential').prop('checked') ? 'on' : '' ),
        requirements: []
    };

    // To set smaller groups of requirements to being stored
    var current_requirements = requirements.slice( current_offset, current_limit );

    // Loop through current requirements and collect its data
    current_requirements.each( function() {

        // Cache our points award object
        var requirement = $(this);
        var trigger_type = requirement.find( '.select-trigger-type' ).val();

        // Setup our points award object
        var requirement_details = {
            requirement_id              : requirement.find( 'input[name="requirement_id"]').val(),
            requirement_type            : requirement.find( 'input[name="requirement_type"]').val(),
            order                       : requirement.find( 'input[name="order"]' ).val(),
            status                      : ( requirement.find( '.requirement-action-change-status input' ).prop('checked') ? 'publish' : 'pending' ),
            points_condition            : requirement.find( '.select-points-condition' ).val(),
            points_required             : requirement.find( '.points-required' ).val(),
            points_type_required        : requirement.find( '.select-points-type-required' ).val(),
            rank_type_required          : requirement.find( '.select-rank-type-required' ).val(),
            rank_required               : requirement.find( '.select-rank-required' ).val(),
            post_type_required          : requirement.find( '.select-post-type-required' ).val(),
            user_role_required          : requirement.find( '.select-user-role-required' ).val(),
            count                       : requirement.find( '.count' ).val(),
            limit                       : requirement.find( '.limit' ).val(),
            limit_type                  : requirement.find( '.limit-type' ).val(),
            trigger_type                : trigger_type,
            achievement_type            : requirement.find( '.select-achievement-type' ).val(),
            achievement_post            : ( gamipress_requirements_ui.specific_activity_triggers[trigger_type] !== undefined ? requirement.find( '.select-post' ).val() : requirement.find( 'select.select-achievement-post' ).val() ),
            achievement_post_site_id    : ( gamipress_requirements_ui.specific_activity_triggers[trigger_type] !== undefined ? requirement.find( '.select-post-site-id' ).val() : '' ),
            optional                    : requirement.find( '.requirement-optional select' ).val(),
            title                       : requirement.find( '.requirement-title .title' ).val(),
            url                         : requirement.find( '.requirement-url .url' ).val()
        };

        if( requirement_details.requirement_type === 'points-award' || requirement_details.requirement_type === 'points-deduct' ) {
            requirement_details.points = requirement.find( '.points' ).val();
            requirement_details.points_type = requirement.find( 'input[name="points_type"]' ).val();
            requirement_details.maximum_earnings = requirement.find( '.maximum-earnings' ).val();
        }

        if( requirement_details.trigger_type === 'gamipress_update_user_meta_any_value'
        || requirement_details.trigger_type === 'gamipress_update_post_meta_any_value' ) {
            
            requirement_details.meta_key_required = requirement.find( '.meta-key-required' ).val();
    
        }

        if( requirement_details.trigger_type === 'gamipress_update_user_meta_specific_value'
        || requirement_details.trigger_type === 'gamipress_update_post_meta_specific_value' ) {
            
            requirement_details.meta_key_required = requirement.find( '.meta-key-required' ).val();
            requirement_details.meta_value_required = requirement.find( '.meta-value-required' ).val();
    
        }

        /**
         * Allow external functions to add their own data to the array
         *
         * @deprecated use gamipress_update_requirement_data event instead
         *
         * @since 1.0.0
         *
         * @selector    .requirements-list .requirement
         * @event       update_requirement_data
         */
        requirement.trigger( 'update_requirement_data', [ requirement_details, requirement ] );

        /**
         * Allow external functions to add their own data to the array
         *
         * @since 1.5.9
         *
         * @selector    .requirements-list .requirement
         * @event       gamipress_update_requirement_data
         *
         * @param Object    requirement_details
         * @param Node      requirement
         */
        requirement.trigger( 'gamipress_update_requirement_data', [ requirement_details, requirement ] );

        // Add our relevant data to the array
        requirement_data.requirements.push( requirement_details );
    });

    $.post(
        ajaxurl,
        requirement_data,
        function( response ) {
            // Parse response
            var titles = JSON.parse( response );

            // Loop all given titles
            $.each( titles, function( id, value ) {

                var requirement = requirements_list.find('.requirement-' + id );

                // Update the title
                requirement.find('.requirement-header-title strong').html(value);
                requirement.find('.requirement-title .title').val(value);

                // Update unsaved values to meet that current ones has been saved
                requirement.find('input, select, textarea').each( function() {
                    $(this).data('unsaved-value', $(this).val());
                });

                // Remove unsaved data warning
                requirement.find('.requirement-unsaved-changes').remove();
            });

            if( current_limit < requirements.length ) {

                // Continue looping each group of requirements to save them
                loop++;

                gamipress_update_requirements( element, loop );

            } else {

                // All requirements saved successfully Hide the spinner
                $(element).siblings( '.requirements-spinner' ).removeClass('is-active');

            }


        }
    );
}
