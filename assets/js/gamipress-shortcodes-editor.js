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
        var attrs = [];
        var inputs = gamipress_get_shortcode_inputs( shortcode );

        $.each( inputs, function( index, el ) {
            // Select2 values are only accessible through jQuery val()
            var value = $(el).val();

            // Turn checked status into yes or no
            if( $(el).attr('type') === 'checkbox' ) {
                value = $(el).prop('checked') ? 'yes' : 'no';
            }

            if (value !== '' && value !== undefined && value !== null ) {
                // CMB2 adds a prefix on each field, so we need to remove it, also, wee need to remove array brace for multiple fields
                attrs.push( el.name.replace( shortcode + '_', '').replace('[]', '') + '="' + value + '"' );
            }
        });

        return attrs;
    }

    function gamipress_get_shortcode_inputs( shortcode ) {
        // Look at .cmb2-wrap to prevent get cmb2 nonce fields
        return $( 'input, select, textarea', '#' + shortcode + '_wrapper .cmb2-wrap' );
    }

    function gamipress_construct_shortcode( shortcode, attributes ) {
        var output = '[';
        output += shortcode;

        if ( attributes ) {
            for( var i = 0; i < attributes.length; i++ ) {
                output += ' ' + attributes[i];
            }

            $.trim( output );
        }
        output += ']';

        return output;
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

    var select2_post_defaults = {
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
        placeholder: gamipress_shortcodes_editor.id_placeholder,
        allowClear: true,
        multiple: false
    };
    var select2_post_multiples = $.extend( true, {}, select2_post_defaults, { multiple: true } );

    $( '#gamipress_achievement_id, #gamipress_nomination_achievement_id, #gamipress_submission_achievement_id' ).select2( select2_post_defaults );
    $( '#gamipress_achievements_include, #gamipress_achievements_exclude, #gamipress_logs_include, #gamipress_logs_exclude' ).select2( select2_post_multiples );

    $( '#gamipress_achievements_type, #gamipress_points_types_type, #gamipress_points_type' ).select2({
        theme: 'default gamipress-select2',
        placeholder: gamipress_shortcodes_editor.post_type_placeholder,
        allowClear: true,
        multiple: true
    });

    $( '#gamipress_achievements_user_id, #gamipress_logs_user_id, #gamipress_points_user_id' ).select2({
        ajax: {
            url: ajaxurl,
            dataType: 'json',
            delay: 250,
            type: 'POST',
            data: function( params ) {
                return {
                    q: params.term,
                    action: 'gamipress_get_users'
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
                        text: item.user_login,
                    });
                });

                return { results: formatted_results };
            }
        },
        theme: 'default gamipress-select2',
        placeholder: gamipress_shortcodes_editor.user_placeholder,
        allowClear: true,
        multiple: false
    });

    $( '#gamipress_achievements_current_user, #gamipress_points_current_user, #gamipress_logs_current_user').change(function() {
        var target = $(this).closest('.cmb-row').next(); // User ID field

        if( $(this).prop('checked') ) {
            target.slideUp().addClass('cmb2-tab-ignore');
        } else {
            target.slideDown().removeClass('cmb2-tab-ignore');
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
        }, 0 );
    }

}(jQuery));
