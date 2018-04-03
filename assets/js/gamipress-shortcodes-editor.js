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
            var key, value;

            // Turn array of repeatable field into a comma separated values
            if( $(el).closest('.cmb-row').hasClass('cmb-repeat-row') ) {
                // Repeatable

                var field_name = el.name.split('[')[0];
                key = el.name.replace( shortcode + '_', '').replace('[]', '');

                key = key.split('[')[0];

                // Just continue if element has not set
                if( attrs[key] === undefined ) {

                   // Look at all fields
                    var fields = $(el).closest('.cmb-tbody').find('[name^="' + field_name + '"]');
                    var values = [];

                    // Loop all fields and make a comma separated attr value
                    for( var i=0; i < fields.length; i++ ) {

                        var field = $(fields[i]);

                        if( field.val().length ) {
                            values.push( field.val() );
                        }
                    }

                    attrs[key] = values.join(',');

                }
            } else {
                // Single

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

                    // CMB2 adds a prefix on each field, so we need to remove it, also, wee need to remove array brace for multiple fields
                    key = el.name.replace( shortcode + '_', '').replace('[]', '');

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
    }).change();

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

    var select2_achievements = {
        ajax: {
            url: ajaxurl,
            dataType: 'json',
            delay: 250,
            type: 'POST',
            data: function( params ) {
                return {
                    q: params.term,
                    action: 'gamipress_get_achievements_options'
                };
            },
            processResults: gamipress_select2_posts_process_results
        },
        escapeMarkup: function ( markup ) { return markup; }, // Let our custom formatter work
        templateResult: gamipress_select2_posts_template_result,
        theme: 'default gamipress-select2',
        placeholder: gamipress_shortcodes_editor.id_placeholder,
        allowClear: true,
        multiple: false
    };
    var select2_achievements_multiple = $.extend( true, {}, select2_achievements, { multiple: true } );

    // Achievement ajax
    $( '#gamipress_achievement_id' ).select2( select2_achievements );

    // Achievement ajax multiple
    $( '#gamipress_achievements_include, #gamipress_achievements_exclude, #gamipress_logs_include, #gamipress_logs_exclude' ).select2( select2_achievements_multiple );

    // Select2 multiple
    $( '#gamipress_achievements_type, #gamipress_points_types_type, #gamipress_points_type, #gamipress_ranks_type, #gamipress_earnings_points_types, #gamipress_earnings_achievement_types, #gamipress_earnings_rank_types' ).select2({
        theme: 'default gamipress-select2',
        placeholder: gamipress_shortcodes_editor.post_type_placeholder,
        allowClear: true,
        multiple: true
    });

    // User ajax
    $( '#gamipress_achievements_user_id, #gamipress_logs_user_id, #gamipress_points_user_id, #gamipress_rank_user_id, #gamipress_ranks_user_id, #gamipress_user_rank_user_id, #gamipress_earnings_user_id' ).select2({
        ajax: {
            url: ajaxurl,
            dataType: 'json',
            delay: 250,
            type: 'POST',
            data: function( params ) {
                return {
                    q: params.term,
                    page: params.page || 1,
                    action: 'gamipress_get_users'
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
    $( '#gamipress_achievements_current_user, #gamipress_points_current_user, #gamipress_logs_current_user, #gamipress_ranks_current_user, #gamipress_user_rank_current_user, #gamipress_earnings_current_user').change(function() {
        var target = $(this).closest('.cmb-row').next(); // User ID field

        if( $(this).prop('checked') ) {
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
    });

    var select2_ranks = {
        ajax: {
            url: ajaxurl,
            dataType: 'json',
            delay: 250,
            type: 'POST',
            data: function( params ) {
                return {
                    q: params.term,
                    action: 'gamipress_get_ranks_options'
                };
            },
            processResults: gamipress_select2_posts_process_results
        },
        escapeMarkup: function ( markup ) { return markup; }, // Let our custom formatter work
        templateResult: gamipress_select2_posts_template_result,
        theme: 'default gamipress-select2',
        placeholder: gamipress_shortcodes_editor.rank_placeholder,
        allowClear: true,
        multiple: false
    };
    var select2_ranks_multiple = $.extend( true, {}, select2_ranks, { multiple: true } );

    // Rank ajax
    $( '#gamipress_rank_id' ).select2( select2_ranks );

    // Rank ajax multiple
    $( '#gamipress_ranks_include, #gamipress_ranks_exclude' ).select2( select2_ranks_multiple );

    // User earnings
    $( '#gamipress_earnings_points, #gamipress_earnings_achievements, #gamipress_earnings_ranks' ).change(function() {

        var id = $(this).attr('id');
        var target = undefined;

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
            $( 'select.select2-hidden-accessible', '.gamipress-shortcode-thickbox .cmb2-wrap').val('').change();

            // Trigger change on all checkboxes to initialize visibility
            $( 'input[type="checkbox"]', '.gamipress-shortcode-thickbox .cmb2-wrap').change();
        }, 0 );
    }

}(jQuery));
