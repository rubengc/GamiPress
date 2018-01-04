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
    $( '#widgets-right ' + gamipress_widget_select2_selector( 'achievement', 'id' ) ).select2( gamipress_widget_select2_achievements );

    // Achievement ajax multiple
    $(  '#widgets-right ' + gamipress_widget_select2_selector( 'achievements', 'include' ) + ', '
        + '#widgets-right ' + gamipress_widget_select2_selector( 'achievements', 'exclude' ) + ', '
        + '#widgets-right ' + gamipress_widget_select2_selector( 'logs', 'include' ) + ', '
        + '#widgets-right ' + gamipress_widget_select2_selector( 'logs', 'exclude' )
    ).select2( gamipress_widget_select2_achievements_multiple );

    // Select2 multiple
    $(  '#widgets-right ' + gamipress_widget_select2_selector( 'achievements', 'type' ) + ', '
        + '#widgets-right ' + gamipress_widget_select2_selector( 'points', 'type' ) + ', '
        + '#widgets-right ' + gamipress_widget_select2_selector( 'points_types', 'type' ) + ', '
        + '#widgets-right ' + gamipress_widget_select2_selector( 'ranks', 'type' ) + ', '
        + '#widgets-right ' + gamipress_widget_select2_selector( 'earnings', 'points_types' ) + ', '
        + '#widgets-right ' + gamipress_widget_select2_selector( 'earnings', 'achievement_types' ) + ', '
        + '#widgets-right ' + gamipress_widget_select2_selector( 'earnings', 'rank_types' )
    ).select2( gamipress_widget_select2_multiple );

    // Rank ajax
    $( '#widgets-right ' + gamipress_widget_select2_selector( 'rank', 'id' ) ).select2( gamipress_widget_select2_ranks );

    // Rank ajax multiple
    $(  '#widgets-right ' + gamipress_widget_select2_selector( 'ranks', 'include' ) + ', '
        + '#widgets-right ' + gamipress_widget_select2_selector( 'ranks', 'exclude' )
    ).select2( gamipress_widget_select2_ranks_multiple );

    // User ajax
    $( '#widgets-right select[id^="widget-gamipress"][id$="[user_id]"]:not(.select2-hidden-accessible)' ).select2( gamipress_widget_select2_users );

    // Current user field
    $('body').on('change', 'input[id^="widget-gamipress"][id$="[current_user]"]', function() {
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

    // User earnings
    $('body').on('change', 'input[id^="widget-gamipress_earnings"][id$="[points]"], '
        + 'input[id^="widget-gamipress_earnings"][id$="[achievements]"], '
        + 'input[id^="widget-gamipress_earnings"][id$="[ranks]"]', function() {

        var id_parts = $(this).attr('id').split('[');
        var id = id_parts[id_parts.length - 1].replace(']', '');
        var n = $(this).closest('form').find('input[name="widget_number"]').val();
        var target = undefined;

        if( id === 'points' ) {
            target = $('.cmb2-id-widget-gamipress-earnings-widget' + n + 'points-types, .cmb2-id-widget-gamipress-earnings-widget' + n + 'awards, .cmb2-id-widget-gamipress-earnings-widget' + n + 'deducts');
        } else if( id === 'achievements' ) {
            target = $('.cmb2-id-widget-gamipress-earnings-widget' + n + 'achievement-types, .cmb2-id-widget-gamipress-earnings-widget' + n + 'steps');
        } else if( id === 'ranks' ) {
            target = $('.cmb2-id-widget-gamipress-earnings-widget' + n + 'rank-types, .cmb2-id-widget-gamipress-earnings-widget' + n + 'rank-requirements');
        }

        if( $(this).prop('checked') ) {
            // Just show if current tab active is ours
            if( $(this).closest('.cmb-tabs-wrap').find('.cmb-tab.active[id$="[' + id + ']"]').length ) {
                target.slideDown();
            }

            target.removeClass('cmb2-tab-ignore');
        } else {
            target.slideUp().addClass('cmb2-tab-ignore');
        }
    });

    $('input[id^="widget-gamipress_earnings"][id$="[points]"], '
        + 'input[id^="widget-gamipress_earnings"][id$="[achievements]"], '
        + 'input[id^="widget-gamipress_earnings"][id$="[ranks]"]').change();

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
            + gamipress_widget_select2_selector( 'ranks', 'type' ) + ', '
            + gamipress_widget_select2_selector( 'earnings', 'points_types' ) + ', '
            + gamipress_widget_select2_selector( 'earnings', 'achievement_types' ) + ', '
            + gamipress_widget_select2_selector( 'earnings', 'rank_types' )
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
            if( target.closest('.cmb-tabs-wrap').length ) {
                // Just show if item tab is active
                if( target.hasClass('cmb-tab-active-item') ) {
                    target.show();
                }
            } else {
                target.show();
            }

            target.removeClass('cmb2-tab-ignore');
        }

        // User earnings
        widget.find('change', 'input[id^="widget-gamipress_earnings"][id$="[points]"], '
            + 'input[id^="widget-gamipress_earnings"][id$="[achievements]"], '
            + 'input[id^="widget-gamipress_earnings"][id$="[ranks]"]').each(function() {

            var id_parts = $(this).attr('id').split('[');
            var id = id_parts[id_parts.length - 1].replace(']', '');
            var n = $(this).closest('form').find('input[name="widget_number"]').val();
            var target = undefined;

            if( id === 'points' ) {
                target = $('.cmb2-id-widget-gamipress-earnings-widget' + n + 'points-types, .cmb2-id-widget-gamipress-earnings-widget' + n + 'awards, .cmb2-id-widget-gamipress-earnings-widget' + n + 'deducts');
            } else if( id === 'achievements' ) {
                target = $('.cmb2-id-widget-gamipress-earnings-widget' + n + 'achievement-types, .cmb2-id-widget-gamipress-earnings-widget' + n + 'steps');
            } else if( id === 'ranks' ) {
                target = $('.cmb2-id-widget-gamipress-earnings-widget' + n + 'rank-types, .cmb2-id-widget-gamipress-earnings-widget' + n + 'rank-requirements');
            }

            if( $(this).prop('checked') ) {
                // Just show if current tab active is ours
                if( $(this).closest('.cmb-tabs-wrap').find('.cmb-tab.active[id$="[' + id + ']"]').length ) {
                    target.slideDown();
                }

                target.removeClass('cmb2-tab-ignore');
            } else {
                target.slideUp().addClass('cmb2-tab-ignore');
            }
        });
    });
})(jQuery);