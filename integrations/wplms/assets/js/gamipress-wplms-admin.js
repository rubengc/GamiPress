(function( $ ) {

    // Listen for our change to our trigger type selectors
    $('.requirements-list').on( 'change', '.select-trigger-type', function() {

        // Grab our selected trigger type and achievement selector
        var trigger_type = $(this).val();
        var score_input = $(this).siblings('.wplms-score');

        if(
            trigger_type === 'gamipress_wplms_complete_course_minimum_mark'
            || trigger_type === 'gamipress_wplms_complete_specific_course_minimum_mark'
            || trigger_type === 'gamipress_wplms_complete_quiz_minimum_mark'
            || trigger_type === 'gamipress_wplms_complete_specific_quiz_minimum_mark'
            || trigger_type === 'gamipress_wplms_complete_assignment_minimum_mark'
            || trigger_type === 'gamipress_wplms_complete_specific_assignment_minimum_mark'
        ) {
            score_input.show();
        } else {
            score_input.hide();
        }

    });

    // Loop requirement list items to show/hide score input on initial load
    $('.requirements-list li').each(function() {

        // Grab our selected trigger type and achievement selector
        var trigger_type = $(this).find('.select-trigger-type').val();
        var score_input = $(this).find('.wplms-score');

        if(
            trigger_type === 'gamipress_wplms_complete_course_minimum_mark'
            || trigger_type === 'gamipress_wplms_complete_specific_course_minimum_mark'
            || trigger_type === 'gamipress_wplms_complete_quiz_minimum_mark'
            || trigger_type === 'gamipress_wplms_complete_specific_quiz_minimum_mark'
            || trigger_type === 'gamipress_wplms_complete_assignment_minimum_mark'
            || trigger_type === 'gamipress_wplms_complete_specific_assignment_minimum_mark'
        ) {
            score_input.show();
        } else {
            score_input.hide();
        }

    });

    $('.requirements-list').on( 'update_requirement_data', '.requirement-row', function(e, requirement_details, requirement) {

        if(
            requirement_details.trigger_type === 'gamipress_wplms_complete_course_minimum_mark'
            || requirement_details.trigger_type === 'gamipress_wplms_complete_specific_course_minimum_mark'
            || requirement_details.trigger_type === 'gamipress_wplms_complete_quiz_minimum_mark'
            || requirement_details.trigger_type === 'gamipress_wplms_complete_specific_quiz_minimum_mark'
            || requirement_details.trigger_type === 'gamipress_wplms_complete_assignment_minimum_mark'
            || requirement_details.trigger_type === 'gamipress_wplms_complete_specific_assignment_minimum_mark'
        ) {
            requirement_details.wplms_score = requirement.find( '.wplms-score input' ).val();
        }

    });

})( jQuery );