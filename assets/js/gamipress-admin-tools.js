(function( $ ) {

    // ----------------------------------
    // Reset Data Tool
    // ----------------------------------

    var reset_data_dialog = $("#reset-data-dialog");

    reset_data_dialog.dialog({
        dialogClass   : 'wp-dialog',
        modal         : true,
        autoOpen      : false,
        closeOnEscape : true,
        draggable     : false,
        width         : 500,
        buttons       : [
            {
                text: "Yes, delete it permanently",
                class: "button-primary reset-data-button",
                click: function() {
                    $('.reset-data-button').prop('disabled', true);

                    $('.reset-data-button').parent().parent().prepend('<span id="reset-data-response"><span class="spinner is-active" style="float: none;"></span></span>');

                    var items = [];

                    $('.cmb2-id-data-to-reset input:checked').each(function() {
                        items.push($(this).val());
                    });

                    $.post(
                        ajaxurl,
                        {
                            action: 'gamipress_reset_data_tool',
                            items: items
                        },
                        function( response ) {

                            if( response.success === false ) {
                                $('#reset-data-response').css({color:'#a00'});
                            }

                            $('#reset-data-response').html(response.data);

                            if( response.success === true ) {

                                setTimeout(function() {
                                    $('.cmb2-id-data-to-reset input:checked').each(function() {
                                        $(this).prop( 'checked', false );
                                    });

                                    $('#reset-data-response').remove();

                                    reset_data_dialog.dialog( "close" );
                                }, 2000);
                            }

                            $('.reset-data-button').prop('disabled', false);
                        }
                    );
                }
            },
            {
                text: "Cancel",
                class: "cancel-reset-data-button",
                click: function() {
                    $( this ).dialog( "close" );
                }
            }

        ]
    });

    $("#reset_data").click(function(e) {
        e.preventDefault();

        $('#reset-data-warning').remove();

        var checked_options = $('.cmb2-id-data-to-reset input:checked');

        if( checked_options.length ) {

            var reminder_html = '';

            checked_options.each(function() {
                reminder_html += '<li>' + $(this).next().text() + '</li>'
            });

            // Add a reminder with data to be removed
            $('#reset-data-reminder').html('<ul>' + reminder_html + '</ul>');

            // Open our dialog
            reset_data_dialog.dialog('open');

            // Remove the initial jQuery UI Dialog auto focus
            $('.ui-dialog :button').blur();
        } else {
            $(this).parent().prepend('<p id="reset-data-warning" class="cmb2-metabox-description" style="color: #a00;">You need to choose at least one option.</p>');
        }
    });

    $('.cmb2-id-data-to-reset').on('change', 'input', function() {

        $('#reset-data-warning').remove();

        var checked_option = $(this).val();

        if( checked_option === 'achievement_types' ) {
            $('.cmb2-id-data-to-reset input[value="achievements"], .cmb2-id-data-to-reset input[value="steps"]').prop( 'checked', $(this).prop( 'checked' ) );
        } else if( checked_option === 'achievements' ) {
            $('.cmb2-id-data-to-reset input[value="steps"]').prop( 'checked', $(this).prop( 'checked' ) );
        } else if( checked_option === 'points_types' ) {
            $('.cmb2-id-data-to-reset input[value="points_awards"]').prop( 'checked', $(this).prop( 'checked' ) );
            $('.cmb2-id-data-to-reset input[value="points_deducts"]').prop( 'checked', $(this).prop( 'checked' ) );
        } else if( checked_option === 'rank_types' ) {
            $('.cmb2-id-data-to-reset input[value="ranks"], .cmb2-id-data-to-reset input[value="rank_requirements"]').prop( 'checked', $(this).prop( 'checked' ) );
        } else if( checked_option === 'ranks' ) {
            $('.cmb2-id-data-to-reset input[value="rank_requirements"]').prop( 'checked', $(this).prop( 'checked' ) );
        } else if( checked_option === 'earnings' ) {
            $('.cmb2-id-data-to-reset input[value="earned_points"], .cmb2-id-data-to-reset input[value="earned_achievements"], .cmb2-id-data-to-reset input[value="earned_ranks"]').prop( 'checked', $(this).prop( 'checked' ) );
        }

    });

    // ----------------------------------
    // Import Settings Tool
    // ----------------------------------

    $('#import_settings').click(function(e) {
        e.preventDefault();

        $('#import-settings-warning').remove();

        if( $('#import_settings_file')[0].files[0] === undefined ) {
            $(this).parent().prepend('<p id="import-settings-warning" class="cmb2-metabox-description" style="color: #a00;">You need to choose a configuration file to import.</p>');
            return false;
        }

        var $this = $(this);
        var form_data = new FormData();
        form_data.append( 'action', 'gamipress_import_settings_tool' );
        form_data.append( 'file', $('#import_settings_file')[0].files[0] );

        // Disable the button
        $this.prop('disabled', true);

        // Show the spinner
        $this.parent().append('<span id="import-settings-response"><span class="spinner is-active" style="float: none;"></span></span>');

        $.ajax({
            url: ajaxurl,
            method: 'post',
            cache: false,
            contentType: false,
            processData: false,
            data: form_data,
            success: function(response) {

                if( response.success === false ) {
                    $('#import-settings-response').css({color:'#a00'});
                }

                $('#import-settings-response').html(response.data);

                if( response.success === true ) {

                    setTimeout(function() {
                        $('#import-settings-response').remove();
                    }, 2000);
                }

                $this.prop('disabled', false);

            }
        });
    });

    // ----------------------------------
    // Recount Activity Tool
    // ----------------------------------

    function gamipress_run_recount_activity_tool( loop ) {

        if( loop === undefined ) {
            loop = 0;
        }

        var button = $("#recount_activity");
        var activity = $('#activity_to_recount').val();

        $.post(
            ajaxurl,
            {
                action: 'gamipress_recount_activity_tool',
                activity: activity,
                loop: loop // Used on run again utility to let know to the tool in which loop we are now
            },
            function( response ) {

                // Run again utility
                if( response.data.run_again !== undefined && response.data.run_again && response.success === true ) {

                    var running_selector = '#recount-activity-response #running-' + activity;

                    if( ! $(running_selector).length ) {
                        $('#recount-activity-response').append( '<span id="running-' + activity + '"></span>' );
                    }

                    $(running_selector).html( response.data.message );

                    loop++;

                    // Run again passing the next loop index
                    gamipress_run_recount_activity_tool( loop );

                    return;
                }

                $('#recount-activity-notice').remove();

                if( response.success === false ) {
                    $('#recount-activity-response').css({color:'#a00'});
                }

                $('#recount-activity-response').html(response.data);

                if( response.success === true ) {

                    setTimeout(function() {
                        $('#recount-activity-response').remove();
                    }, 2000);
                }

                // Enable the button and the activity select
                button.prop('disabled', false);
                $('#activity_to_recount').prop('disabled', false);
            }
        ).fail(function() {

            $('#recount-activity-response').html('The server has returned an internal error.');

            setTimeout(function() {
                $('#recount-activity-notice').remove();
                $('#recount-activity-response').remove();
            }, 5000);

            // Enable the button and the activity select
            button.prop('disabled', false);
            $('#activity_to_recount').prop('disabled', false);
        });
    }

    $("#recount_activity").click(function(e) {
        e.preventDefault();

        $('#recount-activity-warning').remove();

        if( $('#activity_to_recount').val() === '' ) {
            $(this).parent().prepend('<p id="recount-activity-warning" class="cmb2-metabox-description" style="color: #a00;">You need to choose an activity to recount.</p>');
            return false;
        }

        var $this = $(this);

        // Disable the button and the activity select
        $this.prop('disabled', true);
        $('#activity_to_recount').prop('disabled', true);

        // Show a notice to let know to the user that process could take a while
        $this.parent().prepend('<p id="recount-activity-notice" class="cmb2-metabox-description">' + gamipress_admin_tools.recount_activity_notice + '</p>');

        if( ! $('#recount-activity-response').length ) {
            $this.parent().append('<span id="recount-activity-response"></span>');
        }

        // Show the spinner
        $('#recount-activity-response').html('<span class="spinner is-active" style="float: none;"></span>');

        // Make the ajax request
        gamipress_run_recount_activity_tool();
    });

    // Bulk Awards/Revokes Tool

    // Award to all users
    $('#bulk-awards, #bulk-revokes').on('change',
        '#bulk_award_points_all_users, #bulk_award_achievements_all_users, #bulk_award_rank_all_users, '
        + '#bulk_revoke_points_all_users, #bulk_revoke_achievements_all_users, #bulk_revoke_rank_all_users'
        , function() {

        var target = $('#' + $(this).attr('id').replace('_all', '')).closest('.cmb-row');

        if( $(this).prop('checked') ) {
            target.slideUp(250).addClass('cmb2-tab-ignore');
        } else {
            target.slideDown(250).removeClass('cmb2-tab-ignore');
        }

    });

    // Achievements ajax
    $('#bulk_award_achievements, #bulk_revoke_achievements').select2({
        ajax: {
            url: ajaxurl,
            dataType: 'json',
            delay: 250,
            type: 'POST',
            cache: true,
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
        placeholder: gamipress_admin_tools.achievements_placeholder,
        allowClear: true,
        closeOnSelect: false,
        multiple: true
    });

    // Rank ajax
    $('#bulk_award_rank, #bulk_revoke_rank').select2({
        ajax: {
            url: ajaxurl,
            dataType: 'json',
            delay: 250,
            type: 'POST',
            cache: true,
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
        placeholder: gamipress_admin_tools.rank_placeholder,
        allowClear: true,
        multiple: false
    });

    // User ajax
    $( '#bulk_award_points_users, #bulk_award_achievements_users, #bulk_award_rank_users, '
        + '#bulk_revoke_points_users, #bulk_revoke_achievements_users, #bulk_revoke_rank_users').select2({
        ajax: {
            url: ajaxurl,
            dataType: 'json',
            delay: 250,
            type: 'POST',
            cache: true,
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
        placeholder: gamipress_admin_tools.users_placeholder,
        allowClear: true,
        closeOnSelect: false,
        multiple: true
    });

    function gamipress_run_bulk_tool( button, loop ) {

        if( loop === undefined ) {
            loop = 0;
        }

        var response_id = button.attr('id').replace('_button', '_response');
        var active_tab = button.closest('.cmb-tabs-wrap').find('.cmb-tab.active');
        var action = ( button.attr('id').indexOf('bulk_award_') !== -1 ? 'bulk_award' : 'bulk_revoke' );
        var data;

        if( action === 'bulk_award' ) {
            data = {
                action: 'gamipress_bulk_awards_tool',
                bulk_award: button.attr('id').replace('bulk_award_', '').replace('_button', ''),
                loop: loop
            };
        } else if( action === 'bulk_revoke' ) {
            data = {
                action: 'gamipress_bulk_revokes_tool',
                bulk_revoke: button.attr('id').replace('bulk_revoke_', '').replace('_button', ''),
                loop: loop
            };
        }


        // Loop all fields to build the request data
        $(active_tab.data('fields')).find('input, select, textarea').each(function() {

            if( $(this).attr('type') === 'checkbox' ) {
                // Checkboxes are sent just when checked
                if( $(this).prop('checked') ) {
                    data[$(this).attr('name')] = $(this).val();
                }
            } else {
                data[$(this).attr('name')] = $(this).val();
            }

        });

        // Disable the button
        button.prop('disabled', true);

        if( ! $('#' + response_id).length ) {
            button.parent().append('<span id="' + response_id + '" style="display: inline-block; padding: 5px 0 0 8px;"></span>');
        }

        if( ! $('#' + response_id).find('.spinner').length ) {
            // Show the spinner
            $('#' + response_id).html('<span class="spinner is-active" style="float: none; margin: 0;"></span>');
        }

        $.post(
            ajaxurl,
            data,
            function( response ) {

                // Run again utility
                if( response.data.run_again !== undefined && response.data.run_again && response.success === true ) {

                    if( ! $('#' + response_id).find('#' + response_id + '-message').length ) {
                        $('#' + response_id).append('<span id="' + response_id + '-message" style="padding-left: 5px;"></span>');
                    }

                    $('#' + response_id).find('#' + response_id + '-message').html(response.data.message);

                    loop++;

                    // Run again passing the next loop index
                    gamipress_run_bulk_tool( button, loop );

                    return;
                }

                if( response.success === false ) {
                    $('#' + response_id).css({color:'#a00'});
                }

                $('#' + response_id).html(response.data);

                if( response.success !== false ) {
                    loop++;
                }

                // Enable the button
                button.prop('disabled', false);
            }
        ).fail(function() {

            $('#' + response_id).html('The server has returned an internal error.');

            // Enable the button
            button.prop('disabled', false);
        });

    }

    $('#bulk_award_points_button, #bulk_award_achievements_button, #bulk_award_rank_button, '
        + '#bulk_revoke_points_button, #bulk_revoke_achievements_button, #bulk_revoke_rank_button').click(function(e) {
        e.preventDefault();

        gamipress_run_bulk_tool( $(this) );
    });

})( jQuery );