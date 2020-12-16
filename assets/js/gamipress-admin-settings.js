(function ( $ ) {

    // Enable social sharing
    $('body').on('change', '#enable_share', function(e) {
        var target = $(this).closest('.cmb2-metabox').find('.cmb-row:not(.cmb2-id-enable-share)');

        if( $(this).prop('checked') ) {
            target.slideDown('fast');
        } else {
            target.slideUp('fast');
        }

    });

    // Initialize social settings visibility
    if( $('#enable_share').prop('checked') ) {
        $('#cmb2-metabox-social-settings').find('.cmb-row:not(.cmb2-id-enable-share)').show();
    } else {
        $('#cmb2-metabox-social-settings').find('.cmb-row:not(.cmb2-id-enable-share)').hide();
    }

    // Update social buttons preview
    function gamipress_update_social_buttons_preview() {

        // Get social networks selected
        var social_networks = [];

        $('input[name="social_networks[]"]:checked').each(function() {
            social_networks.push( $(this).val() );
        });

        // Get style selected
        var style = $('input[name="social_button_style"]:checked').val();

        $('#social_buttons_preview').find('.gamipress-share-button').each(function() {

            $(this)
                .removeClass('gamipress-share-button-square')
                .removeClass('gamipress-share-button-rounded')
                .removeClass('gamipress-share-button-circle')
                .addClass('gamipress-share-button-' + style);

            if( social_networks.indexOf( $(this).data('network') ) !== -1 ) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });

    }

    // Initialize preview
    gamipress_update_social_buttons_preview();

    // On change a social input, update the buttons preview
    $('body').on('change', 'input[name="social_networks[]"], input[name="social_button_style"]', function(e) {
        gamipress_update_social_buttons_preview();
    });

    // Send test email click
    $('#achievement-earned-email-send, #step-completed-email-send, #points-award-completed-email-send, #points-deduct-completed-email-send, #rank-earned-email-send, #rank-requirement-completed-email-send').on('click', function(e) {
        e.preventDefault();

        var $this = $(this);

        $this.prop( 'disabled', true );

        $this.parent().append('<span class="spinner is-active" style="float:none;"></span>');

        $.ajax({
            url: $this.attr('href'),
            method: 'get',
            success: function( response ) {
                $this.prop( 'disabled', false );
                $this.parent().find('.spinner').remove();
                $this.parent().append('<span class="send-response" ' + ( ! response.success ? 'style="color: #a00;' : '' ) + '">' + response.data + '</span>');

                setTimeout(function() {
                    $this.parent().find('.send-response').remove();
                }, 3000);
            },
            error: function( response ) {
                $this.prop( 'disabled', false );
                $this.parent().find('.spinner').remove();
                $this.parent().append('<span class="send-response" style="color: #a00;">' + response.data + '</span>');

                setTimeout(function() {
                    $this.parent().find('.send-response').remove();
                }, 3000);
            }

        });
    });

    // Disable achievement earned email
    $('#disable_achievement_earned_email').on('change', function() {
        var target = $('.cmb2-id-achievement-earned-email-subject, .cmb2-id-achievement-earned-email-content');

        if( $(this).prop('checked') ) {
            target.slideUp().addClass('cmb2-tab-ignore');
        } else {
            target.slideDown().removeClass('cmb2-tab-ignore');
        }
    } );

    if( $('#disable_achievement_earned_email').prop('checked') ) {
        $('.cmb2-id-achievement-earned-email-subject, .cmb2-id-achievement-earned-email-content').hide().addClass('cmb2-tab-ignore');
    }

    // Disable step completed email
    $('#disable_step_completed_email').on('change', function() {
        var target = $('.cmb2-id-step-completed-email-subject, .cmb2-id-step-completed-email-content');

        if( $(this).prop('checked') ) {
            target.slideUp().addClass('cmb2-tab-ignore');
        } else {
            target.slideDown().removeClass('cmb2-tab-ignore');
        }
    } );

    if( $('#disable_step_completed_email').prop('checked') ) {
        $('.cmb2-id-step-completed-email-subject, .cmb2-id-step-completed-email-content').hide().addClass('cmb2-tab-ignore');
    }

    // Disable points awards completed email
    $('#disable_points_award_completed_email').on('change', function() {
        var target = $('.cmb2-id-points-award-completed-email-subject, .cmb2-id-points-award-completed-email-content');

        if( $(this).prop('checked') ) {
            target.slideUp().addClass('cmb2-tab-ignore');
        } else {
            target.slideDown().removeClass('cmb2-tab-ignore');
        }
    } );

    if( $('#disable_points_award_completed_email').prop('checked') ) {
        $('.cmb2-id-points-award-completed-email-subject, .cmb2-id-points-award-completed-email-content').hide().addClass('cmb2-tab-ignore');
    }

    // Disable points deducts completed email
    $('#disable_points_deduct_completed_email').on('change', function() {
        var target = $('.cmb2-id-points-deduct-completed-email-subject, .cmb2-id-points-deduct-completed-email-content');

        if( $(this).prop('checked') ) {
            target.slideUp().addClass('cmb2-tab-ignore');
        } else {
            target.slideDown().removeClass('cmb2-tab-ignore');
        }
    } );

    if( $('#disable_points_deduct_completed_email').prop('checked') ) {
        $('.cmb2-id-points-deduct-completed-email-subject, .cmb2-id-points-deduct-completed-email-content').hide().addClass('cmb2-tab-ignore');
    }

    // Disable rank earned email
    $('#disable_rank_earned_email').on('change', function() {
        var target = $('.cmb2-id-rank-earned-email-subject, .cmb2-id-rank-earned-email-content');

        if( $(this).prop('checked') ) {
            target.slideUp().addClass('cmb2-tab-ignore');
        } else {
            target.slideDown().removeClass('cmb2-tab-ignore');
        }
    } );

    if( $('#disable_rank_earned_email').prop('checked') ) {
        $('.cmb2-id-rank-earned-email-subject, .cmb2-id-rank-earned-email-content').hide().addClass('cmb2-tab-ignore');
    }

    // Disable rank requirement completed email
    $('#disable_rank_requirement_completed_email').on('change', function() {
        var target = $('.cmb2-id-rank-requirement-completed-email-subject, .cmb2-id-rank-requirement-completed-email-content');

        if( $(this).prop('checked') ) {
            target.slideUp().addClass('cmb2-tab-ignore');
        } else {
            target.slideDown().removeClass('cmb2-tab-ignore');
        }
    } );

    if( $('#disable_rank_requirement_completed_email').prop('checked') ) {
        $('.cmb2-id-rank-requirement-completed-email-subject, .cmb2-id-rank-requirement-completed-email-content').hide().addClass('cmb2-tab-ignore');
    }

    // Automatic updates
    $('#automatic_updates').on('change', function() {
        var target = $('.cmb2-id-automatic-updates-plugins');

        if( target.length ) {
            if( $(this).prop('checked') ) {
                target.slideDown();
            } else {
                target.slideUp();
            }
        }
    } );

    if( ! $('#automatic_updates').prop('checked') ) {
        $('.cmb2-id-automatic-updates-plugins').hide();
    }

    // Fixed action buttons on settings
    $(window).on('scroll', function(e) {

        if( $(window).scrollTop() + $(window).height() > $(document).height() - 70 ) {
            $('.gamipress_settings input[name="submit-cmb"]').parent().removeClass('gamipress-sticky-bar')
        } else {
            $('.gamipress_settings input[name="submit-cmb"]').parent().addClass('gamipress-sticky-bar')
        }

    });

    $(window).trigger('scroll')

})( jQuery );

