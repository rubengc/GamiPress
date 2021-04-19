(function ($) {

    function gamipress_insert_shortcode() {

        var shortcode = gamipress_get_selected_shortcode();
        var attributes = gamipress_get_attributes( shortcode );
        var constructed = gamipress_construct_shortcode( shortcode, attributes );

        window.send_to_editor( constructed );
    }

    function gamipress_get_selected_shortcode() {
        return $( '#select_shortcode' ).val();
    }

    function gamipress_get_attributes( shortcode ) {
        var attrs = {};
        var inputs = gamipress_get_shortcode_inputs( shortcode );

        $.each( inputs, function( index, el ) {
            var key, field, value, fields, values, i;
            var row = $(el).closest('.cmb-row');

            // Skip repeatable fields pattern
            if( row.hasClass('empty-row') && row.hasClass('hidden') ) return true;

            // Turn array of repeatable field into a comma separated values
            if( row.hasClass('cmb-repeat-row') ) {
                // Repeatable

                key = el.name.replace( shortcode + '_', '').replace('[]', '');

                key = key.split('[')[0];

                // Skip empty shortcode keys
                if( key === '' ) return true;

                // Just continue if element has not set
                if( attrs[key] === undefined ) {

                    var field_name = el.name.split('[')[0];

                    // Look at all fields
                    fields = $(el).closest('.cmb-tbody').find('[name^="' + field_name + '"]');
                    values = [];

                    // Loop all fields and make an array with all values
                    // Note: loop max is set to length-1 to skip pattern field
                    for( i=0; i < fields.length-1; i++ ) {
                        field = $(fields[i]);

                        if( field.val().length )
                            values.push( field.val() );
                    }

                    // Setup a comma-separated list of values as attribute value
                    if( values.length )
                        attrs[key] = values.join(',');

                }
            } else if( row.data('fieldtype') === 'multicheck' ) {
                // Multicheck

                key = el.name.replace( shortcode + '_', '').replace('[]', '');

                // Skip empty shortcode keys
                if( key === '' ) return true;

                // Look at checked fields
                fields = $(el).closest('.cmb2-checkbox-list').find('[name^="' + el.name + '"]:checked');
                values = [];

                // Loop checked fields and make an array with all values
                for( i=0; i < fields.length; i++ ) {

                    field = $(fields[i]);

                    if( field.val().length )
                        values.push( field.val() );
                }

                // Setup a comma-separated list of values as attribute value
                if( values.length )
                    attrs[key] = values.join(',');

            } else {
                // Single

                // CMB2 adds a prefix on each field, so we need to remove it, also, wee need to remove array brace for multiple fields
                key = el.name.replace( shortcode + '_', '').replace('[]', '');

                // Skip empty shortcode keys
                if( key === '' ) return true;

                // Select2 values are only accessible through jQuery val()
                value = $(el).val();

                // Turn checked status into yes or no
                if( $(el).attr('type') === 'checkbox' ) {
                    value = $(el).prop('checked') ? 'yes' : 'no';
                }

                // For radio inputs, just get checked input value
                if( $(el).attr('type') === 'radio' ) {
                    value = $(el).closest('.cmb2-radio-list').find('input[type="radio"]:checked').val()
                }

                if( typeof value === 'string' ) {
                    // Replaces " by ' on text fields
                    value = value.replace(/"/g, "'");
                }

                if (value !== '' && value !== undefined && value !== null ) {
                    attrs[key] = value;
                }

            }
        });

        // Allow external functions to add their own data to the array of attrs
        var args = { attributes: attrs, inputs: inputs };

        $('#' + shortcode + '_wrapper').trigger( 'gamipress_shortcode_attributes', [ args ] );

        // TODO: gamipress_get_shortcode_attributes is deprecated since 1.4.8, just keep for backward compatibility
        $('#' + shortcode + '_wrapper').trigger( 'gamipress_get_shortcode_attributes', [ args.attributes, args.inputs ] );

        return args.attributes;
    }

    function gamipress_get_shortcode_inputs( shortcode ) {
        // Look at .cmb2-wrap to prevent get cmb2 nonce fields
        return $( 'input, select, textarea', '#' + shortcode + '_wrapper .cmb2-wrap' );
    }

    function gamipress_construct_shortcode( shortcode, attributes ) {
        var output = '[';
        output += shortcode;

        $.each( attributes, function( key, value ) {
            output += ' ' + key + '="' + value + '"';
        });

        $.trim( output );
        output += ']';

        // Allow external functions to construct their own shortcode
        var args = { output: output, shortcode: shortcode, attributes: attributes };

        $('#' + shortcode + '_wrapper').trigger( 'gamipress_construct_shortcode', [ args ] );

        return args.output;
    }

    function gamipress_shortcode_hide_all_sections() {
        $( '.shortcode-section' ).hide();
    }

    function gamipress_shortcode_show_section( section_name ) {
        $( '#' + section_name + '_wrapper' ).show();
    }

    // Listen for changes to the selected shortcode
    $( '#select_shortcode' ).on( 'change', function() {
        gamipress_shortcode_hide_all_sections();
        gamipress_shortcode_show_section( gamipress_get_selected_shortcode() );
    }).trigger('change');

    // Listen for clicks on the "insert" button
    $( '#gamipress_insert' ).on( 'click', function( e ) {
        e.preventDefault();

        gamipress_insert_shortcode();
    });

    // Listen for clicks on the "cancel" button
    $( '#gamipress_cancel' ).on( 'click', function( e ) {
        e.preventDefault();

        tb_remove();
    });

    // User ajax
    $( '#gamipress_achievements_user_id, ' +
        '#gamipress_logs_user_id, ' +
        '#gamipress_points_user_id, ' +
        '#gamipress_user_points_user_id, ' +
        '#gamipress_points_types_user_id, ' +
        '#gamipress_rank_user_id, ' +
        '#gamipress_ranks_user_id, ' +
        '#gamipress_user_rank_user_id, ' +
        '#gamipress_earnings_user_id' ).gamipress_select2({
        ajax: {
            url: ajaxurl,
            dataType: 'json',
            delay: 250,
            type: 'POST',
            data: function( params ) {
                return {
                    q: params.term,
                    page: params.page || 1,
                    action: 'gamipress_get_users',
                    nonce: gamipress_shortcodes_editor.nonce,
                };
            },
            processResults: gamipress_select2_users_process_results
        },
        escapeMarkup: function ( markup ) { return markup; }, // Let our custom formatter work
        templateResult: gamipress_select2_users_template_result,
        theme: 'default gamipress-select2',
        placeholder: gamipress_shortcodes_editor.user_placeholder,
        allowClear: true,
        multiple: false
    });

    // Current user field
    $( '#gamipress_achievements_current_user, '
        + '#gamipress_points_current_user, '
        + '#gamipress_user_points_current_user, '
        + '#gamipress_points_types_current_user, '
        + '#gamipress_logs_current_user, '
        + '#gamipress_ranks_current_user, '
        + '#gamipress_user_rank_current_user, '
        + '#gamipress_earnings_current_user'
    ).on('change', function() {
        var target = $(this).closest('.cmb-row').next(); // User ID field

        if( $(this).prop('checked') ) {
            // Hide the target
            target.slideUp().addClass('cmb2-tab-ignore');
        } else {
            if( target.closest('.cmb-tabs-wrap').length ) {
                // Just show if item tab is active
                if( target.hasClass('cmb-tab-active-item') ) {
                    target.slideDown();
                }
            } else {
                target.slideDown();
            }

            target.removeClass('cmb2-tab-ignore');
        }
    }).trigger('change');

    // Earners field
    $( '#gamipress_achievement_earners, #gamipress_achievements_earners, '
        + '#gamipress_rank_earners, #gamipress_ranks_earners, #gamipress_user_rank_earners'
    ).on('change', function() {
        var target = $(this).closest('.cmb-row').next(); // Earners limit field

        if( ! $(this).prop('checked') ) {
            // Hide the target
            target.slideUp().addClass('cmb2-tab-ignore');
        } else {
            if( target.closest('.cmb-tabs-wrap').length ) {
                // Just show if item tab is active
                if( target.hasClass('cmb-tab-active-item') ) {
                    target.slideDown();
                }
            } else {
                target.slideDown();
            }

            target.removeClass('cmb2-tab-ignore');
        }
    }).trigger('change');

    // Period field
    $( '#gamipress_points_period, #gamipress_user_points_period, #gamipress_site_points_period').on('change', function() {
        var selector = $(this).attr('id').replace('_period', '').replaceAll('_', '-');

        // Get the period start and end fields
        var target = $(this).closest('.cmb2-wrap').find(
            '.cmb2-id-' + selector + '-period-start, '
            + '.cmb2-id-' + selector + '-period-end'
        );

        if( $(this).val() !== 'custom' ) {
            // Hide the target
            target.slideUp().addClass('cmb2-tab-ignore');
        } else {
            // Show the target
            target.slideDown().removeClass('cmb2-tab-ignore');
        }
    }).trigger('change');

    // Inline field
    $( '#gamipress_points_inline, #gamipress_user_points_inline, #gamipress_site_points_inline').on('change', function() {
        var selector = $(this).attr('id').replace('_inline', '').replaceAll('_', '-');

        // Get the columns and layout fields
        var target = $(this).closest('.cmb2-wrap').find(
            '.cmb2-id-' + selector + '-columns, '
            + '.cmb2-id-' + selector + '-layout'
        );

        if( $(this).prop('checked') ) {
            target.slideUp().addClass('cmb2-tab-ignore');
        } else {
            target.slideDown().removeClass('cmb2-tab-ignore');
        }
    });

    $('body').on('gamipress_shortcode_attributes', '#gamipress_points_wrapper, #gamipress_user_points_wrapper, #gamipress_site_points_wrapper', function( e, args ) {

        // If user checks inline, then columns and layout has no sense
        if( args.attributes.inline === 'yes' ) {
            delete args.attributes.columns;
            delete args.attributes.layout;
        }

    });

    // User earnings
    $( '#gamipress_earnings_points, #gamipress_earnings_achievements, #gamipress_earnings_ranks' ).on('change', function() {

        var id = $(this).attr('id');
        var target;

        if( id === 'gamipress_earnings_points' ) {
            target = $('.cmb2-id-gamipress-earnings-points-types, .cmb2-id-gamipress-earnings-awards, .cmb2-id-gamipress-earnings-deducts');
        } else if( id === 'gamipress_earnings_achievements' ) {
            target = $('.cmb2-id-gamipress-earnings-achievement-types, .cmb2-id-gamipress-earnings-steps');
        } else if( id === 'gamipress_earnings_ranks' ) {
            target = $('.cmb2-id-gamipress-earnings-rank-types, .cmb2-id-gamipress-earnings-rank-requirements');
        }

        if( $(this).prop('checked') ) {
            // Just show if current tab active is ours
            if( $(this).closest('.cmb-tabs-wrap').find('.cmb-tab.active[id$="' + id + '"]').length ) {
                target.slideDown();
            }

            target.removeClass('cmb2-tab-ignore');
        } else {
            target.slideUp().addClass('cmb2-tab-ignore');
        }
    });

    // Setup ThickBox when "Add GamiPress Shortcode" link is clicked
    $('body').on( 'click', '#insert_gamipress_shortcodes', function(e) {
        e.preventDefault();

        gamipress_shortcode_setup_thickbox( $(this) );
    });

    // Add a custom class to our shortcode thickbox
    function gamipress_shortcode_setup_thickbox( link ) {
        setTimeout( function() {
            // Add a custom class to the thickbox
            $('#TB_window').addClass('gamipress-shortcode-thickbox');

            // Clear all select2 fields
            $( 'select.select2-hidden-accessible', '.gamipress-shortcode-thickbox .cmb2-wrap').val('').trigger('change');

            // Trigger change on all checkboxes to initialize visibility
            $( 'input[type="checkbox"]', '.gamipress-shortcode-thickbox .cmb2-wrap').trigger('change');
        }, 0 );
    }

}(jQuery));
