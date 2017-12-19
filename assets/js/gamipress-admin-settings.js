(function ( $ ) {

    // Send test email click
    $('#achievement-earned-email-send, #step-completed-email-send, #points-award-completed-email-send, #points-deduct-completed-email-send, #rank-earned-email-send, #rank-requirement-completed-email-send').click(function(e) {
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
    $('#disable_achievement_earned_email').change( function() {
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
    $('#disable_step_completed_email').change( function() {
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
    $('#disable_points_award_completed_email').change( function() {
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
    $('#disable_points_deduct_completed_email').change( function() {
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
    $('#disable_rank_earned_email').change( function() {
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
    $('#disable_rank_requirement_completed_email').change( function() {
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
    $('#automatic_updates').change( function() {
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

})( jQuery );

