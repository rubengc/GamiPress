(function( $ ) {
    // Clean Data Tool
    $("#search_data_to_clean").click(function(e) {
        e.preventDefault();

        var $this = $(this);

        $this.prop('disabled', true);

        // Show the spinner
        $this.parent().append('<span id="clean-data-response"><span class="spinner is-active" style="float: none;"></span></span>');

        $.post(
            ajaxurl,
            {
                action: 'gamipress_search_data_to_clean'
            },
            function( response ) {
                if( response.success === false ) {
                    $('#clean-data-response').css({color:'#a00'});
                }

                $('#clean-data-response').html(response.data.message);

                if( response.success === true && response.data.found_results > 0 ) {

                    $this.hide();
                    $("#clean_data").show();
                }

                $this.prop('disabled', false);
            }
        );
    });

    $("#clean_data").hide();

    $("#clean_data").click(function(e) {
        e.preventDefault();

        var $this = $(this);

        $this.prop('disabled', true);

        // Show the spinner
        $('#clean-data-response').html('<span class="spinner is-active" style="float: none;"></span>');

        $.post(
            ajaxurl,
            {
                action: 'gamipress_clean_data_tool'
            },
            function( response ) {
                if( response.success === false ) {
                    $('#clean-data-response').css({color:'#a00'});
                }

                $('#clean-data-response').html(response.data);

                //$this.prop('disabled', false);
            }
        );
    });

    // Reset Data Tool
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
        } else if( checked_option === 'rank_types' ) {
            $('.cmb2-id-data-to-reset input[value="ranks"], .cmb2-id-data-to-reset input[value="rank_requirements"]').prop( 'checked', $(this).prop( 'checked' ) );
        } else if( checked_option === 'ranks' ) {
            $('.cmb2-id-data-to-reset input[value="rank_requirements"]').prop( 'checked', $(this).prop( 'checked' ) );
        }

    });

    // Import Settings Tool
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

    // Recount Activity Tool
    $("#recount_activity").click(function(e) {
        e.preventDefault();

        $('#recount-activity-warning').remove();

        if( $('#activity_to_recount').val() === '' ) {
            $(this).parent().prepend('<p id="recount-activity-warning" class="cmb2-metabox-description" style="color: #a00;">You need to choose an activity to recount.</p>');
            return false;
        }

        var $this = $(this);
        var activity = $('#activity_to_recount').val();

        // Disable the button
        $this.prop('disabled', true);

        // Show the spinner
        $this.parent().prepend('<p id="recount-activity-notice" class="cmb2-metabox-description">' + gamipress_admin_tools.recount_activity_notice + '</p>');
        $this.parent().append('<span id="recount-activity-response"><span class="spinner is-active" style="float: none;"></span></span>');

        $.post(
            ajaxurl,
            {
                action: 'gamipress_recount_activity_tool',
                activity: activity
            },
            function( response ) {

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

                // Enable the button
                $this.prop('disabled', false);
            }
        ).fail(function() {

            $('#recount-activity-response').html('The server has returned an internal error.');

            setTimeout(function() {
                $('#recount-activity-notice').remove();
                $('#recount-activity-response').remove();
            }, 5000);

            // Enable the button
            $this.prop('disabled', false);
        });
    });
})( jQuery );