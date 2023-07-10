(function( $ ) {

    // Listen for our change to our trigger type selectors
    $('.requirements-list').on( 'change', '.select-trigger-type', function() {

        // Grab our selected trigger type and achievement selector
        var trigger_type = $(this).val();

        // Percentage fields
        var percentage_inputs = $(this).siblings('.thrive-quiz-builder-quiz-percentage');

        if(
            trigger_type === 'gamipress_thrive_quiz_builder_complete_percentage_quiz'
            || trigger_type === 'gamipress_thrive_quiz_builder_complete_specific_percentage_quiz'
        ) {
            percentage_inputs.show();
        } else {
            percentage_inputs.hide();
        }

        // Quiz Type
        var quiz_type_select = $(this).siblings('.thrive-quiz-builder-quiz-type');

        if(
            trigger_type === 'gamipress_thrive_quiz_builder_complete_quiz_type'
        ) {
            quiz_type_select.show();
        } else {
            quiz_type_select.hide();
        }

    });

    // Loop requirement list items to show/hide score input on initial load
    $('.requirements-list li').each(function() {

        // Grab our selected trigger type and achievement selector
        var trigger_type = $(this).find('.select-trigger-type').val();

        // Percentage fields
        var percentage_inputs = $(this).find('.thrive-quiz-builder-quiz-percentage');

        if(
            trigger_type === 'gamipress_thrive_quiz_builder_complete_percentage_quiz'
            || trigger_type === 'gamipress_thrive_quiz_builder_complete_specific_percentage_quiz'
        ) {
            percentage_inputs.show();
        } else {
            percentage_inputs.hide();
        }

        // Quiz Type
        var topic_category_select = $(this).find('.thrive-quiz-builder-quiz-type');

        if(
            trigger_type === 'gamipress_thrive_quiz_builder_complete_quiz_type'
        ) {
            topic_category_select.show();
        } else {
            topic_category_select.hide();
        }

    });

    $('.requirements-list').on( 'update_requirement_data', '.requirement-row', function(e, requirement_details, requirement) {

        // Percentage fields
        if(
            requirement_details.trigger_type === 'gamipress_thrive_quiz_builder_complete_percentage_quiz'
            || requirement_details.trigger_type === 'gamipress_thrive_quiz_builder_complete_specific_percentage_quiz'
        ) {
            requirement_details.thrive_quiz_builder_percentage_condition = requirement.find( '.thrive-quiz-builder-quiz-percentage select' ).val();
            requirement_details.thrive_quiz_builder_percentage = requirement.find( '.thrive-quiz-builder-quiz-percentage input' ).val();
        }

        // Quiz Type
        if(
            requirement_details.trigger_type === 'gamipress_thrive_quiz_builder_complete_quiz_type'
        ) {
            requirement_details.thrive_quiz_builder_quiz_type = requirement.find( '.thrive-quiz-builder-quiz-type select' ).val();
        }
    });

})( jQuery );