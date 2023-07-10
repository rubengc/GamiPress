(function( $ ) {

    // Listen for our change to our trigger type selectors
    $('.requirements-list').on( 'change', '.select-trigger-type', function() {

        // Grab our selected trigger type and achievement selector
        var trigger_type = $(this).val();
        var tutor_category = $(this).siblings('.select-tutor-category');

        if( trigger_type === 'gamipress_tutor_complete_quiz_course_category'
            || trigger_type === 'gamipress_tutor_pass_quiz_course_category'
            || trigger_type === 'gamipress_tutor_fail_quiz_course_category'
            || trigger_type === 'gamipress_tutor_complete_lesson_course_category'
            || trigger_type === 'gamipress_tutor_complete_course_category'
            || trigger_type === 'gamipress_tutor_enroll_course_category' ) {
                tutor_category.show();
        } else {
            tutor_category.hide();
        }

    });

    // Loop requirement list items to show/hide amount input on initial load
    $('.requirements-list li').each(function() {

        // Grab our selected trigger type and achievement selector
        var trigger_type = $(this).find('.select-trigger-type').val();
        var tutor_category = $(this).find('.select-tutor-category');

        if( trigger_type === 'gamipress_tutor_complete_quiz_course_category'
            || trigger_type === 'gamipress_tutor_pass_quiz_course_category'
            || trigger_type === 'gamipress_tutor_fail_quiz_course_category'
            || trigger_type === 'gamipress_tutor_complete_lesson_course_category'
            || trigger_type === 'gamipress_tutor_complete_course_category'
            || trigger_type === 'gamipress_tutor_enroll_course_category' ) {
                tutor_category.show();
        } else {
            tutor_category.hide();
        }

    });

    $('.requirements-list').on( 'update_requirement_data', '.requirement-row', function(e, requirement_details, requirement) {

        if( requirement_details.trigger_type === 'gamipress_tutor_complete_quiz_course_category'
            || requirement_details.trigger_type === 'gamipress_tutor_pass_quiz_course_category'
            || requirement_details.trigger_type === 'gamipress_tutor_fail_quiz_course_category'
            || requirement_details.trigger_type === 'gamipress_tutor_complete_lesson_course_category'
            || requirement_details.trigger_type === 'gamipress_tutor_complete_course_category'
            || requirement_details.trigger_type === 'gamipress_tutor_enroll_course_category' ) {
            requirement_details.tutor_category = requirement.find( '.select-tutor-category' ).val();
        }

    });

})( jQuery );