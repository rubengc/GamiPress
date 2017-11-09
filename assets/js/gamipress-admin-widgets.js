(function($) {

    // Helper function to build a widget field selector
    function gamipress_widget_select2_selector( shortcode, field ) {

        return 'select[id^="widget-gamipress_' + shortcode + '"][id$="[' + field + ']"]:not(.select2-hidden-accessible)';

    }

    var gamipress_widget_select2_achievements = {
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
        placeholder: gamipress_admin_widgets.id_placeholder,
        allowClear: true,
        multiple: false
    };

    var gamipress_widget_select2_achievements_multiple = $.extend( true, {}, gamipress_widget_select2_achievements, { multiple: true } );

    var gamipress_widget_select2_multiple = {
        theme: 'default gamipress-select2',
        placeholder: gamipress_admin_widgets.post_type_placeholder,
        allowClear: true,
        multiple: true
    };

    var gamipress_widget_select2_users = {
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
        placeholder: gamipress_admin_widgets.user_placeholder,
        allowClear: true,
        multiple: false
    };

    var gamipress_widget_select2_ranks = {
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
        placeholder: gamipress_admin_widgets.rank_placeholder,
        allowClear: true,
        multiple: false
    };

    var gamipress_widget_select2_ranks_multiple = $.extend( true, {}, gamipress_widget_select2_ranks, { multiple: true } );

    // Achievement ajax
    $( gamipress_widget_select2_selector( 'achievement', 'id' ) ).select2( gamipress_widget_select2_achievements );

    // Achievement ajax multiple
    $(  gamipress_widget_select2_selector( 'achievements', 'include' ) + ', '
        + gamipress_widget_select2_selector( 'achievements', 'exclude' ) + ', '
        + gamipress_widget_select2_selector( 'logs', 'include' ) + ', '
        + gamipress_widget_select2_selector( 'logs', 'exclude' )
    ).select2( gamipress_widget_select2_achievements_multiple );

    // Select2 multiple
    $(  gamipress_widget_select2_selector( 'achievements', 'type' ) + ', '
        + gamipress_widget_select2_selector( 'points', 'type' ) + ', '
        + gamipress_widget_select2_selector( 'points_types', 'type' ) + ', '
        + gamipress_widget_select2_selector( 'ranks', 'type' )
    ).select2( gamipress_widget_select2_multiple );

    // Rank ajax
    $( gamipress_widget_select2_selector( 'rank', 'id' ) ).select2( gamipress_widget_select2_ranks );

    // Rank ajax multiple
    $(  gamipress_widget_select2_selector( 'ranks', 'include' ) + ', '
        + gamipress_widget_select2_selector( 'ranks', 'exclude' )
    ).select2( gamipress_widget_select2_ranks_multiple );

    // User ajax
    $( 'select[id^="widget-gamipress"][id$="[user_id]"]:not(.select2-hidden-accessible)' ).select2( gamipress_widget_select2_users );

    // Current user field
    $( 'input[id^="widget-gamipress"][id$="[current_user]"]').change(function() {
        var target = $(this).closest('.cmb-row').next(); // User ID field

        if( $(this).prop('checked') ) {
            target.slideUp().addClass('cmb2-tab-ignore');
        } else {
            target.slideDown().removeClass('cmb2-tab-ignore');
        }
    });

    // Initialize on widgets area
    $(document).on('widget-updated widget-added', function(e, widget) {
        // Achievement ajax
        widget.find( gamipress_widget_select2_selector( 'achievement', 'id' ) ).select2( gamipress_widget_select2_achievements );

        // Achievement ajax multiple
        widget.find(
            gamipress_widget_select2_selector( 'achievements', 'include' ) + ', '
            + gamipress_widget_select2_selector( 'achievements', 'exclude' ) + ', '
            + gamipress_widget_select2_selector( 'logs', 'include' ) + ', '
            + gamipress_widget_select2_selector( 'logs', 'exclude' )
        ).select2( gamipress_widget_select2_achievements_multiple );

        // Select2 multiple
        widget.find(
            gamipress_widget_select2_selector( 'achievements', 'type' ) + ', '
            + gamipress_widget_select2_selector( 'points', 'type' ) + ', '
            + gamipress_widget_select2_selector( 'points_types', 'type' ) + ', '
            + gamipress_widget_select2_selector( 'ranks', 'type' )
        ).select2( gamipress_widget_select2_multiple );

        // Rank ajax
        widget.find( gamipress_widget_select2_selector( 'rank', 'id' ) ).select2( gamipress_widget_select2_ranks );

        // Rank ajax multiple
        widget.find(
            gamipress_widget_select2_selector( 'ranks', 'include' ) + ', '
            + gamipress_widget_select2_selector( 'ranks', 'exclude' )
        ).select2( gamipress_widget_select2_ranks_multiple );

        // User ajax
        widget.find( 'select[id^="widget-gamipress"][id$="[user_id]"]:not(.select2-hidden-accessible)' ).select2( gamipress_widget_select2_users );

        // Current user field
        var current_user = widget.find( 'input[id^="widget-gamipress"][id$="[current_user]"]');
        var target = current_user.closest('.cmb-row').next(); // User ID field

        if( current_user.prop('checked') ) {
            target.hide().addClass('cmb2-tab-ignore');
        } else {
            target.show().removeClass('cmb2-tab-ignore');
        }
    });
})(jQuery);