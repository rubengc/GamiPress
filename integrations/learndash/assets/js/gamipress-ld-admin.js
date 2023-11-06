(function( $ ) {

    // Listen for our change to our trigger type selectors
    $('.requirements-list').on( 'change', '.select-trigger-type', function() {

        // Grab our selected trigger type and achievement selector
        var trigger_type = $(this).val();
        var score_input = $(this).siblings('.ld-quiz-score');
        var min_score_input = $(this).siblings('.ld-quiz-min-score');
        var max_score_input = $(this).siblings('.ld-quiz-max-score');

        // Toggle score field visibility
        if(
            trigger_type === 'gamipress_ld_complete_quiz_grade'
            || trigger_type === 'gamipress_ld_complete_specific_quiz_grade'
            || trigger_type === 'gamipress_ld_complete_quiz_specific_course_grade'
            || trigger_type === 'gamipress_ld_complete_quiz_course_category_grade'
            || trigger_type === 'gamipress_ld_complete_quiz_course_tag_grade'
            || trigger_type === 'gamipress_ld_complete_quiz_max_grade'
            || trigger_type === 'gamipress_ld_complete_specific_quiz_max_grade'
            || trigger_type === 'gamipress_ld_complete_specific_quiz_grade'
            || trigger_type === 'gamipress_ld_complete_quiz_specific_course_max_grade'
            || trigger_type === 'gamipress_ld_complete_quiz_course_category_max_grade'
            || trigger_type === 'gamipress_ld_complete_quiz_course_tag_max_grade'
        ) {
            score_input.show();
        } else {
            score_input.hide();
        }

        // Toggle min and max score fields visibility
        if(
            trigger_type === 'gamipress_ld_complete_quiz_between_grade'
            || trigger_type === 'gamipress_ld_complete_specific_quiz_between_grade'
            || trigger_type === 'gamipress_ld_complete_quiz_specific_course_between_grade'
            || trigger_type === 'gamipress_ld_complete_quiz_course_category_between_grade'
            || trigger_type === 'gamipress_ld_complete_quiz_course_tag_between_grade'
        ) {
            min_score_input.show();
            max_score_input.show();
        } else {
            min_score_input.hide();
            max_score_input.hide();
        }

        // Course category
        var course_category_select = $(this).siblings('.ld-course-category');

        if(
            trigger_type === 'gamipress_ld_complete_quiz_course_category'
            || trigger_type === 'gamipress_ld_complete_quiz_course_category_grade'
            || trigger_type === 'gamipress_ld_complete_quiz_course_category_max_grade'
            || trigger_type === 'gamipress_ld_complete_quiz_course_category_between_grade'
            || trigger_type === 'gamipress_ld_pass_quiz_course_category'
            || trigger_type === 'gamipress_ld_fail_quiz_course_category'
            || trigger_type === 'gamipress_ld_complete_topic_course_category'
            || trigger_type === 'gamipress_ld_assignment_upload_course_category'
            || trigger_type === 'gamipress_ld_assignment_upload_course_category'
            || trigger_type === 'gamipress_ld_approve_assignment_course_category'
            || trigger_type === 'gamipress_ld_complete_lesson_course_category'
            || trigger_type === 'gamipress_ld_complete_course_category'
        ) {
            course_category_select.show();
        } else {
            course_category_select.hide();
        }

        // Course tag
        var course_tag_select = $(this).siblings('.ld-course-tag');

        if(
            trigger_type === 'gamipress_ld_complete_quiz_course_tag'
            || trigger_type === 'gamipress_ld_complete_quiz_course_tag_grade'
            || trigger_type === 'gamipress_ld_complete_quiz_course_tag_max_grade'
            || trigger_type === 'gamipress_ld_complete_quiz_course_tag_between_grade'
            || trigger_type === 'gamipress_ld_pass_quiz_course_tag'
            || trigger_type === 'gamipress_ld_fail_quiz_course_tag'
            || trigger_type === 'gamipress_ld_complete_topic_course_tag'
            || trigger_type === 'gamipress_ld_assignment_upload_course_tag'
            || trigger_type === 'gamipress_ld_assignment_upload_course_tag'
            || trigger_type === 'gamipress_ld_approve_assignment_course_tag'
            || trigger_type === 'gamipress_ld_complete_lesson_course_tag'
            || trigger_type === 'gamipress_ld_complete_course_tag'
        ) {
            course_tag_select.show();
        } else {
            course_tag_select.hide();
        }

        // Topic category
        var topic_category_select = $(this).siblings('.ld-topic-category');

        if(
            trigger_type === 'gamipress_ld_complete_topic_category'
        ) {
            topic_category_select.show();
        } else {
            topic_category_select.hide();
        }

        // Topic tag
        var topic_tag_select = $(this).siblings('.ld-topic-tag');

        if(
            trigger_type === 'gamipress_ld_complete_topic_tag'
        ) {
            topic_tag_select.show();
        } else {
            topic_tag_select.hide();
        }

        // Lesson category
        var lesson_category_select = $(this).siblings('.ld-lesson-category');

        if(
            trigger_type === 'gamipress_ld_assignment_upload_lesson_category'
            || trigger_type === 'gamipress_ld_approve_assignment_lesson_category'
            || trigger_type === 'gamipress_ld_complete_lesson_category'
        ) {
            lesson_category_select.show();
        } else {
            lesson_category_select.hide();
        }

        // Lesson tag
        var lesson_tag_select = $(this).siblings('.ld-lesson-tag');

        if(
            trigger_type === 'gamipress_ld_assignment_upload_lesson_tag'
            || trigger_type === 'gamipress_ld_approve_assignment_lesson_tag'
            || trigger_type === 'gamipress_ld_complete_lesson_tag'
        ) {
            lesson_tag_select.show();
        } else {
            lesson_tag_select.hide();
        }

    });

    // Loop requirement list items to show/hide score input on initial load
    $('.requirements-list li').each(function() {

        // Grab our selected trigger type and achievement selector
        var trigger_type = $(this).find('.select-trigger-type').val();
        var score_input = $(this).find('.ld-quiz-score');
        var min_score_input = $(this).find('.ld-quiz-min-score');
        var max_score_input = $(this).find('.ld-quiz-max-score');

        // Toggle score field visibility
        if(
            trigger_type === 'gamipress_ld_complete_quiz_grade'
            || trigger_type === 'gamipress_ld_complete_specific_quiz_grade'
            || trigger_type === 'gamipress_ld_complete_quiz_specific_course_grade'
            || trigger_type === 'gamipress_ld_complete_quiz_course_category_grade'
            || trigger_type === 'gamipress_ld_complete_quiz_course_tag_grade'
            || trigger_type === 'gamipress_ld_complete_quiz_max_grade'
            || trigger_type === 'gamipress_ld_complete_specific_quiz_max_grade'
            || trigger_type === 'gamipress_ld_complete_quiz_specific_course_max_grade'
            || trigger_type === 'gamipress_ld_complete_quiz_course_category_max_grade'
            || trigger_type === 'gamipress_ld_complete_quiz_course_tag_max_grade'
        ) {
            score_input.show();
        } else {
            score_input.hide();
        }

        // Toggle min and max score fields visibility
        if(
            trigger_type === 'gamipress_ld_complete_quiz_between_grade'
            || trigger_type === 'gamipress_ld_complete_specific_quiz_between_grade'
            || trigger_type === 'gamipress_ld_complete_quiz_specific_course_between_grade'
            || trigger_type === 'gamipress_ld_complete_quiz_course_category_between_grade'
            || trigger_type === 'gamipress_ld_complete_quiz_course_tag_between_grade'
        ) {
            min_score_input.show();
            max_score_input.show();
        } else {
            min_score_input.hide();
            max_score_input.hide();
        }

        // Course category
        var course_category_select = $(this).find('.ld-course-category');

        if(
            trigger_type === 'gamipress_ld_complete_quiz_course_category'
            || trigger_type === 'gamipress_ld_complete_quiz_course_category_grade'
            || trigger_type === 'gamipress_ld_complete_quiz_course_category_max_grade'
            || trigger_type === 'gamipress_ld_complete_quiz_course_category_between_grade'
            || trigger_type === 'gamipress_ld_pass_quiz_course_category'
            || trigger_type === 'gamipress_ld_fail_quiz_course_category'
            || trigger_type === 'gamipress_ld_complete_topic_course_category'
            || trigger_type === 'gamipress_ld_assignment_upload_course_category'
            || trigger_type === 'gamipress_ld_approve_assignment_course_category'
            || trigger_type === 'gamipress_ld_complete_lesson_course_category'
            || trigger_type === 'gamipress_ld_enroll_course_category'
            || trigger_type === 'gamipress_ld_complete_course_category'
        ) {
            course_category_select.show();
        } else {
            course_category_select.hide();
        }

        // Course tag
        var course_tag_select = $(this).find('.ld-course-tag');

        if(
            trigger_type === 'gamipress_ld_complete_quiz_course_tag'
            || trigger_type === 'gamipress_ld_complete_quiz_course_tag_grade'
            || trigger_type === 'gamipress_ld_complete_quiz_course_tag_max_grade'
            || trigger_type === 'gamipress_ld_complete_quiz_course_tag_between_grade'
            || trigger_type === 'gamipress_ld_pass_quiz_course_tag'
            || trigger_type === 'gamipress_ld_fail_quiz_course_tag'
            || trigger_type === 'gamipress_ld_complete_topic_course_tag'
            || trigger_type === 'gamipress_ld_assignment_upload_course_tag'
            || trigger_type === 'gamipress_ld_approve_assignment_course_tag'
            || trigger_type === 'gamipress_ld_complete_lesson_course_tag'
            || trigger_type === 'gamipress_ld_enroll_course_tag'
            || trigger_type === 'gamipress_ld_complete_course_tag'
        ) {
            course_tag_select.show();
        } else {
            course_tag_select.hide();
        }

        // Topic category
        var topic_category_select = $(this).find('.ld-topic-category');

        if(
            trigger_type === 'gamipress_ld_complete_topic_category'
        ) {
            topic_category_select.show();
        } else {
            topic_category_select.hide();
        }

        // Topic tag
        var topic_tag_select = $(this).find('.ld-topic-tag');

        if(
            trigger_type === 'gamipress_ld_complete_topic_tag'
        ) {
            topic_tag_select.show();
        } else {
            topic_tag_select.hide();
        }

        // Lesson category
        var lesson_category_select = $(this).find('.ld-lesson-category');

        if(
            trigger_type === 'gamipress_ld_assignment_upload_lesson_category'
            || trigger_type === 'gamipress_ld_approve_assignment_lesson_category'
            || trigger_type === 'gamipress_ld_complete_lesson_category'
        ) {
            lesson_category_select.show();
        } else {
            lesson_category_select.hide();
        }

        // Lesson tag
        var lesson_tag_select = $(this).find('.ld-lesson-tag');

        if(
            trigger_type === 'gamipress_ld_assignment_upload_lesson_tag'
            || trigger_type === 'gamipress_ld_approve_assignment_lesson_tag'
            || trigger_type === 'gamipress_ld_complete_lesson_tag'
        ) {
            lesson_tag_select.show();
        } else {
            lesson_tag_select.hide();
        }

    });

    $('.requirements-list').on( 'update_requirement_data', '.requirement-row', function(e, requirement_details, requirement) {

        // Add score field
        if(
            requirement_details.trigger_type === 'gamipress_ld_complete_quiz_grade'
            || requirement_details.trigger_type === 'gamipress_ld_complete_specific_quiz_grade'
            || requirement_details.trigger_type === 'gamipress_ld_complete_quiz_specific_course_grade'
            || requirement_details.trigger_type === 'gamipress_ld_complete_quiz_course_category_grade'
            || requirement_details.trigger_type === 'gamipress_ld_complete_quiz_course_tag_grade'
            || requirement_details.trigger_type === 'gamipress_ld_complete_quiz_max_grade'
            || requirement_details.trigger_type === 'gamipress_ld_complete_specific_quiz_max_grade'
            || requirement_details.trigger_type === 'gamipress_ld_complete_quiz_specific_course_max_grade'
            || requirement_details.trigger_type === 'gamipress_ld_complete_quiz_course_category_max_grade'
            || requirement_details.trigger_type === 'gamipress_ld_complete_quiz_course_tag_max_grade'
        ) {
            requirement_details.ld_score = requirement.find( '.ld-quiz-score input' ).val();
        }

        // Add min and max score fields
        if(
            requirement_details.trigger_type === 'gamipress_ld_complete_quiz_between_grade'
            || requirement_details.trigger_type === 'gamipress_ld_complete_specific_quiz_between_grade'
            || requirement_details.trigger_type === 'gamipress_ld_complete_quiz_specific_course_between_grade'
            || requirement_details.trigger_type === 'gamipress_ld_complete_quiz_course_category_between_grade'
            || requirement_details.trigger_type === 'gamipress_ld_complete_quiz_course_tag_between_grade'
        ) {
            requirement_details.ld_min_score = requirement.find( '.ld-quiz-min-score input' ).val();
            requirement_details.ld_max_score = requirement.find( '.ld-quiz-max-score input' ).val();
        }

        // Course category
        if(
            requirement_details.trigger_type === 'gamipress_ld_complete_quiz_course_category'
            || requirement_details.trigger_type === 'gamipress_ld_complete_quiz_course_category_grade'
            || requirement_details.trigger_type === 'gamipress_ld_complete_quiz_course_category_max_grade'
            || requirement_details.trigger_type === 'gamipress_ld_complete_quiz_course_category_between_grade'
            || requirement_details.trigger_type === 'gamipress_ld_pass_quiz_course_category'
            || requirement_details.trigger_type === 'gamipress_ld_fail_quiz_course_category'
            || requirement_details.trigger_type === 'gamipress_ld_complete_topic_course_category'
            || requirement_details.trigger_type === 'gamipress_ld_assignment_upload_course_category'
            || requirement_details.trigger_type === 'gamipress_ld_approve_assignment_course_category'
            || requirement_details.trigger_type === 'gamipress_ld_complete_lesson_course_category'
            || requirement_details.trigger_type === 'gamipress_ld_enroll_course_category'
            || requirement_details.trigger_type === 'gamipress_ld_complete_course_category'
        ) {
            requirement_details.ld_course_category_id = requirement.find( '.ld-course-category select' ).val();
        }

        // Course tag
        if(
            requirement_details.trigger_type === 'gamipress_ld_complete_quiz_course_tag'
            || requirement_details.trigger_type === 'gamipress_ld_complete_quiz_course_tag_grade'
            || requirement_details.trigger_type === 'gamipress_ld_complete_quiz_course_tag_max_grade'
            || requirement_details.trigger_type === 'gamipress_ld_complete_quiz_course_tag_between_grade'
            || requirement_details.trigger_type === 'gamipress_ld_pass_quiz_course_tag'
            || requirement_details.trigger_type === 'gamipress_ld_fail_quiz_course_tag'
            || requirement_details.trigger_type === 'gamipress_ld_complete_topic_course_tag'
            || requirement_details.trigger_type === 'gamipress_ld_assignment_upload_course_tag'
            || requirement_details.trigger_type === 'gamipress_ld_approve_assignment_course_tag'
            || requirement_details.trigger_type === 'gamipress_ld_complete_lesson_course_tag'
            || requirement_details.trigger_type === 'gamipress_ld_enroll_course_tag'
            || requirement_details.trigger_type === 'gamipress_ld_complete_course_tag'
        ) {
            requirement_details.ld_course_tag_id = requirement.find( '.ld-course-tag select' ).val();
        }

        // Topic category
        if(
            requirement_details.trigger_type === 'gamipress_ld_complete_topic_category'
        ) {
            requirement_details.ld_topic_category_id = requirement.find( '.ld-topic-category select' ).val();
        }

        // Topic tag
        if(
            requirement_details.trigger_type === 'gamipress_ld_complete_topic_tag'
        ) {
            requirement_details.ld_topic_tag_id = requirement.find( '.ld-topic-tag select' ).val();
        }

        // Lesson category
        if(
            requirement_details.trigger_type === 'gamipress_ld_assignment_upload_lesson_category'
            || requirement_details.trigger_type === 'gamipress_ld_approve_assignment_lesson_category'
            || requirement_details.trigger_type === 'gamipress_ld_complete_lesson_category'
        ) {
            requirement_details.ld_lesson_category_id = requirement.find( '.ld-lesson-category select' ).val();
        }

        // Lesson tag
        if(
            requirement_details.trigger_type === 'gamipress_ld_assignment_upload_lesson_tag'
            || requirement_details.trigger_type === 'gamipress_ld_approve_assignment_lesson_tag'
            || requirement_details.trigger_type === 'gamipress_ld_complete_lesson_tag'
        ) {
            requirement_details.ld_lesson_tag_id = requirement.find( '.ld-lesson-tag select' ).val();
        }
    });

})( jQuery );